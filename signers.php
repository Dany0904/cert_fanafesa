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

echo $OUTPUT->heading('Firmantes');

echo html_writer::div(

    'Administración de instructores, representantes patronales y representantes de trabajadores.',

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

    '<strong>Firmantes registrados:</strong> '

    .

    $total,

    'alert alert-secondary'

);

echo html_writer::end_div();

echo html_writer::start_div(
    'col-md-6'
);

echo html_writer::div(

    '<strong>Firmantes activos:</strong> '

    .

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

    'Nuevo firmante'

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

    'Firmantes registrados'

);

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

                'Activo',

                'badge badge-success'

            )

            :

            html_writer::span(

                'Inactivo',

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

            'Editar',

            [

                'class' =>

                'btn btn-sm btn-primary'

            ]

        );

    $actions .= ' ';

    $actions .= html_writer::link(

        $toggleurl,

        $record->active

            ? 'Desactivar'

            : 'Activar',

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

        '← Volver',

        [

            'class' =>

            'btn btn-light mt-3'

        ]

    )

);

echo $OUTPUT->footer();