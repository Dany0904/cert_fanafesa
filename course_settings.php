<?php

require('../../config.php');

require_login();

$context = context_system::instance();

$courseid = required_param(
    'courseid',
    PARAM_INT
);

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
    'Configuración FANAFESA'
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
        'Configuración guardada'
    );
}

/*
|--------------------------------------------------------------------------
| Render
|--------------------------------------------------------------------------
*/

echo $OUTPUT->header();

echo $OUTPUT->heading(
    'Configuración de firmantes'
);

echo html_writer::div(
    '<strong>Curso:</strong> ' .
    format_string(
        $course->fullname
    ),
    'alert alert-info'
);

$form->display();

echo $OUTPUT->footer();