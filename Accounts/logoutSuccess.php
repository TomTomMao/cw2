<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Logout Success</title>
</head>
<?php
    session_start();
    require("_account.php");
    $user = new User();
    if ($user->isLoggedIn()) {
        echo "You shouldn't be here!";
    } else {
        echo "<p style='color: green'>Log out successfully</p>"."<a href='login.php'>Go Login</a>";
    }
?>