<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

class group_db {

    public static function get_group_by_groupid($groupid) {
        global $DB;

        return $DB->get_record('groups', array('id' => $groupid));
    }

    public static function get_group_members_by_groupid($groupid, $startpage, $n) {
        global $DB;

        $sql = "SELECT u.*
             FROM {user} u, {groups_members} gm
             WHERE u.id = gm.userid AND gm.groupid = :groupid
             ORDER BY lastname ASC";

        $sqlparams = array();
        $sqlparams['groupid'] = $groupid;

        $begin = $startpage*$n;
        return $DB->get_records_sql($sql, $sqlparams, $begin, $n);
    }

    public static function get_group_members_by_groupingid($groupingid, $startpage, $n) {
        global $DB;

        $sql = "SELECT u.*
              FROM {user} u
               INNER JOIN {groups_members} gm ON u.id = gm.userid
               INNER JOIN {groupings_groups} gg ON gm.groupid = gg.groupid
              WHERE  gg.groupingid = :groupingid
              ORDER BY lastname ASC";

        $sqlparams = array();
        $sqlparams['groupingid'] = $groupingid;

        $begin = $startpage*$n;
        return $DB->get_records_sql($sql, $sqlparams, $begin, $n);
    }

    public static function get_groups_by_courseid($courseid, $startpage, $n, $userid=0) {
        global $DB;

        $sqlparams = array();
        if (empty($userid)) {
           $userfrom  = "";
           $userwhere = "";
        }
        else {
            $userfrom  = ", {groups_members} gm";
            $userwhere = "AND g.id = gm.groupid AND gm.userid = :userid";
            $sqlparams['userid'] = $userid;
        }
        $sql ="SELECT g.*
                FROM {groups} g $userfrom
                WHERE g.courseid = :courseid $userwhere
                ORDER BY name ASC";

        $sqlparams['courseid'] = $courseid;
        $begin = $startpage*$n;
        return $DB->get_records_sql($sql, $sqlparams, $begin, $n);
    }

    public static function get_groups_by_groupingid($groupingid, $startpage, $n, $userid=0) {
        global $DB;

        $sqlparams = array();
        if (empty($userid)) {
           $userfrom  = "";
           $userwhere = "";
        }
        else {
            $userfrom  = ", {groups_members} gm";
            $userwhere = "AND g.id = gm.groupid AND gm.userid = :userid";
            $sqlparams['userid'] = $userid;
        }

        $sql = "SELECT *
              FROM {groups} g, {groupings_groups} gg $userfrom
              WHERE g.id = gg.groupid AND gg.groupingid = :groupingid $userwhere
              ORDER BY name ASC";

        $sqlparams['groupingid'] = $groupingid;
        $begin = $startpage*$n;
        return $DB->get_records_sql($sql, $sqlparams, $begin, $n);
    }

    public static function get_groupings_by_courseid($courseid, $startpage, $n) {
        global $DB;

        $sql = "SELECT *
               FROM {groupings}
               WHERE courseid = :courseid
               ORDER BY name ASC";

        $sqlparams = array();
        $sqlparams['courseid'] = $courseid;
        $begin = $startpage*$n;
        return $DB->get_records_sql($sql, $sqlparams, $begin, $n);
    }

    public static function get_groupings_by_courseid_and_userid($courseid, $startpage, $n, $userid) {
        global $DB;

        //@WARNING hand-made
        $sql = "SELECT gp.*
                FROM {groups} g
                 JOIN {groups_members} gm ON gm.groupid = g.id
                 LEFT JOIN {groupings_groups} gg ON gg.groupid = g.id
                 JOIN {groupings} gp ON gg.groupingid = gp.id
                WHERE gm.userid = :userid AND g.courseid = :courseid
                GROUP BY gg.groupingid";

        $sqlparams = array();
        $sqlparams['courseid'] = $courseid;
        $sqlparams['userid'] = $userid;
        $begin = $startpage*$n;
        return $DB->get_records_sql($sql, $sqlparams, $begin, $n);
    }

    public static function get_course_by_groupid($groupid){
        global $DB;

        //@WARNING hand-made
        $sql ="SELECT c.*
                FROM {groups} g
                 JOIN {course} c ON c.id = g.courseid
                WHERE g.id = :groupid";

        $sqlparams = array();
        $sqlparams['groupid'] = $groupid;

        return $DB->get_record_sql($sql, $sqlparams);
    }

    public static function get_course_by_groupingid($groupingid) {
        global $DB;

        //@WARNING hand-made
        $sql ="SELECT c.*
                FROM {groupings} g
                 JOIN {course} c ON c.id = g.courseid
                WHERE g.id = :groupingid";

        $sqlparams = array();
        $sqlparams['groupingid'] = $groupingid;

        return $DB->get_record_sql($sql, $sqlparams);
    }

}

?>
