<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(UNIAPP_ROOT . '/lib/externalObject.class.php');

class ModuleStructure extends ExternalObject{

    function __construct($courserecord) {
        parent::__construct($courserecord);
    }

    public static function get_class_structure() {
        return new external_single_structure(
            array(
					'id' => new external_value(PARAM_INT, 'course module id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
					'instanceid' => new external_value(PARAM_INT, 'module id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
					'courseid' => new external_value(PARAM_INT, 'course id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
					'modname' => new external_value(PARAM_TEXT, 'module type', VALUE_REQUIRED, 'mod_unknown', NULL_NOT_ALLOWED),
					'type' => new external_value(PARAM_TEXT, 'module title', VALUE_OPTIONAL),
					'name' => new external_value(PARAM_TEXT, 'module title', VALUE_REQUIRED, 'name_unknow', NULL_NOT_ALLOWED),
					'intro' => new external_value(PARAM_RAW, 'module intro', VALUE_OPTIONAL),
					'timemodified' => new external_value(PARAM_INT, 'modification time', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
					'section' => new external_value(PARAM_INT, 'section number', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)

			), 'ModuleStructure'
        );
    }

}

?>
