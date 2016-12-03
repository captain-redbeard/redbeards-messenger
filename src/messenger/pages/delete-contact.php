<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 03-Dec-2016
 * Made Date: 27-Nov-2016
 * Author: Hosvir
 * 
 * */
include(dirname(__FILE__) . "/../includes/loginauth.php");

//Delete
if (isset($guid)) {
    if (QueryBuilder::update(
        "DELETE FROM messages WHERE (user1_guid = ? OR user1_guid = ?) AND (user2_guid = ? OR user2_guid = ?);",
        array(
            $_SESSION[USESSION]->user_guid,
            $guid,
            $_SESSION[USESSION]->user_guid,
            $guid
        )
    )) {
        //Delete conversations
        QueryBuilder::update(
            "DELETE FROM conversations WHERE (user_guid = ? OR user_guid = ?) AND (contact_guid = ? OR contact_guid = ?);",
            array(
                $_SESSION[USESSION]->user_guid,
                $guid,
                $_SESSION[USESSION]->user_guid,
                $guid
            )
        );

        //Delete contact
        QueryBuilder::update(
            "DELETE FROM contacts WHERE contact_guid = ? AND user_guid = ?;",
            array(
                $guid,
                $_SESSION[USESSION]->user_guid
            )
        );

        //Delete us from contacts contacts
        QueryBuilder::update(
            "DELETE FROM contacts WHERE contact_guid = ? AND user_guid = ?;",
            array(
                $_SESSION[USESSION]->user_guid,
                $guid
            )
        );
    }
}

//Redirect
header('Location: ../contacts');
?>
