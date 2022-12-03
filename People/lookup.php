
<?php 
    try { ?>
    <?php $pageTitle = "Lookup People";
        require_once("../head.php");
    ?>

    <?php // handle not login error
        session_start();
        require("../Accounts/_account.php");// there is a User class
        $user = new User();
        if (!$user->isLoggedIn()) {
            header("location: ../Accounts/notLoginError.html"); // check if logged in
        }
    ?>

    <?php    
        require_once("_people.php");
    ?>
    <body>
        <div class="navbar">
            <a href="../People/lookup.php">Lookup People</a>
            <a href="../Vehicles/lookup.php">Lookup Vehicles</a>
            <a href="../Vehicles/new.php">New Vehicles</a>
            <a href="../Reports/new.php">New report</a>
            <a href="../Accounts/home.php">My Account</a>
        </div>
        <hr>
        <h1>Look Up People</h1>
        <hr>
        <form action="lookup.php" method="post">
            <h3>Search by name</h3>
            <table>
                <tr>
                    <td>Name:</td>
                    <td><input type="text" name="peopleName" value="<?php if (!empty($_POST['peopleName'])) { echo $_POST['peopleName'];}?>"></td>
                    <td><input type="submit" value="search"></td>
                </tr>
            </table>
        </form>
        <hr>
        <form action="lookup.php" method="post">
            <h3>Search by licence</h3>
            <table>
                <tr>
                    <td>Licence:</td>
                    <td><input type="text" name="peopleLicence" value="<?php if (!empty($_POST['peopleLicence'])) { echo $_POST['peopleLicence'];}?>"></td>
                    <td><input type="submit" value="search"></td>
                </tr>
            </table>
        </form>
        
        <?php
            require("../reuse/_dbConnect.php");
            $conn = connectDB();
            $peopleDB = new PeopleDB($user->getUsername(), $conn);

            if(!empty($_POST["peopleName"])) {
                $people = $peopleDB->getPeopleByName($_POST["peopleName"]);

                // check and render the data
                echo "<hr>";
                if (count($people)<=0) {
                    echo "<div class='feedback-yellow'><div class='feedback-text-line'>".
                    "Person with name: '".$_POST["peopleName"]."'"." Not found"."</div></div>";
                } else {
                    echo Person::renderPeopleTable($people);
                }

            } elseif(!empty($_POST["peopleLicence"])) {
                $people = $peopleDB->getPeopleByLicence($_POST["peopleLicence"]);

                // check and render the data
                echo "<hr>";
                if (!$people) {
                    echo "Person with driving licence: '".$_POST["peopleLicence"]."'"."Not found";
                } else {
                    echo Person::renderPeopleTable($people);
                }

            } elseif(isset($_POST["peopleName"]) || isset($_POST["peopleLicence"])) {
                echo "<div class='feedback-red'><div class='feedback-text-line'>please enter a name or licence</div></div>";
            }
            
            mysqli_close($conn); // disconnect
        ?>
    
<?php 
    } catch (Exception $error) {
        header("location: ../error.php?errorMessage=".$error->getMessage());
    }
?>
</body>
</html>