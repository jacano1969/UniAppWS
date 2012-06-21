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
require_once(dirname(__FILE__)."/../uniapp_external_api.class.php");
require_once(dirname(__FILE__)."/../util/logger.class.php");

class local_uniappws_course extends uniapp_external_api {

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

		Logger::add($id, 0, $USER->id, 'get_course_modules');

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
							'name' => new external_value(PARAM_TEXT, 'module title', VALUE_REQUIRED, 'name_unknow', NULL_NOT_ALLOWED),
							'intro' => new external_value(PARAM_RAW, 'module intro', VALUE_OPTIONAL),
							'timemodified' => new external_value(PARAM_INT, 'modification time', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
							)	
						) 
					);
    }

}
