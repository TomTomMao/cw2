<?php $pageTitle = "My Account";
    require("../reuse/head.php");
?>

<?php // handle not login error
    session_start();
    require("_account.php");// there is a User class
    $user = new User();
    if (!$user->isLoggedIn()) {
        header("location: notLoginError.html"); // check if logged in
    }
?>
<body>
    <?php 
        require("../reuse/navbar.php");
    ?>
    <!-- <div class="navbar">
        <a href="../People/lookup.php">Lookup People</a>
        <a href="../Vehicles/lookup.php">Lookup Vehicles</a>
        <a href="../Vehicles/new.php">New Vehicles</a>
        <a href="../Reports/new.php">New report</a>
        <a href="../Accounts/home.php">My Account</a>
        <a href="../Accounts/logout.php">Log Out</a>
    </div> -->
    <!-- <hr> -->
    <div class="content">
    <h1>Welcome, <?php echo $user->getUsername();?></h1>
    <div>
        <table>
            <tr><td>Username:</td><td><?php echo $user->getUsername();?></td><td></td></tr>
            <tr><td>Password:</td><td>*********</td><td><a href="changePassword.php">reset</a></td></tr>
            <tr><td>Officer Name:</td><td><?php echo $user->getOfficerName();?></td><td></td></tr>
            <tr><td>Officer ID:</td><td><?php echo $user->getOfficerID();?></td><td></td></tr>
        </table>
    </div>
    </div>
</body>
</html>