<?php $pageTitle = "Accounts";
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
        $conn = connectDB();

        $sql = "SELECT Account_username, Account_userType, Officer_name, Officer_ID FROM Accounts;";
        // echo $sql; // debugging
        $result = mysqli_query($conn, $sql);
        
?>
<body>
    <?php 
        echo "<table>
                <tr>
                    <th>username</th>
                    <th>user type</th>
                    <th>officer name</th>
                    <th>officer ID</th>
                </tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            $username = $row["Account_username"];
            $userType = $row["Account_userType"];
            $officerName = $row["Officer_name"];
            $officerID = $row["Officer_ID"];
            echo "<tr>
                    <td>$username</td>
                    <td>$userType</td>
                    <td>$officerName</td>
                    <td>$officerID</td>
                <tr>";
        }
        echo "</table>"
    ?>
</body>
</html>
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
