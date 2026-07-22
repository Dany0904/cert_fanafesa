<?php

namespace local_cert_fanafesa;

defined('MOODLE_INTERNAL') || die();

class download_manager {

    /**
     * Obtiene la fecha registrada de la primera descarga.
     *
     * No crea registros.
     *
     * @param int $userid
     * @param int $courseid
     * @return int|null Timestamp UNIX o null si aún no existe.
     */
    public static function get_download_date(
        int $userid,
        int $courseid
    ): ?int {

        global $DB;

        $record = $DB->get_record(
            'local_cert_fanafesa_download',
            [
                'userid' => $userid,
                'courseid' => $courseid
            ],
            'firstdownload'
        );

        if (!$record) {
            return null;
        }

        return (int)$record->firstdownload;
    }

    /**
     * Registra la primera descarga del certificado.
     *
     * Si ya existe, simplemente devuelve la fecha registrada.
     *
     * @param int $userid
     * @param int $courseid
     * @return int Timestamp UNIX de la primera descarga.
     */
    public static function register_first_download(
        int $userid,
        int $courseid
    ): int {

        global $DB;

        $params = [
            'userid' => $userid,
            'courseid' => $courseid
        ];

        if ($record = $DB->get_record(
            'local_cert_fanafesa_download',
            $params,
            'firstdownload'
        )) {
            return (int)$record->firstdownload;
        }

        $time = time();

        $record = (object)[
            'userid' => $userid,
            'courseid' => $courseid,
            'firstdownload' => $time,
            'timecreated' => $time,
        ];

        try {

            $DB->insert_record(
                'local_cert_fanafesa_download',
                $record
            );

            return $time;

        } catch (\dml_write_exception $e) {

            // Otro proceso pudo haber insertado el registro primero.
            $record = $DB->get_record(
                'local_cert_fanafesa_download',
                $params,
                'firstdownload',
                MUST_EXIST
            );

            return (int)$record->firstdownload;
        }
    }

    /**
     * Obtiene la fecha de la primera descarga formateada.
     *
     * @param int $userid
     * @param int $courseid
     * @return string
     */
    public static function get_download_date_formatted(
        int $userid,
        int $courseid
    ): string {

        $timestamp = self::get_download_date(
            $userid,
            $courseid
        );

        if ($timestamp === null) {
            return '-';
        }

        return userdate(
            $timestamp,
            '%d/%m/%Y'
        );
    }

    /**
     * Obtiene las fechas de primera descarga de todos los usuarios
     * inscritos en un curso.
     *
     * @param int $courseid
     * @return array [userid => timestamp]
     */
    public static function get_download_dates_by_course(
        int $courseid
    ): array {

        global $DB;

        $records = $DB->get_records(
            'local_cert_fanafesa_download',
            [
                'courseid' => $courseid
            ],
            '',
            'userid, firstdownload'
        );

        $dates = [];

        foreach ($records as $record) {
            $dates[(int)$record->userid] = (int)$record->firstdownload;
        }

        return $dates;
    }
}