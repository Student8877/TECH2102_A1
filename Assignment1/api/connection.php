<?php

class Database
{
    private $host = "localhost";
    private $username = "root";
    private $pass = "";
    private $db = "students";
    public $connection;


    public function createConnection()
    {
        $this -> connection = new mysqli($this -> host, $this -> username, $this -> pass, $this -> db);
        if($this -> connection === false) {
          echo "Error connecting to database: " . $connection -> connect_error;
        }
        echo ("Connected to database...");
        return $this -> connection;
    }
}
?>
