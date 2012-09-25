<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(dirname(__FILE__).'/../../../config.php');
require_once(UNIAPP_ROOT . '/mod/folder/folder.class.php');
require_once(UNIAPP_ROOT . '/mod/folder/externallib.php');
//require_once(UNIAPP_ROOT . '/course/test/TestCourseStructure.class.php');


class folderexternal_test extends UnitTestCase {

	public $TestCourseStructure;

	function setUp() {
			//$this->TestCourseStructure = new TestCourseStructure();
	}

	function tearDown() {
	}

    public function test_get_folder() {
		//$folderid = $this->TestCourseStructure->folder->id;
		$folderid = 1;
		$folder = local_uniappws_folder::get_folder($folderid);

		print_r($folder);
	
		/*
		$struct = Folder::get_class_structure();
        $this->assertEqual(sizeof($folder), sizeof($struct->keys), "Size is: ".sizeof($folder));

		foreach($this->TestCourseStructure->folder as $key => $value) {
        	$this->assertEqual($folder[$key], $value, "$key matches");
		}
		*/
	}
}

?>
