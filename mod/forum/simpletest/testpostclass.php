<?php

if (!defined('MOODLE_INTERNAL')) {
	die('Direct access to this script is forbidden.');	///  It must be included from a Moodle page
}

require_once(dirname(__FILE__).'/../../../config.php');
require_once(UNIAPP_ROOT . '/mod/forum/post.class.php');
print_r(UNIAPP_ROOT . '/mod/forum/post.class.php');

class postclass_test extends UnitTestCase {

	public function test_post_class() {

		$post = new StdClass();
		$post->id = 2;
		$post->discussion = 2;
		$post->parent = 1;
		$post->userid = 2;
		$post->created = 1306943090;
		$post->modified = 1306943096;
		$post->mailed = 0;
		$post->subject = 'Test post';
		$post->message = 'Test post message';
		$post->messageformat = 0;
		$post->messagetrust = 0;
		$post->attachment = null;
		$post->totalscore = 0;
		$post->mailnow = 0;

		$newpost = new ForumPost($post);
		$data = $newpost->get_data();
		$struct = ForumPost::get_class_structure();

		$this->assertEqual(sizeof($struct->keys),sizeof($data), 'Same size');

		foreach ($struct->keys as $key => $value){
			$this->assertEqual($post->$key, $data[$key], 'Same '.$key.' field');
		}

	}

	public function test_forum_class_exception() {

		$post = new StdClass();
		$post->id = 2;
		$post->discussion = 2;
		$post->parent = 1;
		$post->userid = 2;
		$post->created = 1306943090;
		$post->modified = 1306943096;
		$post->mailed = 0;
		$post->subject = 'Test post';
		$post->message = 'Test post message';
		$post->messageformat = 0;
		$post->messagetrust = 0;
		$post->attachment = null;
		$post->totalscore = 0;
		$post->mailnow = 0;

		unset($post->id); // Incomplete record

		$this->expectException('Exception');
		$newpost = new ForumPost($post);
	}

}
?>
