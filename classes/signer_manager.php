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
        string $role
    ) {

        global $DB;

        $record = new \stdClass();

        $record->fullname = $fullname;
        $record->role = $role;
        $record->active = 1;
        $record->timecreated = time();
        $record->timemodified = time();

        return $DB->insert_record(
            'local_cert_fanafesa_signers',
            $record
        );
    }

    public static function save_signature(
        int $signerid,
        int $draftitemid
    ) {

        $context = \context_system::instance();

        file_save_draft_area_files(
            $draftitemid,
            $context->id,
            'local_cert_fanafesa',
            'signature',
            $signerid,
            [
                'subdirs' => 0,
                'maxfiles' => 1
            ]
        );
    }

    public static function has_signature(
        int $signerid
    ): bool {

        $context = \context_system::instance();

        $fs = get_file_storage();

        $files = $fs->get_area_files(
            $context->id,
            'local_cert_fanafesa',
            'signature',
            $signerid,
            'id',
            false
        );

        return !empty($files);
}

}