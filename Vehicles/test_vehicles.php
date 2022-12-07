<?php 
    try { ?>
    <?php $pageTitle = "Lookup Vehicle";
        require_once("../reuse/head.php");
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
        require_once("_vehicles.php");
        $vehiclesDB = new vehiclesDB($user->getUsername());
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
        </div>
        <hr> -->
        <h1>Look Up Vehicles</h1>
        <hr>
        <form action="test_vehicles.php" method="post">
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
                
                $vehicles = $vehiclesDB->getVehiclesByLicence($_POST["vehicleLicence"]);

                // check and render the data
                echo "<hr>";
                    foreach($vehicles as $vehicle) {

                        $vehicleTable = $vehicle->renderHtmlTable();
                        echo $vehicleTable;
                    }

            } elseif(isset($_POST["vehicleLicence"])) {
                echo "<p style='color: red'>please enter a licence</p>";
            }
        ?>

        <?php 
            $newVehicle = new Vehicle("1441443", "Blue", "Ford", "Hourse", NULL);
            echo $newVehicle->renderHtmlTable();
            if ($vehiclesDB->insertNewVehicle($newVehicle)) {
                echo "insert success";
                $newVehicleID = $vehiclesDB->getVehiclesIDByLicence($newVehicle->getLicence())[0];
                $newVehicle->setID($newVehicleID);
                echo $newVehicle->renderHtmlTable();
            } else {
                echo "insert failed";
            }
        ?>
    
<?php 
    } catch (Exception $error) {
        echo $error->getMessage();
        // header("location: ../error.php?errorMessage=".$error->getMessage());
    }
?>
</body>
</html>