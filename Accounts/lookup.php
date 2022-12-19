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

        require_once("../reuse/_dbConnect.php");
        $conn = connectDB();

        $sql = "SELECT Account_username, Account_userType, Officer_name, Officer_ID FROM Accounts;";
        // echo $sql; // debugging
        $result = mysqli_query($conn, $sql);
        
?>
<body>
    <?php 
        require("../reuse/navbar.php");
    ?>
    <div class="content">
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
    ?></div>
</body>
</html>
