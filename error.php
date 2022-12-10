<?php 
$pageTitle = "Error";
    require("reuse/head.php");
    if (isset($_GET["errorMessage"])) {
        $errorMessage = $_GET["errorMessage"];
    } else {
        $errorMessage = "no message";
    }
?>

<body>
    <h2 class="error-header">There is an Error</h2>
    <p class="error-text"><?php echo $errorMessage?></p>
    <button onclick="history.back()">go back page</button> <!-- To built this button, I referenced the example from this link https://www.w3schools.com/jsref/met_his_back.asp -->
</body>

