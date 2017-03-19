<?php
/**
 * @author captain-redbeard
 * @since 07/12/16
 */
namespace Messenger\Models;

use Redbeard\Crew\Config;
use Redbeard\Crew\Database;
use Redbeard\Crew\Utils\Dates;
use Redbeard\Crew\Utils\Strings;

class Request
{
    public $request_guid = null;
    public $request_name = null;
    public $user_guid = null;
    public $expire = null;
    public $expire_time = null;
    public $url = null;
    
    public function __construct(
        $request_guid = null,
        $request_name = null,
        $user_guid = null,
        $expire = null,
        $expire_time = null,
        $url = null
    ) {
        $this->request_guid = $request_guid;
        $this->request_name = $request_name;
        $this->user_guid = $user_guid;
        $this->expire = $expire;
        $this->expire_time = $expire_time;
        $this->url = $url;
    }
    
    public function add($name, $expire)
    {
        $name = Strings::cleanInput($name);
        $expire = Strings::cleanInput($expire, 2);
        
        if (!is_numeric($expire)) {
            return 'Expire must be a number.';
        }
        
        if ($expire < 1) {
            $expire = 1;
        }
        
        if ($expire > 48) {
            $expire = 48;
        }
        
        $guid = Strings::generateRandomString(6);
        
        if (Database::insert(
            "INSERT INTO contact_requests (request_guid, request_name, user_guid, expire) VALUES (?,?,?,?);",
            [
                $guid,
                $name,
                $_SESSION[Config::get('app.user_session')]->guid,
                $expire
            ]
        ) > -1) {
            return Config::get('app.base_href') . "/accept/" . $guid;
        } else {
            return 'Failed to add contact request, contact support.';
        }
    }
    
    public function delete($guid)
    {
        $guid = Strings::cleanInput($guid, 2);
        
        if(!Database::update(
            "DELETE FROM contact_requests WHERE request_guid = ? AND user_guid = ?;",
            [
                $guid,
                $_SESSION[Config::get('app.user_session')]->user_guid
            ]
        )) {
            return 'Failed to delete request, contact support.';
        }
        
        return 0;
    }
    
    public function accept($guid)
    {
        $request = Database::select(
            "SELECT request_guid, user_guid FROM contact_requests WHERE request_guid = ?;",
            [$guid]
        );
        
        if (count($request) > 0 && $request[0]['user_guid'] != $_SESSION[Config::get('app.user_session')]->guid) {
            if (Database::insert(
                "INSERT INTO contacts (user_guid, contact_guid) VALUES (?,?);",
                [
                    $_SESSION[Config::get('app.user_session')]->guid,
                    $request[0]['user_guid']
                ]
            ) > -1) {
                Database::insert(
                    "INSERT INTO contacts (user_guid, contact_guid) VALUES (?,?);",
                    [
                        $request[0]['user_guid'],
                        $_SESSION[Config::get('app.user_session')]->guid
                    ]
                );
                
                Database::update(
                    "DELETE FROM contact_requests WHERE request_guid = ?;",
                    [$guid]
                );
                
                unset($_SESSION['request']);
                
                return 0;
            }
        } else {
            if ($request[0]['user_guid'] == $_SESSION[Config::get('app.user_session')]->guid) {
                unset($_SESSION['request']);
                return 'You can\'t add yourself???';
            } else {
                return 'Request expired or invalid.';
            }
        }
    }
    
    public function getRequests()
    {
        //Define
        $requests = [];
        
        //Get data
        $request_data = Database::select(
            "SELECT request_guid, request_name, user_guid, expire, 
                DATE_ADD(made_date, INTERVAL expire HOUR) as expire_time
                FROM contact_requests
                WHERE user_guid = ?;",
            [$_SESSION[Config::get('app.user_session')]->guid]
        );
        
        //Add to array
        foreach ($request_data as $request) {
            $expire_time = Dates::niceTime($request['expire_time']);
            if (Strings::contains('ago', $expire_time)) {
                $expire_time = 'Expired';
            }
            
            array_push(
                $requests,
                new Request(
                    $request['request_guid'],
                    htmlspecialchars($request['request_name']),
                    $request['user_guid'],
                    htmlspecialchars($request['expire']),
                    $expire_time,
                    Config::get('app.base_href') . '/accept/' . $request['request_guid']
                )
            );
        }
        
        //Return
        return $requests;
    }
}
