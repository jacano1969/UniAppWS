<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(UNIAPP_ROOT . '/grade/externallib.php');
require_once(UNIAPP_ROOT . '/lib/externalObject.class.php');

class GradeItemStructure extends ExternalObject {

    function __construct($gradeitemrecord) {
        parent::__construct($gradeitemrecord);
    }

    public static function get_class_structure(){
        return new external_single_structure(
            array(
                'id'                => new external_value(PARAM_INT,        'grade item id number', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'courseid'          => new external_value(PARAM_INT,        'the id of the course, the graded item belongs to', VALUE_REQUIRED, null, NULL_ALLOWED),
                'itemname'          => new external_value(PARAM_TEXT,       'name of the graded item', VALUE_REQUIRED, null, NULL_ALLOWED),
                'itemtype'          => new external_value(PARAM_ALPHA,      'type of the graded item', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'itemmodule'        => new external_value(PARAM_ALPHANUMEXT,'module of the grade item', VALUE_REQUIRED, null, NULL_ALLOWED),
                'iteminstance'      => new external_value(PARAM_INT,        'instance of the grade item', VALUE_REQUIRED, null, NULL_ALLOWED),
                'itemnumber'        => new external_value(PARAM_INT,        'number of the grade item', VALUE_REQUIRED, null, NULL_ALLOWED),
                'gradepass'         => new external_value(PARAM_FLOAT,      'minimum grade needed to pass', VALUE_REQUIRED, 0.00, NULL_NOT_ALLOWED),
                'grademax'          => new external_value(PARAM_FLOAT,      'maximum attainable grade', VALUE_REQUIRED, 100.00, NULL_NOT_ALLOWED),
                'grademin'          => new external_value(PARAM_FLOAT,      'minimum attainable grade', VALUE_REQUIRED, 0.00, NULL_NOT_ALLOWED),
                'locked'            => new external_value(PARAM_INT,        'grade is locked against further changes', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'hidden'            => new external_value(PARAM_INT,        'grade is hidden from users without the required privileges', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'timecreated'       => new external_value(PARAM_INT,        'time of creation in seconds', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
                'timemodified'      => new external_value(PARAM_INT,        'time of last modification in seconds', VALUE_REQUIRED, null, NULL_ALLOWED)
            ), 'GradeItemStructure'
        );
    }

}
