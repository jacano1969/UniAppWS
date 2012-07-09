<?php

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
 * Web service local plugin template external functions and service definitions.
 *
 * @package	localuniappws
 * @copyright  2012 Goran Josic
 * @license	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Web service functions to install.
$functions = array(
		// course
		'local_uniappws_course_get_course_modules' => array(
				'classname'   => 'local_uniappws_course',
				'methodname'  => 'get_course_modules',
				'classpath'   => 'local/uniappws/course/externallib.php',
				'description' => 'Returns the list of modules from the course; required parameters: courseid',
				'type'		=> 'read',
		),
		'local_uniappws_course_get_courses_by_userid' => array(
				'classname'   => 'local_uniappws_course',
				'methodname'  => 'get_courses_by_userid',
				'classpath'   => 'local/uniappws/course/externallib.php',
				'description' => 'Returns the list of courses given the userid; required parameters: userid; optional parameters: startpage, n (page number)',
				'type'		=> 'read',
		),
		
		// forum
		'local_uniappws_forum_get_forum_by_id' => array(
				'classname'   => 'local_uniappws_forum',
				'methodname'  => 'get_forum_by_id',
				'classpath'   => 'local/uniappws/mod/forum/externallib.php',
				'description' => 'Returns the forum given the id; required parameters: forumid',
				'type'		=> 'read',
		),
		'local_uniappws_forum_get_forums_by_courseid' => array(
				'classname'   => 'local_uniappws_forum',
				'methodname'  => 'get_forums_by_courseid',
				'classpath'   => 'local/uniappws/mod/forum/externallib.php',
				'description' => 'Returns the course forums; required parameters: courseid; optional parameters: startpage, n (page number)',
				'type'		=> 'read',
		),
		'local_uniappws_forum_get_forums_by_userid' => array(
				'classname'   => 'local_uniappws_forum',
				'methodname'  => 'get_forums_by_userid',
				'classpath'   => 'local/uniappws/mod/forum/externallib.php',
				'description' => 'Returns the forums the user is enroled in; required parameters: userid; optional parameters: startpage, n (page number)',
				'type'		=> 'read',
		),
		'local_uniappws_forum_get_forum_discussions' => array(
				'classname'   => 'local_uniappws_forum',
				'methodname'  => 'get_forum_discussions',
				'classpath'   => 'local/uniappws/mod/forum/externallib.php',
				'description' => 'Returns the forum discussions; required parameters: forumid; optional parameters: startpage, n (page number)',
				'type'		=> 'read',
		),
		'local_uniappws_forum_get_discussion_by_id' => array(
				'classname'   => 'local_uniappws_forum',
				'methodname'  => 'get_discussion_by_id',
				'classpath'   => 'local/uniappws/mod/forum/externallib.php',
				'description' => 'Returns the forum discussion given the id; required parameters: discid; optional parameters: startpage, n (page number)',
				'type'		=> 'read',
		),
		'local_uniappws_forum_get_forum_by_postid' => array(
				'classname'   => 'local_uniappws_forum',
				'methodname'  => 'get_forum_by_postid',
				'classpath'   => 'local/uniappws/mod/forum/externallib.php',
				'description' => 'Returns the forum given the post id; required parameters: postid; optional parameters: startpage, n (page number)',
				'type'		=> 'read',
		),
		'local_uniappws_forum_get_forum_by_discussionid' => array(
				'classname'   => 'local_uniappws_forum',
				'methodname'  => 'get_forum_by_discussion_id',
				'classpath'   => 'local/uniappws/mod/forum/externallib.php',
				'description' => 'Returns the forum given the discussion id; required parameters: discid; optional parameters: startpage, n (page number)',
				'type'		=> 'read',
		),
		'local_uniappws_forum_get_posts_by_discussionid' => array(
				'classname'   => 'local_uniappws_forum',
				'methodname'  => 'get_posts_by_discussion_id',
				'classpath'   => 'local/uniappws/mod/forum/externallib.php',
				'description' => 'Returns the forum posts given the discussion id; required parameters: discid; optional parameters: startpage, n (page number)',
				'type'		=> 'read',
		),
		'local_uniappws_forum_create_discussion' => array(
				'classname'   => 'local_uniappws_forum',
				'methodname'  => 'create_discussion',
				'classpath'   => 'local/uniappws/mod/forum/externallib.php',
				'description' => 'Creates a new discussion; required parameters: array discussion[forumid, name, intro]; optional parameters: discussion[groupid, attachments, format, mailnow]',
				'type'		=> 'read',
		),
		'local_uniappws_forum_delete_discussion' => array(
				'classname'   => 'local_uniappws_forum',
				'methodname'  => 'delete_discussion',
				'classpath'   => 'local/uniappws/mod/forum/externallib.php',
				'description' => 'Deletes a discussion given a discussion id; required parameters: discid',
				'type'		=> 'read',
		),
		'local_uniappws_forum_create_post' => array(
				'classname'   => 'local_uniappws_forum',
				'methodname'  => 'create_post',
				'classpath'   => 'local/uniappws/mod/forum/externallib.php',
				'description' => 'Creates a post ; required parameters: parentid, subject, message',
				'type'		=> 'read',
		),
		'local_uniappws_forum_update_post' => array(
				'classname'   => 'local_uniappws_forum',
				'methodname'  => 'update_post',
				'classpath'   => 'local/uniappws/mod/forum/externallib.php',
				'description' => 'Updates a post. Only updates the "subject" and "message" fields, while ignoring all other parameters passed; required parameters: post[id], post[subject], post[message], post[userid]; optional parameter: post[modified]',
				'type'		=> 'read',
		),
		'local_uniappws_forum_delete_post' => array(
				'classname'   => 'local_uniappws_forum',
				'methodname'  => 'delete_post',
				'classpath'   => 'local/uniappws/mod/forum/externallib.php',
				'description' => 'Deletes a post given a post id; required parameters: postid',
				'type'		=> 'read',
		),

		// user
		'local_uniappws_user_get_user' => array(
			'classname'   => 'local_uniappws_user',
			'methodname'  => 'get_user',
			'classpath'   => 'local/uniappws/user/externallib.php',
			'description' => 'Returns the details of the logged user; required parameters: no parameters',
			'type'		=> 'read',
			'capabilities'=> '',
		),

		'local_uniappws_user_get_user_by_id' => array(
			'classname'   => 'local_uniappws_user',
			'methodname'  => 'get_user_by_userid',
			'classpath'   => 'local/uniappws/user/externallib.php',
			'description' => 'Returns the details of a user; required parameters: userid',
			'type'		=> 'read',
			'capabilities'=> '',
		),

		'local_uniappws_user_get_user_by_username'=> array(
			'classname'   => 'local_uniappws_user',
			'methodname'  => 'get_user_by_username',
			'classpath'   => 'local/uniappws/user/externallib.php',
			'description' => 'Returns the details of a user; required parameters: username',
			'type'		=> 'read',
			'capabilities'=> '',
		),

		'local_uniappws_user_get_users_by_courseid'=> array(
			'classname'   => 'local_uniappws_user',
			'methodname'  => 'get_users_by_courseid',
			'classpath'   => 'local/uniappws/user/externallib.php',
			'description' => 'Returns the details of all users of a course; required parameters: courseid; optional parameters: startpage, n (page number)',
			'type'		=> 'read',
			'capabilities'=> '',
		),
);


$functionlist = array();
foreach ($functions as $key=>$value) {
    $functionlist[] = $key;
}

$services = array(
   'UniApp web services'  => array(
        'functions' => $functionlist,
        'enabled' => 0,
        'restrictedusers' => 0,
    ),
);

/*
$services = array(
		'UniApp web services' => array(
				'functions' => array (
					// course
					'local_uniappws_course_get_course_modules',
					'local_uniappws_course_get_courses_by_userid',
					// forum
					'local_uniappws_forum_get_forums_by_courseid',
					'local_uniappws_forum_get_forums_by_userid',
					'local_uniappws_forum_get_forum_by_id',
					'local_uniappws_forum_get_forum_discussions',
					'local_uniappws_forum_get_discussion_by_id',
					'local_uniappws_forum_get_forum_by_postid',
					'local_uniappws_forum_get_forum_by_discussionid',
					'local_uniappws_forum_get_posts_by_discussionid',
					'local_uniappws_forum_create_discussion',
					'local_uniappws_forum_delete_discussion',
					'local_uniappws_forum_create_post',
					'local_uniappws_forum_update_post',
					'local_uniappws_forum_delete_post'
					),
				'restrictedusers' => 0,
				'enabled'=>1,
		)
);
*/
