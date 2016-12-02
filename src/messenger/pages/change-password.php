<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 27-Nov-2016
 * Made Date: 25-Nov-2016
 * Author: Hosvir
 * 
 * */
include(dirname(__FILE__) . "/../includes/login_auth.inc.php");

//Check for post
if(isset($_POST['password'])) { 
	$password = $_POST['password'];
	$npassword = $_POST['npassword'];
	$cpassword = $_POST['cpassword'];
	
	//Check for errors
	if($npassword != $cpassword) {
		$error = "Passwords don't match.";
	}
	if(strlen($npassword) < 9) {
		$error = "Password must be greater than 8 characters.";
	}
	
	if(!isset($error)) {
		//Get user
		$user = QB::select("SELECT user_id, user_guid, password FROM users WHERE user_id = ? AND user_guid = ?;", array($_SESSION[USESSION]->user_id, $_SESSION[USESSION]->user_guid), $mysqli);
		
		if(count($user) > 0) {
			//Check password
			if(password_verify($password, $user[0]['password'])) {
				//Generate password hash
				$newpass = password_hash($npassword, PASSWORD_DEFAULT, array('cost' => PW_COST));
				
				//Update user
				if(QB::update("UPDATE users SET password = ? WHERE user_id = ? AND user_guid = ?;",
								array($newpass, $_SESSION[USESSION]->user_id, $_SESSION[USESSION]->user_guid), 
								$mysqli)){
					
					//Redirect
					header('Location: settings');				
				}else{
					$error = "Failed to update user, contact support.";
				}
			}else{
				$error = "Incorrect password.";
			}
		}else{
			$error = "Failed to find user, contact support.";
		}
	}
}
?>

			<div class="cover-wrapper">				
				<form method="POST" action="">
					<table class="single-table">
						<thead>
							<tr>
								<th>Change Password</th>
							</tr>
						</thead>
						
						<tbody>
							<tr>
								<td>
									<input class="glow w100" type="password" name="password" title="Password" tabindex="1" placeholder="Password" autofocus>
								</td>
							</tr>
							
							<tr>
								<td>
									<input class="glow w100" type="password" name="npassword" title="New password" tabindex="2" placeholder="New password">
								</td>
							</tr>
							
							<tr>
								<td>
									<input class="glow w100" type="password" name="cpassword" title="Confirm password" tabindex="3" placeholder="Confirm password">
								</td>
							</tr>
						</tbody>
						
						<tfoot>
							<tr>
								<td>
									<input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="4" value="Save">
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
