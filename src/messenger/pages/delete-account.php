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
if(isset($_POST['password'])) { 
	$password = $_POST['password'];
	
	//Get user
	$user = QB::select("SELECT user_id, user_guid, password, mfa_enabled FROM users WHERE user_id = ? AND user_guid = ?;", array($_SESSION[USESSION]->user_id, $_SESSION[USESSION]->user_guid), $mysqli);
	
	if(count($user) > 0) {
		//Check password
		if(password_verify($password, $user[0]['password'])) {
			//Delete keys
			if(!STORE_KEYS_LOCAL) {
				unlink(dirname(__FILE__) . "/../keys/public/" . $user[0]['user_guid'] . ".pem");
				unlink(dirname(__FILE__) . "/../keys/private/" . $user[0]['user_guid'] . ".key");
			}else{
				include(dirname(__FILE__) . "/../thirdparty/S3.php");
				S3::setAuth(S3_ACCESS_KEY, S3_SECRET_KEY);
				S3::deleteObject(KEY_BUCKET, $guid . ".pem");
				S3::deleteObject(KEY_BUCKET, $guid . ".key");
			}
			
			//Delete messages
			if(!QB::update("DELETE FROM messages WHERE (user1_guid = ? OR user2_guid = ?);", array($user[0]['user_guid'], $user[0]['user_guid']), $mysqli)) {
				$error = "Failed to delete messages, contact support.";
			}
			
			//Delete conversations
			if(!QB::update("DELETE FROM conversations WHERE (contact_guid = ? OR user_guid = ?);", array($user[0]['user_guid'], $user[0]['user_guid']), $mysqli)) {
				$error = "Failed to delete conversations, contact support.";
			}
			
			//Delete contacts
			if(!QB::update("DELETE FROM contacts WHERE (contact_guid = ? OR user_guid = ?);", array($user[0]['user_guid'], $user[0]['user_guid']), $mysqli)) {
				$error = "Failed to delete contacts, contact support.";
			}
			
			//Delete account
			if(!QB::update("DELETE FROM users WHERE user_guid = ?;", array($user[0]['user_guid']), $mysqli)) {
				$error = "Failed to delete account, contact support.";
			}
			
			//Redirect
			header('Location: logout');
		}else{
			$error = "Incorrect password.";
		}
	}else{
		$error = "Failed to find user, contact support.";
	}
}
?>

			<div class="cover-wrapper">				
				<form method="POST" action="">
					<table class="single-table">
						<thead>
							<tr>
								<th>Delete Account</th>
							</tr>
						</thead>
						
						<tbody>
							<tr>
								<td>
									<p>
										Deleting your account is permanent, there is <strong>no recovery</strong>. If you are sure you wish to delete your account, enter your password then press delete.
									</p>
								</td>
							</tr>
							<tr>
								<td>
									<input class="glow w100" type="password" name="password" title="Password" tabindex="1" placeholder="Password">
								</td>
							</tr>
							
							<tr>
								<td>
									<a class="raw-button blue-button w49 fl" href="conversations" tabindex="2">Return</a>
									<input class="raw-button red-button w49 fr" type="submit" name="submit" title="Submit" tabindex="3" value="Delete">
								</td>
							</tr>
						</tbody>
						
						<tfoot>
							<tr>
								<td>
									<?php if(isset($error)) { ?>
									<br/>
									<div class="message-error"><?php echo $error; ?></div>
									<?php } ?>
									
								</td> 
							</tr>
						</tfoot>
					</table>
				</form>
			</div>
