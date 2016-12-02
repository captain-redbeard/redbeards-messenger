<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 27-Nov-2016
 * Made Date: 27-Nov-2016
 * Author: Hosvir
 * 
 * */
include(dirname(__FILE__) . "/../includes/login_auth.inc.php");

//Delete
if(isset($guid)) {
	if(QB::update("DELETE FROM messages WHERE (user1_guid = ? OR user1_guid = ?) AND (user2_guid = ? OR user2_guid = ?);", 
					array($_SESSION[USESSION]->user_guid, $guid, $_SESSION[USESSION]->user_guid, $guid), 
					$mysqli)) {

		//Delete conversations
		QB::update("DELETE FROM conversations WHERE (user_guid = ? OR user_guid = ?) AND (contact_guid = ? OR contact_guid = ?);", 
					array($_SESSION[USESSION]->user_guid, $guid, $_SESSION[USESSION]->user_guid, $guid), 
					$mysqli);
					
		//Delete contact
		QB::update("DELETE FROM contacts WHERE contact_guid = ? AND user_guid = ?;", 
					array($guid, $_SESSION[USESSION]->user_guid), 
					$mysqli);
		
		//Delete us from contacts contacts
		QB::update("DELETE FROM contacts WHERE contact_guid = ? AND user_guid = ?;", 
					array($_SESSION[USESSION]->user_guid, $guid), 
					$mysqli);
	}
}

//Redirect
header('Location: ../contacts');
?>
