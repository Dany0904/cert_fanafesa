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
    get_string('courses', 'local_cert_fanafesa')
);

$PAGE->set_heading(
    get_string('courses', 'local_cert_fanafesa')
);

echo $OUTPUT->header();

echo $OUTPUT->heading(
    get_string('courseconfiguration', 'local_cert_fanafesa')
);

echo html_writer::div(

    get_string(
        'coursesconfigurationdescription',
        'local_cert_fanafesa'
    ),

    'alert alert-info'

);

$courses = get_courses();

$table = new html_table();

$table->head = [

    get_string('course', 'local_cert_fanafesa'),

    get_string('instructor', 'local_cert_fanafesa'),

    get_string('patron', 'local_cert_fanafesa'),

    get_string('trabajadores', 'local_cert_fanafesa'),

    get_string('enabledc3', 'local_cert_fanafesa'),

    get_string('actions', 'local_cert_fanafesa')

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

     $toggleurl = new moodle_url(
        '/local/cert_fanafesa/toggle_custom_button.php',
        [
            'courseid' => $course->id,
            'usecustombutton' => $config && $config->usecustombutton ? 0 : 1,
            'sesskey' => sesskey()
        ]
    );

    $status = $config && $config->usecustombutton
        ? html_writer::span(
            get_string('enabled', 'local_cert_fanafesa'),
            'badge badge-success'
        )
        : html_writer::span(
            get_string('disabled', 'local_cert_fanafesa'),
            'badge badge-secondary'
        );

    $dc3button = $status . ' ' . html_writer::link(
        $toggleurl,
        $config && $config->usecustombutton
            ? get_string('deactivate', 'local_cert_fanafesa')
            : get_string('activate', 'local_cert_fanafesa'),
        [
            'class' => $config && $config->usecustombutton
                ? 'btn btn-warning btn-sm ml-2'
                : 'btn btn-success btn-sm ml-2'
        ]
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

        get_string('configure', 'local_cert_fanafesa'),

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

        $dc3button,

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

    '<strong>' .
    get_string('courses', 'local_cert_fanafesa') .
    ':</strong> '

    .

    $totalcursos,

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

    $configurados,

    'alert alert-success'

);

echo html_writer::end_div();

echo html_writer::end_div();

echo html_writer::start_div('mb-3');

echo html_writer::label(
    get_string('searchcourse', 'local_cert_fanafesa'),
    'course-search',
    false,
    ['class' => 'form-label']
);

echo html_writer::empty_tag('input', [
    'type' => 'text',
    'id' => 'course-search',
    'class' => 'form-control',
    'placeholder' => get_string('searchcourseplaceholder', 'local_cert_fanafesa')
]);

echo html_writer::end_div();

echo html_writer::start_div(

    'card'

);

echo html_writer::start_div(

    'card-body'

);

echo html_writer::tag(

    'h4',

    get_string('courses', 'local_cert_fanafesa')

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

        get_string('back', 'local_cert_fanafesa'),

        [

            'class' =>

            'btn btn-light mt-3'

        ]

    )

);

$PAGE->requires->js_amd_inline("
require([], function() {

    const table = document.querySelector('.generaltable');

    if (table) {
        table.id = 'courses-table';
    }

    const input = document.getElementById('course-search');

    if (!input || !table) {
        return;
    }

    const rows = table.querySelectorAll('tbody tr');

    input.addEventListener('keyup', function() {

        const value = this.value.toLowerCase().trim();

        rows.forEach(function(row) {

            const course = row.cells[0].textContent.toLowerCase();

            row.style.display = course.includes(value)
                ? ''
                : 'none';
        });

    });

});
");

echo $OUTPUT->footer();