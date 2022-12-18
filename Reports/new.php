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
    <div class="content">
    <h1>Create New Report</h1>
    <hr>
    <form action="newSubmit.php" method="post" class="new-report-form">
        <div class="new-report-form-data">
            <div>
                <h3>*Report General Information</h3>
                <table>
                    <tr>
                        <th>
                            *Statement:
                        </th>
                        <td><textarea required name="reportStatement" placeholder="enter a statement" rows="5" cols="30"
                                id="reportStatement"></textarea></td>
                    </tr>
                    <tr>
                        <th>*Report Date:</th>
                        <td>
                            <input required type="date" name="reportDate" id="reportDate">
                        </td>
                    </tr>
                    <tr>
                        <th>*Offence:</th>
                        <td>
                            <select required name="reportOffence" id="reportOffence">
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
                        </td>
                    </tr>
                </table>

            </div>

            <div>
                <h3>Vehicle Information</h3>
                <table>
                    <tr>
                        <th>*Registration number:</th>
                        <td><input type="text" name="vehicleLicence" id="vehicleLicence"></td>
                    </tr>
                    <tr>
                        <th>*Colour:</th>
                        <td><input type="text" name="vehicleColour" id="vehicleColour"></td>
                    </tr>
                    <tr>
                        <th>*Maker:</th>
                        <td><input type="text" name="vehicleMake" id="vehicleMake"></td>
                    </tr>
                    <tr>
                        <th>*Model:</th>
                        <td><input type="text" name="vehicleModel" id="vehicleModel"></td>
                    </tr>
                </table>
            </div>

            <div>
                <h3>Vehicle's Owner Information</h3>
                <table>
                    <tr>
                        <th>Driving licence:</th>
                        <td><input type="text" name="ownerLicence" id="ownerLicence"></td>
                    </tr>
                    <tr>
                        <th>*First Name:</th>
                        <td><input type="text" name="ownerFirstName" id="ownerFirstName"></td>
                    </tr>
                    <tr>
                        <th>*Last Name:</th>
                        <td><input type="text" name="ownerLastName" id="ownerLastName"></td>
                    </tr>
                    <tr>
                        <th>*Address:</th>
                        <td><input type="text" name="ownerAddress" id="ownerAddress"></td>
                    </tr>
                    <tr>
                        <th>*DOB:</th>
                        <td><input type="date" name="ownerDOB" id="ownerDOB"></td>
                    </tr>
                </table>
            </div>

            <div>
                <h3>Offender's Information</h3>
                <script>
                    function copyOwnerToOffender() {
                        for (id of ["Licence", "FirstName", "LastName", "Address", "DOB"]) {
                            document.getElementById("offender" + id).value = document.getElementById("owner" + id).value
                        }
                    }
                </script>
                <table>
                    <tr>
                        <th></th>
                        <td>
                            <div style="display:inline-block">
                                <button type="button" onclick="copyOwnerToOffender()">Use information from the
                                    owner</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Driving licence:</th>
                        <td><input type="text" name="offenderLicence" id="offenderLicence"></td>
                    </tr>
                    <tr>
                        <th>*First Name:</th>
                        <td><input type="text" name="offenderFirstName" id="offenderFirstName"></td>
                    </tr>
                    <tr>
                        <th>*Last Name:</th>
                        <td><input type="text" name="offenderLastName" id="offenderLastName"></td>
                    </tr>
                    <tr>
                        <th>*Address:</th>
                        <td><input type="text" name="offenderAddress" id="offenderAddress"></td>
                    </tr>
                    <tr>
                        <th>*DOB:</th>
                        <td><input type="date" name="offenderDOB" id="offenderDOB"></td>
                    </tr>
                </table>
            </div>
        </div>
        <input type="submit" value="submit">
        <!--  -->
    </form>
    </div>
</body>

</html>