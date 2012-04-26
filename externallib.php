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
 * External Web Service Template
 *
 * @package    localuniappws
 * @copyright  2012 eLab (http://www.elearninglab.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");

class local_uniappws_external extends external_api {

//=========== hello function ====================================================

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


//=========== get_username function ====================================================

	/**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_username_parameters() {
        return new external_function_parameters(
                array(
					'firstname' => new external_value(PARAM_TEXT, 'The firstname of the user', VALUE_DEFAULT, ''),
					'lastname' => new external_value(PARAM_TEXT, 'The lastname of the user', VALUE_DEFAULT, '')
				)
        );
    }

	/**
     * Returns the username by the session 
     * @return string Hello, message
     */
    public static function get_username($firstname, $lastname) {
        global $USER;

        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::get_username_parameters(),
                array('firstname' => $firstname, 'lastname' => $lastname));

        //Context validation
        //OPTIONAL but in most web service it should present
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        //Capability checking
        //OPTIONAL but in most web service it should present
        if (!has_capability('moodle/user:viewdetails', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
		global $CFG;
		print_r($CFG);
        return $_SERVER;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_username_returns() {
        return new external_value(PARAM_TEXT, 'The username.');
    }

	/**
     * Returns the session object given the session file
     * @return external_description
     */
	 public static function get_user_session_from_file($filename){
        global $CFG;
		$s = file_get_contents($filename);
		$a = unserialize($s);
	 }
}
