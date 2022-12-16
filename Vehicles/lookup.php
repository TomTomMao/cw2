<!-- This page use ownership object rather than vehicle object -->
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
        require_once("_ownership.php");
        require("../reuse/_dbConnect.php");
        require("../reuse/_audit.php");
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
        <form action="lookup.php" method="post">
            <table>
                <tr>
                    <td>Vehicle Licence:</td>
                    <td><input type="text" name="vehicleLicence" value="<?php if (!empty($_POST['vehicleLicence'])) { echo $_POST['vehicleLicence'];}?>"></td>
                    <td><input type="submit" value="search"></td>
                </tr>
            </table>
        </form>
        <script>let ownerships = [];</script> 
        <!-- The line above is for testing the php object method Ownership->toJSON and Vehicle->toJSON and Person->toJSON -->
        <?php
            if(!empty($_POST["vehicleLicence"])) {
                $conn = connectDB();
                $ownershipDB = new OwnershipDB($user,$conn);
                $auditDB = new AuditDB($user, $conn);
                $ownerships = $ownershipDB->getOwnershipsByLicence($_POST["vehicleLicence"]);

                // check and render the data
                echo "<hr>";
                $ownershipDiv = "";
                echo "Note: A vehicle might have multiple ownership, the one with largest id is the latested one that was created into the database.";

                $ownershipsIDKEY = array(); // an array that use ownershipID as keys, ownership object as values.
                // sort the ownership while record audit trials

                $vehicleAuditAdded = false;
                foreach ($ownerships as $ownership) {
                    $ownershipsIDKEY[$ownership->getID()] = $ownership; // pushing into $ownershipIDKEY

                    // record audit trial.
                    // record the audit trial for the ownership.
                    $tableID = $ownership->ID == NULL ? "NULL" : $ownership->ID; // Future update: the interface of different class is not designed well
                    $oldData = $tableID == "NULL" ? "NULL" : $ownership->toJSON(); 
                    $behaviourType = $tableID == "NULL" ? "SELECT-EMPTY" : "SELECT-FOUND";
                    $newData = $tableID == "NULL" ? '{"ownershipVehicleLicence": "'.$_POST["vehicleLicence"].'"}' : "NULL";
                    $audit = new Audit("NULL", $user->getUsername(), "Ownership", $tableID, $oldData, $newData, $behaviourType, "now");
                    $auditDB->insertAudit($audit);
                    $auditTime = $audit->auditTime;
                    
                    // record the audit trial for each owner
                    // if the ownership exists and the ownership has person, record audit trial for the owner
                    if ($ownership->ID != NULL && $ownership->hasPerson()) {
                        $audit = new Audit("NULL", $user->getUsername(), "People", $ownership->getPersonID()
                        , $ownership->getPerson()->toJSON(), "NULL", "SELECT-FOUND-SECONDARY", $auditTime);
                        $auditDB->insertAudit($audit);                        
                    } 

                    // record the audit trial for the vehicle
                    // 
                    if ($ownership->isVehicleIDNull() && $vehicleAuditAdded==false) {
                        // vehicle doesn't exists
                        echo "flag1";
                        $audit = new Audit("NULL", $user->getUsername(), "Vehicles", "NULL"
                        , "NULL", '{"vehicleLicence": "'.$_POST["vehicleLicence"].'"}', "SELECT-EMPTY-SECONDARY", $auditTime);
                        $auditDB->insertAudit($audit);
                    } elseif ($vehicleAuditAdded==false) {
                        // vehicle exists
                        // echo "flag2";
                        $audit = new Audit("NULL", $user->getUsername(), "Vehicles", $ownership->getVehicleID()
                        , $ownership->getVehicle()->toJSON(), "NULL", "SELECT-FOUND-SECONDARY", $auditTime);
                        $auditDB->insertAudit($audit);
                        $vehicleAuditAdded=true;
                    }

                }
                krsort($ownershipsIDKEY); // sort the array by key

                // render the ownership in decending order
                foreach ($ownershipsIDKEY as $k => $ownership) {
                    $ownershipDiv = $ownershipDiv.$ownership->render();
                }

                // push ownership json to the javascript variable named ownerships
                foreach ($ownershipsIDKEY as $k => $ownership) { // testing the php object method Ownership->toJSON and Vehicle->toJSON and Person->toJSON
                    echo "<script>ownerships.push(".$ownership->toJSON().");</script>";// testing the php object method Ownership->toJSON and Vehicle->toJSON and Person->toJSON
                }// testing the php object method Ownership->toJSON and Vehicle->toJSON and Person->toJSON
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