<?php
    $pageTitle = "text_ownership";
    require("../head.php");
    require("_ownership.php");
    require("../Accounts/_account.php");
    require("_vehicles.php");
    require("../People/_people.php");
    session_start();
    $user = new User();
    $ownershipDB = new OwnershipDB($user->getUsername());
    // $ownerships = $ownershipDB->getOwnershipsByLicence("AD223NG");
    // print_r(count($ownerships));
    // foreach ($ownerships as $ownership) {
    //    echo $ownership->render();
    // }
    $newVehicle = new Vehicle("112923x", "yellow", "ford", "animal",NULL);
    $newPerson = new Person(NULL,"aaaassssddddffff" ,"iddadsfsf asdf sdaf", "1999-10-11", "steve jobs", NULL);
    $newVehicle->renderHtmlTable();
    $newPerson->render();
    $newOwnershipID = $ownershipDB->insertOwnershipWithNewVehicle($newVehicle, $newPerson);
    if ($newOwnershipID) {
        echo "<br><p>new ownership inserted, id:".$newOwnershipID."</p><br>";
    } else {
        echo "<br><p>no ownership inserted</p><br>";
    }
?>