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
    get_string('title', 'local_cert_fanafesa')
);

$PAGE->set_heading(
    get_string('title', 'local_cert_fanafesa')
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
    get_string('title', 'local_cert_fanafesa')
);

echo html_writer::div(
    get_string('description', 'local_cert_fanafesa'),
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

    '<strong>' .
    get_string('registeredsigners', 'local_cert_fanafesa') .
    ':</strong> ' 

    .

    $signerscount,

    'alert alert-secondary'

);

echo html_writer::end_div();

echo html_writer::start_div(
    'col-md-6'
);

echo html_writer::div(

    '<strong>' .
    get_string('configuredcourses', 'local_cert_fanafesa') .
    ':</strong> ' 

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
    get_string('signers', 'local_cert_fanafesa')
);

echo html_writer::tag(
    'p',
    get_string('signersdescription', 'local_cert_fanafesa')
);

echo html_writer::link(

    new moodle_url(

        '/local/cert_fanafesa/signers.php'

    ),

    get_string('managesigners', 'local_cert_fanafesa'),

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
    get_string('courses', 'local_cert_fanafesa')
);

echo html_writer::tag(
    'p',
    get_string('coursesdescription', 'local_cert_fanafesa')
);

echo html_writer::link(

    new moodle_url(

        '/local/cert_fanafesa/courses.php'

    ),

     get_string('managecourses', 'local_cert_fanafesa'),

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
    get_string('certificates', 'local_cert_fanafesa')
);

echo html_writer::tag(
    'p',
    get_string('certificatesdescription', 'local_cert_fanafesa')
);

echo html_writer::link(

    new moodle_url(

        '/local/cert_fanafesa/certificates.php'

    ),

    get_string('viewcertificates', 'local_cert_fanafesa'),

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