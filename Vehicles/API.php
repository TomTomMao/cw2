<?php
    
    function isPersonLicenceInDB($personLicence, $user, $conn) {
        if (!$personLicence) {
            echo "false,driving licence shouldn't be empty";
            return;
        } elseif(strlen($personLicence) != 16) {
            echo "false,driving licence must be 16 length";
        }
        $peopleDB = new PeopleDB($user, $conn);
        $people = $peopleDB->getPeopleByLicence($personLicence);
        if (is_null($people)){
            echo "true,person is new";
        } else {
            echo "true,person is not new";
        };
    }


    function getPersonByLicence($personLicence, $user, $conn){
        $peopleDB = new PeopleDB($user, $conn);
        $person = $peopleDB->getPersonByLicence($personLicence);
        if ($person != NULL) {
            echo $person->toJSON();
        } else {
            echo "NULL";
        }
    }
    function checkVehicleLicenceFormat($correct) {
        if ($correct) {
            echo "true";
        } else {
            echo "false";
        };
    }
    function isColourValid($colour) {
        if (strlen($colour)<=20 && strlen($colour)>0) {
            echo "true,colour is valid";
        } else {
            echo "false,length should be >0 and <=20";
        }
    }
    function isMakeValid($make) {
        if (strlen($make)<=20 && strlen($make)>0) {
            echo "true,make is valid";
        } else {
            echo "false,length should be >0 and <=20";
        }
    }
    function isModelValid($model) {
        if (strlen($model)<=20 && strlen($model)>0) {
            echo "true,model is valid";
        } else {
            echo "false,model format is incorrect";
        }
    }
    function isFnameValid($fname) {
        if (strlen($fname)<=25 && strlen($fname)>0) {
            echo "true,firstname is valid";
        } else {
            echo "false,firstname format is incorrect";
        }
    }
    function isLnameValid($lname) {
        if (strlen($lname)<=25 && strlen($lname)>0) {
            echo "true,lastname is valid";
        } else {
            echo "false,lastname format is incorrect";
        }
    }
    function isaddressValid($address) {
        if (strlen($address)<=50 && strlen($address)>0) {
            echo "true,address is valid";
        } else {
            echo "false,address format is incorrect";
        }
    }
    function isDOBValid($DOB) {
        // echo "<br>dob:".$DOB."<br>";
        if ($DOB) {
            echo "true,DOB is valid";
        } else {
            echo "false,DOB format incorrect";
        }
    }
    function isVehicleLicenceValid($vehicleLicence, $user, $conn) {
        // echo "called isvehicleLicenceValide in API.php";
        function _isVehicleLicenceExists ($vehicleLicence, $user, $conn) {
            $vehiclesDB = new VehiclesDB($user, $conn);
            if ($vehiclesDB->isVehicleExists($vehicleLicence)) {
                return true;
                // echo "true,".$vehicleLicence." is already in the database";
            } else {
                return false;
                // echo "false,".$vehicleLicence." is new";
            }
        }

        if (strlen($vehicleLicence) == 7) {
            if (_isVehicleLicenceExists($vehicleLicence, $user, $conn)) {
                echo "false,".$vehicleLicence." is already in the database";
            } else {
                echo "true,".$vehicleLicence." is new";
            }
        } else {
            echo "false,Vehicle Licence format incorrect";
        }
    } function createNewVehicleWithOwner($vehicleLicence,$vehicleColour,$vehicleMake,$vehicleModel,
    $personLicence,$personFirstName,$personLastName,$personAddress,$personDOB,$user,$conn) {
        // get data of vehicle and person, create it , used for creating new vehicle with an exist or non exist owner.
        $newVehicle = new Vehicle($vehicleLicence,$vehicleColour,$vehicleMake,$vehicleModel,"NULL");
        $person = new Person("NULL",$personLicence,$personAddress,$personDOB,$personFirstName." ".$personLastName,"NULL");
        $ownershipDB = new OwnershipDB($user,$conn);
        $result = $ownershipDB->insertOwnershipWithNewVehicle($newVehicle,$person,$conn);
        if (!isset($result["state"])) {
            echo '{"state":"error", "reason":"missing information in insertOwnershipWithNewVehicle()"}';
            print_r($result);
        } elseif ($result["state"]=="failed") {
                echo '{"state":"'.$result["state"].'","reason":"'.$result["reason"].'"}';
        } elseif ($result["state"]=="success") {
                echo '{"state":"'.$result["state"]
                    .'","newOwnershipID":"'.$result["newOwnershipID"]
                    .'","vehicleID":"'.$result["vehicleID"]
                    .'","personID":"'.$result["personID"]
                    .'"}'; 
        } else {
            echo '{"state":"error", "reason":"unexpected data in the state field"}';
            print_r($result);
        }
        
        
    }
?>
<?php
    session_start();
    require("../Accounts/_account.php");// there is a User class
    require("../Vehicles/_vehicles.php");
    require("../Vehicles/_ownership.php");
    require("../People/_people.php");
    require("../reuse/_dbConnect.php");
    $user = new User();
    if (!$user->isLoggedIn()) {
        header("location: ../Accounts/notLoginError.html"); // check if logged in
    } else {
        // routing url to the functions
        usleep(rand(10000,500000));
        $functionName = $_GET["function"];
        $conn = connectDB(); // connect to db
        if ($functionName=="createNewVehicleWithOwner") {
            $vehicleLicence = $_GET["vehicleLicence"];
            $vehicleColour = $_GET["vehicleColour"];
            $vehicleMake = $_GET["vehicleMake"];
            $vehicleModel = $_GET["vehicleModel"];
            $personLicence = $_GET["personLicence"];
            $personFirstName = $_GET["personFirstName"];
            $personLastName = $_GET["personLastName"];
            $personAddress = $_GET["personAddress"];
            $personDOB = $_GET["personDOB"];
            createNewVehicleWithOwner($vehicleLicence,$vehicleColour,$vehicleMake,$vehicleModel, 
            $personLicence,$personFirstName,$personLastName,$personAddress,$personDOB,$user,$conn);
        } elseif ($functionName=="isVehicleLicenceValid") {
            $vehicleLicence = $_GET["vehicleLicence"];
            isVehicleLicenceValid($vehicleLicence, $user, $conn);
        } elseif ($functionName=="isPersonLicenceInDB") {
            $personLicence = $_GET["personLicence"];
            isPersonLicenceInDB($personLicence, $user, $conn);
        } elseif ($functionName=="isColourValid") {
            $colour = $_GET["colour"];
            isColourValid($colour);
        } elseif ($functionName=="isMakeValid") {
            $make = $_GET["make"];
            isMakeValid($make);
        } elseif ($functionName=="isModelValid") {
            $model = $_GET["model"];
            isModelValid($model);
        } elseif ($functionName=="isFnameValid") {
            $fName = $_GET["fName"];
            isFnameValid($fName);
        } elseif ($functionName=="isLnameValid") {
            $lName = $_GET["lName"];
            isLNameValid($lName);
        } elseif ($functionName=="isAddressValid") {
            $address = $_GET["address"];
            isAddressValid($address);
        } elseif ($functionName=="isDOBValid") {
            $DOB = $_GET["DOB"];
            isDOBValid($DOB);
        } elseif ($functionName=="getPersonByLicence") {
            $personLicence = $_GET["personLicence"];
            getPersonByLicence($personLicence, $user, $conn);
        }
        mysqli_close($conn);
    }
?>