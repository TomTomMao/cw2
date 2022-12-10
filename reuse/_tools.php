<?php 
    function arrayToJSON($array) {
        // $array: an associative-array, lengh could be 0 or more than 0;
        // return a json-parsable string
        // for example: ["key1"=>"value1", "key2"=>"value2"] => '{"key1":"value1","key2":"value2"}'
        if (count($array)==0) {
            return "{}";
        } else {
            $json = '{';
                $jsonPairs = []; // array pair in json text format
                foreach($array as $key=>$value) {
                    array_push($jsonPairs, '"'.$key.'":"'.$value.'"'); // $jsonPairs[i] = '"keyi":"valuei"'
                }
                $json = $json.$jsonPairs[0];
                if (count($jsonPairs) > 1) {
                    for ($i = 1; $i < count($jsonPairs); $i += 1){
                        $json = $json.",".$jsonPairs[$i];
                    }
                }
                $json = $json."}";
                return $json;
            }
    }
    function assignJSONToJs($jsonString, $jsVarName) {
        // $jsonString: a json string
        // $jsVarName: the variable name in javascript for saving the json string.

        echo "<script>let $jsVarName=$jsonString</script>";
    }
?>

