<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Utils;

class UniversalHelpers
{
	static function isValidPHP($code) {
		$result = \trim(\shell_exec('echo "'.\addslashes($code).'" | php -l 2> /dev/null'));
		return (strpos( $result, 'No syntax errors detected' ) !== false);
	}
}