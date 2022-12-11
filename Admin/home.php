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
        <h1>Admin Home</h1>
        <p>You can:</p>
            <ul>
                <li><a href="../Admin/newAccount.php" target="blank">create an account</a></li>
                <li><a href="../Reports/lookup.php" target="blank"> add fine for a report</a></li>
            </ul>
        
</body>
</html>
