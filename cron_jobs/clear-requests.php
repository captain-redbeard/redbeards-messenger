<?php
/**
 * @author captain-redbeard
 * @since 29/11/16
 */
use Redbeard\Crew\Config;
use Redbeard\Crew\Database;

require_once '../vendor/autoload.php';

//Load config
Config::init();

//Set database config
Database::init(Config::get('database'));

//Set timezone
date_default_timezone_set(Config::get('app.timezone'));

//Delete requests
Database::update(
    "DELETE FROM contact_requests WHERE made_date < (NOW() - INTERVAL expire HOUR);",
    []
);
