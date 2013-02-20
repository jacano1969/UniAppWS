<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(dirname(__FILE__).'/../../config.php');
require_once(UNIAPP_ROOT . '/lib/externalObject.class.php');

class DiscussionStructure extends ExternalObject{

    function __construct($discussionrecord) {
        parent::__construct($discussionrecord);
    }

    public static function get_class_structure() {
        return new external_single_structure(
        array(
                'id'            => new external_value(PARAM_INT,    'discussion id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'forum'         => new external_value(PARAM_INT,    'forum id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'name'          => new external_value(PARAM_TEXT,   'discussion name', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'firstpost'     => new external_value(PARAM_INT,    'id of the first post', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'userid'        => new external_value(PARAM_INT,    'user id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'groupid'       => new external_value(PARAM_INT,    'id of the group', VALUE_REQUIRED, -1, NULL_NOT_ALLOWED),
                'timemodified'  => new external_value(PARAM_INT,    'date of last modification', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            ), 'DiscussionStructure'
        );
    }
}

?>
