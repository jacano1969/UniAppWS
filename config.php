<?php 

//  This script must be included from a Moodle page
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');   
}

define("UNIAPP_ROOT", $CFG->dirroot . '/local/uniappws');

require_once(UNIAPP_ROOT . '/uniapp_external_api.class.php');
require_once(UNIAPP_ROOT . '/util/logger.class.php');

?>
