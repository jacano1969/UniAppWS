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
 * External forum functions unit tests
 *
 * @package    uniappws
 * @category   external
 * @copyright  2012 Goran Josic
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

class local_uniappws_forum_testcase extends externallib_advanced_testcase {
	
	private $forum,
			$course,
			$user,
			$discussion,
			$post,
			$forum_struct_size,
			$discussion_struct_size,
			$post_struct_size;
    /**
     * Tests set up
     */
    protected function setUp() {
        global $CFG;
        require_once($CFG->dirroot . '/local/uniappws/mod/forum/externallib.php');
		// course related
		$this->course = $this->getDataGenerator()->create_course();
		$this->user = $this->getDataGenerator()->create_user();
		// forums
		$this->forum = array();
		$this->forum[] = $this->getDataGenerator()->create_module('forum', array('course'=>$this->course->id));
		$this->forum[] = $this->getDataGenerator()->create_module('forum', array('course'=>$this->course->id));
		$this->forum[] = $this->getDataGenerator()->create_module('forum', array('course'=>$this->course->id));
        $this->forum_struct_size = count(ForumStructure::get_class_structure()->keys);
		// discussions
		$this->discussion = array();
		$this->discussion[] = array(
			'forumid'    => $this->forum[0]->id,
			'name'       => 'Test Discussion 1',
			'intro'      => 'Test Introduction 1',
			'groupid'    => -1,
			'attachments'=> NULL,
			'format'     => 1,
			'mailnow'    => 0
		);
		$this->discussion[] = array(
			'forumid'    => $this->forum[0]->id,
			'name'       => 'Test Discussion 2',
			'intro'      => 'Test Introduction 2',
			'groupid'    => -1,
			'attachments'=> NULL,
			'format'     => 1,
			'mailnow'    => 0
		);
        $this->discussion_struct_size = count(DiscussionStructure::get_class_structure()->keys);
		// posts
		$this->post = array();
		$this->post[] = array(
			'parentid'   => 1, // temporary value
			'subject'    => 'Test Post Subject 1',
			'message'      => 'Test Post Message 1'
		);
		$this->post[] = array(
			'parentid'   => 2, // temporary value
			'subject'    => 'Test Post Subject 2',
			'message'      => 'Test Post Message 2'
		);
        $this->post_struct_size = count(PostStructure::get_class_structure()->keys);
    }

	/**
     * Test get_forum_by_id
	 * @expectedException moodle_exception
	 * @expectedExceptionMessage Activity Forum not found.
     */
    public function test_exception_forum_not_found_in_get_forum_by_id() {

        global $DB;

        $this->resetAfterTest(true);
		// test the exception
		$forum = local_uniappws_forum::get_forum_by_id(0);
    }

    /**
     * Test get_forum_by_id
     */
    public function test_get_forum_by_id() {

        global $DB;

        $this->resetAfterTest(true);
		$forum = local_uniappws_forum::get_forum_by_id($this->forum[0]->id);
        $this->assertEquals(count($forum), $this->forum_struct_size);
        $this->assertEquals($forum['id'], $this->forum[0]->id);
    }

    /**
     * Test get_forums_by_courseid
     */
    public function test_get_forums_by_courseid() {

        global $DB;

        $this->resetAfterTest(true);
		$forums = local_uniappws_forum::get_forums_by_courseid($this->course->id, NULL, NULL);
        $this->assertEquals(count($forums), 3);
		for($i=0; $i<count($forums); ++$i){
        	$this->assertEquals($forums[$i]['id'], $this->forum[$i]->id);
		}
    }

	/**
     * Test get_forums_by_userid
	 * @expectedException moodle_exception
	 * @expectedExceptionMessage User not found.
     */
    public function test_exception_user_not_found_in_get_forums_by_userid() {

        global $DB;

        $this->resetAfterTest(true);
		$forums = local_uniappws_forum::get_forums_by_userid(0, NULL, NULL);
    }

    /**
     * Test get_forums_by_userid
     */
	public function test_get_forums_by_userid() {

        global $DB;

        $this->resetAfterTest(true);
		$this->getDataGenerator()->enrol_user($this->user->id, $this->course->id);
		$this->setUser($this->user);
		$forums = local_uniappws_forum::get_forums_by_userid($this->user->id, NULL, NULL);
        $this->assertEquals(count($forums), 3);
		for($i=0; $i<count($forums); ++$i){
        	$this->assertEquals($forums[$i]['id'], $this->forum[$i]->id);
		}
    }

	/**
     * Test create_discussion 
	 * @expectedException moodle_exception
	 * @expectedExceptionMessage Sorry, but you do not currently have permissions to do that (Start new discussions)
     */
	public function test_exception_no_permission_to_create_discussion() {

        global $DB;

        $this->resetAfterTest(true);
		// no user set so this should fail
		$outcome = local_uniappws_forum::create_discussion($this->discussion[0], NULL, NULL);
    }

	/**
     * Test create_discussion 
	 * @expectedException moodle_exception
	 * @expectedExceptionMessage Activity Forum not found.
     */
	public function test_exception_forum_not_found_in_create_discussion() {

        global $DB;

        $this->resetAfterTest(true);
		$outcome = local_uniappws_forum::create_discussion(array('forumid' => 0), NULL, NULL);
    }

	/**
     * Test create_discussion 
     */
	public function test_create_discussion() {

        global $DB;

        $this->resetAfterTest(true);
		$this->getDataGenerator()->enrol_user($this->user->id, $this->course->id);
		$this->setUser($this->user);
		$outcome = local_uniappws_forum::create_discussion($this->discussion[0], NULL, NULL);
        $this->assertEquals(count($outcome), 2);
        $this->assertTrue(isset($outcome['discid']));
        $this->assertTrue(is_numeric($outcome['discid']));
        $this->assertTrue(isset($outcome['postid']));
        $this->assertTrue(is_numeric($outcome['postid']));
    }

	/**
     * Test get_forum_discussions
	 * @expectedException moodle_exception
	 * @expectedExceptionMessage Activity Forum not found.
     */
	public function test_exception_forum_not_found_in_get_forum_discussions() {

        global $DB;

        $this->resetAfterTest(true);
		$outcome = local_uniappws_forum::get_forum_discussions(0, NULL, NULL);
    }

	/**
     * Test get_forum_discussions
	 * @depends test_create_discussion
     */
	public function test_get_forum_discussions() {

        global $DB;

        $this->resetAfterTest(true);
		$this->getDataGenerator()->enrol_user($this->user->id, $this->course->id);
		$this->setUser($this->user);
		$outcome0 = local_uniappws_forum::create_discussion($this->discussion[0], NULL, NULL);
		$outcome1 = local_uniappws_forum::create_discussion($this->discussion[1], NULL, NULL);
		$discussions = local_uniappws_forum::get_forum_discussions($this->forum[0]->id, NULL, NULL);
        $this->assertEquals(count($discussions), 2);
        $this->assertEquals($discussions[0]['id'], $outcome0['discid']);
        $this->assertEquals($discussions[1]['id'], $outcome1['discid']);
    }

	/**
     * Test get_forum_discussion_by_id
	 * @expectedException moodle_exception
     */
	public function test_exception_forum_not_found_in_get_discussion_by_id() {

        global $DB;

        $this->resetAfterTest(true);
		$outcome = local_uniappws_forum::get_discussion_by_id(0, NULL, NULL);
    }

	/**
     * Test get_forum_discussions
	 * @depends test_create_discussion
     */
	public function test_get_discussion_by_id() {

        global $DB;

        $this->resetAfterTest(true);
		$this->getDataGenerator()->enrol_user($this->user->id, $this->course->id);
		$this->setUser($this->user);
		$outcome = local_uniappws_forum::create_discussion($this->discussion[0], NULL, NULL);
		$discussion = local_uniappws_forum::get_discussion_by_id($outcome['discid'], NULL, NULL);
        $this->assertEquals(count($discussion), 7);
        $this->assertEquals($discussion['id'], $outcome['discid']);
    }

	/**
     * Test create_post 
	 * @expectedException moodle_exception
     */
	public function test_exception_forum_not_found_in_create_post() {

        global $DB;

        $this->resetAfterTest(true);
		$outcome = local_uniappws_forum::create_post(0, '', '');
    }

	/**
     * Test create_post 
	 * @depends test_create_discussion
     */
	public function test_create_post() {

        global $DB;

        $this->resetAfterTest(true);
		$this->getDataGenerator()->enrol_user($this->user->id, $this->course->id);
		$this->setUser($this->user);
		$disc_outcome = local_uniappws_forum::create_discussion($this->discussion[0], NULL, NULL);
        $this->post[0]['parentid'] = $disc_outcome['postid'];
		// suppress errors because of an internal moodle php E_STRICT violation
		// that generates the error: Only variables should be passed by reference
		$post_outcome = @local_uniappws_forum::create_post(
			$this->post[0]['parentid'],
			$this->post[0]['subject'],
			$this->post[0]['message']
		);
        $this->assertTrue(is_array($post_outcome));
        $this->assertTrue(isset($post_outcome['postid']));
        $this->assertTrue(is_numeric($post_outcome['postid']));
    }

	/**
     * Test update_post 
	 * @expectedException moodle_exception
     */
	public function test_exception_forum_not_found_in_update_post() {

        global $DB;

        $this->resetAfterTest(true);
		$this->getDataGenerator()->enrol_user($this->user->id, $this->course->id);
		$this->setUser($this->user);

		$updated_post = array(
			'id' => 0,
			'parent' => 0,
			'userid' => $this->user->id,
			'discussion' => 0,
			'created' => 0,
			'modified' => 0,
			'subject' => 'new subject',
			'message' => 'new message',
			'attachments' => NULL
		);

		$this->setUser(null);
		// avoid moodle internal quirks
		$update_post_outcome = @local_uniappws_forum::update_post($updated_post);
    }

	/**
     * Test update_post 
	 * @expectedException moodle_exception
	 * @expectedExceptionMessage No permission to edit posts in the forum.
     */
	public function test_exception_no_permission_to_edit_in_update_post() {

        global $DB;

        $this->resetAfterTest(true);
		$this->getDataGenerator()->enrol_user($this->user->id, $this->course->id);
		$this->setUser($this->user);
		$create_discussion_outcome = local_uniappws_forum::create_discussion($this->discussion[0], NULL, NULL);

        $this->post[0]['parentid'] = $create_discussion_outcome['postid'];
		// suppress errors because of an internal moodle php E_STRICT violation
		// that generates the error: Only variables should be passed by reference
		$create_post_outcome = @local_uniappws_forum::create_post(
			$this->post[0]['parentid'],
			$this->post[0]['subject'],
			$this->post[0]['message']
		);

		$updated_post = array(
			'id' => $create_post_outcome['postid'],
			'parent' => $this->post[0]['parentid'],
			'userid' => $this->user->id,
			'discussion' => $create_discussion_outcome['discid'],
			'created' => 0,
			'modified' => 0,
			'subject' => 'new subject',
			'message' => 'new message',
			'attachments' => NULL
		);

		$this->setGuestUser();
		// avoid moodle internal quirks
		$update_post_outcome = @local_uniappws_forum::update_post($updated_post);
    }

	/**
     * Test create_post 
	 * @depends test_create_post
     */
	public function test_update_post() {

        global $DB;

        $this->resetAfterTest(true);
		$this->getDataGenerator()->enrol_user($this->user->id, $this->course->id);
		$this->setUser($this->user);
		$create_discussion_outcome = local_uniappws_forum::create_discussion($this->discussion[0], NULL, NULL);

        $this->post[0]['parentid'] = $create_discussion_outcome['postid'];
		// suppress errors because of an internal moodle php E_STRICT violation
		// that generates the error: Only variables should be passed by reference
		$create_post_outcome = @local_uniappws_forum::create_post(
			$this->post[0]['parentid'],
			$this->post[0]['subject'],
			$this->post[0]['message']
		);

		$updated_post = array(
			'id' => $create_post_outcome['postid'],
			'parent' => $this->post[0]['parentid'],
			'userid' => $this->user->id,
			'discussion' => $create_discussion_outcome['discid'],
			'created' => 0,
			'modified' => 0,
			'subject' => 'new subject',
			'message' => 'new message',
			'attachments' => NULL
		);
		// avoid moodle internal quirks
		$update_post_outcome = @local_uniappws_forum::update_post($updated_post);

		$this->assertTrue(is_array($update_post_outcome));
        $this->assertTrue(isset($update_post_outcome['success']));
        $this->assertEquals($update_post_outcome['success'], 1);
    }

	/**
     * Test delete_post 
	 * @depends test_create_post
     */
	public function test_delete_post() {

        global $DB;

        $this->resetAfterTest(true);
		$this->getDataGenerator()->enrol_user($this->user->id, $this->course->id);
		$this->setUser($this->user);
		$create_discussion_outcome = local_uniappws_forum::create_discussion($this->discussion[0], NULL, NULL);

        $this->post[0]['parentid'] = $create_discussion_outcome['postid'];
		// suppress errors because of an internal moodle php E_STRICT violation
		// that generates the error: Only variables should be passed by reference
		$create_post_outcome = @local_uniappws_forum::create_post(
			$this->post[0]['parentid'],
			$this->post[0]['subject'],
			$this->post[0]['message']
		);
		// avoid moodle internal quirks
		$delete_post_outcome = @local_uniappws_forum::delete_post($create_post_outcome['postid']);

		$this->assertTrue(is_array($delete_post_outcome));
        $this->assertTrue(isset($delete_post_outcome['success']));
        $this->assertEquals($delete_post_outcome['success'], 1);
    }

	/**
     * Test delete_discussion
	 * @depends test_create_discussion
	 * @depends test_create_post
	 * @expectedException moodle_exception
	 * @expectedExceptionMessage Sorry, but you do not currently have permissions to do that (Delete any posts (anytime))
     */
	public function test_exception_no_permission_to_delete_any_post_in_delete_discussion() {

        global $DB;

        $this->resetAfterTest(true);
		$this->getDataGenerator()->enrol_user($this->user->id, $this->course->id);
		$this->setUser($this->user);
		$create_discussion_outcome = local_uniappws_forum::create_discussion($this->discussion[0], NULL, NULL);
        $this->post[0]['parentid'] = $create_discussion_outcome['postid'];
		// suppress errors because of an internal moodle php E_STRICT violation
		// that generates the error: Only variables should be passed by reference
		$create_post_outcome = @local_uniappws_forum::create_post(
			$this->post[0]['parentid'],
			$this->post[0]['subject'],
			$this->post[0]['message']
		);
		// avoid moodle internal quirks
		$delete_discussion_outcome = @local_uniappws_forum::delete_discussion($create_discussion_outcome['discid']);
    }

	/**
     * Test delete_discussion
	 * @depends test_create_discussion
	 * @depends test_create_post
     */
	public function test_delete_discussion() {

        global $DB;

        $this->resetAfterTest(true);
		$this->getDataGenerator()->enrol_user($this->user->id, $this->course->id);
		$this->setUser($this->user);
		$create_discussion_outcome = local_uniappws_forum::create_discussion($this->discussion[0], NULL, NULL);
        $this->post[0]['parentid'] = $create_discussion_outcome['postid'];
		// suppress errors because of an internal moodle php E_STRICT violation
		// that generates the error: Only variables should be passed by reference
		$create_post_outcome = @local_uniappws_forum::create_post(
			$this->post[0]['parentid'],
			$this->post[0]['subject'],
			$this->post[0]['message']
		);
		// set the admin user; this user can delete any post
		$this->setAdminUser();
		// avoid moodle internal quirks
		$delete_discussion_outcome = @local_uniappws_forum::delete_discussion($create_discussion_outcome['discid']);
		$this->assertTrue(is_array($delete_discussion_outcome));
        $this->assertTrue(isset($delete_discussion_outcome['success']));
        $this->assertEquals($delete_discussion_outcome['success'], 1);
    }
}
