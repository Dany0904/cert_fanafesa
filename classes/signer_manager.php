<?php

namespace local_cert_fanafesa;

defined('MOODLE_INTERNAL') || die();

class signer_manager {

    public static function get_all() {
        global $DB;

        return $DB->get_records(
            'local_cert_fanafesa_signers',
            null,
            'fullname ASC'
        );
    }

    public static function create(
        string $fullname,
        string $position
    ) {

        global $DB;

        $record = new \stdClass();

        $record->fullname = $fullname;
        $record->position = $position;
        $record->active = 1;
        $record->timecreated = time();
        $record->timemodified = time();

        return $DB->insert_record(
            'local_cert_fanafesa_signers',
            $record
        );
    }

}