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

//Update
QB::update("UPDATE users SET mfa_enabled = 0 WHERE user_guid = ?;", array($_SESSION[USESSION]->user_guid), $mysqli);

//Redirect
header('Location: settings');
?>
