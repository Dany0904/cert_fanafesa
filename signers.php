<?php

require('../../config.php');

require_login();

$context = context_system::instance();

$PAGE->set_context($context);

$PAGE->set_url(
    new moodle_url('/local/cert_fanafesa/signers.php')
);

$PAGE->set_pagelayout('admin');

$PAGE->set_title(
    get_string('signers', 'local_cert_fanafesa')
);

$PAGE->set_heading(
    get_string('signers', 'local_cert_fanafesa')
);

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
        new moodle_url('/local/cert_fanafesa/signers.php'),
        get_string('signerssaved', 'local_cert_fanafesa')
    );
}

echo $OUTPUT->header();

echo $OUTPUT->heading(
    get_string('signers', 'local_cert_fanafesa')
);

echo html_writer::div(
    get_string('signersdescription', 'local_cert_fanafesa'),
    'alert alert-info'
);

/*
|--------------------------------------------------------------------------
| Estadísticas
|--------------------------------------------------------------------------
*/

$records =
    \local_cert_fanafesa\signer_manager::get_all();

$total = count($records);

$activos = 0;

foreach ($records as $record) {

    if ($record->active) {

        $activos++;

    }

}

echo html_writer::start_div(
    'row mb-4'
);

echo html_writer::start_div(
    'col-md-6'
);

echo html_writer::div(

    '<strong>' .
    get_string('registeredsigners', 'local_cert_fanafesa') .
    ':</strong> ' .
    $total,

    'alert alert-secondary'

);

echo html_writer::end_div();

echo html_writer::start_div(
    'col-md-6'
);

echo html_writer::div(

    '<strong>' .
    get_string('activesigners', 'local_cert_fanafesa') .
    ':</strong> ' .
    $activos,

    'alert alert-success'

);

echo html_writer::end_div();

echo html_writer::end_div();


/*
|--------------------------------------------------------------------------
| Formulario
|--------------------------------------------------------------------------
*/

echo html_writer::start_div(
    'card mb-4'
);

echo html_writer::start_div(
    'card-body'
);

echo html_writer::tag(
    'h4',
    get_string('newsigner', 'local_cert_fanafesa')
);

$form->display();

echo html_writer::end_div();

echo html_writer::end_div();


/*
|--------------------------------------------------------------------------
| Tabla
|--------------------------------------------------------------------------
*/

echo html_writer::start_div(
    'card'
);

echo html_writer::start_div(
    'card-body'
);

echo html_writer::tag(
    'h4',
    get_string('registeredsigners', 'local_cert_fanafesa')
);

$table = new html_table();

$table->head = [

    get_string('name', 'local_cert_fanafesa'),

    get_string('type', 'local_cert_fanafesa'),

    get_string('signature', 'local_cert_fanafesa'),

    get_string('status', 'local_cert_fanafesa'),

    get_string('actions', 'local_cert_fanafesa')

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

                    max-height:60px;

                    max-width:180px;

                    border:1px solid #ddd;

                    padding:4px;

                    background:#fff;

                '

            ]

        );

    }

    $estado =

        $record->active

            ?

            html_writer::span(

                get_string('active', 'local_cert_fanafesa'),

                'badge badge-success'

            )

            :

            html_writer::span(

                get_string('inactive', 'local_cert_fanafesa'),

                'badge badge-secondary'

            );

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

            'active' =>

            $record->active

                ? 0

                : 1

        ]

    );

    $actions =

        html_writer::link(

            $editurl,

            get_string('edit', 'local_cert_fanafesa'),

            [

                'class' =>

                'btn btn-sm btn-primary'

            ]

        );

    $deleteurl = new moodle_url(
        '/local/cert_fanafesa/delete_signer.php',
        [
            'id' => $record->id,
            'sesskey' => sesskey()
        ]
    );

    $actions .= ' ';

    $actions .= html_writer::link(
        $deleteurl,
        get_string('delete', 'local_cert_fanafesa'),
        [
            'class' => 'btn btn-sm btn-danger',
            'onclick' => "return confirm('" .
                get_string('confirmsignerdelete', 'local_cert_fanafesa') .
                "');"
        ]
    );

    $actions .= ' ';

    $actions .= html_writer::link(

        $toggleurl,

        $record->active

            ?

            get_string('deactivate', 'local_cert_fanafesa')

            :

            get_string('activate', 'local_cert_fanafesa'),

        [

            'class' =>

            $record->active

                ?

                'btn btn-sm btn-warning'

                :

                'btn btn-sm btn-success'

        ]

    );

    $table->data[] = [

        format_string(

            $record->fullname

        ),

        get_string(

            $record->role,

            'local_cert_fanafesa'

        ),

        $signaturehtml,

        $estado,

        $actions

    ];

}

$table->attributes['class'] =

    'generaltable table-striped';

echo html_writer::table(

    $table

);

echo html_writer::end_div();

echo html_writer::end_div();


echo html_writer::div(

    html_writer::link(

        new moodle_url(

            '/local/cert_fanafesa/index.php'

        ),

        get_string('back', 'local_cert_fanafesa'),

        [

            'class' =>

            'btn btn-light mt-3'

        ]

    )

);

echo $OUTPUT->footer();