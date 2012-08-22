<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(dirname(__FILE__).'/../../config.php');
require_once(UNIAPP_ROOT . '/lib/externalObject.class.php');

class Forum extends ExternalObject{

    function __construct($forumrecord) {
        parent::__construct($forumrecord);
    }

    public static function get_class_structure(){
        return new external_single_structure(
            array(
                'id'            => new external_value(PARAM_INT,        'forum id number', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'course'        => new external_value(PARAM_INT,        'course id number', VALUE_REQUIRED, '0', NULL_NOT_ALLOWED),
                'type'          => new external_value(PARAM_ALPHANUMEXT,'forum type (news, general,...)', VALUE_REQUIRED, 'general', NULL_NOT_ALLOWED),
                'name'          => new external_value(PARAM_TEXT,       'forum name', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'intro'         => new external_value(PARAM_RAW,        'forum description', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'timemodified'  => new external_value(PARAM_INT,        'date of last modification in seconds', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            ), 'Forum'
        );
    }
}

?>
