<?php
/**
 * @author captain-redbeard
 * @since 09/12/16
 */
use Redbeard\Core\Config;
use Redbeard\Core\Database;
use Redbeard\Core\Functions;

require_once '../vendor/autoload.php';

//Load config
Config::init();

//Set base url
Config::set('app.base_href', Functions::getUrl());

//Set database config
Database::init(Config::get('database'));

//Set timezone
date_default_timezone_set(Config::get('app.timezone'));

//Get expired users
$users = Database::select(
    "SELECT user_id, user_guid FROM users WHERE expire > 0 AND last_load < (NOW() - INTERVAL expire DAY);",
    []
);

if (count($users) > 0) {
    //For each user
    foreach ($users as $user) {
        //Delete public private key pair
        if (Config::get('keys.store_local')) {
            unlink(Config::get('app.base_dir') . Config::get('keys.ppk_public_folder') . $user['user_guid'] . ".pem");
            unlink(Config::get('app.base_dir') . Config::get('keys.ppk_private_folder') . $user['user_guid'] . ".key");
        } else {
            S3::setAuth(Config::get('keys.s3_access_key'), Config::get('keys.s3_secret_key'));
            S3::deleteObject(Config::get('keys.bucket'), $guid . ".pem");
            S3::deleteObject(Config::get('keys.bucket'), $guid . ".key");
        }
        
        //Delete messages
        Database::update(
            "DELETE FROM messages WHERE (user1_guid = ? OR user2_guid = ?);",
            [
                $user['user_guid'],
                $user['user_guid']
            ]
        );
        
        //Delete conversations
        Database::update(
            "DELETE FROM conversations WHERE (contact_guid = ? OR user_guid = ?);",
            [
                $user['user_guid'],
                $user['user_guid']
            ]
        );
        
        //Delete contacts
        Database::update(
            "DELETE FROM contacts WHERE (contact_guid = ? OR user_guid = ?);",
            [
                $user['user_guid'],
                $user['user_guid']
            ]
        );
        
        //Delete user
        Database::update(
            "DELETE FROM users WHERE user_guid = ?;",
            [$user['user_guid']]
        );
    }
}
