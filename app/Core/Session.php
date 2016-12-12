<?php
/**
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 08-Dec-2016
 * Made Date: 05-Nov-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Core;

class Session
{
    public static function start()
    {
        if (!isset($_SESSION)) {
            $session_name = USESSION;
            $secure = SECURE;
            $http_only = true;
            
            $cookie_params = session_get_cookie_params();
            session_set_cookie_params(
                $cookie_params["lifetime"],
                $cookie_params["path"],
                $cookie_params["domain"],
                $secure,
                $http_only
            );
            
            session_name($session_name);
            session_start();
            session_regenerate_id();
        }
    }
    
    public static function kill()
    {
        self::start();
        
        $_SESSION = [];
        
        $params = session_get_cookie_params();
        
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
        
        session_destroy();
        
        return true;
    }
    
    public static function loginCheck()
    {
        if (isset($_SESSION['user_id'], $_SESSION['login_string'])) {
            $user = Database::select(
                "SELECT user_id, user_guid FROM users WHERE user_id = ? LIMIT 1;",
                [$_SESSION['user_id']]
            );
            
            if (count($user) === 1) {
                $login_check = hash(
                    'sha512',
                    $user[0]['user_id'] . $_SERVER['HTTP_USER_AGENT'] . $user[0]['user_guid']
                );
                
                if ($login_check === $_SESSION['login_string']) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
