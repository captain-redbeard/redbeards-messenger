<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 02-Dec-2016
 * Made Date: 29-Nov-2016
 * Author: Hosvir
 * 
 * */
include("../messenger/includes/config.inc.php");
include("../messenger/includes/core.inc.php");
include("../messenger/includes/db.inc.php");
include("../messenger/includes/querybuilder.inc.php");
include("../messenger/includes/login_auth.inc.php");

date_default_timezone_set(TIMEZONE);
header('Content-type: text/html');

//Required for classes
function __autoload($class_name) {
	include_once("../messenger/includes/user.php");
}

//Get
if(isset($_POST['g'])) $guid = clean_input($_POST['g']);
if(isset($_POST['c'])) $cguid = clean_input($_POST['c']);
if(isset($_POST['m'])) $message = clean_input($_POST['m']);
 
//Check for post
if(isset($message) && strlen(trim($message)) > 0) {
	if($guid != null) {
		//Conversation class
		include("../messenger/includes/conversation.php");
		Conversation::sendMessage($guid, $cguid, $message, $mysqli);
		
		echo 1;
	}else {
		echo 0;
	}
}
?>
