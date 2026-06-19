<?php

require('../../config.php');

require_login();

$context = context_system::instance();

/* require_capability(
    'local/cert_fanafesa:manage',
    $context
); */

$PAGE->set_context($context);

$PAGE->set_url(
    new moodle_url('/local/cert_fanafesa/signers.php')
);

$PAGE->set_pagelayout('admin');

$PAGE->set_title('Firmantes');

$PAGE->set_heading('Firmantes');

$form = new \local_cert_fanafesa\form\signer_form();

if ($data = $form->get_data()) {

    \local_cert_fanafesa\signer_manager::create(
        $data->fullname,
        $data->position
    );

    redirect(
        new moodle_url(
            '/local/cert_fanafesa/signers.php'
        ),
        'Firmante guardado'
    );
}

echo $OUTPUT->header();

echo $OUTPUT->heading('Nuevo firmante');

$form->display();

$records =
    \local_cert_fanafesa\signer_manager::get_all();

$table = new html_table();

$table->head = [
    'Nombre',
    'Puesto'
];

foreach ($records as $record) {

    $table->data[] = [
        $record->fullname,
        $record->position
    ];
}

echo html_writer::table($table);

echo $OUTPUT->footer();