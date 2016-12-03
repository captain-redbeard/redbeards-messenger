<?php
/**
 * Details:
 * PHP Messenger.
 * 
 * Modified: 03-Dec-2016
 * Date: 02-Dec-2016
 * Author: Hosvir
 * 
 * */
class Conversation
{

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
     * */
    public static function sendMessage($to_guid, $conversation_guid, $message)
    {
        $message = cleanInput($message, 0);
        $conversation_guid = $conversation_guid != null ? $conversation_guid : generateRandomString(32);

        //Contact set
        if ($to_guid != null) {
            include(dirname(__FILE__) . "/../includes/publicprivatekey.php");

            if (!STORE_KEYS_LOCAL) {
                include(dirname(__FILE__) . "/../thirdparty/S3.php");
                S3::setAuth(S3_ACCESS_KEY, S3_SECRET_KEY);
                $to_public_key = S3::getObject(KEY_BUCKET, $to_guid . ".pem");
                $user_public_key = S3::getObject(KEY_BUCKET, $_SESSION[USESSION]->user_guid . ".pem");
            } else {
                $to_public_key = file_get_contents(dirname(__FILE__) . "/../keys/public/" . $to_guid . ".pem");
                $user_public_key = file_get_contents(dirname(__FILE__) . "/../keys/public/" . $_SESSION[USESSION]->user_guid . ".pem");
            }

            //Encrypt to message
            $to_message = PublicPrivateKey::encrypt(
                $message,
                null,
                STORE_KEYS_LOCAL ? $to_public_key : $to_public_key->body
            );

            //Encrypt user message
            $user_message = PublicPrivateKey::encrypt(
                $message,
                null,
                STORE_KEYS_LOCAL ? $user_public_key : $user_public_key->body
            );

            //Get conversation
            $conversation = QueryBuilder::select(
                "SELECT conversation_guid
                    FROM conversations
                    WHERE user_guid = ?
                    AND contact_guid = ?;",
                array(
                    $_SESSION[USESSION]->user_guid,
                    $to_guid
                )
            );
                
            if (count($conversation) < 1) {
                //Add new conversations
                QueryBuilder::insert(
                    "INSERT INTO conversations (conversation_guid, contact_guid, user_guid) VALUES (?,?,?);",
                    array(
                        $conversation_guid,
                        $_SESSION[USESSION]->user_guid,
                        $to_guid
                    )
                );
                    
                QueryBuilder::insert(
                    "INSERT INTO conversations (conversation_guid, contact_guid, user_guid) VALUES (?,?,?);",
                    array(
                        $conversation_guid,
                        $to_guid,
                        $_SESSION[USESSION]->user_guid
                    )
                );
                
            } elseif ($conversation_guid != $conversation[0]['conversation_guid']) {
                $conversation_guid = $conversation[0]['conversation_guid'];
            }

            //Insert to message
            QueryBuilder::insert(
                "INSERT INTO messages (conversation_guid, user1_guid, user2_guid, direction, message) VALUES (?,?,?,?,?);",
                array(
                    $conversation_guid,
                    $to_guid,
                    $_SESSION[USESSION]->user_guid,
                    0,
                    $to_message
                )
            );

            //Insert from message
            QueryBuilder::insert(
                "INSERT INTO messages (conversation_guid, user1_guid, user2_guid, direction, message) VALUES (?,?,?,?,?);",
                array(
                    $conversation_guid,
                    $to_guid,
                    $_SESSION[USESSION]->user_guid,
                    1,
                    $user_message
                )
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
     * */
    public static function getMessages($conversation_guid, $made_date = null)
    { 
        //Get messages
        if ($made_date == null) {
            $messages = QueryBuilder::select(
                "SELECT user1_guid, user2_guid, direction, message, made_date
                    FROM messages
                    WHERE (user1_guid = ? AND direction = 0 OR user2_guid = ? AND direction = 1)
                    AND conversation_guid = ?
                    ORDER BY made_date DESC
                    LIMIT ?;",
                array(
                    $_SESSION[USESSION]->user_guid,
                    $_SESSION[USESSION]->user_guid,
                    $conversation_guid,
                    CONVERSATION_MAX_LENGTH
                )
            );
        } else {
            $messages = QueryBuilder::select(
                "SELECT user1_guid, user2_guid, direction, message, made_date
                    FROM messages
                    WHERE (user1_guid = ? AND direction = 0 OR user2_guid = ? AND direction = 1)
                    AND conversation_guid = ?
                    AND made_date > ?
                    ORDER BY made_date DESC
                    LIMIT ?;",
                array(
                    $_SESSION[USESSION]->user_guid,
                    $_SESSION[USESSION]->user_guid,
                    $conversation_guid,
                    $made_date,
                    CONVERSATION_MAX_LENGTH
                )
            );
        }

        //Get private key
        include_once(dirname(__FILE__) . "/../includes/publicprivatekey.php");

        if (!STORE_KEYS_LOCAL) {
            include_once(dirname(__FILE__) . "/../thirdparty/S3.php");
            S3::setAuth(S3_ACCESS_KEY, S3_SECRET_KEY);
            $user_private_key = S3::getObject(KEY_BUCKET, $_SESSION[USESSION]->user_guid . ".key");
        } else {
            $user_private_key = file_get_contents(dirname(__FILE__) . "/../keys/private/" . $_SESSION[USESSION]->user_guid . ".key");
        }

        //Decrypt messages
        for ($i = 0; $i < count($messages); $i++) {
            $messages[$i]['message'] = str_replace("---newline---", "<br/>",
                PublicPrivateKey::decrypt(
                    $messages[$i]['message'],
                    null,
                    $_SESSION[USESSION]->passphrase,
                    STORE_KEYS_LOCAL ? $user_private_key : $user_private_key->body
                )
            );
        }

        //Retrun decrypted message array
        return array_reverse($messages);
    }

    /**
     * 
     * Get conversations.
     * 
     * Details:
     * Get all conversations for the current session user.
     * 
     * @param: $made_date   - Made date grater than this
     * 
     * @returns: Conversations array
     * 
     * */
    public static function getConversations($made_date = null)
    {
        //Get conversations
        if ($made_date == null) {
            $conversations = QueryBuilder::select(
                "SELECT conversation_guid, contact_guid,
                    (SELECT username FROM users WHERE user_guid = contact_guid) AS username,
                    (SELECT contact_alias FROM contacts WHERE contact_guid = conversations.contact_guid AND user_guid = conversations.user_guid) AS contact_alias,
                    (SELECT made_date FROM messages WHERE conversation_guid = conversations.conversation_guid AND (user1_guid = ? OR user2_guid = ?)
                    ORDER BY made_date DESC LIMIT 1) AS made_date
                    FROM conversations WHERE user_guid = ?
                    ORDER BY made_date DESC;",
                array(
                    $_SESSION[USESSION]->user_guid,
                    $_SESSION[USESSION]->user_guid,
                    $_SESSION[USESSION]->user_guid
                )
            );
        } else {
            $conversations = QueryBuilder::select(
                "SELECT conversation_guid, contact_guid,
                    (SELECT username FROM users WHERE user_guid = contact_guid) AS username,
                    (SELECT contact_alias FROM contacts WHERE contact_guid = conversations.contact_guid AND user_guid = conversations.user_guid) AS contact_alias,
                    (SELECT made_date FROM messages WHERE conversation_guid = conversations.conversation_guid AND (user1_guid = ? OR user2_guid = ?) ORDER BY made_date DESC LIMIT 1) AS made_date
                    FROM conversations
                    WHERE user_guid = ?
                    AND made_date > ?
                    ORDER BY made_date DESC;",
                array(
                    $_SESSION[USESSION]->user_guid,
                    $_SESSION[USESSION]->user_guid,
                    $_SESSION[USESSION]->user_guid,
                    $made_date
                )
            );
        }

        //Return conversation array
        return $conversations;
    }
}
