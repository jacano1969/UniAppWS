<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(dirname(__FILE__).'/../../../config.php');
require_once(UNIAPP_ROOT . '/mod/resource/resource.class.php');
require_once(UNIAPP_ROOT . '/mod/resource/externallib.php');
require_once(UNIAPP_ROOT . '/course/test/TestCourseStructure.class.php');


class resourceexternal_test extends UnitTestCase {

	public $TestCourseStructure;

	function setUp() {
			$this->TestCourseStructure = new TestCourseStructure();
	}

	function tearDown() {
	}

    public function test_get_resource() {
		$resourceid = $this->TestCourseStructure->resource->id;
		$resource = local_uniappws_resource::get_resource($resourceid);

		$struct = Resource::get_class_structure();
        $this->assertEqual(sizeof($resource), sizeof($struct->keys), "Size is: ".sizeof($resource));

		foreach($this->TestCourseStructure->resource as $key => $value) {
        	$this->assertEqual($resource[$key], $value, "$key matches");
		}
	}
}

?>
