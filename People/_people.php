<?php
    $debugOn=false;
    require_once("../config/debug.php");
    class Person {
        function __construct($ID, $licence, $address, $dateOfBirth, $name, $photoID) {
            $this->ID = $ID;
            $this->licence = $licence;
            $this->address = $address;
            $this->dateOfBirth = $dateOfBirth;
            $this->name = $name;
            $this->photoID = $photoID;
        }

        function getFullName() {
            return $this->name;
        }

        function getFirstName() {
            return explode(" ", $this->getFullName())[0];
        }

        function getLastName() {
            return explode(" ", $this->getFullName())[1];
        }

        function getAddress() {
            return $this->address;
        }

        function getDOB() {
            return $this->dateOfBirth;
        }

        function getID() {
            return $this->ID;
        }

        function getLicence() {
            return $this->licence;
        }
        function getPhotoID() {
            return $this->photoID;
        }
    }
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
            // Return an array of person object that matches the name.
            $people = array();
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
                $index = 0;
                while ($row = mysqli_fetch_assoc($results)) {
                    $people[$index] = new Person($row["People_ID"], $row["People_licence"], 
                    $row["People_address"], $row["People_DOB"], $row["People_name"], $row["People_photoID"]);
                    $index += 1;
                }
                mysqli_close($conn); // disconnect
            }
            return $people;
        }
        function renderPeople($people) {
            // input an array of person object, return an html <table>.
            $peopleTable =  
                "<table class='people-table'>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Driving Licence</th>
                        <th>DOB</th>
                    </tr>";
            foreach($people as $person) {
                $personID = $person->getID();
                $personName = $person->getFullName();
                $personAddress = $person->getAddress();
                $personLicence = $person->getLicence();
                $personDOB = $person->getDOB();
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
            // Return an array of person object that matches the licence. The length of the array should be 0 if no match or 1 if matched.
            $people = array();
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
                $index = 0;
                while ($row = mysqli_fetch_assoc($results)) {
                    $people[$index] = new Person($row["People_ID"], $row["People_licence"], 
                    $row["People_address"], $row["People_DOB"], $row["People_name"], $row["People_photoID"]);
                    $index += 1;
                }
                mysqli_close($conn); // disconnect
            }
            if (count($people))
            return $people;
        }
    }







    
    // TEST CODE WHEN DEVELOP
    function testGetPeopleByName($peopleName) {
        $user = new User();
        $peopleDB = new PeopleDB($user->getUsername());
        $people = $peopleDB->getPeopleByName($peopleName);
        
        debugPrint_r($people);
        foreach($people as $person) {
            print_r ($person);
            echo "<br>";
        }
    }

    function testRenderPeople() {
        $user = new User();
        $peopleDB = new PeopleDB($user->getUsername());
        $people = $peopleDB->getPeopleByName("john");
        $peopleTable = $peopleDB->renderPeople($people);
        echo $peopleTable;
        }

    function testGetPeopleByLicence($peopleLicence) {
        $user = new User();
        $peopleDB = new PeopleDB($user->getUsername());
        $people = $peopleDB->getPeopleByLicence($peopleLicence);
        debugPrint_r($people);
        foreach($people as $person) {
            print_r ($person);
            echo "<br>";
        }
    }
    
    function runPeopleTests() {
        require("../head.php");
        session_start();
        require_once("../Accounts/_account.php");
        
        testGetPeopleByName("john");
        testRenderPeople();
        testGetPeopleByLicence("MEDORH914ANBB223");
    }
    
    // runTests();
    
?>