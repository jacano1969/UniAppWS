<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(dirname(__FILE__).'/../config.php');
require_once(UNIAPP_ROOT . '/user/userStructure.class.php');
require_once(UNIAPP_ROOT . '/user/db/userDB.class.php');
require_once(UNIAPP_ROOT . '/lib.php');

class local_uniappws_user extends uniapp_external_api {	


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_user_parameters() {
        return new external_function_parameters(
            array()
        );
    }

    /**
     *
     * Returns the logged user
     *
     * @return array of user
     */
    public static function get_user() {
        global $USER;
        $context = get_context_instance(CONTEXT_SYSTEM);
        self::validate_context($context);

        $user = user_db::get_user_by_id($USER->id);

        if (empty($user->deleted)) {
            $user->avatar = get_link(new user_picture($user));
        }

        $user = new UserStructure($user);
        $user = $user->get_data();

        return $user;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_user_returns() {
        return UserStructure::get_class_structure();
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_user_by_userid_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'user id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
                 )
        );
    }

    /**
     *
     * Returns a user
     *
     * @param array $userids - An array of user ids used to recover details for the various users
     *
     * @return array of user
     */
    public static function get_user_by_userid($userid) {
        global $CFG, $USER;

        $context = get_context_instance(CONTEXT_SYSTEM);
        self::validate_context($context);

        $user_context = get_context_instance(CONTEXT_USER, $USER->id);

        $params = self::validate_parameters(self::get_user_by_userid_parameters(), array('userid' => $userid));

        $user = user_db::get_user_by_id($userid);

        if (empty($user->deleted)) {
            $user->avatar = get_link(new user_picture($user));

        }

        $user = new UserStructure($user);
        $user = $user->get_data();
        return $user;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_user_by_userid_returns() {
        return UserStructure::get_class_structure();
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_user_by_username_parameters() {
        return new external_function_parameters(
            array(
                'username' 	=> new external_value(PARAM_TEXT, 'user name', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
            )
        );
    }

    /**
     *
     * Function to get user details
     *
     * @param text $username - A username
     *
     * @return user details
     */
    public static function get_user_by_username($username) {
        global $CFG;

        $context = get_context_instance(CONTEXT_SYSTEM);
        self::validate_context($context);

        $params = self::validate_parameters(self::get_user_by_username_parameters(), array('username'=>$username));

        $user = user_db::get_user_by_username($username);

        if (empty($user->deleted)) {
            $user->avatar = get_link(new user_picture($user));
        }

        $user = new UserStructure($user);
        $user = $user->get_data();
        return $user;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_user_by_username_returns() {
        return UserStructure::get_class_structure();
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_users_by_courseid_parameters() {
        return new external_function_parameters(
            array(
                'courseid'  => new external_value(PARAM_INT, 'course ID', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'startpage' => new external_value(PARAM_INT, 'start page', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                'n'         => new external_value(PARAM_INT, 'page number', VALUE_DEFAULT, 10, NULL_NOT_ALLOWED)
            )
        );
    }

    /**
     *
     * Function to get list of users enrolled in a course
     *
     * @param text $username - A username
     *
     * @return user details
     */
    public static function get_users_by_courseid($courseid, $startpage, $n) {
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        self::validate_context($context);

        $users = user_db::get_users_by_courseid($courseid, $startpage, $n);
        $returns = array();
        foreach ($users as $user) {
            $user->avatar = get_link(new user_picture($user));
            $user = new UserStructure($user);
            $returns[] = $user->get_data();
        }
        return $returns;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_users_by_courseid_returns() {
        return new external_multiple_structure(
            UserStructure::get_class_structure()
        );
    }
}

?>
