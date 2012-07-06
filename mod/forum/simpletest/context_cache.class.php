<?php 
require_once($CFG->dirroot . '/lib/accesslib.php');

class context_cache extends context{
	static function get_context($contextlevel, $instanceid) {
        if ($context = self::cache_get($contextlevel, $instanceid)) {
            return $context;
        } else {
			return null;
		}
	}
}

?>
