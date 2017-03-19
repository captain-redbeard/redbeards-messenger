<?php
/**
 * @author captain-redbeard
 * @since 06/12/16
 */
namespace Messenger\Models;

use \Tidy;
use Redbeard\Crew\Config;
use Redbeard\Crew\Database;
use Redbeard\Crew\Session;
use Redbeard\Crew\Utils\Dates;
use Redbeard\Crew\Utils\Strings;
use Redbeard\Crew\Encryption\RSA;

class Message
{
    public $user1_guid = null;
    public $user2_guid = null;
    public $direction = null;
    public $message = null;
    public $made_date = null;
    public $signature = null;
    
    public function __construct(
        $user1_guid = null,
        $user2_guid = null,
        $direction = null,
        $message = null,
        $made_date = null,
        $signature = null
    ) {
        $this->user1_guid = $user1_guid;
        $this->user2_guid = $user2_guid;
        $this->direction = $direction;
        $this->message = $message;
        $this->made_date = $made_date;
        $this->signature = $signature;
    }
    
    /**
     * Gets both parties public keys by GUID, encrypts the message
     * and adds a record to both users.
     * If no conversation has been provided it will create a new one.
     *
     * @param $to_guid             - to guid
     * @param $conversation_guid   - conversation guid
     * @param $message             - message to send
     *
     * @returns result code
     */
    public function send($to_guid, $conversation_guid, $message)
    {
        Session::start();
        
        $to_guid = Strings::cleanInput($to_guid, 2);
        $conversation_guid = Strings::cleanInput($to_guid, 2);
        
        $conversation_guid = $conversation_guid != null ? $conversation_guid : Strings::generateRandomString(32);
        $contact = Database::select(
            "SELECT contact_guid FROM contacts WHERE contact_guid = ? AND user_guid = ?;",
            [$to_guid, $_SESSION[Config::get('app.user_session')]->guid]
        );
        
        if ($to_guid != null && count($contact) > 0) {
            //Get keys
            $to_public_key = file_get_contents(
                Config::get('app.base_dir') .
                Config::get('keys.public_folder') .
                $to_guid .
                '.pem'
            );
            $user_public_key = file_get_contents(
                Config::get('app.base_dir') .
                Config::get('keys.public_folder') .
                $_SESSION[Config::get('app.user_session')]->guid .
                '.pem'
            );
            $user_private_key = file_get_contents(
                Config::get('app.base_dir') .
                Config::get('keys.private_folder') .
                $_SESSION[Config::get('app.user_session')]->guid .
                '.key'
            );
            
            //Create messages
            $to_message = RSA::encrypt(
                $message,
                null,
                $to_public_key
            );
            
            $user_message = RSA::encrypt(
                $message,
                null,
                $user_public_key
            );
            
            $to_signature = RSA::sign($to_message, $user_private_key, $_SESSION[Config::get('app.user_session')]->passphrase);
            $user_signature = RSA::sign($user_message, $user_private_key, $_SESSION[Config::get('app.user_session')]->passphrase);
            
            $conversation = Database::select(
                "SELECT conversation_guid
                    FROM conversations
                    WHERE user_guid = ?
                    AND contact_guid = ?;",
                [
                    $_SESSION[Config::get('app.user_session')]->guid,
                    $to_guid
                ]
            );
                
            if (count($conversation) < 1) {
                Database::insert(
                    "INSERT INTO conversations (conversation_guid, contact_guid, user_guid) VALUES (?,?,?);",
                    [
                        $conversation_guid,
                        $_SESSION[Config::get('app.user_session')]->guid,
                        $to_guid
                    ]
                );
                    
                Database::insert(
                    "INSERT INTO conversations (conversation_guid, contact_guid, user_guid) VALUES (?,?,?);",
                    [
                        $conversation_guid,
                        $to_guid,
                        $_SESSION[Config::get('app.user_session')]->guid
                    ]
                );
                
            } elseif ($conversation_guid != $conversation[0]['conversation_guid']) {
                $conversation_guid = $conversation[0]['conversation_guid'];
            }
            
            Database::insert(
                "INSERT INTO messages (conversation_guid, user1_guid, user2_guid, direction, message, signature) 
                    VALUES (?,?,?,?,?,?);",
                [
                    $conversation_guid,
                    $to_guid,
                    $_SESSION[Config::get('app.user_session')]->guid,
                    0,
                    $to_message,
                    $to_signature
                ]
            );
            
            Database::insert(
                "INSERT INTO messages (conversation_guid, user1_guid, user2_guid, direction, message, signature) 
                    VALUES (?,?,?,?,?,?);",
                [
                    $conversation_guid,
                    $to_guid,
                    $_SESSION[Config::get('app.user_session')]->guid,
                    1,
                    $user_message,
                    $user_signature
                ]
            );
            
            return 0 . ':' . $conversation_guid;
        } else {
            return 1;
        }
    }
    
    /**
     * Get messages for the current session user from the specified conversation.
     *
     * @param $conversation_guid       - conversation guid
     * @param $made_date               - Made date grater than this
     *
     * @returns Decrypted message array
     */
    public function getAll($conversation_guid, $made_date = null)
    {
        if ($conversation_guid == null) {
            return null;
        }
        
        $conversation = Database::select(
            "SELECT conversation_guid, contact_guid, user_guid 
                FROM conversations 
                WHERE conversation_guid = ? 
                AND user_guid = ? 
                LIMIT 1;",
            [
                $conversation_guid,
                $_SESSION[Config::get('app.user_session')]->guid
            ]
        );
        
        if ($made_date == null) {
            $messages = Database::select(
                "SELECT user1_guid, user2_guid, direction, message, signature, made_date
                    FROM messages
                    WHERE (user1_guid = ? AND direction = 0 OR user2_guid = ? AND direction = 1)
                    AND conversation_guid = ?
                    ORDER BY made_date DESC
                    LIMIT ?;",
                [
                    $_SESSION[Config::get('app.user_session')]->guid,
                    $_SESSION[Config::get('app.user_session')]->guid,
                    $conversation_guid,
                    Config::get('app.conversation_max_length')
                ]
            );
        } else {
            $messages = Database::select(
                "SELECT user1_guid, user2_guid, direction, message, signature, made_date
                    FROM messages
                    WHERE (user1_guid = ? AND direction = 0 OR user2_guid = ? AND direction = 1)
                    AND conversation_guid = ?
                    AND made_date > ?
                    ORDER BY made_date DESC
                    LIMIT ?;",
                [
                    $_SESSION[Config::get('app.user_session')]->guid,
                    $_SESSION[Config::get('app.user_session')]->guid,
                    $conversation_guid,
                    $made_date,
                    Config::get('app.conversation_max_length')
                ]
            );
        }
        
        //Get keys
        $from_public_key = file_get_contents(
            Config::get('app.base_dir') .
            Config::get('keys.public_folder') .
            $conversation[0]['contact_guid'] .
            '.pem'
        );
        $user_public_key = file_get_contents(
            Config::get('app.base_dir') .
            Config::get('keys.public_folder') .
            $_SESSION[Config::get('app.user_session')]->guid .
            '.pem'
        );
        $user_private_key = file_get_contents(
            Config::get('app.base_dir') .
            Config::get('keys.private_folder') .
            $_SESSION[Config::get('app.user_session')]->guid .
            '.key'
        );
        
        for ($i = 0; $i < count($messages); $i++) {
            if ($messages[$i]['user2_guid'] == $_SESSION[Config::get('app.user_session')]->guid && $messages[$i]['direction'] === 1) {
                $public_key = $user_public_key;
            } else {
                $public_key = $from_public_key;
            }
            
            if (RSA::verify($messages[$i]['message'], $public_key, $messages[$i]['signature'])) {
                $messages[$i]['signature'] = 0;
            } else {
                $messages[$i]['signature'] = 1;
            }
            
            $messages[$i]['message'] = htmlspecialchars(
                RSA::decrypt(
                    $messages[$i]['message'],
                    null,
                    $_SESSION[Config::get('app.user_session')]->passphrase,
                    $user_private_key
                )
            );
        }
        
        return array_reverse($messages);
    }
    
    public function getMessages($conversation_guid, $made_date = null)
    {
        $messages = [];
        $message_data = $this->getAll($conversation_guid, $made_date);

        if ($message_data > 0) {
            foreach ($message_data as $message) {
                array_push(
                    $messages,
                    new Message(
                        $message['user1_guid'],
                        $message['user2_guid'],
                        $message['direction'],
                        $this->allowTags($message['message']),
                        $message['made_date'],
                        $message['signature']
                    )
                );
            }
        }
        
        return $messages;
    }
    
    public function allowTags($message, $allowImage = false)
    {
        $allowed = [
            '/\\n/',
            '/&lt;br&gt;/',
            '/&lt;b&gt;/',
            '/&lt;\/b&gt;/',
            '/&lt;ul&gt;/',
            '/&lt;\/ul&gt;/',
            '/&lt;li&gt;/',
            '/&lt;\/li&gt;/',
            '/&lt;strong&gt;/',
            '/&lt;\/strong&gt;/'
        ];
        
        $replace = [
            '<br>',
            '<br>',
            '<b>',
            '</b>',
            '<ul>',
            '</ul>',
            '<li>',
            '</li>',
            '<strong>',
            '</strong>'
        ];
        
        if ($allowImage) {
            array_push($allowed, '/&lt;img src=&quot;(.*)&quot;&gt;/');
            array_push($replace, '<img src="$1">');
        }
        
        $tidy = new Tidy();
        
        return $tidy->repairString(
            preg_replace(
                $allowed,
                $replace,
                $message
            ),
            [
                'show-body-only' => true,
                'indent' => true,
                'indent-spaces' => 4
            ]
        );
    }
    
    public function getMadeDate()
    {
        return Dates::niceTime($this->made_date, true);
    }
}
