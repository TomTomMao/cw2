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
                    </select>
                </td>
                <td>=</td>
                <td><input type="text" name="searchValue1" id="searchValue1"></td>
                <td>
                    <input type="checkbox" id="searchValue1Null" onclick="toggleNullValue('searchValue1')">
                    <label for="searchValue1Null" id="searchValue1Null">NULL</label>
                </td>
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
                <td><input type="submit" name="submit" value="Search" onclick="onSubmit()"></td>
            </tr>
        </table>
        <script>
            let reportJSONs = [];
            let lastPostJSON; // json
        </script>
    </form>
    <!-- get reports using php. the report would be saved into $reports -->
<?php
    if (isset($_POST['submit'])) {
            // echo "this is a post"; // debugging
        
            require("../reuse/_dbConnect.php");
            require("_reports.php");
            $conn = connectDB();
            $reportsDB = new ReportsDB($user, $conn);
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

            echo "\$searchCondition:<br>";// debugging
            print_r($searchCondition);// debugging
            echo "flag0<hr>";// debugging

        // DONE: Get reports according to the $searchCondition and then render reports

            // echo "flag1"; // debugging
            // print_r ($searchCondition); // debugging
            // echo "flag1"; // debugging
            $reports = $reportsDB->getReportsMultipleConditions($searchCondition);
            mysqli_close($conn); // disconnect
            // render reports
            if(isset($reports)) {
                $table = Report::renderGeneralTable($reports);
                if ($table) {
                    echo $table;
                } else {
                    echo "No report found";
                }
            }
            
        


            
        // DONE: push reports into javascript code
                if(!empty($reports)) {
                    foreach($reports as $report){
                        $reportJSON = $report->toJSON();
                        // echo "<br>pusing<br>";
                        echo "<script>reportJSONs.push(JSON.parse('".$reportJSON."'));</script>";
                    }
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

</script>
</body>

</html>