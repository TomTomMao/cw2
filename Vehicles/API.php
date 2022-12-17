<?php
    
    function isPersonLicenceInDB($personLicence, $user, $conn) {
        if (!$personLicence) {
            echo "false,driving licence shouldn't be empty";
            return;
        } elseif(strlen($personLicence) != 16) {
            echo "false,driving licence must be 16 length";
            return;
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
        $auditDB = new AuditDB($user, $conn);
        $person = $peopleDB->getPersonByLicence($personLicence);
        if ($person != NULL) {
            echo $person->toJSON();
            // create audit
            $audit = new Audit("NULL",$user->getUsername(), "People", $person->getID(), $person->toJSON(), "NULL", "SELECT-FOUND", "now");
        } else {
            echo "NULL";
            // create audit
            $audit = new Audit("NULL",$user->getUsername(), "People", "NULL", "NULL", '{"personLicence":"'.$personLicence.'"}', "SELECT-EMPTY", "now");
        }
        $auditDB->insertAudit($audit);
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
            echo "false,length should be >0 and <=20";
        }
    }
    function isFnameValid($fname) {
        if (strlen($fname)<=25 && strlen($fname)>0) {
            echo "true,firstname is valid";
        } else {
            echo "false,length should be >0 and <=25";
        }
    }
    function isLnameValid($lname) {
        if (strlen($lname)<=25 && strlen($lname)>0) {
            echo "true,lastname is valid";
        } else {
            echo "false,length should be >0 and <=25";
        }
    }
    function isaddressValid($address) {
        if (strlen($address)<=50 && strlen($address)>0) {
            echo "true,address is valid";
        } else {
            echo "false,length should be >0 and <=50";
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
            echo "false,the length should be 7";
        }
    } function createNewVehicleWithOwner($vehicleLicence,$vehicleColour,$vehicleMake,$vehicleModel,
    $personLicence,$personFirstName,$personLastName,$personAddress,$personDOB,$user,$conn) {
        // get data of vehicle and person, create it , used for creating new vehicle with an exist or non exist owner.
        $newVehicle = new Vehicle($vehicleLicence,$vehicleColour,$vehicleMake,$vehicleModel,"NULL");
        $person = new Person("NULL",$personLicence,$personAddress,$personDOB,$personFirstName." ".$personLastName,"NULL");
        $ownershipDB = new OwnershipDB($user,$conn);
        $peopleDB = new PeopleDB($user, $conn);
        $auditDB = new AuditDB($user, $conn);
        // check if an owner in the database has the same name,address,dob as the $person but has different licence
        if ($peopleDB->isPersonDetailInDB($person)) {
            $personInDB = $peopleDB->getPersonByDetail($person);
            if ($personInDB->licence==NULL || $personInDB->licence=="NULL") {
                echo '{"state":"failed", "reason":"person already in the database but does not have a licence in database"}';
                return;
            } elseif ($personInDB->licence != $person->licence) {
                echo '{"state":"failed", "reason":"person already in the database but the licence number is different with the driving licence number that you entered"}';
                return;
            }
        }
        // check if the owner licence is in database, and check if the data in database of this person is the same as the new person data.
        if ($peopleDB->isPersonLicenceInDB($personLicence)) {
            $personInDB=$peopleDB->getPersonByLicence($personLicence);
            if ($personInDB->name != $person->name) {
                '{"state":"failed", "reason":"person with the licence already in the database but the name you typed in different with the name of the person in the database."}';
                return;
            }
        }

        // check if the person is new before insert the person. (Do this because I don't want to modify the return value of owenrshipDB->getOwnershipByLicence method.)
        $isPersonNew = $peopleDB->isPersonDetailInDB($person)==true ? false : true;

        // insert the ownership as well as the vehicle, maybe the person.
        $result = $ownershipDB->insertOwnershipWithNewVehicle($newVehicle,$person,$conn);
        
        
        
        
        if (!isset($result["state"])) {
            echo '{"state":"error", "reason":"missing information in insertOwnershipWithNewVehicle()"}';
            print_r($result); // debugging
        } elseif ($result["state"]=="failed") {
                echo '{"state":"'.$result["state"].'","reason":"'.$result["reason"].'"}';
        } elseif ($result["state"]=="success") {
            // set id to objects, and get ownership object.
            $person->ID = strval($result["personID"]);
            $newVehicle->ID = strval($result["vehicleID"]);
            $ownership = new Ownership($newVehicle, $person, strval($result["newOwnershipID"]));
            
            // add audit trail(INSERT) for person if the person is new
            // echo "<hr>\$isPersonNew = ".strval($isPersonNew)."<hr>"; // debugging
            if ($isPersonNew==true) {
                $personAudit = new Audit("NULL", $user->getUsername(), "People", $person->ID, "NULL", $person->toJSON(), "INSERT-SUCCESS", "now");
            } elseif($isPersonNew==false) {
                $personAudit = new Audit("NULL", $user->getUsername(), "People", $person->ID, $person->toJSON(), "NULL",  "REFERENCE-INSERT", "now");
            }
            $auditDB->insertAudit($personAudit);
            $auditTime = $personAudit->auditTime;

            // add audit trail for the new vehicle
            $vehicleAudit = new Audit("NULL", $user->getUsername(), "Vehicles", $newVehicle->ID, "NULL", $newVehicle->toJSON(), "INSERT-SUCCESS", $auditTime);
            $auditDB->insertAudit($vehicleAudit);
            
            // add audit trail for the vehicle's being referenced.
            $vehicleAudit = new Audit("NULL", $user->getUsername(), "Vehicles", $newVehicle->ID,  $newVehicle->toJSON(), "NULL", "REFERENCE-INSERT", $auditTime);
            $auditDB->insertAudit($vehicleAudit);

            // add audit trail for the new ownership
            $ownershipAudit = new Audit("NULL", $user->getUsername(), "Ownership", $ownership->ID, "NULL", $ownership->toJSON(), "INSERT-SUCCESS", $auditTime);
            $auditDB->insertAudit($ownershipAudit);


                echo '{"state":"'.$result["state"]
                    .'","newOwnershipID":"'.$result["newOwnershipID"]
                    .'","vehicleID":"'.$result["vehicleID"]
                    .'","personID":"'.$result["personID"]
                    .'"}'; 
        } else {
            echo '{"state":"error", "reason":"unexpected data in the state field"}';
            print_r($result); // debugging
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
    require("../reuse/_audit.php");
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