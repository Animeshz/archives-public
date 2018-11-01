<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFLow\HTTP;

use \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIManager;
use \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIEndpoints;
use \Animeshz\ClusterPlus\API\DialogFlow\DialogflowAPIException;
use \CharlotteDunois\Yasmin\Utils\URLHelpers;
use \GuzzleHttp\Psr7\Request;
use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;
use \React\Promise\ExtendedPromiseInterface;

class APIRequest
{
	/**
	 * The JSON encode/decode options.
	 * @var int|null
	 */
	static protected $jsonOptions;
	
	/**
	 * The API manager.
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIManager
	 */
	protected $api;
	
	/**
	 * The url.
	 * @var string
	 */
	protected $url;
	
	/**
	 * The used deferred.
	 * @var \React\Promise\Deferred
	 */
	public $deferred;
	
	/**
	 * The request method.
	 * @var string
	 */
	private $method;
	
	/**
	 * The endpoint.
	 * @var string
	 */
	private $endpoint;
	
	/**
	 * How many times we've retried.
	 * @var int
	 */
	protected $retries = 0;
	
	/**
	 * Any request options.
	 * @var array
	 */
	protected $options = array();

	/**
	 * Creates a new API Request.
	 * DO NOT initialize this class yourself.
	 * @param \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIManager	$api
	 * @param string												$method
	 * @param string												$endpoint
	 * @param array													$options
	 */
	function __construct(APIManager $api, string $method, string $endpoint, array $options)
	{
		$this->api = $api;
		$this->url = APIEndpoints::HTTP['url'].'v'.APIEndpoints::HTTP['version'].'/';
		
		$this->method = $method;
		$this->endpoint = \ltrim($endpoint, '/');
		$this->options = $options;
		
		if(self::$jsonOptions === null) {
			self::$jsonOptions = (\PHP_VERSION_ID >= 70300 ? \JSON_THROW_ON_ERROR : 0);
		}
	}

	/**
	 * Returns the request method.
	 * @return string
	 */
	function getMethod(): string
	{
		return $this->method;
	}
	
	/**
	 * Returns the endpoint path.
	 * @return string
	 */
	function getEndpoint(): string
	{
		return $this->endpoint;
	}

	/**
	 * Gets the response body from the response.
	 * @param \Psr\Http\Message\ResponseInterface  $response
	 * @return mixed
	 * @throws \RuntimeException
	 * @throws \JsonException
	 */
	static function decodeBody(ResponseInterface $response)
	{
		$body = (string) $response->getBody();

		$type = $response->getHeader('Content-Type')[0];
		if(\stripos($type, 'text/html') !== false) {
			throw new \RuntimeException('Invalid API response: HTML response body received');
		}

		$json = \json_decode($body, true, 512, self::$jsonOptions);
		if($json === null && \json_last_error() !== \JSON_ERROR_NONE) {
			throw new \RuntimeException('Invalid API response: '.\json_last_error_msg());
		}

		return $json;
	}

	/**
	 * Executes the request.
	 * @return \React\Promise\ExtendedPromiseInterface
	 */
	function execute(): ExtendedPromiseInterface
	{
		$request = $this->request();
		
		return URLHelpers::makeRequest($request, $request->requestOptions)->then(function ($response){
			if(!$response) {
				return -1;
			}
			
			$status = $response->getStatusCode();
			$this->api->dialogflow->emit('debug', 'Got response for item "'.$this->endpoint.'" with HTTP status code '.$status);
			
			if($status === 204) {
				return 0;
			}
			
			$body = self::decodeBody($response);
			
			if($status >= 400) {
				$error = $this->handleAPIError($response, $body);
				if($error === null) {
					return -1;
				}
				
				throw $error;
			}
			
			return $body;
		});
	}

	/**
	 * Handles an API error.
	 * @param \Psr\Http\Message\ResponseInterface                               $response
	 * @param mixed                                                             $body
	 * @return \CharlotteDunois\Yasmin\HTTP\DiscordAPIException|\RuntimeException|null
	 */
	protected function handleAPIError(ResponseInterface $response, $body): ?\Throwable
	{
		$status = $response->getStatusCode();

		if($status >= 500) {
			$this->retries++;
			$maxRetries = (int) $this->api->client->getOption('http.requestMaxRetries', 0);

			if($maxRetries > 0 && $this->retries > $maxRetries) {
				$this->api->dialogflow->emit('debug', 'Giving up on item "'.$this->endpoint.'" after '.$maxRetries.' retries due to HTTP '.$status);

				return (new \RuntimeException('Maximum retry of '.$maxRetries.' reached - giving up'));
			}

			$this->api->dialogflow->emit('debug', 'Delaying unshifting item "'.$this->endpoint.'" due to HTTP '.$status);

			$delay = (int) $this->api->client->getOption('http.requestErrorDelay', 30);
			if($this->retries > 2) {
				$delay *= 2;
			}

			$this->api->client->addTimer($delay, function () {
				$this->api->unshiftQueue($this);
			});

			return null;
		} elseif($status === 429) {
			$this->api->dialogflow->emit('debug', 'Unshifting item "'.$this->endpoint.'" due to HTTP '.$status);
			$this->api->unshiftQueue($this);
			
			return null;
		}

		if($status >= 400 && $status < 500) {
			$error = new DialogflowAPIException($this->endpoint, $body);
			// var_dump($body);
		} else {
			$error = new \RuntimeException($response->getReasonPhrase());
		}

		return $error;
	}

	/**
	 * Returns the Guzzle Request.
	 * @return \Psr\Http\Message\RequestInterface
	 */
	function request(): RequestInterface
	{
		$url = $this->url.$this->endpoint;
		
		$options = array(
			'http_errors' => false,
			'protocols' => array('https'),
			'expect' => false,
			'headers' => array(
				'User-Agent' => 'ClusterPlus (https://github.com/Animeshz/ClusterPlus)',
				'Content-Type' => 'application/json; charset=utf-8'
			)
		);
		
		$options['headers']['Authorization'] = $this->api->dialogflow->tokenHandler->getTokenType().' '.$this->api->dialogflow->tokenHandler->getToken();
		
		if(!empty($this->options['data'])) {
			$options['json'] = $this->options['data'];
		}
		
		$request = new Request($this->method, $url);
		$request->requestOptions = $options;
		
		return $request;
	}
}