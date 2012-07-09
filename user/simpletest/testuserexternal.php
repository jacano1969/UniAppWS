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
 * User External Test
 * Patched and implemented by Goran Josic
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

require_once(dirname(__FILE__).'/../../config.php');

require_once(UNIAPP_ROOT . '/user/user.class.php');
require_once(UNIAPP_ROOT . '/course/test/CourseStructure.class.php');
require_once(UNIAPP_ROOT . '/user/externallib.php');


class userexternal_test extends UnitTestCase {

	public $CourseStructure;

	function setUp() {
			$this->CourseStructure = new CourseStructure();
	}

	function tearDown() {
	}

    public function test_get_user() {
		global $USER;
		$CurrentUser = $USER;
		// change temporary the $USER
		$USER = $this->CourseStructure->test_user;
		$user = local_uniappws_user::get_user();
		$USER = $CurrentUser;

        $this->assertEqual(sizeof($user), 11, "Size is: ".sizeof($user));

		foreach($this->CourseStructure->test_user as $key => $value) {
        	$this->assertEqual($user[$key], $value, "$key matches");
		}
	}

    public function test_get_user_by_userid() {
        $userid = $this->CourseStructure->test_user->id;
		$user = local_uniappws_user::get_user_by_userid($userid);

        $this->assertEqual(sizeof($user), 11, "Size is: ".sizeof($user));

		foreach($this->CourseStructure->test_user as $key => $value) {
        	$this->assertEqual($user[$key], $value, "$key matches");
		}
	}

	public function test_get_user_by_username() {
        $username = $this->CourseStructure->test_user->username;
		$user = local_uniappws_user::get_user_by_username($username);

        $this->assertEqual(sizeof($user), 11, "Size is: ".sizeof($user));

		foreach($this->CourseStructure->test_user as $key => $value) {
        	$this->assertEqual($user[$key], $value, "$key matches");
		}
	}

    public function test_get_users_by_courseid() {
        $courseid = $this->CourseStructure->test_course->id;
        $startpage = 0;
        $n = 5;
        $users = local_uniappws_user::get_users_by_courseid($courseid, $startpage, $n);

        $this->assertEqual(sizeof($users), sizeof($this->CourseStructure->user), "Size is: ".sizeof($users));
	}
}
