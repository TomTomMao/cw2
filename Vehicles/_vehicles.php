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
        function toJSON() {
            return '{"ID":"'.$this->ID.
                '","licence":"'.$this->getLicence().
                '","colour":"'.$this->getColour().
                '","make":"'.$this->getMake().
                '","model":"'.$this->getModel().'"}';
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
            return "
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
    function __construct($user, $conn) {
        $this->user = $user;
        $this->conn = $conn;
    }
    function isVehicleExists($vehicleLicence) {
        // given vehicleLicence, check if it is in the database.
        // return true if exists
        // return false if doesn't exist
        $sql = "SELECT Vehicle_ID FROM Vehicles WHERE Vehicle_licence ='".$vehicleLicence."';";
        // echo $sql;
        $conn = $this->conn;
        $results = mysqli_query($conn, $sql);
        if (mysqli_num_rows($results) == 0) {
            // echo "results length is 0 (VehiclesDB->isVehicleExists) (false)"; // debugging
            return false;
        } else {
            // echo "results length is not 0 (VehiclesDB->isVehicleExists) (true)"; // debugging
            return true;
        }
            
    }
    function insertNewVehicle($vehicle) {
        // hasn't been well tested!
        // given an vehicle object, insert it into the database, assume the vehicle's data are valid
        // if the vehicle licence is already in the database, return the false
        // if the vehicle licence is new in the database, return the vehicle id
        
        if ($this->isVehicleExists($vehicle->getLicence())) {
            return false;
        }
        
        $sql = "INSERT INTO Vehicles (Vehicle_make, Vehicle_model, Vehicle_colour, Vehicle_licence) VALUES
        ('".$vehicle->getMake()."', '"
        .$vehicle->getModel()."', '"
        .$vehicle->getColour()."', '"
        .$vehicle->getLicence()."')";
        // echo $sql;
        $conn = $this->conn;
        $results = mysqli_query($conn, $sql);
        $newVehicleID = mysqli_insert_id($conn);
        if ($newVehicleID) {
            return $newVehicleID;
        } else {
            throw new Exception("there must be an error here (VehicleDB->insertNewVehicle())");
        }
    }
    function getVehiclesByLicence ($vehicleLicence) {
        // given an vehicle licence number, return an array of vehicle(s) object
        $sql = "SELECT Vehicle_ID, Vehicle_licence, Vehicle_colour, Vehicle_make, Vehicle_model FROM Vehicles WHERE Vehicle_licence ='"
        .$vehicleLicence."';";
        $vehicles = array();

        $conn = $this->conn; 
        $results = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($results)) {
            array_push($vehicles,new Vehicle($row["Vehicle_licence"], $row["Vehicle_colour"], $row["Vehicle_make"], $row["Vehicle_model"], $row["Vehicle_ID"]));
        } 
        return $vehicles;
    }
    function getVehiclesIDByLicence ($vehicleLicence) {
        // given an vehicle licence number, search the database;
        // return a list of Vehicle_ID if the vehicle Exists;
        // return false if the vehicle doesn't exist.
        $sql = "SELECT Vehicle_ID FROM Vehicles WHERE Vehicle_licence ='".$vehicleLicence."';";
        $vehiclesID = array();
        $conn = $this->conn;
        $results = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($results)) {
            array_push($vehiclesID, $row["Vehicle_ID"]);
        }
        return $vehiclesID;
    }

    function updateVehicle($vehicle){

    }
}
?>