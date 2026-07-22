<?php

require('../../config.php');

require_login();

global $USER;

$cmid = optional_param(
    'cmid',
    0,
    PARAM_INT
);

$userid = optional_param(
    'userid',
    0,
    PARAM_INT
);

$courseid = optional_param(
    'courseid',
    0,
    PARAM_INT
);

/*
|--------------------------------------------------------------------------
| Descarga desde CustomCert
|--------------------------------------------------------------------------
*/

if ($cmid) {

    $cm = get_coursemodule_from_id(

        'customcert',

        $cmid,

        0,

        false,

        MUST_EXIST

    );

    $course = get_course(
        $cm->course
    );

    require_login(

        $course,

        true,

        $cm

    );

    $userid = $USER->id;

    $courseid = $course->id;

    // Registrar únicamente la primera descarga del alumno.
    \local_cert_fanafesa\download_manager::register_first_download(
        $userid,
        $courseid
    );

}

/*
|--------------------------------------------------------------------------
| Descarga administrativa
|--------------------------------------------------------------------------
*/

else if (

    $userid &&

    $courseid

) {

    $course = get_course(
        $courseid
    );

    require_login();

    /*
    require_capability(

        'local/cert_fanafesa:manage',

        context_system::instance()

    );
    */

}

/*
|--------------------------------------------------------------------------
| Error
|--------------------------------------------------------------------------
*/

else {

    throw new moodle_exception(

        'missingparam'

    );

}

\local_cert_fanafesa\certificate_generator::download(

    $userid,

    $courseid

);