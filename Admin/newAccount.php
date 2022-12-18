<?php $pageTitle = "Create new account";
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
        

?>
<body>
    <div class="content">
    <form action="newAccountSubmit.php" method="post">
        <h1>Admin: Create Account</h1>
        <table>
            <tr>
                <td>Account Username:</td>
                <td><input type="text" name="accountUsername" id="accountUsername"></td>
            </tr>
            <tr>
                <td>Officer First Name:</td>
                <td><input type="text" name="officerFirstName" id="officerFirstName"></td>
            </tr>
            <tr>
                <td>Officer Last Name:</td>
                <td><input type="text" name="officerLastName" id="officerLastName"></td>
            </tr>
            <tr>
                <td>Officer ID:</td>
                <td><input type="text" name="officerID" id="officerID"></td>
            </tr>
            <td><input type="submit" value="create account" name="submit"></td>
        </table>
    </form>
    </div>
</body>
</html>
<?php 
?>
