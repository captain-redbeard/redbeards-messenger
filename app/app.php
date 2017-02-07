<?php
/**
 * @author captain-redbeard
 * @since 20/01/17
 */
use Redbeard\Core\Config;
use Redbeard\Core\Router;
use Redbeard\Core\Functions;
use Redbeard\Core\Database;

//Require autoloader
require_once __DIR__ . '/../vendor/autoload.php';

//Load config
Config::init();

//Set base url
Config::set('app.base_href', Functions::getUrl());

//Set database config
Database::init(Config::get('database'));

//Set timezone
date_default_timezone_set(Config::get('app.timezone'));

//Start router
$router = new Router();
$router->route($_GET, $_POST);
