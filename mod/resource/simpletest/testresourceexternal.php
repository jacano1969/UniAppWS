<?php
// This file is part of Moodbile -- http://moodbile.org
//
// Moodbile is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodbile is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodbile.  If not, see <http://www.gnu.org/licenses/>.

/**
 * User External Test
 * Patched and implemented by Goran Josic
 *
 * @package MoodbileServer
 * @subpackage User
 * @copyright 2010 Maria José Casañ, Marc Alier, Jordi Piguillem, Nikolas Galanis marc.alier@upc.edu
 * @copyright 2010 Universitat Politecnica de Catalunya - Barcelona Tech http://www.upc.edu
 *
 * @author Jordi Piguillem
 * @author Nikolas Galanis
 * @author Oscar Martinez Llobet
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(dirname(__FILE__).'/../../../config.php');
require_once(UNIAPP_ROOT . '/mod/resource/resource.class.php');
require_once(UNIAPP_ROOT . '/mod/resource/externallib.php');
require_once(UNIAPP_ROOT . '/course/test/CourseStructure.class.php');


class resourceexternal_test extends UnitTestCase {

	public $CourseStructure;

	function setUp() {
			$this->CourseStructure = new CourseStructure();
	}

	function tearDown() {
	}

    public function test_get_resource() {
		$resourceid = $this->CourseStructure->resource->id;
		$resource = local_uniappws_resource::get_resource($resourceid);

		$struct = Resource::get_class_structure();
        $this->assertEqual(sizeof($resource), sizeof($struct->keys), "Size is: ".sizeof($resource));

		foreach($this->CourseStructure->resource as $key => $value) {
        	$this->assertEqual($resource[$key], $value, "$key matches");
		}
	}
}
