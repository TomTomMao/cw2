<?php $pageTitle = "Login";
    require("../reuse/head.php");
    require("../reuse/errorMessage.php");
?>

<?php
    try{
    session_start();
    require("_account.php");
    $user = new User();
    // handling login button
    if ((empty($_POST["username"])||empty($_POST["password"]))) { // not login and didnt enter username or password
        debugEcho("\$_POST is empty"."<br>\$_POST:");
        debugPrint_r($_POST);
        if ($_SERVER['REQUEST_METHOD']=="POST") {
            $msg = "please enter username and password";
        }
    } else { //entered username and password
        debugEcho("\$_POST is not empty"."<br>\$_POST:");
        debugPrint_r($_POST);
        $loginResult = $user->logIn($_POST["username"],$_POST["password"]);
        if ($loginResult=="success") {
            header("location: loginSuccess.php");
            // echo "<p style='color: green'>Log in successfully</p>"."<a href='home.php'>Go to Home Page</a>";
        } elseif ($loginResult=="wrongPassword") {
            $msg = "Wrong password, please try again";
        } elseif ($loginResult=="usernameNotExist") {
            $msg = "Username doesn't exist, please try again";
        }
    }
?>
<body>
<?php
    if ($user->isLoggedIn()) { // already log in
        echo '
                <p>You are already logged in</p>
                <a href="home.php">Go to Home Page</a>';
    }
    else {
        echo '
                <form action="login.php" method="post" class="login-form">
                    <table>
                        <tr>
                            <td>
                                username:
                            </td>
                            <td>
                                <input type="text" name="username" id="username">
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <!-- // the effect of toggle password here referenced this page:
                            https://www.w3schools.com/howto/howto_js_toggle_password.asp -->
                            <td>password:</td>
                            <td>
                                <input type="password" name="password" id="password">
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
                                <button type="button" onclick="togglePassword(\'password\')">show/hide</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <input type="submit" value="login">
                            </td>
                        </tr>
                    </table>
                </form>';
    }
    if (!empty($msg)) {
        renderErrorMessage($msg);
    }
    } catch (Exception $error) {
        renderErrorMessage($error->getMessage());
    }
?>
</body>
</html>
