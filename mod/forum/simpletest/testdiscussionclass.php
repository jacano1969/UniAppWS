<?php

if (!defined('MOODLE_INTERNAL')) {
	die('Direct access to this script is forbidden.');	///  It must be included from a Moodle page
}

require_once(dirname(__FILE__).'/../../../config.php');
require_once(UNIAPP_ROOT . '/mod/forum/discussion.class.php');

class discussionclass_test extends UnitTestCase {

	public function test_discussion_class() {
		$discussion = new StdClass();
		$discussion->id = 1;
		$discussion->course = 2;
		$discussion->forum = 1;
		$discussion->name = 'test discussion';
		$discussion->firstpost = 0;
		$discussion->userid = 2;
		$discussion->groupid = 0;
		$discussion->assessed = 1;
		$discussion->timemodified = 1306943096;
		$discussion->usermodified = 0;
		$discussion->timestart = 1306943098;
		$discussion->timeend = 1306943099;

		$newdiscussion = new Discussion($discussion);
		$data = $newdiscussion->get_data();
		$struct = Discussion::get_class_structure();

		$this->assertEqual(sizeof($struct->keys),sizeof($data), 'Same size');

		foreach ($struct->keys as $key => $value){
			$this->assertEqual($discussion->$key, $data[$key], 'Same '.$key.' field');
		}

	}

	public function test_forum_class_exception() {
		$discussion = new StdClass();
		$discussion->id = 1;
		$discussion->course = 2;
		$discussion->forum = 1;
		$discussion->name = 'test discussion';
		$discussion->firstpost = 0;
		$discussion->userid = 2;
		$discussion->groupid = 0;
		$discussion->assessed = 1;
		$discussion->timemodified = 1306943096;
		$discussion->usermodified = 0;
		$discussion->timestart = 1306943098;
		$discussion->timeend = 1306943099;

		unset($discussion->id); // Incomplete record

		$this->expectException('Exception');
		$newdiscussion = new Discussion($discussion);
	}

}
?>
