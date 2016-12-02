<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 25-Nov-2016
 * Made Date: 25-Nov-2016
 * Author: Hosvir
 * 
 * */
include_once(dirname(__FILE__) . "/../includes/authentication.php");
sec_session_start(); 

if(!login_check($mysqli)) {
	$loggedin = false;
	header('Location: ' . str_replace("index.php", "", getURL()) . 'login');
}
?>
