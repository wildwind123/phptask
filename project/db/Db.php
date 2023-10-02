<?php
namespace project\db;

class Db {

    private $db;

    public function __construct()
    {
        $hostname = "db"; // or your database host name
        $username = "root";
        $password = "root";
        $database_name = "task";
        $pdo = new \PDO("mysql:host=$hostname;dbname=$database_name", $username, $password);
    
        // Set PDO to throw exceptions on errors
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->db = $pdo;
        
        return $this;
    }

    public function GetDb() 
    {
        return $this->db;
    }

}