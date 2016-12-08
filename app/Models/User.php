<?php
/*
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 08-Dec-2016
 * Made Date: 04-Nov-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Models;

use Messenger\Core\Functions;
use Messenger\Core\Database;
use Messenger\Core\Session;
use Messenger\Core\PublicPrivateKey;
use Messenger\Models\Conversations;
use Messenger\ThirdParty\Google2FA;

class User
{
    public $user_id = null;
    public $user_guid = null;
    public $username = null;
    public $passphrase = null;
    public $timezone = null;
    public $mfa_enabled = null;

    /*
     *
     * Get user object.
     *
     */
    public function getUser($userid, $passphrase)
    {
        $user_details = Database::select(
            "SELECT user_id, user_guid, username, timezone, mfa_enabled FROM users WHERE user_id = ?;",
            [$userid]
        );
 
        if (count($user_details) > 0) {
            $user = new User();
            $user->user_id = $user_details[0]['user_id'];
            $user->user_guid = $user_details[0]['user_guid'];
            $user->username = htmlspecialchars($user_details[0]['username']);
            $user->passphrase = $passphrase;
            $user->timezone = htmlspecialchars($user_details[0]['timezone']);
            $user->mfa_enabled = $user_details[0]['mfa_enabled'];
            return $user;
        } else {
            return false;
        }
    }

    /*
     *
     * Register user.
     *
     */
    public function register($username, $password, $passphrase, $timezone)
    {    
        $username = Functions::cleanInput($username, 1);
        $passphrase = Functions::cleanInput($passphrase, 1);
        $timezone = Functions::cleanInput($timezone, 1);
        
        if ($passphrase == '') {
            $passphrase = null;
        }
        
        //Check for errors
        if ($password == null) {
            return 1;
        } else {
            if (strlen($password) < 9) {
                return 2;
            }
        }
        if (strlen(trim($username)) < 1) {
            return 8;
        }
        if (strlen($username) > 63) {
            return 3;
        }
        if ($timezone == -1) {
            return 7;
        }

        //Continue
        if (!isset($error)) {
            //Check for existing user
            $existing = Database::select("SELECT user_id FROM users WHERE username = ?;", [$username]);
            if (count($existing) > 0) {
                return 4;
            }

            //Get password hash
            $password = password_hash($password, PASSWORD_DEFAULT, ['cost' => PW_COST]);
            $guid = Functions::generateRandomString(32);
            $activation = Functions::generateRandomString(32);
            $secretkey = \Messenger\ThirdParty\Google2FA::generate_secret_key();

            //Create PPK
            if (!STORE_KEYS_LOCAL) {
                //Generate key pair
                $keys = PublicPrivateKey::generateKeyPair(
                    $guid,
                    $guid,
                    true,
                    $passphrase
                );

                \Messenger\ThirdParty\S3::setAuth(S3_ACCESS_KEY, S3_SECRET_KEY);

                //Put public key
                \Messenger\ThirdParty\S3::putObject(
                    $keys[0],
                    KEY_BUCKET,
                    $guid . ".pem",
                    Messenger\ThirdParty\S3::ACL_PRIVATE,
                    [],
                    ['Content-Type' => 'application/x-pem-file']
                );

                //Put private key
                \Messenger\ThirdParty\S3::putObject(
                    $keys[1],
                    KEY_BUCKET,
                    $guid . ".key",
                    \Messenger\ThirdParty\S3::ACL_PRIVATE,
                    [],
                    ['Content-Type' => 'application/x-iwork-keynote-sffkey']
                );
            } else {
                //Save public and private to local folder
                $keys = PublicPrivateKey::generateKeyPair(
                    BASE_DIR . "/keys/public/$guid",
                    BASE_DIR . "/keys/private/$guid",
                    false,
                    $passphrase
                );
                
                if (!$keys) {
                    return 5;
                }
            }

            //Create user
            $insertid = Database::insert(
                "INSERT INTO users (user_guid, username, password, secret_key, activation, timezone, modified) VALUES (?,?,?,?,?,?,NOW());",
                [
                    $guid,
                    $username,
                    $password,
                    $secretkey,
                    $activation,
                    $timezone
                ]
            );

            if ($insertid > -1) {
                //Finally, if we get to this point, set sessions and login
                Session::start();
                $_SESSION['user_id'] = $insertid;
                $_SESSION['login_string'] = hash('sha512', $insertid . $_SERVER['HTTP_USER_AGENT'] . $guid);
                $_SESSION[USESSION] = self::getUser($insertid, $passphrase);

                return 0;
            } else {
                return 6;
            }
        }
    }

    /*
     *
     * Login.
     *
     */
    public function login($username, $password, $passphrase, $mfa)
    {
        $username = Functions::cleanInput($username, 1);
        $passphrase = Functions::cleanInput($passphrase, 1);
        
        //Check for errors
        if ($password == null) {
            return 1;
        } elseif (strlen($password) < 9) {
            return 2;
        }
        if (strlen(trim($username)) < 1) {
            return 8;
        }
        if (strlen($username) > 63) {
            return 3;
        }

        //Check for existing user
        $existing = Database::select(
            "SELECT user_id, user_guid, password, mfa_enabled, secret_key FROM users WHERE username = ?;",
            [$username]
        );

        if (count($existing) > 0) {
            //Check for brute force
            $attempts = Database::select(
                "SELECT made_date FROM login_attempts WHERE user_id = ? AND made_date > DATE_SUB(NOW(), INTERVAL 2 HOUR);",
                [$existing[0]['user_id']]
            );

            //If there have been more than 5 failed logins
            if (count($attempts) < 5) {
                //Check password
                if (password_verify($password, $existing[0]['password'])) {
                    //Check if password needs rehash
                    if (password_needs_rehash(
                        $existing[0]['password'],
                        PASSWORD_DEFAULT,
                        ['cost' => PW_COST]
                    )) {
                        $newhash = password_hash(
                            $password,
                            PASSWORD_DEFAULT,
                            ['cost' => PW_COST]
                        );
                            
                        Database::update(
                            "UPDATE users SET password = ?, modified = now() WHERE user_id = ?;",
                            [
                                $newhash,
                                $existing[0]['user_id']
                            ]
                        );
                    }

                    //Check if MFA enabled
                    if ($existing[0]['mfa_enabled'] == -1) {
                        $rmfa = \Messenger\ThirdParty\Google2FA::verify_key($existing[0]['secret_key'], $mfa);

                        if (!$rmfa) {
                            Database::update(
                                "INSERT INTO login_attempts(user_id, made_date) VALUES (?, NOW());",
                                [$existing[0]['user_id']]
                            );

                            return 7;
                        }
                    }


                    //Finally, if we get to this point, set sessions and login
                    Session::start();
                    $_SESSION['user_id'] = $existing[0]['user_id'];
                    $_SESSION['login_string'] = hash('sha512', $existing[0]['user_id'] . $_SERVER['HTTP_USER_AGENT'] . $existing[0]['user_guid']);
                    $_SESSION[USESSION] = self::getUser($_SESSION['user_id'], $passphrase, $mysqli);

                    //Redirect
                    header('Location: conversations');
                } else {
                    Database::update(
                        "INSERT INTO login_attempts(user_id, made_date) VALUES (?, NOW());",
                        [$existing[0]['user_id']]
                    );
                    return 6;
                }
            } else {
                return 5;
            }
        } else {
            return 4;
        }
    }

    /*
     *
     * Update user.
     *
     */
    public function update($username, $timezone)
    {
        $username = Functions::cleanInput($username, 1);
        $timezone = Functions::cleanInput($timezone, 1);

        //Check for errors
        if (strlen(trim($username)) < 1) {
            return 4;
        }
        if (strlen($username) > 63) {
            return 1;
        }

        //Check for existing user
        $existing = Database::select(
            "SELECT user_id, username FROM users WHERE username = ?;",
            [$username]
        );

        if (count($existing) > 0 && $existing[0]['username'] != $_SESSION[USESSION]->username) {
            return 2;
        }

        if (Database::update(
            "UPDATE users SET username = ?, timezone = ? WHERE user_id = ? AND user_guid = ?;",
            [
                $username,
                $timezone,
                $_SESSION[USESSION]->user_id,
                $_SESSION[USESSION]->user_guid
            ]
        )) {
                $_SESSION[USESSION] = User::getUser($_SESSION[USESSION]->user_id, $_SESSION[USESSION]->passphrase);
                header('Location: conversations');
        } else {
            return 3;
        }
    }

    /*
     *
     * Enable MFA.
     *
     */
    public function enableMfa($code1, $code2)
    {
        $code1 = Functions::cleanInput($_POST['code1']);
        $code2 = Functions::cleanInput($_POST['code2']);

        if ($code1 == null || $code2 == null) {
            return 1;
        }

        $user = Database::select(
            "SELECT secret_key, mfa_enabled FROM users WHERE user_guid = ?;",
            [$_SESSION[USESSION]->user_guid]
        );

        $result1 = Google2FA::verify_key($user[0]['secret_key'], $code1);
        $result2 = Google2FA::verify_key($user[0]['secret_key'], $code2);

        if ($result1 && $result2) {
            if (Database::update(
                "UPDATE users SET mfa_enabled = -1 WHERE user_guid = ?;",
                [$_SESSION[USESSION]->user_guid]
            )) {
                $_SESSION[USESSION]->mfa_enabled = -1;
                return 0;
            }
        } else {
            return 2;
        }
    }

    /*
     *
     * Disable MFA.
     *
     */
    public function disableMfa()
    {
        Database::update(
            "UPDATE users SET mfa_enabled = 0 WHERE user_guid = ?;",
            [$_SESSION[USESSION]->user_guid]
        );

        $_SESSION[USESSION]->mfa_enabled = 0;
    }

    /*
     *
     * Reset password.
     *
     */
    public function resetPassword($password, $new_password, $confirm_new_password)
    {
        //Check for errors
        if ($new_password != $confirm_new_password) {
            return 10;
        }
        if (strlen($new_password) < 9) {
            return 11;
        }


        //Get user
        $user = Database::select(
            "SELECT user_id, user_guid, password FROM users WHERE user_id = ? AND user_guid = ?;",
            [
                $_SESSION[USESSION]->user_id,
                $_SESSION[USESSION]->user_guid
            ]
        );

        if (count($user) > 0) {
            //Check password
            if (password_verify($password, $user[0]['password'])) {
                //Generate password hash
                $newpass = password_hash(
                    $new_password,
                    PASSWORD_DEFAULT,
                    ['cost' => PW_COST]
                );

                //Update user
                if (Database::update(
                    "UPDATE users SET password = ? WHERE user_id = ? AND user_guid = ?;",
                    [
                        $newpass,
                        $_SESSION[USESSION]->user_id,
                        $_SESSION[USESSION]->user_guid
                    ]
                )) {
                    return 0;
                } else {
                    return 14;
                }
            } else {
                return 12;
            }
        } else {
            return 13;
        }
    }

    /*
     *
     * Delete user.
     *
     */
    public function delete($password)
    {
        //Get user
        $user = Database::select(
            "SELECT user_id, user_guid, password, mfa_enabled FROM users WHERE user_id = ? AND user_guid = ?;",
            [
                $_SESSION[USESSION]->user_id,
                $_SESSION[USESSION]->user_guid
            ]
        );

        if (count($user) > 0) {
            //Check password
            if (password_verify($password, $user[0]['password'])) {
                //Delete keys
                if (STORE_KEYS_LOCAL) {
                    unlink(BASE_DIR . "/keys/public/" . $user[0]['user_guid'] . ".pem");
                    unlink(BASE_DIR . "/keys/private/" . $user[0]['user_guid'] . ".key");
                } else {
                    S3::setAuth(S3_ACCESS_KEY, S3_SECRET_KEY);
                    S3::deleteObject(KEY_BUCKET, $guid . ".pem");
                    S3::deleteObject(KEY_BUCKET, $guid . ".key");
                }

                //Delete messages
                if (!Database::update(
                    "DELETE FROM messages WHERE (user1_guid = ? OR user2_guid = ?);",
                    [
                        $user[0]['user_guid'],
                        $user[0]['user_guid']
                    ]
                )) {
                    return 20;
                }

                //Delete conversations
                if (!Database::update(
                    "DELETE FROM conversations WHERE (contact_guid = ? OR user_guid = ?);",
                    [
                        $user[0]['user_guid'],
                        $user[0]['user_guid']
                    ]
                )) {
                    return 21;
                }

                //Delete contacts
                if (!Database::update(
                    "DELETE FROM contacts WHERE (contact_guid = ? OR user_guid = ?);",
                    [
                        $user[0]['user_guid'],
                        $user[0]['user_guid']
                    ]
                )) {
                    return 22;
                }

                //Delete account
                if (!Database::update(
                    "DELETE FROM users WHERE user_guid = ?;",
                    [$user[0]['user_guid']]
                )) {
                    return 23;
                }

                return 0;
            } else {
                return 12;
            }
        } else {
            return 13;
        }
    }

    /*
     *
     * Update last load.
     *
     */
    public function updateLastLoad()
    {
        return Database::update(
            "UPDATE users SET last_load = NOW() WHERE user_guid = ?;",
            [$_SESSION[USESSION]->user_guid]
        );
    }
}
