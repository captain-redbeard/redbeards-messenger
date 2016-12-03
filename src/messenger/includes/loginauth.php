<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 03-Dec-2016
 * Made Date: 25-Nov-2016
 * Author: Hosvir
 * 
 * */
include_once(dirname(__FILE__) . "/../includes/authentication.php");
secureSessionStart(); 

if (!loginCheck()) {
    $loggedin = false;
    header('Location: ' . str_replace("index.php", "", getURL()) . 'login');
}
?>
