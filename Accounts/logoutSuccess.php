<?php $pageTitle = "Logout Success";
    require("../reuse/head.php");
?>
<?php
    session_start();
    require("_account.php");
    $user = new User();
    if ($user->isLoggedIn()) {
        echo "You shouldn't be here!";
    } else {
        
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<p style='color: green' class='text-center'>Log out successfully</p>"."<center><a href='login.php'>Go Login</a></center>";
    }
?>