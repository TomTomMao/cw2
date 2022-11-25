<?php
    $debugOn=false;
    require_once("../config/debug.php");
    class OwnershipDB {
        // this is a class for handling data about Vehicles
        function __construct($username){ // for audit trial function
            $this->username = $username;
        }
        function recordGetVehicleByLicence(){
        }
        function getVehicleByLicence($vehicleLicence) {
            // Input the licence of a vehicle
            // If found the vehicle, Return an array of "ownershipData" that match the licence or an empty array. The key is the ownership_id;
            // If not found the vehicle, return an array of "ownershipData" that the only non-null value is of "Vehicle_Licence", the length must be 1, key is numeric;
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
            // $ownershipsData should be an array of "ownershipData",
            // "ownershipData" should be an array of attritube of one single vehicle,  "Vehicle_ID","Vehicle_licence", "Vehicle_type",
            //   "Vehicle_colour", "People_name", "People_licence", "People_ID", "People_address", "People_DOB" should be in the ownershipData;
            // RETURN A DOM DIV OBJECT in string format.
            $obj = "";
            foreach ($ownershipsData as $ownershipData) {
                $vehicleLicence = $ownershipData["Vehicle_licence"];
                
                if (empty($ownershipData["Vehicle_type"])) {
                    $vehicleType="unknown";
                } else {
                    $vehicleType=$ownershipData["Vehicle_type"];
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
                                <td>Vehicle Type</td>
                                <td>".$vehicleType."</td>
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

    
    function runTests() {
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