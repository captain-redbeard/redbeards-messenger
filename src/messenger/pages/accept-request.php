<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 02-Dec-2016
 * Made Date: 27-Nov-2016
 * Author: Hosvir
 * 
 * */
include_once(dirname(__FILE__) . "/../includes/authentication.php");
sec_session_start(); 

//Set session
$_SESSION['request'] = $guid;

//Manual login check
if(!login_check($mysqli)) {
	$loggedin = false;
	header('Location: ../login');
}

//Add contacts
if(isset($guid)) {
	$request = QB::select("SELECT request_guid, user_guid FROM contact_requests WHERE request_guid = ?;", array($guid), $mysqli);
	
	if(count($request) > 0 && $request[0]['user_guid'] != $_SESSION[USESSION]->user_guid) {
		//Add contact for user
		if(QB::insert("INSERT INTO contacts (user_guid, contact_guid) VALUES (?,?);", 
						array($_SESSION[USESSION]->user_guid, $request[0]['user_guid']), 
						$mysqli) > -1) {
							
			//Add contact for requester
			QB::insert("INSERT INTO contacts (user_guid, contact_guid) VALUES (?,?);", array($request[0]['user_guid'], $_SESSION[USESSION]->user_guid), $mysqli);	
			
			//Delete request
			QB::update("DELETE FROM contact_requests WHERE request_guid = ?;", array($guid), $mysqli);
			
			//Remove session
			unset($_SESSION['request']);
			
			//Redirect
			header('Location: ../contacts');			
		}
	}else{
		if($request[0]['user_guid'] == $_SESSION[USESSION]->user_guid) {
			unset($_SESSION['request']);
			$error = "You can't add yourself???";
		}else{
			$error = "Request expired or invalid.";
		}
	}
}
?>

			<div class="cover-wrapper">				
				<form method="POST" action="">
					<table class="single-table">
						<thead>
							<tr>
								<th>Error</th>
							</tr>
						</thead>
						
						<tbody>
							<tr>
								<td>
									<?php if(isset($error)) { ?>
									<br/>
									<div class="message-error"><?php echo $error; ?></div>
									<?php } ?>
									
								</td> 
							</tr>
						</tbody>
						
						<tfoot>
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
