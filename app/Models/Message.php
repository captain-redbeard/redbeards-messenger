<?php
/**
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 23-Dec-2016
 * Made Date: 06-Dec-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Models;

use Messenger\Core\Functions;
use Messenger\Core\Database;
use Messenger\Core\Session;
use Messenger\Core\PublicPrivateKey;
use Messenger\ThirdParty\S3;

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
    
    public function getMadeDate()
    {
        return Functions::niceTime($this->made_date, true);
    }
    
    /**
     *
     * Send message.
     *
     * Details:
     * Gets both parties public keys by GUID, encrypts the message and adds a record to both users.
     * If no conversation has been provided it will create a new one.
     *
     * @param: $to_guid             - to guid
     * @param: $conversation_guid   - conversation guid
     * @param: $message             - message to send
     *
     * @returns: result code
     *
     */
    public function send($to_guid, $conversation_guid, $message)
    {
        Session::start();
        $message = Functions::cleanInput($message, 0);
        $conversation_guid = $conversation_guid != null ? $conversation_guid : Functions::generateRandomString(32);
        $contact = Database::select(
            "SELECT contact_guid FROM contacts WHERE contact_guid = ? AND user_guid = ?;",
            [$to_guid, $_SESSION[USESSION]->user_guid]
        );
        
        if ($to_guid != null && count($contact) > 0) {
            if (!STORE_KEYS_LOCAL) {
                S3::setAuth(S3_ACCESS_KEY, S3_SECRET_KEY);
                $to_public_key = S3::getObject(KEY_BUCKET, $to_guid . ".pem");
                $user_public_key = S3::getObject(KEY_BUCKET, $_SESSION[USESSION]->user_guid . ".pem");
                $user_private_key = S3::getObject(KEY_BUCKET, $_SESSION[USESSION]->user_guid . ".key");
            } else {
                $to_public_key = file_get_contents(
                    BASE_DIR .
                    PPK_PUBLIC_FOLDER .
                    $to_guid .
                    ".pem"
                );
                $user_public_key = file_get_contents(
                    BASE_DIR .
                    PPK_PUBLIC_FOLDER .
                    $_SESSION[USESSION]->user_guid .
                    ".pem"
                );
                $user_private_key = file_get_contents(
                    BASE_DIR .
                    PPK_PRIVATE_FOLDER .
                    $_SESSION[USESSION]->user_guid .
                    ".key"
                );
            }
            
            $to_message = PublicPrivateKey::encrypt(
                $message,
                null,
                STORE_KEYS_LOCAL ? $to_public_key : $to_public_key->body
            );
            
            $user_message = PublicPrivateKey::encrypt(
                $message,
                null,
                STORE_KEYS_LOCAL ? $user_public_key : $user_public_key->body
            );
            
            $to_signature = PublicPrivateKey::sign($to_message, $user_private_key, $_SESSION[USESSION]->passphrase);
            $user_signature = PublicPrivateKey::sign($user_message, $user_private_key, $_SESSION[USESSION]->passphrase);
            
            $conversation = Database::select(
                "SELECT conversation_guid
                    FROM conversations
                    WHERE user_guid = ?
                    AND contact_guid = ?;",
                [
                    $_SESSION[USESSION]->user_guid,
                    $to_guid
                ]
            );
                
            if (count($conversation) < 1) {
                Database::insert(
                    "INSERT INTO conversations (conversation_guid, contact_guid, user_guid) VALUES (?,?,?);",
                    [
                        $conversation_guid,
                        $_SESSION[USESSION]->user_guid,
                        $to_guid
                    ]
                );
                    
                Database::insert(
                    "INSERT INTO conversations (conversation_guid, contact_guid, user_guid) VALUES (?,?,?);",
                    [
                        $conversation_guid,
                        $to_guid,
                        $_SESSION[USESSION]->user_guid
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
                    $_SESSION[USESSION]->user_guid,
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
                    $_SESSION[USESSION]->user_guid,
                    1,
                    $user_message,
                    $user_signature
                ]
            );
            
            return 0 . ":" . $conversation_guid;
        } else {
            return 1;
        }
    }
    
    /**
     *
     * Get messages.
     *
     * Details:
     * Get messages for the current session user from the specified conversation.
     *
     * @param: $conversation_guid       - conversation guid
     * @param: $made_date               - Made date grater than this
     *
     * @returns: Decrypted message array
     *
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
                $_SESSION[USESSION]->user_guid            
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
                    $_SESSION[USESSION]->user_guid,
                    $_SESSION[USESSION]->user_guid,
                    $conversation_guid,
                    CONVERSATION_MAX_LENGTH
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
                    $_SESSION[USESSION]->user_guid,
                    $_SESSION[USESSION]->user_guid,
                    $conversation_guid,
                    $made_date,
                    CONVERSATION_MAX_LENGTH
                ]
            );
        }
        
        if (!STORE_KEYS_LOCAL) {
            S3::setAuth(S3_ACCESS_KEY, S3_SECRET_KEY);
            $from_public_key = S3::getObject(KEY_BUCKET, $conversation[0]['contact_guid'] . ".pem");
            $user_public_key = S3::getObject(KEY_BUCKET, $_SESSION[USESSION]->user_guid . ".pem");
            $user_private_key = S3::getObject(KEY_BUCKET, $_SESSION[USESSION]->user_guid . ".key");
        } else {
            $from_public_key = file_get_contents(
                BASE_DIR .
                PPK_PUBLIC_FOLDER .
                $conversation[0]['contact_guid'] .
                ".pem"
            );
            $user_public_key = file_get_contents(
                BASE_DIR .
                PPK_PUBLIC_FOLDER .
                $_SESSION[USESSION]->user_guid .
                ".pem"
            );
            $user_private_key = file_get_contents(
                BASE_DIR .
                PPK_PRIVATE_FOLDER .
                $_SESSION[USESSION]->user_guid .
                ".key"
            );
        }
        
        for ($i = 0; $i < count($messages); $i++) {
            if ($messages[$i]['user2_guid'] == $_SESSION[USESSION]->user_guid && $messages[$i]['direction'] === 1) {
                $public_key = $user_public_key;
            } else {
                $public_key = $from_public_key;
            }
            
            if (PublicPrivateKey::verify($messages[$i]['message'], $public_key, $messages[$i]['signature'])) {
                $messages[$i]['signature'] = 0;
            } else {
                $messages[$i]['signature'] = 1;
            }
            
            $messages[$i]['message'] = htmlspecialchars(
                PublicPrivateKey::decrypt(
                    $messages[$i]['message'],
                    null,
                    $_SESSION[USESSION]->passphrase,
                    STORE_KEYS_LOCAL ? $user_private_key : $user_private_key->body
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
                        Functions::allowTags($message['message']),
                        $message['made_date'],
                        $message['signature']
                    )
                );
            }
        }
        
        return $messages;
    }
}
