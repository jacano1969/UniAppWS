<?php

defined('MOODLE_INTERNAL') || die;
require_once(dirname(__FILE__).'/../config.php');

class local_uniappws_files extends uniapp_external_api {

    public static function upload_private_file_parameters() {
        return new external_function_parameters(
            array(
                'filename' => new external_value(PARAM_FILE, 'Filename', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'filedata' => new external_value(PARAM_TEXT, 'Base64 encoded file data', VALUE_REQUIRED, '', NULL_NOT_ALLOWED)
            )
        );
    }

    public static function upload_private_file($filename, $filedata) {
        global $USER;

        $system_context = get_context_instance(CONTEXT_SYSTEM);
        self::validate_context($system_context);

        //$params = self::validate_parameters(self::upload_private_file_parameters(), array('params' => $parameters));

        $user_context = get_context_instance(CONTEXT_USER, $USER->id);
        $contextid = $user_context->id;
        $component = 'user';
        $filearea = 'private';
        $itemid = 0;
        $filepath = '/';

        $dir = make_temp_directory("wsupload");

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
		// remove the overhead string 'data:mime/type;base64,' if present
		$decoded_file_data = base64_decode(preg_replace("-^data:.*;base64,-",'',$filedata));

        file_put_contents($savedfilepath, base64_decode($decoded_file_data));
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

    public static function upload_draft_file_parameters() {
        return new external_function_parameters(
            array(
                'filename' => new external_value(PARAM_FILE, 'Filename', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                'filedata' => new external_value(PARAM_TEXT, 'Base64 encoded file data', VALUE_REQUIRED, '', NULL_NOT_ALLOWED)
            )
        );
    }

    public static function upload_draft_file($filename, $filedata) {
        global $USER, $CFG;

        $system_context = get_context_instance(CONTEXT_SYSTEM);
        self::validate_context($system_context);

        $user_context = get_context_instance(CONTEXT_USER, $USER->id);

        $dir = make_temp_directory("wsupload");

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
		// remove the overhead string 'data:mime/type;base64,' if present
		//print_r(preg_replace("-^data:.*;base64,-",'',$filedata));
		$decoded_file_data = base64_decode(preg_replace("-^data:.*;base64,-",'',$filedata));

        file_put_contents($savedfilepath, $decoded_file_data);
        unset($filedata);
        $browser = get_file_browser();

        $fs = get_file_storage();
		$fileinfo = array(
			'contextid' => $user_context->id,
			'component' => 'user',
			'filearea' => 'draft',
			'itemid' => 0,
			'filepath' => '/',
			'filename' => $filename
		);

		// remove the older draft if it exists
		$file = $fs->get_file(
			$fileinfo['contextid'],
			$fileinfo['component'],
			$fileinfo['filearea'],
			$fileinfo['itemid'],
			$fileinfo['filepath'],
			$fileinfo['filename']
		);

		// Delete it if it exists
		if ($file) { $file->delete(); }

        $info = $fs->create_file_from_pathname($fileinfo, $savedfilepath);

		$file = $fs->get_file(
			$fileinfo['contextid'],
			$fileinfo['component'],
			$fileinfo['filearea'],
			$fileinfo['itemid'],
			$fileinfo['filepath'],
			$fileinfo['filename']
		);

		if ($file) {
            return array('fileid'=>$file->get_id(), 'filename'=>$filename);
		} else {
            throw new moodle_exception('nofile');
		}
    }

    public static function upload_draft_file_returns() {
        return new external_single_structure(
             array(
                 'fileid' => new external_value(PARAM_INT, 'File id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
                 'filename' => new external_value(PARAM_FILE, 'Filename', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
             )
        );
    }

    public static function upload_private_file_returns() {
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
           throw new moodle_exception('file:nonefound','local_uniappws', '','');
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
           throw new moodle_exception('file:nonefound','files', '','');
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
        $url = $url.$f->get_filepath().$f->get_itemid().'/'.$filename;
        //$url = $url.$f->get_filepath().$filename;

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

    public static function get_file_parameters() {
        return new external_function_parameters(
            array(
                'fileid' => new external_value(PARAM_INT, 'File id', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED),
            )
        );
    }

    public static function get_file($fileid) {
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

		$filename = $f->get_filename();
        $filetype = $f->get_mimetype();
        $filesize = $f->get_filesize();

		header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
		header("Cache-Control: public"); // needed for i.e.
		header("Content-Type: $filetype");
		header("Content-Transfer-Encoding: Binary");
		header("Content-Length: $filesize");
		header("Content-Disposition: attachment; filename=$filename");
		return $f->readfile();
    }

    public static function get_file_returns() { }
}

?>
