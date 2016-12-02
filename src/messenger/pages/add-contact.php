<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 02-Dec-2016
 * Made Date: 26-Nov-2016
 * Author: Hosvir
 * 
 * */
include(dirname(__FILE__) . "/../includes/login_auth.inc.php");

//Check for post
if(isset($_POST['expire'])) { 
	$requestname = clean_input($_POST['requestname']);
	$expire = clean_input($_POST['expire']);
	if(!is_numeric($expire)) $error = "Expire must be a number.";
	
	if(!isset($error)) {
		if($expire < 1) $expire = 1;
		if($expire > 48) $expire = 48;
		$guid = generateRandomString(6);
		
		if(QB::insert("INSERT INTO contact_requests (request_guid, request_name, user_guid, expire) VALUES (?,?,?,?);", 
						array($guid, $requestname, $_SESSION[USESSION]->user_guid, $expire), 
						$mysqli) > -1) {
			$url = str_replace("index.php", "accept-request", getURL()) . "/" . $guid;
		}else{
			$error = "Failed to add contact request, contact support.";
		}
	}
}

//Get expire list
$expiretimes = array(1, 6, 12, 24, 48);
?>

			<div class="cover-wrapper">				
				<form method="POST" action="">
					<table class="single-table">
						<thead>
							<tr>
								<th>Add Contact</th>
							</tr>
						</thead>
						
						<tbody>
							<tr>
								<td>
									<p>
										Contacts are added by sharing a <strong>unique URL</strong> with the person who you want to add. Once they go to the URL and <strong>login</strong> they will be added 
										to your contact list and you to theirs.
									</p>
									
									<p>
										These URLs are unique and will expire in the set time limit or when the request is used.
									</p>
									
									<p>
										Create a new request for each contact.
									</p>
								</td>
							</tr>
							
							<tr>
								<td class="center">
									<a href="existing-requests">View existing requests</a>
								</td>
							</tr>
							
							<tr>
								<td>
									<input class="glow w100" type="text" name="requestname" title="Request name" tabindex="1" placeholder="Request name (optional)" autofocus>
								</td>
							</tr>
														
							<tr>
								<td>
									<select class="glow w100" name="expire" tabindex="2">
										<option value="-1" selected disabled>Select Expire Time (hours)</option>
										
										<?php foreach($expiretimes as $tz) { ?>
										
										<option value="<?php echo $tz; ?>"><?php echo $tz; ?></option>	
										<?php } ?>										
									</select>
								</td>
							</tr>
						</tbody>
						
						<tfoot>
							<tr>
								<td>
									<input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="3" value="Get URL">
									<?php if(isset($url)) { ?>
									<br/>
									<p class="small-text center"><strong>Share the below URL with the desired person.</strong></p>
									<div class="message-info"><?php echo $url; ?></div>
									<?php } ?>
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
