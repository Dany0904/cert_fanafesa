<?php

require('../../config.php');
require_once($CFG->libdir . '/filelib.php');

require_login();

$context = context_system::instance();

$id = required_param(
    'id',
    PARAM_INT
);

$signer =
    \local_cert_fanafesa\signer_manager::get(
        $id
    );

$PAGE->set_context($context);

$PAGE->set_url(
    new moodle_url(
        '/local/cert_fanafesa/edit_signer.php',
        [
            'id' => $id
        ]
    )
);

$PAGE->set_pagelayout('admin');

$PAGE->set_title(
    'Editar firmante'
);

$PAGE->set_heading(
    'Editar firmante'
);

$formdata = new stdClass();

$formdata->id = $signer->id;
$formdata->fullname = $signer->fullname;
$formdata->role = $signer->role;

/*
 * Preparar filemanager.
 */
$draftitemid = file_get_submitted_draft_itemid(
    'signature'
);

file_prepare_draft_area(
    $draftitemid,
    $context->id,
    'local_cert_fanafesa',
    'signature',
    $signer->id,
    [
        'subdirs' => 0,
        'maxfiles' => 1
    ]
);

$formdata->signature = $draftitemid;

$form =
    new \local_cert_fanafesa\form\signer_form();

$form->set_data(
    $formdata
);

if ($form->is_cancelled()) {

    redirect(
        new moodle_url(
            '/local/cert_fanafesa/signers.php'
        )
    );
}

if ($data = $form->get_data()) {

    \local_cert_fanafesa\signer_manager::update(
        $data->id,
        $data->fullname,
        $data->role
    );

    \local_cert_fanafesa\signer_manager::save_signature(
        $data->id,
        $data->signature
    );

    redirect(
        new moodle_url(
            '/local/cert_fanafesa/signers.php'
        ),
        'Firmante actualizado'
    );
}

echo $OUTPUT->header();

echo $OUTPUT->heading(
    'Editar firmante'
);

$form->display();

echo $OUTPUT->footer();