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
        function toJSON() {
            if ($this->hasPerson() && $this->hasVehicle()) {
                return '{"ownershipID": "'.$this->ID.'","owner":'.$this->getPerson()->toJSON()
                    .',"vehicle":'.$this->getVehicle()->toJSON().'}';
            } elseif ($this->hasPerson()) {
                return '{"ownershipID": "'.$this->ID.'","owner":'.$this->getPerson()->toJSON()
                    .',"vehicle":{"ID":"","licence":"","colour":"","make":"","model":""}}';
            } elseif ($this->hasVehicle()) {
                return '{"ownershipID": "'.$this->ID.'","owner":{"ID":"","licence":"","address":"","dateOfBirth":"","firstName":"","lastName":"","photoID":""}'
                    .',"vehicle":'.$this->getVehicle()->toJSON().'}';
            } else {
                return '{"ownershipID": "'.$this->ID.'","owner":{"ID":"","licence":"","address":"","dateOfBirth":"","firstName":"","lastName":"","photoID":""}'
                .',"vehicle":{"ID":"","licence":"","colour":"","make":"","model":""}}';
            }
            
        }
        function hasVehicle() {
            if ($this->vehicle) {
                return true;
            } else {
                return false;
            }
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
            if ($this->vehicle->getID()==NULL) {
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
        function __construct($user, $conn){ // for audit trial function
            $this->user = $user;
            $this->conn = $conn;
        }
        function recordGetVehicleByLicence(){
        }
        function getOwnershipsByLicence($vehicleLicence) {
            // assume already connect to the database, and after the function return, the database connection should be disconnected
            // input the vehicle licence number, select them from vehicle table and left join ownership and people table.
            // return a list of ownership object, include ownership without any owner. 
            // note the ownership objects returned include those not in the database for the purpose of future flexibility
            $ownerships = array();
            require("../People/_people.php");
            require("_vehicles.php");

            $conn = $this->conn;

            $sql = "SELECT * FROM Vehicles LEFT JOIN Ownership USING (Vehicle_ID) 
            LEFT JOIN People USING(People_ID) 
            WHERE Vehicle_licence='".$vehicleLicence.
            "' GROUP BY CONCAT(Vehicle_ID, People_ID);";
                        
             // success to connect database
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
        
            if (count($ownerships)==0) {
                $vehicle = new Vehicle($vehicleLicence, NULL, NULL, NULL, NULL);
                $person = NULL;
                $ownership = new Ownership($vehicle, $person, NULL);
                array_push($ownerships, $ownership);
            }
            return $ownerships;

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

            // assume all attributes in $vehicle and $person satisfy the constraint
            // If the vehicle and person are both new, insert the vehicle and person into their table,
                // Use their id, insert an entry of data into ownership. 
                // Return ["state"=>"success", "newOwnershipID"=>$newOwnershipID, "vehicleID"=>$newVehicleID, "personID"=>$newPersonID]
            // If the vehicle is new, but the person is not new, insert person into it's table.
                // Use their id, insert an entry of data into ownership. 
                // Return ["state"=>"success", "newOwnershipID"=>$newOwnershipID, "vehicleID"=>$newVehicleID, "personID"=>$oldPersonID]
               

            // echo "flag0";
            // insert vehicle into vehicle table if no vehicle licence in db match, insert person into people table if no driving licence in db matches
            $conn = $this->conn;
            debugEcho("MySQL connection OK<br>");
            // echo "flag0.3";
            // check if the licence is in the database
            $sql = "SELECT Vehicle_ID FROM Vehicles WHERE Vehicle_licence='".$vehicle->getLicence()."';";
            $results = mysqli_query($conn, $sql);
            // echo "<hr>";
            // print_r($results);
            // echo "<hr>";
            if ($results->num_rows == 1) {// the vehicle is already in the database // return false array
                // echo "flag9";
                return ["state"=>"failed", "reason"=>"vehicle licence already in the database"];
            } elseif ($results->num_rows == 0) {
                // echo "flag0.4";
                // the vehicle licence is new to the database
                // check if the person has licence
                if ($person->getLicence()=="NULL") { // the person doens't has a licence, not support this now. // return array
                    // echo "flag8";
                    return ["state"=>"failed", "reason"=>"person don't have licence"];
                } elseif ($person->getLicence()!="NULL") { // the person has licence
                    // echo "flag1";
                    // check if the person licence in the database 
                    $sql = "SELECT People_ID FROM People WHERE People_licence='".$person->getLicence()."';";
                    $results = mysqli_query($conn, $sql);
                    if ($results->num_rows > 1) {
                        throw new Exception("Data error: there are more than one entry of data in People share the identical driving licence number".
                    ". SQL query: ".$sql);
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
                        .$person->getDOB()."', NULL);";
                        $results = mysqli_query($conn, $sql); // insert the person
                        $newPersonID = mysqli_insert_id($conn); // get the person_id
                        // echo "flag4";
                        $sql = "INSERT INTO Ownership (People_ID, Vehicle_ID) VALUES
                        ('".$newPersonID."', '"
                        .$newVehicleID."');";// insert into ownership table
                        $results = mysqli_query($conn, $sql);
                        $newOwnershipID = mysqli_insert_id($conn);
                        
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

                        return ["state"=>"success", "newOwnershipID"=>$newOwnershipID,
                            "vehicleID"=>$newVehicleID, "personID"=>$oldPersonID];
                        // return $newOwnershipID;
                    }
                }
            }
             
        }
        function insertOwnershipBothExisted($ownership) {
            // Assume $ownership->vehicle object has id and the id is in the database.
            // Assume $ownership->person object has id and the id is in the database or the id="NULL".
            // insert an new entry into ownership table using these IDs.
            // return the id of the entry inserted in the database.
            if ($ownership->getPersonID()=="NULL") {
                $People_ID = $ownership->getPersonID();
            }
            $People_ID = "'".$ownership->getPersonID()."'";

            $sql = "INSERT INTO Ownership (Vehicle_ID, People_ID) VALUES ('".$ownership->getVehicleID()."', ".$ownership->getPersonID().");";
            // echo $sql; //debugging
            $conn = $this->conn; 
            $results = mysqli_query($conn, $sql);
            $lastID = mysqli_insert_id($conn);
            return $lastID;
               
        }
        function isOwnershipInDB(Ownership $ownership) {
            // Given an ownership object, which has NON-NULL value for vehicleID and peopleID,
            // search this ownership using the combination of vehicleID and peopleID.
            // Return the Ownership_ID if there is such an ownership in the database,
            // Return false if such ownership doesn't exist in the database.

            if ($ownership->getPersonID() == "NULL") {
                throw new Exception("Error: Ownership's person has a null value.");
            } elseif ($ownership->getVehicleID() == "NULL") {
                throw new Exception("Error: Ownership's vehicle has a null value.");
            }
            $conn = $this->conn;
            $sql = "SELECT Ownership_ID FROM Ownership WHERE People_ID='"
            .$ownership->getPersonID()."' AND Vehicle_ID='".$ownership->getVehicleID()."';";
            // echo "<hr>"."ownershipDB->isOwnershipInDB: ".$sql."<hr>"; //debugging

            $result = mysqli_query($conn, $sql);
            $ownershipIDs = array();
            while($row = mysqli_fetch_assoc($result)) {
                array_push($ownershipIDs, $row["Ownership_ID"]);
            }

            if (empty($ownershipIDs)) {
                // echo "return value from ownership->isOwnershipInDB: false<br>";//debugging
                return false;
            } elseif (count($ownershipIDs) == 1) {
                // echo "return value from ownership->isOwnershipInDB:".$ownershipIDs[0]." <br>";//debugging
                return $ownershipIDs[0];
            } else {
                $msg = "[";
                foreach ($ownershipIDs as $ownershipID){
                    $msg = $msg.$ownershipID;
                }
                $msg = $msg."]";
                throw new Exception("Error: There is redundancy in ownership table! \$ownershipIDs=$msg");
            }
        }
        function isOwnershipInDBPersonNull($ownership) {
            // Given an ownership object, which has NON-NULL value for vehicleID and a NULL value for peopleID,
            // search this ownership using the combination of vehicleID and NULL peopleID.
            // Return the Ownership_ID if there is such an ownership in the database,
            // Return false if such ownership doesn't exist in the database.
            if ($ownership->getVehicleID() == "NULL") {
                throw new Exception("Error: Ownership's vehicle has a null value.");
            } elseif ($ownership->getPersonID() != "NULL") {
                throw new Exception("Error: Ownership's person has a non-null value.");
            } 
            $conn = $this->conn;
            $sql = "SELECT Ownership_ID FROM Ownership WHERE People_ID IS NULL AND Vehicle_ID='".$ownership->getVehicleID()."';";
            // echo "<hr>"."ownershipDB->isOwnershipInDBPersonNull: ".$sql."<hr>";//debugging

            $result = mysqli_query($conn, $sql);
            $ownershipIDs = array();
            while($row = mysqli_fetch_assoc($result)) {
                array_push($ownershipIDs, $row["Ownership_ID"]);
            }
            
            if (empty($ownershipIDs)) {
                // echo "return value from ownership->isOwnershipInDB: false<br>"; //debugging
                return false;
            } elseif (count($ownershipIDs) >= 1) {
                // echo "return value from ownership->isOwnershipInDB:".$ownershipIDs[0]." <br>"; //debugging
                return $ownershipIDs[0];
            } 
        }
    }

?>
<?php 
    // session_start();
    // // require_once("../../Vehicles/_ownership.php");
    // require_once("../Vehicles/_vehicles.php");
    // require_once("../People/_people.php");
    // require_once("../reuse/_dbConnect.php");
    // require_once("../Accounts/_account.php");
    // $user = new User();
    // if (!$user->isLoggedIn()) {
    //     header("location: ../Accounts/notLoginError.html"); // check if logged in
    // }
    // $conn = connectDB();
    // $ownershipDB = new OwnershipDB($user, $conn);
    // function testInsertOwnershipBothExisted_Trivial($ownershipDB){
    //     $vehicle = new Vehicle("NULL", "NULL", "NULL", "NULL","12");
    //     $person = new Person("1", "NULL", "NULL","NULL", "NULL","NULL");
    //     $ownership = new Ownership($vehicle, $person, "NULL");
    //     $actualValue = $ownershipDB->insertOwnershipBothExisted($ownership);
    //     $expectedValue = "11";
    //     if ($actualValue==$expectedValue) {
    //         echo "<hr>test testInsertOwnershipBothExisted_Trivial() passed!<hr>";
    //         return true;
    //     } else {
    //         echo "<hr>test testInsertOwnershipBothExisted_Trivial() failed!
    //         <br>expected value: $expectedValue
    //         <br>actual value: $actualValue<hr>";
    //         return false;
    //     }
    // }
    // function testIsOwnershipInDB_Exists($ownershipDB) {
    //     try {
    //         $vehicle = new Vehicle("NULL", "NULL", "NULL", "NULL", "12");
    //         $person = new Person("3", "NULL", "NULL", "NULL", "NULL", "NULL");
    //         $ownership = new Ownership($vehicle, $person, "NULL");
    //         $actualValue = $ownershipDB->isOwnershipInDB($ownership);
    //         $expectedValue = "1";
    //         if ($actualValue==$expectedValue) {
    //             echo "<hr>test testIsOwnershipInDB_Exists() passed!<hr>";
    //             return true;
    //         } else {
    //             echo "<hr>test testIsOwnershipInDB_Exists() failed!
    //             <br>expected value: $expectedValue
    //             <br>actual value: $actualValue<hr>";
    //             return false;
    //         }
    //     } catch (Exception $error) {
    //         echo "<hr>test testIsOwnershipInDB_Exists() failed!
    //             <br>error message: ".$error->getMessage()."<hr>";
    //         return false;
    //     }
    // }
    // function testIsOwnershipInDB_New($ownershipDB) {
    //     try {
    //         $vehicle = new Vehicle("NULL", "NULL", "NULL", "NULL", "1");
    //         $person = new Person("1", "NULL", "NULL", "NULL", "NULL", "NULL");
    //         $ownership = new Ownership($vehicle, $person, "NULL");
    //         $actualValue = $ownershipDB->isOwnershipInDB($ownership);
    //         $expectedValue = false;
    //         if ($actualValue==$expectedValue) {
    //             echo "<hr>test testIsOwnershipInDB_New() passed!<hr>";
    //             return true;
    //         } else {
    //             echo "<hr>test testIsOwnershipInDB_New() failed!
    //             <br>expected value: $expectedValue
    //             <br>actual value: $actualValue<hr>";
    //             return false;
    //         }
    //     } catch (Exception $error) {
    //         echo "<hr>test testIsOwnershipInDB_New() failed!
    //             <br>error message: ".$error->getMessage()."<hr>";
    //         return false;
    //     }
    // }

    // function isOwnershipInDBPersonNull_Exists($ownershipDB) {
    //     try {
    //         $vehicle = new Vehicle("NULL", "NULL", "NULL", "NULL", "22");
    //         $person = new Person("NULL", "NULL", "NULL", "NULL", "NULL", "NULL");
    //         $ownership = new Ownership($vehicle, $person, "NULL");
    //         $actualValue = $ownershipDB->isOwnershipInDBPersonNull($ownership);
    //         $expectedValue = "10";
    //         if ($actualValue==$expectedValue) {
    //             echo "<hr>test isOwnershipInDBPersonNull_Exists() passed!<hr>";
    //             return true;
    //         } else {
    //             echo "<hr>test isOwnershipInDBPersonNull_Exists() failed!
    //             <br>expected value: $expectedValue
    //             <br>actual value: $actualValue<hr>";
    //             return false;
    //         }
    //     } catch (Exception $error) {
    //         echo "<hr>test isOwnershipInDBPersonNull_Exists() failed!
    //             <br>error message: ".$error->getMessage()."<hr>";
    //         return false;
    //     }
    // }

    // function isOwnershipInDBPersonNull_New($ownershipDB) {
    //     try {
    //         $vehicle = new Vehicle("21", "NULL", "NULL", "NULL", "1");
    //         $person = new Person("NULL", "NULL", "NULL", "NULL", "NULL", "NULL");
    //         $ownership = new Ownership($vehicle, $person, "NULL");
    //         $actualValue = $ownershipDB->isOwnershipInDB($ownership);
    //         $expectedValue = false;
    //         if ($actualValue==$expectedValue) {
    //             echo "<hr>test isOwnershipInDBPersonNull_New() passed!<hr>";
    //             return true;
    //         } else {
    //             echo "<hr>test isOwnershipInDBPersonNull_New() failed!
    //             <br>expected value: $expectedValue
    //             <br>actual value: $actualValue<hr>";
    //             return false;
    //         }
    //     } catch (Exception $error) {
    //         echo "<hr>test isOwnershipInDBPersonNull_New() failed!
    //             <br>error message: ".$error->getMessage()."<hr>";
    //         return false;
    //     }
    // }

    // function runAllTest($testFunctions, $ownershipDB) {
    //     $results = array(); // true or false;
    //     foreach($testFunctions as $testFunction) {
    //         echo "<h3>test: $testFunction</h3>";
    //         array_push($results, call_user_func($testFunction,$ownershipDB));
    //     }
    //     $allPass = true;
    //     foreach($results as $result) {
    //         $allPass = $allPass && $result;
    //     }
    //     if ($allPass == true) {
    //         echo "<h1><b>All Test Case Passed!</b></h1>";
    //     } else {
    //         echo "<h1><b>Some Test Case Failed!</b></h1>";
    //     }
    // }

    // $testFunctions = ["testInsertOwnershipBothExisted_Trivial","testIsOwnershipInDB_Exists","testIsOwnershipInDB_New","isOwnershipInDBPersonNull_Exists"];
    // runAllTest($testFunctions,$ownershipDB);
    // // please manual reset the data base after run all the test.
    // mysqli_close($conn);
?>
