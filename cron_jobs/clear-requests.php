<?php
/**
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 09-Dec-2016
 * Made Date: 29-Nov-2016
 * Author: Hosvir
 *
 */
use Messenger\Core\Database;

require_once '../app/config.php';
require_once '../vendor/autoload.php';

date_default_timezone_set(TIMEZONE);

//Delete requests
Database::update(
    "DELETE FROM contact_requests WHERE made_date < (NOW() - INTERVAL expire HOUR);",
    []
);
