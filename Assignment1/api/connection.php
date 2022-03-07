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
        $this -> connection = null;
        try {
            $this -> connection = new PDO("mysql:host=" . $this -> host .";dbname=" . $this -> db, $this -> username, $this -> pass);
            $this ->connection -> exec("set names utf8");
        } catch (PDOException $err) {
            echo "Error connecting to database: " . $err -> getMessage();
        }
        return $this -> connection;
    }
}
?>
