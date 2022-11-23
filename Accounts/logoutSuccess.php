<?php $pageTitle = "Logout Success";
    require("../head.php");
?>
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