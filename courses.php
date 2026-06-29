<?php

require('../../config.php');

require_login();

$context = context_system::instance();

/*
require_capability(
    'local/cert_fanafesa:manage',
    $context
);
*/

$PAGE->set_context(
    $context
);

$PAGE->set_url(

    new moodle_url(

        '/local/cert_fanafesa/courses.php'

    )

);

$PAGE->set_pagelayout(
    'admin'
);

$PAGE->set_title(
    'Cursos FANAFESA'
);

$PAGE->set_heading(
    'Cursos FANAFESA'
);

echo $OUTPUT->header();

echo $OUTPUT->heading(
    'Configuración de cursos'
);

echo html_writer::div(

    'Asocie usuarios a cada curso que utilizarán los certificados.',

    'alert alert-info'

);

$courses = get_courses();

$table = new html_table();

$table->head = [

    'Curso',

    'Instructor',

    'Patrón',

    'Trabajadores',

    'Estado',

    'Acciones'

];

$configurados = 0;

foreach ($courses as $course) {

    if (

        $course->id == SITEID

    ) {

        continue;

    }

    $config =

        \local_cert_fanafesa\course_manager::get(

            $course->id

        );

    $instructor = '-';
    $patron = '-';
    $trabajadores = '-';

    $estado = html_writer::span(

        'Sin configurar',

        'badge badge-warning'

    );

    if ($config) {

        $configurados++;

        $instructorobj =

            $config->instructorid

            ?

            \local_cert_fanafesa\signer_manager::get(

                $config->instructorid

            )

            : null;

        $patronobj =

            $config->patronid

            ?

            \local_cert_fanafesa\signer_manager::get(

                $config->patronid

            )

            : null;

        $trabajadoresobj =

            $config->trabajadoresid

            ?

            \local_cert_fanafesa\signer_manager::get(

                $config->trabajadoresid

            )

            : null;

        $instructor =

            $instructorobj->fullname

            ??

            '-';

        $patron =

            $patronobj->fullname

            ??

            '-';

        $trabajadores =

            $trabajadoresobj->fullname

            ??

            '-';

        if (

            $config->instructorid &&

            $config->patronid &&

            $config->trabajadoresid

        ) {

            $estado = html_writer::span(

                'Completo',

                'badge badge-success'

            );

        } else {

            $estado = html_writer::span(

                'Parcial',

                'badge badge-secondary'

            );

        }

    }

    $url = new moodle_url(

        '/local/cert_fanafesa/course_settings.php',

        [

            'courseid' =>

            $course->id

        ]

    );

    $button = html_writer::link(

        $url,

        'Configurar',

        [

            'class' =>

            'btn btn-primary btn-sm'

        ]

    );

    $table->data[] = [

        format_string(

            $course->fullname

        ),

        $instructor,

        $patron,

        $trabajadores,

        $estado,

        $button

    ];

}

$totalcursos = count($table->data);

echo html_writer::start_div(

    'row mb-4'

);

echo html_writer::start_div(

    'col-md-6'

);

echo html_writer::div(

    '<strong>Cursos:</strong> '

    .

    $totalcursos,

    'alert alert-secondary'

);

echo html_writer::end_div();

echo html_writer::start_div(

    'col-md-6'

);

echo html_writer::div(

    '<strong>Cursos configurados:</strong> '

    .

    $configurados,

    'alert alert-success'

);

echo html_writer::end_div();

echo html_writer::end_div();


echo html_writer::start_div(

    'card'

);

echo html_writer::start_div(

    'card-body'

);

echo html_writer::tag(

    'h4',

    'Cursos'

);

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