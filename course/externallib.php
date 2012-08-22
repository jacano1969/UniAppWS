<?php

require_once(dirname(__FILE__).'/../config.php');
require_once(UNIAPP_ROOT . '/course/courseStructure.class.php');
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
            $course = new CourseStructure($course);
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
                CourseStructure::get_class_structure()
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
        global $USER;
		
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

		$modules_list = self::extract_course_modules($courseid);	

		Logger::add($courseid, 0, $USER->id, 'get_course_modules');

		return $modules_list;
    }

	public static function extract_course_modules($courseid){
		global $DB;
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
		$module_names = array('assignment', 'folder', 'forum', 'resource' );
		$course = $DB->get_record('course', array('id' => $courseid));
		$modules_list = array();
		foreach($module_names as $module_name) {
			// get determined type of modules
			$course_modules = get_all_instances_in_course($module_name, $course, $USER->id);

			foreach($course_modules as $course_module) {
				//print_r($course_module);
				$entry = count($modules_list);
				$modules_list[$entry] = array(
					'id' => intval($course_module->coursemodule),
					'instanceid' => intval($course_module->id),
					'courseid' => intval($course_module->course),
					'modname' => $module_name,
					'type' => $course_module->type,
					'name' => $course_module->name,
					'intro' => $course_module->intro,
					'timemodified' => intval($course_module->timemodified)
				);

				// set the type for the assignment
				if($module_name == 'assignment') {
					$modules_list[$entry]['type'] = $course_module->assignmenttype;
				}
			}
		}

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

	/**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_course_modules_count_parameters() {

        return new external_function_parameters(
                array( 'courseid' => new external_multiple_structure (
						new external_value(PARAM_INT, 'The course id', VALUE_DEFAULT, '')
					)
				)
        );
    }

	/**
     * Returns the list of modules from the course
     */
    public static function get_course_modules_count($courseids) {
        global $USER, $DB;
			
        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::get_course_modules_count_parameters(), array('courseid' => $courseids));

        //Context validation
        //OPTIONAL but in most web service it should present
		$context = self::get_context_by_token($_GET['wstoken']); 

        self::validate_context($context);
		
		$modules_count = array();
		foreach($courseids as $courseid) {
			$count = array();
			// set the course id
			$count['id'] = $courseid;	

			// set the modulescount
			$modules = self::extract_course_modules($courseid);
			$count['modulescount'] = count($modules);

			// store count
			array_push($modules_count, $count);
		}
		return $modules_count;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_course_modules_count_returns() {
		return new external_multiple_structure( 
			new external_single_structure( array(
					'id' => new external_value(PARAM_INT, 'course id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
					'modulescount' => new external_value(PARAM_INT, 'module count', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
					)	
				) 
			);
    }

}

?>
