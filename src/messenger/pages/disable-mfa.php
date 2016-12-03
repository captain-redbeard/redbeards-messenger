<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 03-Dec-2016
 * Made Date: 28-Nov-2016
 * Author: Hosvir
 * 
 * */
include(dirname(__FILE__) . "/../includes/loginauth.php");

//Update
QueryBuilder::update(
    "UPDATE users SET mfa_enabled = 0 WHERE user_guid = ?;",
    array($_SESSION[USESSION]->user_guid)
);

//Redirect
header('Location: settings');
?>
