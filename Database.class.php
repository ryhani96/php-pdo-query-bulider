<?php

class Database extends PDO
{

    private $dbHost;
    private $dbName;
    private $dbUser;
    private $dbPasswords;
    private $dbConn;

    public function __construct($dbHost, $dbName, $dbUser, $dbPasswords = "")
    {
        $this->dbHost = $dbHost;
        $this->dbName = $dbName;
        $this->dbUser = $dbUser;
        $this->dbPasswords = $dbPasswords;
        parent::__construct("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPasswords);

        try {
            $this->dbConn = new PDO("mysql:host=$this->dbHost;dbname=$this->dbName", $this->dbUser, $this->dbPasswords);
            $this->dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "Connected to database successfully.";
        } catch (PDOException $e) {
            // Handle errors here, e.g., log them or throw a custom exception
            echo "Database connection failed: " . $e->getMessage();
            die();
        }
    }

    public function getConnection()
    {
        return $this->dbConn;
    }

}