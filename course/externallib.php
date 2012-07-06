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
require_once(dirname(__FILE__).'/../config.php');
require_once(UNIAPP_ROOT . '/course/course.class.php');
require_once(UNIAPP_ROOT . '/course/db/courseDB.class.php');

class local_uniappws_course extends uniapp_external_api {

 	/**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_courses_by_userid_parameters() {
        return new external_function_parameters(
            array(
                'userid'    => new external_value(PARAM_INT, 'user ID', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'startpage' => new external_value(PARAM_INT, 'start page', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                'n'         => new external_value(PARAM_INT, 'page number', VALUE_DEFAULT, 10, NULL_NOT_ALLOWED)
            )
        );
    }

    /**
     * Returns an array of the courses a user is enrolled
     *
     * @params array of userids
     * @return array An array of arrays
     */
    public static function get_courses_by_userid($userid, $startpage, $n) {

        $context = get_context_instance(CONTEXT_SYSTEM);
        self::validate_context($context);

        $viewhidden = false;
        if (has_capability('moodle/course:viewhiddencourses', $context)) {
            $viewhidden = true;
        }

        $courses = course_db::moodbile_get_courses_by_userid($userid, $viewhidden, $startpage, $n);

        $returncourses = array();
        foreach ($courses as $course) {
            $course = new Course($course);
            $returncourses[] = $course->get_data();
        }

        return $returncourses;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_courses_by_userid_returns() {
        return
            new external_multiple_structure(
                Course::get_class_structure()
            );
    }



	/**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_course_modules_parameters() {
        return new external_function_parameters(
                array( 'courseid' => new external_value(PARAM_INT, 'The course id', VALUE_DEFAULT, '') )
        );
    }

	/**
     * Returns the list of modules from the course
     */
    public static function get_course_modules($courseid) {
        global $USER, $DB;
		
        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::get_course_modules_parameters(), array('courseid' => $courseid));

        //Context validation
        //OPTIONAL but in most web service it should present
		$context = self::get_context_by_token($_GET['wstoken']); 

        self::validate_context($context);

		$course_context = get_context_instance(CONTEXT_COURSE, $courseid);
		
		// Context checking
		// This controll enforces the controll on the token.
		// It checks that the token belongs to the course whose id is $courseid.
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
		$course = $DB->get_record('course', array('id' => $courseid));
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
					'type' => $course_module->type,
					'name' => $course_module->name,
					'intro' => $course_module->intro,
					'timemodified' => $course_module->timemodified
				);
			}
		}

		Logger::add($courseid, 0, $USER->id, 'get_course_modules');

		return $modules_list;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_course_modules_returns() {
		return new external_multiple_structure( 
			new external_single_structure( array(
					'id' => new external_value(PARAM_INT, 'course module id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
					'instanceid' => new external_value(PARAM_INT, 'module id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
					'courseid' => new external_value(PARAM_INT, 'course id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
					'modname' => new external_value(PARAM_TEXT, 'module type', VALUE_REQUIRED, 'mod_unknown', NULL_NOT_ALLOWED),
					'type' => new external_value(PARAM_TEXT, 'module title', VALUE_OPTIONAL),
					'name' => new external_value(PARAM_TEXT, 'module title', VALUE_REQUIRED, 'name_unknow', NULL_NOT_ALLOWED),
					'intro' => new external_value(PARAM_RAW, 'module intro', VALUE_OPTIONAL),
					'timemodified' => new external_value(PARAM_INT, 'modification time', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
					)	
				) 
			);
    }

}
