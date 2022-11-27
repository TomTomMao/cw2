<?php
    require("../head.php");
    require("_ownership.php");
    require("../Accounts/_account.php");
    session_start();
    $user = new User();
    $ownershipDB = new OwnershipDB($user->getUsername());
    $ownerships = $ownershipDB->getOwnershipsByLicence("AD223NG");
    print_r(count($ownerships));
    foreach ($ownerships as $ownership) {
       echo $ownership->render();
    }
?>