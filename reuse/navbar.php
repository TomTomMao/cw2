<div class="navbar-fixed-top">
<div class="navbar">
        <?php 
            if ($user->isAdmin()) {
                echo '<a class="nav-link" id="admin" href="../Admin/home.php">Admin</a>';
            }
        ?>
        <a class="nav-link" id="lookupPeople" href="../People/lookup.php">Lookup People</a>
        <a class="nav-link" id="lookupVehicles" href="../Vehicles/lookup.php">Lookup Vehicles</a>
        <a class="nav-link" id="newVehicle" href="../Vehicles/new.php">New Vehicle</a>
        <a class="nav-link" id="newReport" href="../Reports/new.php">New report</a>
        <a class="nav-link" id="retrieveReports" href="../Reports/lookup.php">Retrieve reports</a>
        <a class="nav-link" id="myAccount" href="../Accounts/home.php">My Account</a>
        <a class="nav-link" id="myAuditTrails" href="../Accounts/myAuditTrails.php">Audit Trails</a>
        <a class="nav-link" id="logOut" href="../Accounts/logout.php">Log Out</a>
    </div>
    <script>
        function highlightElementById(elementID, hightLightClassName) {
            document.getElementById(elementID).classList=[hightLightClassName]
        }
        link = window.location.href
        linkList = link.split("/")
        if (linkList[linkList.length-2] == "Reports") {
            if(linkList[linkList.length-1] == "lookup.php") {
                highlightElementById("retrieveReports", "nav-link-highlight");
            } else if(/edit.php/.test(linkList[linkList.length-1])) {
                highlightElementById("retrieveReports", "nav-link-highlight");
            } else if(/new.php/.test(linkList[linkList.length-1])) {
                highlightElementById("newReport", "nav-link-highlight");
            }
        } else if (/Vehicles\/new.php/.test(link)) {
            highlightElementById("newVehicle", "nav-link-highlight");
        } else if (/Vehicles\/lookup.php/.test(link)) {
            highlightElementById("lookupVehicles", "nav-link-highlight");
        } else if (/People\/lookup.php/.test(link)) {
            highlightElementById("lookupPeople", "nav-link-highlight");
        } else if (/Accounts\/home.php/.test(link)) {
            highlightElementById("myAccount", "nav-link-highlight");
        } else if (/Accounts\/changePassword.php/.test(link)) {
            highlightElementById("myAccount", "nav-link-highlight");
        } else if (/Accounts\/myAuditTrails.php/.test(link)) {
            highlightElementById("myAuditTrails", "nav-link-highlight");
        } else if (/Accounts\/lookup.php/.test(link) || /Admin\/home.php/.test(link)
         || /Admin\/addFine.php/.test(link) || /Admin\/addFineSuccess.php/.test(link) || /Admin\/newAccount.php/.test(link)
         ||/Admin\/newAccountSubmit.php/.test(link)) {
            highlightElementById("admin", "nav-link-highlight");
        }
    </script>
    <hr>
</div>