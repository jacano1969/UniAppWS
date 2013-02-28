<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

class grade_db {

    public static function get_grade_items_by_userid($userid, $viewhidden, $startpage, $n) {
        global $DB;

        $where = '';
        if (!$viewhidden) {
            $where = 'AND gi.hidden = 0';
        }

        $sql = "SELECT gi.*
                FROM {grade_items} gi, {user_enrolments} ue, {enrol} e
                WHERE ue.userid = :userid AND
                      ue.enrolid = e.id AND
                      ue.status = :active AND
                      e.status = :enabled AND
                      e.courseid = gi.courseid $where";

        $sqlparams = array();
        $sqlparams['userid']    = $userid;
        $sqlparams['active']    = ENROL_USER_ACTIVE;
        $sqlparams['enabled']   = ENROL_INSTANCE_ENABLED;

        $begin = $startpage*$n;
        return $DB->get_records_sql($sql, $sqlparams, $begin, $n);
    }

    public static function get_courseid_by_gradeitemid($itemid) {
        global $DB;

        $gradeitem = $DB->get_record('grade_items', array('id' => $itemid));

        return $gradeitem->courseid;
    }

    public static function get_grades_by_itemid($itemid, $viewhidden, $startpage, $n) {
        global $DB;

        $conditions = array();
        $conditions['itemid'] = $itemid;
        if (!$viewhidden) {
            $conditions['hidden'] = 0;
        }

        $begin = $startpage*$n;
        $grades = $DB->get_records('grade_grades', $conditions, '', '*', $begin, $n);

        return $grades;
    }

    public static function get_grade_items_by_courseid($courseid, $viewhiddencourses, $viewhiddenactivities, $startpage, $n) {
        global $DB;
        require_once(UNIAPP_ROOT . '/course/db/courseDB.class.php');

        $course = course_db::get_course_by_courseid($courseid);

        if (!($course->visible) && !$viewhiddencourses) {
            return null;
        }

        $where = '';
        if (!$viewhiddenactivities) {
            $where = 'AND hidden = 0';
        }

        $sql = "SELECT *
                FROM {grade_items}
                WHERE courseid = :courseid AND
                      categoryid IS NOT NULL AND
                      itemname IS NOT NULL $where";

        $sqlparams = array();
        $sqlparams['courseid'] = $courseid;

        $begin = $startpage*$n;
        $gradeitems = $DB->get_records_sql($sql, $sqlparams, $begin, $n);

        return $gradeitems;
    }

    public static function get_user_grade_by_itemid($userid, $itemid, $viewhidden) {
        global $DB;

        $conditions = array();
        $conditions['userid'] = $userid;
        $conditions['itemid'] = $itemid;
        if (!$viewhidden) {
            $conditions['hidden'] = 0;
        }

        $grade = $DB->get_record('grade_grades', $conditions);
        return $grade;
    }

    public static function get_user_grades_by_courseid($userid, $courseid, $viewhiddencourses, $viewhiddenactivities, $startpage, $n) {
        global $DB;

        $gradeitems = self::get_grade_items_by_courseid($courseid, $viewhiddencourses, $viewhiddenactivities, 0, 0);

        $conditions = array();
        $conditions['userid'] = $userid;
        if (!$viewhiddenactivities) {
            $conditions['hidden'] = 0;
        }

        $begin = $startpage*$n;
        $remaining = $n;
		if($n == 0) { 
			$remaining = count($gradeitems);
		}
        $return = array();
        foreach ($gradeitems as $item) {
            $conditions['itemid'] = $item->iteminstance;

            $grade = $DB->get_records('grade_grades', $conditions, '', '*', $begin, $remaining);
            $remaining = $remaining - count($grade);
            $return[] = $grade;
            if ($remaining <= 0) {
                break;
            }
        }

        return $return;

    }
}
