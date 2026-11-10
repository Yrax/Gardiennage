<?php
class Database {
  private static ?PDO $instance = null;

  public static function get(): PDO {
    if (self::$instance === null) {
      self::$instance = new PDO(
        "mysql:host=localhost;dbname=" . (getenv('DB_NAME') ?: 'petsitter') . ";charset=utf8mb4",
        "root", ""
      );
      self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    return self::$instance;
  }

  /** Exécute une requête préparée et renvoie le statement. */
  public static function run(string $sql, array $params = []): PDOStatement {
    $stmt = self::get()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
  }
}
