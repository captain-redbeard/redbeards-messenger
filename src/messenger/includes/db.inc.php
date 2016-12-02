<?php
//Create database connection and connect
$mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if($mysqli->connect_error) {
	echo "<h1>Error connecting to database.</h1>";
	exit();
}
?>
