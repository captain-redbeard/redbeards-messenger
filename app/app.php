<?php
/**
 * @author captain-redbeard
 * @since 20/01/17
 */
use Redbeard\Core\Router;
use Redbeard\Core\Functions;

//Require files
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

//Set timezone
date_default_timezone_set(TIMEZONE);

//Set base url
define("BASE_HREF", Functions::getUrl());

//Start router
$router = new Router();
$router->route($_GET, $_POST);
