<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 25-Nov-2016
 * Made Date: 25-Nov-2016
 * Author: Hosvir
 * 
 * */
 
/**
 * Start a secure session.
 * */
function sec_session_start() {
    $session_name = USESSION; 
    $secure = SECURE;
    $httponly = true;
    
    //Forces sessions to only use cookies.
    if(!ini_set('session.use_only_cookies', 1)) {
		header("Location: error.php?err=Could not initiate a safe session (ini_set)");
    }

    //Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],$cookieParams["path"],$cookieParams["domain"],$secure,$httponly);
    
    //Sets the session name to the one set above.
    session_name($session_name);
    session_start();
    session_regenerate_id();
}

/**
 * Check if the login string matches.
 * */
function login_check($mysqli) {
	//Check sessions are set
    if(isset($_SESSION['user_id'], $_SESSION['login_string'])) {
        $user = QB::select("SELECT user_id, user_guid FROM users WHERE user_id = ? LIMIT 1;", array($_SESSION['user_id']), $mysqli);
 
		//Check there is only one returned result
		if(count($user) == 1) {
			$login_check = hash('sha512', $user[0]['user_id'] . $_SERVER['HTTP_USER_AGENT'] . $user[0]['user_guid']);

			//Check if our login strings match
			if($login_check == $_SESSION['login_string']) {
				return true;
			}else {
				return false;
			}
		}else {
			return false;
		}
	}else {
		return false;
	}
}
?>
