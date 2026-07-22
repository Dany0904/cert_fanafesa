<?php

defined('MOODLE_INTERNAL') || die();

function local_cert_fanafesa_pluginfile(
    $course,
    $cm,
    $context,
    $filearea,
    $args,
    $forcedownload,
    array $options = []
) {

    if ($context->contextlevel != CONTEXT_SYSTEM) {
        send_file_not_found();
    }

    if ($filearea !== 'signature') {
        send_file_not_found();
    }

    require_login();

    $itemid = array_shift($args);

    $filename = array_pop($args);

    $filepath = '/';

    $fs = get_file_storage();

    $file = $fs->get_file(
        $context->id,
        'local_cert_fanafesa',
        'signature',
        $itemid,
        $filepath,
        $filename
    );

    if (!$file) {
        send_file_not_found();
    }

    send_stored_file($file);
}

function local_cert_fanafesa_before_footer() {
    global $PAGE;

    if ($PAGE->pagetype !== 'mod-customcert-view') {
        return;
    }

    $cmid = optional_param('id', 0, PARAM_INT);

    if (!$cmid) {
        return;
    }

    $cm = get_coursemodule_from_id(
        'customcert',
        $cmid,
        0,
        false,
        MUST_EXIST
    );

    $courseid = $cm->course;

    $config = \local_cert_fanafesa\course_manager::get($courseid);

    $usecustombutton = $config && !empty($config->usecustombutton);

    if (!$usecustombutton) {
        return;
    }

    $url = new moodle_url('/local/cert_fanafesa/download.php', [
        'cmid' => $cmid
    ]);
    
    $PAGE->requires->js_call_amd(
        'local_cert_fanafesa/customcert',
        'init',
        [
            $url->out(false),
            get_string('downloadcertificate', 'local_cert_fanafesa')
        ]
    );
}

