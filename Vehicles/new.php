<?php
    $pageTitle = "Lookup Vehicle";
    require_once("../head.php");
    
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
    <!-- public functions in this page -->
    <script>
        var vehicle_correct = false;
        function renderFeedbackText(feedbackBoxID, feedbackBoxStyleClass, feedbackText) {
            var feedBackBox = document.getElementById(feedbackBoxID);
            feedBackBox.innerHTML = "<div class='feedback-text-line'>" + feedbackText + "</div>";
            feedBackBox.classList = [feedbackBoxStyleClass];
        }
        function changeFeedback(element, colour, text) {
            element.classList.remove("text-green");
            element.classList.remove("text-red");
            if (colour != "") {
                element.classList.add("text-" + colour);
            }
            element.innerText = text;
        }
        function parseResponseText(rsp) {
            // given a text of format: ture,detail, return an object with attributes of value and detail.
            return {
                value: rsp.split(",")[0],
                detail: rsp.split(",")[1]
            }
        }
        function displayPerson(person) {
            document.getElementById("personFirstName").value = person.firstName;
            document.getElementById("personLastName").value = person.lastName;
            document.getElementById("personAddress").value = person.address;
            document.getElementById("personDOB").value = person.dateOfBirth;

        }
        var checkers = []
        // a checker class
        function Checker(checkingMessage, functionName, attributeName, valueElement, feedbackElement) {
            checkers.push(this);
            this.checkingMessage = checkingMessage;
            this.functionName = functionName;
            this.attributeName = attributeName;
            this.valueElement = valueElement;
            this.feedbackElement = feedbackElement;
            this.correct = false;
            this.getValue = function () {
                return this.valueElement.value;
            }
            this.checkAndFeedback = function () {
                changeFeedback(this.feedbackElement, "", this.checkingMessage)
                this.isValueValid((rsp) => {
                    if (rsp.value == "true") {
                        changeFeedback(this.feedbackElement, "green", rsp.detail)
                        this.correct = true;
                    } else if (rsp.value == "false") {
                        changeFeedback(this.feedbackElement, "red", rsp.detail)
                        this.correct = false;
                        return false;
                    }
                });
            };
            this.isValueValid = function (callback) { // i referenced this ajax example when writing this function: https://www.w3schools.com/xml/ajax_database.asp
                // use server api to check if the value in 
                // assume the input is not empty
                console.assert(this.getValue()) // check the assumption
                var xhttp;
                xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function () {
                    if (this.readyState = 4 && this.status == 200) {
                        rsp = parseResponseText(this.responseText);
                        callback(rsp);
                    }
                };
                console.log("API.php?function=" + this.functionName + "&" + this.attributeName + "=" + this.getValue());
                xhttp.open("GET", "API.php?function=" + this.functionName + "&" + this.attributeName + "=" + this.getValue());
                xhttp.send()
            };
        }
    </script>
    <div class="navbar">
        <a href="../People/lookup.php">Lookup People</a>
        <a href="../Vehicles/lookup.php">Lookup Vehicles</a>
        <a href="../Vehicles/new.php">New Vehicles</a>
        <a href="../Reports/new.php">New report</a>
        <a href="../Accounts/home.php">My Account</a>
    </div>
    <hr>
    <h1>Create New Vehicle</h1>
    <hr>
    <h2>Vehicle Information</h2>
    <table id="newVehicleVehicleInfo">
        <tr>
            <td>
                vehicle licence:

            </td>
            <td>
                <input type="text" id="vehicleLicence"></input>
            </td>
            <td>
                <button
                    onclick="checkVehicleLicence(document.getElementById('vehicleLicence').value, document.getElementById('vehicleLicenceFeedback'))">
                    Check
                </button>
            <td>
                <span id="vehicleLicenceFeedback"></span>
            </td>
            <script>
                function checkVehicleLicence(vehicleLicence, feedbackElement) {

                    changeFeedback(feedbackElement, "", "checking Vehicle Licence...")
                    if (!vehicleLicence) { // check if empty
                        changeFeedback(feedbackElement, "red", "please enter an vehicle Licence");
                    } else { // then check if the format is valid
                        validResult = isVehicleLicenceValid(vehicleLicence)
                        if (!validResult[0]) {
                            changeFeedback(feedbackElement, "red", "Invalid licence: " + validResult[1])
                        } else {
                            isVehicleLicenceExists(vehicleLicence, (rsp) => { //this would wait server response
                                if (rsp.value == "true") {
                                    vehicle_correct = false;
                                    changeFeedback(feedbackElement, "red", rsp.detail)
                                } else if (rsp.value == "false") {
                                    vehicle_correct = true;
                                    changeFeedback(feedbackElement, "green", rsp.detail)
                                }
                            });
                        }
                    }

                }

                function isVehicleLicenceValid(vehicleLicence) {
                    if (vehicleLicence.search(" ") >= 0) {
                        return [false, "there shouldn't be any space"];
                    } else if (vehicleLicence.length != 7) {
                        return [false, "the length of vehicle licence should be 7"];
                    }
                    return [true, "the vehicle licence is valid"];
                }

                function isVehicleLicenceExists(vehicleLicence, callback) { // i referenced this ajax example when writing this function: https://www.w3schools.com/xml/ajax_database.asp
                    // assume the input is not empty
                    var xhttp;
                    xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function () {
                        console.assert(vehicleLicence) // check the assumption
                        if (this.readyState = 4 && this.status == 200) {
                            rsp = parseResponseText(this.responseText);
                            callback(rsp);
                        }
                    };
                    xhttp.open("GET", "API.php?function=isVehicleLicenceExists&vehicleLicence=" + vehicleLicence);
                    xhttp.send()
                }
            </script>
        </tr>
        <tr>
            <td>
                colour:

            </td>
            <td>
                <input type="text" id="vehicleColour"></input>
            </td>
            <td>
                <button onclick="colourChecker.checkAndFeedback()">
                    Check
                </button>
            </td>
            <td>
                <span id="vehicleColourFeedback"></span>
            </td>
            <script>
                var colourChecker = new Checker("checking vehicle colour...", "isColourValid", "colour",
                    document.getElementById('vehicleColour'), document.getElementById('vehicleColourFeedback'));
            </script>
        </tr>
        <tr>
            <td>
                Make:

            </td>
            <td>
                <input type="text" id="vehicleMake"></input>
            </td>
            <td>
                <button onclick="makeChecker.checkAndFeedback()">
                    Check
                </button>
            </td>
            <td>
                <span id="vehicleMakeFeedback"></span>
            </td>
            <script>
                var makeChecker = new Checker("checking vehicle make...", "isMakeValid", "make",
                    document.getElementById('vehicleMake'), document.getElementById('vehicleMakeFeedback'));
            </script>
        </tr>
        <tr>
            <td>
                Model:

            </td>
            <td>
                <input type="text" id="vehicleModel"></input>
            </td>
            <td>
                <button onclick="modelChecker.checkAndFeedback()">
                    Check
                </button>
            </td>
            <td>
                <span id="vehicleModelFeedback"></span>
            </td>
            <script>
                var modelChecker = new Checker("checking vehicle model...", "isModelValid", "model",
                    document.getElementById('vehicleModel'), document.getElementById('vehicleModelFeedback'));
            </script>
        </tr>

        <!-- owner's info -->
    </table>

    <hr>
    <h2>Owner's Information</h2>
    <table id="newVehiclePersonInfo">
        <tr id="newVehicleOwnerLicenceInputRow">
            <td>
                Onwer's licence:

            </td>
            <td>
                <input type="text" id="personLicence"></input>
            </td>
            <td>
                <button
                    onclick="personLicenceChecker.checkAndFeedback();personLicenceChecker.getPersonByLicence(displayPerson)">
                    Check
                </button>
            <td>
                <span id="personLicenceFeedback"></span>
            </td>
            <script>
                var personLicenceChecker = new Checker("checking person...", "isPersonLicenceInDB", "personLicence",
                    document.getElementById('personLicence'), document.getElementById('personLicenceFeedback'));

                personLicenceChecker.getPersonByLicence = function (callback) {// i referenced this ajax example when writing this function: https://www.w3schools.com/xml/ajax_database.asp
                    // get person object from database
                    var xhttp;
                    xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function () {
                        if (this.readyState = 4 && this.status == 200) {
                            try {
                                person = JSON.parse(this.responseText);
                                console.log(person);
                                callback(person);
                            } catch (error) {
                                console.log(error);
                            }
                        }
                    };
                    xhttp.open("GET", "API.php?function=" + "getPersonByLicence" + "&" + "personLicence" + "=" + this.getValue());
                    xhttp.send()
                }
            </script>
        </tr>
        <tr id="newVehicleFirstNameInputRow">
            <td>
                Onwer's First Name:
            </td>
            <td>
                <input type="text" id="personFirstName"></input>
            </td>
            <td>
                <button onclick="personFirstNameChecker.checkAndFeedback()">
                    Check
                </button>
            <td>
                <span id="personFirstNameFeedback"></span>
            </td>
            <script>
                var personFirstNameChecker = new Checker("checking First name...", "isFnameValid", "fName",
                    document.getElementById('personFirstName'), document.getElementById('personFirstNameFeedback'));
            </script>
        </tr>
        <tr id="newVehicleLastNameInputRow">
            <td>
                Onwer's Last Name:
            </td>
            <td>
                <input type="text" id="personLastName"></input>
            </td>
            <td>
                <button onclick="personLastNameChecker.checkAndFeedback()">
                    Check
                </button>
            <td>
                <span id="personLastNameFeedback"></span>
            </td>
            <script>
                var personLastNameChecker = new Checker("checking last name...", "isLnameValid", "lName",
                    document.getElementById('personLastName'), document.getElementById('personLastNameFeedback'));
            </script>
        </tr>
        <tr id="newVehicleAddressInputRow">
            <td>
                Onwer's Address:
            </td>
            <td>
                <input type="text" id="personAddress"></input>
            </td>
            <td>
                <button onclick="personAddressChecker.checkAndFeedback()">
                    Check
                </button>
            <td>
                <span id="personAddressFeedback"></span>
            </td>
            <script>
                var personAddressChecker = new Checker("checking address...", "isAddressValid", "address",
                    document.getElementById('personAddress'), document.getElementById('personAddressFeedback'));
            </script>
        </tr>
        <tr id="newVehicleDOBInputRow">
            <td>
                Onwer's DOB:
            </td>
            <td>
                <input type="date" id="personDOB"></input>
            </td>
            <td>
                <button onclick="personDOBChecker.checkAndFeedback()">
                    Check
                </button>
            <td>
                <span id="personDOBFeedback"></span>
            </td>
            <script>
                var personDOBChecker = new Checker("checking address...", "isDOBValid", "DOB",
                    document.getElementById('personDOB'), document.getElementById('personDOBFeedback'));
            </script>
        </tr>
    </table>
    <hr>
    <script>

        function checkAll() {
            checkVehicleLicence(document.getElementById('vehicleLicence').value, document.getElementById('vehicleLicenceFeedback'))
            for (checker of checkers) {
                checker.checkAndFeedback();
            }

            // wait for 2 second check status, should use aysnc way to wait all checkAndFeedback down
            renderFeedbackText("feedbackBox1", "feedback-green", "Please Waiting...");
            setTimeout(() => {

                var correct = vehicle_correct;
                for (checker of checkers) {
                    correct = checker.correct && correct
                    if (!checker.correct) {
                        renderFeedbackText("feedbackBox1", "feedback-red", "Please check the input");
                    }
                }
                if (correct && document.getElementById("feedbackBox1").innerText != "Create Vehicle Success.") {
                    renderFeedbackText("feedbackBox1", "feedback-green", "all correct");
                } else {
                    renderFeedbackText("feedbackBox1", "feedback-red", "Please check the input");
                }
            }, 2000)

        }
        async function submitData(callback) {
            
            var vehicleLicence = document.getElementById("vehicleLicence").value;
            var vehicleColour = document.getElementById("vehicleColour").value;
            var vehicleMake = document.getElementById("vehicleMake").value;
            var vehicleModel = document.getElementById("vehicleModel").value;
            var personLicence = document.getElementById("personLicence").value;
            var personFirstName = document.getElementById("personFirstName").value;
            var personLastName = document.getElementById("personLastName").value;
            var personAddress = document.getElementById("personAddress").value;
            var personDOB = document.getElementById("personDOB").value;


            // i referenced this ajax example when writing next about 10 lines of codes : https://www.w3schools.com/xml/ajax_database.asp
            var xhttp;
            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState = 4 && this.status == 200) {
                    try {
                        insertResult = JSON.parse(this.responseText);
                        console.log(insertResult);
                        callback(insertResult);
                    } catch (error) {
                        console.log(error);
                    }
                }
            };
            var parameters = "function=createNewVehicleWithOwner" +
                "&vehicleLicence=" + vehicleLicence
                + "&vehicleColour=" + vehicleColour
                + "&vehicleMake=" + vehicleMake
                + "&vehicleModel=" + vehicleModel
                + "&personLicence=" + personLicence
                + "&personFirstName=" + personFirstName
                + "&personLastName=" + personLastName
                + "&personAddress=" + personAddress
                + "&personDOB=" + personDOB;
            xhttp.open("GET", "API.php?" + parameters);
            xhttp.send()
        };
        function sumbitCallBack(data) {
            if (data.state == "success") {
                renderFeedbackText("feedbackBox1", "feedback-green", "Create Vehicle Success.");
            } else if (data.state == "failed") {
                renderFeedbackText("feedbackBox1", "feedback-red", "Failed. Reason: " + data.reason);
            }
        }

    </script>
    <button onclick="checkAll()">Check All</button>
    <button onclick="checkAll();submitData(sumbitCallBack);">Submit</button>
    <div class="" id="feedbackBox1"></div>
</body>

</html>