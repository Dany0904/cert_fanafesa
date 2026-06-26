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

    public static function get_signature_url(
        int $signerid
    ): ?string {

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

        if (empty($files)) {
            return null;
        }

        $file = reset($files);

        return \moodle_url::make_pluginfile_url(
            $file->get_contextid(),
            $file->get_component(),
            $file->get_filearea(),
            $file->get_itemid(),
            $file->get_filepath(),
            $file->get_filename()
        )->out(false);
    }

    public static function update(
        int $id,
        string $fullname,
        string $role
    ) {

        global $DB;

        $record = new \stdClass();

        $record->id = $id;
        $record->fullname = $fullname;
        $record->role = $role;
        $record->timemodified = time();

        return $DB->update_record(
            'local_cert_fanafesa_signers',
            $record
        );
    }

    public static function get(
        int $id
    ) {

        global $DB;

        return $DB->get_record(
            'local_cert_fanafesa_signers',
            [
                'id' => $id
            ],
            '*',
            MUST_EXIST
        );
    }

    public static function set_active(
        int $id,
        int $active
    ) {

        global $DB;

        $record = new \stdClass();

        $record->id = $id;
        $record->active = $active;
        $record->timemodified = time();

        return $DB->update_record(
            'local_cert_fanafesa_signers',
            $record
        );
    }

    public static function get_by_role(
        string $role
    ) {

        global $DB;

        return $DB->get_records(
            'local_cert_fanafesa_signers',
            [
                'role' => $role,
                'active' => 1
            ],
            'fullname ASC'
        );
    }

    public static function get_signature_file(
        int $signerid
    ) {

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

        if (empty($files)) {
            return null;
        }

        return reset($files);
    }

    public static function get_signature_base64(int $signerid): ?string {

        $file = self::get_signature_file($signerid);

        if (!$file) {
            return null;
        }

        $content = $file->get_content();

        return 'data:image/png;base64,' . base64_encode($content);
    }

}