<?php
/**
 *
 * Details:
 * This class is to provide static methods to easily use MySQLI 
 * prepared statements.
 * 
 * Modified: 03-Dec-2016
 * Made Date: 03-Dec-2016
 * Author: Hosvir
 * 
 * */
class Database
{

    /**
     *
     * Connect to database with the variables set in config or environment variables.
     *
     * TODO: Expand to support different databases.
     * 
     * */
    public static function connect() {
        $mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        if ($mysqli->connect_error) {
            echo "<h1>Error connecting to database.</h1>";
            exit();
        }

        return $mysqli;
    }
}
?>
