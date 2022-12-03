<?php 
    function connectDB() {
        require_once("../config/db.inc.php");
        $conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
        if(mysqli_connect_errno()) { // cannot connect database
            echo ("Failed to connect to MySQL: ".mysqli_connect_error()); // for debugging
            die();
        } else {
            // echo ("success to connect to database");
        }
        return $conn;
    }
    
?>