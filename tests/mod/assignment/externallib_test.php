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
 * External assignment functions unit tests
 *
 * @package    uniappws
 * @category   external
 * @copyright  2012 Goran Josic
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

class local_uniappws_assignment_testcase extends externallib_advanced_testcase {
	
	private $assignment,
			$course,
			$user;
    /**
     * Tests set up
     */
    protected function setUp() {
        global $CFG;
        require_once($CFG->dirroot . '/local/uniappws/mod/assignment/externallib.php');

		// course related
		$this->course = $this->getDataGenerator()->create_course();
		$this->user = $this->getDataGenerator()->create_user();
		$this->setUser($this->user);
		$this->assignment = array();
		$this->assignment[] = $this->getDataGenerator()->create_module('assignment', array('course'=>$this->course->id));
		$this->assignment[] = $this->getDataGenerator()->create_module('assignment', array('course'=>$this->course->id));
    }

	/**
     * Test get_forum_by_id
	 * @expectedException moodle_exception
	 * @expectedExceptionMessage Sorry, but you do not currently have permissions to do that (View assignment)
     */
    public function test_exception_no_permission_to_view_assignment_in_get_assignment() {

        global $DB, $CFG;

        $this->resetAfterTest(true);
		$context = get_context_instance(CONTEXT_COURSE, $this->course->id);
		$this->unassignUserCapability('mod/assignment:view', $context->id, $CFG->defaultuserroleid);
		// test the exception
		$assignment = local_uniappws_assignment::get_assignments_by_courseid($this->course->id, NULL, NULL);
    }

    /**
     * Test get_assignment_by_courseid
     */
    public function test_get_assignments_by_courseid() {

        global $DB;

        $this->resetAfterTest(true);
		$context = get_context_instance(CONTEXT_COURSE, $this->course->id);
		$this->assignUserCapability('mod/assignment:view', $context->id);
		$assignment = local_uniappws_assignment::get_assignments_by_courseid($this->course->id, NULL, NULL);
        $this->assertEquals(count($assignment), count($this->assignment));
        $this->assertEquals($assignment[0]['id'], $this->assignment[0]->id);
        $this->assertEquals($assignment[1]['id'], $this->assignment[1]->id);
    }

}
