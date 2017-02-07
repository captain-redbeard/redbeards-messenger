<?php
/**
 * @author captain-redbeard
 * @since 06/12/16
 */
namespace Redbeard\Controllers;

use Redbeard\Core\Database;

class Check extends Controller
{
    public function __construct()
    {
        $this->requiresLogin();
    }

    public function index($to_guid = null, $conversation_guid = null)
    {
        $timelimit = 120;
        set_time_limit($timelimit);
        $starttime = microtime(true);
        $havemessage = false;
        $messages = null;
        $returnto = [];
        $returnconversations = [];
        $returnmessages = [];
        
        header('Content-type: application/json');
        
        while ((microtime(true) - $starttime) < $timelimit && !$havemessage) {
            $messages = Database::select(
                "SELECT
                    (SELECT made_date FROM messages WHERE (user1_guid = ? OR user2_guid = ?) 
                        ORDER BY made_date DESC LIMIT 1) AS last_message,
                    (SELECT last_load FROM users WHERE user_guid = ?) AS last_load;",
                [
                    $_SESSION[$this->config('app.user_session')]->user_guid,
                    $_SESSION[$this->config('app.user_session')]->user_guid,
                    $_SESSION[$this->config('app.user_session')]->user_guid
                ]
            );
            
            if (count($messages) > 0 && date($messages[0]['last_message']) > date($messages[0]['last_load'])) {
                $havemessage = true;
            }
            
            sleep(3);
        }
        
        if ($havemessage) {
            $_SESSION[$this->config('app.user_session')]->updateLastLoad();
            $conv = $this->model('Conversation');
            $mess = $this->model('Message');
            $conversations = $conv->getConversations($messages[0]['last_load']);
            $message = $mess->getMessages($conversation_guid, $messages[0]['last_load']);
            
            if ($conversations > 0) {
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
            
            if (count($message) > 0) {
                foreach ($message as $m) {
                    if ($m->user2_guid === $_SESSION[$this->config('app.user_session')]->user_guid && $m->direction === 1) {
                        $sent = true;
                    } else {
                        $sent = false;
                    }
                    
                    array_push(
                        $returnmessages,
                        [
                            "m" => $m->message,
                            "d" => $m->getMadeDate(),
                            "r" => $m->made_date,
                            "f" => $sent,
                            "s" => $m->signature
                        ]
                    );
                }
            }
            
            $returnto = [
                "c" => $returnconversations,
                "m" => $returnmessages
            ];
            
            print_r(json_encode($returnto));
        } else {
            echo 0;
        }
    }
}
