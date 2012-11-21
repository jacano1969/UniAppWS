<?php

require_once(dirname(__FILE__).'/../../config.php');
require_once(UNIAPP_ROOT . '/mod/resource/resource.class.php');

class local_uniappws_resource extends uniapp_external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_resource_parameters() {
        return new external_function_parameters (
            array(
                'resourceid' => new external_value(PARAM_INT,  'Resource identifier', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }

    /**
     * Returns desired resource
     *
     * @param int resourceid
     *
     * @return resource
     */
    public static function get_resource($resourceid) {
        global $DB;

        if (!$resource = $DB->get_record('resource', array('id'=>$resourceid))) {
            throw new moodle_exception('generalexceptionmessage','moodbile_resource', '','Resource not found');
        }
        $cm = get_coursemodule_from_instance('resource', $resource->id, $resource->course, false, MUST_EXIST);

        $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        require_capability('mod/resource:view', $context);

        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_resource', 'content', 0, 'sortorder DESC, id ASC', false); // TODO: this is not very efficient!!
        if (count($files) < 1) {
            throw new moodle_exception('generalexceptionmessage','moodbile_resource', '','File not found');
        } else {
            $file = reset($files);
            unset($files);

            $resource->filename = $file->get_filename();
            $resource->fileid = $file->get_id();
            $resource->filemime = $file->get_mimetype();
            $resource->filesize = $file->get_filesize();
            $return = new Resource($resource);
            $return = $return->get_data();

            return $return;
        }

        throw new moodle_exception('generalexceptionmessage','moodbile_resource', '','Error');
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_resource_returns() {
        return Resource::get_class_structure();
    }
}

?>
