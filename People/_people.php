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
            return "NULL";
        }
        function getJSONText() {
            return '{"ID":"'.$this->ID.
                '","licence":"'.$this->getLicence().
                '","address":"'.$this->getAddress().
                '","dateOfBirth":"'.$this->getDOB().
                '","firstName":"'.$this->getFirstName().
                '","lastName":"'.$this->getLastName().
                '","photoID":"'.$this->getPhotoID().'"}';
        }
        static function renderPeopleTable($people) {
            // given a array of $personObject
            
            // If length of $people > 0: return a html table with all person of people data.
            // if length of $people ==0: return False;
            $tableHead = "<table class='people-table'>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Driving Licence</th>
                                <th>DOB</th>
                                <th>PhotoID</th>
                            </tr>";
            $tableTail = "</table>";
            $tableBody = "";
            if ($people) {
                foreach($people as $person) {
                    $tableBody = $tableBody.$person->renderRow();
                }
                $table = $tableHead.$tableBody.$tableTail;
                return $table;
            } else {
                return false;
            }
        }
        function renderRow() {
            // render a row of person, if there is a falsy value, use string "null".
            $personID = $this->getID() !=NULL ? $this->getID() : "null";
            $personFullName = $this->getFullName() !=NULL ? $this->getFullName() : "null";
            $personAddress = $this->getAddress() !=NULL ? $this->getAddress() : "null";
            $personLicence = $this->getLicence() !=NULL ? $this->getLicence() : "null";
            $personDOB = $this->getDOB() !=NULL ? $this->getDOB() : "null";
            $personPhotoID = $this->getPhotoID() !=NULL ? $this->getPhotoID() : "null";
            return "
            <tr>
                <td>".$personID."</td>
                <td>".$personFullName."</td>
                <td>".$personAddress."</td>
                <td>".$personLicence."</td>
                <td>".$personDOB."</td>
                <td>".$personPhotoID."</td>
            </tr>";
        }
    }
    class PeopleDB {
        // this is a class for handling data about people
        function __construct($username, $conn){
            $this->username = $username;
            $this->conn = $conn;
        }
        function recordGetPeopleByName(){
        }
        function isPersonLicenceInDB($personLicence) {
            $people = $this->getPeopleByLicence($personLicence);
            return (!is_null($people));
        }
        function getPeopleByName($peopleName) {
            // Input the partial/full name of a person, matching rule is case insensitive.
            // Record the audit trial data into the table PeopleSearchAudit inside the database: (not implemented yet)
                // Officer_ID: $officerID
                // "search", ""
            // Return an array of person object that matches the name.
            // If not match any people in the database, return an empty array.
            $conn = $this->conn;
            $people = array();
            $sql = "SELECT * FROM People WHERE People.People_name LIKE '"
            .$peopleName." %' OR 
            People.People_name LIKE '% ".$peopleName."' OR 
            People.People_name='".$peopleName."';";

            $results = mysqli_query($conn, $sql);
            $index = 0;
            while ($row = mysqli_fetch_assoc($results)) {
                $people[$index] = new Person($row["People_ID"], $row["People_licence"], 
                $row["People_address"], $row["People_DOB"], $row["People_name"], $row["People_photoID"]);
                $index += 1;
                
            }
            return $people;
        }
        
        function getPeopleByLicence($peopleLicence) {
            // Input the Licence of a person
            // Record the audit trial data into the table PeopleSearchAudit inside the database: (not implemented yet)
                // Officer_ID: $officerID
                // "search", ""
            // Return an array of person object that matches the licence. The length of the array should be 0 if no match or 1 if matched.
            // Return an empty array, if didn't match.
            $conn = $this->conn;
            $people = array();
            $sql = "SELECT * FROM People WHERE People.People_Licence='".$peopleLicence."';";

            debugEcho($sql);
             // success to connect database
            $results = mysqli_query($conn, $sql);
            $index = 0;
            while ($row = mysqli_fetch_assoc($results)) {
                $people[$index] = new Person($row["People_ID"], $row["People_licence"], 
                $row["People_address"], $row["People_DOB"], $row["People_name"], $row["People_photoID"]);
                $index += 1; // so far so good, in future, change it.
            }
            
            if (count($people)) // not sure why did I typed this, but so far so good, don't delete it!
            return $people;
        }
        function getPersonByLicence($licence) {
            // given licence, return A person that match this licence if matched, otherwise return null;
            // if returned a person, then the person has ID
            $people = $this->getPeopleByLicence($licence);
            if (!is_null($people)) {
                return $people[0];
            } else {
                return NULL;
            }
        }
        function getPersonIDByLicence($licence) {
            // input licence;
            // return null if licence not in db.
            // return A person id correspond to licence if licence exist in the database.
            $person = $this->getPersonByLicence($licence);
            if (isset($person)) {
                return $person->getID();
            }
        }
        function getPersonByDetail($person) {
            // given $person, return A person that match this licence if matched (dob, name, address), otherwise return null;
            // if returned a person, then the person has ID
            $people = $this->getPeopleByDetail($person);
            if (!is_null($people)) {
                return $people[0];
            } else {
                return NULL;
            }
        }
        function getPeopleByDetail($person) {
            // $person: person object with mandatory data: name, dob, address
            // return an array of person object, the array would be empty if no data matched.
            $conn = $this->conn;
            $sql = "SELECT * FROM People WHERE People_name='".$person->getFullName().
                "', People_DOB='".$person->getDOB()."', People_address='".$person->getAddress()."';";
            $results = mysqli_query($sql);
            $people = array();
            while($row = mysqli_fetch_assoc($results)) {
                array_push(new Person($row["People_ID"], $row["People_licence"], 
                                        $row["People_address"], $row["People_DOB"], $row["People_name"], $row["People_photoID"]));
                //
            }
            return $people;
        }


        function isPersonDetailInDB($person) {
            // $person: person object with mandatory data: name, dob, address
            // USE name, dob, address TO QUERY DATA
            // return true if person is in db.
            // otherwise return false;
            $people = $this->getPeopleByDetail($person);
            if (empty($people)) {
                return false;
            } else {
                return true;
            }
        }

        function insertNewPerson($person) {
            // hasn't been well tested!
            // given an person object, insert it into the database, assume the person's data are valid and person is new
            // person data: ID(optional), firstname(mandatory), lastname(mandatory), dob(mandatory), address(mandatory), photoid(optional), licence(optional)
            // if the person licence is already in the database, return the false
            // if the person licence is new in the database, return the person id
            // if the person don't have a licence and the comb of these values are new to the database.
                // using dob, address to insert the person, and return person id
            // else :
                // return false;

            $conn = $this->conn;

            // person has licence
            if ($person->getLicence()!="NULL") {
                if ($this->isPersonLicenceInDB($person->getLicence())) {
                    return false; // person already exist, false
                } else {
                    // person is new and with licence.
                    $sql = "INSERT INTO People (People_name, People_address, People_licence, People_DOB) VALUES
                    ('".$person->getFullName()."', '"
                    .$person->getAddress()."', '"
                    .$person->getLicence()."', '"
                    .$person->getDOB()."')";
                    echo $sql."from : peopleDB->insertNewPerson";
                    $result = mysqli_query($sql);
                    $lastID = mysqli_insert_id($conn);
                    return $lastID;
                }

                // person doesn't have a licence and in db
            } elseif ($this->isPersonDetailInDB($person)) {
                // return false
                // echo "Person already In the database!";
                return false;

                // person doesn't have a licence and new to db.
            } else {
                // insert the person, and return the id of the new person.
                
                $personID = null;
                
                $sql = "INSERT INTO People (People_name, People_address, People_DOB) VALUES
                    ('".$person->getFullName()."', '"
                    .$person->getAddress()."', '"
                    .$person->getDOB()."')";
                $result = mysqli_query($sql);
                $lastID = mysqli_insert_id($conn);
                return $lastID;

            }
        }
    }
?>