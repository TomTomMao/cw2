<?php
    $debugOn=false;
    require_once("../config/debug.php");
class Vehicle {
        function __construct($licence, $colour, $make, $model, $ID) {
            $this->ID = $ID; 
            $this->licence = $licence;
            $this->colour = $colour;
            $this->make = $make;
            $this->model = $model;
        }
        function setID($ID) {
            $this->ID = $ID;
        }
        function getID() {
            return $this->ID;
        }
        function getLicence() {
            return $this->licence;
        }
        function getColour() {
            return $this->colour;
        }
        function getMake() {
            return $this->make;
        }
        function getModel() {
            return $this->model;
        }
        function renderHtmlTable() {
            $ID = ($this->ID ? $this->ID : "NULL");
            echo "
                <table>
                    <tr>
                        <td>ID</td>
                        <td>".$ID."</td>
                    </tr>
                    <tr>
                        <td>licence</td>
                        <td>".$this->licence."</td>
                    </tr>
                    <tr>
                        <td>colour</td>
                        <td>".$this->colour."</td>
                    </tr>
                    <tr>
                        <td>make</td>
                        <td>".$this->make."</td>
                    </tr>
                    <tr>
                        <td>model</td>
                        <td>".$this->model."</td>
                    </tr>
                    
                </table>
                ";
        }
    }
class VehiclesDB {
    function __construct($username) {
        $this->username=$username;
    }
    function isVehicleExists($vehicleLicence) {
        // given vehicleLicence, check if it is in the database.
        // return true if exists
        // return false if doesn't exist
        $sql = "SELECT Vehicle_ID FROM Vehicles WHERE Vehicle_licence ='".$vehicleLicence."';";
        // echo $sql;
        require("../config/db.inc.php");
        $conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
        if(mysqli_connect_errno()) { // cannot connect database
            debugEcho ("Failed to connect to MySQL: ".mysqli_connect_error()); // for debugging
            die();
        } else { // success to connect database
            debugEcho("MySQL connection OK<br>");
            $results = mysqli_query($conn, $sql);
            mysqli_close($conn); // disconnect
            if (mysqli_num_rows($results) == 0) {
                return false;
            } else {
                return true;
            }
        }
            
    }
    function insertNewVehicle($vehicle) {
        // given an vehicle object, insert it into the database, assume the vehicle's data are valid
        // if the vehicle licence is already in the database, return the false
        // if the vehicle licence is new in the database, return true
        
        if ($this->isVehicleExists($vehicle->getLicence())) {
            return false;
        }
        
        $sql = "INSERT INTO Vehicles (Vehicle_make, Vehicle_model, Vehicle_colour, Vehicle_licence) VALUES
        ('".$vehicle->getMake()."', '"
        .$vehicle->getModel()."', '"
        .$vehicle->getColour()."', '"
        .$vehicle->getLicence()."')";
        // echo $sql;
        require("../config/db.inc.php");
        $conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
        if(mysqli_connect_errno()) { // cannot connect database
            debugEcho ("Failed to connect to MySQL: ".mysqli_connect_error()); // for debugging
            die();
        } else { // success to connect database
            debugEcho("MySQL connection OK<br>");
            $results = mysqli_query($conn, $sql);
            mysqli_close($conn); // disconnect
            return true;
        }
    }
    function getVehiclesByLicence ($vehicleLicence) {
        // given an vehicle licence number, return an array of vehicle(s) object
        $sql = "SELECT Vehicle_ID, Vehicle_licence, Vehicle_colour, Vehicle_make, Vehicle_model FROM Vehicles WHERE Vehicle_licence ='".$vehicleLicence."';";
        $vehicles = array();

        require("../config/db.inc.php");
        $conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
        if(mysqli_connect_errno()) { // cannot connect database
            debugEcho ("Failed to connect to MySQL: ".mysqli_connect_error()); // for debugging
            die();
        } else { // success to connect database
            debugEcho("MySQL connection OK<br>");
            $results = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($results)) {
                array_push($vehicles,new Vehicle($row["Vehicle_licence"], $row["Vehicle_colour"], $row["Vehicle_make"], $row["Vehicle_model"], $row["Vehicle_ID"]));
            } 
            mysqli_close($conn); // disconnect
        }
        return $vehicles;
    }
    function getVehiclesIDByLicence ($vehicleLicence) {
        // given an vehicle licence number, search the database;
        // return a list of Vehicle_ID if the vehicle Exists;
        // return false if the vehicle doesn't exist.
        $sql = "SELECT Vehicle_ID FROM Vehicles WHERE Vehicle_licence ='".$vehicleLicence."';";
        $vehiclesID = array();
        require("../config/db.inc.php");
        $conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
        if(mysqli_connect_errno()) { // cannot connect database
            debugEcho ("Failed to connect to MySQL: ".mysqli_connect_error()); // for debugging
            die();
        } else { // success to connect database
            debugEcho("MySQL connection OK<br>");
            $results = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($results)) {
                array_push($vehiclesID, $row["Vehicle_ID"]);
            }
            mysqli_close($conn); // disconnect
        }
        return $vehiclesID;
    }

    function updateVehicle($vehicle){

    }
}
?>