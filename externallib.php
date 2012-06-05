<?php

// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * UniApp web service 
 *
 * @package    localuniappws
 * @copyright  2012 eLab (http://www.elearninglab.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");

class local_uniappws_external extends external_api {

//********************************************************************************************
//********************************************************************************************
// Web services
//********************************************************************************************
//********************************************************************************************


//=========== get_course_modules web service ====================================================

	/**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_course_modules_parameters() {
        return new external_function_parameters(
                array( 'id' => new external_value(PARAM_INT, 'The course id', VALUE_DEFAULT, '') )
        );
    }

	/**
     * Returns the list of modules from the course
     */
    public static function get_course_modules($id) {
        global $USER, $DB;
		
        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::get_course_modules_parameters(), array('id' => $id));

        //Context validation
        //OPTIONAL but in most web service it should present
		$context = self::get_context_by_token($_GET['wstoken']); 

        self::validate_context($context);

		$course_context = get_context_instance(CONTEXT_COURSE, $id);
		
		// Context checking
		// This controll enforces the controll on the token.
		// It checks that the token belongs to the course whose id is $id.
		if($context != $course_context) {
            throw new moodle_exception('invalidcourseid');
		}
		
        //Capability checking
        //OPTIONAL but in most web service it should present
        /*
		if(!has_capability('moodle/course:view', $context)) {
            throw new moodle_exception('usernotincourse');
        }
		*/

		// module extraction alternative
		/*	
		$forums = get_coursemodules_in_course('forum', $id);
		foreach($forums as $forum_id => $forum) {
			$result = coursemodule_visible_for_user($forum, $USER->id);
			print_r($result);
			if ( $result == 1 ){
				echo '<hr />is visible<br /><br />'; 
				print_r($forum);
				echo '<hr /><br /><br />';
			} else {
				echo 'is not visible<hr /><br /><br />'; 
				print_r($forum);
				echo '<hr /><br /><br />';
			}
		}

		self::dump($forums);
		$course = $DB->get_record('course', array('id' => $id));
		$forums = get_all_instances_in_course('forum', $course, $USER->id, false);
		self::dump($forums);
		exit;
		*/
		// list of possible modulenames		
		//| assignment  |
		//| certificate |
		//| chat        |
		//| choice      |
		//| data        |
		//| feedback    |
		//| folder      |
		//| forum       |
		//| glossary    |
		//| imscp       |
		//| label       |
		//| lesson      |
		//| lti         |
		//| page        |
		//| quiz        |
		//| resource    |
		//| scorm       |
		//| survey      |
		//| url         |
		//| wiki        |
		//| workshop    |
		$module_names = array( 'forum', );
		/*
		$course = $DB->get_record('course', array('id' => $id));
		$forums = get_all_instances_in_course('forum', $course, $USER->id, false);
		foreach($forums as $forum_id => $forum) {
			print_r($forum);
			echo '<hr /><br /><br />';
		}
		*/

		$course = $DB->get_record('course', array('id' => $id));
		$modules_list = array();
		foreach($module_names as $module_name) {
			// get determined type of modules
			$course_modules = get_all_instances_in_course($module_name, $course, $USER->id);

			foreach($course_modules as $course_module) {
				$modules_list[count($modules_list)] = array(
					'id' => $course_module->coursemodule,
					'instanceid' => $course_module->id,
					'courseid' => $course_module->course,
					'modname' => $module_name,
					'name' => $course_module->name,
					'intro' => $course_module->intro,
					'timemodified' => $course_module->timemodified
				);
			}
		}
		self::addToLog($id, 0, $USER->id, 'get_course_modules');

		return $modules_list;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_course_modules_returns() {
		return new external_multiple_structure( 
					new external_single_structure( array(
							'id' => new external_value(PARAM_INT, 'course module id', true),
							'instanceid' => new external_value(PARAM_INT, 'module id', true),
							'courseid' => new external_value(PARAM_INT, 'course id', true),
							'modname' => new external_value(PARAM_TEXT, 'module type', true),
							'name' => new external_value(PARAM_TEXT, 'module title', true),
							'intro' => new external_value(PARAM_RAW, 'module intro', false),
							'timemodified' => new external_value(PARAM_INT, 'modification time', true)
							)	
						) 
					);
    }

//********************************************************************************************
//********************************************************************************************
// Utility functions
//********************************************************************************************
//********************************************************************************************

	// just a dump utility
	private static function dump($object) {
		print_r('<hr />');
		print_r('<hr />');
		print_r('dumping');
		foreach($object as $k => $v) {
			print_r('<hr />');
			print_r($k);
			print_r('<hr />');
			print_r($v);
			print_r('<hr />');
			print_r('<hr />');
			print_r('<br />');
			print_r('<br />');
		}
	}

	/**
     * extracts the context
	 * @return session object or false if the session is not valid
     */
    private static function get_context_by_token($token) {
		global $DB;
		$token_entry = $DB->get_record('external_tokens', array('token'=>$token) );
		return get_context_instance_by_id($token_entry->contextid);
    }

	/**
     * Checks the session type and returns it
	 * @return session object or false if the session is not valid
     */
    private static function get_session($sid) {
		if ( session_is_legacy() ) { // extract the session from the file
			$session = self::extract_user_session_from_file($sid);
		} else {
			$session = self::extract_user_session_from_db($sid);
		}
		
		if( $session === false ) {
			return false;	
		} else {
			return $session;	
		}
    }

	/**
     * Extracts the session object from the session file
     * @return session object or false if the session is not valid
     */
	private static function extract_user_session_from_file($sid) {
		$file_session = new legacy_file_session();
		if( $file_session->session_exists($sid) ) {
			global $CFG;
			$session_file = $CFG->dataroot.'/sessions/sess_'.$sid;
			$session_file_content = file_get_contents($session_file);
			$session = self::unserializesession($session_file_content);
			return $session;
		} else {
			return false; // no valid sessions found
		}
	}

	/**
     * Returns the session object given the session file
     * @return session object or false if the session is not valid
     */
	private static function extract_user_session_from_db($sid) {
		$db_session = new database_session();
		if ( $db_session->session_exists($sid) ) {
			//self::dump($db_session->handler_read($sid));
			$sessdata = $db_session->handler_read($sid);
			$session = self::unserializesession($sessdata);
			return $session;
		} else {
			return false;	
		}
	}

	/**
     * Unserialize serialized sessions
     * @return session object 
     */
	private static function unserializesession($serialized_string) {
		$variables = array();
		$a = preg_split("/(\w+)\|/", $serialized_string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		$counta = count($a);
		for ($i = 0; $i < $counta; $i = $i+2) {
			$variables[$a[$i]] = unserialize($a[$i+1]);
		}
		return $variables;
	}	

	/**
     * Writes inside the log the user activity
     */
	private static function addToLog($courseid, $moduleid, $userid, $action){
		add_to_log($courseid, 'uniappws', 'local_uniappws_'.$action, '', getremoteaddr() , $moduleid, $userid);
	}
}
