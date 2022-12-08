
<?php 
    try { ?>
    <?php $pageTitle = "Lookup People";
        require_once("../reuse/head.php");
    ?>

    <?php // handle not login error
    
        session_start();
        require("../Accounts/_account.php");// there is a User class
        $user = new User();
        if (!$user->isLoggedIn()) {
            header("location: ../Accounts/notLoginError.html"); // check if logged in
        }
    ?>

    <body>
    <?php 
        require("../reuse/navbar.php");
    ?>

        <h1>Retrieve Reports</h1>
        <hr>
        <form action="lookup.php" method="post">
            <h3>Search by offender's Name</h3>
            <table>
                <tr>
                    <td>Offender Name:</td>
                    <td><input type="text" name="offenderName"></td>
                    <td><input type="submit" value="search"></td>
                </tr>
            </table>
        </form>
        <hr>
        <form action="lookup.php" method="post">
            <h3>Search by offender's Licence</h3>
            <table>
                <tr>
                    <td>Offender Name:</td>
                    <td><input type="text" name="offenderLicence"></td>
                    <td><input type="submit" value="search"></td>
                </tr>
            </table>
        </form>
        <hr>
        <form action="lookup.php" method="post">
            <h3>Search by vehicle licence</h3>
            <table>
                <tr>
                    <td>Licence:</td>
                    <td><input type="text" name="vehicleLicence"></td>
                    <td><input type="submit" value="search"></td>
                </tr>
            </table>
        </form>
        
        <?php
            require("../reuse/_dbConnect.php");
            $conn = connectDB();

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