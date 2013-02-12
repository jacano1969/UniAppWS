<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(dirname(__FILE__).'/../../config.php');
require_once(UNIAPP_ROOT . '/lib/externalObject.class.php');

class Choice extends ExternalObject{

    function __construct($resourcerecord) {
        parent::__construct($resourcerecord);
    }

    public static function get_class_structure(){
        return new external_single_structure(
        array(
                'id'            => new external_value(PARAM_INT,        'resource id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'course'        => new external_value(PARAM_INT,        'course id', VALUE_REQUIRED, '0', NULL_NOT_ALLOWED),
                'name'          => new external_value(PARAM_TEXT,       'resource name', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'intro'         => new external_value(PARAM_RAW,        'resource description', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'timemodified'  => new external_value(PARAM_INT,        'date of last modification in seconds', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'options'       => new external_multiple_structure(
					new external_single_structure(
						array(
                		'optionid'    => new external_value(PARAM_INT,        'choice id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                		'text'  => new external_value(PARAM_TEXT,       'resource name', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
						)
					)
				),
                'answer'        => new external_value(PARAM_INT,        'the option id chosen by the user', VALUE_REQUIRED, '0'),
            ), 'Choice'
        );
    }
}

?>
