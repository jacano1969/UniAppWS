<?php

require_once(dirname(__FILE__).'/../../config.php');

class local_uniappws_scorm extends uniapp_external_api {

	/**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_status_parameters() {
        return new external_function_parameters (
            array(
                'scormid' => new external_value(PARAM_INT,  'Scorm identifier', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }

	private static function scorm_get_attempt_status($user, $scorm, $cm='') {
		global $DB;

		$attempts = scorm_get_attempt_count($user->id, $scorm, true);
		if (empty($attempts)) {
			$attemptcount = 0;
		} else {
			$attemptcount = count($attempts);
		}

		$result = array();
		$result['maxattempt'] = $scorm->maxattempt;
		$result['maxgrade'] = $scorm->maxgrade;
		$result['attemptsmade']= $attemptcount;
		$result['grademethod'] = $scorm->grademethod;
		$result['whatgrade'] = $scorm->whatgrade;
		/*
		if (!empty($attempts)) {
			$i = 1;
			$result['attempt'] = array();
			foreach ($attempts as $attempt) {
				$gradereported = scorm_grade_user_attempt($scorm, $user->id, $attempt->attemptnumber);
				if ($scorm->grademethod !== GRADESCOES && !empty($scorm->maxgrade)) {
					$gradereported = $gradereported/$scorm->maxgrade;
					$gradereported = number_format($gradereported*100, 0) .'%';
				}
				$result['attempt'][$attempt->attemptnumber] = $gradereported;
				$i++;
			}
		}
		*/
		$result['calculatedgrade'] = scorm_grade_user($scorm, $user->id);
		return $result;
	}

    /**
     * Returns desired resource
     *
     * @param int resourceid
     *
     * @return resource
     */
    public static function get_status($scormid) {
        global $DB, $USER, $CFG;
		require_once($CFG->dirroot.'/mod/scorm/lib.php');
		require_once($CFG->dirroot.'/mod/scorm/locallib.php');

        if (!$scorm = $DB->get_record('scorm', array('id'=>$scormid))) {
            throw new moodle_exception('resource:notfound', 'local_uniappws', '', '');
        }
        $cm = get_coursemodule_from_instance('scorm', $scorm->id, $scorm->course, false, MUST_EXIST);

        $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        require_capability('mod/resource:view', $context);
		$status = self::scorm_get_attempt_status($USER, $scorm, $cm);
		return $status;
        //throw new moodle_exception('resource:unknownerror', 'local_uniappws', '','');
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_status_returns() {
		return new external_single_structure (
            array(
                'maxattempt' => new external_value(PARAM_INT,  'Allowed attempts', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'maxgrade' => new external_value(PARAM_INT,  'Max grade', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'attemptsmade' => new external_value(PARAM_INT,  'Attempts made', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'whatgrade' => new external_value(PARAM_INT,  'What grade', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'grademethod' => new external_value(PARAM_INT,  'Grade method', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'calculatedgrade' => new external_value(PARAM_FLOAT,  'Calculated grade', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
            )
        );
	}


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_package_parameters() {
        return new external_function_parameters (
            array(
                'scormid' => new external_value(PARAM_INT,  'Scorm identifier', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }

    /**
     * Returns desired scorm package
     *
     * @param int scormid
     *
     * @return resource
     */
    public static function get_package($scormid) {
        global $DB;

        if (!$scorm = $DB->get_record('scorm', array('id'=>$scormid))) {
            throw new moodle_exception('resource:notfound', 'local_uniappws', '', '');
        }
        $cm = get_coursemodule_from_instance('scorm', $scorm->id, $resource->course, false, MUST_EXIST);

        $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        require_capability('mod/resource:view', $context);

        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_scorm', 'package', 0, 'sortorder DESC, id ASC', false); 
        if (count($files) < 1) {
            throw new moodle_exception('resource:notfound', 'local_uniappws', '', '');
        } else {
			// serve the file
            $file = reset($files);
            unset($files);

			$filename = $file->get_filename();
			$filetype = $file->get_mimetype();
			$filesize = $file->get_filesize();

			header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
			header("Cache-Control: public"); // needed for i.e.
			header("Content-Type: $filetype");
			header("Content-Transfer-Encoding: Binary");
			header("Content-Length: $filesize");
			header("Content-Disposition: attachment; filename=$filename");
			return $file->readfile();
        }

        throw new moodle_exception('resource:unknownerror', 'local_uniappws', '','');
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_package_returns() { }
}

?>
