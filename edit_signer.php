<?php

require('../../config.php');

require_once(
    $CFG->libdir .
    '/filelib.php'
);

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

$PAGE->set_context(
    $context
);

$PAGE->set_url(

    new moodle_url(

        '/local/cert_fanafesa/edit_signer.php',

        [

            'id' => $id

        ]

    )

);

$PAGE->set_pagelayout(
    'admin'
);

$PAGE->set_title(
    'Editar firmante'
);

$PAGE->set_heading(
    'Editar firmante'
);

/*
|--------------------------------------------------------------------------
| Form data
|--------------------------------------------------------------------------
*/

$formdata = new stdClass();

$formdata->id =
    $signer->id;

$formdata->fullname =
    $signer->fullname;

$formdata->role =
    $signer->role;

/*
|--------------------------------------------------------------------------
| Filemanager
|--------------------------------------------------------------------------
*/

$draftitemid =

    file_get_submitted_draft_itemid(

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

$formdata->signature =
    $draftitemid;

$form =

    new \local_cert_fanafesa\form\signer_form();

$form->set_data(

    $formdata

);

/*
|--------------------------------------------------------------------------
| Cancelar
|--------------------------------------------------------------------------
*/

if (

    $form->is_cancelled()

) {

    redirect(

        new moodle_url(

            '/local/cert_fanafesa/signers.php'

        )

    );

}

/*
|--------------------------------------------------------------------------
| Guardar
|--------------------------------------------------------------------------
*/

if (

    $data = $form->get_data()

) {

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

/*
|--------------------------------------------------------------------------
| Render
|--------------------------------------------------------------------------
*/

echo $OUTPUT->header();

echo $OUTPUT->heading(
    'Editar firmante'
);

echo html_writer::div(

    'Actualice la información del firmante y su firma asociada.',

    'alert alert-info'

);

/*
|--------------------------------------------------------------------------
| Vista previa
|--------------------------------------------------------------------------
*/

$url =

    \local_cert_fanafesa\signer_manager::get_signature_url(

        $signer->id

    );

if ($url) {

    echo html_writer::start_div(

        'card mb-4'

    );

    echo html_writer::start_div(

        'card-body'

    );

    echo html_writer::tag(

        'h5',

        'Firma actual'

    );

    echo html_writer::empty_tag(

        'img',

        [

            'src' => $url,

            'style' => '

                max-height:100px;

                max-width:300px;

                border:1px solid #ddd;

                background:white;

                padding:6px;

            '

        ]

    );

    echo html_writer::end_div();

    echo html_writer::end_div();

}

/*
|--------------------------------------------------------------------------
| Formulario
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

    'Datos del firmante'

);

$form->display();

echo html_writer::end_div();

echo html_writer::end_div();

/*
|--------------------------------------------------------------------------
| Regresar
|--------------------------------------------------------------------------
*/

echo html_writer::div(

    html_writer::link(

        new moodle_url(

            '/local/cert_fanafesa/signers.php'

        ),

        '← Volver',

        [

            'class' =>

            'btn btn-light mt-3'

        ]

    )

);

echo $OUTPUT->footer();