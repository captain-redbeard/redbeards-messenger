<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 03-Dev-2016
 * Made Date: 25-Nov-2016
 * Author: Hosvir
 * 
 * */

/**
 * 
 * Start a secure session.
 * 
 * */
function secureSessionStart()
{
    $session_name = USESSION; 
    $secure = SECURE;
    $http_only = true;

    //Gets current cookies params.
    $cookie_params = session_get_cookie_params();
    session_set_cookie_params($cookie_params["lifetime"],
        $cookie_params["path"],
        $cookie_params["domain"],
        $secure,
        $http_only);
    
    //Sets the session name to the one set above.
    session_name($session_name);
    session_start();
    session_regenerate_id();
}

/**
 * 
 * Check if the login string matches.
 * 
 * */
function loginCheck() {
    //Check sessions are set
    if (isset($_SESSION['user_id'], $_SESSION['login_string'])) {
        $user = QueryBuilder::select(
            "SELECT user_id, user_guid FROM users WHERE user_id = ? LIMIT 1;",
            array($_SESSION['user_id'])
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
