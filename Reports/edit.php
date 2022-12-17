<?php $pageTitle = "Edit Report";
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

                    // get offence data
                    $sql = "SELECT Offence_ID, Offence_description, Offence_maxFine, Offence_maxPoints FROM Offence;";
                    $offencesResults = mysqli_query($conn, $sql);
                    // echo $sql; // debugging
                    $offences = array();
                    while ($row = mysqli_fetch_assoc($offencesResults)) {
                        array_push($offences, $row);
                    }

                    require("_reports.php");
                    $reportsDB = new ReportsDB($user, $conn);
                    
                    // DONE: ACCORDING TO THE $_GET["id"], retrieve a report.
                        $reportID = $_GET["id"];
                        $report = $reportsDB->getReportByReportID($reportID);
                        // echo $report->renderGeneralRow(true); // debugging
                    // DONE: Assert the report is in the database
                        if ($report==false) {
                            header("Location: ../error.php?errorMessage=Report doesn't exist");
                            die();
                        }
                    // DONE: Assert the report belongs to the current user or the current user is admin
                    // echo "report accountUsername:".$report->accountUsername; //debugging
                    // echo "Your username:".$user->getUsername(); // debugging
                        if ($report->accountUsername != $user->getUsername() && !$user->isAdmin()) {
                            header("Location: ../error.php?errorMessage=You can't edit this report! Because this report was not created by you.");
                        }
                    // DONE: SAVE REPORT INTO A JAVASCRIPT VARIABLE as an javascript object: reportJSON
                        $reportJSON = $report->toJSON();
                        require_once("../reuse/_tools.php");
                        assignJSONToJs($reportJSON, "reportJSON");
                    // DONE: USING JS OBJECT TO AUTO FILL THE REPORT. (do it at flag-todo-3)
                    
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
    <h1>Edit Report</h1>
    <hr>
    <form action="newSubmit.php?edit=true&id=<?php echo $_GET['id']?>" method="post" class="new-report-form">
        <div class="new-report-form-data">
            <div>
                <h3>*Report General Information</h3>
                <table>
                    <tr>
                        <th>
                            *Statement:
                        </th>
                        <td><textarea name="reportStatement" placeholder="enter a statement" rows="5" cols="30"
                                id="reportStatement"></textarea></td>
                    </tr>
                    <tr>
                        <th>*Report Date:</th>
                        <td>
                            <input type="date" name="reportDate" id="reportDate">
                        </td>
                    </tr>
                    <tr>
                        <th>*Offence:</th>
                        <td>
                            <select name="reportOffence" id="reportOffence">
                                <?php 
                                foreach ($offences as $offence) {
                                    $offenceID = $offence["Offence_ID"];
                                    $offenceDescription = $offence["Offence_description"];
                                    $offenceMaxFine = $offence["Offence_maxFine"];
                                    $offenceMaxPoints = $offence["Offence_maxPoints"];
                                    echo '<option value="'.$offenceID.'" id=offence-'.$offenceID.'>'.'(Max Fine:'.$offenceMaxFine.'; Max Panelty Points:'.$offenceMaxPoints.') '.$offenceDescription.'</option>';
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
    <!-- flag-doto-3 -->
    <script>
        console.log(reportJSON);
        document.getElementById("reportStatement").value = reportJSON["incidentReport"] ? reportJSON["incidentReport"] : "";
        document.getElementById("reportDate").value = reportJSON["incidentDate"] ? reportJSON["incidentDate"] : "";
        document.getElementById("offence-" + reportJSON["offenceID"]).selected = true;
        document.getElementById("vehicleLicence").value = reportJSON["vehicleLicence"] ? reportJSON["vehicleLicence"] : "";
        document.getElementById("vehicleColour").value = reportJSON["vehicleColour"] ? reportJSON["vehicleColour"] : "";
        document.getElementById("vehicleMake").value = reportJSON["vehicleMake"] ? reportJSON["vehicleMake"] : "";
        document.getElementById("vehicleModel").value = reportJSON["vehicleModel"] ? reportJSON["vehicleModel"] : "";
        document.getElementById("ownerLicence").value = reportJSON["ownerLicence"] ? reportJSON["ownerLicence"] : "";
        document.getElementById("ownerFirstName").value = reportJSON["ownerName"].split(" ")[0] ? reportJSON["ownerName"].split(" ")[0] : "";
        document.getElementById("ownerLastName").value = reportJSON["ownerName"].split(" ")[1] ? reportJSON["ownerName"].split(" ")[1] : "";
        document.getElementById("ownerAddress").value = reportJSON["ownerAddress"] ? reportJSON["ownerAddress"] : "";
        document.getElementById("ownerDOB").value = reportJSON["ownerDOB"] ? reportJSON["ownerDOB"] : "";
        document.getElementById("offenderLicence").value = reportJSON["offenderLicence"] ? reportJSON["offenderLicence"] : "";
        document.getElementById("offenderFirstName").value = reportJSON["offenderName"].split(" ")[0] ? reportJSON["offenderName"].split(" ")[0] : "";
        document.getElementById("offenderLastName").value = reportJSON["offenderName"].split(" ")[1] ? reportJSON["offenderName"].split(" ")[1] : "";
        document.getElementById("offenderAddress").value = reportJSON["offenderAddress"] ? reportJSON["offenderAddress"] : "";
        document.getElementById("offenderDOB").value = reportJSON["offenderDOB"] ? reportJSON["offenderDOB"] : "";
    </script>
</body>

</html>