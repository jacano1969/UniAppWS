<?php

abstract class ExternalObject {

    private $externalObject = array();

    function __construct($record) {
        $struct = $this->get_class_structure();

        foreach ($struct->keys as $key=>$value){
            if (!isset($record->$key) && ($value->allownull != NULL_ALLOWED)) {
                throw new moodle_exception("Missing " . $key . " field");
            }
            if (!isset($record->$key) && ($value->required == VALUE_OPTIONAL)) {
                $record->$key = $value->default;
            }

            // Transform numbers to the correct format instead of strings
            if (($value->type == PARAM_INT) || ($value->type == PARAM_INTEGER) ||
                 ($value->type == PARAM_FLOAT) ||($value->type == PARAM_NUMBER)) {
                settype($record->$key, $value->type);
            }

            $this->externalObject[$key] = $record->$key;
        }
    }

    public static function get_class_structure(){}

    function get_data(){
        return $this->externalObject;
    }
}

?>
