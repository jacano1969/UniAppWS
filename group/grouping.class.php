<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(dirname(__FILE__).'/../config.php');
require_once(UNIAPP_ROOT . '/lib/externalObject.class.php');

class Grouping extends ExternalObject{

    function __construct($groupingrecord) {
        parent::__construct($groupingrecord);
    }

    public static function get_class_structure(){
        return new external_single_structure(
            array (
                'id'            => new external_value(PARAM_INT,    'Grouping record id', VALUE_REQUIRED, 0 , NULL_NOT_ALLOWED),
                'name'          => new external_value(PARAM_TEXT,   'Multilang compatible name', VALUE_REQUIRED, '0' , NULL_NOT_ALLOWED),
                'description'   => new external_value(PARAM_RAW,    'Grouping description', VALUE_OPTIONAL, null , NULL_ALLOWED),
            ), 'Grouping'
        );
    }
}

?>
