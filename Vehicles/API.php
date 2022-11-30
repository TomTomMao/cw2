<?php
    function isVehicleLicenceExists ($vehicleLicence, $user) {
        $vehiclesDB = new VehiclesDB($user->getUsername());
        if ($vehiclesDB->isVehicleExists($vehicleLicence)) {
            echo "true,".$vehicleLicence." is already in the database";
        } else {
            echo "false,".$vehicleLicence." is new";
        }
    }
    function isPersonLicenceInDB($personLicence, $user) {
        if (!$personLicence) {
            echo "false,driving licence shouldn't be empty";
            return;
        } elseif(strlen($personLicence) != 16) {
            echo "false,driving licence must be 16 length";
        }
        $peopleDB = new PeopleDB($user->getUsername());
        $people = $peopleDB->getPeopleByLicence($personLicence);
        if (is_null($people)){
            echo "true,person is new";
        } else {
            echo "true,person is not new";
        };
    }

    function getPersonByLicence($personLicence, $user){
        $peopleDB = new PeopleDB($user->getUsername());
        $person = $peopleDB->getPersonByLicence($personLicence);
        if ($person != NULL) {
            echo $person->getJSONText();
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
    function isVehicleLicenceValid($licence) {
        if (strlen($licence) == 7) {
            echo "true";
        } else {
            echo "false";
        }
    } function createNewVehicleWithOwner($vehicleLicence,$vehicleColour,$vehicleMake,$vehicleModel,
    $personLicence,$personFirstName,$personLastName,$personAddress,$personDOB,$user) {
        // get data of vehicle and person, create it , used for creating new vehicle with an exist or non exist owner.
        $newVehicle = new Vehicle($vehicleLicence,$vehicleColour,$vehicleMake,$vehicleModel,NULL);
        $person = new Person(NULL,$personLicence,$personAddress,$personDOB,$personFirstName." ".$personLastName,NULL);
        $ownershipDB = new OwnershipDB($user->getUsername());
        $newOwnershipID = $ownershipDB->insertOwnershipWithNewVehicle($newVehicle,$person);
        if ($newOwnershipID != False) {
            echo '{"state":"success","newOwnershipID":"'.$newOwnershipID.'"}';
        } else {
            echo '{"state":"failed","reason":"unknown"}';
        }
    }
?>
<?php
    session_start();
    require("../Accounts/_account.php");// there is a User class
    require("../Vehicles/_vehicles.php");
    require("../Vehicles/_ownership.php");
    require("../People/_people.php");
    $user = new User();
    if (!$user->isLoggedIn()) {
        header("location: ../Accounts/notLoginError.html"); // check if logged in
    } else {
        // routing url to the functions
        sleep(0.5);
        $functionName = $_GET["function"];
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
            $personLicence,$personFirstName,$personLastName,$personAddress,$personDOB,$user);
        } elseif ($functionName=="isVehicleLicenceExists") {
            $vehicleLicence = $_GET["vehicleLicence"];
            isVehicleLicenceExists($vehicleLicence, $user);
        } elseif ($functionName=="isPersonLicenceInDB") {
            $personLicence = $_GET["personLicence"];
            isPersonLicenceInDB($personLicence, $user);
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
            getPersonByLicence($personLicence, $user);
        }
    }
?>