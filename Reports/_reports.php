<?php 
    class Report {
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
                                $Officer_ID) {
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



        }
        function toJSON() {
            return '{"incidentID":"'.$this->incidentID.'","accountUsername":"'.$this->accountUsername.'","incidentDate":"'.$this->incidentDate.'","incidentReport":"'.$this->incidentReport.'","offenceID":"'.$this->offenceID.'","offenceDescription":"'.$this->offenceDescription.'","offenceMaxFine":"'.$this->offenceMaxFine.'","offenceMaxPoints":"'.$this->offenceMaxPoints.'","vehicleID":"'.$this->vehicleID.'","vehicleLicence":"'.$this->vehicleLicence.'","vehicleMake":"'.$this->vehicleMake.'","vehicleModel":"'.$this->vehicleModel.'","vehicleColour":"'.$this->vehicleColour.'","ownerID":"'.$this->ownerID.'","ownerName":"'.$this->ownerName.'","ownerAddress":"'.$this->ownerAddress.'","ownerDOB":"'.$this->ownerDOB.'","ownerLicence":"'.$this->ownerLicence.'","ownershipID":"'.$this->ownershipID.'","offenderID":"'.$this->offenderID.'","offenderName":"'.$this->offenderName.'","offenderAddress":"'.$this->offenderAddress.'","offenderDOB":"'.$this->offenderDOB.'","offenderLicence":"'.$this->offenderLicence.'","officerName":"'.$this->officerName.'","officerID":"'.$this->officerID.'"}';
        }
        function renderGeneralRow($showTable = false) {
            if ($showTable) {
                $tableHead = "<table class='report-table'>
                <tr>
                    <th>incident Creator</th>
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
                </tr>";
                $tableTail = "</table>";
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
                <td><button onclick=\"showReportDetail($incidentID)\" class='detial-button' id='detial".$incidentID."''>show</button></td>
                <td><a href='edit?id=".$incidentID."''>edit</a></td>
            </tr>".$tableTail;
        }
        static function renderGeneralTable($reports) {
            // render a reports table
            $tableHead = "<table class='report-table'>
                <tr>
                    <th>incident Creator</th>
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
                </tr>";
                $tableTail = "</table>";
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
                
                Incident.Offence_ID, offence.Offence_description, offence.Offence_maxFine, offence.Offence_maxPoints,
                
                ownership.Vehicle_ID , vehicles.Vehicle_licence,vehicles.Vehicle_make, vehicles.Vehicle_model, vehicles.Vehicle_colour,
                
                ownership.People_ID AS 'Owner_ID', owner.People_name AS 'Owner_name', owner.People_address AS 'Owner_Address', owner.People_DOB AS 'Owner_DOB', owner.People_licence AS 'Owner_licence',
                
                Incident.Ownership_ID, 
                
                Incident.People_ID AS 'Offender_ID', offender.People_name AS 'Offender_name', offender.People_address AS 'Offender_address', offender.People_DOB AS 'Offender_DOB',  offender.People_licence AS 'Offender_licence',

                accounts.Officer_name, accounts.Officer_ID
                
                FROM incident
                LEFT JOIN people AS offender ON incident.People_ID = offender.People_ID
                LEFT JOIN ownership USING (Ownership_ID)
                LEFT JOIN people AS owner ON ownership.People_ID = owner.People_ID
                LEFT JOIN vehicles ON ownership.Vehicle_ID = vehicles.Vehicle_ID
                LEFT JOIN offence USING(Offence_ID)
                LEFT JOIN accounts USING(Account_username) ";
        }
        static function getWhereName(string $fieldName) {
            // given a string $fieldName, return it's column name that would be used in sql where clause.
            return [
                "offenderID"=>"offender.People_ID",
                "offenderName"=>"offender.People_name",
                "offenderDOB"=>"offender.People_DOB",
                "offenderLicence"=>"offender.People_Licence",
                "ownerID"=>"owner.People_ID",
                "ownerName"=>"owner.People_name",
                "ownerDOB"=>"owner.People_DOB",
                "ownerLicence"=>"owner.People_Licence",
                "vehicleID"=>"vehicles.Vehicle_ID",
                "vehicleLicence"=>"vehicles.Vehicle_licence",
                "vehicleColour"=>"vehicles.Vehicle_Colour",
                "vehicleMake"=>"vehicles.Vehicle_Make",
                "vehicleModel"=>"vehicles.Vehicle_Model",
                "incidentDate"=>"incident.Incident_date",
                "incidentID"=>"incident.Incident_ID",
                "officerName"=>"accounts.Officer_name",
                "officerID"=>"accounts.Officer_ID"
            ][$fieldName];
        }



        function getReportsMultipleConditions(array $conditions) {
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
                $row["Officer_ID"]));
            }
            return $reports;
        }
        
    }
?>