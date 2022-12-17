<?php
try{
    $messages = [];
    require("../reuse/errorMessage.php");
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
        require("../reuse/_audit.php");
        require("_reports.php");
        $conn = connectDB();
        $ownershipDB = new OwnershipDB($user, $conn);
        $vehicleDB = new VehiclesDB($user, $conn);
        $peopleDB = new PeopleDB($user, $conn);
        $reportDB = new ReportsDB($user, $conn);
        $auditDB = new AuditDB($user, $conn);
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
        function isVehicleFormValid($post) {
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
            $messages = array();
            if (!empty($_POST["ownerLicence"]) && strlen($_POST["ownerLicence"])!=16) {
                array_push($messages,"Invalid owner driving licence length!<br>length should be 16 or empty");
            }
            if (strlen($_POST["ownerFirstName"])>25 || strlen($_POST["ownerFirstName"])<1) {
                array_push($messages,"Invalid owner first name length!<br>length should be 1 to 25");
            }
            if (strlen($_POST["ownerLastName"])>25 || strlen($_POST["ownerLastName"])<1) {
                array_push($messages,"Invalid owner last name length!<br>length should be 1 to 25");
            }
            if (strlen($_POST["ownerAddress"])>50 || strlen($_POST["vehicleModel"])<1) {
                array_push($messages,"Invalid owner address length!<br>length should be 1 to 50");
            }
            if (empty($_POST["ownerDOB"])) {
                array_push($messages,"Invalid ownerDOB length!<br>should not be empty value");
            }
            if (empty($messages)) {
                return ["allValid"=>true];
            } else {
                return ["allValid"=>false, "messages"=>$messages];
            }
        }
        function isOffenderFormValid($post) {
            // assume there is a Offender form
            // return an associative array:
            // ["allValid"=>true/false, "detail"=>["OffenderField1"=>"valid/invalid hint", "OffenderField2"=>"valid/invalid hint", ...]]
            // Return True if the format of all the field of Offender is valid.
            // Return An associative array whose key is field name of Offender form, and value is "correct" 

            $messages = array();
            if (!empty($_POST["offenderLicence"]) && strlen($_POST["offenderLicence"])!=16) {
                array_push($messages,"Invalid offender driving licence length!<br>length should be 16 or empty");
            }
            if (strlen($_POST["offenderFirstName"])>25 || strlen($_POST["offenderFirstName"])<1) {
                array_push($messages,"Invalid offender first name length!<br>length should be 1 to 25");
            }
            if (strlen($_POST["offenderLastName"])>25 || strlen($_POST["offenderLastName"])<1) {
                array_push($messages,"Invalid offender last name length!<br>length should be 1 to 25");
            }
            if (strlen($_POST["offenderAddress"])>50 || strlen($_POST["vehicleModel"])<1) {
                array_push($messages,"Invalid offender address length!<br>length should be 1 to 50");
            }
            if (empty($_POST["offenderDOB"])) {
                array_push($messages,"Invalid offenderDOB length!<br>should not be empty value");
            }
            if (empty($messages)) {
                return ["allValid"=>true];
            } else {
                return ["allValid"=>false, "messages"=>$messages];
            }
        }
    // NOTE: $auditTime is defined once the vehicle is inserted or referenced.
    // NOTE(CTND): However, if there is no vehicle involved. the Time should be set manually.
    // DONE1(for edit): IF it is an editing submit, Check if the report id exists in $_GET and in databas, and check if the user can edit it.
        if (isset($_GET["edit"])&&$_GET["edit"]=="true") {
            if (empty($_GET["id"])) {
                throw new Exception("in valid argument, report id is not given for editing");
                die();
            } else {
                $reportTmp = $reportDB->getReportByReportID($_GET["id"], false);
                if ($reportTmp==false) {
                    // // add audit trail
                    // $audit = new Audit("NULL", $user->getUsername(), "Incidents", "NULL","NULL", $reportTmp->toJSON(), "SELECT-EMPTY-SECONDARY", "now");
                    // $auditDB->insertAudit($audit);

                    throw new Exception("in valid argument, report id doesn't exist");
                    die();
                } elseif ($reportTmp->accountUsername != $user->getUsername() && !$user->isAdmin()) {
                    // // add audit trail
                    // $audit = new Audit("NULL", $user->getUsername(), "Incidents", strval($reportTmp->Incident_ID), $reportTmp->toJSON(), "NULL", "SELECT-FOUND-SECONDARY", "now");
                    // $auditDB->insertAudit($audit);

                    throw new Exception("Illegal submit, you don't have permission to edit this report");
                    die();
                } else {
                    // // add audit trail
                    // $audit = new Audit("NULL", $user->getUsername(), "Incidents", strval($reportTmp->Incident_ID), $reportTmp->toJSON(), "NULL", "SELECT-FOUND-SECONDARY", "now");
                    // $auditDB->insertAudit($audit);

                    echo "no assertion about editing submit valiated"; //debugging
                    // die(); //debugging, because I haven't avoid insert new report.
                }
            } 
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
            ."<br>If you don't enter vehicle information, then owner information must be empty as well."
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
        
    // DONE(NOTE TESTED): Validate each form if exists
        if ($hasVehicleForm && isVehicleFormValid($_POST)['allValid']!=true) {
            $messages = isVehicleFormValid($_POST)['messages'];
            mysqli_close($conn);
            throw new Exception("check the \$messages");
            die();
        }
        if ($hasOwnerForm && isOwnerFormValid($_POST)['allValid']!=true) {
            $messages = isOwnerFormValid($_POST)['messages'];
            mysqli_close($conn);
            throw new Exception("check the \$messages");
            die();
        }
        if ($hasOffenderForm && isOffenderFormValid($_POST)['allValid']!=true) {
            $messages = isOffenderFormValid($_POST)['messages'];
            mysqli_close($conn);
            throw new Exception("check the \$messages");
            die();
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
            // set the audit time.
            date_default_timezone_set("Europe/London");
            $auditTime = date("y-m-d H-i-s"); //I reference the link for calling the date function: https://www.w3schools.com/php/php_date.asp

            // case that the fields of vehicle form is invalid (function is not implemented yet, always valid) : give error feedback
        } elseif ($hasVehicleForm && isVehicleFormValid($_POST)['allValid']!=true) {
            // moved to "Validate each form if exists"
            // // echo "vehicle Information is not valid:";// debug
            // $messages = isVehicleFormValid($_POST)['messages'];
            // // print_r($messages); // debug
            // mysqli_close($conn);
            // throw new Exception("check the \$messages");
            // die();
            
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
                
                // add audit trial (INSERT-SUCCESS)
                $vehicleAudit = new Audit("NULL", $user->getUsername(), "Vehicles", strval($newVehicleFromDB->ID), "NULL", $newVehicleFromDB->toJSON(), "INSERT-SUCCESS", "now");
                $auditTime = $vehicleAudit->auditTime;
                $auditDB->insertAudit($vehicleAudit);
            
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
                    // create audit object, don't INSERT here. INSERT WHEN THE OWNERSHIP IS CREATED. 
                    $oldVehicleAudit = new Audit("NULL", $user->getUsername(), "Vehicles", strval($vehicleID), $oldVehicleFromDB->toJSON(), "NULL", "REFERENCE-INSERT", "now");
                    $auditTime = $oldVehicleAudit->auditTime;

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
                        // audit (INSERT-SUCCESS)
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
                        // audit (INSERT-SUCCESS)
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

                    // audit (INSERT-SUCCESS)
                    $ownerAudit = new Audit("NULL", $user->getUsername(), "People", strval($ownerID), "NULL", $newOwnerFromDB->toJSON(), "INSERT-SUCCESS", $auditTime);
                    $auditDB->insertAudit($ownerAudit);
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
                        $ownerLicenceTmp = $ownerWithoutLicenceFromDB->getLicence(); 
                        if (empty($ownerLicenceTmp)) {
                            unset($ownerLicenceTmp);
                            // echo "GOOD, owner licence from db is empty:'".$ownerWithoutLicenceFromDB->getLicence()."'<hr>"; // debugging
                        } else {
                            unset($ownerLicenceTmp);
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

                        // audit (INSERT-SUCCESS)
                        $ownerAudit = new Audit("NULL", $user->getUsername(), "People", strval($ownerID), "NULL", $ownerWithoutLicenceFromDB->toJSON(), "INSERT-SUCCESS", $auditTime);
                        $auditDB->insertAudit($ownerAudit);
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
        


    // DONE: processing the {offender form} -> {$offenderID, database change} 
        $offenderID = "NULL";
        if ($hasOffenderForm==false) {
            $offenderID = "NULL";            
            // case that the fields of offender form is invalid (function is not implemented yet, always valid) : give error feedback
        } elseif($hasOffenderForm && isOffenderFormValid($_POST)['allValid']==false) {
            $messages = isOffenderFormValid($_POST)['messages'];
            // echo "<hr>";
            // echo "offender Information is not valid:";
            mysqli_close($conn);
            throw new Exception("check the \$messages");
            die();
            
            // valid:
        } elseif($hasOffenderForm && isOffenderFormValid($_POST)['allValid']==true) {
            // sudo-code:
            // case 1: the offender has a licence:
                // search licence
                    // if licence in db (not a new offender):
                        // if form detail not match the db:
                            // feedback these difference and die();
                        // if form detial match the db:
                            // get the offender id
                            // set the offender id on $offenderID;
                    // if licence not in db (new):
                        // if the combination of (name, address, dob) match some data in the database, 
                            // feedback this problem.
                            // die();
                        // insert this offender, get offender id
                        // set the offender id on $offenderID;
                        // audit (INSERT-SUCCESS)
            // case 2: the offender doesn't have a licence:
                // if there are given name, dob, address in the form:
                    // use these 3 data as unique key to search the database
                    // if match one:
                        // if the one has licence:
                            // feed back this problem and die()
                        // get the id of the data in database
                        // set the offender id on $offenderID
                    // else:
                        // insert new data with name, dob, address
                        // get id of the newly-inserted offender
                        // set the offender id on $offenderID
                        // audit (INSERT-SUCCESS)
                // else:
                    // feedback these missing field and die();
            
            // implementation:

            // case1: has licence info
            if (!empty($_POST['offenderLicence'])) {
                // licence in db
                if ($peopleDB->isPersonLicenceInDB($_POST['offenderLicence'])){
                    // echo "offender is in the database<hr>";// debugging
                    $isOffenderNew = false;

                    $offenderFromDB = $peopleDB->getPersonByLicence($_POST['offenderLicence']);
                    // echo "<hr>offender from database:<br>".$offenderFromDB->renderRow(true)."<hr>"; //debugging
                    // assume offenderFromDB is not empty
                    if (empty($offenderFromDB)) {
                        throw new Exception("vehicle licence is not in database, but it should be in the database as it was checked by $peopleDB->isPersonLicenceInDB");
                    }
                    $offenderFromForm = new Person("NULL", $_POST['offenderLicence'], $_POST['offenderAddress'], 
                                                $_POST["offenderDOB"], $_POST["offenderFirstName"]." ".$_POST["offenderLastName"], "NULL");
                    // echo "<hr>offender from the form:<br>".$offenderFromForm->renderRow(true)."<hr>"; //debugging

                    // get error state, and give feed back.
                    $oldOffenderSame = true;
                    if ($offenderFromDB->getLicence()!=$offenderFromForm->getLicence()) {
                        array_push($messages, "although offender licence is in the database, but the licence is not the same with the offender with the licence in the database");
                        // echo "although offender licence is in the database, but the licence is not the same with the offender with the licence in the database<hr>"; //debugging
                        $oldOffenderSame = false;
                    }
                    if ($offenderFromDB->getAddress()!=$offenderFromForm->getAddress()) {
                        array_push($messages, "although offender licence is in the database, but the address is not the same with the offender with the address in the database");
                        // echo "although offender licence is in the database, but the address is not the same with the offender with the address in the database<hr>"; //debugging
                        $oldOffenderSame = false;
                    }
                    if ($offenderFromDB->getDOB()!=$offenderFromForm->getDOB()) {
                        array_push($messages, "although offender licence is in the database, but the DOB is not the same with the offender with the DOB in the database");
                        // echo "although offender licence is in the database, but the DOB is not the same with the offender with the DOB in the database<hr>"; //debugging
                        $oldOffenderSame = false;
                    }
                    if ($offenderFromDB->getFirstName()!=$offenderFromForm->getFirstName()) {
                        array_push($messages, "although offender licence is in the database, but the first name is not the same with the offender with the first name in the database");
                        // echo "although offender licence is in the database, but the first name is not the same with the offender with the first name in the database<hr>"; //debugging
                        $oldOffenderSame = false;
                    }
                    if ($offenderFromDB->getLastName()!=$offenderFromForm->getLastName()) {
                        array_push($messages, "although offender licence is in the database, but the last name is not the same with the offender with the last name in the database");
                        // echo "although offender licence is in the database, but the last name is not the same with the offender with the last name in the database<hr>"; //debugging
                        $oldOffenderSame = false;
                    }
                    
                    if ($oldOffenderSame) {
                        // if the form with licence match the db:
                            // get the offender id
                            // set the offender id on $offenderID;
                        // echo "GOOD GOOD GOOD for the offender form!<br>"; // debugging
                        $offenderID = $offenderFromDB->getID();
                        $isOffenderNew = false;
                        // echo "Offender already in database:offender id=".$offenderID; // debugging
                    } else {
                        // if the form with licence not match the db:
                            // feedback these difference and die():
                        
                        throw new Exception("check \$messages");
                                // echo "The offender information you entered is not the same as the one in the database.<br> Please Check your offender data<hr>"; // debugging
                        die();
                    }
                
                    // licence not in db
                } else {
                    $offenderFromForm = new Person("NULL", $_POST['offenderLicence'], $_POST['offenderAddress'], 
                                                $_POST["offenderDOB"], $_POST["offenderFirstName"]." ".$_POST["offenderLastName"], "NULL");

                    // if the combination of (name, dob, address) matches data, feedback this error.
                    if ($peopleDB->isPersonDetailInDB($offenderFromForm)) {
                        echo "<hr><b>The offender you typed is already in database, please enter correct information: </b><br>";
                        echo "your data:".$offenderFromForm->renderRow(true)."<br>";
                        $offenderFromDB = $peopleDB->getPersonByDetail($offenderFromForm);
                        echo "database data:".$offenderFromDB->renderRow(true)."<br>";
                        die();
                    }

                    // offender is new, then insert this offender into the database
                    $isOffenderNew = true;
                    
                    $offenderID = $peopleDB->insertNewPerson($offenderFromForm); // id set
                    $newOffenderFromDB = $peopleDB->getPersonByLicence($offenderFromForm->getLicence());
                    // echo "new offender:<br>".$newOffenderFromDB->renderRow(true)."<hr>"; // debugging
                
                    // audit (INSERT-SUCCESS)
                    $offenderAudit = new Audit("NULL", $user->getUsername(), "People", strval($offenderID), "NULL", $newOffenderFromDB->toJSON(), "INSERT-SUCCESS", $auditTime);
                    $auditDB->insertAudit($offenderAudit);
                }
                
                
            } 
            // case 2: has no licence info
            else {

                // if has empty data of these, feedback error.
                if(empty($_POST["offenderAddress"]) || empty($_POST["offenderFirstName"]) || empty($_POST["offenderLastName"]) || empty($_POST["offenderDOB"])) {
                    throw new Exception("missed some data of offenderAddress or offenderFirstName or offenderLastName or offenderDOB<hr>");
                    // echo "missed some data of offenderAddress, offenderFirstName, offenderLastName, offenderDOB<hr>";
                    die();
                } 
                // else: name, dob, address is given
                else {
                    $offenderWithoutLicenceFromForm = new Person("NULL", "NULL",$_POST["offenderAddress"],$_POST["offenderDOB"],$_POST["offenderFirstName"]." ".$_POST["offenderLastName"], "NULL");
                    // echo "offender without licence from the form: <br>".$offenderWithoutLicenceFromForm->renderRow(true)."<hr>"; // debug

                    // match one
                    if ($peopleDB->isPersonDetailInDB($offenderWithoutLicenceFromForm)) {
                        $offenderWithoutLicenceFromDB = $peopleDB->getPersonByDetail($offenderWithoutLicenceFromForm);

                        // check if the offender from db has no licence, if it has licence, feedback this error and die()
                        $offenderID = $offenderWithoutLicenceFromDB->getID(); // set id
                        $offenderTmp = $offenderWithoutLicenceFromDB->getLicence(); 
                        if (empty($offenderTmp)) {
                            unset($offenderTmp);
                            // echo "GOOD, offender licence from db is empty:'".$offenderWithoutLicenceFromDB->getLicence()."'<hr>"; // debugging
                        } else {
                            unset($offenderTmp);
                            // the one in db have a licence, error.
                            throw new Exception("<br>The offender you submit:".$offenderWithoutLicenceFromForm->renderRow(true).
                            "is already in the database, and has a driving licence: ".$offenderWithoutLicenceFromDB->getLicence().
                            "<br>Please fill the correct offender information and create the report again.</b>");
                            // echo "<b>Error: offender licence from db is not empty:</b>'".$offenderWithoutLicenceFromDB->getLicence()."'<hr>";
                            die();
                        }

                        // echo "<b>Uses exists offender in database:</b> <br>".$offenderWithoutLicenceFromDB->renderRow(true)."<hr>"; // debugging
                        $isOffenderNew = false;
                    }
                    // not match one
                    else {
                        // insert new data with name, dob, address
                        // get id of the newly-inserted offender
                        // set the offender id on $offenderID
                        $offenderID = $peopleDB->insertNewPerson($offenderWithoutLicenceFromForm);
                        $offenderWithoutLicenceFromDB = $peopleDB->getPersonByDetail($offenderWithoutLicenceFromForm);
                        
                        // echo "\$offenderID: ".$offenderID."<hr>";
                        // echo "offenderWithoutLicenceFromForm id: ".$offenderWithoutLicenceFromForm->getID()."<hr>"; // debugging 
                        // echo "offenderWithoutLicenceFromDB id: ".$offenderWithoutLicenceFromDB->getID()."<hr>"; // debugging
                        // echo "<b>offender without licence is new, created:</b> <br>".$offenderWithoutLicenceFromDB->renderRow(true)."<hr>"; // debugging
                        $isOffenderNew = true;

                        // audit (INSERT-SUCCESS)
                        $offenderAudit = new Audit("NULL", $user->getUsername(), "People", strval($offenderID), "NULL", $offenderWithoutLicenceFromDB->toJSON(), "INSERT-SUCCESS", $auditTime);
                        $auditDB->insertAudit($offenderAudit);
                    }
                }
            }
        } 
        
        echo "<hr>---------------------Processing offender form done, OffenderID:".$offenderID."---------------------<br>";
        if (!isset($isOffenderNew)) {
            echo "No offender involved.<hr>";
        } elseif ($isOffenderNew) {
            echo "New offender created.<hr>";
        } else {
            echo "There is no new offender created.<hr>";
        }

    // DONE: processing {vehicleID, ownerID} -> {$ownershipID, database change} 
        // result: $ownershipID would be "NULL" or, an id in the database. An ownership might be created into the database.
        // sudo:
            // if no vehicle involved:
                // set $ownershipID "NULL";
            // if there is a vehicle involved:
                // create a vehicle and owner object using $vehicleID and $ownerID
                // create a $ownership object
                // if $ownership contain null person id
                    // If $ownership is in db
                        // use the existed id as $ownershipID (insert audit later when the report is created)
                    // elseif $ownership not in db
                        // insert the new ownership.
                        // audit INSERT-SUCCESS FOR the ownership
                        // audit REFERENCE-INSERT for the vehicle
                // elseif $ownership is not new, get existed ownership id. (insert audit later when the report is created.)
                // elseif $ownership is new, use ownershipDB->insertOwnershipBothExisted($ownership), get new ownership id.
                    // audit INSERT-SUCCESS for the ownership
                    // audit REFERENCE-INSERT for the vehicle
                    // audit REFERENCE-INSERT for the owner
        
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
                    $newOwnershipFromDB = $ownershipDB->getOwnershipByID(strval($ownershipID));

                    // audit INSERT-SUCCESS FOR the ownership
                    $ownershipAudit = new Audit("NULL", $user->getUsername(), "Ownership", strval($ownershipID), "NULL", $newOwnershipFromDB->toJSON(), "INSERT-SUCCESS", $auditTime);
                    $auditDB->insertAudit($ownershipAudit);

                    // audit REFERENCE-INSERT for the vehicle
                    $vehicleAudit = new Audit("NULL", $user->getUsername(), "Vehicles", strval($newOwnershipFromDB->getVehicleID()), $newOwnershipFromDB->getVehicle()->toJSON(), "NULL", "REFERENCE-INSERT", $auditTime);
                    $auditDB->insertAudit($vehicleAudit);
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
                
                $newOwnershipFromDB = $ownershipDB->getOwnershipByID(strval($ownershipID));

                // audit INSERT-SUCCESS FOR the ownership
                $ownershipAudit = new Audit("NULL", $user->getUsername(), "Ownership", strval($ownershipID), "NULL", $newOwnershipFromDB->toJSON(), "INSERT-SUCCESS", $auditTime);
                $auditDB->insertAudit($ownershipAudit);

                // audit REFERENCE-INSERT for the vehicle
                $vehicleAudit = new Audit("NULL", $user->getUsername(), "Vehicles", strval($newOwnershipFromDB->getVehicleID()), $newOwnershipFromDB->getVehicle()->toJSON(), "NULL", "REFERENCE-INSERT", $auditTime);
                $auditDB->insertAudit($vehicleAudit);

                // audit REFERENCE-INSERT for the owner
                $ownerAudit = new Audit("NULL", $user->getUsername(), "People", strval($newOwnershipFromDB->getPersonID()), $newOwnershipFromDB->getPerson()->toJSON(), "NULL", "REFERENCE-INSERT", $auditTime);
                $auditDB->insertAudit($ownerAudit);

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

    
    // DONE2(for edit): If it is an editing submit, update the report using{ownership, offenderID, report general dataform, reportID} and die();
        // because at done1, it is already validated, so it can be assumed that the report is valid.
        if (isset($_GET["edit"])&&$_GET["edit"]=="true") {
            $reportID = $_GET["id"];
            $offenceID = $_POST["reportOffence"];

            $incidentDate = $_POST["reportDate"];
            $reportStatement = $_POST["reportStatement"];
            $ownershipIDtext = $ownershipID=="NULL" ? "NULL" : "'".$ownershipID."'";
            $offenderIDtext = $offenderID=="NULL" ? "NULL" : "'".$offenderID."'";
            
            $sql = "UPDATE Incident SET Ownership_ID=$ownershipIDtext, People_ID=$offenderIDtext, Offence_ID='$offenceID'"
            .", Incident_date='$incidentDate', Incident_report='$reportStatement' WHERE Incident_ID=$reportID";
            
            echo "<hr>report update query:".$sql."<hr>"; //debugging
            try{
                $initialIncidentsFromDB = $reportDB->getReportByReportID($reportID);
                mysqli_query($conn, $sql);
                echo "Report update successfully, reportID:$reportID";
                
                // audit incident (UPDATE-SUCCESS)
                $updatedIncidentsFromDB = $reportDB->getReportByReportID($reportID);
                $incidentAudit = new Audit("NULL", $user->getUsername(), "Incidents", strval($updatedIncidentsFromDB->incidentID), $initialIncidentsFromDB->toJSON(), $updatedIncidentsFromDB->toJSON(), "UPDATE-SUCCESS", $auditTime);
                $auditDB->insertAudit($incidentAudit);
                
                // if ownership is not null: audit ownership (REFERENCE-UPDATE) (not sure if audits trail for vehicle and owner should be added or not, probabaly should not because they were already recorded when creating ownership. However, .... I DONT KNOW...)
                if ($ownershipID != "NULL" && $ownershipID != NULL) {
                    $ownershipFromDB = $ownershipDB->getOwnershipByID($ownershipID);
                    $ownershipAudit = new Audit("NULL", $user->getUsername(), "Ownership", strval($ownershipFromDB->ID), $ownershipFromDB->toJSON(), "NULL", "REFERENCE-UPDATE", $auditTime);
                    $auditDB->insertAudit($ownershipAudit);
                }
                
                // if offender is not null: audit offender (REFERENCE-UPDATE)
                if ($offenderID != "NULL" && $offenderID != NULL) {
                    $offenderFromDB = $peopleDB->getPersonByID($offenderID);
                    $offenderAudit = new Audit("NULL", $user->getUsername(), "People", strval($offenderFromDB->ID), $offenderFromDB->toJSON(), "NULL", "REFERENCE-UPDATE", $auditTime);
                    $auditDB->insertAudit($offenderAudit);
                }


            } catch (Exception $error) {
                renderErrorMessages(["Failed to create Report",$error->getMessage()]);
            }
            die();// do this so won't insert an identical one.
        }
    // DONE: processing {ownership, offenderID, report general data form} -> {reportID, database change}
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
            "VALUES('$ownershipID','$offenderID','$offenceID','$username','$incidentDate',\"$reportStatement\");";
        }
        // echo "<hr>report insertion query:".$sql."<hr>"; // debugging
        try{
            mysqli_query($conn, $sql);
            $reportID = mysqli_insert_id($conn);
            echo "<div class='feedback-green'><div class='feedback-text-line'>Report created successfully, reportID:$reportID</div></div>";

            // audit incident (INSERT-SUCCESS)
            $newIncidentsFromDB = $reportDB->getReportByReportID($reportID);
            $incidentAudit = new Audit("NULL", $user->getUsername(), "Incidents", strval($newIncidentsFromDB->incidentID), "NULL", $newIncidentsFromDB->toJSON(), "INSERT-SUCCESS", $auditTime);
            $auditDB->insertAudit($incidentAudit);
            
            // if ownership is not null: audit ownership (REFERENCE-INSERT) (not sure if audits trail for vehicle and owner should be added or not, probabaly should not because they were already recorded when creating ownership. However, .... I DONT KNOW...)
            if ($ownershipID != "NULL" && $ownershipID != NULL) {
                $ownershipFromDB = $ownershipDB->getOwnershipByID($ownershipID);
                $ownershipAudit = new Audit("NULL", $user->getUsername(), "Ownership", strval($ownershipFromDB->ID), $ownershipFromDB->toJSON(), "NULL", "REFERENCE-INSERT", $auditTime);
                $auditDB->insertAudit($ownershipAudit);
            }
            
            // if offender is not null: audit offender (REFERENCE-INSERT)
            if ($offenderID != "NULL" && $offenderID != NULL) {
                $offenderFromDB = $peopleDB->getPersonByID($offenderID);
                $offenderAudit = new Audit("NULL", $user->getUsername(), "People", strval($offenderFromDB->ID), $offenderFromDB->toJSON(), "NULL", "REFERENCE-INSERT", $auditTime);
                $auditDB->insertAudit($offenderAudit);
            }
        } catch (Exception $error) {
            renderErrorMessages(["Failed to create Report",$error->getMessage()]);
        }
    }
    
    

    // DONE: test the ownership insert module:
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


    // DONE: test the report insert sql sentence:
        // note: 'new+licence' means new with a licence, '-' means without
        // note2:if both vehicle and owner are old, assume the ownership is new.
        // Restart the database between test cases!
        // test case
            // normal case
                // testcase1: vehicle: new, owner: new+licence == offender: new+licence
                    // vehicle licence: test001
                    // colour: test
                    // make: test
                    // model: test
                    // ownerLicence: testtesttest0001
                    // ownerFirstName: owner1
                    // ownerLastName: owner1
                    // ownerAddress: test
                    // ownerDOB: today
                    // offenderLicence: testtesttest0001
                    // offenderFirstName: owner1
                    // offenderLastName: owner1
                    // offenderAddress: test
                    // offenderDOB: today
                    // result: new vehicle, new owner, no new offender, new ownership
                // testcase2: vehicle: old, owner: old+licence == offender: old+licence
                    // vehicle licence: TE12SLA
                    // colour: White
                    // make: Tesla
                    // model: Model3
                    // ownerLicence: ALLEN88K23KLR9B3
                    // ownerFirstName: Jennifer
                    // ownerLastName: Allen
                    // ownerAddress: 46 Bramcote Drive, Nottingham
                    // ownerDOB: 1994-03-12
                    // offenderLicence: ALLEN88K23KLR9B3
                    // offenderFirstName: Jennifer
                    // offenderLastName: Allen
                    // offenderAddress: 46 Bramcote Drive, Nottingham
                    // offenderDOB: 1994-03-12
                    // results: no new vehicle, no new owner, no new offender, new ownership
            // driving without licence
                // testcase1: vehicle: new, owner: new-licence == offender: new-licence
                    // vehicle licence: test001
                    // colour: test
                    // make: test
                    // model: test
                    // ownerLicence: 
                    // ownerFirstName: owner2
                    // ownerLastName: owner2
                    // ownerAddress: test
                    // ownerDOB: today
                    // offenderLicence: 
                    // offenderFirstName: owner2
                    // offenderLastName: owner2
                    // offenderAddress: test
                    // offenderDOB: today
                    // result: new vehicle, new owner, no new offender, new ownership
                // testcase2: vehicle: old, owner: old-licence == offender: old-licence
                    // vehicle licence: TE12SLA
                    // colour: White
                    // make: Tesla
                    // model: Model3
                    // ownerLicence: 
                    // ownerFirstName: Smith
                    // ownerLastName: Tony
                    // ownerAddress: 22 Avenue Road, Grantham
                    // ownerDOB: 2012-01-01
                    // offenderLicence: 
                    // offenderFirstName: Smith
                    // offenderLastName: Tony
                    // offenderAddress: 22 Avenue Road, Grantham
                    // offenderDOB: 2012-01-01
                    // result: no new vehicle, no new owner, no new offender, new ownership

            // driving without licence(stolen car)
                // testcase1: vehicle: new, owner: null        ; offender: new-licence
                    // vehicle licence: test001
                    // colour: test
                    // make: test
                    // model: test
                    // ownerLicence: 
                    // ownerFirstName: 
                    // ownerLastName: 
                    // ownerAddress: 
                    // ownerDOB: 
                    // offenderLicence: 
                    // offenderFirstName: owner2
                    // offenderLastName: owner2
                    // offenderAddress: test
                    // offenderDOB: today
                    // result: new vehicle, no new owner, no new offender, new ownership
            // no vehicle involved
                // testcase1: vehicle: null, owner: null        ; offender: new+licence
                    // offenderLicence: testtesttest0001
                    // offenderFirstName: owner1
                    // offenderLastName: owner1
                    // offenderAddress: test
                    // offenderDOB: today
                    // no new vehicle, no new owner, new offender, no new ownership
                // testcase2: vehicle: null, owner: null        ; offender: new-licence
                    // offenderLicence: 
                    // offenderFirstName: owner2
                    // offenderLastName: owner2
                    // offenderAddress: test
                    // offenderDOB: today
                    // no new vehicle, no new owner, new offender, no new ownership

                // testcase3: vehicle: null, owner: null        ; offender: old-licence
                    // offenderLicence: 
                    // offenderFirstName: Smith
                    // offenderLastName: Tony
                    // offenderAddress: 22 Avenue Road, Grantham
                    // offenderDOB: 2012-01-01
                    // no new vehicle, no new owner, no new offender, no new ownership
                
                // testcase4: vehicle: null, owner: null        ; offender: old+licence
                    // offenderLicence: ALLEN88K23KLR9B3
                    // offenderFirstName: Jennifer
                    // offenderLastName: Allen
                    // offenderAddress: 46 Bramcote Drive, Nottingham
                    // offenderDOB: 1994-03-12
                    // no new vehicle, no new owner, no new offender, no new ownership

            // illegal parking
                // testcase1: vehicle:  new, owner: null        ; offender: null; 
                    // vehicle licence: test001
                    // colour: test
                    // make: test
                    // model: test
                    // new vehicle, new null ownership
                // testcase2: vehicle:  new, owner: new+licence == offender: new+licence; 
                    // vehicle licence: test001
                    // colour: test
                    // make: test
                    // model: test
                    // ownerLicence: testtesttest0001
                    // ownerFirstName: owner1
                    // ownerLastName: owner1
                    // ownerAddress: test
                    // ownerDOB: today
                    // offenderLicence: testtesttest0001
                    // offenderFirstName: owner1
                    // offenderLastName: owner1
                    // offenderAddress: test
                    // offenderDOB: today
                    // new vehicle, new owner, no new offender, new ownership

                // testcase3: vehicle:  new, owner: new-licence == offender: new-licence; 
                    // vehicle licence: test001
                    // colour: test
                    // make: test
                    // model: test
                    // ownerLicence: 
                    // ownerFirstName: owner1
                    // ownerLastName: owner1
                    // ownerAddress: test
                    // ownerDOB: today
                    // offenderLicence: 
                    // offenderFirstName: owner1
                    // offenderLastName: owner1
                    // offenderAddress: test
                    // offenderDOB: today
                    // new vehicle, new owner, no new offender, new ownership

                // testcase4: vehicle:  new, owner: old-licence == offender: old-licence; 
                    // vehicle licence: test001
                    // colour: test
                    // make: test
                    // model: test
                    // ownerLicence: 
                    // ownerFirstName: Smith
                    // ownerLastName: Tony
                    // ownerAddress: 22 Avenue Road, Grantham
                    // ownerDOB: 2012-01-01
                    // offenderLicence: 
                    // offenderFirstName: Smith
                    // offenderLastName: Tony
                    // offenderAddress: 22 Avenue Road, Grantham
                    // offenderDOB: 2012-01-01
                    // new vehicle, no new owner, no new offender, new ownership

                // testcase5: vehicle:  new, owner: old+licence == offender: old+licence; 
                    // vehicle licence: test001
                    // colour: test
                    // make: test
                    // model: test
                    // ownerLicence: ALLEN88K23KLR9B3
                    // ownerFirstName: Jennifer
                    // ownerLastName: Allen
                    // ownerAddress: 46 Bramcote Drive, Nottingham
                    // ownerDOB: 1994-03-12
                    // offenderLicence: ALLEN88K23KLR9B3
                    // offenderFirstName: Jennifer
                    // offenderLastName: Allen
                    // offenderAddress: 46 Bramcote Drive, Nottingham
                    // offenderDOB: 1994-03-12
                    // new vehicle, no new owner, no new offender, new ownership

                // testcase6: vehicle:  old, owner: null        ; offender: null; 
                    // vehicle licence: TE12SLA
                    // colour: White
                    // make: Tesla
                    // model: Model3
                    // no new vehicle, no new owner, no new offender, new ownership
                    
                // testcase7: vehicle:  old, owner: new+licence == offender: new+licence; 
                    // vehicle licence: TE12SLA
                    // colour: White
                    // make: Tesla
                    // model: Model3
                    // ownerLicence: testtesttest0001
                    // ownerFirstName: owner1
                    // ownerLastName: owner1
                    // ownerAddress: test
                    // ownerDOB: today
                    // offenderLicence: testtesttest0001
                    // offenderFirstName: owner1
                    // offenderLastName: owner1
                    // offenderAddress: test
                    // offenderDOB: today
                    // no new vehicle, new owner, no new offender, new ownership

                // testcase8: vehicle:  old, owner: new-licence == offender: new-licence; 
                    // vehicle licence: TE12SLA
                    // colour: White
                    // make: Tesla
                    // model: Model3
                    // ownerFirstName: owner1
                    // ownerLastName: owner1
                    // ownerAddress: test
                    // ownerDOB: today
                    // offenderLicence: 
                    // offenderFirstName: owner1
                    // offenderLastName: owner1
                    // offenderAddress: test
                    // offenderDOB: today
                    // no new vehicle, new owner, no new offender, new ownership

                // testcase9(redundancy): vehicle:  old, owner: old+licence == offender: old+licence; 
                    // vehicle licence: TE12SLA
                    // colour: White
                    // make: Tesla
                    // model: Model3
                    // ownerLicence: ALLEN88K23KLR9B3
                    // ownerFirstName: Jennifer
                    // ownerLastName: Allen
                    // ownerAddress: 46 Bramcote Drive, Nottingham
                    // ownerDOB: 1994-03-12
                    // offenderLicence: ALLEN88K23KLR9B3
                    // offenderFirstName: Jennifer
                    // offenderLastName: Allen
                    // offenderAddress: 46 Bramcote Drive, Nottingham
                    // offenderDOB: 1994-03-12
                    // no new vehicle, no new owner, no new offender, new ownership

                // testcase10(redundancy): vehicle:  old, owner: old-licence == offender: old-licence; 
                    // vehicle licence: TE12SLA
                    // colour: White
                    // make: Tesla
                    // model: Model3
                    // ownerLicence: 
                    // ownerFirstName: Smith
                    // ownerLastName: Tony
                    // ownerAddress: 22 Avenue Road, Grantham
                    // ownerDOB: 2012-01-01
                    // offenderLicence: 
                    // offenderFirstName: Smith
                    // offenderLastName: Tony
                    // offenderAddress: 22 Avenue Road, Grantham
                    // offenderDOB: 2012-01-01

            // owner and offender are not the same person:
                // testcase1: vehicle:  new, owner:  new+licence != offender: new+licence; 
                    // vehicle licence: test001
                    // colour: test
                    // make: test
                    // model: test
                    // ownerLicence: testtesttest0001
                    // ownerFirstName: owner1
                    // ownerLastName: owner1
                    // ownerAddress: test
                    // ownerDOB: today
                    // offenderLicence: testtesttest0002
                    // offenderFirstName: owner2
                    // offenderLastName: owner2
                    // offenderAddress: test
                    // offenderDOB: today
                    // new vehicle, new owner, new offender, new ownership
            // check if ownership with null person id would be inserted twice
                // test it when testing above cases.
            // check existed non-null-person ownership
                // test it when testing above cases.

} catch (Exception $error) {
    // throw $error;
    
    $messages = $messages;
    if (empty($messages)) {
        renderErrorMessages([$error->getMessage()]);
    } else {
        renderErrorMessages($messages);
    }
    
    // header("location: ../error.php?errorMessage=".$error->getMessage());
}   
?>