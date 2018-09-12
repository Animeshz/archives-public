<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace ClusterPlus\Interfaces\documentory;

/**
 * Attaches listener to the client
 *
 * @property \CharlotteDunois\Livia\Client<\CharlotteDunois\Yasmin\Client>   $client   Instance of current client.
 */
interface addAction
{
	/** 
	 * Adds a role to the guild member. Resolves with $this.
	 * 
	 * @param \CharlotteDunois\Yasmin\Models\GuildMember		$member			A GuildMember instance.
	 * @param \CharlotteDunois\Yasmin\Models\Role|string		$role			A role instance or role ID.
	 * @return \React\Promise\ExtendedPromiseInterface
	 */
	function addRole(\CharlotteDunois\Yasmin\Models\GuildMember $member, $role);

	/** 
	 * Removes a role to the guild member. Resolves with $this.
	 * 
	 * @param \CharlotteDunois\Yasmin\Models\GuildMember		$member			A GuildMember instance.
	 * @param \CharlotteDunois\Yasmin\Models\Role|string		$role			A role instance or role ID.
	 * @return \React\Promise\ExtendedPromiseInterface
	 */
	function removeRole(\CharlotteDunois\Yasmin\Models\GuildMember $member, $role);

	/**
	 * Moves the guild member to the given voice channel, if connected to voice. Resolves with $this.
	 * 
	 * @param \CharlotteDunois\Yasmin\Models\GuildMember				$member			A GuildMember instance.
	 * @param \CharlotteDunois\Yasmin\Models\VoiceChannel|string		$channel
	 * @return \React\Promise\ExtendedPromiseInterface
	 * @throws \InvalidArgumentException
	 */
	function setVoiceChannel(\CharlotteDunois\Yasmin\Models\GuildMember $member, $channel);

	/**
	 * Creates an invite. Resolves with an instance of Invite.
	 *
	 * Options are as following (all are optional).
	 *
	 * ```
	 * array(
	 *    'maxAge' => int,
	 *    'maxUses' => int, (0 = unlimited)
	 *    'temporary' => bool,
	 *    'unique' => bool
	 * )
	 * ```
	 *
	 * @param \CharlotteDunois\Yasmin\Models\TextChannel		$channel			A TextChannel instance.
	 * @param array												$options
	 * @return \React\Promise\ExtendedPromiseInterface
	 * @see \CharlotteDunois\Yasmin\Models\Invite
	 */
	function createInvite(\CharlotteDunois\Yasmin\Models\TextChannel $channel);

	/**
	 * Sends a message to a channel. Resolves with an instance of Message, or a Collection of Message instances, mapped by their ID.
	 *
	 * Options are as following (all are optional):
	 *
	 * ```
	 * array(
	 *    'embed' => array|\CharlotteDunois\Yasmin\Models\MessageEmbed, (an (embed) array/object or an instance of MessageEmbed)
	 *    'files' => array, (an array of `[ 'name' => string, 'data' => string || 'path' => string ]` or just plain file contents, file paths or URLs)
	 *    'nonce' => string, (a snowflake used for optimistic sending)
	 *    'disableEveryone' => bool, (whether @everyone and @here should be replaced with plaintext, defaults to client option disableEveryone)
	 *    'tts' => bool,
	 *    'split' => bool|array, (*)
	 * )
	 *
	 *   * array(
	 *   *   'before' => string, (The string to insert before the split)
	 *   *   'after' => string, (The string to insert after the split)
	 *   *   'char' => string, (The string to split on)
	 *   *   'maxLength' => int, (The max. length of each message)
	 *   * )
	 * ```
	 *
	 * @param \CharlotteDunois\Yasmin\Models\TextChannel		$channel			A TextChannel instance.
	 * @param string											$content			The message content.
	 * @param array												$options			Any message options.
	 * @return \React\Promise\ExtendedPromiseInterface
	 * @see \CharlotteDunois\Yasmin\Models\Message
	 */
	function send(\CharlotteDunois\Yasmin\Models\TextChannel $channel, string $content);

	/**
	 * Sets the topic of the channel. Resolves with $this.
	 * 
	 * @param \CharlotteDunois\Yasmin\Models\TextChannel		$channel			A TextChannel instance.
	 * @param string											$topic
	 * @return \React\Promise\ExtendedPromiseInterface
	 * @throws \InvalidArgumentException
	 */
	function setTopic(\CharlotteDunois\Yasmin\Models\TextChannel $channel, string $topic);

	/**
	 * Set the color of the role. Resolves with $this.
	 * 
	 * Colors are as follows:
	 * 
	 * ```
	 * 	array(
	 * 		'AQUA' => 1752220,
	 * 		'BLUE' => 3447003,
	 * 		'GREEN' => 3066993,
	 * 		'PURPLE' => 10181046,
	 * 		'GOLD' => 15844367,
	 * 		'ORANGE' => 15105570,
	 * 		'RED' => 15158332,
	 * 		'GREY' => 9807270,
	 * 		'DARKER_GREY' => 8359053,
	 * 		'NAVY' => 3426654,
	 * 		'DARK_AQUA' => 1146986,
	 * 		'DARK_GREEN' => 2067276,
	 * 		'DARK_BLUE' => 2123412,
	 * 		'DARK_GOLD' => 12745742,
	 * 		'DARK_PURPLE' => 7419530,
	 * 		'DARK_ORANGE' => 11027200,
	 * 		'DARK_GREY' => 9936031,
	 * 		'DARK_RED' => 10038562,
	 * 		'LIGHT_GREY' => 12370112,
	 * 		'DARK_NAVY' => 2899536
	 * 	)
	 * ```
	 * 
	 * @param \CharlotteDunois\Yasmin\Models\Role				$role			A role instance or role ID.
	 * @param int|string										$color
	 * @param string											$reason
	 * @return \React\Promise\ExtendedPromiseInterface
	 * @throws \InvalidArgumentException
	 * @see \CharlotteDunois\Yasmin\Utils\DataHelpers::resolveColor()
	 */
	function setColor(\CharlotteDunois\Yasmin\Models\Role $role, $color);
}