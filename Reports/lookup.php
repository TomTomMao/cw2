<?php 
    try { 
        require("../reuse/errorMessage.php");?>
<?php $pageTitle = "Lookup People";
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
        // require_once("_report.php");
    ?>

<body>
    <!-- report container -->
    <div class="invisible" id="report-detail-container">
            <button id="report-detail-container-hide-button" onclick="document.getElementById('report-detail-container').classList=['invisible']">X</button>
            <div class="report-detail-information">
                <table class="report-detail-table">
                    <tr>
                        <td class="report-detail-table-header">accountUsername</td>
                        <td class="report-detail-table-data" id="report-detail-accountUsername"></td>
                    </tr>          
                    <tr>
                        <td class="report-detail-table-header">incidentDate</td>
                        <td class="report-detail-table-data" id="report-detail-incidentDate"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">incidentID</td>
                        <td class="report-detail-table-data" id="report-detail-incidentID"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">incidentReport</td>
                        <td class="report-detail-table-data" id="report-detail-incidentReport"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">offenceDescription</td>
                        <td class="report-detail-table-data" id="report-detail-offenceDescription"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">offenceID</td>
                        <td class="report-detail-table-data" id="report-detail-offenceID"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">offenceMaxFine</td>
                        <td class="report-detail-table-data" id="report-detail-offenceMaxFine"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">offenceMaxPoints</td>
                        <td class="report-detail-table-data" id="report-detail-offenceMaxPoints"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">offenderAddress</td>
                        <td class="report-detail-table-data" id="report-detail-offenderAddress"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">offenderDOB</td>
                        <td class="report-detail-table-data" id="report-detail-offenderDOB"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">offenderID</td>
                        <td class="report-detail-table-data" id="report-detail-offenderID"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">offenderLicence</td>
                        <td class="report-detail-table-data" id="report-detail-offenderLicence"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">offenderName</td>
                        <td class="report-detail-table-data" id="report-detail-offenderName"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">officerID</td>
                        <td class="report-detail-table-data" id="report-detail-officerID"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">officerName</td>
                        <td class="report-detail-table-data" id="report-detail-officerName"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">ownerAddress</td>
                        <td class="report-detail-table-data" id="report-detail-ownerAddress"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">ownerDOB</td>
                        <td class="report-detail-table-data" id="report-detail-ownerDOB"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">ownerID</td>
                        <td class="report-detail-table-data" id="report-detail-ownerID"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">ownerLicence</td>
                        <td class="report-detail-table-data" id="report-detail-ownerLicence"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">ownerName</td>
                        <td class="report-detail-table-data" id="report-detail-ownerName"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">ownershipID</td>
                        <td class="report-detail-table-data" id="report-detail-ownershipID"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">vehicleColour</td>
                        <td class="report-detail-table-data" id="report-detail-vehicleColour"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">vehicleID</td>
                        <td class="report-detail-table-data" id="report-detail-vehicleID"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">vehicleLicence</td>
                        <td class="report-detail-table-data" id="report-detail-vehicleLicence"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">vehicleMake</td>
                        <td class="report-detail-table-data" id="report-detail-vehicleMake"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">vehicleModel</td>
                        <td class="report-detail-table-data" id="report-detail-vehicleModel"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">fineID</td>
                        <td class="report-detail-table-data" id="report-detail-fineID"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">fineAmount</td>
                        <td class="report-detail-table-data" id="report-detail-fineAmount"></td>
                    </tr>
                    <tr>
                        <td class="report-detail-table-header">finePoints</td>
                        <td class="report-detail-table-data" id="report-detail-finePoints"></td>
                    </tr>


                </table>
            </div>
    </div>
    <script>
        function showReportDetail(incidentID) {
            document.getElementById("report-detail-container").classList=[];
            report = reportJSONs.filter(reportJSON=>reportJSON.incidentID==incidentID)[0];
            reportTable = document.querySelector(".report-detail-table")
            keys = Object.keys(report)
            for(key of keys) {
                text = report[key] == "" ? "NULL" : report[key];
                document.getElementById("report-detail-"+key).innerText = text;
            }
        }
    </script>
    <?php 
        require("../reuse/navbar.php");
    ?>
    <script>
        let values = {}
        function toggleNullValue(inputBoxID) {
            if (document.getElementById(inputBoxID+"Null").checked) {
                values[inputBoxID] = document.getElementById(inputBoxID).value
                document.getElementById(inputBoxID).value = "NULL"
            } else {
                if(values[inputBoxID]) {
                    document.getElementById(inputBoxID).value = values[inputBoxID]
                } else {
                    document.getElementById(inputBoxID).value = ""
                }
            }
        }
        function onSubmit() {
            if (document.querySelector("#columnName2 #empty").selected) {
                document.querySelector("#searchValue2").value="";
            }
            if (document.querySelector("#columnName3 #empty").selected) {
                document.querySelector("#searchValue3").value="";
            }
            if (document.querySelector("#searchValue2").value=="") {
                document.querySelector("#columnName2 #empty").selected=true;
            }
            if (document.querySelector("#searchValue3").value=="") {
                document.querySelector("#columnName3 #empty").selected=true;
            }
        }
    </script>
    <h1>Retrieve Reports</h1>
    <hr>
    <form action="lookup.php" method="post" >
        <table>
            <tr>
                <td></td>
                <td>Search By</td> 
                <td>
                    <select name="columnName1" id="columnName1">
                        <option value="offenderID" id="offenderID">Offender ID</option>
                        <option value="offenderName" id="offenderName">Offender Full Name</option>
                        <option value="offenderDOB" id="offenderDOB">Offender DOB</option>
                        <option value="offenderLicence" id="offenderLicence">Offender Licence</option>
                        <option value="ownerID" id="ownerID">Owner ID</option>
                        <option value="ownerName" id="ownerName">Owner Full Name</option>
                        <option value="ownerDOB" id="ownerDOB">Owner DOB</option>
                        <option value="ownerLicence" id="ownerLicence">Owner Licence</option>
                        <option value="vehicleID" id="vehicleID">Vehicle ID</option>
                        <option value="vehicleLicence" id="vehicleLicence">Vehicle Licence</option>
                        <option value="vehicleColour" id="vehicleColour">Vehicle Colour</option>
                        <option value="vehicleMake" id="vehicleMake">Vehicle Make</option>
                        <option value="vehicleModel" id="vehicleModel">Vehicle Model</option>
                        <option value="incidentDate" id="incidentDate">Incident Date</option>
                        <option value="incidentID" id="incidentID">Incident ID</option>
                        <option value="officerName" id="officerName">Officer Name</option>
                        <option value="officerID" id="officerID">Officer ID</option>
                        <option value="offenceID" id="offenceID">Offence ID</option>
                        <option value="offenceDescription" id="offenceDescription">Offence Description</option>
                    </select>
                </td>
                <td>=</td>
                <td><input type="text" name="searchValue1" id="searchValue1"></td>
                <td>
                    <input type="checkbox" id="searchValue1Null" onclick="toggleNullValue('searchValue1')">
                    <label for="searchValue1Null" id="searchValue1Null">NULL</label>
                </td>
                <td><button type="button" onclick="document.querySelector('#columnName1 #officerName').selected=true;document.getElementById('searchValue1').value=userInfo.officerName">Use my officer name</button></td>
            </tr>
            <tr>
                <td style="text-align: right">AND</td>
                <td>Search By</td> 
                <td>
                    <select name="columnName2" id="columnName2">
                        <option value="empty" id="empty"></option>
                        <option value="offenderID" id="offenderID">Offender ID</option>
                        <option value="offenderName" id="offenderName">Offender Full Name</option>
                        <option value="offenderDOB" id="offenderDOB">Offender DOB</option>
                        <option value="offenderLicence" id="offenderLicence">Offender Licence</option>
                        <option value="ownerID" id="ownerID">Owner ID</option>
                        <option value="ownerName" id="ownerName">Owner Full Name</option>
                        <option value="ownerDOB" id="ownerDOB">Owner DOB</option>
                        <option value="ownerLicence" id="ownerLicence">Owner Licence</option>
                        <option value="vehicleID" id="vehicleID">Vehicle ID</option>
                        <option value="vehicleLicence" id="vehicleLicence">Vehicle Licence</option>
                        <option value="vehicleColour" id="vehicleColour">Vehicle Colour</option>
                        <option value="vehicleMake" id="vehicleMake">Vehicle Make</option>
                        <option value="vehicleModel" id="vehicleModel">Vehicle Model</option>
                        <option value="incidentDate" id="incidentDate">Incident Date</option>
                        <option value="incidentID" id="incidentID">Incident ID</option>
                        <option value="officerName" id="officerName">Officer Name</option>
                        <option value="officerID" id="officerID">Officer ID</option>
                        <option value="offenceID" id="offenceID">Offence ID</option>
                        <option value="offenceDescription" id="offenceDescription">Offence Description</option>
                    </select>
                </td>
                <td>=</td>
                <td><input type="text" name="searchValue2" id="searchValue2"></td>
                <td>
                    <input type="checkbox" id="searchValue2Null" onclick="toggleNullValue('searchValue2')">
                    <label for="searchValue2Null" id="searchValue2Null">NULL</label>
                </td>
            </tr>
            <tr>
                <td style="text-align: right">AND</td>
                <td>Search By</td> 
                <td>
                    <select name="columnName3" id="columnName3">
                        <option value="empty" id="empty"></option>
                        <option value="offenderID" id="offenderID">Offender ID</option>
                        <option value="offenderName" id="offenderName">Offender Full Name</option>
                        <option value="offenderDOB" id="offenderDOB">Offender DOB</option>
                        <option value="offenderLicence" id="offenderLicence">Offender Licence</option>
                        <option value="ownerID" id="ownerID">Owner ID</option>
                        <option value="ownerName" id="ownerName">Owner Full Name</option>
                        <option value="ownerDOB" id="ownerDOB">Owner DOB</option>
                        <option value="ownerLicence" id="ownerLicence">Owner Licence</option>
                        <option value="vehicleID" id="vehicleID">Vehicle ID</option>
                        <option value="vehicleLicence" id="vehicleLicence">Vehicle Licence</option>
                        <option value="vehicleColour" id="vehicleColour">Vehicle Colour</option>
                        <option value="vehicleMake" id="vehicleMake">Vehicle Make</option>
                        <option value="vehicleModel" id="vehicleModel">Vehicle Model</option>
                        <option value="incidentDate" id="incidentDate">Incident Date</option>
                        <option value="incidentID" id="incidentID">Incident ID</option>
                        <option value="officerName" id="officerName">Officer Name</option>
                        <option value="officerID" id="officerID">Officer ID</option>
                        <option value="offenceID" id="offenceID">Offence ID</option>
                        <option value="offenceDescription" id="offenceDescription">Offence Description</option>
                    </select>
                </td>
                <td>=</td>
                <td><input type="text" name="searchValue3" id="searchValue3"></td>
                <td>
                    <input type="checkbox" id="searchValue3Null" onclick="toggleNullValue('searchValue3')">
                    <label for="searchValue3Null" id="searchValue3Null">NULL</label>
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><input type="submit" name="submit" value="Search" onclick="onSubmit()"></td>
            </tr>
        </table>
        <script>
            let reportJSONs = [];
            let lastPostJSON; // json
            let userInfo;
        </script>
    </form>
    <!-- get reports using php. the report would be saved into $reports -->
<?php
        // DONE: Push username, isAdmin into javascript variable
        $username = $user->getUsername();
        $isAdmin = $user->isAdmin() ? "true" : "false";
        $officerName = $user->getOfficerName();
        echo "<script>userInfo={username:'$username', isAdmin:$isAdmin, officerName:'$officerName'}</script>";
    if (isset($_POST['submit'])) {
            // echo "this is a post"; // debugging
        
            require("../reuse/_dbConnect.php");
            require("_reports.php");
            require_once("../reuse/_audit.php");
            require_once("../People/_people.php");
            require_once("../Vehicles/_vehicles.php");
            require_once("../Vehicles/_ownership.php");
            $conn = connectDB();
            $auditDB = new AuditDB($user, $conn);
            $reportsDB = new ReportsDB($user, $conn);
            $peopleDB = new PeopleDB($user, $conn);
            $vehiclesDB = new VehiclesDB($user, $conn);
            $ownershipDB = new OwnershipDB($user, $conn);

            // echo "\$_POST:<br>";// debugging
            // print_r($_POST); // debugging
            // echo "flag-1<hr>";// debugging
        // DONE: Put the post into javascript variable lastPost
            require_once("../reuse/_tools.php");
            $_POSTJSON = arrayToJSON($_POST);
            echo "<script>lastPostJSON=".$_POSTJSON."</script>";

        // DONE: error handling if user didn't enter anything but submitted the form
            // if user entered three empty value
            // echo "<hr>searchValue1:".$_POST["searchValue1"].";<hr>"; // debugging
            // echo "<hr>searchValue2:".$_POST["searchValue2"].";<hr>"; // debugging
            // echo "<hr>searchValue3:".$_POST["searchValue3"].".<hr>"; // debugging
            if (empty($_POST["searchValue1"]) && empty($_POST["searchValue2"]) && empty($_POST["searchValue3"])) {
                // raise error and feedback
                throw new Exception("Please enter at least one search value.");
            }
        // DONE: Processing forms, convert them into a $searchCondition list
            $searchCondition = array();
            if (!empty($_POST["columnName1"]) && !empty($_POST["searchValue1"])) {
                $columnName1 = ReportsDB::getWhereName($_POST["columnName1"]);
                $searchValue1 = $_POST["searchValue1"];
                array_push($searchCondition, ["columnName"=>$columnName1, "searchValue"=>$searchValue1]);
            }
            if (!empty($_POST["columnName2"]) && !empty($_POST["searchValue2"])) {
                $columnName2 = ReportsDB::getWhereName($_POST["columnName2"]);
                $searchValue2 = $_POST["searchValue2"];
                array_push($searchCondition, ["columnName"=>$columnName2, "searchValue"=>$searchValue2]);
            }
            if (!empty($_POST["columnName3"]) && !empty($_POST["searchValue3"])) {
                $columnName3 = ReportsDB::getWhereName($_POST["columnName3"]);
                $searchValue3 = $_POST["searchValue3"];
                array_push($searchCondition, ["columnName"=>$columnName3, "searchValue"=>$searchValue3]);
            }

            // echo "\$searchCondition:<br>";// debugging
            // print_r($searchCondition);// debugging
            // echo "flag0<hr>";// debugging

        // DONE: Get reports according to the $searchCondition and then render reports

            // echo "flag1"; // debugging
            // print_r ($searchCondition); // debugging
            // echo "flag1"; // debugging
            $reports = $reportsDB->getReportsMultipleConditions($searchCondition);
            // render reports
            if(isset($reports)) {
                $table = Report::renderGeneralTable($reports);
                if ($table) {
                    echo $table;
                } else {
                    echo "No report found";
                }
            }
        
        // TODO: INSERT AUDIT TRAILS FOR THE REPORT SEARCHED
            // sudo: 
                // if $reports is not empty:
                    // add audit for each report (SELECT-FOUND)
                    // create a set of ownership id involved
                    // create a set of people id involved in these reports and ownerships.
                    // create a set of vehicle licence involved in these ownerships.
                    // get these ownerships by id
                    // get these people by id
                    // get these vehicles by licence (because i didn't implement getVehicleByID in VehiclesDB class)
                    // add audit for these ownerships (SELECT-FOUND-SECONDARY)
                    // add audit for these people (SELECT-FOUND-SECONDARY)
                    // add audit for these vehicles (SELECT-FOUND-SECONDARY)
            date_default_timezone_set("Europe/London");
            $auditTime = date("y-m-d H:i:s");

            // add audit for each report (SELECT-FOUND)
            if (!empty($reports)) {
                
                // add audit for the incident (SELECT-FOUND)
                foreach($reports as $report) {
                    $audit = new Audit("NULL", $user->getUsername(), "Incidents", strval($report->incidentID), $report->toJSON(), "NULL", "SELECT-FOUND", $auditTime);
                    $auditDB->insertAudit($audit);
                }

                // create a set of ownership id
                // create a set of person id involved in these reports and ownerships.
                // create a set of vehicle licence involved in these ownerships.
                $ownershipIDs = [];
                $personIDs = [];
                $vehicleLicences = [];
                foreach($reports as $report) {
                    if (in_array($report->ownershipID, $ownershipIDs, true)==false) {
                        array_push($ownershipIDs, $report->ownershipID);  
                    } 
                    if (in_array($report->ownerID, $personIDs, true)==false) {
                        array_push($personIDs, $report->ownerID);  
                    } 
                    if (in_array($report->offenderID, $personIDs, true)==false) {
                        array_push($personIDs, $report->offenderID);  
                    } 
                    if (in_array($report->vehicleLicence, $vehicleLicences, true)==false) {
                        array_push($vehicleLicences, $report->vehicleLicence);  
                    } 
                }
                // print_r($ownershipIDs); // debugging
                // print_r($personIDs); // debugging
                // print_r($vehicleLicences); // debugging
                // get these ownerships by id
                $ownerships = [];
                foreach($ownershipIDs as $ownershipID) {
                    array_push($ownerships, $ownershipDB->getOwnershipByID($ownershipID));
                }
                // echo "<hr>#ownerships:".count($ownerships)."<hr>"; // debugging

                // get these people by id
                $people = [];
                foreach($personIDs as $personID) {
                    array_push($people, $peopleDB->getPersonByID($personID));
                }
                // echo "<hr>#people:".count($people)."<hr>"; // debugging

                // get these vehicles by vehicleLicences
                $vehicles = [];
                foreach($vehicleLicences as $vehicleLicence) {
                    array_push($vehicles, $vehiclesDB->getVehiclesByLicence($vehicleLicence)[0]);
                }
                // echo "<hr>#vehicles:".count($vehicles)."<hr>"; // debugging

                // add audit for these ownerships (SELECT-FOUND-SECONDARY)
                foreach($ownerships as $ownership) {
                    $ownershipAudit = new Audit("NULL", $user->getUsername(), "Ownership", strval($ownership->ID), $ownership->toJSON(), "NULL", "SELECT-FOUND-SECONDARY", $auditTime);
                    $auditDB->insertAudit($ownershipAudit);
                }
                // add audit for these people (SELECT-FOUND-SECONDARY)
                foreach($people as $person) {
                    $personAudit = new Audit("NULL", $user->getUsername(), "People", strval($person->ID), $person->toJSON(), "NULL", "SELECT-FOUND-SECONDARY", $auditTime);
                    $auditDB->insertAudit($personAudit);
                }
                // add audit for these vehicles (SELECT-FOUND-SECONDARY)
                foreach($vehicles as $vehicle) {
                    // echo $vehicle->toJSON()."<br>"; // debugging
                    $vehicleAudit = new Audit("NULL", $user->getUsername(), "Vehicles", strval($vehicle->ID), $vehicle->toJSON(), "NULL", "SELECT-FOUND-SECONDARY", $auditTime);
                    $auditDB->insertAudit($vehicleAudit);
                }
                
            } else {
                // add audit for incident (SELECT-EMPTY)
                $audit = new Audit("NULL", $user->getUsername(), "Incidents", "NULL", "NULL", json_encode($searchCondition), "SELECT-EMPTY", $auditTime);
                // echo json_encode($searchCondition); //debugging
                $auditDB->insertAudit($audit);
            }

            
            
            mysqli_close($conn); // disconnect
            
        // DONE: push reports into javascript code
            if(!empty($reports)) {
                foreach($reports as $report){
                    $reportJSON = $report->toJSON();
                    // echo "<br>pusing<br>";
                    echo "<script>reportJSONs.push($reportJSON);</script>";
                }
            } else {
            }
        
    }else {
            // print_r($_POST); // debugging
            // echo "this is not a post"; // debugging
    }

?>

    <?php 
    } catch (Exception $error) {
        // throw $error; // debugging
        // print_r($error); // debugging
        renderErrorMessages([$error->getMessage()]);
    }
?>
<script>
    // auto fill using javascript
    document.querySelector("#columnName1" + " #" + lastPostJSON.columnName1).selected = true;
    document.querySelector("#columnName2" + " #" + lastPostJSON.columnName2).selected = true;
    document.querySelector("#columnName3" + " #" + lastPostJSON.columnName3).selected = true;
    document.getElementById("searchValue1").value = lastPostJSON.searchValue1;
    document.getElementById("searchValue2").value = lastPostJSON.searchValue2;
    document.getElementById("searchValue3").value = lastPostJSON.searchValue3;
    if (lastPostJSON.searchValue1=="NULL") {
        document.getElementById("searchValue1Null").checked=true;
    }
    if (lastPostJSON.searchValue2=="NULL") {
        document.getElementById("searchValue2Null").checked=true;
    }
    if (lastPostJSON.searchValue3=="NULL") {
        document.getElementById("searchValue3Null").checked=true;
    }

    // auto delete input box if the field name becomes empty
    function setEditLink() {
        // disable the edit and add fine links of the reports which can not be edit by the user.
        username = userInfo.username
        isAdmin = userInfo.isAdmin
        if (isAdmin) {
            return
        } else {
            // forbid edit report links
            for (report of reportJSONs) {
                if (report['accountUsername']!=username) {
                    console.log(document.getElementById("edit-"+report["incidentID"]))
                    document.getElementById("edit-"+report["incidentID"]).classList=["forbidden-link"]
                }
            }
            // forbid all add fine links
            for (addFineTag of Array.from(document.getElementsByClassName("add-fine"))){
                addFineTag.classList = ['add-fine forbidden-link']
            }
        }
    }
    setEditLink();
</script>
</body>

</html>