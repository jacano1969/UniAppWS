<?php

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
		'local_uniappws_course_get_course_modules_count' => array(
				'classname'   => 'local_uniappws_course',
				'methodname'  => 'get_course_modules_count',
				'classpath'   => 'local/uniappws/course/externallib.php',
				'description' => 'Returns the the number of course modules for every courseid passed; required parameters: courseid (array of ids)',
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

		// group
		'local_uniappws_group_get_group_by_id'=> array(
			'classname'   => 'local_uniappws_group',
			'methodname'  => 'get_group_by_groupid',
			'classpath'   => 'local/uniappws/group/externallib.php',
			'description' => 'Returns a group; required parameters: groupid',
			'type'		=> 'read',
			'capabilities'=> '',
		),

		'local_uniappws_group_get_users_by_groupid'=> array(
			'classname'   => 'local_uniappws_group',
			'methodname'  => 'get_group_members_by_groupid',
			'classpath'   => 'local/uniappws/group/externallib.php',
			'description' => 'Returns the group members; required parameters: groupid; optional parameters: startpage, n (page number)',
			'type'		=> 'read',
			'capabilities'=> '',
		),

		'local_uniappws_group_get_users_by_groupingid'=> array(
			'classname'   => 'local_uniappws_group',
			'methodname'  => 'get_group_members_by_groupingid',
			'classpath'   => 'local/uniappws/group/externallib.php',
			'description' => 'Returns the group members; required parameters: groupingid; optional parameters: startpage, n (page number)',
			'type'		=> 'read',
			'capabilities'=> '',
		),

		'local_uniappws_group_get_groups_by_courseid'=> array(
			'classname'   => 'local_uniappws_group',
			'methodname'  => 'get_groups_by_courseid',
			'classpath'   => 'local/uniappws/group/externallib.php',
			'description' => 'Returns the groups of a course; required parameters: courseid; optional parameters: startpage, n (page number)',
			'type'		=> 'read',
			'capabilities'=> '',
		),

		'local_uniappws_group_get_groups_by_groupingid'=> array(
			'classname'   => 'local_uniappws_group',
			'methodname'  => 'get_groups_by_groupingid',
			'classpath'   => 'local/uniappws/group/externallib.php',
			'description' => 'Returns the groups of a course; required parameters: groupingid; optional parameters: startpage, n (page number)',
			'type'		=> 'read',
			'capabilities'=> '',
		),

		'local_uniappws_group_get_user_course_groups'=> array(
			'classname'   => 'local_uniappws_group',
			'methodname'  => 'get_groups_by_courseid_and_userid',
			'classpath'   => 'local/uniappws/group/externallib.php',
			'description' => 'Returns the groups of a course; required parameters: courseid, userid; optional parameters: startpage, n (page number)',
			'type'		=> 'read',
			'capabilities'=> '',
		),

		'local_uniappws_group_get_groupings_by_courseid'=> array(
			'classname'   => 'local_uniappws_group',
			'methodname'  => 'get_groupings_by_courseid',
			'classpath'   => 'local/uniappws/group/externallib.php',
			'description' => 'Returns the groupings of a course; required parameters: courseid; optional parameters: startpage, n (page number)',
			'type'		=> 'read',
			'capabilities'=> '',
		),

		'local_uniappws_group_get_user_course_groupings'=> array(
			'classname'   => 'local_uniappws_group',
			'methodname'  => 'get_groupings_by_courseid_and_userid',
			'classpath'   => 'local/uniappws/group/externallib.php',
			'description' => 'Returns the groupings of a course; required parameters: courseid, userid; optional parameters: startpage, n (page number)',
			'type'		=> 'read',
			'capabilities'=> '',
		),

		// resources
		'local_uniappws_resource_get_resource' => array(
			'classname'   => 'local_uniappws_resource',
			'methodname'  => 'get_resource',
			'classpath'   => 'local/uniappws/mod/resource/externallib.php',
			'description' => 'Gets a resource (File resource type); required parameters: resourceid',
			'type'		=> 'read',
			'capabilities'=> '',
		),
		'local_uniappws_files_upload' => array(
			'classname'   => 'local_uniappws_files',
			'methodname'  => 'upload_file',
			'classpath'   => 'local/uniappws/files/externallib.php',
			'description' => 'Uploads a file',
			'type'		=> 'write',
			'capabilities'=> '',
		),

		'local_uniappws_files_get_file_url' => array(
			'classname'   => 'local_uniappws_files',
			'methodname'  => 'get_file_url',
			'classpath'   => 'local/uniappws/files/externallib.php',
			'description' => 'Returns the URL of a file',
			'type'		=> 'read',
			'capabilities'=> '',
		),

		'local_uniappws_files_get_user_filesinfo' => array(
			'classname'   => 'local_uniappws_files',
			'methodname'  => 'get_user_filesinfo',
			'classpath'   => 'local/uniappws/files/externallib.php',
			'description' => 'Gets name and id of user files',
			'type'		=> 'read',
			'capabilities'=> '',
		),

		// assignment
		'local_uniappws_assign_get_assignments_by_courseid' => array(
			'classname'   => 'local_uniappws_assignment',
			'methodname'  => 'get_assignments_by_courseid',
			'classpath'   => 'local/uniappws/mod/assignment/externallib.php',
			'description' => 'Gets course assignments; required parameters: courseid; optional parameters: startpage, n (page number)',
			'type'        => 'read',
			'capabilities'=> 'mod/assignment:view',
		),

		'local_uniappws_assign_get_assignment_by_id' => array(
			'classname'   => 'local_uniappws_assignment',
			'methodname'  => 'get_assignment_by_assigid',
			'classpath'   => 'local/uniappws/mod/assignment/externallib.php',
			'description' => 'Gets an assignment by its id; required parameters: assigid',
			'type'        => 'read',
			'capabilities'=> 'mod/assignment:view',
		),

		'local_uniappws_assign_get_submission_by_assignid' => array(
			'classname'   => 'local_uniappws_assignment',
			'methodname'  => 'get_submission_by_assigid',
			'classpath'   => 'local/uniappws/mod/assignment/externallib.php',
			'description' => 'Gets a submission; required parameters: assigid',
			'type'        => 'read',
			'capabilities'=> '',
		),

		'local_uniappws_assign_get_submission_files' => array(
			'classname'   => 'local_uniappws_assignment',
			'methodname'  => 'get_submission_files',
			'classpath'   => 'local/uniappws/mod/assignment/externallib.php',
			'description' => 'Gets submission files; required parameters: assigid; optional parameters: startpage, n (page number)',
			'type'        => 'read',
			'capabilities'=> '',
		),

		'local_uniappws_assign_submit_online' => array(
			'classname'   => 'local_uniappws_assignment',
			'methodname'  => 'submit_online_assignment',
			'classpath'   => 'local/uniappws/mod/assignment/externallib.php',
			'description' => 'Submits an online assignment; required parameters: assigid, data',
			'type'        => 'write',
			'capabilities'=> 'mod/assignment:submit',
		),

		'local_uniappws_assign_submit_singleupload' => array(
			'classname'   => 'local_uniappws_assignment',
			'methodname'  => 'submit_singleupload_assignment',
			'classpath'   => 'local/uniappws/mod/assignment/externallib.php',
			'description' => 'Submits a singleupload assignment; required parameters: courseid, assigid, fileid',
			'type'        => 'write',
			'capabilities'=> 'mod/assignment:submit',
		),

		'local_uniappws_assign_submit_upload' => array(
			'classname'   => 'local_uniappws_assignment',
			'methodname'  => 'submit_upload_assignment',
			'classpath'   => 'local/uniappws/mod/assignment/externallib.php',
			'description' => 'Submits an upload assignment; required parameters: assigid, isfinal(boolean value, if true it is final submission otherwise is draft), files(array of fileids)',
			'type'        => 'write',
			'capabilities'=> 'mod/assignment:submit',
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

?>
