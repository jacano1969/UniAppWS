<?php

require_once(dirname(__FILE__).'/../../config.php');
require_once(UNIAPP_ROOT . '/mod/url/url.class.php');

class local_uniappws_url extends uniapp_external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_url_parameters() {
        return new external_function_parameters (
            array(
                'urlid' => new external_value(PARAM_INT,  'Resource identifier', VALUE_REQUIRED, 0, NULL_NOT_ALLOWED)
            )
        );
    }

    /**
     * Returns desired url
     *
     * @param int urlid
     *
     * @return url
     */
    public static function get_url($urlid) {
        global $DB;
        if (!$resource = $DB->get_record('url', array('id'=>$urlid))) {
            throw new moodle_exception('url:notfound', 'local_uniappws', '', '');
        }

        $cm = get_coursemodule_from_instance('url', $resource->id, $resource->course, false, MUST_EXIST);

        $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        require_capability('mod/resource:view', $context);
		$resource->url = $resource->externalurl;
        $url = new Url($resource);

		return $url->get_data();

        throw new moodle_exception('url:unknownerror', 'local_uniappws', '', '');
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_url_returns() {
        return Url::get_class_structure();
    }
}

?>
