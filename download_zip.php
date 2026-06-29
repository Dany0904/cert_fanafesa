<?php

require('../../config.php');

require_login();

global $CFG;

require_once(
    $CFG->libdir . '/filelib.php'
);

$courseid = required_param(

    'courseid',

    PARAM_INT

);

$action = required_param(

    'action',

    PARAM_ALPHA

);

$course = get_course(

    $courseid

);

$userids = [];

/*
|--------------------------------------------------------------------------
| Todos los inscritos
|--------------------------------------------------------------------------
*/

if (

    $action === 'all'

) {

    $context =

        context_course::instance(

            $courseid

        );

    $users =

        get_enrolled_users(

            $context

        );

    foreach (

        $users as $user

    ) {

        $userids[] =

            $user->id;

    }

}

/*
|--------------------------------------------------------------------------
| Seleccionados
|--------------------------------------------------------------------------
*/

else {

    $userids =

        optional_param_array(

            'users',

            [],

            PARAM_INT

        );

}

if (

    empty($userids)

) {

    throw new moodle_exception(

        'nousersselected',

        'local_cert_fanafesa'

    );

}

/*
|--------------------------------------------------------------------------
| ZIP temporal
|--------------------------------------------------------------------------
*/

$tempdir = make_request_directory();

$zipname = clean_filename(

    format_string(

        $course->shortname

    )

)

. '_certificados.zip';

$zippath =

    $tempdir .

    '/' .

    $zipname;

$zip = new ZipArchive();

$result = $zip->open(

    $zippath,

    ZipArchive::CREATE |

    ZipArchive::OVERWRITE

);

if (

    $result !== true

) {

    throw new moodle_exception(

        'cannotcreatezip',

        'error'

    );

}

/*
|--------------------------------------------------------------------------
| PDFs
|--------------------------------------------------------------------------
*/

foreach (

    $userids as $userid

) {

    $user = core_user::get_user(

        $userid,

        '*',

        MUST_EXIST

    );

    $pdf =

        \local_cert_fanafesa\certificate_generator::generate(

            $userid,

            $courseid

        );

    $filename = clean_filename(

        fullname(

            $user

        )

    )

    . '.pdf';

    $zip->addFromString(

        $filename,

        $pdf

    );

}

$zip->close();

/*
|--------------------------------------------------------------------------
| Descargar
|--------------------------------------------------------------------------
*/

send_temp_file(

    $zippath,

    $zipname

);