<?php

if (!defined('MOODLE_INTERNAL')) {
	die('Direct access to this script is forbidden.');	///  It must be included from a Moodle page
}

require_once(dirname(__FILE__).'/../../../config.php');
require_once(UNIAPP_ROOT . '/mod/forum/forum.class.php');

class forumclass_test extends UnitTestCase {

	public function test_forum_class() {

		$forum = new StdClass();
		$forum->id = 1;
		$forum->course = 2;
		$forum->type = 'news';
		$forum->name = 'Forum name';
		$forum->intro = 'Forum intro';
		$forum->introformat = 0;
		$forum->assessed = 0;
		$forum->assesstimestart = 0;
		$forum->assesstimefinish = 0;
		$forum->scale = 0;
		$forum->maxbytes = 0;
		$forum->maxattachments = 1;
		$forum->forcesubscribe = 1;
		$forum->trackingtype = 1;
		$forum->rsstype = 0;
		$forum->rssarticles = 0;
		$forum->timemodified = 1306943096;
		$forum->warnafter = 0;
		$forum->blockafter = 0;
		$forum->blockperiod = 0;
		$forum->completiondiscussions = 0;
		$forum->completionreplies = 0;
		$forum->completionposts = 0;

		$newforum = new Forum($forum);
		$data = $newforum->get_data();
		$struct = Forum::get_class_structure();

		$this->assertEqual(sizeof($struct->keys),sizeof($data), 'Same size');

		foreach ($struct->keys as $key => $value){
			$this->assertEqual($forum->$key, $data[$key], 'Same '.$key.' field');
		}

	}

	public function test_forum_class_exception() {

		$forum = new StdClass();
		$forum->id = 1;
		$forum->course = 2;
		$forum->type = 'news';
		$forum->name = 'Forum name';
		$forum->intro = 'Forum intro';
		$forum->introformat = 0;
		$forum->assessed = 0;
		$forum->assesstimestart = 0;
		$forum->assesstimefinish = 0;
		$forum->scale = 0;
		$forum->maxbytes = 0;
		$forum->maxattachments = 1;
		$forum->forcesubscribe = 1;
		$forum->trackingtype = 1;
		$forum->rsstype = 0;
		$forum->rssarticles = 0;
		$forum->timemodified = 1306943096;
		$forum->warnafter = 0;
		$forum->blockafter = 0;
		$forum->blockperiod = 0;
		$forum->completiondiscussions = 0;
		$forum->completionreplies = 0;
		$forum->completionposts = 0;

		unset($forum->id); // Incomplete record

		$this->expectException('Exception');
		$newforum = new Forum($forum);
	}

}
?>
