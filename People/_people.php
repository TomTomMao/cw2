<?php
    $debugOn=false;
    require_once("../config/debug.php");
    class PeopleDB {
        // this is a class for handling data about people
        function __construct($username){
            $this->username = $username;
        }
        function recordGetPeopleByName(){
        }
        function getPeopleByName($peopleName) {
            // Input the partial/full name of a person, matching rule is case insensitive.
            // Record the audit trial data into the table PeopleSearchAudit inside the database: (not implemented yet)
                // Officer_ID: $officerID
                // "search", ""
            // Return an array of people matches the name.
            $peopleData = array();
            $sql = "SELECT * FROM People WHERE People.People_name LIKE '"
            .$peopleName." %' OR 
            People.People_name LIKE '% ".$peopleName."' OR 
            People.People_name='".$peopleName."';";

            debugEcho($sql);
            require("../config/db.inc.php");
            $conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
            if(mysqli_connect_errno()) { // cannot connect database
                debugEcho ("Failed to connect to MySQL: ".mysqli_connect_error()); // for debugging
                die();
            } else { // success to connect database
                debugEcho("MySQL connection OK<br>");
                $results = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_assoc($results)) {
                    $peopleData[$row["People_ID"]] = $row;
                }
                mysqli_close($conn); // disconnect
            }
            return $peopleData;
        }
        function renderPeopleData($peopleData) {
            // GIVEN PEOPLE DATA, RETURN A TABLE.
            $peopleTable =  
                "<table class='people-table'>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Driving Licence</th>
                        <th>DOB</th>
                    </tr>";
            foreach($peopleData as $personID=>$personData) {
                $personName = $personData["People_name"];
                $personAddress = $personData["People_address"];
                $personLicence = $personData["People_licence"];
                $personDOB = $personData["People_DOB"];
                if (empty($personLicence)) {
                    $personLicence = "<i>NULL</i>";
                }
                if (empty($personDOB)) {
                    $personDOB = "<i>NULL</i>";
                }
                $peopleTable = $peopleTable. 
                    "
                        <tr>
                            <td class='people-data-id'><a href='detail.php?&id=".$personID."'>".$personID."</a></td>
                            <td class='people-data-name'>".$personName."</td>
                            <td class='people-data-address'>".$personAddress."</td>
                            <td class='people-data-licence'>".$personLicence."</td>
                            <td class='people-data-dob'>".$personDOB."</td>
                        </tr>";
                
            }
            return  $peopleTable."</table>";
        }
        function getPeopleByLicence($peopleLicence) {
            // Input the Licence of a person
            // Record the audit trial data into the table PeopleSearchAudit inside the database: (not implemented yet)
                // Officer_ID: $officerID
                // "search", ""
            // Return an array of people matches the licence. The length of the array should be 0 or 1.
            $peopleData = array();
            $sql = "SELECT * FROM People WHERE People.People_Licence='".$peopleLicence."';";

            debugEcho($sql);
            require("../config/db.inc.php");
            $conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
            if(mysqli_connect_errno()) { // cannot connect database
                debugEcho ("Failed to connect to MySQL: ".mysqli_connect_error()); // for debugging
                die();
            } else { // success to connect database
                debugEcho("MySQL connection OK<br>");
                $results = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_assoc($results)) {
                    $peopleData[$row["People_ID"]] = $row;
                }
                mysqli_close($conn); // disconnect
            }
            if (count($peopleData))
            return $peopleData;
        }
    }







    
    // TEST CODE WHEN DEVELOP
    function testGetPeopleByName($peopleName) {
        $user = new User();
        $peopleDB = new PeopleDB($user->getUsername());
        $peopleData = $peopleDB->getPeopleByName($peopleName);
        
        debugPrint_r($peopleData);
        foreach($peopleData as $personData) {
            print_r ($personData);
            echo "<br>";
        }
    }

    function testRenderPeopleData() {
        $user = new User();
        $peopleDB = new PeopleDB($user->getUsername());
        $peopleData = $peopleDB->getPeopleByName("john");
        $peopleTable = $peopleDB->renderPeopleData($peopleData);
        echo $peopleTable;
        }

    function testGetPeopleByLicence($peopleLicence) {
        $user = new User();
        $peopleDB = new PeopleDB($user->getUsername());
        $peopleData = $peopleDB->getPeopleByLicence($peopleLicence);
        debugPrint_r($peopleData);
        foreach($peopleData as $personData) {
            print_r ($personData);
            echo "<br>";
        }
    }
    
    function runTests() {
        require("../head.php");
        session_start();
        require_once("../Accounts/_account.php");
        
        testGetPeopleByName("john");
        testRenderPeopleData();
        testGetPeopleByLicence("MEDORH914ANBB223");
    }
    
    // runTests();
    
?>