<?php

require_once ('config.php');

class Database {

    private $host = DB_SERVER;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    private $dbname = DB_NAME;
    private $db = null;

    // connect to database
    public function __construct() {
        
    }

    // get the database connection
    public function getDb() {
        $this->db = new mysqli($this->host, $this->username, $this->password, $this->dbname);

        // check connection
        if($this->db->connect_error){
            die("ERROR: Could not connect to database. " . $this->db->connect_error);
        }

        return $this->db;
    }

    public function destroy(){
        $this->db->close();
    }
}
?>
