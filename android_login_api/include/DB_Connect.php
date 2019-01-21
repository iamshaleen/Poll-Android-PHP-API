<?php
class DB_Connect {
    private $conn;

    // Connecting to database
    public function connect() {
        require_once 'include/Config.php';
        
 

// Connecting to sql database
		$connInfo = array("Database"=>"pcdpAndroid","UID"=>"pcdpAndroid","PWD"=>"7Tn72Y6y","ReturnDatesAsStrings" => true);
        $serverName="HCPLH1216LP1538";
        //echo sqlsrv_connect($serverName, $connInfo);
        $this->conn = sqlsrv_connect($serverName, $connInfo);
        if($this->conn){

        }
        else{
            die( print_r( sqlsrv_errors(), true));
        }
        // return database handler
        return $this->conn;
    }
}

?>	