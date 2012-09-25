<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(dirname(__FILE__).'/../../config.php');
require_once(UNIAPP_ROOT . '/lib/externalObject.class.php');

class Folder extends ExternalObject{

    function __construct($folderrecord) {
        parent::__construct($folderrecord);
    }

    public static function get_class_structure(){
		 return new external_single_structure(
				array(
					'rootid'   => new external_value(PARAM_INT,       'folderid; folder module', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
					'parent'   => new external_value(PARAM_TEXT,      'folder parent', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
					'name'     => new external_value(PARAM_TEXT,      'folder name', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
					'size'     => new external_value(PARAM_INT,       'file size', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
					'mime'     => new external_value(PARAM_TEXT,      'file mime', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
					'type'     => new external_value(PARAM_TEXT,      'dir or file', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
					'url'      => new external_value(PARAM_TEXT,      'file url', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
				), 'Folder'
		);
    }
}

?>
