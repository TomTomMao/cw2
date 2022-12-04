<!-- This page use ownership object rather than vehicle object -->
<?php 
    try { ?>
    <?php $pageTitle = "Lookup Vehicle";
        require_once("../head.php");
    ?>

    <?php // handle not login error
        session_start();
        require("../Accounts/_account.php");// there is a User class
        $user = new User();
        if (!$user->isLoggedIn()) {
            header("location: ../Accounts/notLoginError.html"); // check if logged in
        }
    ?>

    <?php    
        require_once("_ownership.php");
        require("../reuse/_dbConnect.php");
    ?>
    <body>
        <div class="navbar">
            <a href="../People/lookup.php">Lookup People</a>
            <a href="../Vehicles/lookup.php">Lookup Vehicles</a>
            <a href="../Vehicles/new.php">New Vehicles</a>
            <a href="../Reports/new.php">New report</a>
            <a href="../Accounts/home.php">My Account</a>
        </div>
        <hr>
        <h1>Look Up Vehicles</h1>
        <hr>
        <form action="lookup.php" method="post">
            <table>
                <tr>
                    <td>Vehicle Licence:</td>
                    <td><input type="text" name="vehicleLicence" value="<?php if (!empty($_POST['vehicleLicence'])) { echo $_POST['vehicleLicence'];}?>"></td>
                    <td><input type="submit" value="search"></td>
                </tr>
            </table>
        </form>
        
        
        <?php
            if(!empty($_POST["vehicleLicence"])) {
                $conn = connectDB();
                $ownershipDB = new OwnershipDB($user->getUsername(),$conn);
                $ownerships = $ownershipDB->getOwnershipsByLicence($_POST["vehicleLicence"]);

                // check and render the data
                echo "<hr>";
                $ownershipDiv = "";
                foreach ($ownerships as $ownership) {
                    $ownershipDiv = $ownershipDiv.$ownership->render();
                }
                echo $ownershipDiv;
                mysqli_close($conn);
            } elseif(isset($_POST["vehicleLicence"])) {
                echo "<p style='color: red'>please enter a licence</p>";
            }
        ?>
    
<?php 
    } catch (Exception $error) {
        header("location: ../error.php?errorMessage=".$error->getMessage());
    }
?>
</body>
</html>