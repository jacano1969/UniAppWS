<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(dirname(__FILE__).'/../config.php');
require_once(UNIAPP_ROOT . '/lib/externalObject.class.php');

class Group extends ExternalObject{

    function __construct($grouprecord) {
        parent::__construct($grouprecord);
    }

    public static function get_class_structure(){
        return new external_single_structure(
            array (
                'id'            => new external_value(PARAM_INT,    'Group record id', VALUE_REQUIRED, 0 , NULL_NOT_ALLOWED),
                'name'          => new external_value(PARAM_TEXT,   'Multilang compatible name, course unique', VALUE_REQUIRED, '0' , NULL_NOT_ALLOWED),
                'description'   => new external_value(PARAM_RAW,    'Group description', VALUE_OPTIONAL, null , NULL_ALLOWED),
            ), 'Group'
        );
    }
}

?>
