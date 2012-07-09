<?php
require_once($CFG->libdir . "/externallib.php");

class uniapp_external_api extends external_api {
	/**
     * Makes sure user may execute functions in this context.
     * @param object $context
     * @return void
     */
    protected static function validate_context($context) {
        global $CFG;
        if (empty($context)) {
            throw new invalid_parameter_exception('Context does not exist');
        }

        $rcontext = get_context_instance(CONTEXT_SYSTEM);

        if ($rcontext->contextlevel == $context->contextlevel) {
            if ($rcontext->id != $context->id) {
                throw new restricted_context_exception();
            }
        } else if ($rcontext->contextlevel > $context->contextlevel) {
            throw new restricted_context_exception();
        } else {
            $parents = get_parent_contexts($context);
            if (!in_array($rcontext->id, $parents)) {
                throw new restricted_context_exception();
            }
        }

        if ($context->contextlevel >= CONTEXT_COURSE) {
            list($context, $course, $cm) = get_context_info_array($context->id);
        }
    }

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
