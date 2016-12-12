<?php
/**
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 10-Dec-2016
 * Made Date: 04-Nov-2016
 * Author: Hosvir
 *
 */
use Messenger\Core\Router;
use Messenger\Core\Functions;

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

define("BASE_HREF", Functions::getUrl());
define("BASE_DIR", __DIR__);

date_default_timezone_set(TIMEZONE);

$router = new Router();
$router->route($_GET, $_POST);
