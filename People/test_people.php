<?php
    require("../reuse/head.php");
    require("_people.php");
    require("../Accounts/_account.php");
    session_start();
    function testGetPersonIDByLicence() {
        $allPass = true;
        $user = new User();
        $peopleDB = new PeopleDB($user);
        $personID = $peopleDB->getPersonIDByLicence("SMITH92LDOFJJ829");
        $testCases = array();
        $testCases["SMITH92LDOFJJ829"] = 1;
        $testCases["ALLEN88K23KLR9B3"] = 2;
        $testCases["asduhfxcvoijhads"] = NULL;
        foreach($testCases as $input=>$expectOutput) {
            $actualOutput = $peopleDB->getPersonIDByLicence($input);
            $actualOutput = $actualOutput==NULL ? "NULL" : $actualOutput;
            $expectOutput = $expectOutput==NULL ? "NULL" : $expectOutput;
            echo "actual output:".$actualOutput.", expected output:".$expectOutput;
            echo "<br>";
            if ($actualOutput!=$expectOutput) {
                $allPass = false;
            }
        }
    }
    echo  "testGetPersonIDByLicence() passed all? ".testGetPersonIDByLicence()."<br>";
?>