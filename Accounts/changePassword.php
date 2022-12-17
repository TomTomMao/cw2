<?php 
    try {
        
    
    $pageTitle = "Change Password";
    require("../reuse/head.php");
?>

<?php

    session_start();
    require("_account.php");// there is a User class
    require_once("../reuse/_audit.php");
    require_once("../reuse/_dbConnect.php");
    $user = new User();
    $msg = "";
    $changeSuccess=false;
    $conn = connectDB();
    if (!$user->isLoggedIn()) {
        header("location: notLoginError.html"); // check if logged in
    }
    if ($_SERVER['REQUEST_METHOD']=="POST") {
        if (empty($_POST["newPassword"])) {
            $msg = "new password should't be empty";
        } elseif (empty($_POST["new2Password"])) {
            $msg = "confirmation password should't be empty";
        } elseif ($_POST["newPassword"]!=$_POST["new2Password"]) {
            $msg = "confirmation password is not the same as new password, please enter again!";
        } elseif (isset($_POST['newPassword'])) {
            if ($user->isValidPassword($_POST['newPassword'])) {
                $user->changePassword($_POST['newPassword']);
                
                // Add audit trail for password changing
                $auditDB = new AuditDB($user, $conn);
                $audit = new Audit("NULL", $user->getUsername(), "Accounts", $user->getUsername(), "NULL",  '{"accountUsername":"'.$user->getUsername().'"}', "CHANGEPASSWORD-SUCCESS", "now");
                $auditDB->insertAudit($audit);

                $msg = "password changed!";
                $changeSuccess=true;
            } else {
                // Add audit trail for password changing
                $auditDB = new AuditDB($user, $conn);
                $audit = new Audit("NULL", $user->getUsername(), "Accounts", $user->getUsername(), "NULL",  '{"accountUsername":"'.$user->getUsername().'"}', "CHANGEPASSWORD-FAILED", "now");
                $auditDB->insertAudit($audit);

                $msg = "invalid password";
            }
        }
    }
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
        <a href="../Accounts/logout.php">Log Out</a>
    </div>
    <hr> -->
    <h1>Changing Password</h1>
    <form action="changePassword.php" method="post">
    <div>
        <table>
            <tr>
                <td>Username:</td>
                <td><?php echo $user->getUsername();?></td>
                <td></td>
            </tr>
            <tr>
                <!-- // the effect of toggle password here referenced this page:
                https://www.w3schools.com/howto/howto_js_toggle_password.asp -->
                <td>New password:</td>
                <td>
                    
                        <input type="password" name="newPassword" id="newPassword">
                    
                </td>
                <td>
                    <script>
                        function togglePassword(id) {
                            password = document.getElementById(id);
                            if (password.type == "password") {
                                password.type = "text"
                            } else if (password.type == "text") {
                                password.type = "password"
                            }
                        }
                    </script>
                    <button type="button" onclick="togglePassword('newPassword')">show/hide</button>
                </td>
            </tr>
            <tr>
                <!-- // the effect of toggle password here referenced this page:
                https://www.w3schools.com/howto/howto_js_toggle_password.asp -->
                <td>Confirm password:</td>
                <td>
                    <script>
                        function comparePassword() { // not used yet
                            password1 = document.getElementById("newPassword").value;
                            password2 = document.getElementById("new2Password").value;
                            if (password1 != password2) {
                                document.getElementById("new2Password").style.color = "red";
                            } else {
                                document.getElementById("new2Password").style.color = "green";
                            }
                        }
                    </script>
                        <input type="password" name="new2Password" id="new2Password">
                    
                </td>
                <td>
                    <button type="button" onclick="togglePassword('new2Password')">show/hide</button>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" value="change password">
                </td>
            </tr>
        </table>
    </div>
    <?php 
    if (!empty($msg)) {

        echo "<div class='feedback-";
        if ($changeSuccess) {
            echo "green";
        } else {
            echo "red";
        }
        echo "'><div class='feedback-text-line'>".$msg."</div></div>";
    }
    ?>
    </form>
</body>

</html>
<?php
} catch (Exception $error) {
    require("../reuse/errorMessage.php");
    renderErrorMessage($error->getMessage());
}
?>