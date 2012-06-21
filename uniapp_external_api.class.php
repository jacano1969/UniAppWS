<?php
require_once($CFG->libdir . "/externallib.php");

class uniapp_external_api extends external_api {

	/**
     * extracts the context given a token
	 * @return session object or false if the session is not valid
     */
    public function get_context_by_token($token) {
		global $DB;
		$token_entry = $DB->get_record('external_tokens', array('token'=>$token) );
		return get_context_instance_by_id($token_entry->contextid);
    }
}
?>
