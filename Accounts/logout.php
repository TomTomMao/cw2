<?php
    session_start();
    require("_account.php");
    $user = new User();
    $user -> logOut();
    
    header("location: logoutSuccess.php")
?>
