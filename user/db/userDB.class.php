<?php
// This file is part of Moodbile -- http://moodbile.org
//
// Moodbile is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodbile is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodbile.  If not, see <http://www.gnu.org/licenses/>.

/**
 * User DataBase Class
 *
 * @package MoodbileServer
 * @subpackage User
 * @copyright 2010 Maria José Casañ, Marc Alier, Jordi Piguillem, Nikolas Galanis marc.alier@upc.edu
 * @copyright 2010 Universitat Politecnica de Catalunya - Barcelona Tech http://www.upc.edu
 *
 * @author Jordi Piguillem
 * @author Nikolas Galanis
 * @author Oscar Martinez Llobet
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

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
    }}
