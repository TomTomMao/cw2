<?php 
    try {
    $pageTitle = "Audit trails";
    require("../reuse/head.php");
?>
<?php // handle not login error
    session_start();
    require("_account.php");// there is a User class
    $user = new User();
    if (!$user->isLoggedIn()) {
        header("location: notLoginError.html"); // check if logged in
    }
?>
<?php
    require_once("../reuse/_audit.php");
    require_once("../reuse/_dbConnect.php");
    $conn = connectDB();
    $auditDB = new AuditDB($user, $conn);
    // print_r($_GET); // debugging
    if (isset($_POST["submit"])) {
        if ($user->isAdmin()==false) {
            header("location: ../error.php?errorMessage=You are not an admin, so you are not allowed to access this page."); 
        }
        if (isset($_POST["accountUsername"])) {
            if(empty($_POST["accountUsername"])) {
                throw new Exception ("User name is empty", 1);
            }
            $audits = $auditDB->getAuditByUsername($_POST["accountUsername"]);
            if (empty($audits)) {
                $accountUsername = $_POST["accountUsername"];
                throw new Exception ("Audit trails for the user '$accountUsername' not found", 0);
            }
        } elseif (isset($_POST["tableName"]) && isset($_POST["tableID"])) {
            $audits = $auditDB->getAuditByTableID($_POST["tableName"], $_POST["tableID"]);
            if (empty($_POST["tableName"]) || empty($_POST["tableID"])) {
                throw new Exception ("table name and table ID should not be empty", 1);
            }
        }
    } elseif (isset($_GET["q"])) {
        if ($_GET["q"]=="accounts") {
            $audits = $auditDB->getAccountsAudit();
        }
    } else {
        $audits = $auditDB->getAuditByUsername($user->getUsername());
    }
    
?>

<body>
    <?php 
        require("../reuse/navbar.php");
        if (empty($audits)) {
            throw new Exception ("Audit trails not found");
            die();
        }
    ?>
    <script>
        let audits = [];
        function getAuditByID(filteredAuditss, targetAuditID) {
            targetAudit = filteredAuditss.filter(audit => audit.auditID == targetAuditID)[0];
            return targetAudit;
        }
        function showGeneralAuditInfo(filteredAudits, start) {
            // remove all the event listener of show detail buttons.
            document.querySelectorAll("general-audit-info button").forEach(button => {
                button = button;
            });
            // render filteredAudits[start] to filteredAudits[start+24] into the table#general-audit-table
            for (let i = start; i <= start + 24; i++) {

                // remove the event lisener for buttons. // reference: https://bobbyhadz.com/blog/javascript-remove-all-event-listeners-from-element
                button = document.querySelector("#general-audit-info-" + (i - start) + " button");
                button.replaceWith(button.cloneNode(true));
                button = document.querySelector("#general-audit-info-" + (i - start) + " button");

                if (i < filteredAudits.length) {
                    // console.log(i)
                    // console.log(filteredAudits.length)
                    // console.log("#general-audit-info-" + (i - start) + ">td.audit-id");
                    // console.log(document.querySelector("#general-audit-info-" + (i - start) + ">td.audit-id")); // debugging
                    document.querySelector("#general-audit-info-" + (i - start) + ">td.audit-id").innerText = filteredAudits[i].auditID;
                    document.querySelector("#general-audit-info-" + (i - start) + ">td.username").innerText = filteredAudits[i].accountUsername;
                    document.querySelector("#general-audit-info-" + (i - start) + ">td.table-name").innerText = filteredAudits[i].tableName;
                    document.querySelector("#general-audit-info-" + (i - start) + ">td.table-id").innerText = filteredAudits[i].tableID;
                    document.querySelector("#general-audit-info-" + (i - start) + ">td.audit-behaviour").innerText = filteredAudits[i].behaviourType;
                    document.querySelector("#general-audit-info-" + (i - start) + ">td.audit-time").innerText = filteredAudits[i].auditTime;
                    button.classList = [""]
                    button.addEventListener("click", () => {
                        audit = filteredAudits[i];
                        showDetailAuditInfo(audit);
                    })
                } else {
                    document.querySelector("#general-audit-info-" + (i - start) + ">td.audit-id").innerText = "";
                    document.querySelector("#general-audit-info-" + (i - start) + ">td.username").innerText = "";
                    document.querySelector("#general-audit-info-" + (i - start) + ">td.table-name").innerText = "";
                    document.querySelector("#general-audit-info-" + (i - start) + ">td.table-id").innerText = "";
                    document.querySelector("#general-audit-info-" + (i - start) + ">td.audit-behaviour").innerText = "";
                    document.querySelector("#general-audit-info-" + (i - start) + ">td.audit-time").innerText = "";
                    button.classList = ["invisible"]
                }
            }
        }
        function showDetailAuditInfo(audit) {
            document.getElementById("detail-audit-info").classList = [""];
            document.getElementById("detail-audit-info-auditID").innerText = audit.auditID;
            document.getElementById("detail-audit-info-accountUsername").innerText = audit.accountUsername;
            document.getElementById("detail-audit-info-auditTime").innerText = audit.auditTime;
            document.getElementById("detail-audit-info-behaviourType").innerText = audit.behaviourType;
            document.getElementById("detail-audit-info-tableName").innerText = audit.tableName;
            document.getElementById("detail-audit-info-tableID").innerText = audit.tableID;

            oldContainer = document.getElementById("old-data");
            newContainer = document.getElementById("new-data");
            oldContainer.innerHTML = "";
            newContainer.innerHTML = "";
            if (audit.tableName == "People") {
                console.log("rendering people");
                renderPeopleData(oldContainer, newContainer, audit);
            } else if (audit.tableName == "Ownership") {
                console.log("rendering ownership");
                renderOwnershipData(oldContainer, newContainer, audit);
            } else if (audit.tableName == "Vehicles") {
                console.log("rendering vehicle");
                renderVehicleData(oldContainer, newContainer, audit);
            } else if (audit.tableName == "Incidents") {
                console.log("rendering incident");
                renderIncidentData(oldContainer, newContainer, audit);
            } else if (audit.tableName == "Fines") {
                console.log("rendering fines");
                renderFineData(oldContainer, newContainer, audit);
            } else if (audit.tableName == "Accounts") {
                console.log("rendering accounts");
                renderAccountData(oldContainer, newContainer, audit);
            }
        }
        function renderPeopleData(oldContainer, newContainer, audit) {
            // render people data in a container with 760*215
            if (audit.behaviourType == "INSERT-SUCCESS") {
                oldContainer.innerHTML = "<h1>No old data</h1>"

                table = document.getElementById("people-table-template").cloneNode(true);
                table.id = "people-table";
                newContainer.innerHTML = "<h1>Data Inserted:</h1>"
                newContainer.appendChild(table);
                document.querySelector("#people-table #personID").innerText = audit.newData.ID;
                document.querySelector("#people-table #personName").innerText = audit.newData.firstName + " " + audit.newData.lastName;
                document.querySelector("#people-table #personAddress").innerText = audit.newData.address;
                document.querySelector("#people-table #personLicence").innerText = audit.newData.licence;
                document.querySelector("#people-table #personDOB").innerText = audit.newData.dateOfBirth;
                document.querySelector("#people-table #totalFines").innerText = audit.newData.totalFine  ? audit.newData.totalFine : "user didn't see this field";
                document.querySelector("#people-table #totalPoints").innerText = audit.newData.totalPoints ? audit.newData.totalPoints : "user didn't see this field";
            } else if (audit.behaviourType == "SELECT-FOUND") {

                newContainer.innerHTML = "<h1>No new data</h1>"

                table = document.getElementById("people-table-template").cloneNode(true);
                table.id = "people-table";
                oldContainer.innerHTML = "<h1>The data searched by the user:</h1>"
                oldContainer.appendChild(table);
                document.querySelector("#people-table #personID").innerText = audit.oldData.ID;
                document.querySelector("#people-table #personName").innerText = audit.oldData.firstName + " " + audit.oldData.lastName;
                document.querySelector("#people-table #personAddress").innerText = audit.oldData.address;
                document.querySelector("#people-table #personLicence").innerText = audit.oldData.licence;
                document.querySelector("#people-table #personDOB").innerText = audit.oldData.dateOfBirth;
                document.querySelector("#people-table #totalFines").innerText = audit.oldData.totalFine ? audit.oldData.totalFine : "user didn't see this field";
                document.querySelector("#people-table #totalPoints").innerText = audit.oldData.totalPoints ? audit.oldData.totalPoints : "user didn't see this field";
            } else if (audit.behaviourType == "SELECT-EMPTY") {
                if (audit.newData.partialName) {
                    newContainer.innerHTML = "<h1>Not found people with the name:</h1><p>" + audit.newData.partialName + "</p>"
                } else {
                    newContainer.innerHTML = "<h1>Not found people with the licence:</h1><p>" + audit.newData.personLicence + "</p>"
                }
            } else if (audit.behaviourType == "SELECT-FOUND-SECONDARY") {

                newContainer.innerHTML = "<h1>No new data</h1>"

                table = document.getElementById("people-table-template").cloneNode(true);
                table.id = "people-table";
                oldContainer.innerHTML = "<h1>User saw this person data when looking at incident data or ownership data:</h1>"
                oldContainer.appendChild(table);
                document.querySelector("#people-table #personID").innerText = audit.oldData.ID;
                document.querySelector("#people-table #personName").innerText = audit.oldData.firstName + " " + audit.oldData.lastName;
                document.querySelector("#people-table #personAddress").innerText = audit.oldData.address;
                document.querySelector("#people-table #personLicence").innerText = audit.oldData.licence;
                document.querySelector("#people-table #personDOB").innerText = audit.oldData.dateOfBirth;
                document.querySelector("#people-table #totalFines").innerText = audit.oldData.totalFine ? audit.oldData.totalFine : "user didn't see this field";
                document.querySelector("#people-table #totalPoints").innerText = audit.oldData.totalPoints ? audit.oldData.totalPoints : "user didn't see this field";
            } else if (audit.behaviourType == "REFERENCE-INSERT") {

                newContainer.innerHTML = "<h1>No new data</h1>"

                table = document.getElementById("people-table-template").cloneNode(true);
                table.id = "people-table";
                oldContainer.innerHTML = "<h1>User referenced this person data when creating an ownership or an incident:</h1>"
                oldContainer.appendChild(table);
                document.querySelector("#people-table #personID").innerText = audit.oldData.ID;
                document.querySelector("#people-table #personName").innerText = audit.oldData.firstName + " " + audit.oldData.lastName;
                document.querySelector("#people-table #personAddress").innerText = audit.oldData.address;
                document.querySelector("#people-table #personLicence").innerText = audit.oldData.licence;
                document.querySelector("#people-table #personDOB").innerText = audit.oldData.dateOfBirth;
                document.querySelector("#people-table #totalFines").innerText = audit.oldData.totalFine ? audit.oldData.totalFine : "user didn't see this field";
                document.querySelector("#people-table #totalPoints").innerText = audit.oldData.totalPoints ? audit.oldData.totalPoints : "user didn't see this field";
            } else if (audit.behaviourType == "REFERENCE-UPDATE") {

                newContainer.innerHTML = "<h1>No new data</h1>"

                table = document.getElementById("people-table-template").cloneNode(true);
                table.id = "people-table";
                oldContainer.innerHTML = "<h1>User referenced this person data when updating an incident:</h1>"
                oldContainer.appendChild(table);
                document.querySelector("#people-table #personID").innerText = audit.oldData.ID;
                document.querySelector("#people-table #personName").innerText = audit.oldData.firstName + " " + audit.oldData.lastName;
                document.querySelector("#people-table #personAddress").innerText = audit.oldData.address;
                document.querySelector("#people-table #personLicence").innerText = audit.oldData.licence;
                document.querySelector("#people-table #personDOB").innerText = audit.oldData.dateOfBirth;
                document.querySelector("#people-table #totalFines").innerText = audit.oldData.totalFine ? audit.oldData.totalFine : "user didn't see this field";
                document.querySelector("#people-table #totalPoints").innerText = audit.oldData.totalPoints ? audit.oldData.totalPoints : "user didn't see this field";
            }
        }
        function renderOwnershipData(oldContainer, newContainer, audit) {
            // render people data in a container with 760*215
            if (audit.behaviourType == "INSERT-SUCCESS") {
                oldContainer.innerHTML = "<h1>No old data</h1>"

                table = document.getElementById("ownership-table-template").cloneNode(true);
                table.id = "ownership-table";
                newContainer.innerHTML = "<h1>Data Inserted:</h1>"
                newContainer.appendChild(table);
                document.querySelector("#ownership-table #ownershipID").innerText = audit.newData.ownershipID;
                document.querySelector("#ownership-table #vehicleLicence").innerText = audit.newData.vehicle.licence;
                document.querySelector("#ownership-table #vehicleMake").innerText = audit.newData.vehicle.make;
                document.querySelector("#ownership-table #vehicleModel").innerText = audit.newData.vehicle.model;
                document.querySelector("#ownership-table #vehicleColour").innerText = audit.newData.vehicle.colour;
                document.querySelector("#ownership-table #ownerName").innerText = audit.newData.owner.firstName + " " + audit.newData.owner.lastName;
                document.querySelector("#ownership-table #ownerID").innerText = audit.newData.owner.ID;
                document.querySelector("#ownership-table #ownerLicence").innerText = audit.newData.owner.licence;
                document.querySelector("#ownership-table #ownerAddress").innerText = audit.newData.owner.address;
                document.querySelector("#ownership-table #ownerDOB").innerText = audit.newData.owner.dateOfBirth;


            } else if (audit.behaviourType == "SELECT-FOUND") {
                newContainer.innerHTML = "<h1>No New data</h1>"

                table = document.getElementById("ownership-table-template").cloneNode(true);
                table.id = "ownership-table";
                oldContainer.innerHTML = "<h1>Data Searched by the User:</h1>"
                oldContainer.appendChild(table);
                document.querySelector("#ownership-table #ownershipID").innerText = audit.oldData.ownershipID;
                document.querySelector("#ownership-table #vehicleLicence").innerText = audit.oldData.vehicle.licence;
                document.querySelector("#ownership-table #vehicleMake").innerText = audit.oldData.vehicle.make;
                document.querySelector("#ownership-table #vehicleModel").innerText = audit.oldData.vehicle.model;
                document.querySelector("#ownership-table #vehicleColour").innerText = audit.oldData.vehicle.colour;
                document.querySelector("#ownership-table #ownerName").innerText = audit.oldData.owner.firstName + " " + audit.oldData.owner.lastName;
                document.querySelector("#ownership-table #ownerID").innerText = audit.oldData.owner.ID;
                document.querySelector("#ownership-table #ownerLicence").innerText = audit.oldData.owner.licence;
                document.querySelector("#ownership-table #ownerAddress").innerText = audit.oldData.owner.address;
                document.querySelector("#ownership-table #ownerDOB").innerText = audit.oldData.owner.dateOfBirth;

            } else if (audit.behaviourType == "SELECT-EMPTY") {
                newContainer.innerHTML = "<h1>Not found ownership with the vehicle licence:</h1><p>" + audit.newData.ownershipVehicleLicence + "</p>"
            } else if (audit.behaviourType == "SELECT-FOUND-SECONDARY") {
                newContainer.innerHTML = "<h1>No New data</h1>"

                table = document.getElementById("ownership-table-template").cloneNode(true);
                table.id = "ownership-table";
                oldContainer.innerHTML = "<h1>User saw this person data when looking at an ownership data:</h1>"
                oldContainer.appendChild(table);
                document.querySelector("#ownership-table #ownershipID").innerText = audit.oldData.ownershipID;
                document.querySelector("#ownership-table #vehicleLicence").innerText = audit.oldData.vehicle.licence;
                document.querySelector("#ownership-table #vehicleMake").innerText = audit.oldData.vehicle.make;
                document.querySelector("#ownership-table #vehicleModel").innerText = audit.oldData.vehicle.model;
                document.querySelector("#ownership-table #vehicleColour").innerText = audit.oldData.vehicle.colour;
                document.querySelector("#ownership-table #ownerName").innerText = audit.oldData.owner.firstName + " " + audit.oldData.owner.lastName;
                document.querySelector("#ownership-table #ownerID").innerText = audit.oldData.owner.ID;
                document.querySelector("#ownership-table #ownerLicence").innerText = audit.oldData.owner.licence;
                document.querySelector("#ownership-table #ownerAddress").innerText = audit.oldData.owner.address;
                document.querySelector("#ownership-table #ownerDOB").innerText = audit.oldData.owner.dateOfBirth;
            } else if (audit.behaviourType == "REFERENCE-INSERT") {
                newContainer.innerHTML = "<h1>No New data</h1>"

                table = document.getElementById("ownership-table-template").cloneNode(true);
                table.id = "ownership-table";
                oldContainer.innerHTML = "<h1>User referenced this ownership data when creating an incident report:</h1>"
                oldContainer.appendChild(table);
                document.querySelector("#ownership-table #ownershipID").innerText = audit.oldData.ownershipID;
                document.querySelector("#ownership-table #vehicleLicence").innerText = audit.oldData.vehicle.licence;
                document.querySelector("#ownership-table #vehicleMake").innerText = audit.oldData.vehicle.make;
                document.querySelector("#ownership-table #vehicleModel").innerText = audit.oldData.vehicle.model;
                document.querySelector("#ownership-table #vehicleColour").innerText = audit.oldData.vehicle.colour;
                document.querySelector("#ownership-table #ownerName").innerText = audit.oldData.owner.firstName + " " + audit.oldData.owner.lastName;
                document.querySelector("#ownership-table #ownerID").innerText = audit.oldData.owner.ID;
                document.querySelector("#ownership-table #ownerLicence").innerText = audit.oldData.owner.licence;
                document.querySelector("#ownership-table #ownerAddress").innerText = audit.oldData.owner.address;
                document.querySelector("#ownership-table #ownerDOB").innerText = audit.oldData.owner.dateOfBirth;
            } else if (audit.behaviourType == "REFERENCE-UPDATE") {
                newContainer.innerHTML = "<h1>No New data</h1>"

                table = document.getElementById("ownership-table-template").cloneNode(true);
                table.id = "ownership-table";
                oldContainer.innerHTML = "<h1>User referenced this ownership data when updating an incident report:</h1>"
                oldContainer.appendChild(table);
                document.querySelector("#ownership-table #ownershipID").innerText = audit.oldData.ownershipID;
                document.querySelector("#ownership-table #vehicleLicence").innerText = audit.oldData.vehicle.licence;
                document.querySelector("#ownership-table #vehicleMake").innerText = audit.oldData.vehicle.make;
                document.querySelector("#ownership-table #vehicleModel").innerText = audit.oldData.vehicle.model;
                document.querySelector("#ownership-table #vehicleColour").innerText = audit.oldData.vehicle.colour;
                document.querySelector("#ownership-table #ownerName").innerText = audit.oldData.owner.firstName + " " + audit.oldData.owner.lastName;
                document.querySelector("#ownership-table #ownerID").innerText = audit.oldData.owner.ID;
                document.querySelector("#ownership-table #ownerLicence").innerText = audit.oldData.owner.licence;
                document.querySelector("#ownership-table #ownerAddress").innerText = audit.oldData.owner.address;
                document.querySelector("#ownership-table #ownerDOB").innerText = audit.oldData.owner.dateOfBirth;
            }
        }
        function renderVehicleData(oldContainer, newContainer, audit) {
            // render people data in a container with 760*215
            if (audit.behaviourType == "INSERT-SUCCESS") {
                oldContainer.innerHTML = "<h1>No old data</h1>"

                table = document.getElementById("vehicle-table-template").cloneNode(true);
                table.id = "vehicle-table";
                newContainer.innerHTML = "<h1>Data Inserted:</h1>"
                newContainer.appendChild(table);
                document.querySelector("#vehicle-table #vehicleID").innerText = audit.newData.ID;
                document.querySelector("#vehicle-table #vehicleLicence").innerText = audit.newData.licence;
                document.querySelector("#vehicle-table #vehicleMake").innerText = audit.newData.make;
                document.querySelector("#vehicle-table #vehicleModel").innerText = audit.newData.model;
                document.querySelector("#vehicle-table #vehicleColour").innerText = audit.newData.colour;


            } else if (audit.behaviourType == "SELECT-FOUND") {
                newContainer.innerHTML = "<h1>No new data</h1>"

                table = document.getElementById("vehicle-table-template").cloneNode(true);
                table.id = "vehicle-table";
                oldContainer.innerHTML = "<h1>Data Searched:</h1>"
                oldContainer.appendChild(table);
                document.querySelector("#vehicle-table #vehicleID").innerText = audit.oldData.ID;
                document.querySelector("#vehicle-table #vehicleLicence").innerText = audit.oldData.licence;
                document.querySelector("#vehicle-table #vehicleMake").innerText = audit.oldData.make;
                document.querySelector("#vehicle-table #vehicleModel").innerText = audit.oldData.model;
                document.querySelector("#vehicle-table #vehicleColour").innerText = audit.oldData.colour;

            } else if (audit.behaviourType == "SELECT-EMPTY") {
                newContainer.innerHTML = "<h1>Not found vehicle with the vehicle licence:</h1><p>" + audit.newData.vehicleLicence + "</p>"
            } else if (audit.behaviourType == "SELECT-FOUND-SECONDARY") {
                newContainer.innerHTML = "<h1>No New data</h1>"

                table = document.getElementById("vehicle-table-template").cloneNode(true);
                table.id = "vehicle-table";
                oldContainer.innerHTML = "<h1>User saw this vehicle data when searching an ownership data or an incident data:</h1>"
                oldContainer.appendChild(table);
                document.querySelector("#vehicle-table #vehicleID").innerText = audit.oldData.ID;
                document.querySelector("#vehicle-table #vehicleLicence").innerText = audit.oldData.licence;
                document.querySelector("#vehicle-table #vehicleMake").innerText = audit.oldData.make;
                document.querySelector("#vehicle-table #vehicleModel").innerText = audit.oldData.model;
                document.querySelector("#vehicle-table #vehicleColour").innerText = audit.oldData.colour;
            } else if (audit.behaviourType == "REFERENCE-INSERT") {

                newContainer.innerHTML = "<h1>No New data</h1>"

                table = document.getElementById("vehicle-table-template").cloneNode(true);
                table.id = "vehicle-table";
                oldContainer.innerHTML = "<h1>User referenced this vehicle data when creating/updating an ownership/an incident:</h1>"
                oldContainer.appendChild(table);
                document.querySelector("#vehicle-table #vehicleID").innerText = audit.oldData.ID;
                document.querySelector("#vehicle-table #vehicleLicence").innerText = audit.oldData.licence;
                document.querySelector("#vehicle-table #vehicleMake").innerText = audit.oldData.make;
                document.querySelector("#vehicle-table #vehicleModel").innerText = audit.oldData.model;
                document.querySelector("#vehicle-table #vehicleColour").innerText = audit.oldData.colour;
            } else if (audit.behaviourType == "REFERENCE-UPDATE") {

                newContainer.innerHTML = "<h1>No New data</h1>"

                table = document.getElementById("vehicle-table-template").cloneNode(true);
                table.id = "vehicle-table";
                oldContainer.innerHTML = "<h1>User referenced this vehicle data when creating/updating an ownership/an incident:</h1>"
                oldContainer.appendChild(table);
                document.querySelector("#vehicle-table #vehicleID").innerText = audit.oldData.ID;
                document.querySelector("#vehicle-table #vehicleLicence").innerText = audit.oldData.licence;
                document.querySelector("#vehicle-table #vehicleMake").innerText = audit.oldData.make;
                document.querySelector("#vehicle-table #vehicleModel").innerText = audit.oldData.model;
                document.querySelector("#vehicle-table #vehicleColour").innerText = audit.oldData.colour;
            } else if (audit.behaviourType == "SELECT-EMPTY-SECONDARY") {
                newContainer.innerHTML = "<h1>Not found vehicle with the vehicle licence when search an Ownership:</h1><p>" + audit.newData.vehicleLicence + "</p>"
            }
        }
        function renderIncidentData(oldContainer, newContainer, audit) {
            // render people data in a container with 760*215
            if (audit.behaviourType == "INSERT-SUCCESS") {
                oldContainer.innerHTML = "<h1>No old data</h1>"

                table = document.getElementById("report-table-template").cloneNode(true);
                table.id = "report-table";
                newContainer.innerHTML = "<h1>Data Inserted:</h1>"
                newContainer.appendChild(table);
                report = audit.newData;
                keys = Object.keys(report);
                for (key of keys) {
                    text = report[key] == "" ? "NULL" : report[key];
                    document.getElementById("report-detail-" + key).innerText = text;
                }

            } else if (audit.behaviourType == "UPDATE-SUCCESS") {
                oldTable = document.getElementById("report-table-template").cloneNode(true);
                oldTable.id = "report-table-old";
                oldContainer.innerHTML = "<h1>Before Update:</h1>"
                oldContainer.appendChild(oldTable);
                oldReport = audit.oldData;
                newReport = audit.newData;
                keys = Object.keys(oldReport);
                for (key of keys) {
                    oldText = oldReport[key] == "" ? "NULL" : oldReport[key];
                    newText = newReport[key] == "" ? "NULL" : newReport[key];
                    oldHTML = oldText == newText ? oldText : "<span style='color: red;'>" + oldText + "</span>"
                    document.querySelector("#report-table-old #report-detail-" + key).innerHTML = oldHTML;
                }

                newTable = document.getElementById("report-table-template").cloneNode(true);
                newTable.id = "report-table-new";
                newContainer.innerHTML = "<h1>After Update:</h1>"
                newContainer.appendChild(newTable);
                keys = Object.keys(newReport);
                for (key of keys) {
                    oldText = oldReport[key] == "" ? "NULL" : oldReport[key];
                    newText = newReport[key] == "" ? "NULL" : newReport[key];
                    newHTML = oldText == newText ? newText : "<span style='color: #15fc00;'>" + newText + "</span>"
                    document.querySelector("#report-table-new #report-detail-" + key).innerHTML = newHTML;
                }

            } else if (audit.behaviourType == "SELECT-FOUND") {
                newContainer.innerHTML = "<h1>No new data</h1>"

                table = document.getElementById("report-table-template").cloneNode(true);
                table.id = "report-table";
                oldContainer.innerHTML = "<h1>Data Found:</h1>"
                oldContainer.appendChild(table);
                report = audit.oldData;
                keys = Object.keys(report);
                for (key of keys) {
                    text = report[key] == "" ? "NULL" : report[key];
                    document.getElementById("report-detail-" + key).innerText = text;
                }
            } else if (audit.behaviourType == "SELECT-EMPTY") {

                oldContainer.innerHTML = "<h1>No Data Found:</h1>"
                paragraphs = []
                for (condition of audit.newData) {
                    paragraph = document.createElement("p");
                    paragraph.innerText = "condition" + ": " + condition.columnName + " = " + condition.searchValue;
                    paragraphs.push(paragraph);
                }
                newContainer.innerHTML = "<h1>Not found Incident with Following Conditions:</h1>"
                for (paragraph of paragraphs) {
                    newContainer.appendChild(paragraph)
                }
            } else if (audit.behaviourType == "SELECT-FOUND-SECONDARY") {
                console.warn("not implemented for SELECT-FOUND-SECONDARY of Incident")
            }

        }
        function renderFineData(oldContainer, newContainer, audit) {
            if (audit.behaviourType == "INSERT-SUCCESS") {
                oldTable = document.getElementById("report-table-template").cloneNode(true);
                oldTable.id = "report-table-old";
                oldContainer.innerHTML = "<h1>Before Update:</h1>"
                oldContainer.appendChild(oldTable);
                oldReport = audit.oldData;
                newReport = audit.newData;
                keys = Object.keys(oldReport);
                for (key of keys) {
                    oldText = oldReport[key] == "" ? "NULL" : oldReport[key];
                    newText = newReport[key] == "" ? "NULL" : newReport[key];
                    oldHTML = oldText == newText ? oldText : "<span style='color: red;'>" + oldText + "</span>"
                    document.querySelector("#report-table-old #report-detail-" + key).innerHTML = oldHTML;
                }

                newTable = document.getElementById("report-table-template").cloneNode(true);
                newTable.id = "report-table-new";
                newContainer.innerHTML = "<h1>After Update:</h1>"
                newContainer.appendChild(newTable);
                keys = Object.keys(newReport);
                for (key of keys) {
                    oldText = oldReport[key] == "" ? "NULL" : oldReport[key];
                    newText = newReport[key] == "" ? "NULL" : newReport[key];
                    newHTML = oldText == newText ? newText : "<span style='color: #15fc00;'>" + newText + "</span>"
                    document.querySelector("#report-table-new #report-detail-" + key).innerHTML = newHTML;
                }
            }
        }
        function renderAccountData(oldContainer, newContainer, audit) {
            if (audit.behaviourType == "INSERT-SUCCESS") {
                oldContainer.innerHTML = "<h1>No old data</h1>"

                table = document.getElementById("account-table-template").cloneNode(true);
                table.id = "vehicle-table";
                newContainer.innerHTML = "<h1>Account Created:</h1>"
                newContainer.appendChild(table);
                document.querySelector("#vehicle-table #accountUsername").innerText = audit.newData.accountUsername;
                document.querySelector("#vehicle-table #officerID").innerText = audit.newData.officerID;
                document.querySelector("#vehicle-table #officerName").innerText = audit.newData.officerName;
            } else if (audit.behaviourType == "LOGIN-SUCCESS") {
                oldContainer.innerHTML = "<h1>No old data</h1>"

                table = document.getElementById("account-table-template").cloneNode(true);
                table.id = "vehicle-table";
                newContainer.innerHTML = "<h1>User logged in:</h1>"
                newContainer.appendChild(table);
                document.querySelector("#vehicle-table #accountUsername").innerText = audit.newData.accountUsername;
                table.deleteRow(1);
                table.deleteRow(1);
            } else if (audit.behaviourType == "LOGOUT-SUCCESS") {
                oldContainer.innerHTML = "<h1>No old data</h1>"

                table = document.getElementById("account-table-template").cloneNode(true);
                table.id = "vehicle-table";
                newContainer.innerHTML = "<h1>User logged out:</h1>"
                newContainer.appendChild(table);
                document.querySelector("#vehicle-table #accountUsername").innerText = audit.newData.accountUsername;
                table.deleteRow(1);
                table.deleteRow(1);
            } else if (audit.behaviourType == "LOGIN-FAILED") {
                oldContainer.innerHTML = "<h1>No old data</h1>"

                table = document.getElementById("account-table-template").cloneNode(true);
                table.id = "vehicle-table";
                newContainer.innerHTML = "<h1>User failed to log in:</h1>"
                newContainer.appendChild(table);
                document.querySelector("#vehicle-table #accountUsername").innerText = audit.newData.accountUsername;
                table.deleteRow(1);
                table.deleteRow(1);
            } else if (audit.behaviourType == "CHANGEPASSWORD-SUCCESS") {
                oldContainer.innerHTML = "<h1>No old data</h1>"

                table = document.getElementById("account-table-template").cloneNode(true);
                table.id = "vehicle-table";
                newContainer.innerHTML = "<h1>User Changed Password:</h1>"
                newContainer.appendChild(table);
                document.querySelector("#vehicle-table #accountUsername").innerText = audit.newData.accountUsername;
                table.deleteRow(1);
                table.deleteRow(1);
            }
        }
        function removeAllRangeButtons() {
            buttons = Array.from(document.querySelectorAll("#audit-range button"));
            buttons.forEach(button => document.getElementById("audit-range").removeChild(button));
        }
        function showRangeButtons(filteredAudits) {
            auditSize = filteredAudits.length;
            numberOfButtons = Math.ceil(auditSize / 25);
            console.log(numberOfButtons);
            for (let i = 0; i < numberOfButtons; i++) {
                button = document.createElement("button");
                button.id = i * 25;
                button.addEventListener("click", () => {
                    showGeneralAuditInfo(filteredAudits, i * 25);

                    // set current button selected
                    buttons = Array.from(document.querySelectorAll("#audit-range button"));
                    console.log(buttons);
                    for (button of buttons) {
                        button.classList = "";
                    }
                    document.getElementById(String(i * 25)).classList = "button-selected";
                })
                button.innerText = String(i * 25 + 1) + " - " + String(Math.min(auditSize, i * 25 + 25))
                document.getElementById("audit-range").appendChild(button);
            }
        }
        function showFilterOptions(audits) {
            accountUsernames = new Set();
            tableNames = new Set();
            behaviourTypes = new Set();
            for (audit of audits) {
                accountUsernames.add(audit.accountUsername);
                tableNames.add(audit.tableName);
                behaviourTypes.add(audit.behaviourType);
            }
            accountUsernameSelector = document.getElementById("filter-accountUsername");
            accountUsernames.forEach((accountUsername) => {
                usernameOption = document.createElement("option");
                usernameOption.value = accountUsername;
                usernameOption.innerText = accountUsername;
                accountUsernameSelector.appendChild(usernameOption);
            })
            tableNamesSelector = document.getElementById("filter-tableName");
            tableNames.forEach((tableName) => {
                tableNameOption = document.createElement("option");
                tableNameOption.value = tableName;
                tableNameOption.innerText = tableName;
                tableNamesSelector.appendChild(tableNameOption);
            })
            behaviourTypesSelector = document.getElementById("filter-behaviourType");
            behaviourTypes.forEach((behaviourType) => {
                behaviourTypeOption = document.createElement("option");
                behaviourTypeOption.value = behaviourType;
                behaviourTypeOption.innerText = behaviourType;
                behaviourTypesSelector.appendChild(behaviourTypeOption);
            })
        }
        function toggleFilter() {
            filter = document.getElementById("audit-filter");
            if (Array.from(filter.classList).includes("invisible")) {
                // console.log("flag1")
                filter.classList = ["audit-filter"]
                document.getElementById("toggleFilter").innerText = "Hide Filter";
            } else if (Array.from(filter.classList).includes("invisible") == false) {
                // console.log("flag2")
                filter.classList = ["audit-filter invisible"]
                document.getElementById("toggleFilter").innerText = "Show Filter";
            }
        }
        function toggleHint() {
            hint = document.getElementById("hint-text");
            if (Array.from(hint.classList).includes("invisible")) {
                // console.log("flag1")
                hint.classList = ["hint-text"]
                document.getElementById("toggleHint").innerText = "Hide Hint";
            } else if (Array.from(hint.classList).includes("invisible") == false) {
                // console.log("flag2")
                hint.classList = ["hint-text invisible"]
                document.getElementById("toggleHint").innerText = "Show Hint";
            }
        }
    </script>
    <?php
        foreach($audits as $audit) {
            echo "<script>audits.push(".$audit->toJSON().")</script>";
        }
    ?>
    <div class="content">
    <div class="hint-container">
        <button onclick="toggleHint()" id="toggleHint">Show Hint</button>
        <div class="hint-text invisible" id=hint-text> 
            <p> Many types of behaviour would be recorded: 
                <li>user login</li>
                <li>user logout</li>
                <li>admin create accounts</li>
                <li>failed to login</li>
                <li>user saw some information indirectly such as saw the information of an offender of a report</li>
                <li>admin adds fine</li>
                <li>user searches people or vehicles or reports</li>
                <li>user modifies a report</li>
                <li>user adds vehicle information; user adds a report.</li>
                <li>And so on.</li>
            </p>
            <h2> Behaviour:</h2>
            <ul>
                <li>- SELECT-FOUND means a user searched for this information and found it.</li>
                <li>- SELECT-EMPTY means the information wasn't found.</li>
                <li>- REFERENCE means the data was referenced as part of another data. For example, a user created a report with an offender, so the offender's data is referenced.</li>
                <li>- The suffix of REFERENCE shows the information whether it was a reference for inserting or updating.</li>
                <li>- LOGIN-SUCCESS, LOGIN-FAILED, LOGOUT-SUCCESS, refer to login information.</li>
                <li>- UPDATE-SUCCESS means the user managed to update the data.</li>
                <li>- INSERT-SUCCESS means the user managed to create the data.</li>
                <li>- INSERT-SUCCESS for Accounts means an admin created the account, password info won't be displayed in the detail.</li>
            </ul>
       </div>
    </div>
    <div class="audit-filter-container">
        <button onclick="toggleFilter()" id="toggleFilter">Show Filter</button>
        <div class="audit-filter invisible" id="audit-filter">
            <table>
                <tr>
                    <td><input type="checkbox" name="filterByAuditID" id="filterByAuditID"></td>
                    <th>audit id:</th>
                    <td><input type="number" name="filter-auditIDMin" id="filter-auditIDMin"></td>
                    <td>to:</td>
                    <td><input type="number" name="filter-auditIDMax" id="filter-auditIDMax"></td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="filterByTime" id="filterByTime"></td>
                    <th>time:</th>
                    <td><input type="datetime-local" name="filter-timeStart" id="filter-timeStart"></td>
                    <td>to:</td>
                    <td><input type="datetime-local" name="filter-timeEnd" id="filter-timeEnd">
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="filterByAccountUsername" id="filterByAccountUsername"></td>
                    <th>username:</th>
                    <td>
                        <select name="filter-accountUsername" id="filter-accountUsername">
                            <!-- options here, from the list of array -->
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="filterByTableName" id="filterByTableName"></td>
                    <th>table name</th>
                    <td>
                        <select name="filter-tableName" id="filter-tableName">
                            <!-- options here, from the list of array -->
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="filterByBehaviourType" id="filterByBehaviourType"></td>
                    <th>behaviour type</th>
                    <td>
                        <select name="filter-behaviourType" id="filter-behaviourType">
                            <!-- options here, from the list of array -->
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><button role="button" onclick="filterAudits(audits)">confirm</button></td>
                </tr>
            </table>
        </div>
    </div>
    <div id="audit-trails">
        <div id="detail-audit-info" class="invisible">
            <button onclick="document.getElementById('detail-audit-info').classList=['invisible']">X</button>
            <div class="audit-detail-information">
                <div class="audit-detail-information-subcontainer">
                    <table class="audit-detail-table">
                        <tr>
                            <td>audit id:</td>
                            <td id="detail-audit-info-auditID"></td>
                        </tr>
                        <tr>
                            <td>account username:</td>
                            <td id="detail-audit-info-accountUsername"></td>
                        </tr>
                        <tr>
                            <td>audit time:</td>
                            <td id="detail-audit-info-auditTime"></td>
                        </tr>
                        <tr>
                            <td>behaviour type:</td>
                            <td id="detail-audit-info-behaviourType"></td>
                        </tr>
                        <tr>
                            <td>affected table name:</td>
                            <td id="detail-audit-info-tableName"></td>
                        </tr>
                        <tr>
                            <td>data id:</td>
                            <td id="detail-audit-info-tableID"></td>
                        </tr>
                    </table>
                </div>
                <div class="audit-detail-information-subcontainer" id="old-data">

                </div>
                <div class="audit-detail-information-subcontainer" id="new-data">

                </div>
            </div>
        </div>
        <div id="general-audit-info">
            <div id="audit-range">
                <!-- buttons would be added here -->
            </div>
            <table id="general-audit-table">
                <thead>
                    <tr>
                        <th>audit id</th>
                        <th>username</th>
                        <th>table name</th>
                        <th>table id</th>
                        <th>Behaviour</th>
                        <th>Behaviour time</th>
                        <th>detail</th>
                    </tr>
                </thead>
                <tbody>
                    <tr id="general-audit-info-0">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-1">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-2">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-3">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-4">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-5">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-6">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-7">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-8">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-9">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-10">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-11">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-12">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-13">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-14">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-15">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-16">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-17">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-18">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-19">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-20">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-21">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-22">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-23">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                    <tr id="general-audit-info-24">
                        <td class="audit-id"></td>
                        <td class="username"></td>
                        <td class="table-name"></td>
                        <td class="table-id"></td>
                        <td class="audit-behaviour"></td>
                        <td class="audit-time"></td>
                        <td class="detail-button"><button>show detail</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>

        filteredAudits = audits;
        showFilterOptions(audits);
        showRangeButtons(filteredAudits);
        firstButton = document.getElementById("0");
        if (firstButton != undefined && firstButton.classList != undefined) {
            firstButton.classList = ["button-selected"];
        }
        showGeneralAuditInfo(filteredAudits, 0);

        function filterAudits(audits) {
            filteredAudits = audits;
            if (document.getElementById("filterByAuditID").checked) {
                minAuditID = document.getElementById("filter-auditIDMin").value;
                maxAuditID = document.getElementById("filter-auditIDMax").value;
                filteredAudits = filteredAudits.filter(audit => parseInt(audit.auditID) >= parseInt(minAuditID) && parseInt(audit.auditID) <= parseInt(maxAuditID));
            }
            if (document.getElementById("filterByTime").checked) {
                timeStart = document.getElementById("filter-timeStart").value;
                timeEnd = document.getElementById("filter-timeEnd").value;
                filteredAudits = filteredAudits.filter(audit => {
                    auditTime = audit.auditTime.slice(0, 10) + "T" + audit.auditTime.slice(11, 16);
                    return auditTime >= timeStart && auditTime <= timeEnd;
                });
            }
            if (document.getElementById("filterByAccountUsername").checked) {
                username = document.getElementById("filter-accountUsername").value;
                filteredAudits = filteredAudits.filter(audit => audit.accountUsername == username);
            }
            if (document.getElementById("filterByTableName").checked) {
                tableName = document.getElementById("filter-tableName").value;
                filteredAudits = filteredAudits.filter(audit => audit.tableName == tableName);
            }
            if (document.getElementById("filterByBehaviourType").checked) {
                behaviourType = document.getElementById("filter-behaviourType").value;
                filteredAudits = filteredAudits.filter(audit => audit.behaviourType == behaviourType);
            }
            console.log(filteredAudits);
            removeAllRangeButtons();
            showRangeButtons(filteredAudits);
            firstButton = document.getElementById("0");
            if (firstButton != undefined && firstButton.classList != undefined) {
                firstButton.classList = ["button-selected"];
            }
            showGeneralAuditInfo(filteredAudits, 0);
        }
    </script>
    <div class="audit-detail-information-subcontainer invisible">
        <table class="people-table" id="people-table-template">
            <tbody>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Driving Licence</th>
                    <th>DOB</th>
                    <th>Total Fine</th>
                    <th>Total Points</th>
                </tr>
                <tr>
                    <td id="personID"></td>
                    <td id="personName"></td>
                    <td id="personAddress"></td>
                    <td id="personLicence"></td>
                    <td id="personDOB"></td>
                    <td id="totalFines"></td>
                    <td id="totalPoints"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="audit-detail-information-subcontainer invisible">
        <table>
            <tbody class="ownership-table" id="ownership-table-template">
                <tr>
                    <th>Ownership ID</th>
                    <th id="ownershipID"></th>
                </tr>
                <tr>
                    <td>Vehicle Licence</td>
                    <td id="vehicleLicence"></td>
                </tr>
                <tr>
                    <td>Vehicle Make</td>
                    <td id="vehicleMake"></td>
                </tr>
                <tr>
                    <td>Vehicle Model</td>
                    <td id="vehicleModel"></td>
                </tr>
                <tr>
                    <td>Vehicle Colour</td>
                    <td id="vehicleColour"></td>
                </tr>
                <tr>
                    <td>Owner Name</td>
                    <td id="ownerName"></td>
                </tr>

                <tr>
                    <td>Owner ID</td>
                    <td id="ownerID"></td>
                </tr>

                <tr>
                    <td>Owner's Licence</td>
                    <td id="ownerLicence"></td>
                </tr>
                <tr>
                    <td>Owner's Address</td>
                    <td id="ownerAddress"></td>
                </tr>
                <tr>
                    <td>Owner's DOB</td>
                    <td id="ownerDOB"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="audit-detail-information-subcontainer invisible">
        <table>
            <tbody class="vehicle-table" id="vehicle-table-template">
                <tr>
                    <th>Vehicle ID</th>
                    <th id="vehicleID"></th>
                </tr>
                <tr>
                    <td>Vehicle Licence</td>
                    <td id="vehicleLicence"></td>
                </tr>
                <tr>
                    <td>Vehicle Make</td>
                    <td id="vehicleMake"></td>
                </tr>
                <tr>
                    <td>Vehicle Model</td>
                    <td id="vehicleModel"></td>
                </tr>
                <tr>
                    <td>Vehicle Colour</td>
                    <td id="vehicleColour"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="audit-detail-information-subcontainer invisible">
        <table class="report-detail-table" id="report-table-template">
            <tr>
                <td class="report-detail-table-header">accountUsername</td>
                <td class="report-detail-table-data" id="report-detail-accountUsername"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">incidentDate</td>
                <td class="report-detail-table-data" id="report-detail-incidentDate"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">incidentID</td>
                <td class="report-detail-table-data" id="report-detail-incidentID"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">incidentReport</td>
                <td class="report-detail-table-data" id="report-detail-incidentReport"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">offenceDescription</td>
                <td class="report-detail-table-data" id="report-detail-offenceDescription"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">offenceID</td>
                <td class="report-detail-table-data" id="report-detail-offenceID"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">offenceMaxFine</td>
                <td class="report-detail-table-data" id="report-detail-offenceMaxFine"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">offenceMaxPoints</td>
                <td class="report-detail-table-data" id="report-detail-offenceMaxPoints"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">offenderAddress</td>
                <td class="report-detail-table-data" id="report-detail-offenderAddress"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">offenderDOB</td>
                <td class="report-detail-table-data" id="report-detail-offenderDOB"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">offenderID</td>
                <td class="report-detail-table-data" id="report-detail-offenderID"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">offenderLicence</td>
                <td class="report-detail-table-data" id="report-detail-offenderLicence"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">offenderName</td>
                <td class="report-detail-table-data" id="report-detail-offenderName"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">officerID</td>
                <td class="report-detail-table-data" id="report-detail-officerID"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">officerName</td>
                <td class="report-detail-table-data" id="report-detail-officerName"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">ownerAddress</td>
                <td class="report-detail-table-data" id="report-detail-ownerAddress"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">ownerDOB</td>
                <td class="report-detail-table-data" id="report-detail-ownerDOB"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">ownerID</td>
                <td class="report-detail-table-data" id="report-detail-ownerID"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">ownerLicence</td>
                <td class="report-detail-table-data" id="report-detail-ownerLicence"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">ownerName</td>
                <td class="report-detail-table-data" id="report-detail-ownerName"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">ownershipID</td>
                <td class="report-detail-table-data" id="report-detail-ownershipID"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">vehicleColour</td>
                <td class="report-detail-table-data" id="report-detail-vehicleColour"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">vehicleID</td>
                <td class="report-detail-table-data" id="report-detail-vehicleID"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">vehicleLicence</td>
                <td class="report-detail-table-data" id="report-detail-vehicleLicence"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">vehicleMake</td>
                <td class="report-detail-table-data" id="report-detail-vehicleMake"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">vehicleModel</td>
                <td class="report-detail-table-data" id="report-detail-vehicleModel"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">fineID</td>
                <td class="report-detail-table-data" id="report-detail-fineID"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">fineAmount</td>
                <td class="report-detail-table-data" id="report-detail-fineAmount"></td>
            </tr>
            <tr>
                <td class="report-detail-table-header">finePoints</td>
                <td class="report-detail-table-data" id="report-detail-finePoints"></td>
            </tr>


        </table>

    </div>
    <div class="audit-detail-information-subcontainer invisible">
        <table class="account-detail-table" id="account-table-template">
            <tr>
                <td class="account-detail-table-header">Username:</td>
                <td class="account-detail-table-data" id="accountUsername"></td>
            </tr>
            <tr>
                <td class="account-detail-table-header">Officer ID:</td>
                <td class="account-detail-table-data" id="officerID"></td>
            </tr>
            <tr>
                <td class="account-detail-table-header">Officer Name:</td>
                <td class="account-detail-table-data" id="officerName"></td>
            </tr>


        </table>

    </div>
    </div>
</body>

</html>
<?php
    }
    catch (Exception $error) {
        require("../reuse/errorMessage.php");
        echo "<div class='content'>";
        if ($error->getCode()==0) {
            renderErrorMessage($error->getMessage(),false);
        } else {
            renderErrorMessage($error->getMessage(),true);
        }
        echo "</div>";
    }
?>