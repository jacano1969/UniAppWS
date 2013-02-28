<?php

if (!defined('MOODLE_INTERNAL')) {
	die('Direct access to this script is forbidden.');	///  It must be included from a Moodle page
}
require_once(dirname(__FILE__).'/../config.php');
global $MBL;
require_once(UNIAPP_ROOT . '/grade/externallib.php');
require_once(UNIAPP_ROOT . '/grade/gradeStructure.class.php');
require_once(UNIAPP_ROOT . '/grade/gradeItemStructure.class.php');
require_once(UNIAPP_ROOT . '/grade/db/gradeDB.class.php');


class local_uniappws_grade extends uniapp_external_api {

	/**
	 * Returns description of method parameters
	 * @return external_function_parameters
	 */
	public static function get_grade_items_by_userid_parameters() {
		return new external_function_parameters (
			array(
				'userid'	=> new external_value(PARAM_INT, 'user ID', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
				'startpage' => new external_value(PARAM_INT, 'start page', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
				'n'		 => new external_value(PARAM_INT, 'page number', VALUE_DEFAULT, 10, NULL_NOT_ALLOWED)
			)
		);
	}

	/**
	 * Returns a list of n grade items starting from page startpage
	 *
	 * @param int userid
	 * @param int startpage
	 * @param int n
	 *
	 * @return array of grade items
	 */
	public static function get_grade_items_by_userid($userid, $startpage, $n) {
//		$params = self::validate_parameters(self::get_grade_items_by_userid_parameters(), array('params' => $parameters));
//		$params = $params['params'];

		$context = get_context_instance(CONTEXT_USER, $userid);
		self::validate_context($context);

		$viewhidden = false;
		if (has_capability('moodle/course:viewhiddenactivities', $context)) {
			$viewhidden = true;
		}

		$gradeitems = grade_db::get_grade_items_by_userid($userid, $viewhidden, $startpage, $n);

		$returngradeitems = array();
		foreach ($gradeitems as $gradeitem) {
			$newgradeitem = new GradeItemStructure($gradeitem);
			$returngradeitems[] = $newgradeitem->get_data();
		}
		return $returngradeitems;
	}

	/**
	 * Returns description of method result value
	 * @return external_description
	 */
	public static function get_grade_items_by_userid_returns() {
		return
			new external_multiple_structure(
				GradeItemStructure::get_class_structure()
			);
	}


	/**
	 * Returns description of method parameters
	 * @return external_function_parameters
	 */
	public static function get_grades_by_itemid_parameters() {
		return new external_function_parameters (
			array(
				'itemid'	=> new external_value(PARAM_INT, 'grade item ID', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
				'startpage' => new external_value(PARAM_INT, 'start page', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
				'n'		 => new external_value(PARAM_INT, 'page number', VALUE_DEFAULT, 10, NULL_NOT_ALLOWED)
			)
		);
	}

	/**
	 * Returns all grades coresponding to a specific grade item
	 *
	 * @param int itemid
	 *
	 * @return array of grade
	 */
	public static function get_grades_by_itemid($itemid, $startpage, $n) {
//		$params = self::validate_parameters(self::get_grades_by_itemid_parameters(), array('params' => $parameters));
//		$params = $params['params'];

		$courseid = grade_db::get_courseid_by_gradeitemid($itemid);

		$context = get_context_instance(CONTEXT_COURSE, $courseid);
		self::validate_context($context);

		$viewhidden = false;
		if (has_capability('moodle/course:viewhiddenactivities', $context)) {
			$viewhidden = true;
		}

		$grades = grade_db::get_grades_by_itemid($itemid, $viewhidden, $startpage, $n);

		$returngrades = array();
		foreach ($grades as $grade) {
			$newgrade = new GradeStructure($grade);
			$returngrades[] = $newgrade->get_data();
		}

		return $returngrades;
	}

	/**
	 * Returns description of method result value
	 * @return external_description
	 */
	public static function get_grades_by_itemid_returns() {
		return
			new external_multiple_structure(
				GradeStructure::get_class_structure()
			);
	}

	/**
	 * Returns description of method parameters
	 * @return external_function_parameters
	 */
	public static function get_user_grade_by_itemid_parameters() {
		return new external_function_parameters (
			array(
				'userid'	=> new external_value(PARAM_INT, 'user ID', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
				'itemid'	=> new external_value(PARAM_INT, 'grade item ID', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
			)
		);
	}

	/**
	 * Returns a user's grade corresponding to a specific grade item
	 *
	 * @param int itemid
	 *
	 * @return grade
	 */
	public static function get_user_grade_by_itemid($userid, $itemid) {
//		$params = self::validate_parameters(self::get_user_grade_by_itemid_parameters(), array('params' => $parameters));
//		$params = $params['params'];

		$courseid = grade_db::get_courseid_by_gradeitemid($itemid);

		$context = get_context_instance(CONTEXT_COURSE, $courseid);
		self::validate_context($context);

		$viewhidden = false;
		if (has_capability('moodle/course:viewhiddenactivities', $context)) {
			$viewhidden = true;
		}

		if (!$grade = grade_db::get_user_grade_by_itemid($userid, $itemid, $viewhidden)) {
						throw new moodle_exception('generalexceptionmessage','grade', '','No grade is set for the particular user');
		}

		$return = new GradeStructure($grade);
		$return = $return->get_data();

		return $return;
	}

	/**
	 * Returns description of method result value
	 * @return external_description
	 */
	public static function get_user_grade_by_itemid_returns() {
		return GradeStructure::get_class_structure();
	}


	/**
	 * Returns description of method parameters
	 * @return external_function_parameters
	 */
	public static function get_grade_items_by_courseid_parameters() {
		return new external_function_parameters (
			array(
				'courseid'  => new external_value(PARAM_INT, 'user ID', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
				'startpage' => new external_value(PARAM_INT, 'start page', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
				'n'		 => new external_value(PARAM_INT, 'page number', VALUE_DEFAULT, 10, NULL_NOT_ALLOWED)
			)
		);
	}

	/**
	 * Returns a list of n grade items starting from page startpage
	 *
	 * @param int userid
	 * @param int startpage
	 * @param int n
	 *
	 * @return array of grade items
	 */
	public static function get_grade_items_by_courseid($courseid, $startpage, $n) {
//		$params = self::validate_parameters(self::get_grade_items_by_courseid_parameters(), array('params' => $parameters));
//		$params = $params['params'];

		$context = get_context_instance(CONTEXT_COURSE, $courseid);
		self::validate_context($context);

		$viewhiddencourses = false;
		if (has_capability('moodle/course:viewhiddencourses', $context)) {
			$viewhiddencourses = true;
		}

		$viewhiddenactivities = false;
		if (has_capability('moodle/course:viewhiddenactivities', $context)) {
			$viewhiddenactivities = true;
		}

		$gradeitems = grade_db::get_grade_items_by_courseid($courseid, $viewhiddencourses,
													 $viewhiddenactivities, $startpage, $n);

		$returngradeitems = array();
		foreach ($gradeitems as $gradeitem) {
			$newgradeitem = new GradeItemStructure($gradeitem);
			$returngradeitems[] = $newgradeitem->get_data();
		}

		return $returngradeitems;
	}

	/**
	 * Returns description of method result value
	 * @return external_description
	 */
	public static function get_grade_items_by_courseid_returns() {
		return
			new external_multiple_structure(
				GradeItemStructure::get_class_structure()
			);
	}


	/**
	 * Returns description of method parameters
	 * @return external_function_parameters
	 */
	public static function get_user_grades_by_courseid_parameters() {
		return new external_function_parameters (
			array(
				'courseid'  => new external_value(PARAM_INT, 'grade item ID', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
			)
		);
	}

	/**
	 * Returns a user's grade corresponding to a specific courseid
	 *
	 * @param int itemid
	 *
	 * @return grade
	 */
	public static function get_user_grades_by_courseid($courseid) {
		global $USER;

		$context = get_context_instance(CONTEXT_COURSE, $courseid);
		self::validate_context($context);

		$viewhiddenactivities = false;
		if (has_capability('moodle/course:viewhiddenactivities', $context)) {
			$viewhiddenactivities = true;
		}

		$viewhiddencourses = false;
		if (has_capability('moodle/course:viewhiddencourses', $context)) {
			$viewhiddencourses = true;
		}

		$grades = grade_db::get_user_grades_by_courseid($USER->id, $courseid, $viewhiddencourses, $viewhiddenactivities, 0, 0);

		$returngrades = array();
		foreach ($grades as $grade_entries) {
			foreach ($grade_entries as $grade) {
				$newgrade = new GradeStructure($grade);
				$returngrades[] = $newgrade->get_data();
			}
		}

		return $returngrades;
	}

	/**
	 * Returns description of method result value
	 * @return external_description
	 */
	public static function get_user_grades_by_courseid_returns() {
		return
			new external_multiple_structure(
				GradeStructure::get_class_structure()
		);
	}

}
