<?php

/**
 * Handling database connection
 *
 * 
 */
class DbConnect {

    private $connection;
    function __construct() {
        
    }

    /**
     * Establishing database connection
     * @return database connection handler
     */
    function connect() {
    	require_once 'DbConfig.php';
        // Connecting to mysql database
        $this->connection = new mysqli(DbConfig::HOSTNAME, DbConfig::USERNAME,DbConfig::PASSWORD, DbConfig::DBNAME);
        // Check for database connection error
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }
        // returing connection resource
        return $this->connection;
    }

}

?>