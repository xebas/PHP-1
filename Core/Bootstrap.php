<?php
/*
* ==========================================================
*     AUTOLOAD COMPOSER
* ==========================================================
*/
require dirname(__DIR__) . '/vendor/autoload.php';
/*
* ==========================================================
*     CACHE BLOCK PAGES
* ==========================================================
*/
header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Pragma-directive: no-cache");
header("Cache-directive: no-cache");
header("Cache-control: no-cache");
header("Pragma: no-cache");
header("Expires: 0");
/*
* ==========================================================
*     SESSIONS & COOKIES
* ==========================================================
*/
// server should keep session data for AT LEAST 8 hour
ini_set('session.gc_maxlifetime', 60 * 60 * 8);
// each client should remember their session id for EXACTLY 8 hour
session_set_cookie_params(60 * 60 * 8);
ini_set('session.use_strict_mode', 1);
session_start();
/*
* ==========================================================
*     ERRORS
* ==========================================================
*/
error_reporting(E_ALL);
set_exception_handler('Core\Error::exceptionHandler');
/*
* ==========================================================
*     APP.JSON CONFIGURATIONS
* ==========================================================
*/
define('CONFIG_DIR', dirname(__DIR__) . '/Core/Config/');
$GLOBALS['config'] = array();
$app_config_json = file_get_contents(CONFIG_DIR . 'app.json');
$GLOBALS['config'] = json_decode($app_config_json, true);
$app = $GLOBALS['config']['front']['namespace'] . $GLOBALS['config']['front']['class'];
define('APP', $app);
unset($app);
