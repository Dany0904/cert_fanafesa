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
    'Certificados FANAFESA'
);

$PAGE->set_heading(
    'Certificados FANAFESA'
);

$courseid = optional_param(
    'courseid',
    0,
    PARAM_INT
);

echo $OUTPUT->header();

echo $OUTPUT->heading(
    'Certificados'
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
    'Seleccionar curso'
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

echo html_writer::select(

    $options,

    'courseid',

    $courseid,

    ['0' => 'Seleccione un curso'],

    [

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

        '<strong>Curso:</strong> ' .

        format_string(
            $course->fullname
        ),

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

            'Este curso aún no tiene configuración FANAFESA.',

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

                'No hay alumnos inscritos.',

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

                'Alumno',

                'Correo',

                'Certificado'

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

                $button =
                    html_writer::link(

                        $downloadurl,

                        'PDF',

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

                'ZIP seleccionados',

                [

                    'type' => 'submit',

                    'name' => 'action',

                    'value' => 'selected',

                    'class' => 'btn btn-primary me-2'

                ]

            );

            echo html_writer::tag(

                'button',

                'ZIP completo',

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

echo $OUTPUT->footer();