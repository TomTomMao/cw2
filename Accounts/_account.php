<?php
require("../config/debug.php");
class User {
    // Assume session is started
    // rely on the $_SESSION
        // USE: $_SESSION["username"], $_SESSION["userStatus"], $_SESSION["userType"]
        // These session do not have to be set initially.
        // userStatus could be "logged" or "notLogged"
        // userType could be "police" or "admin"

    function hasSessionStarted(){
        if (session_status()==1) {
           throw new Exception("Session not started");
        }
    }

    function logIn($username, $password) {
        // this method take username and password, and set session to be log in.
        // assume the config in db.inc.php is correct.
        // assume username and password is not empty
        // if username and password matched in the database,
            // return "success".
            // and set $_SESSION['username'] to be the username
            // and set $_SESSION['userType'] to be the userType(either "police" or "admin")
            // and set $_SESSION['userStatus'] to be "logged"
        // if username is in the database but password is wrong,
            // return "wrongPassword"
        // if username is not in the database
            // return "usernameNotExist"


        $this->hasSessionStarted();
        if (empty($username) && empty($password)) { // if empty username or password, raise an error
            throw new Exception("Wrong argument error: 
            username or password should not be empty
            username: '', password: ''");
        }
        require("../config/db.inc.php");
        $conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
        if(mysqli_connect_errno()) { // cannot connect database
           debugEcho ("Failed to connect to MySQL: ".mysqli_connect_error()); // for debugging
           die();
        } else { // success to connect database
            debugEcho("MySQL connection OK<br>"); // for debugging
            $sql = "SELECT * FROM Accounts WHERE Account_username='".$username."';"; // select a row match the user
            debugEcho($sql); // for debugging
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result)>1) { // to much result, database has a problem
                debugEcho("DATABASE ERROR, there are two user share the same username, 
                    check the database"); // for debugging
            } elseif (mysqli_num_rows($result)==1) { // account exist, need to check password
                $row = mysqli_fetch_assoc($result);
                debugPrint_r($row); // for debugging
                if ($row["Account_password"]==$password) {
                    debugEcho("login success"); // for debugging
                    $_SESSION["username"]=$username;
                    $_SESSION["userType"]=$row["Account_type"];
                    $_SESSION["userStatus"]="logged";
                    return "success";
                } else {
                    debugEcho("wrong password"); // for debugging
                    return "wrongPassword";
                }
            } else {
                debugEcho("username not exist");
                return "usernameNotExist";
            }
            mysqli_close($conn); // disconnect
        }
    }

    function isLoggedIn(){
        // assume session has started
        $this->hasSessionStarted();
        if (isset($_SESSION["userStatus"]) && $_SESSION["userStatus"]=="logged") {
            debugEcho("isLoggedIn()=true");
            return true;
        } else {
            debugEcho("isLoggedIn()=false");
            return false;
        }
    }

    function logout() {
        // Unset $_SESSION['username'] 
        // Unset $_SESSION['userType']
        // Unset $_SESSION['userStatus']
        $this->hasSessionStarted();
        debugPrint_r($_SESSION);
        unset($_SESSION['username']);
        unset($_SESSION['userType']);
        unset($_SESSION['userStatus']);
        if (!$this->isLoggedIn()) {
            debugEcho("logout successfully");
        } else {
            debugEcho("Something went wrong!"); 
        }
    }

    function getUsername() {
        // assume session has started and the user has logged in
        $this->hasSessionStarted();
        if ($this->isLoggedIn()) {
            return $_SESSION["username"];
        } 
    }

    function getUserType() {
        $this->hasSessionStarted();
        if ($this->isLoggedIn()) {
            return $_SESSION["userType"];
        }
    }
    
    function isAdmin() {
        $this->hasSessionStarted();
        if ($this->getUserType()=="admin") {
            return true;
        } else {
            return false;
        }
    }
    function isValidPassword($candidatePassword) {
        if (empty($candidatePassword)) {
            return false;
        } elseif (strlen($candidatePassword)>40) {
            return false;
        } else {
            return true;
        }
    }
    function changePassword($newPassword) {
        // assume the user has already logged in, assume $newPassword is legal.(i.e., can update into the database)
        // behavior of this method: change the password in the database

        $this->hasSessionStarted();

        require("../config/db.inc.php");
        $conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
        if(mysqli_connect_errno()) { // cannot connect database
           debugEcho ("Failed to connect to MySQL: ".mysqli_connect_error()); // for debugging
           die();
        } else { // success to connect database
            debugEcho("MySQL connection OK<br>"); // for debugging
            $sql = "UPDATE Accounts SET Account_password='".$newPassword."' WHERE Account_username='".$this->getUsername()."';"; // update the password
            debugEcho ($sql); // for debugging
            $result = mysqli_query($conn, $sql);
            mysqli_close($conn); // disconnect
        }
    }
    
}


?>