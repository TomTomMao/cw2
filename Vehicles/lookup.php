<?php $pageTitle = "Lookup Vehicles";
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
    <h1>Look Up Vehicles</h1>
    <hr>
    <form action="lookup.php" method="post">
        <h3>Search by Registration Number</h3>
        <div>
            Registration Number:
            <input type="text" name="Registration Number">
        </div>
        <input type="submit" value="search">
    </form>
</body>
</html>