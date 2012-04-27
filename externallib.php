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


//=========== hello web service ====================================================

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function hello_parameters() {
        return new external_function_parameters(
                array('welcomemessage' => new external_value(PARAM_TEXT, 'The welcome message. By default it is "Hello,"', VALUE_DEFAULT, 'Hello, '))
        );
    }

    /**
     * Returns hello message
     * @return string Hello, message
     */
    public static function hello($welcomemessage = 'Hello, ') {
        global $USER;

        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::hello_parameters(),
                array('welcomemessage' => $welcomemessage));

        //Context validation
        //OPTIONAL but in most web service it should present
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        //Capability checking
        //OPTIONAL but in most web service it should present
        if (!has_capability('moodle/user:viewdetails', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }

        return $params['welcomemessage'] . $USER->firstname;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function hello_returns() {
        return new external_value(PARAM_TEXT, 'The welcome message + user first name');
    }


//=========== get_course_list web service ====================================================

	/**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_course_list_parameters() {
        return new external_function_parameters(
                array( 'sid' => new external_value(PARAM_TEXT, 'The sid session value', VALUE_DEFAULT, '') )
        );
    }

	/**
     * Returns the users's course list
     */
    public static function get_course_list($sid) {
        global $USER;

        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::get_course_list_parameters(), array('sid' => $sid));

        //Context validation
        //OPTIONAL but in most web service it should present
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        //Capability checking
        //OPTIONAL but in most web service it should present
        if (!has_capability('moodle/user:viewdetails', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
		
		$session = self::get_session($sid);	

		$raw_courses = enrol_get_users_courses($session['USER']->id, true,  Null, 'visible DESC,sortorder ASC');
		//self::dump($courses);
		$courses = array();	
		foreach ( $raw_courses as $course ) {
			$courses[count($courses)] = array('id' => $course->id, 'name' => $course->fullname);
		}
		return $courses;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_course_list_returns() {
		return new external_multiple_structure( 
					new external_single_structure( array(
						'id' => new external_value(PARAM_INT, 'course id'),
						'name' => new external_value(PARAM_TEXT, 'course full name')
					)	
				) 
        );
    }

//********************************************************************************************
//********************************************************************************************
// Utility functions
//********************************************************************************************
//********************************************************************************************

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
}
