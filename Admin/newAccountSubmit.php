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

        
    
    // DONE: FORBID NON-POST REQUEST
        if (!isset($_POST["submit"])) {
            throw new Exception ("This page should be posted!");
            die();
        } 

    require("../reuse/navbar.php");
    require("../reuse/_dbConnect.php");
    $conn = connectDB();

    // DONE: FORBID THE FORM THAT HAS EMPTY VALUE
        if (empty($_POST["accountUsername"]) || empty($_POST["officerFirstName"]) || empty($_POST["officerLastName"]) || empty($_POST["officerID"]) ) {
            throw new Exception("There shouldn't be any empty value");
            die();
        } else {
            $accountUsername = $_POST["accountUsername"];
            $officerFirstName = $_POST["officerFirstName"];
            $officerLastName = $_POST["officerLastName"];
            $officerName = $officerFirstName.$officerLastName;
            $officerID = $_POST["officerID"]; 
        }
    // DONE: FORBID THE CASE THAT THE USERNAME EXISTS IN THE DATABASE.
        // use username to query the account table
        // if result is more than one, throw exception and die()
        $sql = "SELECT Account_username FROM accounts WHERE Account_username='$accountUsername';";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result)!=0) {
            mysqli_close($conn);
            throw new Exception("Can not create account $accountUsername. Because the account exist");
        }

    // TODO: FORBID THE CASE THAT ANY FIELD HAS INVALID DATA FORMAT
        
    // TODO: GENERATE AN RANDOM PASSWORD, AND INSERT DATA INTO THE DATABASE.
        // note: This is not secure.
        $CHARS=["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","1","2","3","4","5","6","7","8","9","0"];
        $randPasswordKeys = array_rand($CHARS, 8);
        $password = "";
        foreach($randPasswordKeys as $randPasswordKey) {
            $password = $password.$CHARS[$randPasswordKey];
        }
        // echo $password; // debugging
        $sql = "INSERT INTO Accounts (Account_username, Account_password, Account_userType, Officer_name, Officer_ID) VALUES
        ('$accountUsername', '$password', 'police', '$officerName', '$officerID')";
        // echo $sql; //debugging
        mysqli_query($conn, $sql);


    // TODO: SHOW THE ACCOUNT INFORMATION.
        $sql = "SELECT Account_username, Account_password, Officer_name, Officer_id, Account_userType FROM accounts WHERE Account_username = '$accountUsername';";
        $result = mysqli_query($conn, $sql);
        $account = mysqli_fetch_assoc($result);
        $usernameFromDB = $account["Account_username"];
        $passwordFromDB = $account["Account_password"];
        $officerNameFromDB = $account["Officer_name"];
        $officerIDFromDB = $account["Officer_id"];
        echo "<p>Account created Successfully</p>
        <table class='account-created-info'>
            <tr><td class='account-td'>username</td class='account-td'><td class='account-td'>$usernameFromDB</td class='account-td'></tr>
            <tr><td class='account-td'>password</td class='account-td'><td class='account-td'>$passwordFromDB</td class='account-td'></tr>
            <tr><td class='account-td'>officer name</td class='account-td'><td class='account-td'>$officerNameFromDB</td class='account-td'></tr>
            <tr><td class='account-td'>officer id</td class='account-td'><td class='account-td'>$officerIDFromDB</td></tr>
        </table>"
?>