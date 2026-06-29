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
    new moodle_url(
        '/local/cert_fanafesa/index.php'
    )
);

$PAGE->set_pagelayout(
    'admin'
);

$PAGE->set_title(
    'Certificados FANAFESA'
);

$PAGE->set_heading(
    'Certificados FANAFESA'
);

/*
|--------------------------------------------------------------------------
| Estadísticas
|--------------------------------------------------------------------------
*/

$signerscount = count(
    \local_cert_fanafesa\signer_manager::get_all()
);

$configuredcourses = $DB->count_records(
    'local_cert_fanafesa_course'
);

/*
|--------------------------------------------------------------------------
| Render
|--------------------------------------------------------------------------
*/

echo $OUTPUT->header();

echo $OUTPUT->heading(
    'Certificados FANAFESA'
);

echo html_writer::div(

    'Sistema de administración y generación de certificados personalizados.',

    'alert alert-info'

);

/*
|--------------------------------------------------------------------------
| Resumen
|--------------------------------------------------------------------------
*/

echo html_writer::start_div(
    'row mb-4'
);

echo html_writer::start_div(
    'col-md-6'
);

echo html_writer::div(

    '<strong>Firmantes registrados:</strong> '

    .

    $signerscount,

    'alert alert-secondary'

);

echo html_writer::end_div();

echo html_writer::start_div(
    'col-md-6'
);

echo html_writer::div(

    '<strong>Cursos configurados:</strong> '

    .

    $configuredcourses,

    'alert alert-secondary'

);

echo html_writer::end_div();

echo html_writer::end_div();

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

echo html_writer::start_div(
    'row'
);

/*
|--------------------------------------------------------------------------
| Firmantes
|--------------------------------------------------------------------------
*/

echo html_writer::start_div(
    'col-md-4'
);

echo html_writer::start_div(
    'card mb-4 shadow-sm'
);

echo html_writer::start_div(
    'card-body'
);

echo html_writer::tag(

    'h4',

    'Firmantes'

);

echo html_writer::tag(

    'p',

    'Administración de instructores, representantes patronales y representantes de trabajadores.'

);

echo html_writer::link(

    new moodle_url(

        '/local/cert_fanafesa/signers.php'

    ),

    'Administrar firmantes',

    [

        'class' =>

        'btn btn-primary'

    ]

);

echo html_writer::end_div();

echo html_writer::end_div();

echo html_writer::end_div();

/*
|--------------------------------------------------------------------------
| Cursos
|--------------------------------------------------------------------------
*/

echo html_writer::start_div(
    'col-md-4'
);

echo html_writer::start_div(
    'card mb-4 shadow-sm'
);

echo html_writer::start_div(
    'card-body'
);

echo html_writer::tag(

    'h4',

    'Cursos'

);

echo html_writer::tag(

    'p',

    'Asociación de firmantes y configuración específica para cada curso.'

);

echo html_writer::link(

    new moodle_url(

        '/local/cert_fanafesa/courses.php'

    ),

    'Configurar cursos',

    [

        'class' =>

        'btn btn-primary'

    ]

);

echo html_writer::end_div();

echo html_writer::end_div();

echo html_writer::end_div();

/*
|--------------------------------------------------------------------------
| Certificados
|--------------------------------------------------------------------------
*/

echo html_writer::start_div(
    'col-md-4'
);

echo html_writer::start_div(
    'card mb-4 shadow-sm'
);

echo html_writer::start_div(
    'card-body'
);

echo html_writer::tag(

    'h4',

    'Certificados'

);

echo html_writer::tag(

    'p',

    'Consulta, descarga individual y generación masiva de certificados en formato ZIP.'

);

echo html_writer::link(

    new moodle_url(

        '/local/cert_fanafesa/certificates.php'

    ),

    'Ver certificados',

    [

        'class' =>

        'btn btn-primary'

    ]

);

echo html_writer::end_div();

echo html_writer::end_div();

echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();