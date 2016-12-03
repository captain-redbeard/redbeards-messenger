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

include_once(dirname(__FILE__) . "/../includes/authentication.php");
secureSessionStart(); 

//Redirect if already logged in
if ($loggedin && isset($_SESSION[USESSION])) {
    header('Location: conversations');
}

//Check for post
if (isset($_POST['username'])) { 
    $username = cleanInput($_POST['username'], 1);
    $password = $_POST['password'];
    $passphrase = cleanInput($_POST['passphrase'], 1);
    $mfa = cleanInput($_POST['mfa']);

    //Check for errors
    if ($password == null) {
        $error = "No password entered.";
    } elseif(strlen($password) < 9) {
        $error = "Password must be greater than 8 characters.";
    }
    if (strlen($username) > 63) {
        $error = "Username must be less than 64 characters.";
    }

    //Continue
    if (!isset($error)) {
        //Check for existing user
        $existing = QueryBuilder::select(
            "SELECT user_id, user_guid, password, mfa_enabled, secret_key FROM users WHERE username = ?;",
            array($username)
        );

        if (count($existing) > 0) {
            //Check for brute force
            $attempts = QueryBuilder::select(
                "SELECT made_date FROM login_attempts WHERE user_id = ? AND made_date > DATE_SUB(NOW(), INTERVAL 2 HOUR);",
                array($existing[0]['user_id'])
            );

            //If there have been more than 5 failed logins 
            if (count($attempts) < 5) {
                //Check password
                if (password_verify($password, $existing[0]['password'])) {
                    //Check if password needs rehash
                    if (password_needs_rehash($existing[0]['password'], PASSWORD_DEFAULT, array('cost' => PW_COST))) {
                        $newhash = password_hash($password, PASSWORD_DEFAULT, array('cost' => PW_COST));
                        
                        QueryBuilder::update(
                            "UPDATE users SET password = ?, modified = now() WHERE user_id = ?;",
                            array(
                                $newhash,
                                $existing[0]['user_id']
                            )
                        );
                    }

                    //Check if MFA enabled
                    if ($existing[0]['mfa_enabled'] == -1) {
                        include(dirname(__FILE__) . "/../thirdparty/googleauth.php");
                        $rmfa = Google2FA::verify_key($existing[0]['secret_key'], $mfa);

                        if (!$rmfa) {
                            QueryBuilder::update(
                                "INSERT INTO login_attempts(user_id, made_date) VALUES (?, NOW());",
                                array($existing[0]['user_id'])
                            );
                            $error = "MFA Failed.";
                        }
                    }

                    if (!isset($error)) {
                        //Finally, if we get to this point, set sessions and login
                        include(dirname(__FILE__) ."/../includes/user.php");
                        $_SESSION['user_id'] = $existing[0]['user_id'];
                        $_SESSION['login_string'] = hash('sha512', $existing[0]['user_id'] . $_SERVER['HTTP_USER_AGENT'] . $existing[0]['user_guid']);
                        $_SESSION[USESSION] = User::getUser($_SESSION['user_id'], $passphrase, $mysqli);

                        //Redirect
                        header('Location: conversations');
                    }
                } else {
                    $error = "Incorrect password.";
                    QueryBuilder::update(
                        "INSERT INTO login_attempts(user_id, made_date) VALUES (?, NOW());",
                        array($existing[0]['user_id'])
                    );
                }
            } else {
                $error = "To many login attempts, try again later.";
            }
        } else {
            $error = "Username not found.";
        }
    }
}
?>

            <div class="cover-wrapper">
                <form method="POST" action="">
                    <table class="single-table">
                        <thead>
                            <tr>
                                <th>Login</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <input class="glow w100" type="text" name="username" title="Username" tabindex="1" placeholder="Username" autofocus value="<?php if(isset($username)) echo $username; ?>">
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input class="glow w100" type="password" name="password" title="Password" tabindex="2" placeholder="Password">
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input class="glow w100" type="password" name="passphrase" title="Private key passphrase" tabindex="3" placeholder="Private key passphrase">
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input class="glow w100" type="text" name="mfa" title="MFA Code" tabindex="4" placeholder="MFA Code (if enabled)">
                                </td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <td>
                                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="5" value="Submit">
                                    <?php if(isset($error)) { ?>
                                    <br/>
                                    <div class="message-error"><?php echo $error; ?></div>
                                    <?php } ?>

                                </td> 
                            </tr>

                            <tr><td></td></tr>
                            <tr><td></td></tr>

                            <tr>
                                <td>
                                    <a href="register">Register</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
