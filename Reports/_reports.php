<?php 
    class Report {
        static $TABLEHEAD = "<table class='report-table'>
        <tr>
            <th>report creater</th>
            <th>incident ID</th>
            <th>incident date</th>
            <th>incident description</th>
            <th>offence</th>
            <th>vehicle licence</th>
            <th>owner name</th>
            <th>owner licence</th>
            <th>offender name</th>
            <th>offender licence</th>
            <th>detail</th>
            <th>edit</th>
            <th>fine</th>
        </tr>";
        static $TABLETAIL = "</table>";
        function __construct(   $Incident_ID,
                                $Account_username,
                                $Incident_date,
                                $Incident_report,
                                $Offence_ID,
                                $Offence_description,
                                $Offence_maxFine,
                                $Offence_maxPoints,
                                $Vehicle_ID,
                                $Vehicle_licence,
                                $Vehicle_make,
                                $Vehicle_model,
                                $Vehicle_colour,
                                $Owner_ID,
                                $Owner_name,
                                $Owner_Address,
                                $Owner_DOB,
                                $Owner_licence,
                                $Ownership_ID,
                                $Offender_ID,
                                $Offender_name,
                                $Offender_address,
                                $Offender_DOB,
                                $Offender_licence,
                                $Officer_name,
                                $Officer_ID,
                                $Fine_ID,
                                $Fine_amount,
                                $Fine_points) {
                $this->incidentID=$Incident_ID;
                $this->accountUsername=$Account_username;
                $this->incidentDate=$Incident_date;
                $this->incidentReport=$Incident_report;
                $this->offenceID=$Offence_ID;
                $this->offenceDescription=$Offence_description;
                $this->offenceMaxFine=$Offence_maxFine;
                $this->offenceMaxPoints=$Offence_maxPoints;
                $this->vehicleID=$Vehicle_ID;
                $this->vehicleLicence=$Vehicle_licence;
                $this->vehicleMake=$Vehicle_make;
                $this->vehicleModel=$Vehicle_model;
                $this->vehicleColour=$Vehicle_colour;
                $this->ownerID=$Owner_ID;
                $this->ownerName=$Owner_name;
                $this->ownerAddress=$Owner_Address;
                $this->ownerDOB=$Owner_DOB;
                $this->ownerLicence=$Owner_licence;
                $this->ownershipID=$Ownership_ID;
                $this->offenderID=$Offender_ID;
                $this->offenderName=$Offender_name;
                $this->offenderAddress=$Offender_address;
                $this->offenderDOB=$Offender_DOB;
                $this->offenderLicence=$Offender_licence;
                $this->officerName=$Officer_name;
                $this->officerID=$Officer_ID;
                $this->fineID=$Fine_ID;
                $this->fineAmount=$Fine_amount;
                $this->finePoints=$Fine_points;
                
                


        }
        function toJSONNew() {
            throw new Exception("Not Implemented Error", 1);
        }
        function toJSON($new = false) {
            if ($new) {
                return $this->toJSONNew();
            }
            return '{"incidentID":"'.$this->incidentID
                .'","accountUsername":"'.$this->accountUsername
                .'","incidentDate":"'.$this->incidentDate
                .'","incidentReport":"'.$this->incidentReport
                .'","offenceID":"'.$this->offenceID
                .'","offenceDescription":"'.$this->offenceDescription
                .'","offenceMaxFine":"'.$this->offenceMaxFine
                .'","offenceMaxPoints":"'.$this->offenceMaxPoints
                .'","vehicleID":"'.$this->vehicleID
                .'","vehicleLicence":"'.$this->vehicleLicence
                .'","vehicleMake":"'.$this->vehicleMake
                .'","vehicleModel":"'.$this->vehicleModel
                .'","vehicleColour":"'.$this->vehicleColour
                .'","ownerID":"'.$this->ownerID
                .'","ownerName":"'.$this->ownerName
                .'","ownerAddress":"'.$this->ownerAddress
                .'","ownerDOB":"'.$this->ownerDOB
                .'","ownerLicence":"'.$this->ownerLicence
                .'","ownershipID":"'.$this->ownershipID
                .'","offenderID":"'.$this->offenderID
                .'","offenderName":"'.$this->offenderName
                .'","offenderAddress":"'.$this->offenderAddress
                .'","offenderDOB":"'.$this->offenderDOB
                .'","offenderLicence":"'.$this->offenderLicence
                .'","officerName":"'.$this->officerName
                .'","officerID":"'.$this->officerID
                .'","fineID":"'.$this->fineID
                .'","fineAmount":"'.$this->fineAmount
                .'","finePoints":"'.$this->finePoints.'"}';
        }
        function renderGeneralRow($showTable = false) {
            if ($showTable) {
                $tableHead = Report::$TABLEHEAD;
                $tableTail = Report::$TABLETAIL;
            } else {
                $tableHead = "";
                $tableTail = "";
            }
            // render a row of person, if there is a falsy value, use string "null".
            $accountUsername = $this->accountUsername != NULL ? $this->accountUsername : "NULL";
            $incidentID = $this->incidentID != NULL ? $this->incidentID : "NULL";
            $incidentDate = $this->incidentDate != NULL ? $this->incidentDate : "NULL";
            $incidentReport = $this->incidentReport != NULL ? $this->incidentReport : "NULL";
            $offenceDescription = $this->offenceDescription != NULL ? $this->offenceDescription : "NULL";
            $vehicleLicence = $this->vehicleLicence != NULL ? $this->vehicleLicence : "NULL";
            $ownerName = $this->ownerName != NULL ? $this->ownerName : "NULL";
            $ownerLicence = $this->ownerLicence != NULL ? $this->ownerLicence : "NULL";
            $offenderName = $this->offenderName != NULL ? $this->offenderName : "NULL";
            $offenderLicence = $this->offenderLicence != NULL ? $this->offenderLicence : "NULL";
            $fineID = $this->fineID != NULL ? $this->fineID : "NULL";
            $fineAmount = $this->fineAmount != NULL ? $this->fineAmount : "NULL";
            $finePoints = $this->finePoints != NULL ? $this->finePoints : "NULL";
            if ($fineID == "NULL") {
                $fineCell = "<td><a class='add-fine' id='fine-$incidentID' target='_blank' href='../Admin/addFine.php?id=".$incidentID."'>add</a></td>";
            } else {
                $fineCell = "<td>".$fineAmount."</td>";
            }

            return $tableHead."
            <tr id='$incidentID'>
                <td>".$accountUsername."</td>
                <td>".$incidentID."</td>
                <td>".$incidentDate."</td>
                <td>".$incidentReport."</td>
                <td>".$offenceDescription."</td>
                <td>".$vehicleLicence."</td>
                <td>".$ownerName."</td>
                <td>".$ownerLicence."</td>
                <td>".$offenderName."</td>
                <td>".$offenderLicence."</td>
                <td><button onclick=\"showReportDetail($incidentID)\" class='detial-button' id='detial".$incidentID."'>show</button></td>
                <td><a id='edit-$incidentID' target='_blank' href='edit.php?id=".$incidentID."'>edit</a></td>
                ".$fineCell."
            </tr>".$tableTail;
        }
        static function renderGeneralTable($reports) {
            // render a reports table
            $tableHead = Report::$TABLEHEAD;
            $tableTail = Report::$TABLETAIL;
            $tableBody = "";
            if (!empty($reports)){
                foreach($reports as $report) {
                    $tableBody = $tableBody.$report->renderGeneralRow();
                }
                return $tableHead.$tableBody.$tableTail;
            } else {
                return false;
            }
        }
    }
    class ReportsDB {
        function __construct($user, $conn) {
            $this->user = $user;
            $this->conn = $conn;
            $this->sqlPrefix = "SELECT 

                Incident_ID, Incident.Account_username, Incident_date, Incident_report, 
                
                Incident.Offence_ID, Offence.Offence_description, Offence.Offence_maxFine, Offence.Offence_maxPoints,
                
                Ownership.Vehicle_ID , Vehicles.Vehicle_licence,Vehicles.Vehicle_make, Vehicles.Vehicle_model, Vehicles.Vehicle_colour,
                
                Ownership.People_ID AS 'Owner_ID', owner.People_name AS 'Owner_name', owner.People_address AS 'Owner_Address', owner.People_DOB AS 'Owner_DOB', owner.People_licence AS 'Owner_licence',
                
                Incident.Ownership_ID, 
                
                Incident.People_ID AS 'Offender_ID', offender.People_name AS 'Offender_name', offender.People_address AS 'Offender_address', offender.People_DOB AS 'Offender_DOB',  offender.People_licence AS 'Offender_licence',

                Accounts.Officer_name, Accounts.Officer_ID,
                
                Fines.Fine_ID, Fines.Fine_amount, Fines.Fine_points

                FROM Incident
                LEFT JOIN People AS offender ON Incident.People_ID = offender.People_ID
                LEFT JOIN Ownership USING (Ownership_ID)
                LEFT JOIN People AS owner ON Ownership.People_ID = owner.People_ID
                LEFT JOIN Vehicles ON Ownership.Vehicle_ID = Vehicles.Vehicle_ID
                LEFT JOIN Offence USING(Offence_ID)
                LEFT JOIN Accounts USING(Account_username)
                LEFT JOIN Fines USING(Incident_ID) ";
        }
        static function getWhereName($fieldName) {
            // given a string $fieldName, return it's column name that would be used in sql where clause.
            $dictionary =[
                "offenderID"=>"offender.People_ID",
                "offenderName"=>"offender.People_name",
                "offenderDOB"=>"offender.People_DOB",
                "offenderLicence"=>"offender.People_Licence",
                "ownerID"=>"owner.People_ID",
                "ownerName"=>"owner.People_name",
                "ownerDOB"=>"owner.People_DOB",
                "ownerLicence"=>"owner.People_Licence",
                "vehicleID"=>"Vehicles.Vehicle_ID",
                "vehicleLicence"=>"Vehicles.Vehicle_licence",
                "vehicleColour"=>"Vehicles.Vehicle_Colour",
                "vehicleMake"=>"Vehicles.Vehicle_Make",
                "vehicleModel"=>"Vehicles.Vehicle_Model",
                "incidentDate"=>"Incident.Incident_date",
                "incidentID"=>"Incident.Incident_ID",
                "officerName"=>"Accounts.Officer_name",
                "officerID"=>"Accounts.Officer_ID",
                "offenceID"=>"Offence.Offence_ID",
                "offenceDescription"=>"Offence.Offence_description"
            ]; 
            // echo $dictionary[$fieldName]."<br>"; // debugging
            return $dictionary[$fieldName];
        }



        function getReportsMultipleConditions($conditions, $audit=true) {
            // Assume $conditions is not empty
            // $conditions: an array of search condition
            // e,g,: $conditions = Array ( [0] => Array ( ["columnName"] => offender.People_ID ["searchValue"] => "1" ) 
            //                              [1] => Array ( ["columnName"] => offender.People_DOB ["searchValue"] => "1999-01-01" ) 
            //                              [2] => Array ( ["columnName"] => owner.People_Licence ["searchValue"] => "NULL" ) )
            // Results: Use those conditions to search the database. If conditionvalue is NULL, use 'WHERE $columnName IS NULL' IN SQL 
            if (empty($conditions)) {
                throw new Exception("Empty Condition Error.(from ReportDB->getReportsMultipleConditions)");
            }
            $conditionStrings = array();
            foreach($conditions as $condition) {
                if ($condition["searchValue"] == "NULL") {
                    array_push($conditionStrings, $condition["columnName"]." IS NULL");
                } else {
                    array_push($conditionStrings, $condition["columnName"]." = '".$condition["searchValue"]."'");
                }
            }
            $conditionSqlClause = "WHERE ".implode(" AND ", $conditionStrings);
            $sql = $this->sqlPrefix.$conditionSqlClause.";";
            $conn = $this->conn;
            // echo $sql;// debugging
            $results = mysqli_query($conn, $sql);
            $reports = array();
            // echo $sql; //debugging
            while ($row=mysqli_fetch_assoc($results)) {
                array_push($reports, new Report($row["Incident_ID"],
                $row["Account_username"],
                $row["Incident_date"],
                $row["Incident_report"],
                $row["Offence_ID"],
                $row["Offence_description"],
                $row["Offence_maxFine"],
                $row["Offence_maxPoints"],
                $row["Vehicle_ID"],
                $row["Vehicle_licence"],
                $row["Vehicle_make"],
                $row["Vehicle_model"],
                $row["Vehicle_colour"],
                $row["Owner_ID"],
                $row["Owner_name"],
                $row["Owner_Address"],
                $row["Owner_DOB"],
                $row["Owner_licence"],
                $row["Ownership_ID"],
                $row["Offender_ID"],
                $row["Offender_name"],
                $row["Offender_address"],
                $row["Offender_DOB"],
                $row["Offender_licence"],
                $row["Officer_name"],
                $row["Officer_ID"],
                $row["Fine_ID"],
                $row["Fine_amount"],
                $row["Fine_points"]
            ));
            }
            return $reports;
        }

        function getReportByReportID($reportID, $audit=true) {
            // given reportID 
            // return the first report object that in the db searching result.
            // return false if no report has this report id.
            $conditions = array();
            $condition = array();
            $condition["columnName"] = "Incident.Incident_ID";
            $condition["searchValue"] = $reportID;
            array_push($conditions, $condition);
            $reports = $this->getReportsMultipleConditions($conditions,$audit);
            if (empty($reports)) {
                return false;
            } else {
                return $reports[0];
            }
            // return $this->getReportsMultipleConditions($conditions)[0];
        }
        
    }
?>