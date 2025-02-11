<?php

require_once ('config.php');

class Database {

    private $host = "DB_SERVER"; // replace only the name, don't remove the "
    private $username = "DB_USERNAME"; // replace only the name, don't remove the "
    private $password = "DB_PASSWORD"; // replace only the name, don't remove the "
    private $dbname = "DB_NAME"; // replace only the name, don't remove the "
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
