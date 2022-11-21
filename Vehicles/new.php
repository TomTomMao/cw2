<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Report</title>
</head>
<body>
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
    <form action="new.php" method="post">
        <div>
            <h3>Vehicle Information</h3>
            <div>
                Registration number: <input type="text" name="vehicleRegistrationNumber">
            </div>
            <div>
                Colour: 
                <select name="vehicleColour" id="vehicleColour">
                    <option value="white">white</option>
                    <option value="blue">blue</option>
                    <option value="green">green</option>
                    <option value="yellow">yellow</option>
                    <option value="red">red</option>
                    <option value="purple">purple</option>
                    <option value="black">black</option>
                    <option value="orange">orange</option>
                    <option value="silver">silver</option>
                </select>
            </div>
            <div>
                Maker:
                <input type="text" name="vechicleMake">
            </div>
            <div>
                Model:
                <input type="model">
            </div>
        </div>
        <hr>
        <div>
            <h3>Vehicle's Owner Information</h3>
            <div>Driving license: <input type="text" name="ownerLicense"></div>
            <div>First Name: <input type="text" name="ownerFirstName"></div>
            <div>Last Name: <input type="text" name="ownerLastName"></div>
            <div>Address: <input type="text" name="ownerAddress"></div>
        </div>
        <input type="submit" value="submit">
    </form>
</body>
</html>