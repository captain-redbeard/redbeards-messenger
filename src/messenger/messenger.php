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
include(dirname(__FILE__) . "/includes/config.php");
include(dirname(__FILE__) . "/includes/core.php");
include(dirname(__FILE__) . "/includes/querybuilder.php");

date_default_timezone_set(TIMEZONE);

//Required for classes
function __autoload($class_name)
{
    require(dirname(__FILE__) . "/includes/user.php");
}

//Variables
$page = null;
$menu = null;
$contact = null;
$loggedin = true;

//Get variables
if (isset($_GET['page'])) $page = cleanInput($_GET['page']);
if (isset($_GET['menu'])) $menu = cleanInput($_GET['menu']);
if (isset($_GET['guid'])) $guid = cleanInput($_GET['guid']);
if (isset($_GET['cguid'])) $cguid = cleanInput($_GET['cguid']);

//Check for default page
if (!isset($page)) {
    $page = "login";
}

//Include page
if (file_exists(dirname(__FILE__) .  "/pages/" . $page . ".php")) {
    include(dirname(__FILE__) . "/pages/" . $page . ".php"); 
} else {
    include(dirname(__FILE__) . "/pages/404.php"); 
}
