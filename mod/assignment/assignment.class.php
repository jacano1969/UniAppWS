<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(dirname(__FILE__).'/../../config.php');
require_once(UNIAPP_ROOT . '/lib/externalObject.class.php');

class Assignment extends ExternalObject{

    function __construct($assignmentrecord) {
        parent::__construct($assignmentrecord);
    }

    public static function get_class_structure(){
        return new external_single_structure(
            array (
                'id'              => new external_value(PARAM_INT,  'assignment record id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'name'            => new external_value(PARAM_TEXT, 'multilang compatible name', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'intro'           => new external_value(PARAM_RAW,  'assignment description text', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'assignmenttype'  => new external_value(PARAM_ALPHA, 'assignment type: upload, online, uploadsingle, offline', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'maxbytes'        => new external_value(PARAM_INT,  'maximium bytes per submission', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'timedue'         => new external_value(PARAM_INT,  'assignment due time', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'grade'           => new external_value(PARAM_INT,  'grade scale for assignment', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
            ), 'Assignment'
        );
    }
}

?>
