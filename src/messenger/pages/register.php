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

//Check for post
if (isset($_POST['username'])) { 
    $username = cleanInput($_POST['username'], 1);
    $password = $_POST['password'];
    $passphrase = cleanInput($_POST['passphrase'], 1);
    $timezone = cleanInput($_POST['timezone'], 1);

    //Register
    include(dirname(__FILE__) . "/../includes/register.php");
    $code = register($username, $password, $passphrase, $timezone);

    switch($code) {
        case 0:
            //Success
            header('Location: conversations');
            break;
        case 1:
            $error = "No password entered.";
            break;
        case 2:
            $error = "Password must be greater than 8 characters.";
            break;
        case 3:
            $error = "Username must be less than 64 characters.";
            break;
        case 4:
            $error = "Username is already taken.";
            break;
        case 5:
            $error = "Failed to create PPK.";
            break;
        case 6:
            $error = "Failed to create user, contact support.";
            break;
        case 7:
            $error = "You must select a Timezone.";
            break;
    }
}

//Get timezone list
$tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
?>

            <div class="cover-wrapper">
                <form method="POST" action="">
                    <table class="single-table">
                        <thead>
                            <tr>
                                <th>Register</th>
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
                                    <input class="glow w100" type="password" name="passphrase" title="Private key passphrase" tabindex="3" placeholder="Private key passphrase (optional)">
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <select class="glow w100" name="timezone" tabindex="4">
                                        <?php if(!isset($timezone)) { ?>

                                        <option value="-1" selected disabled>Select Timezone</option>
                                        <?php } foreach($tzlist as $tz) { ?>

                                        <option value="<?php echo $tz; ?>" <?php if(isset($timezone) && $tz == $timezone) echo "selected"; ?>><?php echo $tz; ?></option>
                                        <?php } ?>
                                    </select>
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
                                    <a class="font-color-blue" href="login">Login</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
