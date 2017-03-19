<?php
/**
 * @author captain-redbeard
 * @since 06/12/16
 */
namespace Messenger\Models;

use Redbeard\Crew\Config;
use Redbeard\Crew\Database;
use Redbeard\Crew\Utils\Strings;
use Redbeard\Crew\Utils\Dates;

class Contact
{
    public $contact_guid = null;
    public $alias = null;
    public $username = null;
    public $made_date = null;
    
    public function __construct($contact_guid = null, $alias = null, $username = null, $made_date = null)
    {
        $this->contact_guid = $contact_guid;
        $this->alias = $alias;
        $this->username = $username;
        $this->made_date = $made_date;
    }
    
    public function getContacts()
    {
        $contacts = [];
        
        $contact_data = Database::select(
            "SELECT contact_guid, contact_alias, made_date,
                (SELECT username FROM users WHERE user_guid = contact_guid) AS username
                FROM contacts
                WHERE user_guid = ?;",
            [$_SESSION[Config::get('app.user_session')]->guid]
        );
        
        foreach ($contact_data as $contact) {
            array_push(
                $contacts,
                new Contact(
                    $contact['contact_guid'],
                    htmlspecialchars($contact['contact_alias']),
                    htmlspecialchars($contact['username']),
                    $contact['made_date']
                )
            );
        }
        
        return $contacts;
    }
    
    public function getByGuid($guid)
    {
        $contact = null;
        
        $contact_data = Database::select(
            "SELECT contact_guid, contact_alias, made_date, 
                (SELECT username FROM users WHERE user_guid = contact_guid) AS username
                FROM contacts
                WHERE contact_guid = ? 
                AND user_guid = ?;",
            [$guid, $_SESSION[Config::get('app.user_session')]->guid]
        );
        
        if (count($contact_data) > 0) {
            $contact = new Contact(
                $contact_data[0]['contact_guid'],
                htmlspecialchars($contact_data[0]['contact_alias']),
                htmlspecialchars($contact_data[0]['username']),
                $contact_data[0]['made_date']
            );
        }
        
        return $contact;
    }
    
    public function setAlias($alias)
    {
        $alias = Strings::cleanInput($alias);
        
        if (strlen($alias) > 63) {
            return 'Alias must be less than 64 characters.';
        }
        
        if (Database::update(
            "UPDATE contacts SET contact_alias = ? WHERE contact_guid = ? AND user_guid = ?;",
            [
                $alias,
                $this->contact_guid,
                $_SESSION[Config::get('app.user_session')]->guid
            ]
        )) {
            return 0;
        } else {
            return 'Failed to save contact, contact support.';
        }
    }
    
    public function delete($guid)
    {
        if(!Database::update(
            "DELETE FROM messages WHERE (user1_guid = ? OR user1_guid = ?) 
                AND (user2_guid = ? OR user2_guid = ?);",
            [
                $_SESSION[Config::get('app.user_session')]->guid,
                $guid,
                $_SESSION[Config::get('app.user_session')]->guid,
                $guid
            ]
        )) {
            return 'Failed to delete messages, contact support.';
        }
        
        if(!Database::update(
            "DELETE FROM conversations WHERE (user_guid = ? OR user_guid = ?) 
                AND (contact_guid = ? OR contact_guid = ?);",
            [
                $_SESSION[Config::get('app.user_session')]->guid,
                $guid,
                $_SESSION[Config::get('app.user_session')]->guid,
                $guid
            ]
        )) {
            return 'Failed to delete conversations, contact support.';
        }
        
        if(!Database::update(
            "DELETE FROM contacts WHERE (user_guid = ? OR user_guid = ?) 
                AND (contact_guid = ? OR contact_guid = ?);",
            [
                $_SESSION[Config::get('app.user_session')]->guid,
                $guid,
                $_SESSION[Config::get('app.user_session')]->guid,
                $guid
            ]
        )) {
            return 'Failed to delete contacts, contact support.';
        }
        
        return 0;
    }
    
    public function getMadeDate()
    {
        return Dates::convertTime($this->made_date, false);
    }
}
