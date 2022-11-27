<?php 
    try { ?>
    <?php $pageTitle = "Lookup Vehicle";
        require_once("../head.php");
    ?>

    <?php // handle not login error
        session_start();
        require("../Accounts/_account.php");// there is a User class
        $user = new User();
        if (!$user->isLoggedIn()) {
            header("location: ../Accounts/notLoginError.html"); // check if logged in
        }
    ?>

    <?php    
        require_once("_ownership.php");
        require_once("../People/_people.php");
        require_once("_vehicles.php");
        $peopleDB = new peopleDB($user->getUsername());
        $ownershipDB = new OwnershipDB($user->getUserName());
        $vehiclesDB = new VehiclesDB($user->getUserName());
    ?>
    
    <?php 
        // function isVehicleLicenceInDB($vehicleLicence) {
        //     $user = new User();
        //     $ownershipDB = new OwnershipDB($user->getUsername());
        //     $ownershipsData = $ownershipDB->getVehicleByLicence($vehicleLicence);
        //     return !isset($ownershipsData["NULL"]);
        // } use $vehicleDB->isVehicleExists()
        function isPersonLicenceInDB($personLicence) {
            $user = new User();
            $peopleDB = new PeopleDB($user->getUsername());
            $people = $peopleDB->getPeopleByLicence($personLicence);
            return (!is_null($people));
        }
        function checkVehicleLicenceFormat($correct) {
            return $correct;
        }
        function isColourValid($colour) {
            if (strlen($colour)<=20 && strlen($colour)>0) {
                return true;
            } else {
                return false;
            }
        }
        function isMakerValid($maker) {
            if (strlen($maker)<=20 && strlen($maker)>0) {
                return true;
            } else {
                return false;
            }
        }
        function isModelValid($model) {
            if (strlen($model)<=20 && strlen($model)>0) {
                return true;
            } else {
                return false;
            }
        }
        function isFnameValid($fname) {
            if (strlen($fname)<=25 && strlen($fname)>0) {
                return true;
            } else {
                return false;
            }
        }
        function isLnameValid($lname) {
            if (strlen($lname)<=25 && strlen($lname)>0) {
                return true;
            } else {
                return false;
            }
        }
        function isaddressValid($address) {
            if (strlen($address)<=50 && strlen($address)>0) {
                return true;
            } else {
                return false;
            }
        }


        // state machine:
        // 
        // (a, b, c):
            // a: if vehicle's registration number is valid, 0 is false, 1 is true
            // b: if owner's registration number is valid, 0 is false, 1 is true
            // c: if not ignore the detail of the vehicle and the owner 0 is hidden, 1 is first, 2 is more
        
        // if not enter car registration number and is a post, 
            // set a = 0, c = 0;
        // else if entered
            // if vehicle's registration number is valid, (i.e., match the format and the vehicle is not in the database)
                // do this: tell user it is valid, and set state machine (0, ?, 0), and autofill the vehicle registration number in the input box
            // else (not valid)
                // do this: tell user what's going wrong (not match the format or already in the database), and set the state machine (1, ?, 0)
        

        // if not enter owner driving licence number and is a post,
            // set b = 0, c = 0;
        // else if entered
            // if the Driving licence is valid (i.e., match the format)
                // set the state machine (?, 1, 0)
                // if it match an entry in people table in database
                    // do this: get this entry from the database and autofill the details of person input box and the licence number
                // else (not match):
                    // do this: only autofill the licence number
            // else (not valid):
                // tell the user what is going wrong (not match the format), set the state machine (0, ?, 0)
            

        // if the state machine is (1, 1, 1)
                // make the input boxes of detail of both vehicle and owner visible
                // check the validity of the detail data not include numbers of the car and the person, because they are checked before.
                    // if they are all right, set the button to be submit
                    // otherwise the buttun should be check and feedback should be given.

        // if both vehicle number and owner number are valid, AND the the last post only let the server check if both numbers are valid, which means machine is (1, 1, 0)
                // make the input boxes of detail of both vehicle and owner visible
                // set the state machine (1, 1, 1)
        
        
        // if a && b == 0
            // hide the detailed box
        $isRegistrationNumberOK = false;
        $isDrivingLicenceOK = false;
        if (!isset($_SESSION["displayRound"])) {
            $_SESSION["displayRound"] = "hidden";
        }
        $REDMESSAGEPREFIX = "<div class='feedback-red'><div class='feedback-text-line'>";
        $GREENMESSAGEPREFIX = "<div class='feedback-green'><div class='feedback-text-line'>";
        $MESSAGESSUFFIX = "</div></div>";
        // put message between prefix and suffix. class style is in ../head.php.


        $vehicleMessage = "";
        
        $vehicleRegistrationNumber = ""; // data for autofill, other data of vehicle do not need to be autofilled as the vehicle should be new.
        (isset($_POST["vehicleColour"]) && $vehicleColour = $_POST["vehicleColour"]) || $vehicleColour = "";
        (isset($_POST["vehicleMake"]) && $vehicleMake = $_POST["vehicleMake"]) || $vehicleMake = "";
        (isset($_POST["vehicleModel"]) && $vehicleModel = $_POST["vehicleModel"]) || $vehicleModel = "";        
        
        $personMessage = "";


        // data for autofill
        $ownerLicenceValue = "";
        $ownerFirstNameValue = "";
        $ownerLastNameValue = "";
        $ownerAddress = "";
        $ownerPhotoID = "";
        $ownerDOB = "";


        $submitButtonValue = "check";
        $errorMessage="";

        $allCorrect = false;


        // for the views of vehicle
        if (isset($_POST["vehicleRegistrationNumber"]) && empty($_POST["vehicleRegistrationNumber"])) { // no vehicle registration number (0, ?, 0)
            $vehicleMessage = $vehicleMessage.$REDMESSAGEPREFIX."Please enter <b>Vehicle Registration Number</b>!".$MESSAGESSUFFIX;
            $_SESSION["displayRound"] = "hidden";
        }
        elseif (!empty($_POST["vehicleRegistrationNumber"])) { // the form has vehicle registration number
            if (!checkVehicleLicenceFormat($_POST["vehicleRegistrationNumber"])){ // (0, ?, 0) invalid
                $_SESSION["displayRound"] = "hidden";
                $vehicleMessage = $REDMESSAGEPREFIX."Can not Create this Vehicle: Wrong Registration Licence Format.".$MESSAGESSUFFIX;
            } elseif ($vehiclesDB->isVehicleExists($_POST["vehicleRegistrationNumber"])) {//  (0, ?, 0) vehicle already in db. invalid
                $_SESSION["displayRound"] = "hidden";
                $vehicleMessage = $REDMESSAGEPREFIX."Can not Create this Vehicle: Vehicle Already in Database.".$MESSAGESSUFFIX;
                $vehicleRegistrationNumber = $_POST["vehicleRegistrationNumber"]; // for later autofill
            } else { // valid (1, ?, ?)
                $isRegistrationNumberOK = true;
                $vehicleMessage = $GREENMESSAGEPREFIX."Vehicle is new".$MESSAGESSUFFIX; // vehicle not in db
                $vehicleRegistrationNumber = $_POST["vehicleRegistrationNumber"]; // for later autofill
            }
        } // else is not a post

        if (isset($_POST["ownerLicense"]) && empty($_POST["ownerLicense"])) { // not entered (?, 0, 0)
            $_SESSION["displayRound"] = "hidden";
            $isDrivingLicenceOK = false;
            $personMessage = $REDMESSAGEPREFIX."Please enter <b>Owner's licence number</b>".$MESSAGESSUFFIX;
        }
        elseif (!empty($_POST["ownerLicense"])) { // the form has owner's driving licence number 
            if (isPersonLicenceInDB($_POST["ownerLicense"])) {// the owner is in the database  (?, 1, ?)
                $isDrivingLicenceOK = true;
                $personMessage = $GREENMESSAGEPREFIX."Person Exists".$MESSAGESSUFFIX;

                $person = $peopleDB->getPeopleByLicence($_POST["ownerLicense"])[0];
                
                $ownerLicenceValue = $person->getLicence();
                $ownerFirstNameValue = $person->getFirstName();
                $ownerLastNameValue = $person->getLastName();
                $ownerAddress = $person->getAddress();
                $ownerPhotoID = $person->getPhotoID();
                $ownerDOB = $person->getDOB();

            } else { // the owner is not in the database(?, 1, 0)
                $isDrivingLicenceOK = true;
                $personMessage = $GREENMESSAGEPREFIX."The owner is new, please enter detail".$MESSAGESSUFFIX;

                $ownerLicenceValue = $_POST["ownerLicense"];
                $ownerFirstNameValue = $_POST["ownerFirstName"];
                $ownerLastNameValue = $_POST["ownerLastName"];
                $ownerAddress = $_POST["ownerAddress"];
                $ownerDOB = $_POST["ownerDOB"];
            }
        } // else is not a post

        if ($isRegistrationNumberOK && $isDrivingLicenceOK) { // if both ok, check which round is it
            if ($_SESSION["displayRound"]=="hidden") {
                $_SESSION["displayRound"] = "first";
            }
            $hiddenTag = "";
        } else { // if either registrationnumber or driving licence is not ok, hidden the rest of field.
            $hiddenTag = "hidden";
            $_SESSION["displayRound"] = "hidden";
        }
        // echo $_SESSION["displayRound"]; // debuging
        if ($_SESSION["displayRound"] == "first") { // the registration number and driving licence was just confirmed. No need to check detail, as the detail input box is just displayed.
            $_SESSION["displayRound"] = "more"; // set to more to indicate it is not the first time that the detail box is displayed.
            // echo "not checking"; // debuging
        }
        elseif ($_SESSION["displayRound"] == "more") {// this time need to check the detail
            // echo "checking"; // debuging
            $allCorrect = True;
            // check if they are all true
            if (!isColourValid($_POST["vehicleColour"])) {
                $colourMessage = $REDMESSAGEPREFIX."Colour format incorrect".$MESSAGESSUFFIX;
                $allCorrect = False;
            } else {
                $colourMessage = "";
            }
            if (!isMakerValid($_POST["vehicleMake"])) {
                $makerMessage = $REDMESSAGEPREFIX."Maker format incorrect".$MESSAGESSUFFIX;
                $allCorrect = False;
            } else {
                $makerMessage = "";
            }
            if (!isModelValid($_POST["vehicleModel"])) {
                $modelMessage = $REDMESSAGEPREFIX."Model format incorrect".$MESSAGESSUFFIX;
                $allCorrect = False;
            } else {
                $modelMessage = "";
            }
            if (!isFnameValid($_POST["ownerFirstName"])) {
                $fnameMessage = $REDMESSAGEPREFIX."First name format incorrect".$MESSAGESSUFFIX;
                $allCorrect = False;
            } else {
                $fnameMessage = "";
            }
            if (!isLnameValid($_POST["ownerLastName"])) {
                $lnameMessage = $REDMESSAGEPREFIX."Last name format incorrect".$MESSAGESSUFFIX;
                $allCorrect = False;
            } else {
                $lnameMessage = "";
            }
            if (!isaddressValid($_POST["ownerAddress"])) {
                $addressMessage = $REDMESSAGEPREFIX."Address format incorrect".$MESSAGESSUFFIX;
                $allCorrect = False;
            } else {
                $addressMessage = "";
            }
            $vehicleMessage = $vehicleMessage.$colourMessage.$makerMessage.
                                $modelMessage;
            $personMessage = $personMessage.$fnameMessage.$lnameMessage.
            $addressMessage;
            
        }
        if ($allCorrect && $_POST["action"]=="check and submit") { //if they are all true, and submit, then update it into the database.
            
            require("../config/db.inc.php");
            $conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
            if(mysqli_connect_errno()) { // cannot connect database
                die();
            } else { // success to connect database
                echo $_POST["ownerDOB"];
                mysqli_close($conn); // disconnect
                echo "success";
            }
        }



    ?>
<body>
    <div class="navbar">
        <a href="../People/lookup.php">Lookup People</a>
        <a href="../Vehicles/lookup.php">Lookup Vehicles</a>
        <a href="../Vehicles/new.php">New Vehicles</a>
        <a href="../Reports/new.php">New report</a>
        <a href="../Accounts/home.php">My Account</a>
    </div>
    <hr>
    <h1>Create New Vehicle</h1>
    <hr>
    <form action="new.php" method="post">
        <div>
            <h3>Vehicle Information</h3>
            <?php echo $vehicleMessage; ?>
            <div>
                *Registration number: <input type="text" name="vehicleRegistrationNumber" value="<?php echo $vehicleRegistrationNumber ?>">
            </div>
            <div <?php echo $hiddenTag; ?>>
                Colour: <input type="text" name="vehicleColour" id="vehicleColour" value="<?php echo $vehicleColour ?>">
                <!-- <select name="vehicleColour" id="vehicleColour">
                    <option value="white">white</option>
                    <option value="blue">blue</option>
                    <option value="green">green</option>
                    <option value="yellow">yellow</option>
                    <option value="red">red</option>
                    <option value="purple">purple</option>
                    <option value="black">black</option>
                    <option value="orange">orange</option>
                    <option value="silver">silver</option>
                </select> -->
            </div>
            <div <?php echo $hiddenTag; ?>>
                Maker:
                <input type="text" name="vehicleMake" value="<?php echo $vehicleMake ?>">
            </div>
            <div <?php echo $hiddenTag; ?>>
                Model:
                <input type="text" name="vehicleModel" value="<?php echo $vehicleModel ?>">
            </div>
        </div>
        <hr>
        <div>
            <h3>Owner Information</h3>
            <?php echo $personMessage; ?>
            <div>*Driving license: <input type="text" name="ownerLicense" value="<?php echo $ownerLicenceValue; ?>"></div>
            <div <?php echo $hiddenTag; ?>>First Name: <input type="text" name="ownerFirstName" value="<?php echo $ownerFirstNameValue; ?>"></div>
            <div <?php echo $hiddenTag; ?>>Last Name: <input type="text" name="ownerLastName" value="<?php echo $ownerLastNameValue; ?>"></div>
            <div <?php echo $hiddenTag; ?>>Address: <input type="text" name="ownerAddress" value="<?php echo $ownerAddress; ?>"></div>
            <div <?php echo $hiddenTag; ?>>Date of Birth: <input type="date" name="ownerDOB" value="<?php echo $ownerDOB; ?>"></div>
        </div>
        <!-- <input type="submit" name="action" value="<?php // echo $submitButtonValue; ?>"> -->
        <input class="button check-button" type="submit" name="action" value="check">
        <input class="button submit-button" type="submit" name="action" value="check and submit">
    </form>
    <div class="feedback-red"><?php echo $errorMessage; ?></div>
</body>
</html>

<?php 
    } catch (Exception $error) {
        header("location: ../error.php?errorMessage=".$error->getMessage());
    }
?>