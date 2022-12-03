<?php
    $debugOn=false;
    require_once("../config/debug.php");
    
    class Ownership {
        function __construct($vehicle, $person, $ID) {
            // input and vehicle object, a person object and a ID, person and ID can be null
            $this->ID = $ID;
            $this->vehicle = $vehicle;
            $this->person = $person;
        }
        function hasPerson() {
            // if this ownership has person, return true;
            // else return false;
            if ($this->person) {
                return true;
            } else {
                return false;
            }
        }
        function setVehicle ($vehicle){
            $this->vehicle = $vehicle;
        }
        function setPerson ($person){
            $this->person = $person;
        }
        function setID($ID) {
            $this->ID = $ID;
        }
        function getVehicle() {
            return $this->vehicle;
        }
        function getPerson() {
            return $this->person;
        }
        function getID() {
            return $this->ID;
        }


        function isPersonIDNull() {
            if ($this->person && $this->person->getID()) {
                return false;
            } else {
                return true;
            }
        }
        function getPersonID() {
            // if the ownership has set a person, return the person's id
            if (!$this->hasPerson()) {
                throw new Exception("this ownership don't contain a person");
            }
            return $this->person->getID();
        }
        function getPersonFullName() {
            if (!$this->hasPerson()) {
                throw new Exception("this ownership don't contain a person");
            }
            return $this->person->getFullName();
        }
        function getPersonFirstName() {
            if (!$this->hasPerson()) {
                throw new Exception("this ownership don't contain a person");
            }
            return $this->person->getFirstName();
        }
        function getPersonLastName() {
            if (!$this->hasPerson()) {
                throw new Exception("this ownership don't contain a person");
            }
            return $this->person->getLastName();
        }
        function getPersonAddress() {
            if (!$this->hasPerson()) {
                throw new Exception("this ownership don't contain a person");
            }
            return $this->person->getAddress();
        }
        function getPersonDOB() {
            if (!$this->hasPerson()) {
                throw new Exception("this ownership don't contain a person");
            }
            return $this->person->getDOB();
        }
        function getPersonLicence() {
            if (!$this->hasPerson()) {
                throw new Exception("this ownership don't contain a person");
            }
            return $this->person->getLicence();
        }
        function getPhotoID() {
            if (!$this->hasPerson()) {
                throw new Exception("this ownership don't contain a person");
            }
            return $this->person->getPhotoID();
        }




        function isVehicleIDNull() {
            // return if the vehicle id is null
            if ($this->vehicle->getID()) {
                return true;
            } else {
                return false;
            }
        }

        function getVehicleID() {
            return $this->vehicle->getID();
        }
        function getVehicleLicence() {
            return $this->vehicle->getLicence();
        }
        function getVehicleMake() {
            return $this->vehicle->getMake();
        }
        function getVehicleModel() {
            return $this->vehicle->getModel();
        }
        function getVehicleColour() {
            return $this->vehicle->getColour();
        }

        function render() {
            $vehicleLicence = $this->getVehicleLicence() ? $this->getVehicleLicence() : "unknown";
            $vehicleID = $this->getVehicleID() ? $this->getVehicleID() : "unknown";
            $vehicleMake = $this->getVehicleMake() ? $this->getVehicleMake() : "unknown";
            $vehicleModel = $this->getVehicleModel() ? $this->getVehicleModel() : "unknown";
            $vehicleColour = $this->getVehicleColour() ? $this->getVehicleColour() : "unknown";
            if (isset($this->person)) {
                $personID = $this->getPersonID() ? $this->getPersonID() : "unknown";
                $personLicence = $this->getPersonLicence() ? $this->getPersonLicence() : "unknown";
                $personFullName = $this->getPersonFullName() ? $this->getPersonFullName() : "unknown";
                $personDOB = $this->getPersonDOB() ? $this->getPersonDOB() : "unknown";
                $personAddress = $this->getPersonAddress() ? $this->getPersonAddress() : "unknown";
            } else {
                $personID = "unknown";
                $personLicence = "unknown";
                $personFullName = "unknown";
                $personDOB = "unknown";
                $personAddress = "unknown";
            }
                $ownershipID = $this->getID()!=NULL ? $this->getID() : "Ownership not in database";
            $obj = "
            <div class='ownership-container'>
                <table>
                    <tr>
                        <th>Ownership ID</th>
                        <th>".$ownershipID."</th>
                    </tr>
                    <tr>
                        <td>Vehicle Licence</td>
                        <td>".$vehicleLicence."</td>
                    <tr>
                    <tr>
                        <td>Vehicle Make</td>
                        <td>".$vehicleMake."</td>
                    <tr>
                    <tr>
                        <td>Vehicle Model</td>
                        <td>".$vehicleModel."</td>
                    <tr>
                    <tr>
                        <td>Vehicle Colour</td>
                        <td>".$vehicleColour."</td>
                    <tr>
                    <tr>
                        <td>Owner Name</td>
                        <td>".$personFullName."</td>
                    <tr>
                    <tr>
                        <td>Owner ID</td>
                        <td>".$personID."</td>
                    <tr>
                    <tr>
                        <td>Owner's Licence</td>
                        <td>".$personLicence."</td>
                    <tr>
                    <tr>
                        <td>Owner's Licence</td>
                        <td>".$personAddress."</td>
                    <tr>
                    <tr>
                        <td>Owner's Licence</td>
                        <td>".$personDOB."</td>
                    <tr>
                </table>    
            </div>
            ";
            return $obj;
        }
    }
    class OwnershipDB {
        // this is a class for handling data about Vehicles
        function __construct($username){ // for audit trial function
            $this->username = $username;
        }
        function recordGetVehicleByLicence(){
        }
        function getOwnershipsByLicence($vehicleLicence) {
            // input the vehicle licence number, select them from vehicle table and left join ownership and people table.
            // return a list of ownership object, include ownership without any owner. 
            // note the ownership objects returned include those not in the database for the purpose of future flexibility
            $ownerships = array();
            require("../People/_people.php");
            require("_vehicles.php");

            $sql = "SELECT * FROM Vehicles LEFT JOIN Ownership USING (Vehicle_ID) 
            LEFT JOIN People USING(People_ID) 
            WHERE Vehicle_licence='".$vehicleLicence.
            "' GROUP BY CONCAT(Vehicle_ID, People_ID);";
            
            require("../config/db.inc.php");
            $conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
            if(mysqli_connect_errno()) { // cannot connect database
                debugEcho ("Failed to connect to MySQL: ".mysqli_connect_error()); // for debugging
                die();
            } else { // success to connect database
                debugEcho("MySQL connection OK<br>");
                $results = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_assoc($results)) { 
                    // row >= 1, this case, vehicle is in database
                    $vehicle = new Vehicle($row["Vehicle_licence"], $row["Vehicle_colour"], $row["Vehicle_make"], $row["Vehicle_model"],$row["Vehicle_ID"]);
                    if ($row["People_ID"]) { // the result has ownerid
                        $person = new Person($row["People_ID"], $row["People_licence"], $row["People_address"], $row["People_DOB"], $row["People_name"], $row["People_photoID"]);
                    } else {
                        $person = NULL;
                    }
                    $ownership = new Ownership($vehicle, $person, $row["Ownership_ID"]);
                    array_push($ownerships, $ownership);
                }
                mysqli_close($conn); // disconnect
            }
            if (count($ownerships)==0) {
                $vehicle = new Vehicle($vehicleLicence, NULL, NULL, NULL, NULL);
                $person = NULL;
                $ownership = new Ownership($vehicle, $person, NULL);
                array_push($ownerships, $ownership);
            }
            return $ownerships;

        }
        function getVehicleByLicence($vehicleLicence) {
            // don't use this, use getOwnershipsByLicence instead.
            // Input the licence of a vehicle
            // If found the vehicle, RETURN an array of "ownershipData" that match the licence or an empty array. The key is the ownership_id;
            // If not found the vehicle, RETURN an array of "ownershipData" that the only non-null value is of "Vehicle_Licence", the length must be 1, key is "NULL";
            //      "ownershipData" contains "Vehicle_ID","Vehicle_licence", "Vehicle_type",
            //          "Vehicle_colour", "People_name", "People_licence", "People_ID", "People_address", "People_DOB";
            $ownershipsData = array();
            $sql = "SELECT * FROM Vehicles LEFT JOIN Ownership USING (Vehicle_ID) 
            LEFT JOIN People USING(People_ID) 
            WHERE Vehicle_licence='".$vehicleLicence.
            "' GROUP BY CONCAT(Vehicle_ID, People_ID);";

            debugEcho($sql);
            require("../config/db.inc.php");
            $conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
            if(mysqli_connect_errno()) { // cannot connect database
                debugEcho ("Failed to connect to MySQL: ".mysqli_connect_error()); // for debugging
                die();
            } else { // success to connect database
                debugEcho("MySQL connection OK<br>");
                $results = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_assoc($results)) {
                    $ownershipsData[strval($row["Ownership_ID"])] = $row;//converted to strval to avoid long array.
                }
                mysqli_close($conn); // disconnect
            }
            if (count($ownershipsData)==0){
                $ownershipsData["NULL"] = array(
                    "Vehicle_ID" => NULL,
                    "Vehicle_licence" => $vehicleLicence,
                    "Vehicle_type" => NULL,
                    "Vehicle_colour" => NULL,
                    "People_name" => NULL,
                    "People_licence" => NULL,
                    "People_ID" => NULL,
                    "People_address" => NULL,
                    "People_DOB" => NULL
                );
            }
            return $ownershipsData;
        }
        function renderOwnershipData($ownershipsData) {
            // don't use this function anymore, use ownership->render()
            // $ownershipsData should be an array of "ownershipData",
            // "ownershipData" should be an array of attritube of one single vehicle,  "Vehicle_ID","Vehicle_licence", "Vehicle_type",
            //   "Vehicle_colour", "People_name", "People_licence", "People_ID", "People_address", "People_DOB" should be in the ownershipData;
            // RETURN A DOM DIV OBJECT in string format.
            $obj = "";
            foreach ($ownershipsData as $ownershipData) {
                $vehicleLicence = $ownershipData["Vehicle_licence"];
                
                if (empty($ownershipData["Vehicle_make"])) {
                    $vehicleMake="unknown";
                } else {
                    $vehicleMake=$ownershipData["Vehicle_make"];
                }
                if (empty($ownershipData["Vehicle_model"])) {
                    $vehicleModel="unknown";
                } else {
                    $vehicleModel=$ownershipData["Vehicle_model"];
                }
                if (empty($ownershipData["Vehicle_colour"])) {
                    $vehicleColour="unknown";
                } else {
                    $vehicleColour=$ownershipData["Vehicle_colour"];
                }
                if (empty($ownershipData["People_name"])) {
                    $peopleName="unknown";
                } else {
                    $peopleName=$ownershipData["People_name"];
                }
                if (empty($ownershipData["People_licence"])) {
                    $peopleLicence="unknown";
                } else {
                    $peopleLicence=$ownershipData["People_licence"];
                }
                $obj = $obj."
                    <div class='ownership-container'>
                        <table>
                            <tr>
                                <td>Vehicle Licence</td>
                                <td>".$vehicleLicence."</td>
                            <tr>
                            <tr>
                                <td>Vehicle Make</td>
                                <td>".$vehicleMake."</td>
                            <tr>
                            <tr>
                                <td>Vehicle Model</td>
                                <td>".$vehicleModel."</td>
                            <tr>
                            <tr>
                                <td>Vehicle Colour</td>
                                <td>".$vehicleColour."</td>
                            <tr>
                            <tr>
                                <td>Owner Name</td>
                                <td>".$peopleName."</td>
                            <tr>
                            <tr>
                                <td>Owner's Licence</td>
                                <td>".$peopleLicence."</td>
                            <tr>
                        </table>    
                    </div>
                    ";
            }
            return $obj;
        }
        function insertOwnershipWithNewVehicle($vehicle, $person) {
            // give a vehicle object and a person object
            // if the vehicle is not new, return ["state":"failed", "reason":"vehilce already existed"]
            // if the person don't have licence, return ["state": "failed", "reason":"person don't have driving licence"]
            // if the vehicle is new, insert the vehicle into table
            // if the person is new, insert the person into table
            // 
            // echo "flag0";
            // insert vehicle into vehicle table if no vehicle licence in db match, insert person into people table if no driving licence in db matches
            require("../config/db.inc.php");
            $conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
            // echo "flag0.1";
            if(mysqli_connect_errno()) { // cannot connect database
                // echo "flag0.2";
                debugEcho ("Failed to connect to MySQL: ".mysqli_connect_error()); // for debugging
                die();
            } else { // success to connect database
                debugEcho("MySQL connection OK<br>");
                // echo "flag0.3";
                // check if the licence is in the database
                $sql = "SELECT Vehicle_ID FROM Vehicles WHERE Vehicle_licence='".$vehicle->getLicence()."';";
                $results = mysqli_query($conn, $sql);
                // echo "<hr>";
                // print_r($results);
                // echo "<hr>";
                if ($results->num_rows == 0) {
                    // echo "flag0.4";
                    // the vehicle licence is new to the database
                    // check if the person has licence
                        if ($person->getLicence()!=NULL) { // the person has licence
                            // echo "flag1";
                            // check if the person licence in the database 
                            $sql = "SELECT People_ID FROM People WHERE People_licence='".$person->getLicence()."';";
                            $results = mysqli_query($conn, $sql);
                            if ($results->num_rows > 1) {
                                throw new Exception("Data error: there are more than one entry of data in People share the identical driving licence number".
                            "SQL query: ".$sql);
                            }
                            if ($results->num_rows==0) { // the person with licence is not in the database
                                // insert vehicle and person, and get their id, use this to insert into ownership table.
                                // echo "flag2";
                                $sql = "INSERT INTO Vehicles (Vehicle_make, Vehicle_model, Vehicle_colour, Vehicle_licence) VALUES
                                ('".$vehicle->getMake()."', '"
                                .$vehicle->getModel()."', '"
                                .$vehicle->getColour()."', '"
                                .$vehicle->getLicence()."');";
                                $results = mysqli_query($conn, $sql); // insert the vehicle
                                $newVehicleID = mysqli_insert_id($conn); // get the vehicle_id
                                // echo "flag3";
                                $sql = "INSERT INTO People (People_name, People_address, People_licence, People_DOB, People_photoID) VALUES
                                ('".$person->getFullName()."', '"
                                .$person->getAddress()."', '"
                                .$person->getLicence()."', '"
                                .$person->getDOB()."', '"
                                .$person->getPhotoID()."');";
                                $results = mysqli_query($conn, $sql); // insert the person
                                $newPersonID = mysqli_insert_id($conn); // get the person_id
                                // echo "flag4";
                                $sql = "INSERT INTO Ownership (People_ID, Vehicle_ID) VALUES
                                ('".$newPersonID."', '"
                                .$newVehicleID."');";// insert into ownership table
                                $results = mysqli_query($conn, $sql);
                                $newOwnershipID = mysqli_insert_id($conn);
                                mysqli_close($conn);
                                // echo "flag5";
                                return ["state"=>"success", "newOwnershipID"=>$newOwnershipID,
                                 "vehicleID"=>$newVehicleID, "personID"=>$newPersonID];
                                // return $newOwnershipID;
                            } else { // the person with licence is in the database
                                // echo "flag6";
                                $oldPersonID = mysqli_fetch_assoc($results)["People_ID"];
                                // insert vehicle, and it's id, use the existed person_id and the new vehicle id to insert a ownership into ownership table.
                                $sql = "INSERT INTO Vehicles (Vehicle_make, Vehicle_model, Vehicle_colour, Vehicle_licence) VALUES
                                ('".$vehicle->getMake()."', '"
                                .$vehicle->getModel()."', '"
                                .$vehicle->getColour()."', '"
                                .$vehicle->getLicence()."');";
                                $results = mysqli_query($conn, $sql); // insert the vehicle
                                $newVehicleID = mysqli_insert_id($conn); // get the vehicle_id
                                // echo "flag7";
                                $sql = "INSERT INTO Ownership (People_ID, Vehicle_ID) VALUES
                                ('".$oldPersonID."', '"
                                .$newVehicleID."');";// insert into ownership table
                                $results = mysqli_query($conn, $sql);

                                $newOwnershipID = mysqli_insert_id($conn);
                                mysqli_close($conn);
                                return ["state"=>"success", "newOwnershipID"=>$newOwnershipID,
                                 "vehicleID"=>$newVehicleID, "personID"=>$oldPersonID];
                                // return $newOwnershipID;
                            }
                        } else { // the person doens't has a licence, not support this now. // return false
                            // echo "flag8";
                            return ["state"=>"failed", "reason"=>"person don't have licence"];
                        }
                } elseif ($results->num_rows == 1) {// the vehicle is already in the database // return false
                    // echo "flag9";
                    mysqli_close($conn); // disconnect
                    return ["state"=>"failed", "reason"=>"vehicle licence already in the database"];
                }
            } 
        }
        function insertOwnershipBothExisted($ownership) {
            // Assume $ownership->vehicle object has id and in the database.
            // Assume $ownership->person object has id and in the database.
            // insert an new entry into ownership table using these IDs.
            // return the id of the entry inserted in the database.
            $sql = "INSERT INTO Ownership (Vehicle_ID, People_ID) VALUES ('".$ownership->getVehicleID()."', '".$ownership->getPersonID()."');";
            echo $sql;
            require("../config/db.inc.php");
            $conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
            if(mysqli_connect_errno()) { // cannot connect database
                debugEcho ("Failed to connect to MySQL: ".mysqli_connect_error()); // for debugging
                die();
            } else { // success to connect database
                debugEcho("MySQL connection OK<br>");
                $results = mysqli_query($conn, $sql);
                $lastID = mysqli_insert_id($conn);
                mysqli_close($conn); // disconnect
                return $lastID;
            }   
        }
        
        
    }







    
    // TEST CODE WHEN DEVELOP
    function testVehicleByLicence($vehicleLicence) {
        $user = new User();
        $ownershipDB = new OwnershipDB($user->getUsername());
        $ownershipsData = $ownershipDB->getVehicleByLicence($vehicleLicence);
        debugPrint_r($ownershipsData);
        echo "<br>NUMBER OF ROWS:".count($ownershipsData)."<br>";
        foreach($ownershipsData as $key=>$ownershipData) {
            echo $key;
            print_r ($ownershipData);
            echo "<br>";
        }
    }

    function testRenderOwnershipData($vehicleLicence) {
        $user = new User();
        $ownershipDB = new OwnershipDB($user->getUsername());
        $ownershipsData = $ownershipDB->getVehicleByLicence($vehicleLicence);
        $ownerShipDiv = $ownershipDB->renderOwnershipData($ownershipsData);
        echo $ownerShipDiv;
        }

    
    function runOwnershipTests() {
        $pageTitle="test _ownership";
        require("../head.php");
        session_start();
        require_once("../Accounts/_account.php");
        
        testVehicleByLicence("LB15AJL");
        testRenderOwnershipData("LB15AJL");
        testRenderOwnershipData("LKC2JNS");
        testRenderOwnershipData("BC16OEA");

        
        
    }
    
    // runTests();
    
?>