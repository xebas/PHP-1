<?php

namespace Core;

use PDO;

class Model
{
  private static $instanceDB = null;

  public function __construct()
  {

    $DB_HOST = $GLOBALS['config']['db']['host'];
    $DB_PORT = $GLOBALS['config']['db']['port'];
    $DB_NAME = $GLOBALS['config']['db']['dbname'];
    $DB_USER = $GLOBALS['config']['db']['user'];
    $DB_PASS = $GLOBALS['config']['db']['pass'];

    self::$instanceDB = new PDO('mysql:host=' . $DB_HOST . ';port=' . $DB_PORT . ';dbname=' . $DB_NAME . ";charset=UTF8", $DB_USER, $DB_PASS);
  }

  public static function getInstanceDB()
  {

    if (!self::$instanceDB) {
      new self();
    }
    return self::$instanceDB;
  }

  public static function closeDB()
  {
    self::$instanceDB = null;
  }
}
