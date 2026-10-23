<?php
class Database {
  private static ?PDO $instance = null;

  public static function get(): PDO {
    if (self::$instance === null) {
      self::$instance = new PDO(
        "mysql:host=localhost;dbname=petsitter",
        "root", ""
      );
      self::$instance->setAttribute(
        PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION
      );
    }
    return self::$instance;
  }
}
