<?php
// Database configuration constants
define('DB_HOST', 'db');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root_password');
define('DB_NAME', 'airgo');

class Database {
    private static $conn = null;
    
    // Private constructor to prevent instantiation
    private function __construct() {}
    
    public static function getConnection() {
        if (self::$conn === null) {
            self::$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
            
            // Check connection
            if (self::$conn->connect_error) {
                die("Connection failed: " . self::$conn->connect_error);
            }
            
            // Set charset to utf8mb4
            self::$conn->set_charset("utf8mb4");
        }
        
        return self::$conn;
    }
} 