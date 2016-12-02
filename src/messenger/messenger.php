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
include(dirname(__FILE__) . "/includes/config.inc.php");
include(dirname(__FILE__) . "/includes/core.inc.php");
include(dirname(__FILE__) . "/includes/db.inc.php");
include(dirname(__FILE__) . "/includes/querybuilder.inc.php");

date_default_timezone_set(TIMEZONE);

//Required for classes
function __autoload($class_name) {
	include_once(dirname(__FILE__) . "/includes/user.php");
}

//Variables
$page = null;
$menu = null;
$contact = null;
$loggedin = true;

//Get variables
if(isset($_GET['page'])) $page = clean_input($_GET['page']);
if(isset($_GET['menu'])) $menu = clean_input($_GET['menu']);
if(isset($_GET['guid'])) $guid = clean_input($_GET['guid']);
if(isset($_GET['cguid'])) $cguid = clean_input($_GET['cguid']);

//Check for default page
if(!isset($page)) {
	$page = "login";
}

//Include page
if(file_exists(dirname(__FILE__) .  "/pages/" . $page . ".php")){
	include(dirname(__FILE__) . "/pages/" . $page . ".php"); 
}else{
	include(dirname(__FILE__) . "/pages/404.php"); 
}
?>
			
