<?php
    // note: use require_once please.
    // when require this, use define the $debugOn as true if you want to echo out debug text, vise versa.
    // this file is a flaw.. don't use this any more.
    if ($debugOn) {
        function debugPrint_r($debugVariable, $debugOn) {
            echo "<div style='color: black; border: 1px black solid'>\""
            .$_SERVER['PHP_SELF']."\": ";
            echo "<span style='color: blue'>";
            print_r($debugVariable);
            echo "</span></div>";
        }
        function debugEcho($debugText, $debugOn) {
            echo "<div style='color: black; border: 1px black solid'>\""
            .$_SERVER['PHP_SELF']."\": ";
            echo "<span style='color: blue'>";
            echo($debugText);
            echo "</span></div>";
        }
    } else {
        function debugPrint_r($debugVariable, $debugOn) {
            if ($debugOn) {
                echo "<div style='color: black; border: 1px black solid'>\""
                .$_SERVER['PHP_SELF']."\": ";
                echo "<span style='color: blue'>";
                print_r($debugVariable);
                echo "</span></div>";
            } else {

            }
        }
        function debugEcho($debugText, $debugOn) {
            if ($debugOn) {
                echo "<div style='color: black; border: 1px black solid'>\""
                .$_SERVER['PHP_SELF']."\": ";
                echo "<span style='color: blue'>";
                echo($debugText);
                echo "</span></div>";
            } else {
                
            }
        }
    }
    
?>