<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 02-Dec-2016
 * Made Date: 25-Nov-2016
 * Author: Hosvir
 * 
 * */
include(dirname(__FILE__) . "/../includes/login_auth.inc.php");

//Check for post
if(isset($_POST['username'])) { 
	$username = clean_input($_POST['username'], 1);
	$timezone = clean_input($_POST['timezone'], 1);
	
	//Check for errors
	if(strlen($username) > 63) {
		$error = "Username must be less than 64 characters.";
	}
	
	//Check for existing user
	$existing = QB::select("SELECT user_id FROM users WHERE username = ?;", array($username), $mysqli);
			
	if(count($existing) > 0) {
		$error = "Username is already taken.";
	}

	//Continue
	if(!isset($error)) {	
		if(QB::update("UPDATE users SET username = ?, timezone = ? WHERE user_id = ? AND user_guid = ?;", 
						array($username, $timezone, $_SESSION[USESSION]->user_id, $_SESSION[USESSION]->user_guid), 
						$mysqli)) {
			$_SESSION[USESSION] = User::getUser($_SESSION['user_id'], $mysqli);
			header('Location: conversations');
		}else{
			$error = "Failed to save settings.";
		}
	}
}

//Get user details
$user = QB::select("SELECT secret_key, mfa_enabled FROM users WHERE user_guid = ?;", array($_SESSION[USESSION]->user_guid), $mysqli);

//Get timezone list
$tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
?>

			<div class="cover-wrapper">				
				<form method="POST" action="">
					<table class="single-table">
						<thead>
							<tr>
								<th>Settings</th>
							</tr>
						</thead>
						
						<tbody>
							<tr>
								<td>
									<input class="glow w100" type="text" name="username" title="Username" tabindex="1" placeholder="Username" value="<?php echo $_SESSION[USESSION]->username; ?>">
								</td>
							</tr>
							
							<tr>
								<td>
									<select class="glow w100" name="timezone" tabindex="2">
										<?php foreach($tzlist as $tz) { ?>
										
										<option value="<?php echo $tz; ?>" <?php if($tz == $_SESSION[USESSION]->timezone) echo "selected"; ?>><?php echo $tz; ?></option>	
										<?php } ?>										
									</select>
								</td>
							</tr>
							
							<tr>
								<td>
									<?php if($user[0]['mfa_enabled'] == -1) { ?>
										
									<a class="raw-button red-button w49 fl" href="disable-mfa">Disable MFA</a>
									<?php }else { ?>
										
									<a class="raw-button blue-button w49 fl" href="enable-mfa">Enable MFA</a>
									<?php } ?>
									
									<a class="raw-button blue-button w49 fr" href="change-password">Reset Password</a>
								</td>
							</tr>
							
							<tr><td></td></tr>
							<tr><td></td></tr>
							
							<tr>
								<td>
									<a class="raw-button red-button w100" href="delete-account">Delete Account</a>
								</td>
							</tr>
							
							<tr><td></td></tr>
							<tr><td></td></tr>
						</tbody>
						
						<tfoot>
							<tr>
								<td>
									<input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="3" value="Save">
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
									<a href="conversations">Return</a>
								</td>
							</tr>
						</tfoot>
					</table>
				</form>
			</div>
