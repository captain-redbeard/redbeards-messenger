<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 07-Dec-2016
 * Made Date: 06-Dec-2016
 * Author: Hosvir
 * 
 * */
namespace Messenger\Controllers;

use Messenger\Core\Database;

class C extends Controller
{
    public function __construct()
    {
        $this->requiresLogin();
    }

    public function index($to_guid = null, $conversation_guid = null)
    {
        //Set time limit
        $timelimit = 120;
        set_time_limit($timelimit);
        $starttime = microtime(true);
        $havemessage = false;
        $messages = null;
        $returnto = [];
        $returnconversations = [];
        $returnmessages = [];

        //Set header
        header('Content-type: application/json');

        //Loop
        while ((microtime(true) - $starttime) < $timelimit && !$havemessage) {
            //Check for new messages
            $messages = Database::select(
                "SELECT
                    (SELECT made_date FROM messages WHERE (user1_guid = ? OR user2_guid = ?) ORDER BY made_date DESC LIMIT 1) AS last_message,
                    (SELECT last_load FROM users WHERE user_guid = ?) AS last_load;",
                [
                    $_SESSION[USESSION]->user_guid,
                    $_SESSION[USESSION]->user_guid,
                    $_SESSION[USESSION]->user_guid
                ]
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
            //Conversation class
            $_SESSION[USESSION]->updateLastLoad();
            $conv = $this->model('Conversation');
            $mess = $this->model('Message');
            $conversations = $conv->getConversations($messages[0]['last_load']);
            $message = $mess->getMessages($conversation_guid, $messages[0]['last_load']);

            //Check we have conversations
            if ($conversations > 0) {
                //Create array
                foreach ($conversations as $c) {
                    array_push(
                        $returnconversations,
                        [
                            "c" => $c->conversation_guid, 
                            "g" => $c->contact_guid, 
                            "u" => $c->username, 
                            "a" => $c->alias, 
                            "d" => $c->made_date
                        ]
                    );
                }
            }

            //Check we have messages
            if (count($message) > 0) {
                //Create array
                foreach ($message as $m) {
                    if ($m->user2_guid == $_SESSION[USESSION]->user_guid && $m->direction == 1) $sent = true; else $sent = false;

                    array_push(
                        $returnmessages,
                        [
                            "m" => $m->message, 
                            "d" => $m->getMadeDate(), 
                            "r" => $m->made_date,
                            "f" => $sent
                        ]
                    );
                }
            }

            //Build return array
            $returnto = [
                "c" => $returnconversations,
                "m" => $returnmessages
            ];

            //Print JSON results
            print_r(json_encode($returnto));
        } else {
            echo 0;
        }
    }
}
