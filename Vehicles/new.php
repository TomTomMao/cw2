<?php
    $pageTitle = "Lookup Vehicle";
    require_once("../reuse/head.php");
    
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
            // auto fill the input box using person object.
            if (document.getElementById("personFirstName").value != person.firstName) {
                changeFeedback(document.getElementById("personFirstNameFeedback"), "red", "not match the person with the driving licence, auto corrected")
            }
            if (document.getElementById("personLastName").value != person.lastName) {
                changeFeedback(document.getElementById("personLastNameFeedback"), "red", "not match the person with the driving licence, auto corrected")
            }
            if (document.getElementById("personAddress").value != person.address) {
                changeFeedback(document.getElementById("personAddressFeedback"), "red", "not match the person with the driving licence, auto corrected")
            }
            if (document.getElementById("personDOB").value != person.dateOfBirth) {
                changeFeedback(document.getElementById("personDOBFeedback"), "red", "not match the person with the driving licence, auto corrected")
            }
            document.getElementById("personFirstName").value = person.firstName;
            document.getElementById("personLastName").value = person.lastName;
            document.getElementById("personAddress").value = person.address;
            document.getElementById("personDOB").value = person.dateOfBirth;

        }
        var checkers = []
        // a checker class
        function showCheckingAllFeedback() {
            // check if all checker is correct
            // if the global variable thenSubmit is true, try submit data and show feedback
            console.log(checkers.map(checker => checker.correct))
            console.log("52:" + checkers.every(checker => checker.correct))
            if (checkers.every(checker => checker.correct)) {
                console.log("rendered all data correct")
                renderFeedbackText("feedbackBox1", "feedback-green", "all data correct")
                if (thenSubmit == true) {
                    submitData()
                }
            }
            else {
                console.log("rendered some data is wrong")
                renderFeedbackText("feedbackBox1", "feedback-red", "some data is wrong")
            }
        }


        function Checker(checkingMessage, functionName, attributeName, valueElement, feedbackElement) {
            checkers.push(this);
            this.checkingMessage = checkingMessage;
            this.functionName = functionName;
            this.attributeName = attributeName;
            this.valueElement = valueElement;
            this.feedbackElement = feedbackElement;
            this.correct = false;
            this.isCheckingAll = false;
            this.isCheckingAllChecked = false;
            this.getValue = function () {
                return this.valueElement.value;
            }
            this.showFeedBack = (rsp) => {
                // console.log("=-=-=-=-=-=-=-=-")
                // console.log("entered:" + this.valueElement.id + ".showFeedBack") // debug
                // console.log("rsp:" + rsp.value)
                // console.log("rsp.detail:" + rsp.detail)
                // console.log("=-=-=-=-=-=-=-=-")
                if (rsp.value == "true") {
                    changeFeedback(this.feedbackElement, "green", rsp.detail)
                    this.correct = true;
                } else if (rsp.value == "false") {
                    changeFeedback(this.feedbackElement, "red", rsp.detail)
                    this.correct = false;
                }
                if (this.isCheckingAll) {
                    console.log("flag1")
                    console.log(this.attributeName + ": " + this.isCheckingAllChecked)
                    this.isCheckingAllChecked = true
                    console.log("flag2")
                    console.log(this.attributeName + ": " + this.isCheckingAllChecked)

                    // if all elements has been checked, call showCheckingAllFeedback(), and set all checkers' isCheckingAllChecked = false
                    if (checkers.every(checker => checker.isCheckingAllChecked)) {
                        console.log("flag3")
                        showCheckingAllFeedback()
                        checkers.forEach((checker) => {
                            console.log("setting all false")
                            checker.isCheckingAllChecked = false
                        })
                    }
                } else {
                    renderFeedbackText("feedbackBox1", "", "")
                }
            }
            this.checkAndFeedback = function (isCheckingAll = false) {
                this.isCheckingAll = isCheckingAll
                changeFeedback(this.feedbackElement, "", this.checkingMessage)
                this.isValueValid(this.showFeedBack);
            };

            this.isValueValid = function (callback) { // i referenced this ajax example when writing this function: https://www.w3schools.com/xml/ajax_database.asp
                // use server api to check if the value in 
                // assume the input is not empty
                console.assert(this.getValue()) // check the assumption
                var xhttp;
                xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function () {
                    if (this.readyState = 4 && this.status == 200 && this.responseText) {
                        rsp = parseResponseText(this.responseText);
                        // console.log("---responsed start---")
                        // console.log(rsp)
                        // console.log("---responsed over ---")
                        callback(rsp);
                    }
                };
                // console.log("API.php?function=" + this.functionName + "&" + this.attributeName + "=" + this.getValue());
                xhttp.open("GET", "API.php?function=" + this.functionName + "&" + this.attributeName + "=" + this.getValue());
                xhttp.send()
            };
        }
    </script>
<?php 
        require("../reuse/navbar.php");
    ?>
<!-- <div class="navbar">
        <a href="../People/lookup.php">Lookup People</a>
        <a href="../Vehicles/lookup.php">Lookup Vehicles</a>
        <a href="../Vehicles/new.php">New Vehicles</a>
        <a href="../Reports/new.php">New report</a>
        <a href="../Accounts/home.php">My Account</a>
    </div>
    <hr> -->
    <div class="content">
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
                <button onclick="vehicleLicenceChecker.checkAndFeedback(false)">
                    Check
                </button>
            <td>
                <span id="vehicleLicenceFeedback"></span>
            </td>
            <script>
                var vehicleLicenceChecker = new Checker("checking vehicle licence...", "isVehicleLicenceValid", "vehicleLicence",
                    document.getElementById('vehicleLicence'), document.getElementById('vehicleLicenceFeedback'));
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
                <button onclick="colourChecker.checkAndFeedback(false)">
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
                <button onclick="makeChecker.checkAndFeedback(false)">
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
                <button onclick="modelChecker.checkAndFeedback(false)">
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
                <button onclick="personFirstNameChecker.checkAndFeedback(false)">
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
                <button onclick="personLastNameChecker.checkAndFeedback(false)">
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
                <button onclick="personAddressChecker.checkAndFeedback(false)">
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
                <button onclick="personDOBChecker.checkAndFeedback(false)">
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

        function checkAll(submit = false) {
            // check all data, and give feed back
            // if submit==true: change global variable thenSubmit,
            // and then after all value checked correct, the it would try to create the vehicle


            // initialize data and views
            thenSubmit = submit
            renderFeedbackText("feedbackBox1", "", "")
            document.getElementById("feedbackBox1").innerText = ""
            document.getElementById("feedbackBox1").classList = []
            for (checker of checkers) {
                checker.isCheckingAllChecked = false
                checker.correct = false
            }
            personLicenceChecker.getPersonByLicence(displayPerson);
            for (checker of checkers) {
                checker.checkAndFeedback(isCheckingAll = true);
            }

        }
        let thenSubmit = false;
        async function submitData() {

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
                if (this.readyState = 4 && this.status == 200 && this.responseText) {
                    try {
                        insertResult = JSON.parse(this.responseText);
                        console.log(insertResult);
                        submitCallback(insertResult);
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
        function submitCallback(data) {
            if (data.state == "success") {
                renderFeedbackText("feedbackBox1", "feedback-green", "Create Vehicle Success." +
                    "new ownership id: " + data.newOwnershipID +
                    " new vehicle id:" + data.vehicleID +
                    " person id:" + data.personID);
            } else if (data.state == "failed") {
                renderFeedbackText("feedbackBox1", "feedback-red", "Failed. Reason: " + data.reason);
            } else if (data.state == "error") {
                renderFeedbackText("feedbackBox1", "feedback-red", "Error. Reason: " + data.reason)
            }
        }

    </script>
    <button onclick="checkAll()">Check All</button>
    <button onclick="checkAll(true);">Submit</button>
    <div class="" id="feedbackBox1"></div>
    </div>
</body>

</html>