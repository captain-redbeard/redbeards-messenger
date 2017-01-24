<?php
/**
 * @author captain-redbeard
 * @since 06/12/16
 */
namespace Redbeard\Models;

use Redbeard\Core\Functions;
use Redbeard\Core\Database;

class Conversation
{
    public $conversation_guid = null;
    public $contact_guid = null;
    public $username = null;
    public $alias = null;
    public $made_date = null;
    
    public function __construct(
        $conversation_guid = null,
        $contact_guid = null,
        $username = null,
        $alias = null,
        $made_date = null
    ) {
        $this->conversation_guid = $conversation_guid;
        $this->contact_guid = $contact_guid;
        $this->username = $username;
        $this->alias = $alias;
        $this->made_date = $made_date;
    }
    
    public function getMadeDate()
    {
        return Functions::convertTime($this->made_date, true);
    }
    
    public function getAll($made_date = null)
    {
        if ($made_date == null) {
            $conversations = Database::select(
                "SELECT conversation_guid, contact_guid,
                    (SELECT username FROM users WHERE user_guid = contact_guid) AS username,
                    (SELECT contact_alias FROM contacts WHERE contact_guid = conversations.contact_guid 
                        AND user_guid = conversations.user_guid) AS contact_alias,
                    (SELECT made_date FROM messages WHERE conversation_guid = conversations.conversation_guid 
                        AND (user1_guid = ? OR user2_guid = ?) ORDER BY made_date DESC LIMIT 1) AS made_date
                    FROM conversations WHERE user_guid = ?
                    ORDER BY made_date DESC;",
                [
                    $_SESSION[USESSION]->user_guid,
                    $_SESSION[USESSION]->user_guid,
                    $_SESSION[USESSION]->user_guid
                ]
            );
        } else {
            $conversations = Database::select(
                "SELECT conversation_guid, contact_guid,
                    (SELECT username FROM users WHERE user_guid = contact_guid) AS username,
                    (SELECT contact_alias FROM contacts WHERE contact_guid = conversations.contact_guid 
                        AND user_guid = conversations.user_guid) AS contact_alias,
                    (SELECT made_date FROM messages WHERE conversation_guid = conversations.conversation_guid 
                        AND (user1_guid = ? OR user2_guid = ?) ORDER BY made_date DESC LIMIT 1) AS made_date
                    FROM conversations
                    WHERE user_guid = ?
                    AND made_date > ?
                    ORDER BY made_date DESC;",
                [
                    $_SESSION[USESSION]->user_guid,
                    $_SESSION[USESSION]->user_guid,
                    $_SESSION[USESSION]->user_guid,
                    $made_date
                ]
            );
        }
        
        return $conversations;
    }
    
    public function getNew($guid)
    {
        return Database::select(
            "SELECT contact_guid, made_date, 
                (SELECT username FROM users WHERE user_guid = contact_guid) AS username 
                FROM contacts 
                WHERE contact_guid = ? 
                AND user_guid = ?;",
            [
                $guid,
                $_SESSION[USESSION]->user_guid
            ]
        );
    }
    
    public function delete($guid)
    {
        Database::update(
            "DELETE FROM messages WHERE conversation_guid = ? AND (user1_guid = ? OR user2_guid = ?);",
            [
                $guid,
                $_SESSION[USESSION]->user_guid,
                $_SESSION[USESSION]->user_guid
            ]
        );
        
        return Database::update(
            "DELETE FROM conversations WHERE conversation_guid = ? AND (user_guid = ? OR contact_guid = ?);",
            [
                $guid,
                $_SESSION[USESSION]->user_guid,
                $_SESSION[USESSION]->user_guid
            ]
        );
    }
    
    public function getConversations($made_date = null)
    {
        $conversations = [];
        $conversation_data = $this->getAll($made_date);
        
        foreach ($conversation_data as $conversation) {
            array_push(
                $conversations,
                new Conversation(
                    $conversation['conversation_guid'],
                    $conversation['contact_guid'],
                    htmlspecialchars($conversation['username']),
                    htmlspecialchars($conversation['contact_alias']),
                    $conversation['made_date']
                )
            );
        }
        
        return $conversations;
    }
}
