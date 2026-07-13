<?php

require('../../config.php');

require_login();

$context = context_system::instance();

$PAGE->set_context($context);

$PAGE->set_url(
    new moodle_url(
        '/local/cert_fanafesa/certificates.php'
    )
);

$PAGE->set_pagelayout('admin');

$PAGE->set_title(
    get_string('pluginname', 'local_cert_fanafesa')
);

$PAGE->set_heading(
    get_string('pluginname', 'local_cert_fanafesa')
);

$courseid = optional_param(
    'courseid',
    0,
    PARAM_INT
);

echo $OUTPUT->header();

echo $OUTPUT->heading(
    get_string('certificates', 'local_cert_fanafesa')
);

/*
|--------------------------------------------------------------------------
| Selector de cursos
|--------------------------------------------------------------------------
*/

$options = [];

$courses = get_courses();

foreach ($courses as $course) {

    if ($course->id == SITEID) {
        continue;
    }

    $options[$course->id] =
        format_string(
            $course->fullname
        );
}

echo html_writer::start_div('card mb-4');
echo html_writer::start_div('card-body');

echo html_writer::tag(
    'h5',
    get_string('selectcourse', 'local_cert_fanafesa')
);

echo html_writer::start_tag(
    'form',
    [
        'method' => 'get',
        'action' => new moodle_url(
            '/local/cert_fanafesa/certificates.php'
        )
    ]
);

echo html_writer::empty_tag('input', [
    'type' => 'text',
    'id' => 'course-search',
    'class' => 'form-control mb-3',
    'placeholder' => get_string('searchcourseplaceholder', 'local_cert_fanafesa'),
    'style' => 'max-width:700px'
]);

echo html_writer::select(

    $options,

    'courseid',

    $courseid,

    [
        '0' => get_string(
            'selectacourse',
            'local_cert_fanafesa'
        )
    ],

    [

        'id' => 'course-selector',

        'class' => 'form-control',

        'style' => 'max-width:700px',

        'onchange' => 'this.form.submit();'

    ]

);

echo html_writer::end_tag('form');

echo html_writer::end_div();
echo html_writer::end_div();

/*
|--------------------------------------------------------------------------
| Curso seleccionado
|--------------------------------------------------------------------------
*/

if ($courseid) {

    $course = get_course(
        $courseid
    );

    echo html_writer::div(

        '<strong>' .
        get_string('courselabel', 'local_cert_fanafesa') .
        ':</strong> ' .

        format_string($course->fullname),

        'alert alert-info'

    );

    /*
    |--------------------------------------------------------------------------
    | Verificar configuración
    |--------------------------------------------------------------------------
    */

    $config =
        \local_cert_fanafesa\course_manager::get(
            $courseid
        );

    if (
        empty($config)
    ) {

        echo $OUTPUT->notification(

            get_string(
                'coursewithoutconfiguration',
                'local_cert_fanafesa'
            ),

            'warning'

        );

    } else {

        $contextcourse =
            context_course::instance(
                $courseid
            );

        $users =
            get_enrolled_users(

                $contextcourse,

                '',

                0,

                'u.id,
                 u.firstname,
                 u.lastname,
                 u.email'

            );

        if (empty($users)) {

            echo $OUTPUT->notification(

                get_string(
                    'nostudentsenrolled',
                    'local_cert_fanafesa'
                ),

                'info'

            );

        } else {

            echo html_writer::start_tag(

                'form',

                [

                    'method' => 'post',

                    'action' => new moodle_url(

                        '/local/cert_fanafesa/download_zip.php'

                    )

                ]

            );

            echo html_writer::empty_tag(

                'input',

                [

                    'type' => 'hidden',

                    'name' => 'courseid',

                    'value' => $courseid

                ]

            );

            $table = new html_table();

            $table->head = [

                '',

                get_string(
                    'student',
                    'local_cert_fanafesa'
                ),

                get_string(
                    'email',
                    'local_cert_fanafesa'
                ),

                get_string(
                    'certificate',
                    'local_cert_fanafesa'
                )

            ];

            foreach ($users as $user) {

                $fullname = fullname(
                    $user
                );

                $downloadurl =
                    new moodle_url(

                        '/local/cert_fanafesa/download.php',

                        [

                            'userid' => $user->id,

                            'courseid' => $courseid

                        ]

                    );

                $button = html_writer::link(

                    $downloadurl,

                    get_string(
                        'downloadpdf',
                        'local_cert_fanafesa'
                    ),

                    [

                        'class' =>

                        'btn btn-sm btn-primary'

                    ]

                );

                $checkbox = html_writer::checkbox(

                    'users[]',

                    $user->id,

                    false,

                    ''

                );

                $table->data[] = [

                    $checkbox,

                    $fullname,

                    $user->email,

                    $button

                ];
            }

            $table->attributes['class'] =
                'generaltable';

            echo html_writer::table(
                $table
            );

            echo html_writer::empty_tag(
                'br'
            );

            echo html_writer::start_div('mt-3');

            echo html_writer::tag(

                'button',

                get_string(
                    'zipselected',
                    'local_cert_fanafesa'
                ),

                [

                    'type' => 'submit',

                    'name' => 'action',

                    'value' => 'selected',

                    'class' => 'btn btn-primary me-2'

                ]

            );

            echo html_writer::tag(

                'button',

                get_string(
                    'zipall',
                    'local_cert_fanafesa'
                ),

                [

                    'type' => 'submit',

                    'name' => 'action',

                    'value' => 'all',

                    'class' => 'btn btn-secondary'

                ]

            );

            echo html_writer::end_div();

            echo html_writer::end_tag(
                'form'
            );
        }
    }
}

$PAGE->requires->js_amd_inline("
require([], function() {

    const input = document.getElementById('course-search');
    const select = document.getElementById('course-selector');

    if (!input || !select) {
        return;
    }

    const originalOptions = Array.from(select.options);

    input.addEventListener('keyup', function() {

        const value = this.value.toLowerCase().trim();
        const current = select.value;

        select.innerHTML = '';

        originalOptions.forEach(function(option) {

            if (
                option.value === '0' ||
                option.text.toLowerCase().includes(value)
            ) {

                select.appendChild(option.cloneNode(true));

            }

        });

        select.value = current;

    });

});
");

echo $OUTPUT->footer();