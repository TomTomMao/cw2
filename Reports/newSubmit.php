<?php // handle not login error
        session_start();
        require("../Accounts/_account.php");// there is a User class
        $user = new User();
        if (!$user->isLoggedIn()) {
            header("location: ../Accounts/notLoginError.html"); // check if logged in
        }
    ?>
<?php 

    if (empty($_POST)) {
        echo "error, empty post";
        die();
    } else {
        // check if mandatory fields is empty
        $mandatoryFields = ["reportStatement",
                            "reportDate",
                            "reportOffence",
                            "vehicleLicence",
                            "vehicleColour",
                            "vehicleMake",
                            "vehicleModel"];
                            // "ownerLicence",
                            // "ownerFirstName",
                            // "ownerLastName",
                            // "ownerAddress",
                            // "ownerDOB",
                            // "offenderLicence",
                            // "offenderFirstName",
                            // "offenderLastName",
                            // "offenderAddress",
                            // "offenderDOB"
        $allSet = true;
        foreach($mandatoryFields as $mandatoryField) {
            if (empty($_POST[$mandatoryField])) {
                echo "Error, missing field: ".$mandatoryField."<br>";
                $allSet = false; // empty happened
            }
        }
        if ($allSet==false) { // has empty madatory field, end
            echo "end at this line";
            die();
        }

        // check if data format are valid, if not show which data are invalid.
        // (not implemented)

        // assume all the data format are valid
        // try to insert data into the database
            require("../reuse/_dbConnect.php");
            $conn = connectDB();
            require("../People/_people.php");
            require("../Vehicles/_ownership.php");
            require("../Vehicles/_vehicles.php");
        // keep coding here, now go to refactor the mysqli connection of _vehicles and _ownership
        echo "everything is good";
    }
        




    echo "all good";
    print_r($_POST);
?>