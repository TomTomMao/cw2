<?php $pageTitle = "Lookup People";
    require("../head.php");
?>
<body>
    <div class="navbar">
        <a href="../People/lookup.php">Lookup People</a>
        <a href="../Vehicles/lookup.php">Lookup Vehicles</a>
        <a href="../Vehicles/new.php">New Vehicles</a>
        <a href="../Reports/new.php">New report</a>
        <a href="../Accounts/home.php">My Account</a>
    </div>
    <hr>
    <h1>Look Up People</h1>
    <hr>
    <form action="lookup.php" method="post">
        <h3>Search by name</h3>
        <div>
            Name:
            <input type="text" name="ownerName">
        </div>
        <input type="submit" value="search">
    </form>
    <hr>
    <form action="lookup.php" method="post">
        <h3>Search by license</h3>
        <div>
            License:
            <input type="text" name="ownerLicense">
        </div>
        <input type="submit" value="search">
    </form>

</body>
</html>