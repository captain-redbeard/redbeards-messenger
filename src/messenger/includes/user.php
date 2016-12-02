<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 26-Nov-2016
 * Made Date: 25-Nov-2016
 * Author: Hosvir
 * 
 * */
class User {
	public $user_id;
	public $user_guid;
	public $username;
	public $passphrase;
	public $timezone;
 
    public static function getUser($userid, $passphrase, $mysqli) {
		$user = QB::select("SELECT user_id, user_guid, username, timezone FROM users WHERE user_id = ?;", array($userid), $mysqli);
 
        if(count($user) > 0) {
            $privUser = new User();
            $privUser->user_id = $user[0]['user_id'];
			$privUser->user_guid = $user[0]['user_guid'];
			$privUser->username = $user[0]['username'];
			$privUser->passphrase = $passphrase;
			$privUser->timezone = $user[0]['timezone'];
            return $privUser;
        }else {
            return false;
        }
    }
    
}
?>
