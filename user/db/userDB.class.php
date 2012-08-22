<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

class user_db {

    /**
     * Returns a user by id
     *
     * @param int $userid
     *
     * @return user
     */
    public static function moodbile_get_user_by_id($userid) {
        global $DB;

        return $DB->get_record('user', array('id' => $userid));
    }


    /**
     * Returns a user by username
     *
     * @param int $username
     *
     * @return user
     */
    public static function moodbile_get_user_by_username($username) {
        global $DB;

        return $DB->get_record('user', array('username' => $username));
    }

     /**
     * Returns an array of n users registered to the course with id = courseid
     * starting from page startpage
     *
     * @param int $courseid
     * @param int $startpage
     * @param int $n
     *
     * @return array of user
     */
    public static function moodbile_get_users_by_courseid($courseid, $startpage, $n) {
        global $DB;

        $sql = "SELECT u.*
                FROM {user} u
                JOIN ( SELECT DISTINCT ue.userid
                        FROM {user_enrolments} ue
                        JOIN (SELECT e.id
                            FROM {enrol} e
                            WHERE e.courseid = :courseid AND e.status = :enabled
                        ) en ON (en.id = ue.enrolid)
                        WHERE ue.status = :active
                    ) us ON (us.userid = u.id)";

        $sqlparams = array();
        $sqlparams['courseid']  = $courseid;
        $sqlparams['active']  = ENROL_USER_ACTIVE;
        $sqlparams['enabled'] = ENROL_INSTANCE_ENABLED;

        $begin = $startpage*$n;
        $users = $DB->get_records_sql($sql, $sqlparams, $begin, $n);

        return $users;
    }
}

?>
