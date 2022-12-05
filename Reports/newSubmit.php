<?php
try{
?>

<?php // handle not login error
        session_start();
        require("../Accounts/_account.php");// there is a User class
        require("../head.php");
        $user = new User();
        if (!$user->isLoggedIn()) {
            header("location: ../Accounts/notLoginError.html"); // check if logged in
        }
    ?>
<?php 
    // check if mandatory fields is empty
    if (empty($_POST)) {
        echo "error, empty post";
        die();
    } else {
        require("../Vehicles/_ownership.php");
        require("../Vehicles/_vehicles.php");
        require("../reuse/_dbConnect.php");
        require("../People/_people.php");
        $conn = connectDB();
        $ownershipDB = new OwnershipDB($user->getUsername(), $conn);
        $vehicleDB = new VehiclesDB($user->getUsername(), $conn);
        $peopleDB = new PeopleDB($user->getUsername(), $conn);
        

        function getReportType($acceptableForms, $post) {
            // given an array of $acceptableForms, and $post information,
            // return the type of post if it matches one of the formats.
            // return "invalid form" if it matches no formats

            // check each acceptable form
            foreach ($acceptableForms as $acceptableForm) {
                $reportType = $acceptableForm["reportType"];
                $format = $acceptableForm["format"];

                // $fieldName=>$validationResult; $validationResult: true if post satisfy the constraint of , else false.
                $formatValidationResult = []; 

                // check each field, push result into $formaValidationResult
                foreach($format as $fieldName=>$fieldConstrain) { 
                    if (empty($_POST[$fieldName])) {
                        $fieldPostType = "false";
                    } else {
                        $fieldPostType = "true";
                    }
                    if ($fieldConstrain == "true" && $fieldPostType == "true") {
                        $formatValidationResult[$fieldName] = true;
                    } elseif ($fieldConstrain == "false" && $fieldPostType == "false") {
                        $formatValidationResult[$fieldName] = true;
                    } elseif ($fieldConstrain == "optional") {
                        $formatValidationResult[$fieldName] = true;
                    } else {
                        $formatValidationResult[$fieldName] = false;
                    }    
                }

                // check $formatValidationResult, if all true, return the report type of this accpetable form.
                $allFieldSatisfy = true;
                foreach($formatValidationResult as $key=>$value) {
                    if ($value==false) {
                        $allFieldSatisfy = false;
                        // break this inner loop.(optional)
                    } else {
                        // keep looping
                    }
                }

                if ($allFieldSatisfy) {
                    return $acceptableForm["reportType"];
                } else {
                    // check next one if exists.
                }
            }
            return "invalid form";
        }
        function isVehicleFormValid($post) {
            // assume there is a vehicle form
            // return an associative array: 
            // ["allValid"=>true/false, "detail"=>["vehicleField1"=>"valid/invalid hint", "vehicleField2"=>"valid/invalid hint", ...]]
            // Return True if the format of all the field of vehicle is valid.
            // Return An associative array whose key is field name of vehicle form, and value is "correct"  

            return ["allValid"=>true];
        }
        function isOwnerFormValid($post) {
            // assume there is a owner form
            // return an associative array:
            // ["allValid"=>true/false, "detail"=>["ownerField1"=>"valid/invalid hint", "ownerField2"=>"valid/invalid hint", ...]]
            // Return True if the format of all the field of owner is valid.
            // Return An associative array whose key is field name of owner form, and value is "correct" 

            return ["allValid"=>true];
        }

        // check report type, and set $hasVehicleForm, $hasOwnerForm, $hasOffenderForm to be true/false;
        require("acceptableForms.php");
        $acceptableForms = [$acceptableForm1,$acceptableForm2,$acceptableForm3,$acceptableForm4,$acceptableForm5];
        $reportType = getReportType($acceptableForms, $_POST);
        echo "form type:<br>".$reportType."<hr>";
        $hasVehicleForm = null;
        $hasOwnerForm = null;
        $hasOffenderForm = null;
        if ($reportType == "known vehicle only") {
           $hasVehicleForm = true;
           $hasOwnerForm = false;
           $hasOffenderForm = false;
        } elseif ($reportType == "known vehicle and offender") {
            $hasVehicleForm = true;
            $hasOwnerForm = false;
            $hasOffenderForm = true;
        } elseif ($reportType == "known vehicle and owner and offender") {
            $hasVehicleForm = true;
            $hasOwnerForm = true;
            $hasOffenderForm = true;
        } elseif ($reportType == "known vehicle and owner") {
            $hasVehicleForm = true;
            $hasOwnerForm = true;
            $hasOffenderForm = false;
        } elseif ($reportType == "known offender only") {
            $hasVehicleForm = false;
            $hasOwnerForm = false;
            $hasOffenderForm = true;
        }


        // processing vehicle form. If the all the data in the form that is related to vehicle is valid,
        // These code would set a $vehicleID, either NULL, new one, or old one (from db);
        // These code would also set a $isVehicleNew:

        // no vehicle form, use "NULL" as id
        $vehicleID = "NULL";
        if ($hasVehicleForm==false) {
            $vehicleID = "NULL"; // just make it more explicit :)
            // case that the fields of vehicle form is invalid (function is not implemented yet, always valid) : give error feedback
        } elseif ($hasVehicleForm && isVehicleFormValid($_POST)['allValid']==false) {
            echo "<hr>";
            echo "vehicle Information is not valid:";
            $hint = isVehicleFormValid($_POST)['detail'];
            print_r($hint);
            echo "<hr>";
            mysqli_close($conn);
            die();

            // case that the fields of vehicle form is valid: insert and get id if the vehicle is new, get existed id if the vehicle is in db.
        } elseif ($hasVehicleForm && isVehicleFormValid($_POST)['allValid']==true) { // boolean expression could be simplified, but current one is more explicit
            $isVehicleNew = !$vehicleDB->isVehicleExists($_POST["vehicleLicence"]);
            // vehicle is new
            if ($isVehicleNew) {
                echo "<hr>vehicle is new<hr>iserting this new vehicle into database...<hr>";
                $newVehicle = new Vehicle($_POST["vehicleLicence"], $_POST["vehicleColour"], $_POST["vehicleMake"], $_POST["vehicleModel"], $conn);
                $vehicleID = $vehicleDB->insertNewVehicle($newVehicle);
                $newVehicleFromDB = $vehicleDB->getVehiclesByLicence($newVehicle->getLicence())[0];
                echo "<hr>new vehicle inserted(data from database): <br>".$newVehicleFromDB->renderHtmlTable();
            
            // vehicle is not new
            } else {
                echo "<hr>vehicle is old<hr>";
                
                $oldVehicleArray = $vehicleDB->getVehiclesByLicence($_POST["vehicleLicence"]);
                
                // throw some exception for debugging, they should never happen.
                if (empty($oldVehicleArray)) {
                    throw new Exception("Error: Vehicle Should exists in the database, but get now result by selecting the vehicleLicence!");
                } elseif (count($oldVehicleArray) > 1) {
                    throw new Exception("Error: One vehicle licence appeared twice in the vehicle table!");
                } else {
                    //get the vehicle from database.
                    $oldVehicleFromDB = $oldVehicleArray[0];
                    echo "old vehicle from the database:<br>".$oldVehicleFromDB->renderHtmlTable()."<hr>";
                }
                // the vehicle object created use post data.
                $oldVehicleFromForm = new Vehicle($_POST["vehicleLicence"], $_POST["vehicleColour"], $_POST["vehicleMake"], $_POST["vehicleModel"], null);
                    echo "old vehicle from the user input:<br>".$oldVehicleFromForm->renderHtmlTable()."<hr>";
                // check if vehicle in the db which share the same plate number has the same other information.
                $oldVehicleSame = true;
                if ($oldVehicleFromForm->getColour()!=$oldVehicleFromDB->getColour()) {
                    echo "although vehicle licence is in the database, but the colour is not the same with the vehicle with the licence in the database<hr>";
                    $oldVehicleSame = false;
                }
                if ($oldVehicleFromForm->getMake()!=$oldVehicleFromDB->getMake()) {
                    echo "although vehicle licence is in the database, but the make is not the same with the vehicle with the licence in the database<hr>";
                    $oldVehicleSame = false;
                }
                if ($oldVehicleFromForm->getModel()!=$oldVehicleFromDB->getModel()) {
                    echo "although vehicle licence is in the database, but the model is not the same with the vehicle with the licence in the database<hr>";
                    $oldVehicleSame = false;
                }
                if ($oldVehicleSame==false) {
                    echo "Please Check your vehicle data<hr>";
                    die();
                } else {
                    echo "GOOD GOOD GOOD for the vehicle form!<hr>";
                    $vehicleID = $oldVehicleFromDB->getID();
                    // return something if in future refactor these code into a function.
                }
            }
        }
        // if ($isVehicleNew) {
        //     echo "new";
        // } else {
        //     echo "old";
        // } // just tried if I can access $isVehicleNew, and yes I can (YEAH!!)
        
        echo "<hr>---------------------Processing vehicle form done---------------------<hr>";


        // TODO:
        // processing the {owner form} -> {$ownerID, database change}
        $ownerID = "NULL";
        if ($hasOwnerForm==false) {
            $ownerID = "NULL";            

            // case that the fields of owner form is invalid (function is not implemented yet, always valid) : give error feedback
        } elseif($hasOwnerForm && isOwnerFormValid($_POST)['allValid']==false) {
            echo "<hr>";
            echo "owner Information is not valid:";
            $hint = isOwnerFormValid($_POST)['detail'];
            print_r($hint);
            echo "<hr>";
            mysqli_close($conn);
            die();
            
            // valid:
        } elseif($hasOwnerForm && isOwnerFormValid($_POST)['allValid']==true) {
            // sudo-code:
            // case 1: the owner has a licence:
                // search licence
                    // if licence in db (not a new owner):
                        // if form not match the db:
                            // feedback these difference and die();
                        // if form match the db:
                            // get the owner id
                            // set the owner id on $ownerID;
                    // if licence not in db (new):
                        // insert this owner, get owner id
                        // set the owner id on $ownerID;
            // case 2: the owner doesn't have a licence:
                // if there are given name, dob, address in the form:
                    // use these 3 data as unique key to search the database
                    // if match one:
                        // get the id of the data in database
                        // set the owner id on $ownerID
                    // else:
                        // insert new data with name, dob, address
                        // get id of the newly-inserted owner
                        // set the owner id on $ownerID
                // else:
                    // feedback these missing field and die();
            
            // implementation:

            // case1: has licence info
            if (!empty($_POST['ownerLicence'])) {
                // licence in db
                if ($peopleDB->isPersonLicenceInDB($_POST['ownerLicence'])){
                    echo "owner is in the database";

                    $ownerFromDB = $peopleDB->getPersonByLicence($_POST['ownerLicence']);
                    echo "owner from database:<br>".$ownerFromDB->renderRow()."<hr>";
                    // assume ownerFromDB is not empty
                    if (empty($ownerFromDB)) {
                        throw new Exception("vehicle licence is not in database, but it should be in the database as it was checked by $peopleDB->isPersonLicenceInDB");
                    }
                    $ownerFromForm = new Person("NULL", $_POST['ownerLicence'], $_POST['ownerAddress'], 
                                                $_POST["ownerDOB"], $_POST["ownerFirstName"]." ".$_POST["ownerLastName"], "NULL");
                    echo "owner from the form:<br>".$ownerFromForm->renderRow()."<hr>";

                    // get error state, and give feed back.
                    $oldOwnerSame = true;
                    if ($ownerFromDB->getLicence()!=$ownerFromForm->getLicence()) {
                        echo "although owner licence is in the database, but the licence is not the same with the owner with the licence in the database<hr>";
                        $oldOwnerSame = false;
                    }
                    if ($ownerFromDB->getAddress()!=$ownerFromForm->getAddress()) {
                        echo "although owner licence is in the database, but the address is not the same with the owner with the address in the database<hr>";
                        $oldOwnerSame = false;
                    }
                    if ($ownerFromDB->getDOB()!=$ownerFromForm->getDOB()) {
                        echo "although owner licence is in the database, but the DOB is not the same with the owner with the DOB in the database<hr>";
                        $oldOwnerSame = false;
                    }
                    if ($ownerFromDB->getFirstName()!=$ownerFromForm->getFirstName()) {
                        echo "although owner licence is in the database, but the first name is not the same with the owner with the first name in the database<hr>";
                        $oldOwnerSame = false;
                    }
                    if ($ownerFromDB->getLastName()!=$ownerFromForm->getLastName()) {
                        echo "although owner licence is in the database, but the last name is not the same with the owner with the last name in the database<hr>";
                        $oldOwnerSame = false;
                    }
                    
                    if ($oldOwnerSame) {
                        // if form match the db:
                            // get the owner id
                            // set the owner id on $ownerID;
                        echo "GOOD GOOD GOOD for the owner form!<hr>";
                        $ownerID = $ownerFromDB->getID();
                    } else {
                        // if form not match the db:
                            // feedback these difference and die():
                        echo "Please Check your owner data<hr>";
                        die();
                    }
                
                    // licence not in db
                } else {
                    // owner is new, then insert this owner into the database
                    $ownerFromForm = new Person("NULL", $_POST['ownerLicence'], $_POST['ownerAddress'], 
                                                $_POST["ownerDOB"], $_POST["ownerFirstName"]." ".$_POST["ownerLastName"], "NULL");
                    
                    $ownerID = $peopleDB->insertNewPerson($ownerFromForm); // id set
                    $newOwnerFromDB = $peopleDB->getPersonByLicence($ownerFromForm->getLicence());
                    echo "new owner:<br>".$newOwnerFromDB->renderRow()."<hr>";
                }
                
                
            } 
            // case 2: has no licence info
            else {

                // if has empty data of these, feedback error.
                if(!empty($_POST["ownerAddress"]) || !empty($_POST["ownerFirstName"]) || !empty($_POST["ownerLastName"]) || !empty($_POST["ownerDOB"])) {
                    echo "missed some data of ownerAddress, ownerFirstName, ownerLastName, ownerDOB<hr>";
                    die();
                } 
                // else: name, dob, address is given
                else {
                    $ownerWithoutLicenceFromForm = new Person("NULL", "NULL",$_POST["ownerAddress"],$_POST["ownerDOB"],$_POST["ownerFirstName"]." ".$_POST["ownerLastName"], "NULL");
                    echo "owner without licence from the form: <br>".$ownerWithoutLicenceFromForm->renderRow()."<hr>";

                    // match one
                    if ($peopleDB->isPersonDetailInDB($ownerWithoutLicenceFromForm)) {
                        $ownerWithoutLicenceFromDB = $peopleDB->getPersonByDetail($ownerWithoutLicenceFromForm);
                        // no need to compare the db person and the form person.
                        $ownerID = $ownerWithoutLicenceFromDB->getID(); // set id
                        echo "owner without licence from the database: <br>".$ownerWithoutLicenceFromDB->renderRow()."<hr>";
                        
                    }
                    // not match one
                    else {
                        // insert new data with name, dob, address
                        // get id of the newly-inserted owner
                        // set the owner id on $ownerID
                        $ownerID = $peopleDB->insertNewPerson($ownerWithoutLicenceFromForm);
                        $ownerWithoutLicenceFromDB = $peopleDB->getPersonByDetail($ownerWithoutLicenceFromForm);

                        echo "\$ownerID: ".$ownerID."<hr>";
                        echo "ownerWithoutLicenceFromForm: ".$ownerWithoutLicenceFromForm->getID()."<hr>";
                        echo "ownerWithoutLicenceFromDB: ".$ownerWithoutLicenceFromDB->getID()."<hr>";
                        echo "owner without licence from the database: <br>".$ownerWithoutLicenceFromDB->renderRow()."<hr>";
                    }
                }
            }
        } 

        echo "<hr>---------------------Processing owner form done---------------------<hr>";
        // to do
        // test owner form
        // processing the {offender form} -> {$offenderID, database change}
        // using {vehicleID, ownerID} -> {$ownershipID, database change} 
        // using {ownership, offenderID, report general data form} -> {reportID, database change}
    }

} catch (Exception $error) {
    echo '<div class="feedback-yellow"><div class="feedback-text-line">Error: '.$error->getMessage().'</div></div>';
    // header("location: ../error.php?errorMessage=".$error->getMessage());
}   
?>