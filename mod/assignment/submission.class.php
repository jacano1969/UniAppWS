<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(dirname(__FILE__).'/../../config.php');
require_once(UNIAPP_ROOT . '/lib/externalObject.class.php');

class Submission extends ExternalObject{

    function __construct($submissionrecord) {
        parent::__construct($submissionrecord);
    }

    public static function get_class_structure() {
        return new external_single_structure(
            array (
                'id'                => new external_value(PARAM_INT, 'submission id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'assignment'        => new external_value(PARAM_INT, 'assignment id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'userid'            => new external_value(PARAM_INT, 'user id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                //probably will need numfiles
                'data1'             => new external_value(PARAM_CLEANHTML,  'assignment data', VALUE_OPTIONAL, '0', NULL_ALLOWED),
                'grade'             => new external_value(PARAM_FLOAT,      'grade', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'submissioncomment' => new external_value(PARAM_RAW,        'submission comment', VALUE_REQUIRED, '0', NULL_NOT_ALLOWED)
            ), 'Submission'
        );
    }
}
?>
