<?php

require('../../config.php');

require_login();
require_sesskey();

global $DB;

$id = required_param('id', PARAM_INT);

$used = $DB->record_exists_select(
    'local_cert_fanafesa_course',
    'instructorid = :instructorid
        OR patronid = :patronid
        OR trabajadoresid = :trabajadoresid',
    [
        'instructorid' => $id,
        'patronid' => $id,
        'trabajadoresid' => $id
    ]
);

if ($used) {

    redirect(
        new moodle_url('/local/cert_fanafesa/signers.php'),
        get_string('signerinuse', 'local_cert_fanafesa'),
        null,
        \core\output\notification::NOTIFY_ERROR
    );

}

$fs = get_file_storage();

$fs->delete_area_files(
    context_system::instance()->id,
    'local_cert_fanafesa',
    'signature',
    $id
);

$DB->delete_records(
    'local_cert_fanafesa_signers',
    [
        'id' => $id
    ]
);

redirect(
    new moodle_url('/local/cert_fanafesa/signers.php'),
    get_string('signerdeleted', 'local_cert_fanafesa'),
    null,
    \core\output\notification::NOTIFY_SUCCESS
);