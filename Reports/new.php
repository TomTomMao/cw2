<?php $pageTitle = "New Report";
    require("../head.php");
    print_r ($_POST);
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
    <h1>Create New Report(Vehicle involved)</h1>
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
                    <option value="$offenceId1">$offenceName1</option>
                    <option value="$offenceId2">$offenceName2</option>
                    <option value="$offenceId3">$offenceName3</option>
                    <option value="$offenceId4">$offenceName4</option>
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
                <select name="vehicleColour" id="vehicleColour">
                    <option value="white">white</option>
                    <option value="blue">blue</option>
                    <option value="green">green</option>
                    <option value="yellow">yellow</option>
                    <option value="red">red</option>
                    <option value="purple">purple</option>
                    <option value="black">black</option>
                    <option value="orange">orange</option>
                    <option value="silver">silver</option>
                </select>
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