<?php $pageTitle = "My audit trails";
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
    $audits = $auditDB->getAuditByUsername($user->getUsername());
?>

<body>
    <?php 
        require("../reuse/navbar.php");
    ?>
    <script>
        let audits = [];
        function getAuditByID(filteredAudits, targetAuditID) {
            targetAudit = filteredAudits.filter(audit => audit.auditID == targetAuditID)[0];
            return targetAudit;
        }
        function showGeneralAuditInfo(filteredAudit, start) {
            // remove all the event listener of show detail buttons.
            document.querySelectorAll("general-audit-info button").forEach(button => {
                button = button;
            });
            // render filteredAudit[start] to filteredAudit[start+24] into the table#general-audit-table
            for (let i = start; i <= start + 24; i++) {

                // remove the event lisener for buttons. // reference: https://bobbyhadz.com/blog/javascript-remove-all-event-listeners-from-element
                button = document.querySelector("#general-audit-info-" + (i - start) + " button");
                button.replaceWith(button.cloneNode(true));
                button = document.querySelector("#general-audit-info-" + (i - start) + " button");

                if (i < filteredAudit.length) {
                    // console.log(i)
                    // console.log(filteredAudit.length)
                    // console.log("#general-audit-info-" + (i - start) + ">td.audit-id");
                    // console.log(document.querySelector("#general-audit-info-" + (i - start) + ">td.audit-id")); // debugging
                    document.querySelector("#general-audit-info-" + (i - start) + ">td.audit-id").innerText = filteredAudit[i].auditID;
                    document.querySelector("#general-audit-info-" + (i - start) + ">td.username").innerText = filteredAudit[i].accountUsername;
                    document.querySelector("#general-audit-info-" + (i - start) + ">td.table-name").innerText = filteredAudit[i].tableName;
                    document.querySelector("#general-audit-info-" + (i - start) + ">td.table-id").innerText = filteredAudit[i].tableID;
                    document.querySelector("#general-audit-info-" + (i - start) + ">td.audit-behaviour").innerText = filteredAudit[i].behaviourType;
                    document.querySelector("#general-audit-info-" + (i - start) + ">td.audit-time").innerText = filteredAudit[i].auditTime;
                    button.classList = [""]
                    button.addEventListener("click", () => {
                        audit = filteredAudit[i];
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
                oldContainer.innerHTML = "<h1>User saw this person data when searching incidents or ownership:</h1>"
                oldContainer.appendChild(table);
                document.querySelector("#people-table #personID").innerText = audit.oldData.ID;
                document.querySelector("#people-table #personName").innerText = audit.oldData.firstName + " " + audit.oldData.lastName;
                document.querySelector("#people-table #personAddress").innerText = audit.oldData.address;
                document.querySelector("#people-table #personLicence").innerText = audit.oldData.licence;
                document.querySelector("#people-table #personDOB").innerText = audit.oldData.dateOfBirth;
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
                oldContainer.innerHTML = "<h1>User saw this person data when searching an incident:</h1>"
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
                oldContainer.innerHTML = "<h1>User saw this vehicle data when searching an ownership:</h1>"
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
            }
        }
        function showRangeButtons(filteredAudit) {
            auditSize = filteredAudit.length;
            numberOfButtons = Math.ceil(auditSize / 25);
            console.log(numberOfButtons);
            for (let i = 0; i < numberOfButtons; i++) {
                button = document.createElement("button");
                button.id = i * 25;
                button.addEventListener("click", () => {
                    showGeneralAuditInfo(filteredAudit, i * 25);
                })
                button.innerText = String(i * 25 + 1) + " - " + String(Math.min(auditSize, i * 25 + 25))
                document.getElementById("audit-range").appendChild(button);
            }
        }
    </script>
    <?php
        foreach($audits as $audit) {
            echo "<script>audits.push(".$audit->toJSON().")</script>";
        }
    ?>

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

        filteredAudit = audits;
        showRangeButtons(filteredAudit)
        showGeneralAuditInfo(filteredAudit, 0);
    </script>
    <div class="audit-detail-information-subcontainer invisible" id="old-data">
        <table class="people-table" id="people-table-template">
            <tbody>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Driving Licence</th>
                    <th>DOB</th>
                </tr>
                <tr>
                    <td id="personID"></td>
                    <td id="personName"></td>
                    <td id="personAddress"></td>
                    <td id="personLicence"></td>
                    <td id="personDOB"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="audit-detail-information-subcontainer invisible" id="old-data">
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
    <div class="audit-detail-information-subcontainer invisible" id="old-data">
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
    <div class="audit-detail-information-subcontainer invisible" id="old-data">
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
    <div class="audit-detail-information-subcontainer invisible" id="old-data">
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
</body>

</html>