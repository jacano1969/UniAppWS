<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(dirname(__FILE__).'/../config.php');
require_once(UNIAPP_ROOT . '/lib/externalObject.class.php');

class UserStructure extends ExternalObject{

    function __construct($userrecord) {
        parent::__construct($userrecord);
    }

    public static function get_class_structure(){
        return new external_single_structure(
            array(
                'id'            => new external_value(PARAM_INT,    'ID of the user', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'username'      => new external_value(PARAM_RAW,    'Username policy is defined in Moodle security config', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'idnumber'      => new external_value(PARAM_RAW,    'An arbitrary ID code number perhaps from the institution', VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                'firstname'     => new external_value(PARAM_TEXT,   'The first name of the user', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'lastname'      => new external_value(PARAM_TEXT,   'The family name of the user', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'email'         => new external_value(PARAM_EMAIL,  'A valid and unique email address', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'city'          => new external_value(PARAM_TEXT,   'Home city of the user', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'country'       => new external_value(PARAM_TEXT,   'Home country code of the user, such as AU or CZ', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'lang'          => new external_value(PARAM_TEXT,   'Language code such as "en", must exist on server', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'avatar'        => new external_value(PARAM_URL,    'URL of the user avatar', VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                'timemodified'  => new external_value(PARAM_INT,    'Time of last modification in seconds', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }

}

?>
