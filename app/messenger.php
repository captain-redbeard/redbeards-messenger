<?php
/*
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 05-Dec-2016
 * Made Date: 04-Nov-2016
 * Author: Hosvir
 *
 */
use Messenger\Core\Router;

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set(TIMEZONE);

//Create new router
$router = new Router();
