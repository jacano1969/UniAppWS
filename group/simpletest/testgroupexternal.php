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
 * Group External Funtions' Tests
 *
 * @package MoodbileServer
 * @subpackage Group
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

if(!defined('MOODLE_INTERNAL')) {
	die('Direct access to this script is forbidden.'); ///  It must be included from a Moodle page
}

require_once(dirname(__FILE__) . '/../../config.php');

require_once(UNIAPP_ROOT . '/group/grouping.class.php');
require_once(UNIAPP_ROOT . '/group/group.class.php');
require_once(UNIAPP_ROOT . '/user/user.class.php');
require_once(UNIAPP_ROOT . '/group/externallib.php');
require_once(UNIAPP_ROOT . '/course/test/CourseStructure.class.php');

global $DB;
class groupsexternal_test extends UnitTestCase {

	public $CourseStructure;

	function setUp() {
			$this->CourseStructure = new CourseStructure();
	}
	function tearDown() {
	}

	public function test_get_group_by_groupid_exception() {
		$groupid = -2;
		$this->expectException();
		$group = local_uniappws_group :: get_group_by_groupid($groupid);
	}

	public function test_get_group_by_groupid() {
		global $DB;

		$struct = Group :: get_class_structure();

		foreach($this->CourseStructure->group as $test_group) {
			$group = local_uniappws_group :: get_group_by_groupid( $test_group->id );
			$this->assertEqual(sizeof($struct->keys), sizeof((array) $group), 'Same size');

			foreach($struct->keys as $key => $value) {
				$this->assertEqual($test_group->$key, $group[$key], 'Same ' . $key . ' field');
			}
		}
	}

	/*public function test_get_group_members_by_groupid_exception() {
		$groupid = -2;
		$this->expectException();
		$group = local_uniappws_group :: get_group_members_by_groupid($groupid);
	}*/
	
	public function test_get_group_members_by_groupid() {
		$groupid = $this->CourseStructure->test_group->id; 
		$startpage = 0;
		$n = 5;
		$users = local_uniappws_group :: get_group_members_by_groupid($groupid, $startpage, $n);
		$this->assertEqual(sizeof($users), $this->CourseStructure->test_group_members_number, 'Correct number of members in the group');
	}
	
	public function test_get_group_members_by_groupingid() {
		$groupingid = $this->CourseStructure->test_grouping->id; 
		$startpage = 0;
		$n = 5;
		$users = local_uniappws_group :: get_group_members_by_groupingid($groupingid, $startpage, $n);
		$this->assertEqual(sizeof($users), sizeof($this->CourseStructure->user), 'Correct number of members in the grouping');
	}


	public function test_get_groups_by_courseid() {
		$courseid = $this->CourseStructure->test_course->id; 
		$startpage = 0;
		$n = 5;
		$groups = local_uniappws_group :: get_groups_by_courseid($courseid, $startpage, $n);
		$this->assertEqual(sizeof($groups), sizeof($this->CourseStructure->group), 'Correct number of groups');
		foreach($groups as $group) {
			$test_group = array();
			foreach( $this->CourseStructure->group as $n => $g) {
				if($g->id == $group['id'])	{
					$test_group = $g;	
					// assertions
					foreach($group as $key => $value) {
						$this->assertEqual($value, $test_group->$key, "Group name: ".$group['name']."; $key value matches");
					}
					break;
				}
			}
		}
	}
	
	public function test_get_groups_by_courseid_and_userid() {
		$courseid = $this->CourseStructure->test_course->id; 
		$userid = $this->CourseStructure->test_user->id; 
		$startpage = 0;
		$n = 5;
		$groups = local_uniappws_group :: get_groups_by_courseid_and_userid($courseid, $userid, $startpage, $n);
		$this->assertEqual(sizeof($groups), sizeof($this->CourseStructure->test_user_group), 'The number of groups the testing user is in is correct');

		foreach($groups as $group) {
			$test_group = array();
			foreach( $this->CourseStructure->test_user_group as $n) {
				if($this->CourseStructure->group[$n]->id == $group['id'])	{
					$test_group = $this->CourseStructure->group[$n];	
					// assertions
					foreach($group as $key => $value) {
						$this->assertEqual($value, $test_group->$key, "Group name: ".$group['name']."; $key value matches");
					}
					break;
				}
			}
		}
	}

	public function test_get_groupings_by_courseid() {
		$courseid = $this->CourseStructure->test_course->id; 
		$startpage = 0;
		$n = 5;
		$groupings = local_uniappws_group :: get_groupings_by_courseid($courseid, $startpage, $n);
		$this->assertEqual(sizeof($groupings), sizeof($this->CourseStructure->grouping), 'Correct number of groupings');

		foreach($groupings as $grouping) {
			$test_grouping = array();
			foreach( $this->CourseStructure->grouping as $g) {
				if($g->id == $grouping['id'])	{
					$test_grouping = $g;	
					// assertions
					foreach($grouping as $key => $value) {
						$this->assertEqual($value, $test_grouping->$key, "Grouping name: ".$grouping['name']."; $key value matches");
					}
					break;
				}
			}
		}
	}

	public function test_get_groupings_by_courseid_and_userid() {
		$courseid = $this->CourseStructure->test_course->id; 
		$userid = $this->CourseStructure->test_user->id; 
		$startpage = 0;
		$n = 5;
		$groupings = local_uniappws_group :: get_groupings_by_courseid_and_userid($courseid, $userid, $startpage, $n);
		$this->assertEqual(sizeof($groupings), sizeof($this->CourseStructure->grouping), 'Correct number of groupings');

		foreach($groupings as $grouping) {
			$test_grouping = array();
			foreach( $this->CourseStructure->grouping as $g) {
				if($g->id == $grouping['id'])	{
					$test_grouping = $g;	
					// assertions
					foreach($grouping as $key => $value) {
						$this->assertEqual($value, $test_grouping->$key, "Grouping name: ".$grouping['name']."; $key value matches");
					}
					break;
				}
			}
		}
	}

	public function test_get_groups_by_groupingid() {
		$grupingid = $this->CourseStructure->test_grouping->id; 
		$userid = $this->CourseStructure->test_user->id; 
		$startpage = 0;
		$n = 5;
		$groups = local_uniappws_group :: get_groups_by_groupingid($grupingid, $startpage, $n);
		$this->assertEqual(sizeof($groups), sizeof($this->CourseStructure->test_grouping->groups), 'The number of groups the grouping is correct');

		foreach($groups as $group) {
			$test_group = array();
			foreach( $this->CourseStructure->test_grouping->groups as $g) {
				if($g->id == $group['id'])	{
					$test_group = $g;	
					// assertions
					foreach($group as $key => $value) {
						$this->assertEqual($value, $test_group->$key, "Group name: ".$group['name']."; $key value matches");
					}
					break;
				}
			}
		}
	}
}
