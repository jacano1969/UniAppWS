<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once(dirname(__FILE__).'/../../config.php');
require_once(UNIAPP_ROOT . '/mod/assignment/assignment.class.php');
require_once(UNIAPP_ROOT . '/mod/assignment/submission.class.php');
require_once(UNIAPP_ROOT . '/mod/assignment/db/assignmentDB.class.php');

class local_uniappws_assignment extends uniapp_external_api {

    public static function get_assignments_by_courseid_parameters() {
        return new external_function_parameters(
            array (
                'courseid'  => new external_value(PARAM_INT, 'Course id', VALUE_REQUIRED, ', NULL_NOT_ALLOWED'),
                'startpage' => new external_value(PARAM_INT, 'Start page', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                'n'         => new external_value(PARAM_INT, 'Page number', VALUE_DEFAULT, 10, NULL_NOT_ALLOWED)
            )
        );
    }

    public static function get_assignments_by_courseid($courseid, $startpage, $n) {
        $system_context = get_context_instance(CONTEXT_SYSTEM);

        self::validate_context($system_context);

        // check for view capability at course level
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        require_capability('mod/assignment:view', $context);

        require_once(UNIAPP_ROOT . '/course/db/courseDB.class.php');
        if (!$course = course_db::get_course_by_courseid($courseid)) {
            throw new moodle_exception('assignment:unknowncourseidnumber', 'local_uniappws', '', $courseid);
        }

        $assignments = assignment_db::get_assignments_by_courseid($courseid, $startpage, $n);

        $returnassign = array();
        foreach ($assignments as $assig) {
            if (!$cm = get_coursemodule_from_instance('assignment', $assig->id)) {
                throw new moodle_exception('assignment:notfound', 'local_uniappws', '', '');
            }
            $context = get_context_instance(CONTEXT_MODULE, $cm->id);
            if (!has_capability('mod/assignment:view',$context)) {
                continue;
            }
            $assig = new Assignment($assig);
            $returnassign[] = $assig->get_data();
        }

        return $returnassign;
    }

    public static function get_assignments_by_courseid_returns() {
        return new external_multiple_structure(
            Assignment::get_class_structure()
        );
    }

    public static function get_submission_by_assigid_parameters() {
         return new external_function_parameters(
            array(
                'assigid' => new external_value(PARAM_INT, 'Assignment id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
            )
        );
    }

    public static function get_submission_by_assigid($assigid) {
        global $USER;
        $system_context = get_context_instance(CONTEXT_SYSTEM);

        self::validate_context($system_context);

        $assig = assignment_db::get_assignment($assigid);
        if ($assig === false || $assig === null){
            throw new moodle_exception('assignment:notfound', 'local_uniappws', '', '');
        }
        $submission = assignment_db::get_submission($USER->id, $assigid);
        if ($submission === false || $submission === null) {
            throw new moodle_exception('assignment:submissionnotfound', 'local_uniappws', '', '');
        }

        $cm = get_coursemodule_from_instance("assignment", $assigid);
        add_to_log($assig->course, 'assignment', 'view submission', 'submissions.php?id='.$cm->id, $assig->id, $cm->id);

        $submission = new Submission($submission);

        return $submission->get_data();
    }

    public static function get_submission_by_assigid_returns() {
        return Submission::get_class_structure();
    }

    public static function submit_online_assignment_parameters() {
        return new external_function_parameters(
            array(
                'assigid'  => new external_value(PARAM_INT, 'Assignment id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'data'     => new external_value(PARAM_TEXT, 'Text to submit', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
            )
        );
    }

    public static function submit_online_assignment($assigid, $data) {
        global $USER;
        global $CFG;

        require_once($CFG->dirroot.'/mod/assignment/type/online/assignment.class.php');

        $system_context = get_context_instance(CONTEXT_SYSTEM);

        self::validate_context($system_context);

        if (!$cm = get_coursemodule_from_instance('assignment', $assigid)) {
            throw new moodle_exception('assignment:notfound', 'local_uniappws', '', '');
        }
        if (self::submit_online_assignment_permission($assigid, $cm) === false){
            throw new moodle_exception('usernosubmit', 'assignment', '', '');
        }
        $ret=false;
        $subid = assignment_db::get_submission_id($USER->id, $assigid);
        if ( $subid !== false) {
            $data1 = new stdClass();
            $data1->id=$subid->id;
            $data1->data1=$data;
            $data1->data2=1;
            $data1->timemodified = time();
            assignment_db::update_submission($data1);
            $ret = $subid->id;
        }
        else {
            $sub = self::prepare_new_submission( $USER->id, $assigid);
            $sub->data1=$data;
            $sub->data2=1;
            $ret = assignment_db::insert_submission($sub);
        }
        return array('subid' => $ret);
    }

    private static function submit_online_assignment_permission($assigid, $cm) {
        global $USER;
        if (!is_enrolled(get_context_instance(CONTEXT_MODULE, $cm->id), $USER->id, 'mod/assignment:submit')) {
            return false;
        }
        $submission = assignment_db::get_submission($USER->id, $assigid);
        $assig = assignment_db::get_assignment($assigid);
        if ($assig->assignmenttype != 'online') {
            return false;
        }
        return self::assignment_submission_is_open($assig) && (!$submission || $assig->resubmit || !$submission->timemarked);
    }

    private static function prepare_new_submission($userid, $assigid, $teachermodified=false) {
        $submission = new stdClass();
        $submission->assignment   = $assigid;
        $submission->userid       = $userid;
        $submission->timecreated  = time();
        // teachers should not be modifying modified date, except offline assignments
        if ($teachermodified) {
             $submission->timemodified = 0;
        } else {
             $submission->timemodified = $submission->timecreated;
        }
        $submission->numfiles           = 0;
        $submission->data1              = '';
        $submission->data2              = '';
        $submission->grade              = -1;
        $submission->submissioncomment  = '';
        $submission->format             = 0;
        $submission->teacher            = 0;
        $submission->timemarked         = 0;
        $submission->mailed             = 0;
        return $submission;
    }

    public static function submit_online_assignment_returns() {
        return new external_single_structure(
            array(
                'subid' => new external_value(PARAM_INT, 'Submission id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }

    public static function submit_singleupload_assignment_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'assigid'  => new external_value(PARAM_INT, 'Assignment id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'fileid'   => new external_value(PARAM_INT, 'File id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
            )
        );
    }

    public static function submit_singleupload_assignment($courseid, $assigid, $fileid) {
        global $USER;
        $system_context = get_context_instance(CONTEXT_SYSTEM);

        self::validate_context($system_context);

        if (!$cm = get_coursemodule_from_instance('assignment', $assigid, $courseid)) {
            throw new moodle_exception('assignment:notfound', 'local_uniappws', '', '');
        }

        if (self::submit_singleupload_assignment_permissions($assigid, $cm, $courseid) === false){
            throw new moodle_exception('usernosubmit', 'assignment', '', '');
        }

        $fs = get_file_storage();
        //File sent to submission via ws can only have been uploaded via ws,so
        //we check if the file belongs to the user that is making the submission
        $file = $fs->get_file_by_id($fileid);
        if ($file->get_contextid() === get_context_instance(CONTEXT_USER, $USER->id)){
            throw new moodle_exception('nofiles', 'assignment', '', '');
        }

        $subid = assignment_db::get_submission_id($USER->id, $assigid);
        if ( $subid !== false) {
            $ret= $subid->id;
            $context= get_context_instance(CONTEXT_MODULE, $cm->id);
            $fs->delete_area_files($context->id, 'mod_assignment', 'submission', $ret);
        }
        else {
            $sub = self::prepare_new_submission( $USER->id, $assigid);
            $ret = assignment_db::insert_submission($sub);
        }
        $cm = get_coursemodule_from_instance('assignment', $assigid, $courseid);
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        $newcontextid = $context->id;
        $newcomponent = 'mod_assignment';
        $newfilearea = 'submission';
        $newitemid = $ret;
        $newfilepath = '/';

        // Prepare file record object
        $fileinfo = array(
            'contextid' => $newcontextid,   // ID of context
            'component' => $newcomponent,   // usually = table name
            'filearea' => $newfilearea,     // usually = table name
            'itemid' => $ret,               // usually = ID of row in table
        );
        $result = $fs->create_file_from_storedfile($fileinfo, $fileid);
        $data = new stdClass();
        $data->id = $ret;
        $data->numfiles = 1;
        $data->timemodified = time();
        assignment_db::update_submission($data);
        return array('subid' => $ret);
    }

    private static function submit_singleupload_assignment_permissions($assigid, $cm, $courseid) {
        global $USER;
        //Permissions
        if (!is_enrolled(get_context_instance(CONTEXT_MODULE, $cm->id), $USER->id, 'mod/assignment:submit')) {
            return false;
        }
        $filecount = self::count_files($assigid, $courseid);
        $submission = assignment_db::get_submission($USER->id, $assigid);
        $assig = assignment_db::get_assignment($assigid);
        if ($assig->assignmenttype != 'uploadsingle') {
            return false;
        }
        if ($submission) {
            if (($submission->grade > 0) and !$assig->resubmit) {
                return false;
            }
        }
        return self::assignment_submission_is_open($assig) && (!$filecount || $assig->resubmit || !$submission->timemarked);
    }

    private static function count_files($assigid, $courseid) {
          global $CFG;
          global $USER;

          $filearea = $courseid.'/'.$CFG->moddata.'/assignment/'. $assigid.'/'.$USER->id;

          if (is_dir($CFG->dataroot.'/'.$filearea) && $basedir = make_upload_directory($filearea)) {
              if ($files = get_directory_list($basedir)) {
                  return count($files);
              }
          }
          return 0;
    }

    private static function assignment_submission_is_open($assig) {
        $time = time();
        if ($assig->preventlate && $assig->timedue) {
            return ($assig->timeavailable <= $time && $time <= $assig->timedue);
        } else {
            return ($assig->timeavailable <= $time);
        }
    }

    public static function submit_singleupload_assignment_returns() {
        return new external_single_structure(
            array(
                'subid' => new external_value(PARAM_INT, 'Submission id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }

    public static function submit_upload_assignment_parameters() {
        return new external_function_parameters(
            array(
                'assigid'  => new external_value(PARAM_INT, 'Assignment id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'isfinal'  => new external_value(PARAM_BOOL, 'If true is final submission, if false is a draft submission', VALUE_DEFAULT, false, NULL_NOT_ALLOWED),
                'files' => new external_multiple_structure(
                     new external_value(PARAM_INT, 'File id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
                )
            )
        );
    }

    public static function submit_upload_assignment($assigid, $isfinal, $files) {
        global $USER;
        global $CFG;
        global $DB;
        require_once($CFG->dirroot.'/mod/assignment/type/upload/assignment.class.php');
        $system_context = get_context_instance(CONTEXT_SYSTEM);

        self::validate_context($system_context);

        //$params = self::validate_parameters(self::submit_upload_assignment_parameters(), array('params' => $parameters));

        if (!$cm = get_coursemodule_from_instance('assignment', $assigid)) {
            throw new moodle_exception('assignment:notfound', 'local_uniappws', '', '');
        }

		$assignment = assignment_db::get_assignment($assigid);

        if ($assignment->var1 < count($files)) {
            throw new moodle_exception('assignment:maxfilesnumberexceeded', 'local_uniappws', '', '');
        }

        if (self::submit_upload_assignment_permissions($assigid, $cm) === false){
            throw new moodle_exception('usernosubmit', 'assignment', '', '');
        }

        $fs = get_file_storage();
        //File sent to submission via ws can only have been uploaded via ws,so
        //we check if the file belongs to the user that is making the submission
        $user_context = get_context_instance(CONTEXT_USER, $USER->id);
        foreach ($files as $uploadedfile) {
            $file = $fs->get_file_by_id($uploadedfile);
            if ($file->get_contextid() === $user_context ){
                throw new moodle_exception('nofiles', 'assignment', '', '');
            }
        }
        $subid = assignment_db::get_submission_id($USER->id, $assigid);
        if ( $subid !== false) {
            $ret = $subid->id;
            $context = get_context_instance(CONTEXT_MODULE, $cm->id);
            $fs->delete_area_files($context->id, 'mod_assignment', 'submission', $ret);
        }
        else {
            $sub = self::prepare_new_submission( $USER->id, $assigid);
            $ret = assignment_db::insert_submission($sub);
        }

        $cm = get_coursemodule_from_instance('assignment', $assigid);
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        $newcontextid = $context->id;
        $newcomponent = 'mod_assignment';
        $newfilearea = 'submission';
        $newitemid = $ret;
        $newfilepath = '/';

        // Prepare file record object
        $fileinfo = array(
            'contextid' => $newcontextid,   // ID of context
            'component' => $newcomponent,   // usually = table name
            'filearea' => $newfilearea,     // usually = table name
            'itemid' => $newitemid,         // usually = ID of row in table
        );

        foreach ($files as $fileid) {
            $result = $fs->create_file_from_storedfile($fileinfo, $fileid);
        }

        $data = new stdClass();
        $data->id = $ret;
        $data->numfiles = count($files);
        $data->timemodified = time();
        if ($isfinal) {
            $data->data2 = ASSIGNMENT_STATUS_SUBMITTED;
        }
        assignment_db::update_submission($data);
        return array('subid' =>$ret);
    }

    private static function submit_upload_assignment_permissions($assigid, $cm) {
        global $USER;
        $assig = assignment_db::get_assignment($assigid);
        if ($assig->assignmenttype != 'upload') {
            return false;
        }
        $submission = assignment_db::get_submission($USER->id, $assigid);
        if (is_enrolled(get_context_instance(CONTEXT_MODULE, $cm->id), $USER, 'mod/assignment:submit')
          and self::assignment_submission_is_open($assig)                       // assignment not closed yet
          and (empty($submission) or ($submission->userid == $USER->id))        // his/her own submission
          and !self::assignment_submission_is_finalized($submission)) {         // no uploading after final submission
            return true;
        } else {
            return false;
        }
    }

    private static function assignment_submission_is_finalized($submission) {
       if (empty($submission)) {
            return '';

        } else if ($submission->data2 == ASSIGNMENT_STATUS_SUBMITTED or $submission->data2 == ASSIGNMENT_STATUS_CLOSED) {
            return $submission->data2;

        } else {
            return '';
        }
    }

    public static function submit_upload_assignment_returns() {
        return new external_single_structure(
            array(
                'subid' => new external_value(PARAM_INT, 'Submission id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }

    public static function get_submission_files_parameters() {
        return new external_function_parameters(
            array(
                 'assigid'   => new external_value(PARAM_INT, 'Assignment id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                 'startpage' => new external_value(PARAM_INT, 'Start page', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                 'n'         => new external_value(PARAM_INT, 'Page number', VALUE_DEFAULT, 10, NULL_NOT_ALLOWED)
            )
        );
    }

    public static function get_submission_files($assigid, $startpage, $n) {
        global $USER;

        $system_context = get_context_instance(CONTEXT_SYSTEM);

        self::validate_context($system_context);

        //$params = self::validate_parameters(self::get_submission_files_parameters(), array('params' => $parameters));

        $cm = get_coursemodule_from_instance('assignment', $assigid);
        $subid = assignment_db::get_submission_id($USER->id, $assigid);
        if ($subid === false) {
            throw new moodle_exception('nofiles', 'assignment', '', '');
        }
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        $contextid = $context->id;
        $fs = get_file_storage();

        $files = $fs->get_area_files($contextid, 'mod_assignment','submission', $subid->id, "timemodified", false);
        $ret = array();
        $begin = $startpage*$n;
        $aux = array_slice($files, $begin, $begin+$n);
        if (empty($aux)) {
			throw new moodle_exception('nofiles', 'assignment', '', '');
        }

        foreach ($aux as $file) {
            $ret[] = array('fileid' => $file->get_id(), 'filename' => $file->get_filename());
        }
        return $ret;
    }

    public static function get_submission_files_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'fileid' => new external_value(PARAM_INT,'Fileid', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                    'filename' => new external_value(PARAM_TEXT,'Filename', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                )
            )
        );
    }

    public static function get_assignment_by_assigid_parameters() {
        return new external_function_parameters(
            array(
                'assigid' => new external_value(PARAM_INT, 'Assignment id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
            )
        );
    }

    public static function get_assignment_by_assigid($assigid) {

        $system_context = get_context_instance(CONTEXT_SYSTEM);

        self::validate_context($system_context);

        $assig = assignment_db::get_assignment($assigid);
        if ($assig === false || $assig === null){
			throw new moodle_exception('assignment:notfound', 'local_uniappws', '', '');
        }
        if (!$cm = get_coursemodule_from_instance('assignment', $assig->id)) {
			throw new moodle_exception('assignment:notfound', 'local_uniappws', '', '');
        }
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        if (!has_capability('mod/assignment:view',$context)) {
            throw new moodle_exception('assignment:nopermissions', 'local_uniappws', '', '');
        }

        $assig = new Assignment($assig);
        return $assig->get_data();
    }

    public static function get_assignment_by_assigid_returns() {
        return Assignment::get_class_structure();
    }
}
?>
