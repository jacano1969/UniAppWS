<?php

require_once(dirname(__FILE__).'/config.php');

function get_link($userpicture){
    global $OUTPUT, $PAGE, $USER;

    if (empty($userpicture->size)) {
        $file = 'f2';
        $size = 35;
    } else if ($userpicture->size === true or $userpicture->size == 1) {
        $file = 'f1';
        $size = 100;
    } else if ($userpicture->size >= 50) {
        $file = 'f1';
        $size = $userpicture->size;
    } else {
        $file = 'f2';
        $size = $userpicture->size;
    }

    $class = $userpicture->class;
    $user = $userpicture->user;
    $usercontext = get_context_instance(CONTEXT_USER, $USER->id);
    if ($user->picture == 1) {
        $usercontext = get_context_instance(CONTEXT_USER, $user->id);
        $src = moodle_url::make_pluginfile_url($usercontext->id, 'user', 'icon', NULL, '/', $file);
    } else if ($user->picture == 2) {
        //TODO: gravatar user icon support
    } else { // Print default user pictures (use theme version if available)
        $PAGE->set_context($usercontext);
        $src = $OUTPUT->pix_url('u/' . $file);
    }

    return $src->out();
}

?>
