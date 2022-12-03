<?php 
    require("_tools.php");
    function testArrayToJSON() {
        $allPass = true;
        $allPass = $allPass && ArrayToJSON([])=='{}';

        $allPass = $allPass && ArrayToJSON(["state"=>"success"])=='{"state":"success"}';

        $allPass = $allPass && ArrayToJSON(["state"=>"success", "id"=>5])=='{"state":"success","id":"5"}';

        $allPass = $allPass && ArrayToJSON([""=>"success", "id"=>5])=='{"":"success","id":"5"}';

        $allPass = $allPass && ArrayToJSON(["state"=>"", "id"=>5])=='{"state":"","id":"5"}';


        // echo ArrayToJSON([])=='{}';
        // echo "<br>";
        // echo ArrayToJSON([]);
        // echo "<hr>";
        
        // echo ArrayToJSON(["state"=>"success"])=='{"state":"success"}';
        // echo ArrayToJSON(["state"=>"success"]);
        // echo "<br>";
        // echo "<hr>";
        
        // echo ArrayToJSON(["state"=>"success", "id"=>5])=='{"state":"success","id":"5"}';
        // echo ArrayToJSON(["state"=>"success", "id"=>5]);
        // echo "<br>";
        // echo "<hr>";
        
        // echo ArrayToJSON([""=>"success", "id"=>5])=='{"":"success","id":"5"}';
        // echo ArrayToJSON([""=>"success", "id"=>5]);
        // echo "<br>";
        // echo "<hr>";
        
        // echo ArrayToJSON(["state"=>"", "id"=>5])=='{"state":"","id":"5"}';
        // echo "<br>";
        // echo ArrayToJSON(["state"=>"", "id"=>5]);
        return $allPass;
    }
    if (testArrayToJSON()) {
        echo "test arrayToJSON() all Passed";
    } else {
        echo "test arrayToJSON() not all passed";
    }
    ;
?>

