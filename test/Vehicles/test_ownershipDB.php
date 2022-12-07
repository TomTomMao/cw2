<?php 
    session_start();
    // require_once("../../Vehicles/_ownership.php");
    require_once("../Vehicles/_vehicles.php");
    require_once("../People/_people.php");
    require_once("../reuse/_dbConnect.php");
    require_once("../Accounts/_account.php");
    $user = new User();
    if (!$user->isLoggedIn()) {
        header("location: ../Accounts/notLoginError.html"); // check if logged in
    }
    $conn = connectDB();
    $ownershipDB = new OwnershipDB($user, $conn);
    function testIsOwnershipInDBExists() {
        try {
            //code...
            $vehicle = new Vehicle("NULL", "NULL", "NULL", "NULL", "1");
            $person = new Person("12", "NULL", "NULL", "NULL", "NULL", "NULL");
            $ownership = new Ownership($vehicle, $person);
            $actualValue = $ownershipDB->isOwnershipInDB($ownership);
            $expectedValue = "1";
            if ($actualValue==$expectedValue) {
                echo "<hr>test testIsOwnershipInDBExists() passed!<hr>";
                return true;
            } else {
                echo "<hr>test testIsOwnershipInDBExists() failed!
                <br>expected value: $expectedValue
                <br>actual value: $actualValue<hr>";
                return false;
            }
        } catch (Exception $error) {
            echo "<hr>test testIsOwnershipInDBExists() failed!
                <br>error message: ".$error->getMessage()."<hr>";
            return false;
        }
    }
    function testIsOwnershipInDBNew() {
        $vehicle = new Vehicle("NULL", "NULL", "NULL", "NULL", "1");
        $person = new Person("20", "NULL", "NULL", "NULL", "NULL", "NULL");
        $ownership = new Ownership($vehicle, $person);
        $actualValue = $ownershipDB->isOwnershipInDB($ownership);
        $expectedValue = "false";
        if ($actualValue==$expectedValue) {
            echo "<hr>test testIsOwnershipInDBExists() passed!<hr>";
            return true;
        } else {
            echo "<hr>test testIsOwnershipInDBExists() failed!
            <br>expected value: $expectedValue
            <br>actual value: $actualValue<hr>";
            return false;
        }
    }

    
    function runAllTest($testFunctions) {
        $results = array(); // true or false;
        foreach($testFunctions as $testFunction) {
            array_push($results, $testFunction());
        }
        $allPass = true;
        foreach($results as $result) {
            $allPass = $allPass && $result;
        }
    }

    $testFunctions = [testIsOwnershipInDBExists,testIsOwnershipInDBNew];
    runAllTest($testFunctions);

?>
