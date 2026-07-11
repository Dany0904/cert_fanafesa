<?php

require('../../config.php');

require_login();
require_sesskey();

global $DB;

$courseid = required_param('courseid', PARAM_INT);
$usecustombutton = required_param(
    'usecustombutton',
    PARAM_INT
);

$record = $DB->get_record(
    'local_cert_fanafesa_course',
    ['courseid' => $courseid]
);

if (!$record) {

    $record = new stdClass();

    $record->courseid = $courseid;
    $record->instructorid = 0;
    $record->patronid = 0;
    $record->trabajadoresid = 0;
    $record->usecustombutton = $usecustombutton;
    $record->timecreated = time();
    $record->timemodified = time();

    $DB->insert_record(
        'local_cert_fanafesa_course',
        $record
    );

} else {

    $record->usecustombutton = $usecustombutton;
    $record->timemodified = time();

    $DB->update_record(
        'local_cert_fanafesa_course',
        $record
    );

}

redirect(
    new moodle_url('/local/cert_fanafesa/courses.php')
);