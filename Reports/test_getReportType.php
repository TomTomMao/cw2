<?php $pageTitle = "New Report";
    require("../reuse/head.php");
    print_r ($_POST);
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
    <h1>Create New Report(TESTING)</h1>
    <hr>

    // has vehicle, unknown offender, unknown owner;
    <form action="newSubmit.php" method="post" id="test1" style="border: black 1px solid; background-color: #eeeeee">
        <div>
            <h3>*Report General Information</h3>
            <div>
                *Statement: <textarea name="reportStatement" placeholder="enter a statement" rows="5" cols="30"
                    id="reportStatement">some value here</textarea>
            </div>
            <div>
                *Report Date:<input type="date" name="reportDate" id="reportDate" value="2022-01-01">
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
                *Registration number: <input type="text" name="vehicleLicence" id="vehicleLicence" value="1112223">
            </div>
            <div>
                *Colour:
                <select name="vehicleColour" id="vehicleColour">
                    <option value="white">white</option>
                    <option value=""></option>
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
                <input type="text" name="vehicleMake" id="vehicleMake" value="tesla">
            </div>
            <div>
                *Model:
                <input type="text" name="vehicleModel" id="vehicleModel" value="model x">
            </div>
        </div>
        <hr>
        <div>
            <h3>(empty)Vehicle's Owner Information</h3>
            <div>Driving licence: <input type="text" name="ownerLicence" id="ownerLicence"></div>
            <div>First Name: <input type="text" name="ownerFirstName" id="ownerFirstName"></div>
            <div>Last Name: <input type="text" name="ownerLastName" id="ownerLastName"></div>
            <div>Address: <input type="text" name="ownerAddress" id="ownerAddress"></div>
            <div>DOB: <input type="date" name="ownerDOB" id="ownerDOB"></div>
        </div>
        <hr>
        <div>
            <h3>(empty)Offender's Information</h3>
            <script>
                function copyOwnerToOffender() {
                    for (id of ["Licence", "FirstName", "LastName", "Address", "DOB"]) {
                        document.getElementById("offender" + id).value = document.getElementById("owner" + id).value
                    }
                }
            </script>
            <div style="display:inline-block">
                <button type="button" onclick="copyOwnerToOffender()">Don't click this in test cases</button>
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
    <br>

    // has vehicle, known offender, unknown owner;
    <form action="newSubmit.php" method="post" id="test2" style="border: black 1px solid; background-color: #eeeeee">
        <div>
            <h3>*Report General Information</h3>
            <div>
                *Statement: <textarea name="reportStatement" placeholder="enter a statement" rows="5" cols="30"
                    id="reportStatement">some value here</textarea>
            </div>
            <div>
                *Report Date:<input type="date" name="reportDate" id="reportDate" value="2022-01-01">
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
                *Registration number: <input type="text" name="vehicleLicence" id="vehicleLicence" value="1112223">
            </div>
            <div>
                *Colour:
                <select name="vehicleColour" id="vehicleColour">
                    <option value="white">white</option>
                    <option value=""></option>
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
                <input type="text" name="vehicleMake" id="vehicleMake" value="tesla">
            </div>
            <div>
                *Model:
                <input type="text" name="vehicleModel" id="vehicleModel" value="model x">
            </div>
        </div>
        <hr>
        <div>
            <h3>(empty)Vehicle's Owner Information</h3>
            <div>Driving licence: <input type="text" name="ownerLicence" id="ownerLicence"></div>
            <div>First Name: <input type="text" name="ownerFirstName" id="ownerFirstName"></div>
            <div>Last Name: <input type="text" name="ownerLastName" id="ownerLastName"></div>
            <div>Address: <input type="text" name="ownerAddress" id="ownerAddress"></div>
            <div>DOB: <input type="date" name="ownerDOB" id="ownerDOB"></div>
        </div>
        <hr>
        <div>
            <h3>*Offender's Information</h3>
            <script>
                function copyOwnerToOffender() {
                    for (id of ["Licence", "FirstName", "LastName", "Address", "DOB"]) {
                        document.getElementById("offender" + id).value = document.getElementById("owner" + id).value
                    }
                }
            </script>
            <div style="display:inline-block">
                <button type="button" onclick="copyOwnerToOffender()">Don't click this in test cases</button>
            </div>
            <div>Driving licence: <input type="text" name="offenderLicence" id="offenderLicence" value="1234123412341234"></div>
            <div>*First Name: <input type="text" name="offenderFirstName" id="offenderFirstName" value="wenwne"></div>
            <div>*Last Name: <input type="text" name="offenderLastName" id="offenderLastName" value="moa"></div>
            <div>*Address: <input type="text" name="offenderAddress" id="offenderAddress" value="kasd, sss, ddd"></div>
            <div>*DOB: <input type="date" name="offenderDOB" id="offenderDOB" value="1999-01-01"></div>
        </div>
        <hr>
        <input type="submit" value="submit">
        <!--  -->
    </form>
    <br>

    // has vehicle, known offender, known owner;
    <form action="newSubmit.php" method="post" id="test3" style="border: black 1px solid; background-color: #eeeeee">
        <div>
            <h3>*Report General Information</h3>
            <div>
                *Statement: <textarea name="reportStatement" placeholder="enter a statement" rows="5" cols="30"
                    id="reportStatement">some value here</textarea>
            </div>
            <div>
                *Report Date:<input type="date" name="reportDate" id="reportDate" value="2022-01-01">
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
                *Registration number: <input type="text" name="vehicleLicence" id="vehicleLicence" value="1112223">
            </div>
            <div>
                *Colour:
                <select name="vehicleColour" id="vehicleColour">
                    <option value="white">white</option>
                    <option value=""></option>
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
                <input type="text" name="vehicleMake" id="vehicleMake" value="tesla">
            </div>
            <div>
                *Model:
                <input type="text" name="vehicleModel" id="vehicleModel" value="model x">
            </div>
        </div>
        <hr>
        <div>
            <h3>*Vehicle's Owner Information</h3>
            <div>Driving licence: <input type="text" name="ownerLicence" id="ownerLicence" value="1111222233334444"></div>
            <div>*First Name: <input type="text" name="ownerFirstName" id="ownerFirstName" value="nenene"></div>
            <div>*Last Name: <input type="text" name="ownerLastName" id="ownerLastName" value="sa"></div>
            <div>*Address: <input type="text" name="ownerAddress" id="ownerAddress" value="deakins place, 111"></div>
            <div>*DOB: <input type="date" name="ownerDOB" id="ownerDOB" value="1999-10-10"></div>
        </div>
        <hr>
        <div>
            <h3>*Offender's Information</h3>
            <script>
                function copyOwnerToOffender() {
                    for (id of ["Licence", "FirstName", "LastName", "Address", "DOB"]) {
                        document.getElementById("offender" + id).value = document.getElementById("owner" + id).value
                    }
                }
            </script>
            <div style="display:inline-block">
                <button type="button" onclick="copyOwnerToOffender()">Don't click this in test cases</button>
            </div>
            <div>Driving licence: <input type="text" name="offenderLicence" id="offenderLicence" value="1234123412341234"></div>
            <div>*First Name: <input type="text" name="offenderFirstName" id="offenderFirstName" value="wenwne"></div>
            <div>*Last Name: <input type="text" name="offenderLastName" id="offenderLastName" value="moa"></div>
            <div>*Address: <input type="text" name="offenderAddress" id="offenderAddress" value="kasd, sss, ddd"></div>
            <div>*DOB: <input type="date" name="offenderDOB" id="offenderDOB" value="1999-01-01"></div>
        </div>
        <hr>
        <input type="submit" value="submit">
        <!--  -->
    </form>
    <br>

    // has vehicle, unknown offender, known owner;
    <form action="newSubmit.php" method="post" id="test4" style="border: black 1px solid; background-color: #eeeeee">
        <div>
            <h3>*Report General Information</h3>
            <div>
                *Statement: <textarea name="reportStatement" placeholder="enter a statement" rows="5" cols="30"
                    id="reportStatement">some value here</textarea>
            </div>
            <div>
                *Report Date:<input type="date" name="reportDate" id="reportDate" value="2022-01-01">
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
                *Registration number: <input type="text" name="vehicleLicence" id="vehicleLicence" value="1112223">
            </div>
            <div>
                *Colour:
                <select name="vehicleColour" id="vehicleColour">
                    <option value="white">white</option>
                    <option value=""></option>
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
                <input type="text" name="vehicleMake" id="vehicleMake" value="tesla">
            </div>
            <div>
                *Model:
                <input type="text" name="vehicleModel" id="vehicleModel" value="model x">
            </div>
        </div>
        <hr>
        <div>
            <h3>Vehicle's Owner Information</h3>
            <div>Driving licence: <input type="text" name="ownerLicence" id="ownerLicence" value="1111222233334444"></div>
            <div>*First Name: <input type="text" name="ownerFirstName" id="ownerFirstName" value="nenene"></div>
            <div>*Last Name: <input type="text" name="ownerLastName" id="ownerLastName" value="sa"></div>
            <div>*Address: <input type="text" name="ownerAddress" id="ownerAddress" value="deakins place, 111"></div>
            <div>*DOB: <input type="date" name="ownerDOB" id="ownerDOB" value="1999-10-10"></div>
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
                <button type="button" onclick="copyOwnerToOffender()">Don't click this in test cases</button>
            </div>
            <div>Driving licence: <input type="text" name="offenderLicence" id="offenderLicence"></div>
            <div>First Name: <input type="text" name="offenderFirstName" id="offenderFirstName"></div>
            <div>Last Name: <input type="text" name="offenderLastName" id="offenderLastName"></div>
            <div>Address: <input type="text" name="offenderAddress" id="offenderAddress"></div>
            <div>DOB: <input type="date" name="offenderDOB" id="offenderDOB"></div>
        </div>
        <hr>
        <input type="submit" value="submit">
    </form>
    <br>

    // has no vehicle, known offender, unknown owner;
    <form action="newSubmit.php" method="post" id="test5" style="border: black 1px solid; background-color: #eeeeee">
        <div>
            <h3>*Report General Information</h3>
            <div>
                *Statement: <textarea name="reportStatement" placeholder="enter a statement" rows="5" cols="30"
                    id="reportStatement">some value here</textarea>
            </div>
            <div>
                *Report Date:<input type="date" name="reportDate" id="reportDate" value="2022-01-01">
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
            <h3>(empty)Vehicle Information</h3>
            <div>
                Registration number: <input type="text" name="vehicleLicence" id="vehicleLicence">
            </div>
            <div>
                Colour:
                <select name="vehicleColour" id="vehicleColour">
                    <option value=""></option>
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
                Maker:
                <input type="text" name="vehicleMake" id="vehicleMake" >
            </div>
            <div>
                Model:
                <input type="text" name="vehicleModel" id="vehicleModel">
            </div>
        </div>
        <hr>
        <div>
            <h3>(empty)Vehicle's Owner Information</h3>
            <div>Driving licence: <input type="text" name="ownerLicence" id="ownerLicence"></div>
            <div>First Name: <input type="text" name="ownerFirstName" id="ownerFirstName"></div>
            <div>Last Name: <input type="text" name="ownerLastName" id="ownerLastName"></div>
            <div>Address: <input type="text" name="ownerAddress" id="ownerAddress"></div>
            <div>DOB: <input type="date" name="ownerDOB" id="ownerDOB"></div>
        </div>
        <hr>
        <div>
            <h3>*Offender's Information</h3>
            <script>
                function copyOwnerToOffender() {
                    for (id of ["Licence", "FirstName", "LastName", "Address", "DOB"]) {
                        document.getElementById("offender" + id).value = document.getElementById("owner" + id).value
                    }
                }
            </script>
            <div style="display:inline-block">
                <button type="button" onclick="copyOwnerToOffender()">Don't click this in test cases</button>
            </div>
            <div>Driving licence: <input type="text" name="offenderLicence" id="offenderLicence" value="1234123412341234"></div>
            <div>*First Name: <input type="text" name="offenderFirstName" id="offenderFirstName" value="haha"></div>
            <div>*Last Name: <input type="text" name="offenderLastName" id="offenderLastName" value="tika"></div>
            <div>*Address: <input type="text" name="offenderAddress" id="offenderAddress" value="sd,dsfa,s adf,sdd"></div>
            <div>*DOB: <input type="date" name="offenderDOB" id="offenderDOB" value="2000-01-11"></div>
        </div>
        <hr>
        <input type="submit" value="submit">
    </form>
</body>

</html>