<?php

require('../../config.php');

require_login();

$context = context_system::instance();

$courseid = required_param('courseid', PARAM_INT);

if (!$course = get_course($courseid)) {
    throw new moodle_exception('invalidcourseid');
}

$course = get_course(
    $courseid
);

$PAGE->set_context(
    $context
);

$PAGE->set_url(
    new moodle_url(
        '/local/cert_fanafesa/course_settings.php',
        [
            'courseid' => $courseid
        ]
    )
);

$PAGE->set_pagelayout(
    'admin'
);

$PAGE->set_title(
    get_string('coursesettings', 'local_cert_fanafesa')
);

$PAGE->set_heading(
    $course->fullname
);

/*
|--------------------------------------------------------------------------
| Firmantes por rol
|--------------------------------------------------------------------------
*/

$instructorrecords =
    \local_cert_fanafesa\signer_manager::get_by_role(
        'instructor'
    );

$patronrecords =
    \local_cert_fanafesa\signer_manager::get_by_role(
        'patron'
    );

$trabajadoresrecords =
    \local_cert_fanafesa\signer_manager::get_by_role(
        'trabajadores'
    );

/*
|--------------------------------------------------------------------------
| Opciones selects
|--------------------------------------------------------------------------
*/

$instructoroptions = [];

foreach ($instructorrecords as $record) {

    $instructoroptions[
        $record->id
    ] = $record->fullname;
}

$patronoptions = [];

foreach ($patronrecords as $record) {

    $patronoptions[
        $record->id
    ] = $record->fullname;
}

$trabajadoresoptions = [];

foreach ($trabajadoresrecords as $record) {

    $trabajadoresoptions[
        $record->id
    ] = $record->fullname;
}

/*
|--------------------------------------------------------------------------
| Formulario
|--------------------------------------------------------------------------
*/

$form =
    new \local_cert_fanafesa\form\course_form(
        null,
        [
            'instructors' => $instructoroptions,
            'patrons' => $patronoptions,
            'trabajadores' => $trabajadoresoptions
        ]
    );

/*
|--------------------------------------------------------------------------
| Cargar configuración existente
|--------------------------------------------------------------------------
*/

$config =
    \local_cert_fanafesa\course_manager::get(
        $courseid
    );

if ($config) {

    $data = new stdClass();

    $data->courseid =
        $courseid;

    $data->instructorid =
        $config->instructorid;

    $data->patronid =
        $config->patronid;

    $data->trabajadoresid =
        $config->trabajadoresid;

    $form->set_data(
        $data
    );
} else {

    $data = new stdClass();

    $data->courseid =
        $courseid;

    $form->set_data(
        $data
    );
}

/*
|--------------------------------------------------------------------------
| Cancelar
|--------------------------------------------------------------------------
*/

if ($form->is_cancelled()) {

    redirect(
        new moodle_url(
            '/course/view.php',
            [
                'id' => $courseid
            ]
        )
    );
}

/*
|--------------------------------------------------------------------------
| Guardar
|--------------------------------------------------------------------------
*/

if ($data = $form->get_data()) {

    \local_cert_fanafesa\course_manager::save(
        $courseid,
        $data->instructorid,
        $data->patronid,
        $data->trabajadoresid
    );

    redirect(
        new moodle_url(
            '/local/cert_fanafesa/course_settings.php',
            [
                'courseid' => $courseid
            ]
        ),
        get_string('configurationsaved', 'local_cert_fanafesa')
    );
}

/*
|--------------------------------------------------------------------------
| Render
|--------------------------------------------------------------------------
*/

echo $OUTPUT->header();

echo $OUTPUT->heading(
    get_string('signersconfiguration', 'local_cert_fanafesa')
);

echo html_writer::div(
    get_string('coursesettings_desc', 'local_cert_fanafesa'),
    'alert alert-info'
);

/*
|--------------------------------------------------------------------------
| Estado actual
|--------------------------------------------------------------------------
*/

$completo = false;

if ($config) {

    $completo =

        !empty($config->instructorid)

        &&

        !empty($config->patronid)

        &&

        !empty($config->trabajadoresid);

}

$estado =

    $completo

        ?

        html_writer::span(

            get_string('configurationcomplete', 'local_cert_fanafesa'),

            'badge badge-success'

        )

        :

        html_writer::span(

            get_string('configurationpending', 'local_cert_fanafesa'),

            'badge badge-warning'

        );

echo html_writer::start_div(
    'card mb-4'
);

echo html_writer::start_div(
    'card-body'
);

echo html_writer::tag(

    'h5',

    format_string(

        $course->fullname

    )

);

echo html_writer::div(

     '<strong>' .
    get_string('courseidlabel', 'local_cert_fanafesa') .
    ':</strong> '

    .

    $course->id

);

echo html_writer::div(

    '<strong>' .
    get_string('statuslabel', 'local_cert_fanafesa') .
    ':</strong> '

    .

    $estado

);

echo html_writer::end_div();

echo html_writer::end_div();

/*
|--------------------------------------------------------------------------
| Firmantes actuales
|--------------------------------------------------------------------------
*/

if ($config) {

    $instructor =

        $config->instructorid

            ?

            \local_cert_fanafesa\signer_manager::get(

                $config->instructorid

            )

            : null;

    $patron =

        $config->patronid

            ?

            \local_cert_fanafesa\signer_manager::get(

                $config->patronid

            )

            : null;

    $trabajadores =

        $config->trabajadoresid

            ?

            \local_cert_fanafesa\signer_manager::get(

                $config->trabajadoresid

            )

            : null;

    echo html_writer::start_div(
        'card mb-4'
    );

    echo html_writer::start_div(
        'card-body'
    );

    echo html_writer::tag(

        'h5',

        get_string('assignedsigners', 'local_cert_fanafesa')

    );

    echo html_writer::alist([

        '<strong>' .
        get_string('instructor', 'local_cert_fanafesa') .
        ':</strong> '

        .

        (

            $instructor->fullname

            ??

            '-'

        ),

        '<strong>' .
        get_string('patron', 'local_cert_fanafesa') .
        ':</strong> '

        .

        (

            $patron->fullname

            ??

            '-'

        ),

        '<strong>' .
        get_string('trabajadores', 'local_cert_fanafesa') .
        ':</strong> '

        .

        (

            $trabajadores->fullname

            ??

            '-'

        )

    ]);

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

    get_string('assignsigners', 'local_cert_fanafesa')

);

$form->display();

echo html_writer::end_div();

echo html_writer::end_div();

/*
|--------------------------------------------------------------------------
| Navegación
|--------------------------------------------------------------------------
*/

echo html_writer::div(

    html_writer::link(

        new moodle_url(

            '/local/cert_fanafesa/courses.php'

        ),

        get_string('back', 'local_cert_fanafesa'),

        [

            'class' =>

            'btn btn-light mt-3'

        ]

    )

);

echo $OUTPUT->footer();