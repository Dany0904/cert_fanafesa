<?php

namespace local_cert_fanafesa;

defined('MOODLE_INTERNAL') || die();

class course_manager {

    public static function get(
        int $courseid
    ) {

        global $DB;

        return $DB->get_record(
            'local_cert_fanafesa_course',
            [
                'courseid' => $courseid
            ]
        );
    }

    public static function save(
        int $courseid,
        ?int $instructorid,
        ?int $patronid,
        ?int $trabajadoresid
    ) {

        global $DB;

        $existing = self::get($courseid);

        if ($existing) {

            $existing->instructorid = $instructorid;
            $existing->patronid = $patronid;
            $existing->trabajadoresid = $trabajadoresid;
            $existing->timemodified = time();

            return $DB->update_record(
                'local_cert_fanafesa_course',
                $existing
            );
        }

        $record = new \stdClass();

        $record->courseid = $courseid;
        $record->instructorid = $instructorid;
        $record->patronid = $patronid;
        $record->trabajadoresid = $trabajadoresid;
        $record->timecreated = time();
        $record->timemodified = time();

        return $DB->insert_record(
            'local_cert_fanafesa_course',
            $record
        );
    }

}