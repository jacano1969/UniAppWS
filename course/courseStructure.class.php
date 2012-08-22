<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(UNIAPP_ROOT . '/lib/externalObject.class.php');

class CourseStructure extends ExternalObject{

    function __construct($courserecord) {
        parent::__construct($courserecord);
    }

    public static function get_class_structure() {
        return new external_single_structure(
            array(
                'id'            => new external_value(PARAM_INT,            'course id number', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'idnumber'      => new external_value(PARAM_RAW,            'id number', VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                'category'      => new external_value(PARAM_INT,            'course category id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'fullname'      => new external_value(PARAM_TEXT,           'full name of the course', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'shortname'     => new external_value(PARAM_TEXT,           'short name of the course', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'summary'       => new external_value(PARAM_RAW,            'course description', VALUE_OPTIONAL, null, NULL_ALLOWED),
                'format'        => new external_value(PARAM_ALPHANUMEXT,    'course format: weeks, topics, social, site,..', VALUE_DEFAULT, 1, NULL_NOT_ALLOWED),
                'startdate'     => new external_value(PARAM_INT,            'timestamp for course start', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'groupmode'     => new external_value(PARAM_INT,            'no group, separate, visible', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                'lang'          => new external_value(PARAM_ALPHANUMEXT,    'forced course language', VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                'timecreated'   => new external_value(PARAM_INT,            'timestamp of course creation', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'timemodified'  => new external_value(PARAM_INT,            'timestamp of course last modification', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'showgrades'    => new external_value(PARAM_INT,            '1 if grades are shown, otherwise 0', VALUE_DEFAULT, 1, NULL_NOT_ALLOWED)
            ), 'CourseStructure'
        );
    }

}

?>
