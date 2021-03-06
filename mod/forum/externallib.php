<?php

require_once(dirname(__FILE__).'/../../config.php');
require_once(UNIAPP_ROOT . '/mod/forum/forumStructure.class.php');
require_once(UNIAPP_ROOT . '/mod/forum/discussionStructure.class.php');
require_once(UNIAPP_ROOT . '/mod/forum/postStructure.class.php');
require_once(UNIAPP_ROOT . '/mod/forum/db/forumDB.class.php');

class local_uniappws_forum extends uniapp_external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_forum_by_id_parameters() {
        return new external_function_parameters (
            array(
                'forumid' => new external_value(PARAM_INT,  'A forum Id ', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
            )
        );
    }

    /**
     * Returns a forum
     *
     * @param int forumid
     * @return array A forum
     */
    public static function get_forum_by_id($forumid) {
		global $DB;
		
        if (!$cm = get_coursemodule_from_instance('forum', $forumid)) {
            throw new moodle_exception('forum:notfound', 'local_uniappws', '', '');
        }

        $context = get_context_instance(CONTEXT_COURSE, $cm->course);

        self::validate_context($context);

        $viewhidden = false;
        if (has_capability('moodle/course:viewhiddenactivities', $context)) {
            $viewhidden = true;
        }

        $forum = forum_db::get_forum_by_id($forumid, $viewhidden);
        $return = new ForumStructure($forum);
        $return = $return->get_data();

        return $return;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_forum_by_id_returns() {
        return ForumStructure::get_class_structure();
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_forums_by_courseid_parameters() {
        return new external_function_parameters(
            array(
                'courseid'  => new external_value(PARAM_INT, 'course ID', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'startpage' => new external_value(PARAM_INT, 'start page', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                'n'         => new external_value(PARAM_INT, 'page number', VALUE_DEFAULT, 10, NULL_NOT_ALLOWED)
            )
        );
    }

    /**
     * Returns a list of n forums starting from page startpage
     *
     * @param int courseid
     * @param int startpage
     * @param int n
     *
     * @return array of forum
     */
    public static function get_forums_by_courseid($courseid, $startpage, $n) {
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        self::validate_context($context);

        $viewhidden = false;
        if(has_capability('moodle/course:viewhiddenactivities', $context)) {
            $viewhidden = true;
        }

        $forums = forum_db::get_forums_by_courseid($courseid, $viewhidden, $context, $startpage, $n);

        $returnforums = array();
        foreach ($forums as $forum) {
            $forum = new ForumStructure($forum);
            $returnforums[] = $forum->get_data();
        }
        return $returnforums;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_forums_by_courseid_returns() {
        return
            new external_multiple_structure(
                ForumStructure::get_class_structure()
            );
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_forums_by_userid_parameters() {
        return new external_function_parameters(
            array(
                'userid'    => new external_value(PARAM_INT, 'user ID', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'startpage' => new external_value(PARAM_INT, 'start page', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                'n'         => new external_value(PARAM_INT, 'page number', VALUE_DEFAULT, 10, NULL_NOT_ALLOWED)
            )
        );
    }

    /**
     * Returns a forum
     *
     * @param int userid
     * @param int startpage
     * @param int n
     *
     * @return array of forum
     */
    public static function get_forums_by_userid($userid, $startpage, $n) {
		global $DB;
		$USER = $DB->get_record('user', array('id'=>$userid) );
		if( !isset($USER) or empty($USER) ) {
			throw new moodle_exception('user:notfound', 'local_uniappws', '', '');
		}
		$courses = enrol_get_my_courses(array('timemodified'), 'fullname');

        $returnforums = array();
        $viewable = array();
        foreach ($courses as $course) {
            $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
            self::validate_context($coursecontext);

            $courseforums = forum_db::get_forums_by_courseid($course->id, true, $coursecontext, 0, 0);

            foreach ($courseforums as $forum) {
                if (!$cm = get_coursemodule_from_instance('forum', $forum->id)) {
                    throw new moodle_exception('forum:notfound', 'local_uniappws', '', '');
                }

                $forumcontext = get_context_instance(CONTEXT_MODULE, $cm->id);

                self::validate_context($forumcontext);
                if ((($course->visible != 1) && (!has_capability('moodle/course:viewhiddencourses', $coursecontext))) ||
                    (($cm->visible != 1) && (!has_capability('moodle/course:viewhiddenactivities', $forumcontext)))) {
                    continue;
                } else {
                    $viewable[] = $forum->id;
                }
            }
        }

        $forums = forum_db::get_forums_by_userid($userid, $viewable, $startpage, $n);

        $returnforums = array();
        foreach ($forums as $forum) {
            $forum = new ForumStructure($forum);
            $returnforums[] = $forum->get_data();
        }
        return $returnforums;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_forums_by_userid_returns() {
        return
            new external_multiple_structure(
                ForumStructure::get_class_structure()
            );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_forum_discussions_parameters() {
        return new external_function_parameters(
            array(
                'forumid'   => new external_value(PARAM_INT, 'forum ID', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'startpage' => new external_value(PARAM_INT, 'start page', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                'n'         => new external_value(PARAM_INT, 'page number', VALUE_DEFAULT, 10, NULL_NOT_ALLOWED)
            )
        );
    }

    /**
     * Returns a list of n discussions of a forum starting from page n
     *
     * @param int forumid
     * @param int startpage
     * @param int n
     *
     * @return array of discussions
     */
    public static function get_forum_discussions($forumid, $startpage, $n) {
        global $CFG;
        require_once($CFG->dirroot.'/mod/forum/lib.php');   // We'll need this

        if (!$cm = get_coursemodule_from_instance('forum', $forumid)) {
            throw new moodle_exception('forum:notfound', 'local_uniappws', '', '');
        }

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        self::validate_context($context);

        require_capability('mod/forum:viewdiscussion', $context);

        $viewhidden = false;
        if(has_capability('moodle/course:viewhiddenactivities', $context)) {
            $viewhidden = true;
        }

        $sort = "d.timemodified DESC";

        $discussions = forum_db::get_forum_discussions($forumid, $viewhidden, $sort, $startpage, $n);
		
        $returndiscussions = array();
        foreach ($discussions as $discussion) {
            $discussion = new DiscussionStructure($discussion);
            $returndiscussions[] = $discussion->get_data();
        }
        return $returndiscussions;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_forum_discussions_returns() {
        return
            new external_multiple_structure(
                DiscussionStructure::get_class_structure()
            );
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_discussion_by_id_parameters() {
        return new external_function_parameters (
            array(
                'discid' => new external_value(PARAM_INT,  'A discussion Id ', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }

    /**
     * Returns a discussion
     *
     * @param int discid
     *
     * @return discussion
     */
    public static function get_discussion_by_id($discid) {
//        $params = self::validate_parameters(self::get_discussion_by_id_parameters(), array('discid' => $parameters));

        $forum = forum_db::get_forum_by_discussion_id($discid);
        if (!$cm = get_coursemodule_from_instance('forum', $forum->id)) {
            throw new moodle_exception('forum:notfound', 'local_uniappws', '', '');
        }
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        self::validate_context($context);
        require_capability('mod/forum:viewdiscussion', $context);

        $discussion = forum_db::get_discussion_by_id($discid);
        $return = new DiscussionStructure($discussion);
        $return = $return->get_data();
        return $return;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_discussion_by_id_returns() {
        return DiscussionStructure::get_class_structure();
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_forum_by_discussion_id_parameters() {
        return new external_function_parameters (
            array(
                'discid' => new external_value(PARAM_INT,  'A discussion Id ', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }

    /**
     * Returns the forum in which the discussion belongs
     *
     * @param int discid
     *
     * @return forum
     */
    public static function get_forum_by_discussion_id($discid) {
//        $params = self::validate_parameters(self::get_forum_by_discussion_id_parameters(), array('discid' => $parameters));

        $forum = forum_db::get_forum_by_discussion_id($discid);

        if (!$cm = get_coursemodule_from_instance('forum', $forum->id)) {
            throw new moodle_exception('forum:notfound', 'local_uniappws', '', '');
        }
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        self::validate_context($context);

        $return = new ForumStructure($forum);
        $return = $return->get_data();
        return $return;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_forum_by_discussion_id_returns() {
        return ForumStructure::get_class_structure();
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_forum_by_postid_parameters() {
        return new external_function_parameters (
            array(
                'postid' => new external_value(PARAM_INT,  'A post Id ', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }

    /**
     * Returns the forum in which the post belongs
     *
     * @param int postid
     *
     * @return forum
     */
    public static function get_forum_by_postid($postid) {
//        $params = self::validate_parameters(self::get_forum_by_post_id_parameters(), array('postid' => $parameters));

        $forum = forum_db::get_forum_by_postid($postid);

        if (!$cm = get_coursemodule_from_instance('forum', $forum->id)) {
            throw new moodle_exception('forum:notfound', 'local_uniappws', '', '');
        }
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        self::validate_context($context);

        $return = new ForumStructure($forum);
        $return = $return->get_data();
        return $return;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_forum_by_postid_returns() {
        return ForumStructure::get_class_structure();
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function create_discussion_parameters() {
        return new external_function_parameters (
            array(
                'discussion' =>  new external_single_structure (
                        array(
                            'forumid'     => new external_value(PARAM_INT,  'The Id of a forum instance in a course', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                            'name'        => new external_value(PARAM_TEXT, 'The name/subject of discussion', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                            'intro'       => new external_value(PARAM_RAW,  'The introduction/message of discussion', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                            'groupid'     => new external_value(PARAM_INT,  'The id of the group the discussion belongs to', VALUE_DEFAULT, -1, NULL_NOT_ALLOWED),
                            'attachments' => new external_value(PARAM_TEXT, 'Attachments', VALUE_DEFAULT, null, NULL_ALLOWED),
                            'format'      => new external_value(PARAM_INT,  'Format', VALUE_DEFAULT, 1, NULL_NOT_ALLOWED),
                            'mailnow'     => new external_value(PARAM_BOOL, 'Mail now', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED)
                        )
                    )
            )
        );
    }

    /**
     * Given an object containing all the necessary data,
     * create a new discussion and return the id
     *
     * @param Discussion $discussion  A discussion object to create
     *
     * @return array An array of arrays
     */
    public static function create_discussion($discussion) {
        global $CFG;
        require_once("$CFG->dirroot/mod/forum/lib.php");

//        $params = self::validate_parameters(self::create_discussion_parameters(), array('discussion' => $discussion));
//        $params = $params['discussion'];

        if (!$cm = get_coursemodule_from_instance('forum', $discussion['forumid'])) {
            throw new moodle_exception('forum:notfound', 'local_uniappws', '', '');
        }
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        self::validate_context($context);

        $canviewhidden = false;
        if (has_capability('moodle/course:viewhiddenactivities', get_context_instance(CONTEXT_COURSE, $cm->course))) {
            $canviewhidden = true;
        }
        require_capability('mod/forum:startdiscussion', $context);

        $result = forum_db::create_discussion($discussion, $canviewhidden);

        return $result;
    }

	/**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function create_discussion_returns() {
        return new external_single_structure(
            array(
                'discid' => new external_value(PARAM_INT, 'The id of the created discussion', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'postid' => new external_value(PARAM_INT, 'The id of the first post', VALUE_REQUIRED, '', NULL_NOT_ALLOWED)
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function delete_discussion_parameters() {
        return new external_function_parameters (
            array(
                'discid' => new external_value(PARAM_INT,  'A discussion Id ', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }

    /**
     * Returns a discussion
     *
     * @param int discid
     *
     * @return bool success
     */
    public static function delete_discussion($discid) {
        global $CFG;

//        $params = self::validate_parameters(self::delete_discussion_parameters(), array('discid' => $discid));

        $forum = self::get_forum_by_discussion_id($discid);
        if (!$cm = get_coursemodule_from_instance('forum', $forum['id'])) {
            throw new moodle_exception('forum:notfound', 'local_uniappws', '', '');
        }
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        self::validate_context($context);
        require_capability('mod/forum:deleteanypost', $context);

        $result = forum_db::delete_discussion($discid);

		if($result == true){
        	return array("success" => true);
		} else {
        	return array("success" => false);
		}
    }

	/**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function delete_discussion_returns() {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'The result of the "delete discussion" operation', VALUE_REQUIRED, '', NULL_NOT_ALLOWED)
            )
        );
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_posts_by_discussion_id_parameters() {
        return new external_function_parameters(
            array(
                'discid'    => new external_value(PARAM_INT, 'discussion ID', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'startpage' => new external_value(PARAM_INT, 'start page', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                'n'         => new external_value(PARAM_INT, 'page number', VALUE_DEFAULT, 10, NULL_NOT_ALLOWED)
            )
        );
    }

    /**
     * Returns n posts of a discussion starting from page startpage
     *
     * @param int discid
     * @param int startpage
     * @param int n
     *
     * @return array of posts
     */
    public static function get_posts_by_discussion_id($discid, $startpage, $n){
        global $CFG, $DB;

//        $params = self::validate_parameters(self::get_posts_by_discussion_id_parameters(), array('params' => $parameters));
//        $params = $params['params'];

        $forum = self::get_forum_by_discussion_id($discid);
        if (!$cm = get_coursemodule_from_instance('forum', $forum['id'])) {
            throw new moodle_exception('forum:notfound', 'local_uniappws', '', '');
        }
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        self::validate_context($context);

        $posts = forum_db::get_posts_by_discussion_id($discid, $forum, $context, $startpage, $n);

        $returnposts = array();
        foreach ($posts as $post) {
            $post = new PostStructure($post);
			$returnposts[] = $post->get_data();	
        }
		
        return $returnposts;
    }

	/**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_posts_by_discussion_id_returns() {
        return
            new external_multiple_structure(
                PostStructure::get_class_structure()
            );
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function create_post_parameters() {
        return new external_function_parameters (
            array(
                'parentid'   => new external_value(PARAM_INT,  'The parent post ID', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'subject'    => new external_value(PARAM_TEXT, 'The subject of the post', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'message'    => new external_value(PARAM_TEXT, 'Message of the post', VALUE_REQUIRED, '', NULL_NOT_ALLOWED)
            )
        );
    }

    /**
     * Creates a new post in a discussion
     *
     * @param int parentid
     * @param text subject
     * @param text message
     *
     * @return int postid
     */
    public static function create_post($parentid, $subject, $message){
        global $CFG;
        require_once("$CFG->dirroot/lib/filelib.php");

//        $params = self::validate_parameters(self::create_post_parameters(), array('params' => $parameters));
//        $params = $params['params'];

        $discussion = forum_db::get_discussion_by_post_id($parentid);
        $forum = forum_db::get_forum_by_discussion_id($discussion->id);
        if (!$cm = get_coursemodule_from_instance('forum', $forum->id)) {
            throw new moodle_exception('forum:notfound', 'local_uniappws', '', '');
        }
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        self::validate_context($context);

        $post = new StdClass();
        $post->discussion = $discussion->id;
        $post->parent     = $parentid;
        $post->subject    = $subject;
        $post->message    = $message;
        $post->itemid     = file_get_submitted_draft_itemid('message');

        $result = forum_db::create_post($post, $forum);
        return array('postid' => $result);
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function create_post_returns() {
        return new external_single_structure(
            array(
                'postid' => new external_value(PARAM_INT, 'The id of the newly created post', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function update_post_parameters() {
        return new external_function_parameters (
            array(
                'post' => PostStructure::get_class_structure()
            )
        );
    }

    /**
     * Updates a post. Only updates the "subject" and "message" fields, while ignoring
     * all other parameters passed.
     *
     * @param int postid
     * @param text subject
     * @param text message
     *
     * @return void
     */
    public static function update_post($post) {
        global $CFG, $USER;

        $forum = forum_db::get_forum_by_postid($post['id']);
        if (!$cm = get_coursemodule_from_instance('forum', $forum->id)) {
            throw new moodle_exception('forum:notfound', 'local_uniappws', '', '');
        }
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        self::validate_context($context);

        $caneditpost = false;
        if (has_capability('mod/forum:editanypost', $context) || ($USER->id == $post['userid'])) {
            $caneditpost = true;
        }

        $result = forum_db::update_post($post['id'], $post['subject'], $post['message'], $cm, $caneditpost);
        return array('success' => $result);
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function update_post_returns() {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'The result of the "Update Post" operation', VALUE_REQUIRED, '', NULL_NOT_ALLOWED)
            )
        );
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function delete_post_parameters() {
        return new external_function_parameters (
            array(
                'postid' => new external_value(PARAM_INT,  'The post identifier', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }

    /**
     * Deletes a post
     *
     * @param int postid
     *
     * @return bool success
     */
    public static function delete_post($postid) {
        global $CFG, $USER;
        require_once("$CFG->dirroot/mod/forum/lib.php");

//        $params = self::validate_parameters(self::delete_post_parameters(), array('postid' => $postid));

        $forum = forum_db::get_forum_by_postid($postid);
        if (!$cm = get_coursemodule_from_instance('forum', $forum->id)) {
            throw new moodle_exception('forum:notfound', 'local_uniappws', '', '');
        }
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        self::validate_context($context);

        $candeleteownpost = false;
        if (has_capability('mod/forum:deleteownpost', $context)) {
            $candeleteownpost = true;
        }

        $candeleteallposts = false;
        if (has_capability('mod/forum:deleteanypost', $context)) {
            $candeleteallposts = true;
        }

        $result = forum_db::delete_post($postid, $candeleteownpost, $candeleteallposts);
        return array('success' => $result);
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function delete_post_returns() {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'The result of the "Delete Post" operation', VALUE_REQUIRED, '', NULL_NOT_ALLOWED)
            )
        );
    }
}

?>
