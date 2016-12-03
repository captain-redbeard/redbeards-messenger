<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 03-Dec-2016
 * Made Date: 29-Nov-2016
 * Author: Hosvir
 * 
 * */
include("../messenger/includes/config.php");
include("../messenger/includes/core.php");
include("../messenger/includes/querybuilder.php");
include("../messenger/includes/loginauth.php");

date_default_timezone_set(TIMEZONE);
header('Content-type: text/html');

//Required for classes
function __autoload($class_name)
{
    include_once("../messenger/includes/user.php");
}

//Get
if (isset($_POST['g'])) $toGuid = cleanInput($_POST['g']);
if (isset($_POST['c'])) $conversationGuid = cleanInput($_POST['c']);
if (isset($_POST['m'])) $message = cleanInput($_POST['m']);

//Check for post
if (isset($message) && strlen(trim($message)) > 0) {
    if ($toGuid != null) {
        //Conversation class
        include("../messenger/includes/conversation.php");
        Conversation::sendMessage(
            $toGuid,
            $conversationGuid,
            $message
        );

        echo 1;
    } else {
        echo 0;
    }
}
