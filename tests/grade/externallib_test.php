<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * External course functions unit tests
 *
 * @package    uniappws
 * @category   external
 * @copyright  2012 Goran Josic
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/report/grader/lib.php');

class local_uniappws_grade_testcase extends externallib_advanced_testcase {

	private $course, $user, $forum, $forum_grade_item, $testgrade, $user_struct_size, $grade_struct_size;

    /**
     * Tests set up
     */
    protected function setUp() {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/local/uniappws/grade/externallib.php');
        require_once($CFG->dirroot . '/local/uniappws/grade/gradeStructure.class.php');

		$this->course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($this->course->id);

        // Create and enrol a student.
        $role = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
		$this->user = array();
        $this->user[] = $this->getDataGenerator()->create_user(array('username' => 'Student John'));
        $this->user[] = $this->getDataGenerator()->create_user(array('username' => 'Student Cortana'));
        $this->getDataGenerator()->enrol_user($this->user[0]->id, $this->course->id, $role->id);
        $this->getDataGenerator()->enrol_user($this->user[1]->id, $this->course->id, $role->id);

        // Test with limited grades.
        $CFG->unlimitedgrades = 0;
        $this->grade_struct_size = count(GradeStructure::get_class_structure()->keys);

        $forummax = 80;
		$this->forum = array();
        $this->forum[] = $this->getDataGenerator()->create_module('forum', array('assessed' => 1, 'scale' => $forummax, 'course' => $this->course->id));
        $this->forum[] = $this->getDataGenerator()->create_module('forum', array('assessed' => 1, 'scale' => $forummax, 'course' => $this->course->id));
        // Switch the stdClass instance for a grade item instance.
		$this->forum_grade_item = array();
        $this->forum_grade_item[] = grade_item::fetch(array('itemtype' => 'mod', 'itemmodule' => 'forum', 'iteminstance' => $this->forum[0]->id, 'courseid' => $this->course->id));

        $report = $this->create_report($this->course, $coursecontext);
		$this->testgrade = array();
        $this->testgrade[0] = 60.00;
        $this->testgrade[1] = 80.00;

        $data = new stdClass();
        $data->id = $this->course->id;
        $data->report = 'grader';

        $data->grade = array();
        $data->grade[$this->user[0]->id] = array();
        $data->grade[$this->user[0]->id][$this->forum[0]->id] = $this->testgrade[0];
        $data->grade[$this->user[0]->id][$this->forum[1]->id] = $this->testgrade[0]-5;

        $data->grade[$this->user[1]->id] = array();
        $data->grade[$this->user[1]->id][$this->forum[0]->id] = $this->testgrade[1];

        $warnings = $report->process_data($data);

		if(count($warnings) > 0) {
			return false;
		} else {
			return true;
		}
    }

	private function create_report($course, $coursecontext) {

        $gpr = new grade_plugin_return(array('type' => 'report', 'plugin'=>'grader', 'courseid' => $course->id));
        $report = new grade_report_grader($course->id, $gpr, $coursecontext);

        return $report;
    }

	/**
     * Test get_grade_items_by_userid
     */
    public function test_get_grade_items_by_userid() {

        global $DB;

        $this->resetAfterTest(true);
		$gradeitems = local_uniappws_grade::get_grade_items_by_userid($this->user[0]->id, NULL, NULL);
		$this->assertEquals(count($gradeitems), 3);
		// course gradeitem
		$this->assertEquals($gradeitems[0]['itemtype'], 'course');
		$this->assertEquals($gradeitems[0]['itemmodule'], '');
		// forum gradeitem
		$this->assertEquals($gradeitems[1]['itemtype'], 'mod');
		$this->assertEquals($gradeitems[1]['itemmodule'], 'forum');
		$this->assertEquals($gradeitems[1]['iteminstance'], $this->forum[0]->id);
		// forum gradeitem
		$this->assertEquals($gradeitems[2]['itemtype'], 'mod');
		$this->assertEquals($gradeitems[2]['itemmodule'], 'forum');
		$this->assertEquals($gradeitems[2]['iteminstance'], $this->forum[1]->id);

    }

	/**
     * Test get_grades_by_itemid
     */
    public function test_get_grades_by_itemid() {

        global $DB;

        $this->resetAfterTest(true);
		$grades = local_uniappws_grade::get_grades_by_itemid($this->forum[0]->id, NULL, NULL);
		$this->assertEquals(count($grades), 2);
		// check John
		$this->assertEquals(count($grades[0]), $this->grade_struct_size);
		$this->assertEquals($grades[0]['userid'], $this->user[0]->id);
		$this->assertEquals($grades[0]['finalgrade'], $this->testgrade[0]);
		// check Cortana
		$this->assertEquals(count($grades[1]), $this->grade_struct_size);
		$this->assertEquals($grades[1]['userid'], $this->user[1]->id);
		$this->assertEquals($grades[1]['finalgrade'], $this->testgrade[1]);
    }

	/**
     * Test get_grade_items_by_userid
     */
    public function test_get_grade_items_by_courseid() {

        global $DB;

        $this->resetAfterTest(true);
		$gradeitems = local_uniappws_grade::get_grade_items_by_courseid($this->course->id, NULL, NULL);
		$this->assertEquals(count($gradeitems), 2);
		// forum 1 gradeitem
		$this->assertEquals($gradeitems[0]['itemtype'], 'mod');
		$this->assertEquals($gradeitems[0]['itemmodule'], 'forum');
		$this->assertEquals($gradeitems[0]['iteminstance'], $this->forum[0]->id);
		// forum 2 gradeitem
		$this->assertEquals($gradeitems[1]['itemtype'], 'mod');
		$this->assertEquals($gradeitems[1]['itemmodule'], 'forum');
		$this->assertEquals($gradeitems[1]['iteminstance'], $this->forum[1]->id);

    }

	/**
     * Test get_user_grades_by_courseid
     */
    public function test_get_user_grades_by_courseid() {

        global $DB;

        $this->resetAfterTest(true);
		$grades = local_uniappws_grade::get_user_grades_by_courseid($this->user[0]->id, $this->course->id);
		$this->assertEquals(count($grades), 2);
		// forum 1 grades
		$this->assertEquals(count($grades[0]), $this->grade_struct_size);
		$this->assertEquals($grades[0]['userid'], $this->user[0]->id);
		$this->assertEquals($grades[0]['finalgrade'], $this->testgrade[0]);
		// forum 2 grades
		$this->assertEquals(count($grades[1]), $this->grade_struct_size);
		$this->assertEquals($grades[1]['userid'], $this->user[0]->id);
		$this->assertEquals($grades[1]['finalgrade'], $this->testgrade[1]);
    }
}
