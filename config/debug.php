<?php
    $debugOn = false;
    if ($debugOn) {
        function debugPrint_r($debugVariable) {
            echo "<div style='color: black; border: 1px black solid'>\""
            .$_SERVER['PHP_SELF']."\": ";
            echo "<span style='color: blue'>";
            print_r($debugVariable);
            echo "</span></div>";
        }
        function debugEcho($debugText) {
            echo "<div style='color: black; border: 1px black solid'>\""
            .$_SERVER['PHP_SELF']."\": ";
            echo "<span style='color: blue'>";
            echo($debugText);
            echo "</span></div>";
        }
    } else {
        function debugPrint_r($debugVariable) {
            
        }
        function debugEcho($debugText) {
            
        }
    }
    
?>