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
 * File Related External Functions
 *
 * @package MoodbileServer
 * @subpackage Files
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

defined('MOODLE_INTERNAL') || die;
require_once(dirname(__FILE__).'/../config.php');

class local_uniappws_files extends uniapp_external_api {

    public static function upload_file_parameters() {
        return new external_function_parameters(
            array(
                'filename' => new external_value(PARAM_FILE, 'Filename', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'filedata' => new external_value(PARAM_TEXT, 'Base64 encoded file data', VALUE_REQUIRED, '', NULL_NOT_ALLOWED)
            )
        );
    }

    public static function upload_file($filename, $filedata) {
        global $USER;

        $system_context = get_context_instance(CONTEXT_SYSTEM);
        self::validate_context($system_context);

        //$params = self::validate_parameters(self::upload_file_parameters(), array('params' => $parameters));

        $user_context = get_context_instance(CONTEXT_USER, $USER->id);
        $contextid = $user_context->id;
        $component = 'user';
        $filearea = 'private';
        $itemid = 0;
        $filepath = '/';

        $dir = make_upload_directory('temp/wsupload');

        if (empty($filename)) {
            $filenamets = uniqid('wsupload').'_'.time().'.tmp';
        } else {
            $filenamets = $filename;
        }

        if (file_exists($dir.$filenamets)) {
            $savedfilepath = $dir.uniqid('m').$filenamets;
        } else {
            $savedfilepath = $dir.$filenamets;
        }

        file_put_contents($savedfilepath, base64_decode($filedata));
        unset($filedata);
        $browser = get_file_browser();

        // check existing file
        if ($file = $browser->get_file_info($user_context, $component, $filearea, $itemid, $filepath, $filenamets)) {
            throw new moodle_exception('fileexist');
        }

        // move file to filepool
        if ($dir = $browser->get_file_info($user_context, $component, $filearea, $itemid, $filepath, '.')) {
            $info = $dir->create_file_from_pathname($filenamets, $savedfilepath);
            $fs = get_file_storage();
            $file = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, $filenamets);
            $params = $info->get_params();
            unlink($savedfilepath);
            return array(
                'fileid'=>$file->get_id(),
                'filename'=>$filename,
                );

        } else {
            throw new moodle_exception('nofile');
        }
    }

    public static function upload_file_returns() {
        return new external_single_structure(
             array(
                 'fileid' => new external_value(PARAM_INT, 'File id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                 'filename' => new external_value(PARAM_FILE, 'Filename', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
             )
        );
    }

    public static function get_user_filesinfo_parameters() {
        return new external_function_parameters(
                array(
                    'startpage' => new external_value(PARAM_INT, 'Start page', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                    'n'         => new external_value(PARAM_INT, 'Page number', VALUE_DEFAULT, 10, NULL_NOT_ALLOWED)
                )
        );
    }

    public static function get_user_filesinfo($startpage, $n) {
        global $USER;

        $system_context = get_context_instance(CONTEXT_SYSTEM);
        self::validate_context($system_context);

        $fs = get_file_storage();
        $user_context = get_context_instance(CONTEXT_USER, $USER->id);
        $contextid = $user_context->id;
        $results = $fs->get_area_files($contextid, 'user', 'private', 0, "sortorder, itemid, filepath, filename" , false);
        if (empty($results)) {
           throw new moodle_exception('generalexceptionmessage','moodbile_files', '','No files found');
        }
        $ret = array();
        $i=0;
        $begin = $startpage*$n;
        foreach ($results as $file) {//TODO improve this loop
            if ($file->get_filename() !== '.') {
                if ($i >= $begin && $i < $begin+$n ) {
                    $ret[] = array( 'fileid' => $file->get_id(), 'filename' => $file->get_filename());
                }
                $i++;
                if ($i == $begin+$n ) break;
            }
        }
        if (empty($ret)) {
           throw new moodle_exception('generalexceptionmessage','moodbile_files', '','No files found');
        }
        return $ret;
    }

    public static function get_user_filesinfo_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'fileid' => new external_value(PARAM_INT, 'File id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                    'filename' => new external_value(PARAM_FILE, 'Filename', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                )
            )
        );
    }

    public static function get_file_url_parameters() {
        return new external_function_parameters(
            array(
                'fileid' => new external_value(PARAM_INT, 'File id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
            )
        );
    }

    public static function get_file_url($fileid) {
        global $CFG, $DB, $USER;
        $system_context = get_context_instance(CONTEXT_SYSTEM);
        self::validate_context($system_context);

        $fs = get_file_storage();
        $f = $fs->get_file_by_id($fileid);
        if (!$f) {
           throw new moodle_exception('nofile');
        }
        if ($f->get_filesize() == 0) {
            throw new moodle_exception('invalidfile');
        }
        $url = "{$CFG->wwwroot}/pluginfile.php/{$f->get_contextid()}/{$f->get_component()}/{$f->get_filearea()}";
        $filename = $f->get_filename();
        //$url = $url.$f->get_filepath().$f->get_itemid().'/'.$filename;
        $url = $url.$f->get_filepath().$filename;

        //return
        return array(
                'filename' =>$filename,
                'filesize' => $f->get_filesize(),
                'mime' => $f->get_mimetype(),
                'url' => $url
        );
    }

    public static function get_file_url_returns() {
        return new external_single_structure(
            array(
                'filename' => new external_value(PARAM_TEXT, 'Filename', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'filesize' => new external_value(PARAM_INT, 'Filesize', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                'mime' => new external_value(PARAM_TEXT, 'File MIME', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'url' => new external_value(PARAM_URL, 'File url', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'timedue' => new external_value(PARAM_INT, 'Time when access expires', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }

}
