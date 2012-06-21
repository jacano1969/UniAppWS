<?php 
class Logger {
	/**
	 * Writes the user activity inside the log 
	 */
	public static function add($courseid, $moduleid, $userid, $action){
		add_to_log($courseid, 'uniappws', 'local_uniappws_'.$action, '', getremoteaddr() , $moduleid, $userid);
	}
}
?>
