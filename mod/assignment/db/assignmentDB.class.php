<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

class assignment_db {

    public static function get_assignments_by_courseid($courseid, $startpage, $n) {
        global $DB;
        $begin = $startpage*$n;
        return $DB->get_records('assignment', array('course' => $courseid), '', '*', $begin, $n);
    }

    public static function get_assignment($assigid) {
        global $DB;

        return $DB->get_record('assignment',  array('id' => $assigid));
    }

    public static function update_submission($update){
        global $DB;

        return $DB->update_record('assignment_submissions', $update);
    }

    public static function insert_submission($submission){
        global $DB;

        return $DB->insert_record('assignment_submissions', $submission);
    }

    public static function get_submission_id($userid, $assigid) {
        global $DB;

        return $DB->get_record('assignment_submissions',  array('assignment' => $assigid, 'userid' => $userid),'id');
    }

    public static function get_submission($userid, $assigid) {
        global $DB;

        return $DB->get_record('assignment_submissions',  array('assignment' => $assigid, 'userid' => $userid));
    }
}

?>
