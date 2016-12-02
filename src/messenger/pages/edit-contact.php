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
if(isset($_POST['submit'])) { 
	$alias = clean_input($_POST['alias']);
	
	//Check for errors
	if(strlen($alias) > 63) {
		$error = "Alias must be less than 64 characters.";
	}
	
	//Continue
	if(!isset($error)) {
		if(QB::update("UPDATE contacts SET contact_alias = ? WHERE contact_guid = ? AND user_guid = ?;", 
						array($alias, $guid, $_SESSION[USESSION]->user_guid), 
						$mysqli)) {
			
			//Redirect
			header('Location: ../conversations');
		}else{
			$error = "Failed to save contact, contact support.";
		}
	}
}

//Get contact
$contact = QB::select("SELECT contact_guid, contact_alias, made_date, (SELECT username FROM users WHERE user_guid = contact_guid) AS username FROM contacts WHERE user_guid = ? AND contact_guid = ?;", 
						array($_SESSION[USESSION]->user_guid, $guid), 
						$mysqli);
?>

			<div class="cover-wrapper">				
				<form method="POST" action="">
					<table class="single-table">
						<thead>
							<tr>
								<th>Edit Contact</th>
							</tr>
						</thead>
						
						<tbody>
							<tr>
								<td>
									<input class="glow w100" type="text" name="alias" title="Alias" tabindex="1" placeholder="Alias" value="<?php echo $contact[0]['contact_alias']; ?>">
								</td>
							</tr>
							
							<tr>
								<td>
									<input class="glow disabled w100" type="text" name="username" title="Username" tabindex="2" placeholder="Username" value="<?php echo $contact[0]['username']; ?>" disabled>
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
