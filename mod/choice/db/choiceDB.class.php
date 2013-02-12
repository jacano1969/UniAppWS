<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

class choice_db {

    public static function get_choices_by_courseid($courseid, $startpage, $n) {
        global $DB;
        $begin = $startpage*$n;
        return $DB->get_records('choice', array('course' => $courseid), '', '*', $begin, $n);
    }

    public static function get_choice($choiceid) {
        global $DB;

        return $DB->get_record('choice',  array('id' => $choiceid));
    }

    public static function update_answer($update){
        global $DB;

        return $DB->update_record('choice_answers', $update);
    }

    public static function insert_answer($answer){
        global $DB;

        return $DB->insert_record('choice_answers', $answer);
    }

    public static function get_answer_id($userid, $choiceid) {
        global $DB;

        return $DB->get_record('choice_answers',  array('choiceid' => $choiceid, 'userid' => $userid), 'id');
    }

    public static function get_answer($userid, $choiceid) {
        global $DB;

        return $DB->get_record('choice_answers',  array('choiceid' => $choiceid, 'userid' => $userid));
    }
}

?>
