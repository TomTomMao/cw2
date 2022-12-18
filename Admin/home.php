<?php $pageTitle = "Admin";
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

        
?>

<body>
    <div class="content">
    <h1>Admin Home</h1>
    <hr>
    <h2><a href="../Admin/newAccount.php" target="blank">create an account</a></h2>
    <hr>
    <h2><a href="../Reports/lookup.php" target="blank"> add fine for a report</a></h2>
    <hr>
    <h2><a href="../Accounts/lookup.php" target="blank"> Lookup accounts information</a></h2>


    <hr>
    <h2>Check the audit trails of a user</h2>
    <form action="../Accounts/myAuditTrails.php" method="POST">
        <table>
            <tr>
                <td>Username:</td>
                <td><input type="text" name="accountUsername"></td>
                <td><input type="submit" value="search" name="submit"></td>
            </tr>
        </table>
    </form>
    <hr>

    <h2>Check the audit trails per audit</h2>
    <form action="../Accounts/myAuditTrails.php" method="POST">
        <table>
            <tr>
                <td>Table name:</td>
                <td><select name="tableName" id="table-name-select">
                        <option value="People">People</option>
                        <option value="Vehicles">Vehicles</option>
                        <option value="Fines">Fines</option>
                        <option value="Incidents">Incidents</option>
                        <option value="Ownership">Ownership</option>
                        <option value="Accounts">Accounts</option>
                    </select></td>
                <td>Table id:</td>
                <td><input type="text" name="tableID"></td>
                <td><input type="submit" value="search" name="submit"></td>
            </tr>
        </table>
    </form>
    <hr>
    <h2><a href="../Accounts/myAuditTrails.php?q=accounts">Check account records</a></h2>
    </div>
</body>

</html>