<?php
/**
 * @author captain-redbeard
 * @since 20/01/17
 */
namespace Messenger\Models;

use Redbeard\Crew\Config;
use Redbeard\Crew\Session;
use Redbeard\Crew\Tracking;
use Redbeard\Crew\Database;
use Redbeard\Crew\Utils\Dates;
use Redbeard\Crew\Utils\Strings;
use Redbeard\Crew\Utils\Validator;
use Redbeard\Crew\Encryption\RSA;
use Redbeard\Crew\ThirdParty\Google2FA;
use Endroid\QrCode\QrCode;

class User
{
    public $id = null;
    public $guid = null;
    public $username = null;
    public $passphrase = null;
    public $timezone = null;
    public $secret_key = null;
    public $activation = null;
    public $mfa_enabled = null;
    public $modified = null;
    public $made_date = null;
    public $expire = null;
    
    public function __construct(
        $id = null,
        $guid = null,
        $username = null,
        $passphrase = null,
        $timezone = null,
        $secret_key = null,
        $activation = null,
        $mfa_enabled = null,
        $modified = null,
        $made_date = null,
        $expire = null
    )
    {
        $this->id = $id;
        $this->guid = $guid;
        $this->username = $username;
        $this->passphrase = $passphrase;
        $this->timezone = $timezone;
        $this->secret_key = $secret_key;
        $this->activation = $activation;
        $this->mfa_enabled = $mfa_enabled;
        $this->modified = $modified;
        $this->made_date = $made_date;
        $this->expire = $expire;
    }
    
    public function getUser($user_id, $passphrase)
    {
        //Define
        $user = null;
        
        //Get details
        $user_details = Database::select(
            "SELECT user_id, user_guid, username, timezone, secret_key, activation, mfa_enabled, expire,
            modified, made_date
            FROM users
            WHERE user_id = ?;",
            [$user_id]
        );
        
        $user_details = $user_details[0];
        $user = new User(
            $user_details['user_id'],
            $user_details['user_guid'],
            htmlspecialchars($user_details['username']),
            $passphrase,
            htmlspecialchars($user_details['timezone']),
            htmlspecialchars($user_details['secret_key']),
            htmlspecialchars($user_details['activation']),
            $user_details['mfa_enabled'],
            $user_details['modified'],
            $user_details['made_date'],
            $user_details['expire']
        );
           
        //Return
        return $user;
    }
    
    public function register($username, $password, $confirm_password, $passphrase, $timezone = 'UTC', $set_session = true)
    {
        $username = Strings::cleanInput($username);
        $timezone = Strings::cleanInput($timezone, 1);
        
        $validUsername = Validator::validateLength('Username', $username, 3, 64);
        $validPassword = Validator::validateLength('Password', $password, 8, 256);
        
        if ($validUsername !== 0) {
            return $validUsername;
        }
        
        if ($validPassword !== 0) {
            return $validPassword;
        }
        
        if ($password !== $confirm_password) {
            return 'Passwords don\'t match.';
        }
        
        if ($timezone === -1) {
            return 'You must select a Timezone.';
        }
        
        if (!isset($error)) {
            $existing = Database::select("SELECT user_id FROM users WHERE username = ?;", [$username]);
            if (count($existing) > 0) {
                return 'Username is already taken.';
            }
            
            $password = password_hash($password, PASSWORD_DEFAULT, ['cost' => Config::get('app.password_cost')]);
            $guid = Strings::generateRandomString(32);
            $activation = Strings::generateRandomString(32);
            $secretkey = Google2FA::generateSecretKey();
            
            //Create ppk
            $keys = PublicPrivateKey::generateKeyPair(
                Config::get('app.base_dir') . Config::get('keys.public_folder') . $guid,
                Config::get('app.base_dir') . Config::get('keys.private_folder') . $guid,
                false,
                $passphrase
            );
            
            if (!$keys) {
                return 'Failed to create PPK, contact support.';
            }
            
            //Insert user
            $user_id = Database::insert(
                "INSERT INTO users (user_guid, username, password, secret_key, activation, timezone, modified) 
                    VALUES (?,?,?,?,?,?,NOW());",
                [
                    $guid,
                    $username,
                    $password,
                    $secretkey,
                    $activation,
                    $timezone
                ]
            );
            
            if ($user_id > -1) {
                if ($set_session) {
                    Session::start();
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['login_string'] = hash('sha512', $user_id . $_SERVER['HTTP_USER_AGENT'] . $guid);
                    $_SESSION[Config::get('app.user_session')] = $this->getUser($user_id, $passphrase);
                }
                return 0;
            } else {
                return 'Failed to create user, contact support.';
            }
        }
    }
    
    public function login($username, $password, $passphrase, $mfa = null)
    {
        $username = Strings::cleanInput($username);
        
        $validUsername = Validator::validateLength('Username', $username, 3, 64);
        $validPassword = Validator::validateLength('Password', $password, 8, 256);
        
        if ($validUsername !== 0) {
            return $validUsername;
        }
        
        if ($validPassword !== 0) {
            return $validPassword;
        }
        
        $existing = Database::select(
            "SELECT user_id, user_guid, password, mfa_enabled, secret_key
            FROM users
            WHERE username = ?;",
            [$username]
        );
        
        if (count($existing) > 0) {
            $attempts = Database::select(
                "SELECT made_date FROM login_attempts WHERE user_id = ? 
                    AND made_date > DATE_SUB(NOW(), INTERVAL 2 HOUR);",
                [$existing[0]['user_id']]
            );
            
            if (count($attempts) < Config::get('app.max_login_attempts')) {
                if (password_verify($password, $existing[0]['password'])) {
                    if (password_needs_rehash(
                        $existing[0]['password'],
                        PASSWORD_DEFAULT,
                        ['cost' => Config::get('app.password_cost')]
                    )) {
                        $newhash = password_hash(
                            $password,
                            PASSWORD_DEFAULT,
                            ['cost' => Config::get('app.password_cost')]
                        );
                            
                        Database::update(
                            "UPDATE users SET password = ?, modified = now() WHERE user_id = ?;",
                            [
                                $newhash,
                                $existing[0]['user_id']
                            ]
                        );
                    }
                    
                    if ($existing[0]['mfa_enabled']) {
                        $rmfa = Google2FA::verifyKey($existing[0]['secret_key'], $mfa);
                        
                        if (!$rmfa) {
                            Database::update(
                                "INSERT INTO login_attempts(user_id, made_date) VALUES (?, NOW());",
                                [$existing[0]['user_id']]
                            );
                            
                            return 'MFA Failed.';
                        }
                    }
                    
                    Session::start();
                    $_SESSION['user_id'] = $existing[0]['user_id'];
                    $_SESSION['login_string'] = hash(
                        'sha512',
                        $existing[0]['user_id'] . $_SERVER['HTTP_USER_AGENT'] . $existing[0]['user_guid']
                    );
                    $_SESSION[Config::get('app.user_session')] = $this->getUser($_SESSION['user_id'], $passphrase);
                    
                    return 0;
                } else {
                    Database::update(
                        "INSERT INTO login_attempts(user_id, made_date) VALUES (?, NOW());",
                        [$existing[0]['user_id']]
                    );
                    return 'Incorrect password.';
                }
            } else {
                return 'To many login attempts, try again later.';
            }
        } else {
            return 'User not found.';
        }
    }
    
    public function update($username, $timezone = 'UTC', $set_session = true)
    {
        $username = Strings::cleanInput($username);
        $timezone = Strings::cleanInput($timezone, 1);
        
        $valid_username = Validator::validateLength('Username', $username, 3, 64);
        
        if ($valid_username !== 0) {
            return $valid_username;
        }
        
        $existing = Database::select(
            "SELECT user_id, username FROM users WHERE username = ?;",
            [$username]
        );
        
        if (count($existing) > 0 && $existing[0]['username'] !== $this->username) {
            return 'Username already taken.';
        }
        
        if (Database::update(
            "UPDATE users SET username = ?, timezone = ? WHERE user_guid = ?;",
            [
                $username,
                $timezone,
                $this->guid
            ]
        )) {
            if ($set_session) {
                $_SESSION[Config::get('app.user_session')] = $this->getUser($this->id, $this->passphrase);
            }
            return 0;
        } else {
            return 'Failed to update user, contact support.';
        }
    }
    
    public function enableMfa($code1, $code2)
    {
        $code1 = Strings::cleanInput($code1, 2);
        $code2 = Strings::cleanInput($code2, 2);
        
        if ($code1 === null || $code2 === null) {
            return 'You must provide two consecutive codes.';
        }
        
        $result1 = Google2FA::verifyKey($this->secret_key, $code1);
        $result2 = Google2FA::verifyKey($this->secret_key, $code2);
        
        if ($result1 && $result2) {
            if (Database::update(
                "UPDATE users SET mfa_enabled = 1 WHERE user_id = ? AND user_guid = ?;",
                [
                    $this->id,
                    $this->guid
                ]
            )) {
                $this->mfa_enabled = 1;
                return 0;
            }
        } else {
            return 'Invalid codes.';
        }
    }
    
    public function disableMfa()
    {
        Database::update(
            "UPDATE users SET mfa_enabled = 0
            WHERE user_id = ?
            AND user_guid = ?;",
            [
                $this->id,
                $this->guid
            ]
        );
        
        $this->mfa_enabled = 0;
        
        return 0;
    }
    
    public function updateExpire($expire)
    {
        $expire = Strings::cleanInput($expire, 2);
        
        if (!is_numeric($expire)) {
            return 1;
        }
        
        if ($expire < 0) {
            $expire = 0;
        }
        
        if ($expire > 90) {
            $expire = 90;
        }
        
        Database::update(
            "UPDATE users SET expire = ? WHERE user_id = ? AND user_guid = ?;",
            [
                $expire,
                $this->id,
                $this->guid
            ]
        );
        
        $this->expire = $expire;
        
        return 0;
    }
    
    public function resetPassword($password, $new_password, $confirm_new_password)
    {
        if ($new_password !== $confirm_new_password) {
            return 'Passwords don\'t match.';
        }
        
        $validPassword = Validator::validateLength('Password', $new_password, 8, 256);
        
        if ($validPassword !== 0) {
            return $validPassword;
        }
        
        $user = Database::select(
            "SELECT user_id, user_guid, password FROM users WHERE user_id = ? AND user_guid = ?;",
            [
                $this->id,
                $this->guid
            ]
        );

        if (count($user) > 0) {
            if (password_verify($password, $user[0]['password'])) {
                $newpass = password_hash(
                    $new_password,
                    PASSWORD_DEFAULT,
                    ['cost' => Config::get('app.password_cost')]
                );
                
                if (Database::update(
                    "UPDATE users SET password = ? WHERE user_id = ? AND user_guid = ?;",
                    [
                        $newpass,
                        $this->id,
                        $this->guid
                    ]
                )) {
                    return 0;
                } else {
                    return 'Failed to reset password, contact support.';
                }
            } else {
                return 'Incorrect password';
            }
        } else {
            return 'User not found.';
        }
    }
    
    public function generateNewKeypair($password, $passphrase)
    {
        $user = Database::select(
            "SELECT user_id, user_guid, password, mfa_enabled FROM users WHERE user_id = ? AND user_guid = ?;",
            [
                $this->id,
                $this->guid
            ]
        );

        if (count($user) > 0) {
            if (password_verify($password, $user[0]['password'])) {
                //Delete keys
                unlink(Config::get('app.base_dir') . Config::get('keys.public_folder') . $user[0]['user_guid'] . '.pem');
                unlink(Config::get('app.base_dir') . Config::get('keys.private_folder') . $user[0]['user_guid'] . '.key');
                
                if (!Database::update(
                    "DELETE FROM messages WHERE (user1_guid = ? OR user2_guid = ?);",
                    [
                        $user[0]['user_guid'],
                        $user[0]['user_guid']
                    ]
                )) {
                    return 'Failed to delete messages, contact support.';
                }
                
                if (!Database::update(
                    "DELETE FROM conversations WHERE (contact_guid = ? OR user_guid = ?);",
                    [
                        $user[0]['user_guid'],
                        $user[0]['user_guid']
                    ]
                )) {
                    return 'Failed to delete conversations, contact support.';
                }
                
                //Create keys
                $keys = PublicPrivateKey::generateKeyPair(
                    Config::get('app.base_dir') . Config::get('keys.public_folder') . $user[0]['user_guid'],
                    Config::get('app.base_dir') . Config::get('keys.private_folder') . $user[0]['user_guid'],
                    false,
                    $passphrase
                );
                
                if (!$keys) {
                    return 'Failed to create PPK, contact support.';
                }
                
                $_SESSION[Config::get('app.user_session')] = $this->getUser($this->id, $passphrase);
                return 0;
            } else {
                return 'Incorrect password.';
            }
        }
    }
    
    public function delete($password)
    {
        $user = Database::select(
            "SELECT user_id, user_guid, password, mfa_enabled FROM users WHERE user_id = ? AND user_guid = ?;",
            [
                $this->id,
                $this->guid
            ]
        );
        
        if (count($user) > 0) {
            if (password_verify($password, $user[0]['password'])) {
                //Delete keys
                unlink(Config::get('app.base_dir') . Config::get('keys.public_folder') . $user[0]['user_guid'] . '.pem');
                unlink(Config::get('app.base_dir') . Config::get('keys.private_folder') . $user[0]['user_guid'] . '.key');
               
                if (!Database::update(
                    "DELETE FROM messages WHERE (user1_guid = ? OR user2_guid = ?);",
                    [
                        $user[0]['user_guid'],
                        $user[0]['user_guid']
                    ]
                )) {
                    return 'Failed to delete messages, contact support.';
                }
                
                if (!Database::update(
                    "DELETE FROM conversations WHERE (contact_guid = ? OR user_guid = ?);",
                    [
                        $user[0]['user_guid'],
                        $user[0]['user_guid']
                    ]
                )) {
                    return 'Failed to delete conversations, contact support.';
                }
                
                if (!Database::update(
                    "DELETE FROM contacts WHERE (contact_guid = ? OR user_guid = ?);",
                    [
                        $user[0]['user_guid'],
                        $user[0]['user_guid']
                    ]
                )) {
                    return 'Failed to delete contacts, contact support.';
                }
                
                if (!Database::update(
                    "DELETE FROM users WHERE user_guid = ?;",
                    [$user[0]['user_guid']]
                )) {
                    return 'Failed to delete user, contact support.';
                }
                
                return 0;
            } else {
                return 'Incorrect password.';
            }
        }
    }
    
    public function updateLastLoad()
    {
        return Database::update(
            "UPDATE users SET last_load = NOW() WHERE user_guid = ?;",
            [$this->guid]
        );
    }
    
    public function getQrCode()
    {
        $qrCode = new QrCode();
        $qrCode
            ->setText("otpauth://totp/" .
                      Config::get('site.name') . ":" .
                      $this->username . "?secret=" .
                      $this->secret_key . "&issuer=" .
                      Config::get('site.name'))
            ->setSize(200)
            ->setPadding(0)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setImageType(QrCode::IMAGE_TYPE_PNG)
        ;
        
        return $qrCode;
    }
    
    public function getModified()
    {
        return Dates::niceTime($this->modified);
    }
    
    public function getMadeDate()
    {
        return Dates::convertTime($this->made_date);
    }
}
