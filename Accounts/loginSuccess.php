<?php $pageTitle = "Login Success";
    require("../reuse/head.php");
?>
<?php
    session_start();
    require("_account.php");
    $user = new User();
    if (!$user->isLoggedIn()) {
        header("location: notLoginError.html");
    } else {
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<p style='color: green' class='text-center'>Log in successfully</p>"."<center><a href='home.php'>Go to Home Page</a></center>";
    }
?>

</body>
</html>
