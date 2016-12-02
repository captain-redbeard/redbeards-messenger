<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 02-Dec-2016
 * Made Date: 29-Nov-2016
 * Author: Hosvir
 * 
 * */
include(dirname(__FILE__) . "/../includes/config.inc.php");
include(dirname(__FILE__) . "/../includes/db.inc.php");
include(dirname(__FILE__) . "/../includes/querybuilder.inc.php");

//Delete requests
$requests = QB::update("DELETE FROM contact_requests WHERE made_date < (NOW() - INTERVAL expire HOUR);", array(), $mysqli);
?>
