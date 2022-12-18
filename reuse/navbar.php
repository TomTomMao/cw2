<div class="navbar-fixed-top">
<div class="navbar">
        <?php 
            if ($user->isAdmin()) {
                echo '<a href="../Admin/home.php">Admin</a>';
            }
        ?>
        <a href="../People/lookup.php">Lookup People</a>
        <a href="../Vehicles/lookup.php">Lookup Vehicles</a>
        <a href="../Vehicles/new.php">New Vehicles</a>
        <a href="../Reports/new.php">New report</a>
        <a href="../Reports/lookup.php">Retrieve report</a>
        <a href="../Accounts/home.php">My Account</a>
        <a href="../Accounts/myAuditTrails.php">My Audit Trails</a>
        <a href="../Accounts/logout.php">Log Out</a>
    </div>
    <hr>
</div>