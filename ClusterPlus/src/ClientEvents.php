<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
 */

namespace CharlotteDunois\Yasmin;

/**
 * Documents all Client events. ($client->on('name here', callable))
 *
 * The second parameter of *Update events is null, if cloning for that event is disabled.
 */
interface ClientEvents
{    
	/**
	 * Emitted when Provider is successfully initialized and set to the client.
	 * @return void
	 */
	function providerSet();
	
	/**
	 * Emitted when guildMemberAdd event is triggered "and" we can identify whom invited the user.
	 * @return void
	 */
	function guildMemberAddByInvite(\CharlotteDunois\Yasmin\Models\User $inviter, \CharlotteDunois\Yasmin\Models\GuildMember $invitedUser);  
}