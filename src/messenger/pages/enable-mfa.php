<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 28-Nov-2016
 * Made Date: 28-Nov-2016
 * Author: Hosvir
 * 
 * */
include(dirname(__FILE__) . "/../includes/login_auth.inc.php");
include(dirname(__FILE__) . "/../thirdparty/qrcode.php");

//Get user details
$user = QB::select("SELECT secret_key, mfa_enabled FROM users WHERE user_guid = ?;", array($_SESSION[USESSION]->user_guid), $mysqli);

//Check for post
if(isset($_POST['code1'])) { 
	$code1 = clean_input($_POST['code1']);
	$code2 = clean_input($_POST['code2']);
	
	if(!isset($code1) || !isset($code2)) {
		$error = "You must provide two consecutive codes.";
	}
	
	if(!isset($error)) {
		include(dirname(__FILE__) ."/../thirdparty/googleauth.php");
		$result1 = Google2FA::verify_key($user[0]['secret_key'], $code1);	
		$result2 = Google2FA::verify_key($user[0]['secret_key'], $code2);	
		
		if($result1 && $result2) {
			if(QB::update("UPDATE users SET mfa_enabled = -1 WHERE user_guid = ?;", array($_SESSION[USESSION]->user_guid), $mysqli)) {
				//Redirect
				header('Location: conversations');
			}
		}else{
			$error = "Invalid codes.";
		}
	}
	
}

//Generate QR code
$qr = new QRCode();
$qr->setErrorCorrectLevel(QR_ERROR_CORRECT_LEVEL_H);
$qr->setTypeNumber(10);
$qr->addData("otpauth://totp/PHP-Messenger:" . $_SESSION[USESSION]->username . "?secret=" . $user[0]['secret_key'] . "&issuer=PHP-Messenger");
$qr->make();
?>

			<div class="cover-wrapper">				
				<form method="POST" action="">
					<table class="single-table">
						<thead>
							<tr>
								<th>Enable MFA</th>
							</tr>
						</thead>
						
						<tbody>
							<tr>
								<td>
									<p>If your virtual MFA application supports scanning QR codes, scan the following image.</p>
									<div class="qr-wrapper">
										<?php $qr->printHTML(); ?>
									</div>
								</td>
							</tr>
							
							<tr>
								<td>
									<div class="message-info"><?php echo $user[0]['secret_key']; ?></div>
										
									</p>
									<p>After the application is configured, enter two consecutive authentication codes in the boxes below and click Activate.</p>
								</td>
							</tr>
							
							<tr>
								<td>
									<input class="glow w100" type="text" name="code1" title="Code 1" tabindex="1" placeholder="Code 1">
								</td>
							</tr>
							
							<tr>
								<td>
									<input class="glow w100" type="text" name="code2" title="Code 2" tabindex="2" placeholder="Code 2">
								</td>
							</tr>
						</tbody>
						
						<tfoot>
							<tr>
								<td>
									<input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="4" value="Activate">
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
