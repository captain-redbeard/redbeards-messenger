<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 03-Dec-2016
 * Made Date: 25-Nov-2016
 * Author: Hosvir
 * 
 * */
include("../messenger/includes/config.php");
include("../messenger/includes/core.php");
include("../messenger/includes/querybuilder.php");
include("../messenger/includes/loginauth.php");

date_default_timezone_set(TIMEZONE);
header('Content-type: application/json');

//Required for classes
function __autoload($class_name)
{
    include_once("../messenger/includes/user.php");
}

//Get
if (isset($_GET['g'])) $toGuid = cleanInput($_GET['g']);
if (isset($_GET['c'])) $conversationGuid = cleanInput($_GET['c']);

//Set time limit
$timelimit = 120;
set_time_limit($timelimit);
$starttime = microtime(true);
$havemessage = false;
$messages = null;
$returnto;
$returnconversations = array();
$returnmessages = array();

//Loop
while ((microtime(true) - $starttime) < $timelimit && !$havemessage) {
    //Check for new messages
    $messages = QueryBuilder::select(
        "SELECT
            (SELECT made_date FROM messages WHERE (user1_guid = ? OR user2_guid = ?) ORDER BY made_date DESC LIMIT 1) AS last_message,
            (SELECT last_load FROM users WHERE user_guid = ?) AS last_load;",
        array(
            $_SESSION[USESSION]->user_guid,
            $_SESSION[USESSION]->user_guid,
            $_SESSION[USESSION]->user_guid
        )
    );

    //Echo result
    if (count($messages) > 0 && date($messages[0]['last_message']) > date($messages[0]['last_load'])) {
        $havemessage = true;
    }

    //Sleep
    sleep(3);
}

//Echo results
if ($havemessage) {
    //Update
    QueryBuilder::update(
        "UPDATE users SET last_load = NOW() WHERE user_guid = ?;",
        array($_SESSION[USESSION]->user_guid)
    );

    //Conversation class
    include("../messenger/includes/conversation.php");
    $conversations = Conversation::getConversations($messages[0]['last_load']);
    $message = Conversation::getMessages($conversationGuid, $messages[0]['last_load']);

    //Check we have conversations
    if ($conversations > 0) {
        //Create array
        foreach ($conversations as $c) {
            array_push(
                $returnconversations,
                array(
                    "c" => $c['conversation_guid'], 
                    "g" => $c['contact_guid'], 
                    "u" => $c['username'], 
                    "a" => $c['contact_alias'], 
                    "d" => $c['made_date']
                )
            );
    }
}

    //Check we have messages
    if (count($message) > 0) {
        //Create array
        foreach ($message as $m) {
            if ($m['user2_guid'] == $_SESSION[USESSION]->user_guid && $m['direction'] == 1) $sent = true; else $sent = false;

            array_push(
                $returnmessages,
                array(
                    "m" => $m['message'], 
                    "d" => nicetime($m['made_date']), 
                    "r" => $m['made_date'],
                    "f" => $sent
                )
            );
        }
    }

    //Build return array
    $returnto = array(
        "c" => $returnconversations,
        "m" => $returnmessages
    );

    //Print JSON results
    print_r(json_encode($returnto));
} else {
    echo 0;
}
