<?php

global $CFG;
global $DB;
require_once(dirname(__FILE__).'/../../../config.php');
require_once(UNIAPP_ROOT . '/mod/forum/externallib.php');
require_once(UNIAPP_ROOT . '/course/test/TestCourseStructure.class.php');

class forumexternal_test extends UnitTestCase {

	public $TestCourseStructure;

	function setUp() {
		$this->TestCourseStructure = new TestCourseStructure();
	}

	function tearDown() {
	}

	public function test_get_forum_by_id() {
		$forum = $this->TestCourseStructure->forum[1];
		$result = local_uniappws_forum::get_forum_by_id($forum->id);

		$this->assertEqual(sizeof($result), 6, "Correct number of results");

		$struct = Forum::get_class_structure();
		$this->assertEqual(sizeof($struct->keys),sizeof($result), 'Same size');

		foreach ($struct->keys as $key => $value){
			$this->assertEqual($forum->$key, $result[$key], 'Same '.$key.' field');
		}
	}

	public function test_get_forums_by_courseid() {
		$courseid = $this->TestCourseStructure->test_course->id;
		$startpage = 0;
		$n = 5;
		$forums = local_uniappws_forum::get_forums_by_courseid($courseid, $startpage, $n);

		$this->assertEqual(sizeof($forums), sizeof($this->TestCourseStructure->forum), "Same number of results: ".sizeof($forums));

		$struct = Forum::get_class_structure();

		for ($i = 0; $i < 2; $i++) {
			$this->assertEqual(sizeof($struct->keys),sizeof($forums[$i]), 'Same size: '.sizeof($struct->keys).' - '.sizeof($forums[$i]).' ');

			foreach ($struct->keys as $key => $value){
				$this->assertEqual($this->TestCourseStructure->forum[$i]->$key, $forums[$i][$key], 'Same '.$key.' field');
			}
		}

		for ($i = 0; $i < 2; $i++) {
			$this->assertEqual(sizeof($struct->keys),sizeof($forums[$i]), 'Same size');

			foreach ($struct->keys as $key => $value){
				$this->assertEqual($this->TestCourseStructure->forum[$i]->$key, $forums[$i][$key], 'Same '.$key.' field');
			}
		}
	}

	public function test_get_forums_by_userid() {
		global $USER;
		$CurrentUser = $USER;

		$userid = $this->TestCourseStructure->test_user->id;
		$startpage = 0;
		$n = 5;
		// change temporary the $USER
		$USER = $this->TestCourseStructure->test_user;
		$forums = local_uniappws_forum::get_forums_by_userid($userid, $startpage, $n);
		$USER = $CurrentUser;

		$this->assertEqual(sizeof($forums), sizeof($this->TestCourseStructure->forum), "Same number of results: ".sizeof($forums));

		$struct = Forum::get_class_structure();

		for ($i = 0; $i < 2; $i++) {
			$this->assertEqual(sizeof($struct->keys),sizeof($forums[$i]), 'Same size: '.sizeof($struct->keys).' - '.sizeof($forums[$i]).' ');

			foreach ($struct->keys as $key => $value){
				$this->assertEqual($this->TestCourseStructure->forum[$i]->$key, $forums[$i][$key], 'Same '.$key.' field');
			}
		}

		for ($i = 0; $i < 2; $i++) {
			$this->assertEqual(sizeof($struct->keys), sizeof($forums[$i]), 'Same size');

			foreach ($struct->keys as $key => $value){
				$this->assertEqual($this->TestCourseStructure->forum[$i]->$key, $forums[$i][$key], 'Same '.$key.' field');
			}
		}
	}

	public function test_get_forum_discussions() {
		global $DB;

		$forumid = $this->TestCourseStructure->test_forum->id;
		$startpage = 0;
		$n = 5;
		$discussions = local_uniappws_forum::get_forum_discussions($forumid, $startpage, $n);

		$this->assertEqual(sizeof($discussions), sizeof($this->TestCourseStructure->forum_discussion), "Same number of results: ".sizeof($discussions));
	}

	public function test_get_discussion_by_id() {
		global $DB;

		$discid = $this->TestCourseStructure->test_forum_discussion->id;

		$result = local_uniappws_forum::get_discussion_by_id($discid);

		$struct = Discussion::get_class_structure();

		$this->assertEqual(sizeof($struct->keys), sizeof($result), 'Same size');

		foreach ($struct->keys as $key => $value){
			$this->assertEqual($this->TestCourseStructure->test_forum_discussion->$key, $result[$key], 'Same '.$key.' field');
		}
	}
}

?>
