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

    $url = new moodle_url('/local/cert_fanafesa/download.php', [
        'cmid' => $cmid
    ]);

    $button = html_writer::div(
        html_writer::link(
            $url,
            'Descargar certificado',
            ['class' => 'btn btn-success']
        ),
        'fanafesa-btn-container'
    );

    echo $button;
}