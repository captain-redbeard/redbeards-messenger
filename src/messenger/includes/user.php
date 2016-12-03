<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 03-Dec-2016
 * Made Date: 25-Nov-2016
 * Author: Hosvir
 * 
 * */
class User
{
    public $user_id;
    public $user_guid;
    public $username;
    public $passphrase;
    public $timezone;
 
    public static function getUser($userid, $passphrase)
    {
        $user_details = QueryBuilder::select(
            "SELECT user_id, user_guid, username, timezone FROM users WHERE user_id = ?;",
            array($userid)
        );
 
        if (count($user_details) > 0) {
            $user = new User();
            $user->user_id = $user_details[0]['user_id'];
            $user->user_guid = $user_details[0]['user_guid'];
            $user->username = $user_details[0]['username'];
            $user->passphrase = $passphrase;
            $user->timezone = $user_details[0]['timezone'];
            return $user;
        } else {
            return false;
        }
    }
}
