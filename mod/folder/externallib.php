<?php

require_once(dirname(__FILE__).'/../../config.php');
require_once(UNIAPP_ROOT . '/mod/folder/folder.class.php');
require_once(UNIAPP_ROOT . '/util/dump.class.php');

class local_uniappws_folder extends uniapp_external_api {

	public static $folder_structure;

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_folder_parameters() {
        return new external_function_parameters (
            array(
                'folderid' => new external_value(PARAM_INT,  'Folder identifier', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }

	private static function parse_tree($subdir, $revision, $context){
		global $CFG;
		$current_dir = '';
		if($subdir['dirname'] == null){ // this is the root
			$current_dir = '/';
			self::$folder_structure[$current_dir]['parent'] = $current_dir;
		} else { // a subfolder
			$current_dir = $subdir['dirname'];
		}

		// prepare the container
		self::$folder_structure[$current_dir]['subdir'] = array();
		foreach($subdir['subdirs'] as $dir){
			self::$folder_structure[$dir['dirname']]['parent'] = $current_dir;
			array_push(self::$folder_structure[$current_dir]['subdir'], $dir['dirname']);
			self::parse_tree($dir, $revision, $context);
		}

		self::$folder_structure[$current_dir]['file'] = array();
		foreach($subdir['files'] as $file){
			$new_file_entry = array(); 
			//$new_file_entry['id'] = $file->get_id();
			$new_file_entry['name'] = $file->get_filename();
			$new_file_entry['fileid'] = $file->get_id();
			$new_file_entry['size'] = $file->get_filesize();
			$new_file_entry['mime'] = $file->get_mimetype();
			/*
			$new_file_entry['url'] = file_encode_url(
				"$CFG->wwwroot/pluginfile.php",
				'/'.$context->id.'/mod_folder/content/'.$revision.$file->get_filepath().$file->get_filename(),
				true
			);
			*/
			array_push(self::$folder_structure[$current_dir]['file'], $new_file_entry);
		}
	}

    /**
     * Returns desired folder
     *
     * @param int folderid
     *
     * @return folder
     */
    public static function get_folder($folderid) {
        global $DB;

        if (!$folder = $DB->get_record('folder', array('id'=>$folderid))) {
            throw new moodle_exception('generalexceptionmessage','moodbile_resource', '','Folder not found');
        }

        $cm = get_coursemodule_from_instance('folder', $folder->id, $folder->course, false, MUST_EXIST);

        $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        require_capability('mod/folder:view', $context);
		$fs = get_file_storage();
		$tree = $fs->get_area_tree($context->id, 'mod_folder', 'content', 0);
		self::parse_tree($tree, $folder->revision, $context);
		
		$folder = array();

		foreach(self::$folder_structure as $folder_name => $content) {
			$folder_entry = array();
			$folder_entry['rootid'] = $folderid;
			$folder_entry['parent'] = $content['parent'];
			$folder_entry['name'] = $folder_name;
			$folder_entry['fileid'] = 0;
			$folder_entry['size'] = 0;
			$folder_entry['mime'] = 'inode/directory';
			$folder_entry['type'] = 'dir';
			//$folder_entry['url'] = '';
			array_push($folder, $folder_entry);
			// check files
			foreach($content['file'] as $file){
				$file['rootid'] = $folderid;
				$file['parent'] = $folder_entry['name'];
				$file['type'] = 'file';
				array_push($folder, $file);
			}
		}
		return $folder;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_folder_returns() {
        return new external_multiple_structure( Folder::get_class_structure() );
    }
}

?>
