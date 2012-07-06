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
 * Forum External API Test
 *
 * @package MoodbileServer
 * @subpackage Forum
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

global $CFG;
global $DB;
require_once(dirname(__FILE__).'/../../../config.php');
require_once(UNIAPP_ROOT . '/mod/forum/externallib.php');
require_once(UNIAPP_ROOT . '/course/test/CourseStructure.class.php');

class forumexternal_test extends UnitTestCase {

	public $CourseStructure;

	function setUp() {
		$this->CourseStructure = new CourseStructure();
	}

	function tearDown() {
	}

	public function test_get_forum_by_id() {
		$forum = $this->CourseStructure->forum[1];
		$result = local_uniappws_forum::get_forum_by_id($forum->id);

		$this->assertEqual(sizeof($result), 6, "Correct number of results");

		$struct = Forum::get_class_structure();
		$this->assertEqual(sizeof($struct->keys),sizeof($result), 'Same size');

		foreach ($struct->keys as $key => $value){
			$this->assertEqual($forum->$key, $result[$key], 'Same '.$key.' field');
		}
	}

	public function test_get_forums_by_courseid() {
		$courseid = $this->CourseStructure->test_course->id;
		$startpage = 0;
		$n = 5;
		$forums = local_uniappws_forum::get_forums_by_courseid($courseid, $startpage, $n);

		$this->assertEqual(sizeof($forums), sizeof($this->CourseStructure->forum), "Same number of results: ".sizeof($forums));

		$struct = Forum::get_class_structure();

		for ($i = 0; $i < 2; $i++) {
			$this->assertEqual(sizeof($struct->keys),sizeof($forums[$i]), 'Same size: '.sizeof($struct->keys).' - '.sizeof($forums[$i]).' ');

			foreach ($struct->keys as $key => $value){
				$this->assertEqual($this->CourseStructure->forum[$i]->$key, $forums[$i][$key], 'Same '.$key.' field');
			}
		}

		for ($i = 0; $i < 2; $i++) {
			$this->assertEqual(sizeof($struct->keys),sizeof($forums[$i]), 'Same size');

			foreach ($struct->keys as $key => $value){
				$this->assertEqual($this->CourseStructure->forum[$i]->$key, $forums[$i][$key], 'Same '.$key.' field');
			}
		}
	}

	public function test_get_forums_by_userid() {
		global $USER;
		$CurrentUser = $USER;

		$userid = $this->CourseStructure->test_user->id;
		$startpage = 0;
		$n = 5;
		// change temporary the $USER
		$USER = $this->CourseStructure->test_user;
		$forums = local_uniappws_forum::get_forums_by_userid($userid, $startpage, $n);
		$USER = $CurrentUser;

		$this->assertEqual(sizeof($forums), sizeof($this->CourseStructure->forum), "Same number of results: ".sizeof($forums));

		$struct = Forum::get_class_structure();

		for ($i = 0; $i < 2; $i++) {
			$this->assertEqual(sizeof($struct->keys),sizeof($forums[$i]), 'Same size: '.sizeof($struct->keys).' - '.sizeof($forums[$i]).' ');

			foreach ($struct->keys as $key => $value){
				$this->assertEqual($this->CourseStructure->forum[$i]->$key, $forums[$i][$key], 'Same '.$key.' field');
			}
		}

		for ($i = 0; $i < 2; $i++) {
			$this->assertEqual(sizeof($struct->keys), sizeof($forums[$i]), 'Same size');

			foreach ($struct->keys as $key => $value){
				$this->assertEqual($this->CourseStructure->forum[$i]->$key, $forums[$i][$key], 'Same '.$key.' field');
			}
		}
	}

	public function test_get_forum_discussions() {
		global $DB;

		$forumid = $this->CourseStructure->test_forum->id;
		$startpage = 0;
		$n = 5;
		$discussions = local_uniappws_forum::get_forum_discussions($forumid, $startpage, $n);

		$this->assertEqual(sizeof($discussions), sizeof($this->CourseStructure->forum_discussion), "Same number of results: ".sizeof($discussions));
	}

	public function test_get_discussion_by_id() {
		global $DB;

		$discid = $this->CourseStructure->test_forum_discussion->id;

		$result = local_uniappws_forum::get_discussion_by_id($discid);

		$struct = Discussion::get_class_structure();

		$this->assertEqual(sizeof($struct->keys), sizeof($result), 'Same size');

		foreach ($struct->keys as $key => $value){
			$this->assertEqual($this->CourseStructure->test_forum_discussion->$key, $result[$key], 'Same '.$key.' field');
		}
	}
	/*
	*/
}
