<?php
/**
 * @author captain-redbeard
 * @since 29/11/16
 */
use Redbeard\Core\Database;

require_once '../app/config.php';
require_once '../vendor/autoload.php';

date_default_timezone_set(TIMEZONE);

//Delete requests
Database::update(
    "DELETE FROM contact_requests WHERE made_date < (NOW() - INTERVAL expire HOUR);",
    []
);
