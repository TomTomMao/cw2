<?php
    class dbCondition {
        function __construct($columnName, $operator, $value, $not=false) {
            // a sql condition clause, like People_ID = 5
            // $columnName: database columnName, should be a string.
            // $operator: '=' or '>' or '>=' or or'<' or '<=' or 'IS' or 'LIKE', should be a string
            // $value: a string, if null, use the string "NULL".
            if (gettype($columnName)!="string") {
                $type = gettype($columnName);
                throw new Exception("Wrong parameter type of \$columnName, should use a string, given $type", 1);
            } 
            if (gettype($operator)!="string") {
                $type = gettype($operator);
                throw new Exception("Wrong parameter type of \$operator, should use a string, given $type", 1);
            } 
            if (gettype($value)!="string") {
                $type = gettype($value);
                throw new Exception("Wrong parameter type of \$value, should use a string, given $type", 1);
            } 
            if (gettype($not)!="boolean") {
                $type = gettype($not);
                throw new Exception("Wrong parameter type of \$not, should use a boolean, given $type", 1);
            } 
            
            $this->columnName = $columnName;
            $this->operator = $operator;
            $this->value = $value;
            $this->not = $not;
        }
        function toSql() {

        }
    }
?>