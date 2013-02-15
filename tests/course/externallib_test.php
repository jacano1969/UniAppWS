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
 * UniAppWs course functions unit tests
 *
 * @package    uniappws
 * @category   external
 * @copyright  2012 Goran Josic
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/local/uniappws/course/externallib.php');
require_once($CFG->dirroot . '/local/uniappws/course/courseStructure.class.php');

class local_uniappws_course_testcase extends externallib_advanced_testcase {
	
    /**
     * Tests set up
     */
    protected function setUp() {
        global $CFG;
        require_once($CFG->dirroot . '/local/uniappws/course/externallib.php');
    }

    /**
     * Test get_courses_by_userid
     */
    public function test_get_courses_by_userid() {

        global $DB;

        $this->resetAfterTest(true);
		// create users
		$user = $this->getDataGenerator()->create_user();
		// create courses
		$course1 = $this->getDataGenerator()->create_course();
		$course2 = $this->getDataGenerator()->create_course();
		// enrol user
		$this->getDataGenerator()->enrol_user($user->id, $course1->id);
		$this->getDataGenerator()->enrol_user($user->id, $course2->id);
		// get the output	
		$courses = local_uniappws_course::get_courses_by_userid($user->id, 0, 0);
		// test the output
        $course_struct_size = count(CourseStructure::get_class_structure()->keys);
		$this->assertEquals(count($courses), 2);
		$this->assertTrue(is_array($courses));
		$this->assertEquals(count($courses[0]), $course_struct_size);
		$this->assertEquals($courses[0]['id'], $course1->id);
		$this->assertEquals(count($courses[1]), $course_struct_size);
		$this->assertEquals($courses[1]['id'], $course2->id);
    }


	// no easy way to test get_course_modules so here the testing is on the function
	// that does all the work
    /**
     * Test get_course_modules
     */
    public function test_extract_course_modules() {

        global $DB;

        $this->resetAfterTest(true);
		// create course
		$course = $this->getDataGenerator()->create_course();
		$module1 = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
		$module2 = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
		// get the output
		$modules = local_uniappws_course::extract_course_modules($course->id);
		// test the output
        $module_struct_size = count(ModuleStructure::get_class_structure()->keys);
		$this->assertEquals(count($modules), 2);
		$this->assertTrue(is_array($modules));
		$this->assertEquals(count($modules[0]), $module_struct_size);
		$this->assertEquals($modules[0]['id'], $module1->id);
		$this->assertEquals(count($modules[1]), $module_struct_size);
		$this->assertEquals($modules[1]['id'], $module2->id);
    }
}
