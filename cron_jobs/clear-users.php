<?php
/**
 * @author captain-redbeard
 * @since 09/12/16
 */
use Redbeard\Core\Database;

require_once '../app/config.php';
require_once '../vendor/autoload.php';

date_default_timezone_set(TIMEZONE);

//Get expired users
$users = Database::select(
    "SELECT user_id, user_guid FROM users WHERE expire > 0 AND last_load < (NOW() - INTERVAL expire DAY);",
    []
);

if (count($users) > 0) {
    //For each user
    foreach ($users as $user) {
        //Delete public private key pair
        if (STORE_KEYS_LOCAL) {
            unlink(BASE_DIR . PPK_PUBLIC_FOLDER . $user['user_guid'] . ".pem");
            unlink(BASE_DIR . PPK_PRIVATE_FOLDER . $user['user_guid'] . ".key");
        } else {
            S3::setAuth(S3_ACCESS_KEY, S3_SECRET_KEY);
            S3::deleteObject(KEY_BUCKET, $guid . ".pem");
            S3::deleteObject(KEY_BUCKET, $guid . ".key");
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
