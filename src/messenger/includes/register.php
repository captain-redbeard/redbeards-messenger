<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 03-Dec-2016
 * Made Date: 28-Nov-2016
 * Author: Hosvir
 * 
 * */

function register($username, $password, $passphrase, $timezone)
{
    //Check for errors
    if ($password == null) {
        return 1;
    } else {
        if (strlen($password) < 9) return 2;
    }
    if (strlen($username) > 63) return 3;
    if ($timezone == -1) return 7;

    //Continue
    if (!isset($error)) {
        //Check for existing user
        $existing = QueryBuilder::select("SELECT user_id FROM users WHERE username = ?;", array($username));
        if (count($existing) > 0) return 4;

        //MFA Secret Key
        include(dirname(__FILE__) . "/../thirdparty/googleauth.php");

        //Get password hash
        $password = password_hash($password, PASSWORD_DEFAULT, array('cost' => PW_COST));
        $guid = generateRandomString(32);
        $activation = generateRandomString(32);
        $secretkey = Google2FA::generate_secret_key();

        //Create PPK
        include(dirname(__FILE__) . "/publicprivatekey.php");
        if (!STORE_KEYS_LOCAL) {
            //Generate key pair
            $keys = PublicPrivateKey::generateKeyPair(
                $guid,
                $guid,
                true,
                $passphrase
            );
            
            include(dirname(__FILE__) . "/../thirdparty/S3.php");
            S3::setAuth(S3_ACCESS_KEY, S3_SECRET_KEY);

            //Put public key
            S3::putObject(
                $keys[0],
                KEY_BUCKET,
                $guid . ".pem",
                S3::ACL_PRIVATE,
                array(),
                array('Content-Type' => 'application/x-pem-file')
            );

            //Put private key
            S3::putObject(
                $keys[1],
                KEY_BUCKET,
                $guid . ".key",
                S3::ACL_PRIVATE,
                array(),
                array('Content-Type' => 'application/x-iwork-keynote-sffkey')
            );
        } else {
            //Save public and private to local folder
            $keys = PublicPrivateKey::generateKeyPair(
                dirname(__FILE__) . "/../keys/public/$guid",
                dirname(__FILE__) . "/../keys/private/$guid",
                false,
                $passphrase
            );
            
            if (!$keys) return 5;
        }

        //Create user
        $insertid = QueryBuilder::insert(
            "INSERT INTO users (user_guid, username, password, secret_key, activation, timezone, modified) VALUES (?,?,?,?,?,?,NOW());",
            array(
                $guid,
                $username,
                $password,
                $secretkey,
                $activation,
                $timezone
            )
        );

        if ($insertid > -1) {
            //Finally, if we get to this point, set sessions and login
            include(dirname(__FILE__) . "/authentication.php");
            include(dirname(__FILE__) ."/user.php");
            secureSessionStart(); 
            $_SESSION['user_id'] = $insertid;
            $_SESSION['login_string'] = hash('sha512', $insertid . $_SERVER['HTTP_USER_AGENT'] . $guid);
            $_SESSION[USESSION] = User::getUser($insertid, $passphrase);

            return 0;
        } else {
            return 6;
        }
    }
}
