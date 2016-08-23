<?php
        class DBUtil {
            // Store the single instance of Database
            private static $singletonInstance;

            private $dbHost = 'localhost';
            private $dbUser = 'root';
            private $dbPass = 'jagan';
            private $dbName = 'carpool_db';

            // Private constructor to limit object instantiation to within the class
            private function __construct() {
                mysql_connect($this->dbHost,$this->dbUser,$this->dbPass) or die("Mysql Connection Failed: " . mysql_error());
                mysql_select_db($this->dbName) or die("Database 'carpool_db' Selection failed: " . mysql_error());
            }

            // Getter method for creating/returning the single instance of this class
            public static function getInstance() {
                if (!self::$singletonInstance)
                {
                    self::$singletonInstance = new DBUtil();
                }
                return self::$singletonInstance;
            }

            public static function executeQuery($query) {
               self::getInstance();
               return mysql_query($query);
            }
         }
?>