<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(dirname(__FILE__).'/../../config.php');
require_once(UNIAPP_ROOT . '/lib/externalObject.class.php');

class ForumPost extends ExternalObject{

    function __construct($postrecord) {
        parent::__construct($postrecord);
    }

    public static function get_class_structure(){
        return new external_single_structure(
        array(
                'id'        => new external_value(PARAM_INT,    'post id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'parent'    => new external_value(PARAM_INT,    'parent post id', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                'userid'    => new external_value(PARAM_INT,    'user id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'discussion'=> new external_value(PARAM_INT,    'discussion id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'modified'  => new external_value(PARAM_INT,    'time of last modification in seconds', VALUE_OPTIONAL, 0, NULL_NOT_ALLOWED),
                'subject'   => new external_value(PARAM_TEXT,   'post subject', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'message'   => new external_value(PARAM_RAW,    'post message', VALUE_REQUIRED, '', NULL_NOT_ALLOWED)
            ), 'Post'
        );
    }
}

?>
