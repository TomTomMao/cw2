<?php
    
    class Audit {
        function __construct($auditID, $accountUsername, $tableName, $tableID, $oldData, $newData, $behaviourType, $auditTime = "now") {
            // if attribute is null, please use a string "NULL" rather than NULL value. If the attribute is null type, it would be convert to a String "NULL"
            $this->auditID = $auditID == NULL ? "NULL" : $auditID;
            $this->accountUsername = $accountUsername == NULL ? "NULL" : $accountUsername;
            $this->tableName = $tableName == NULL ? "NULL" : $tableName;
            $this->tableID = $tableID == NULL ? "NULL" : $tableID;
            $this->oldData = $oldData == NULL ? "NULL" : $oldData;
            $this->newData = $newData == NULL ? "NULL" : $newData;
            $this->behaviourType = $behaviourType == NULL ? "NULL" : $behaviourType;
            $this->auditTime = $auditTime == "now" ? date("y-m-d H-i-s") : $auditTime;//I reference the link for calling the date function: https://www.w3schools.com/php/php_date.asp
            if (gettype($this->auditID)!="string") {
                $type = gettype($this->auditID);
                throw new Exception("Wrong parameter type of \$auditID, should use a string, given $type", 1);
            } 
            if (gettype($this->accountUsername)!="string") {
                $type = gettype($this->accountUsername);
                throw new Exception("Wrong parameter type of \$accountUsername, should use a string, given $type", 1);
            } 
            if (gettype($this->tableName)!="string") {
                $type = gettype($this->tableName);
                throw new Exception("Wrong parameter type of \$tableName, should use a string, given $type", 1);
            } 
            if (gettype($this->tableID)!="string") {
                $type = gettype($this->tableID);
                throw new Exception("Wrong parameter type of \$tableID, should use a string, given $type", 1);
            } 
            if (gettype($this->oldData)!="string") {
                $type = gettype($this->oldData);
                throw new Exception("Wrong parameter type of \$oldData, should use a string, given $type", 1);
            } 
            if (gettype($this->newData)!="string") {
                $type = gettype($this->newData);
                throw new Exception("Wrong parameter type of \$newData, should use a string, given $type", 1);
            } 
            if (gettype($this->behaviourType)!="string") {
                $type = gettype($this->behaviourType);
                throw new Exception("Wrong parameter type of \$behaviourType, should use a string, given $type", 1);
            } 
            if (gettype($this->auditTime)!="string") {
                $type = gettype($auditTime);
                throw new Exception("Wrong parameter type of \$auditTime, should use a string, given $type", 1);
            } 
            
        }
        function toJSON() {

            // they have {}, so need to be handled specifically.
            $oldDataJson = $this->oldData=="NULL" ? '"NULL"' : $this->oldData; 
            $newDataJson = $this->newData=="NULL" ? '"NULL"' : $this->newData;
            return '{"auditID":"'.$this->auditID.
                '","accountUsername":"'.$this->accountUsername.
                '","tableName":"'.$this->tableName.
                '","tableID":"'.$this->tableID.
                '","oldData":'.$oldDataJson.
                ',"newData":'.$newDataJson.
                ',"behaviourType":"'.$this->behaviourType.
                '","auditTime":"'.$this->auditTime.'"}';
        }
        function isSame($that) {
            // compare $this's and $that's $accountUsername, $tableName, $tableID, $oldData, $newData, $behaviourType, $auditTime 
            // return true if the above attributes are same
            // return false otherwise

            if ($this->accountUsername != $that->accountUsername) {
                return false;
            }
            if ($this->tableName != $that->tableName) {
                return false;
            }
            if ($this->tableID != $that->tableID) {
                return false;
            }
            if ($this->oldData != $that->oldData) {
                return false;
            }
            if ($this->newData != $that->newData) {
                return false;
            }
            if ($this->behaviourType != $that->behaviourType) {
                return false;
            }
            if ($this->auditTime != $that->auditTime) {
                return false;
            }
            return true;
        }

    }
    class AuditDB {
        // this is a class for handling data about people
        function __construct($user, $conn){
            $this->user = $user; // this is for auditing the 'audit trial'
            $this->conn = $conn;
        }
        function insertAudit($audit) {
            // audit: an Audit object
            // insert audit into the database's audit table.
            $auditID = $audit->auditID == "NULL" ? "NULL" : "'".$audit->auditID."'";
            $accountUsername = $audit->accountUsername == "NULL" ? "NULL" : "'".$audit->accountUsername."'";
            $tableName = $audit->tableName == "NULL" ? "NULL" : "'".$audit->tableName."'";
            $tableID = $audit->tableID == "NULL" ? "NULL" : "'".$audit->tableID."'";
            $oldData = $audit->oldData == "NULL" ? "NULL" : "'".$audit->oldData."'";
            $newData = $audit->newData == "NULL" ? "NULL" : "'".$audit->newData."'";
            $behaviourType = $audit->behaviourType == "NULL" ? "NULL" : "'".$audit->behaviourType."'";
            $auditTime = $audit->auditTime == "NULL" ? "NULL" : "'".$audit->auditTime."'";
            $conn = $this->conn;
            $sql = "INSERT INTO Audit (Account_username, Table_name, Table_ID, Old_data, New_data, Behaviour_type, Audit_time) VALUES"
            ."($accountUsername, $tableName, $tableID, $oldData, $newData, $behaviourType, $auditTime)";
            // echo $sql; //debugging
            mysqli_query($conn, $sql);
        }
        function getAuditByUsername($accountUsername, $tableName=false, $timeStart=false, $timeEnd=false) {
            // $accountUsername: the Account_username of the audit record.
            // $tableName: the Table_name of the audit record
            // $timeStart: the minimum time of Audit_time of the audit record, use false for not filtering the start time
            // $timeEnd: the maximum time of Audit_time of the audit record, use false for not filtering the end time
            // return a list of Audit objects, which could be an empty list.
            $sqlPrefix = "SELECT * FROM Audit WHERE Account_username = '$accountUsername'";
            
            $sqlTableNameCondtion = $tableName==false ? "" : " AND Table_name = '$tableName'";
            $sqltimeStartCondition = $timeStart==false ? "" : " AND Audit_time >= '$timeStart'";
            $sqltimeEndCondition = $timeEnd==false ? "" : " AND Audit_time <= '$timeEnd'";
            
            $sqlExtraConditions = $sqlTableNameCondtion.$sqltimeStartCondition.$sqltimeEndCondition;
    
            $sqlSuffix = ";";
            $conn = $this->conn;
            $sql = $sqlPrefix.$sqlExtraConditions.$sqlSuffix;
            // echo "<hr>getAuditByUsername sql: ".$sql."<hr>"; //debugging
            $result = mysqli_query($conn, $sql);
            $auditObjects = [];
            while($row = mysqli_fetch_assoc($result)) {
                array_push($auditObjects, new Audit($row['Audit_ID'], $row["Account_username"], $row["Table_name"], $row["Table_ID"]
                ,$row["Old_data"], $row["New_data"], $row["Behaviour_type"], $row["Audit_time"]));
            }
            return $auditObjects;
        }
        function getAuditByTableID($tableName, $tableID) {
            // $tableName: the Table_name of the audit Record
            // $tableID: The Table_ID of the audit Record
            // return a list of Audit objects, which could be an empty list.
            $conn = $this->conn;
            $sql = "SELECT * FROM Audit WHERE Table_name = '$tableName' AND Table_ID = '$tableID';";
            $result = mysqli_query($conn, $sql);$auditObjects = [];
            while($row = mysqli_fetch_assoc($result)) {
                array_push($auditObjects, new Audit($row['Audit_ID'], $row["Account_username"], $row["Table_name"], $row["Table_ID"]
                ,$row["Old_data"], $row["New_data"], $row["Behaviour_type"], $row["Audit_time"]));
            }
            return $auditObjects;
        }
    }
    function testcase1($user, $conn) {
        // test insertAudit and getAuditByUsername
        // return true if testcase1 passed, else raise an error.
        // echo "<hr>creating audit1<hr>"; // debugging
        $audit1 = new Audit("NULL", "daniels", "People", "2"
        , '{"ID":"2","licence":"ALLEN88K23KLR9B3","address":"46 Bramcote Drive, Nottingham","dateOfBirth":"1994-03-12","firstName":"Jennifer","lastName":"Allen","photoID":"NULL"}'
        , '{"ID":"2","licence":"ALLEN88K23KLR9B3","address":"46 Bramcote Drive, Nottingham","dateOfBirth":"1994-03-12","firstName":"Jennifer","lastName":"Allen","photoID":"NULL"}'
        , 'SELECT', date('2022-12-13 23:44:00')); //I reference the link for calling the date function: https://www.w3schools.com/php/php_date.asp
        // echo "<hr>audit1 created<hr>"; // debugging
        $auditDB = new AuditDB($user, $conn);
        $auditDB->insertAudit($audit1);
        
        $auditFromDB = $auditDB->getAuditByUsername("daniels", false, $timeStart=date('2022-12-13 23:44:00'), $timeEnd=date('2022-12-13 23:44:00'))[0];
        if ($audit1->isSame($auditFromDB)) {
            // echo "<hr>\$audit1=".$audit1->toJSON()."<hr>"; //debugging
            // echo "<hr>\$auditFromDB=".$auditFromDB->toJSON()."<hr>"; //debugging
            return true;
        } else {
            // echo "<hr>\$audit1=".$audit1->toJSON()."<hr>"; //debugging
            // echo "<hr>\$auditFromDB=".$auditFromDB->toJSON()."<hr>"; //debugging
            throw new Exception("testcase1 failed, the attribute of \$audit1 is different with the attribute of \auditFromDB");
        }
    }
    function testcase2($user, $conn) {
        // test getAuditByTableID using Incidents 1.
        // return true if passed, otherwise false;
        $auditDB = new AuditDB($user, $conn);
        $auditObjects = $auditDB->getAuditByTableID("Incidents", "1");
        foreach($auditObjects as $auditObject) {
            // echo "<hr>".$auditObject->toJSON()."<hr>"; // debugging
        }
        $expectedJSON = '{"auditID":"2","accountUsername":"daniels","tableName":"Incidents","tableID":"1","oldData":"NULL","newData":{"incidentID":"1","accountUsername":"daniels","incidentDate":"2017-12-01","incidentReport":"40mph in a 30 limit","offenceID":"1","offenceDescription":"Speeding","offenceMaxFine":"1000","offenceMaxPoints":"3","vehicleID":"15","vehicleLicence":"FJ17AUG","vehicleMake":"Honda","vehicleModel":"Civic","vehicleColour":"Green","ownerID":"4","ownerName":"James Smith","ownerAddress":"26 Devonshire Avenue, Nottingham","ownerDOB":"1978-11-24","ownerLicence":"SMITHR004JFS20TR","ownershipID":"3","offenderID":"4","offenderName":"James Smith","offenderAddress":"26 Devonshire Avenue, Nottingham","offenderDOB":"1978-11-24","offenderLicence":"SMITHR004JFS20TR","officerName":"Daniel Sull","officerID":"ds001","fineID":"","fineAmount":"","finePoints":""},"behaviourType":"INSERT","auditTime":"2017-12-01 11:08:17"}';
        if ($auditObjects[0]->toJSON() == $expectedJSON) {
            return true;
        } else {
            throw new Exception("testcase2 failed, the json is different with the expected value");
        }
    }
    function testcase3($user, $conn) {
        // test getAuditByTable ID using Ownership 1
        // return true if passed, otherwise false;
        $auditDB = new AuditDB($user, $conn);
        $auditObjects = $auditDB->getAuditByTableID("Ownership", "1");
        foreach($auditObjects as $auditObject) {
            // echo "<hr>".$auditObject->toJSON()."<hr>"; // debugging
        }
        $expectedJSON = '{"auditID":"3","accountUsername":"daniels","tableName":"Ownership","tableID":"1","oldData":"NULL","newData":{"ownershipID":"1","owner":{"ID":"3","licence":"MYERS99JDW8REWL3","address":"323 Derby Road, Nottingham","dateOfBirth":"1981-04-25","firstName":"John","lastName":"Myers","photoID":"NULL"},"vehicle":{"ID":"12","licence":"LB15AJL","colour":"Blue","make":"Ford","model":"Fiesta"}},"behaviourType":"INSERT","auditTime":"2019-06-07 21:55:35"}';
        if ($auditObjects[0]->toJSON() == $expectedJSON) {
            return true;
        } else {
            throw new Exception("testcase3 failed, the json is different with the expected value");
        }
    }
    // DO THIS: refresh the database manually.
    // test:
    // require("../Accounts/_account.php");
    // require("../reuse/_dbConnect.php");
    // $user = new User();
    // $conn = connectDB();
    // if (testcase1($user, $conn)) {
    //     echo "<h1>test case 1 passed</h1>";
    // };
    // if (testcase2($user, $conn)) {
    //     echo "<h1>test case 2 passed</h1>";
    // };
    // if (testcase3($user, $conn)) {
    //     echo "<h1>test case 3 passed</h1>";
    // };
    // mysqli_close($conn);
?>