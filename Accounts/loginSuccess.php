<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login Success</title>
</head>
<?php
    session_start();
    require("_account.php");
    $user = new User();
    if (!$user->isLoggedIn()) {
        header("location: notLoginError.html");
    } else {
        echo "<p style='color: green'>Log in successfully</p>"."<a href='home.php'>Go to Home Page</a>";
    }
?>