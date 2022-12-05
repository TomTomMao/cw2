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
        // processing the {offender form} -> {$offenderID, database change}
        // using {vehicleID, ownerID} -> {$ownershipID, database change} 
        // using {ownership, offenderID, report general data form} -> {reportID, database change}
    }

} catch (Exception $error) {
    echo '<div class="feedback-yellow"><div class="feedback-text-line">Error: '.$error->getMessage().'</div></div>';
    // header("location: ../error.php?errorMessage=".$error->getMessage());
}   
?>