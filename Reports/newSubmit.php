<?php
try{
    $messages = [];
?>

<?php // handle not login error

        session_start();
        require("../Accounts/_account.php");// there is a User class
        $pageTitle = "submit report";
        require("../reuse/head.php");
        $user = new User();
        if (!$user->isLoggedIn()) {
            header("location: ../Accounts/notLoginError.html"); // check if logged in
        }
    ?>
<?php 
    // check if mandatory fields is empty
    if (empty($_POST)) {
        throw new Exception ("Empty post");
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
                    // do nothing, go to next loop of the outer loop, check next one if exists.
                }
            }
            return "invalid form";
        }
        function isVehicleFormValid($post,) {
            // assume there is a vehicle form
            // return an associative array: 
            //  ["allValid"=>true/false, "message"=>["feedback message1", "feedback message2"...]]
            // "allValid" is True and "message" is empty, if the format of all the field of vehicle is valid
            // "allValid" is False and "message" contains an array of feedback messages
            $messages = array();
            if (strlen($_POST["vehicleLicence"])!=7) {
                array_push($messages,"Invalid vehicle licence length!<br>length should be 7");
            }
            if (strlen($_POST["vehicleColour"])>20 || strlen($_POST["vehicleColour"])<1) {
                array_push($messages,"Invalid vehicle colour length!<br>length should be 1 to 20");
            }
            if (strlen($_POST["vehicleMake"])>20 || strlen($_POST["vehicleMake"])<1) {
                array_push($messages,"Invalid vehicle make length!<br>length should be 1 to 20");
            }
            if (strlen($_POST["vehicleModel"])>20 || strlen($_POST["vehicleModel"])<1) {
                array_push($messages,"Invalid vehicle model length!<br>length should be 1 to 20");
            }
            if (empty($messages)) {
                return ["allValid"=>true];
            } else {
                return ["allValid"=>false, "messages"=>$messages];
            }
        }
        function isOwnerFormValid($post) {
            // assume there is a owner form
            // return an associative array:
            // ["allValid"=>true/false, "detail"=>["ownerField1"=>"valid/invalid hint", "ownerField2"=>"valid/invalid hint", ...]]
            // Return True if the format of all the field of owner is valid.
            // Return An associative array whose key is field name of owner form, and value is "correct" 

            return ["allValid"=>true];
        }

    // DONE: check report type, and set $hasVehicleForm, $hasOwnerForm, $hasOffenderForm to be true/false;
        require("acceptableForms.php");
        $acceptableForms = [$acceptableForm1,$acceptableForm2,$acceptableForm3,$acceptableForm4,$acceptableForm5];
        $reportType = getReportType($acceptableForms, $_POST);
        echo "form type:<br>".$reportType."<hr>"; // DEBUGGING
        $hasVehicleForm = null;
        $hasOwnerForm = null;
        $hasOffenderForm = null;
        if ($reportType == "invalid form") {
            throw new Exception("Invalid Form:<br> You should at least enter the <b>licence, colour, make, model</b> of the vehicle<br>"
            ."OR the <b>name, dob, address</b> of the offender!"
            ."<br>The Owner information can be all empty; or at least <b>name, dob, address</b> should be all given!"
            ."<br>The driving licence number of offender and owner can be empty if they don't have a driving licence");
            die();
        }
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


    // DONE: processing vehicle form. {vehicle form} -> {vehicleID, database change}
        // If the all the data in the form that is related to vehicle is valid,
        // These code would set a $vehicleID, either NULL, new one, or old one (from db);
        // These code would also set a $isVehicleNew:
        // These code would insert a new vehicle if there is a vehicle in the post form and valid and new.

        // no vehicle form, use "NULL" as id
        $vehicleID = "NULL";
        if ($hasVehicleForm==false) {
            $vehicleID = "NULL"; // just make it more explicit :)
            // case that the fields of vehicle form is invalid (function is not implemented yet, always valid) : give error feedback
        } elseif ($hasVehicleForm && isVehicleFormValid($_POST)['allValid']!=true) {
            // echo "vehicle Information is not valid:";// debug
            $messages = isVehicleFormValid($_POST)['messages'];
            // print_r($messages); // debug
            mysqli_close($conn);
            throw new Exception("check the \$messages");
            die();
            
            // case that the fields of vehicle form is valid: insert and get id if the vehicle is new, get existed id if the vehicle is in db.
        } elseif ($hasVehicleForm && isVehicleFormValid($_POST)['allValid']==true) { // boolean expression could be simplified, but current one is more explicit
            $isVehicleNew = !$vehicleDB->isVehicleExists($_POST["vehicleLicence"]);
            // echo "flag1"; // debugging
            // echo $isVehicleNew; // debugging
            // vehicle is new
            if ($isVehicleNew) {
                echo "<hr>vehicle is new<hr>iserting this new vehicle into database...<hr>";
                $newVehicle = new Vehicle($_POST["vehicleLicence"], $_POST["vehicleColour"], $_POST["vehicleMake"], $_POST["vehicleModel"], $conn);
                $vehicleID = $vehicleDB->insertNewVehicle($newVehicle);
                $newVehicleFromDB = $vehicleDB->getVehiclesByLicence($newVehicle->getLicence())[0];
                echo "<hr>new vehicle inserted(data from database): <br>".$newVehicleFromDB->renderHtmlTable();
            
            // vehicle is not new
            } else {
                // echo "<hr>vehicle is old<hr>"; // debugging
                
                $oldVehicleArray = $vehicleDB->getVehiclesByLicence($_POST["vehicleLicence"]);
                
                // throw some exception for debugging, they should never happen.
                if (empty($oldVehicleArray)) {
                    throw new Exception("Vehicle Should exists in the database, but get no result by selecting the vehicleLicence!");
                } elseif (count($oldVehicleArray) > 1) {
                    throw new Exception("One vehicle licence appeared twice in the vehicle table!");
                } else {
                    //get the vehicle from database.
                    $oldVehicleFromDB = $oldVehicleArray[0];
                    // echo "old vehicle from the database:<br>".$oldVehicleFromDB->renderHtmlTable()."<hr>"; // debugging
                }
                // the vehicle object created use post data.
                $oldVehicleFromForm = new Vehicle($_POST["vehicleLicence"], $_POST["vehicleColour"], $_POST["vehicleMake"], $_POST["vehicleModel"], null);
                    
                    // echo "old vehicle from the user input:<br>".$oldVehicleFromForm->renderHtmlTable()."<hr>"; // debugging
                // check if vehicle in the db which share the same plate number has the same other information.
                $oldVehicleSame = true;
                if ($oldVehicleFromForm->getColour()!=$oldVehicleFromDB->getColour()) {
                    // echo "Although vehicle licence is in the database, but the colour is not the same with the vehicle with the licence in the database<hr>"; //feedback
                    array_push($messages, "Although vehicle licence is in the database, but the colour is not the same with the vehicle with the licence in the database");
                    $oldVehicleSame = false;
                }
                if ($oldVehicleFromForm->getMake()!=$oldVehicleFromDB->getMake()) {
                    // echo "Although vehicle licence is in the database, but the make is not the same with the vehicle with the licence in the database<hr>";//feedback
                    array_push($messages, "Although vehicle licence is in the database, but the make is not the same with the vehicle with the licence in the database");
                    $oldVehicleSame = false;
                }
                if ($oldVehicleFromForm->getModel()!=$oldVehicleFromDB->getModel()) {
                    // echo "Although vehicle licence is in the database, but the model is not the same with the vehicle with the licence in the database<hr>";//feedback
                    array_push($messages, "Although vehicle licence is in the database, but the model is not the same with the vehicle with the licence in the database");
                    $oldVehicleSame = false;
                }
                if ($oldVehicleSame==false) {
                    echo "Please Check your vehicle data<hr>";//feedback
                    throw new Exception("check \$messages");
                    die();
                } else {
                    $vehicleID = $oldVehicleFromDB->getID();
                    // echo "GOOD GOOD GOOD for the vehicle form!<br>vehicle id=$vehicleID<hr>"; // debugging
                    // return something if in future refactor these code into a function.
                }
            }
        }
        // if ($isVehicleNew) {
        //     echo "new";
        // } else {
        //     echo "old";
        // } // just tried if I can access $isVehicleNew, and yes I can (YEAH!!)
        
        echo "<hr>---------------------Processing vehicle form done, VehicleID:".$vehicleID."---------------------<br>"; // debugging
        if (!isset($isVehicleNew)) {
            echo "No vehicle involved.<hr>";
        } elseif ($isVehicleNew) {
            echo "New vehicle created.<hr>";
        } else {
            echo "There is no new vehicle created.<hr>";
        }

    // DONE: processing the {owner form} -> {$ownerID, database change}
        $ownerID = "NULL";
        if ($hasOwnerForm==false) {
            $ownerID = "NULL";            
            // case that the fields of owner form is invalid (function is not implemented yet, always valid) : give error feedback
        } elseif($hasOwnerForm && isOwnerFormValid($_POST)['allValid']==false) {
            $messages = isOwnerFormValid($_POST)['messages'];
            // echo "<hr>";
            // echo "owner Information is not valid:";
            mysqli_close($conn);
            throw new Exception("check the \$messages");
            die();
            
            // valid:
        } elseif($hasOwnerForm && isOwnerFormValid($_POST)['allValid']==true) {
            // sudo-code:
            // case 1: the owner has a licence:
                // search licence
                    // if licence in db (not a new owner):
                        // if form detail not match the db:
                            // feedback these difference and die();
                        // if form detial match the db:
                            // get the owner id
                            // set the owner id on $ownerID;
                    // if licence not in db (new):
                        // if the combination of (name, address, dob) match some data in the database, 
                            // feedback this problem.
                            // die();
                        // insert this owner, get owner id
                        // set the owner id on $ownerID;
            // case 2: the owner doesn't have a licence:
                // if there are given name, dob, address in the form:
                    // use these 3 data as unique key to search the database
                    // if match one:
                        // if the one has licence:
                            // feed back this problem and die()
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
                    // echo "owner is in the database<hr>";// debugging
                    $isOwnerNew = false;

                    $ownerFromDB = $peopleDB->getPersonByLicence($_POST['ownerLicence']);
                    // echo "<hr>owner from database:<br>".$ownerFromDB->renderRow(true)."<hr>"; //debugging
                    // assume ownerFromDB is not empty
                    if (empty($ownerFromDB)) {
                        throw new Exception("vehicle licence is not in database, but it should be in the database as it was checked by $peopleDB->isPersonLicenceInDB");
                    }
                    $ownerFromForm = new Person("NULL", $_POST['ownerLicence'], $_POST['ownerAddress'], 
                                                $_POST["ownerDOB"], $_POST["ownerFirstName"]." ".$_POST["ownerLastName"], "NULL");
                    // echo "<hr>owner from the form:<br>".$ownerFromForm->renderRow(true)."<hr>"; //debugging

                    // get error state, and give feed back.
                    $oldOwnerSame = true;
                    if ($ownerFromDB->getLicence()!=$ownerFromForm->getLicence()) {
                        array_push($messages, "although owner licence is in the database, but the licence is not the same with the owner with the licence in the database");
                        // echo "although owner licence is in the database, but the licence is not the same with the owner with the licence in the database<hr>"; //debugging
                        $oldOwnerSame = false;
                    }
                    if ($ownerFromDB->getAddress()!=$ownerFromForm->getAddress()) {
                        array_push($messages, "although owner licence is in the database, but the address is not the same with the owner with the address in the database");
                        // echo "although owner licence is in the database, but the address is not the same with the owner with the address in the database<hr>"; //debugging
                        $oldOwnerSame = false;
                    }
                    if ($ownerFromDB->getDOB()!=$ownerFromForm->getDOB()) {
                        array_push($messages, "although owner licence is in the database, but the DOB is not the same with the owner with the DOB in the database");
                        // echo "although owner licence is in the database, but the DOB is not the same with the owner with the DOB in the database<hr>"; //debugging
                        $oldOwnerSame = false;
                    }
                    if ($ownerFromDB->getFirstName()!=$ownerFromForm->getFirstName()) {
                        array_push($messages, "although owner licence is in the database, but the first name is not the same with the owner with the first name in the database");
                        // echo "although owner licence is in the database, but the first name is not the same with the owner with the first name in the database<hr>"; //debugging
                        $oldOwnerSame = false;
                    }
                    if ($ownerFromDB->getLastName()!=$ownerFromForm->getLastName()) {
                        array_push($messages, "although owner licence is in the database, but the last name is not the same with the owner with the last name in the database");
                        // echo "although owner licence is in the database, but the last name is not the same with the owner with the last name in the database<hr>"; //debugging
                        $oldOwnerSame = false;
                    }
                    
                    if ($oldOwnerSame) {
                        // if the form with licence match the db:
                            // get the owner id
                            // set the owner id on $ownerID;
                        // echo "GOOD GOOD GOOD for the owner form!<br>"; // debugging
                        $ownerID = $ownerFromDB->getID();
                        $isOwnerNew = false;
                        // echo "Owner already in database:owner id=".$ownerID; // debugging
                    } else {
                        // if the form with licence not match the db:
                            // feedback these difference and die():
                        
                        throw new Exception("check \$messages");
                                // echo "The owner information you entered is not the same as the one in the database.<br> Please Check your owner data<hr>"; // debugging
                        die();
                    }
                
                    // licence not in db
                } else {
                    $ownerFromForm = new Person("NULL", $_POST['ownerLicence'], $_POST['ownerAddress'], 
                                                $_POST["ownerDOB"], $_POST["ownerFirstName"]." ".$_POST["ownerLastName"], "NULL");

                    // if the combination of (name, dob, address) matches data, feedback this error.
                    if ($peopleDB->isPersonDetailInDB($ownerFromForm)) {
                        echo "<hr><b>The owner you typed is already in database, please enter correct information: </b><br>";
                        echo "your data:".$ownerFromForm->renderRow(true)."<br>";
                        $ownerFromDB = $peopleDB->getPersonByDetail($ownerFromForm);
                        echo "database data:".$ownerFromDB->renderRow(true)."<br>";
                        die();
                    }

                    // owner is new, then insert this owner into the database
                    $isOwnerNew = true;
                    
                    $ownerID = $peopleDB->insertNewPerson($ownerFromForm); // id set
                    $newOwnerFromDB = $peopleDB->getPersonByLicence($ownerFromForm->getLicence());
                    // echo "new owner:<br>".$newOwnerFromDB->renderRow(true)."<hr>"; // debugging
                }
                
                
            } 
            // case 2: has no licence info
            else {

                // if has empty data of these, feedback error.
                if(empty($_POST["ownerAddress"]) || empty($_POST["ownerFirstName"]) || empty($_POST["ownerLastName"]) || empty($_POST["ownerDOB"])) {
                    throw new Exception("missed some data of ownerAddress or ownerFirstName or ownerLastName or ownerDOB<hr>");
                    // echo "missed some data of ownerAddress, ownerFirstName, ownerLastName, ownerDOB<hr>";
                    die();
                } 
                // else: name, dob, address is given
                else {
                    $ownerWithoutLicenceFromForm = new Person("NULL", "NULL",$_POST["ownerAddress"],$_POST["ownerDOB"],$_POST["ownerFirstName"]." ".$_POST["ownerLastName"], "NULL");
                    // echo "owner without licence from the form: <br>".$ownerWithoutLicenceFromForm->renderRow(true)."<hr>"; // debug

                    // match one
                    if ($peopleDB->isPersonDetailInDB($ownerWithoutLicenceFromForm)) {
                        $ownerWithoutLicenceFromDB = $peopleDB->getPersonByDetail($ownerWithoutLicenceFromForm);

                        // check if the owner from db has no licence, if it has licence, feedback this error and die()
                        $ownerID = $ownerWithoutLicenceFromDB->getID(); // set id
                        if (empty($ownerWithoutLicenceFromDB->getLicence())) {
                            // echo "GOOD, owner licence from db is empty:'".$ownerWithoutLicenceFromDB->getLicence()."'<hr>"; // debugging
                        } else {
                            // the one in db have a licence, error.
                            throw new Exception("<br>The owner you submit:".$ownerWithoutLicenceFromForm->renderRow(true).
                            "is already in the database, and has a driving licence: ".$ownerWithoutLicenceFromDB->getLicence().
                            "<br>Please fill the correct owner information and create the report again.</b>");
                            // echo "<b>Error: owner licence from db is not empty:</b>'".$ownerWithoutLicenceFromDB->getLicence()."'<hr>";
                            die();
                        }

                        // echo "<b>Uses exists owner in database:</b> <br>".$ownerWithoutLicenceFromDB->renderRow(true)."<hr>"; // debugging
                        $isOwnerNew = false;
                    }
                    // not match one
                    else {
                        // insert new data with name, dob, address
                        // get id of the newly-inserted owner
                        // set the owner id on $ownerID
                        $ownerID = $peopleDB->insertNewPerson($ownerWithoutLicenceFromForm);
                        $ownerWithoutLicenceFromDB = $peopleDB->getPersonByDetail($ownerWithoutLicenceFromForm);
                        
                        // echo "\$ownerID: ".$ownerID."<hr>";
                        // echo "ownerWithoutLicenceFromForm id: ".$ownerWithoutLicenceFromForm->getID()."<hr>"; // debugging 
                        // echo "ownerWithoutLicenceFromDB id: ".$ownerWithoutLicenceFromDB->getID()."<hr>"; // debugging
                        // echo "<b>owner without licence is new, created:</b> <br>".$ownerWithoutLicenceFromDB->renderRow(true)."<hr>"; // debugging
                        $isOwnerNew = true;
                    }
                }
            }
        } 
        
        echo "<hr>---------------------Processing owner form done, OwnerID:".$ownerID."---------------------<br>";
        if (!isset($isOwnerNew)) {
            echo "No owner involved.<hr>";
        } elseif ($isOwnerNew) {
            echo "New owner created.<hr>";
        } else {
            echo "There is no new owner created.<hr>";
        }
        // test owner form (done)
        


    // TODO: processing the {offender form} -> {$offenderID, database change} (not done)
        $offenderID = $ownerID;

    // DONE: processing {vehicleID, ownerID} -> {$ownershipID, database change} 
        // result: $ownershipID would be "NULL" or, an id in the database. An ownership might be created into the database.
        // sudo:
            // if no vehicle involved:
                // set $ownershipID "NULL";
            // if there is a vehicle involved:
                // create a vehicle and owner object using $vehicleID and $ownerID
                // create a $ownership object
                // if $ownership contain null person id, use the existed id as $ownershipID or insert new one and use that new id.
                // elseif $ownership is not new, get existed ownership id.
                // elseif $ownership is new, use ownershipDB->insertOwnershipBothExisted($ownership), get new ownership id.
        
        // if no vehicle involved:
        if ($vehicleID == "NULL") {
            $ownershipID = "NULL"; // set $ownershipID null;
        } 
        // if there is a vehicle involved:
        else {

            // create a vehicle and owner object using $vehicleID and $ownerID
            // create a $ownership object
            $ownership = new Ownership(new Vehicle("NULL", "NULL", "NULL", "NULL", $vehicleID), new Person($ownerID, "NULL", "NULL", "NULL","NULL", "NULL"), "NULL");
            // echo "<p></p>".$ownership->render()."<hr>"; // debugging
            
            // if $ownership contain null person id, use the existed id as $ownershipID or insert new one and use that new id.
            if ($ownership->getPersonID() == "NULL" && $ownerID == "NULL") { // double checking for reducing potential bug!
                // echo "flag1";// debugging
                // $flag1 = $ownershipDB->isOwnershipInDBPersonNull($ownership); //debugging
                // echo "flag1>".$flag1."<flag1";// debugging
                // check if ownership is in db
                if ($ownershipDB->isOwnershipInDBPersonNull($ownership)!=false) {
                    // if so, use existed ownership id
                    // echo "flag1.1";// debugging
                    $ownershipID = $ownershipDB->isOwnershipInDBPersonNull($ownership);
                    $isOwnershipNew = false;
                } else {
                    // if not, insert new ownership and use the new id
                    // echo "flag1.2";// debugging
                    $ownershipID = $ownershipDB->insertOwnershipBothExisted($ownership);
                    $isOwnershipNew = true;
                }
            } 
            // elseif $ownership is not new, get existed ownership id.
            elseif($ownershipDB->isOwnershipInDB($ownership)!=false) {
                $isOwnershipNew = false;
                $ownershipID = $ownershipDB->isOwnershipInDB($ownership);
            }
            // elseif $ownership is new, use ownershipDB->insertOwnershipBothExisted($ownership), get new ownership id.
            elseif($ownershipDB->isOwnershipInDB($ownership)==false) {
                $isOwnershipNew = true;
                $ownershipID = $ownershipDB->insertOwnershipBothExisted($ownership);
            }
            // undifined error
            else {
                throw new Exception("Undefined behaviour for getting ownership id");
            }
        }
        // echo "<h3>ownershipID:$ownershipID</h3>"; // debugging
        echo "<hr>---------------------Processing ownership done, OwnershipID:".$ownershipID."---------------------<br>";
        if (!isset($isOwnershipNew)) {
            echo "No ownership involved.<hr>";
        } elseif ($isOwnershipNew) {
            echo "New ownership created.<hr>";
        } else {
            echo "There is no new ownership created.<hr>";
        }

    // TODO: processing {ownership, offenderID, report general data form} -> {reportID, database change}
        $offenceID = $_POST["reportOffence"];
        $username = $user->getUsername();
        $incidentDate = $_POST["reportDate"];
        $reportStatement = $_POST["reportStatement"];
        if ($ownershipID=="NULL" && $offenderID=="NULL") {
            $sql = "INSERT INTO Incident (Ownership_ID, People_ID, Offence_ID, Account_username, Incident_date, Incident_report)".
            "VALUES(NULL,NULL,'$offenceID','$username','$incidentDate','$reportStatement');";
        } elseif($ownershipID=="NULL") {
            $sql = "INSERT INTO Incident (Ownership_ID, People_ID, Offence_ID, Account_username, Incident_date, Incident_report)".
            "VALUES(NULL,'$offenderID','$offenceID','$username','$incidentDate','$reportStatement');";
        } elseif($offenderID=="NULL") {
            $sql = "INSERT INTO Incident (Ownership_ID, People_ID, Offence_ID, Account_username, Incident_date, Incident_report)".
            "VALUES('$ownershipID',NULL,'$offenceID','$username','$incidentDate','$reportStatement');";
        } else {
            $sql = "INSERT INTO Incident (Ownership_ID, People_ID, Offence_ID, Account_username, Incident_date, Incident_report)".
            "VALUES('$ownershipID','$offenderID','$offenceID','$username','$incidentDate','$reportStatement');";
        }
        echo "<hr>report insertion query:".$sql."<hr>";
    }
    
    

    // TODO: test the ownership insert module:
        // note: 'new+licence' means new with a licence, '-' means without
        // Restart the database firstly!
        // test case 1 to 10 are inserting new ownership.
        // test cases1: Test the form with vehicle:new, owner: new+licence
            // vehicle licence: test001; 
            // vehicle colour: test; 
            // vehicle Make: test; 
            // vehicle model: test;
            // owner licence: testtesttest0001;
            // owner fname: fname1;
            // owner lname: lname1;
            // owner address: test center;
            // owner dob: today;
            // RESULTS1: NEW vehicle added into vehicle table
            // RESULTS2: NEW person add into people table
            // RESULTS3: NEW ownership add into people table
            // RESULTS4: Print the correct id of the ownership.

        // test cases2: Test the form with vehicle:new, owner: new-licence
            // vehicle licence: test002; 
            // vehicle colour: test; 
            // vehicle Make: test; 
            // vehicle model: test;
            // owner licence:;
            // owner fname: fname2;
            // owner lname: lname2;
            // owner address: test center;
            // owner dob: today;
            // RESULTS1: NEW vehicle added into vehicle table
            // RESULTS2: NEW person add into people table
            // RESULTS3: NEW ownership add into people table
            // RESULTS4: Print the correct id of the ownership.

        // test cases3: Test the form with vehicle:new, owner: exists+licence
            // vehicle licence: test003; 
            // vehicle colour: test; 
            // vehicle Make: test; 
            // vehicle model: test;
            // owner licence: testtesttest0001;
            // owner fname: fname1;
            // owner lname: lname1;
            // owner address: test center;
            // owner dob: today;
            // RESULTS1: NEW vehicle added into vehicle table
            // RESULTS2: NEW ownership add into people table
            // RESULTS3: Print the correct id of the ownership.
            
        // test cases4: Test the form with vehicle:new, owner: exists-licence
            // vehicle licence: test004; 
            // vehicle colour: test; 
            // vehicle Make: test; 
            // vehicle model: test;
            // owner licence:;
            // owner fname: fname2;
            // owner lname: lname2;
            // owner address: test center;
            // owner dob: today;
            // RESULTS1: NEW vehicle added into vehicle table.
            // RESULTS2: NEW ownership add into people table.
            // RESULTS3: Print the correct id of the ownership.

        // test cases5: Test the form with vehicle:new, owner: emtpy
            // vehicle licence: test005; 
            // vehicle colour: test; 
            // vehicle Make: test; 
            // vehicle model: test;
            // owner licence:;
            // owner fname:;
            // owner lname:;
            // owner address:;
            // owner dob:;
            // RESULTS1: NNEW vehicle added into vehicle table.
            // RESULTS2: NEW ownership add into people table.
            // RESULTS3: Print the correct id of the ownership.


        
        // test cases6: Test the form with vehicle:exists, owner: new+licence
            // vehicle licence: test004; 
            // vehicle colour: test; 
            // vehicle Make: test; 
            // vehicle model: test;
            // owner licence: testtesttest0004;
            // owner fname: fname4;
            // owner lname: lname4;
            // owner address: test center;
            // owner dob: today;
            // RESULTS1: NEW person add into people table.
            // RESULTS2: NEW ownership add into people table.
            // RESULTS3: Print the correct id of the ownership.

        // test cases7: Test the form with vehicle:exists, owner: new-licence
            // vehicle licence: test004; 
            // vehicle colour: test; 
            // vehicle Make: test; 
            // vehicle model: test;
            // owner licence: ;
            // owner fname: fname5;
            // owner lname: lname5;
            // owner address: test center;
            // owner dob: today;
            // RESULTS1: NEW person add into people table.
            // RESULTS2: NEW ownership add into people table.
            // RESULTS3: Print the correct id of the ownership.

        // test cases8: Test the form with vehicle:exists, owner: exists+licence
            // vehicle licence: test001; 
            // vehicle colour: test; 
            // vehicle Make: test; 
            // vehicle model: test;
            // owner licence: testtesttest0004;
            // owner fname: fname4;
            // owner lname: lname4;
            // owner address: test center;
            // owner dob: today;
            // RESULTS2: NEW ownership add into people table.
            // RESULTS3: Print the correct id of the ownership.

        // test cases9: Test the form with vehicle:exists, owner: exists-licence
            // vehicle licence: test001; 
            // vehicle colour: test; 
            // vehicle Make: test; 
            // vehicle model: test;
            // owner licence: ;
            // owner fname: fname5;
            // owner lname: lname5;
            // owner address: test center;
            // owner dob: today;
            // RESULTS2: NEW ownership add into people table.
            // RESULTS3: Print the correct id of the ownership.

        // test cases10: Test the form with vehicle:exists, owner: empty
            // vehicle licence: test001; 
            // vehicle colour: test; 
            // vehicle Make: test; 
            // vehicle model: test;
            // owner licence: ;
            // owner fname: ;
            // owner lname: ;
            // owner address: ;
            // owner dob: ;
            // RESULTS2: NEW ownership add into people table.
            // RESULTS3: Print the correct id of the ownership.


} catch (Exception $error) {
    // throw $error;
    require("../reuse/errorMessage.php");
    $messages = $messages;
    if (empty($messages)) {
        renderErrorMessages([$error->getMessage()]);
    } else {
        renderErrorMessages($messages);
    }
    
    // header("location: ../error.php?errorMessage=".$error->getMessage());
}   
?>