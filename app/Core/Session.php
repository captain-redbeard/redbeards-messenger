<?php
/*
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 06-Dec-2016
 * Made Date: 05-Nov-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Core;

class Session
{
    /*
     *
     * Start a secure session.
     *
     */
    public static function start()
    {
        $session_name = USESSION;
        $secure = SECURE;
        $http_only = true;

        //Gets current cookies params.
        $cookie_params = session_get_cookie_params();
        session_set_cookie_params(
            $cookie_params["lifetime"],
            $cookie_params["path"],
            $cookie_params["domain"],
            $secure,
            $http_only
        );
        
        //Sets the session name to the one set above.
        session_name($session_name);
        session_start();
        session_regenerate_id();
    }

    /**
     *
     * Kill the current session.
     *
     * */
    public static function kill()
    {
        self::start();
        
        //Unset all session values
        $_SESSION = [];
         
        //Get session parameters
        $params = session_get_cookie_params();
         
        //Delete the actual cookie.
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
         
        // Destroy session
        session_destroy();

        return true;
    }

    /*
     *
     * Check if the login string matches.
     *
     */
    public static function loginCheck()
    {
        //Check sessions are set
        if (isset($_SESSION['user_id'], $_SESSION['login_string'])) {
            $user = Database::select(
                "SELECT user_id, user_guid FROM users WHERE user_id = ? LIMIT 1;",
                [$_SESSION['user_id']]
            );

            //Check there is only one returned result
            if (count($user) == 1) {
                $login_check = hash('sha512', $user[0]['user_id'] . $_SERVER['HTTP_USER_AGENT'] . $user[0]['user_guid']);

                //Check if our login strings match
                if ($login_check == $_SESSION['login_string']) {
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
