<?php

session_start();
require dirname(__DIR__) . '/vendor/autoload.php';

error_reporting(E_ALL);
set_exception_handler('Core\Error::exceptionHandler');

define('CONFIG_DIR', dirname(__DIR__) . '/Core/Config/');

$GLOBALS['config'] = array();

$app_config_json = file_get_contents(CONFIG_DIR . 'app.json');
$GLOBALS['config'] = json_decode($app_config_json, true);

$app = $GLOBALS['config']['front']['namespace'] . $GLOBALS['config']['front']['class'];
define('APP', $app);
unset($app);
