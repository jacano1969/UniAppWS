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
 * @package    localuniappws
 * @copyright  2012 Goran Josic
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
        'local_uniappws_hello' => array(
                'classname'   => 'local_uniappws_external',
                'methodname'  => 'hello',
                'classpath'   => 'local/uniappws/externallib.php',
                'description' => 'Return Hello, add a message parameter to print Hello, $message',
                'type'        => 'read',
        ),

		'local_uniappws_get_username' => array(
                'classname'   => 'local_uniappws_external',
                'methodname'  => 'get_username',
                'classpath'   => 'local/uniappws/externallib.php',
                'description' => 'Given the firstname and lastname as parameters returns the username',
                'type'        => 'read',
        )
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'UniApp web services' => array(
                'functions' => array ('local_uniappws_hello', 'local_uniappws_get_username'),
                'restrictedusers' => 0,
                'enabled'=>1,
        )
);
