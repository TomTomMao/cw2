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

        if (isset($_GET["id"])) {
            $reportID = $_GET["id"];
        } else {
            $reportID = "";
        }
?>

<body>
    <form action="addFine.php" method="post">
        <h1>Add Fine</h1>
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
                <td><input type="number" name="finePoint" id="finePoint"></td>
            </tr>
            <td><input type="submit" value="add"></td>
        </table>
    </form>
</body>
</html>
