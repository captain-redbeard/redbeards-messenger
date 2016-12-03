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
include(dirname(__FILE__) . "/../includes/loginauth.php");

//Delete
if (isset($guid)) {
    QueryBuilder::update(
        "DELETE FROM contact_requests WHERE request_guid = ? AND user_guid = ?;",
        array(
            $guid,
            $_SESSION[USESSION]->user_guid
        )
    );
}

//Redirect
header('Location: ../existing-requests');
?>
