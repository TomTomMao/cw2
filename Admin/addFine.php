<?php $pageTitle = "Add Fine to Report";
    require("../reuse/head.php");
    session_start();
        require("../Accounts/_account.php");// there is a User class
        
        $user = new User();
        if (!$user->isLoggedIn()) {
            header("location: ../Accounts/notLoginError.html"); // check if logged in
        } elseif (!$user->isAdmin()) {
            header("location: ../error.php?errorMessage=You are not an admin, so you are not allowed to access this page."); // check if logged in
        }

        require("../reuse/navbar.php");
        require_once("../reuse/_dbConnect.php");
        require_once("../reuse/_audit.php");
        require_once("../Reports/_reports.php");
        $conn = connectDB();
        $auditDB = new AuditDB($user, $conn);
        $reportsDB = new ReportsDB($user, $conn);

        if (isset($_GET["id"])) {
            $reportID = $_GET["id"];
        } else {
            $reportID = "";
        }

        function isReportHasFine($conn, $reportID) {
            $sql = "SELECT * FROM Fines WHERE Incident_ID = $reportID";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result)>0) {
                return true;
            } else {
                return false;
            }
        }
        if (isReportHasFine($conn, $reportID)) {
            throw new Exception("You shouldn't be here, as the report already has a fine.");
        }
?>

<?php 
    if (isset($_POST["submit"])) {
        // echo "this is a post"; // debugging
        // check if the table is empty or not. Note: if the value is 0, it is not empty.
        if (empty($_POST["reportID"])) {
            throw new Exception("Empty Report ID!", 1);
        } elseif (!isset($_POST["fineAmount"]) || ($_POST['fineAmount']!= 0 && empty($_POST["fineAmount"]))) {
            throw new Exception("You didn't enter fine amount!", 1);
        } elseif (!isset($_POST["finePoints"]) || ($_POST['finePoints']!= 0 && empty($_POST["finePoints"]))) {
            throw new Exception("You didn't enter fine points!", 1);
        }

        
        
        $reportID = $_POST["reportID"];
        $fineAmount = $_POST["fineAmount"];
        $finePoints = $_POST["finePoints"];

        // CHECK IF THE REPORT HAS FINE.
        if (isReportHasFine($conn, $reportID)) {
            throw new Exception("Failed to add fine, because the report already has a fine.");
        }
        
        try {
            $oldReport = $reportsDB->getReportByReportID($reportID);
            $sql = "INSERT INTO Fines (Fine_amount, Fine_points, Incident_ID) VALUES ($fineAmount, $finePoints, $reportID)";
            $result = mysqli_query($conn, $sql);
            $newFineID = mysqli_insert_id($conn);
            $newReport = $reportsDB->getReportByReportID($reportID);
            
            // add audit trail (INSERT-SUCCESS)
            $audit = new Audit("NULL", $user->getUsername(), "Fines", strval($newFineID), $oldReport->toJSON(), $newReport->toJSON(), "INSERT-SUCCESS", "now");
            $auditDB->insertAudit($audit);

            mysqli_close($conn);

            header("location: addFineSuccess.php");
        } catch (Exception $error) {
            throw $error;
        }


    } else {
        // echo "not a post"; // debugging
    }
?>
<body>
    <div class="content">
    <form action="addFine.php?id=<?php echo $reportID;?>" method="post">
        <h1>Admin: Add Fine</h1>
        <table>
            <tr>
                <td>Report ID:</td>
                <td><input type="number" name="reportID" id="reportID" value="<?php echo $reportID;?>"></td>
            </tr>
            <tr>
                <td>Fine Amount:</td>
                <td><input type="number" name="fineAmount" id="fineAmount"></td>
            </tr>
            <tr>
                <td>Fine Point:</td>
                <td><input type="number" name="finePoints" id="finePoints"></td>
            </tr>
            <td><input type="submit" value="add" name="submit"></td>
        </table>
    </form>
    </div>
</body>
</html>