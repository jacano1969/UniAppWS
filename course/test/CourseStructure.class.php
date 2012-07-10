<?php
	
class CourseStructure {
	
	public $user = array();		
	public $test_user;

	public $group = array();
	public $test_group;
	public $test_user_group_number;
	public $test_user_group = array();
	public $test_group_members_number;

	public $grouping = array();
	public $test_grouping;

	public $course = array();		
	public $test_course;

	public $forum = array();		
	public $test_forum;

	public $forum_discussion = array();
	public $test_forum_discussion;

	public $resource;

	
	function __construct() {
		$this->user = array();
		$this->user[0] = new StdClass();

		$this->user[0]->id = 3;
		$this->user[0]->username = "goran.teacher";
		$this->user[0]->idnumber = null;
		$this->user[0]->firstname = "Goran";
		$this->user[0]->lastname = "Teacher";
		$this->user[0]->email = "goran.josic@usi.ch";
		$this->user[0]->city = "Lugano";
		$this->user[0]->country = "CH";
		$this->user[0]->lang = "en";
		$this->user[0]->timemodified = 1308576789;

		$this->user[1] = new StdClass();
		$this->user[1]->id = 4;
		$this->user[1]->username = 'goran.student';
		$this->user[1]->idnumber = null;
		$this->user[1]->firstname = "Goran";
		$this->user[1]->lastname = "Teacher";
		$this->user[1]->email = "josicg@usi.ch";
		$this->user[1]->city = "Lugano";
		$this->user[1]->country = "CH";
		$this->user[1]->lang = "en";
		$this->user[1]->timemodified = 1335792013;
		
		$this->user[2] = new StdClass();
		$this->user[2]->id = 7;
		$this->user[2]->username = "kyoko.student";
		$this->user[2]->idnumber = "";
		$this->user[2]->firstname = "Kyoko";
		$this->user[2]->lastname = "Otonashi";
		$this->user[2]->email = "kyoko.otonashi@ikkoku.jp";
		$this->user[2]->city = "Tokyo";
		$this->user[2]->country = "JP";
		$this->user[2]->lang = "en";
		$this->user[2]->timemodified = 1341480254;
		// this user is used for testing
		$this->test_user = $this->user[2];

		$this->group = array();
		$this->group[0] = new StdClass();
		$this->group[0]->id = 1;
		$this->group[0]->name = 'students';
		$this->group[0]->description = 'This group contains all students';

		$this->group[1] = new StdClass();
		$this->group[1]->id = 2;
		$this->group[1]->name = 'males';
		$this->group[1]->description = 'This group contains only males';

		$this->group[2] = new StdClass();
		$this->group[2]->id = 3;
		$this->group[2]->name = 'females';
		$this->group[2]->description = 'This group contains only females';
		// this group is used for testing
		$this->test_group = $this->group[0];
		$this->test_group_members_number = 2;
		$this->test_user_group = array(0,2); // students and females

		$this->grouping = array();
		$this->grouping[0] = new StdClass();
		$this->grouping[0]->id = 1;
		$this->grouping[0]->name = 'humans';
		$this->grouping[0]->description = 'This is the group of humans made of males and females';
		$this->grouping[0]->groups = array($this->group[1], $this->group[2]); // males and females
		$this->test_grouping = $this->grouping[0];
		
		$this->course = array();
		$this->course[0] = new StdClass();
		$this->course[0]->id = 2;
		$this->course[0]->idnumber = null;
		$this->course[0]->category = 1;
		$this->course[0]->fullname = "Moodle Notifications Plugin Developing Course";
		$this->course[0]->shortname = "moodle_notifications";
		$this->course[0]->summary = "";
		$this->course[0]->format = "topics";
		$this->course[0]->startdate = 1308607200;
		$this->course[0]->groupmode = 0;
		$this->course[0]->lang = "en";
		$this->course[0]->timecreated = 1308576891;
		$this->course[0]->timemodified = 1339575505;
		$this->course[0]->showgrades = null;

		$this->course[1] = new StdClass();
		$this->course[1]->id = 3;
		$this->course[1]->idnumber = null;
		$this->course[1]->category = 1;
		$this->course[1]->fullname = "Academic integrity";
		$this->course[1]->shortname = "academic_integrity";
		$this->course[1]->summary = "<p>Academic Integrity - certificate developing course</p>";
		$this->course[1]->format = "topics";
		$this->course[1]->startdate = 1315864800;
		$this->course[1]->groupmode = 0;
		$this->course[1]->lang = "";
		$this->course[1]->timecreated = 1315833784;
		$this->course[1]->timemodified = 1328108140;
		$this->course[1]->showgrades = null;

		$this->course[2] = new StdClass();
		$this->course[2]->id = 7;
		$this->course[2]->idnumber = null;
		$this->course[2]->category = 1;
		$this->course[2]->fullname = "Test Course";
		$this->course[2]->shortname = "test_course";
		$this->course[2]->summary = "This course is used for testing purposes";
		$this->course[2]->format = "topics";
		$this->course[2]->startdate = 1341525600;
		$this->course[2]->groupmode = 0;
		$this->course[2]->lang = "";
		$this->course[2]->timecreated = 1341478763;
		$this->course[2]->timemodified = 1341479121;
		$this->course[2]->showgrades = null;
		// this course is used for testing
		$this->test_course = $this->course[2];

		$this->forum[0] = new StdClass();
		$this->forum[0]->id = 10;
		$this->forum[0]->course = $this->test_course->id;
		$this->forum[0]->type = "news";
		$this->forum[0]->name = "News forum";
		$this->forum[0]->intro = "General news and announcements";
		$this->forum[0]->timemodified = 1341478869;

		$this->forum[1] = new StdClass();
		$this->forum[1]->id = 11;
		$this->forum[1]->course = $this->test_course->id;
		$this->forum[1]->type = "general";
		$this->forum[1]->name = "Nippon";
		$this->forum[1]->intro = "<p>Talk about Nippon here.</p>";
		$this->forum[1]->timemodified = 1341479984;
		// this forum is used for testing
		$this->test_forum = $this->forum[1];

		$this->forum_discussion[0] = new StdClass();
		$this->forum_discussion[0]->id = 3;
		$this->forum_discussion[0]->forum = $this->test_forum->id;
		$this->forum_discussion[0]->name = "Visit to Kyoto";
		$this->forum_discussion[0]->firstpost = 22;
		$this->forum_discussion[0]->userid = 3;
		$this->forum_discussion[0]->groupid = -1;
		$this->forum_discussion[0]->timemodified = 1341481797;

		$this->forum_discussion[1] = new StdClass();
		$this->forum_discussion[1]->id = 4;
		$this->forum_discussion[1]->forum = $this->test_forum->id;
		$this->forum_discussion[1]->name = "Visit to Tokyo";
		$this->forum_discussion[1]->firstpost = 23;
		$this->forum_discussion[1]->userid = 3;
		$this->forum_discussion[1]->groupid = -1;
		$this->forum_discussion[1]->timemodified = 134148008;
		// this discussion is used for testing
		$this->test_forum_discussion = $this->forum_discussion[0];

		$this->resource = new StdClass();
		$this->resource->id = 150;
		$this->resource->course = 7;
		$this->resource->name = 'Magic Kyoto';
		$this->resource->intro = 'Bamboo way in one of the beautiful parks of Kyoto';
		$this->resource->timemodified = 1341928186;
		$this->resource->fileid = 329;
		$this->resource->filemimetype = 'image/jpeg';
		$this->resource->filesize = 393239;
	}
}
?>
