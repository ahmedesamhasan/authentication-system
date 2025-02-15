<?php

namespace User\AuthenticationSystem\Config; // ✅ يجب أن يتطابق مع المسار والمجلدات

use PDO;
use PDOException;

class Config
{
  private static $host = "localhost";
  private static $user = "root";
  private static $password = "";
  private static $db = "auth";
  private static $conn = null;

  public static function getConnection()
  {
    if (self::$conn === null) {
      try {
        $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db . ";charset=utf8mb4";
        $options = [
          PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        self::$conn = new PDO($dsn, self::$user, self::$password, $options);
      } catch (PDOException $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        die("Connection error. Please try again later.");
      }
    }
    return self::$conn;
  }
}
