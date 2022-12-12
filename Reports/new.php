<?php $pageTitle = "New Report";
    require("../reuse/head.php");
    session_start();
        require("../Accounts/_account.php");// there is a User class
        
        $user = new User();
        if (!$user->isLoggedIn()) {
            header("location: ../Accounts/notLoginError.html"); // check if logged in
        }



?>
<?php 
                    require("../reuse/_dbConnect.php");
                    $conn = connectDB();
                    $sql = "SELECT Offence_ID, Offence_description, Offence_maxFine, Offence_maxPoints FROM Offence;";
                    $offencesResults = mysqli_query($conn, $sql);
                    // echo $sql; // debugging
                    $offences = array();
                    while ($row = mysqli_fetch_assoc($offencesResults)) {
                        array_push($offences, $row);
                    }
                    mysqli_close($conn);
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
    </div> -->
    <hr>
    <h1>Create New Report</h1>
    <hr>
    <form action="newSubmit.php" method="post">
        <div>
            <h3>*Report General Information</h3>
            <div>
                *Statement: <textarea name="reportStatement" placeholder="enter a statement" rows="5" cols="30"
                    id="reportStatement"></textarea>
            </div>
            <div>
                *Report Date:<input type="date" name="reportDate" id="reportDate">
            </div>
            <div>
                *Offence:
                
                <select name="reportOffence" id="reportOffence">
                    <?php 
                        foreach ($offences as $offence) {
                            $offenceID = $offence["Offence_ID"];
                            $offenceDescription = $offence["Offence_description"];
                            $offenceMaxFine = $offence["Offence_maxFine"];
                            $offenceMaxPoints = $offence["Offence_maxPoints"];
                            echo '<option value="'.$offenceID.'">'.'(Max Fine:'.$offenceMaxFine.'; Max Panelty Points:'.$offenceMaxPoints.') '.$offenceDescription.'</option>';
                        }
                    ?>
                </select>
            </div>
        </div>
        <hr>
        <div>
            <h3>*Vehicle Information</h3>
            <div>
                *Registration number: <input type="text" name="vehicleLicence" id="vehicleLicence">
            </div>
            <div>
                *Colour:
                <input type="text" name="vehicleColour" id="vehicleColour">

            </div>
            <div>
                *Maker:
                <input type="text" name="vehicleMake" id="vehicleMake">
            </div>
            <div>
                *Model:
                <input type="text" name="vehicleModel" id="vehicleModel">
            </div>
        </div>
        <hr>
        <div>
            <h3>Vehicle's Owner Information</h3>
            <div>Driving licence: <input type="text" name="ownerLicence" id="ownerLicence"></div>
            <div>First Name: <input type="text" name="ownerFirstName" id="ownerFirstName"></div>
            <div>Last Name: <input type="text" name="ownerLastName" id="ownerLastName"></div>
            <div>Address: <input type="text" name="ownerAddress" id="ownerAddress"></div>
            <div>DOB: <input type="date" name="ownerDOB" id="ownerDOB"></div>
        </div>
        <hr>
        <div>
            <h3>Offender's Information</h3>
            <script>
                function copyOwnerToOffender() {
                    for (id of ["Licence", "FirstName", "LastName", "Address", "DOB"]) {
                        document.getElementById("offender" + id).value = document.getElementById("owner" + id).value
                    }
                }
            </script>
            <div style="display:inline-block">
                <button type="button" onclick="copyOwnerToOffender()">Use information from the owner</button>
            </div>
            <div>Driving licence: <input type="text" name="offenderLicence" id="offenderLicence"></div>
            <div>First Name: <input type="text" name="offenderFirstName" id="offenderFirstName"></div>
            <div>Last Name: <input type="text" name="offenderLastName" id="offenderLastName"></div>
            <div>Address: <input type="text" name="offenderAddress" id="offenderAddress"></div>
            <div>DOB: <input type="date" name="offenderDOB" id="offenderDOB"></div>
        </div>
        <hr>
        <input type="submit" value="submit">
        <!--  -->
    </form>

</body>

</html>