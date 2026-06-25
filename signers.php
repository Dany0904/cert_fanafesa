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

    $signerid =
        \local_cert_fanafesa\signer_manager::create(
            $data->fullname,
            $data->role
        );

    \local_cert_fanafesa\signer_manager::save_signature(
        $signerid,
        $data->signature
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
    'Tipo',
    'Firma',
    'Estado',
    'Acciones'
];

foreach ($records as $record) {

    $signaturehtml = '-';

    $url =
        \local_cert_fanafesa\signer_manager::get_signature_url(
            $record->id
        );

    if ($url) {

        $signaturehtml = html_writer::empty_tag(
            'img',
            [
                'src' => $url,
                'style' => '
                    max-height:70px;
                    max-width:200px;
                    border:1px solid #ccc;
                    padding:4px;
                    background:white;
                '
            ]
        );
    }

    $estado =
        $record->active
            ? 'Activo'
            : 'Inactivo';

    $editurl = new moodle_url(
        '/local/cert_fanafesa/edit_signer.php',
        [
            'id' => $record->id
        ]
    );

    $toggleurl = new moodle_url(
        '/local/cert_fanafesa/toggle_signer.php',
        [
            'id' => $record->id,
            'active' => $record->active ? 0 : 1
        ]
    );

    $actions =
        html_writer::link(
            $editurl,
            'Editar'
        );

    $actions .= ' | ';

    $actions .= html_writer::link(
        $toggleurl,
        $record->active
            ? 'Desactivar'
            : 'Activar'
        );

    $table->data[] = [

        $record->fullname,

        get_string(
            $record->role,
            'local_cert_fanafesa'
        ),

        $signaturehtml,

        $estado,

        $actions
    ];
}

$table->attributes['class'] = 'generaltable';

echo html_writer::table($table);

echo $OUTPUT->footer();