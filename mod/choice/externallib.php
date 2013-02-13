<?php
require_once(dirname(__FILE__).'/../../config.php');
require_once("$CFG->dirroot/mod/choice/lib.php");
require_once(UNIAPP_ROOT . '/mod/choice/choice.class.php');
require_once(UNIAPP_ROOT . '/mod/choice/db/choiceDB.class.php');

class local_uniappws_choice extends uniapp_external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_choice_parameters() {
        return new external_function_parameters (
            array(
                'choiceid' => new external_value(PARAM_INT,  'Choice identifier', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }

    /**
     * Returns desired choice
     *
     * @param int choiceid
     *
     * @return choice
     */
    public static function get_choice($choiceid) {
        global $DB, $USER;
        if (!$choice = $DB->get_record('choice', array('id'=>$choiceid))) {
            throw new moodle_exception('choice:notfound', 'local_uniappws', '', '');
        }
        $cm = get_coursemodule_from_instance('choice', $choice->id, $choice->course, false, MUST_EXIST);

        $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        require_capability('mod/choice:choose', $context);

        $choice_options = $DB->get_records('choice_options', array('choiceid'=>$choiceid), 'id', 'id as optionid, text');
        if(count($choice_options) < 1) {
            throw new moodle_exception('choice:nooptions', 'uniappws_choice', '', '');
        } else {
			// prepare for the output
			$choice->options = array();
			foreach($choice_options as $option) {
				$choice->options[] = array("optionid" => $option->optionid, "text" => $option->text);
			}
			
			// set the user answer if available
			$user_answer = choice_db::get_answer($USER->id, $choiceid);
			if ( !empty($user_answer) or $choice->allowupdate == 1 ) {
				$choice->answer = $user_answer->optionid;
			}

            $Choice = new Choice($choice);
            return $Choice->get_data();
        }

        throw new moodle_exception('choice:unknownerror', 'local_uniappws', '', '');
    }

   	/**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_choice_returns() {
        return Choice::get_class_structure();
    }

	/**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function submit_choice_parameters() {
        return new external_function_parameters (
            array(
                'choiceid' => new external_value(PARAM_INT,  'Choice identifier', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'optionid' => new external_value(PARAM_INT,  'Option identifier', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }

    /**
     * Returns desired choice
     *
     * @param int choiceid
     *
     * @return choice
     */
    public static function submit_choice($choiceid, $optionid) {
        global $DB, $USER;
        if (!$choice = $DB->get_record('choice', array('id'=>$choiceid))) {
            throw new moodle_exception('choice:notfound', 'local_uniappws', '', '');
        }
        $cm = get_coursemodule_from_instance('choice', $choice->id, $choice->course, false, MUST_EXIST);

        $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        require_capability('mod/choice:choose', $context);
		
		// check the time
		$timenow = time();
		if ($choice->timeclose > 0 and $timenow < $choice->timeclose) {
        	throw new moodle_exception('notopenyet','choice', '', userdate($choice->timeclose));
		} else if ($choice->timeclose > 0 and $timenow > $choice->timeclose) {
        	throw new moodle_exception('expired','choice', '', userdate($choice->timeclose));
		}
		
		$user_answer = choice_db::get_answer_id($USER->id, $choiceid);
		if ( empty($user_answer) or $choice->allowupdate == 1 ) {
			choice_user_submit_response($optionid, $choice, $USER->id, $course, $cm);
			$user_answer = choice_db::get_answer($USER->id, $choiceid);
			return array('subid' => $user_answer->optionid);
		} else {
        	throw new moodle_exception('choice:updatenotallowed', 'local_uniappws', '','');
		}
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function submit_choice_returns() {
		return new external_single_structure(
            array(
                'subid' => new external_value(PARAM_INT, 'Submission id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }
}
?>
