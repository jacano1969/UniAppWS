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

class local_uniappws_user_testcase extends externallib_advanced_testcase {

	private $course, $user, $user_struct_size;
    /**
     * Tests set up
     */
    protected function setUp() {
        global $CFG;
        require_once($CFG->dirroot . '/local/uniappws/user/externallib.php');
		$this->user = array();
		$this->user[] = $this->getDataGenerator()->create_user();
		$this->user[] = $this->getDataGenerator()->create_user();
		$this->user[] = $this->getDataGenerator()->create_user();
		$this->course = $this->getDataGenerator()->create_course();
        $this->user_struct_size = count(UserStructure::get_class_structure()->keys);
		$this->setUser($this->user[0]);
    }

    /**
     * Test get_user
     */
    public function test_get_user() {

        global $DB;

        $this->resetAfterTest(true);
		$user = local_uniappws_user::get_user();
		$this->assertEquals(count($user), $this->user_struct_size);
		$this->assertEquals($user['id'], $this->user[0]->id);
    }

	/**
     * Test get_user_by_userid
     */
    public function test_get_user_by_userid() {

        global $DB;

        $this->resetAfterTest(true);
		$user = local_uniappws_user::get_user_by_userid($this->user[0]->id);
		$this->assertEquals(count($user), $this->user_struct_size);
		$this->assertEquals($user['id'], $this->user[0]->id);
    }

	/**
     * Test get_user_by_username
     */
    public function test_get_user_by_username() {

        global $DB;

        $this->resetAfterTest(true);
		$user = local_uniappws_user::get_user_by_username($this->user[0]->username);
		$this->assertEquals(count($user), $this->user_struct_size);
		$this->assertEquals($user['id'], $this->user[0]->id);
    }

	/**
     * Test get_user_by_username
     */
    public function test_get_user_by_courseid() {

        global $DB;

        $this->resetAfterTest(true);
		$this->getDataGenerator()->enrol_user($this->user[0]->id, $this->course->id);
		$this->getDataGenerator()->enrol_user($this->user[1]->id, $this->course->id);
		$this->getDataGenerator()->enrol_user($this->user[2]->id, $this->course->id);
		$list = local_uniappws_user::get_users_by_courseid($this->course->id, NULL, NULL);
		$this->assertEquals(count($list), 3);
		$this->assertEquals($list[0]['id'], $this->user[0]->id);
		$this->assertEquals($list[1]['id'], $this->user[1]->id);
		$this->assertEquals($list[2]['id'], $this->user[2]->id);
    }


}
