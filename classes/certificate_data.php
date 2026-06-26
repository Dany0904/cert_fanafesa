<?php

namespace local_cert_fanafesa;

defined('MOODLE_INTERNAL') || die();

class certificate_data {

    public static function get(int $userid, int $courseid): array {

        global $DB;

        /*
         * Usuario
         */
        $user = $DB->get_record('user', [
            'id' => $userid
        ], '*', MUST_EXIST);

        /*
         * Perfil usuario
         */
        $profile = profile_user_record($userid);

        $empresa = $profile->empresa ?? '';

        /*
         * Curso
         */
        $course = get_course($courseid);

        /*
         * Configuración del curso
         */
        $courseconfig = course_manager::get($courseid);

        /*
         * Firmantes (OBJETOS COMPLETOS)
         */
        $instructor = signer_manager::get($courseconfig->instructorid);
        $patron = signer_manager::get($courseconfig->patronid);
        $trabajadores = signer_manager::get($courseconfig->trabajadoresid);


        /*
         * Custom fields del curso
         */
        $customfields = self::get_course_custom_fields($courseid);

        return [
            // Usuario
            'fullname' => fullname($user),
            'curp' => $profile->curp ?? '',
            'puesto' => $profile->puesto ?? '',
            'ocupacion' => $profile->ocupacion ?? '',

            // Curso
            'coursename' => $course->fullname,
            'duracion' => $customfields['duracion'] ?? '',
            'periodoinicio' => !empty($customfields['periodoinicio'])
                ? userdate((int)$customfields['periodoinicio'], '%d/%m/%Y')
                : '',

            'periodofinal' => !empty($customfields['periodofinal'])
                ? userdate((int)$customfields['periodofinal'], '%d/%m/%Y')
                : '',
            'areatematica' => $customfields['areatematica'] ?? '',

            // Firmantes (texto)
            'instructor' => $instructor->fullname ?? '',
            'patron' => $patron->fullname ?? '',
            'trabajadores' => $trabajadores->fullname ?? '',

            // Firmas (URLs)
            'instructorsignature' => signer_manager::get_signature_base64($courseconfig->instructorid),
            'patronsignature' => signer_manager::get_signature_base64($courseconfig->patronid),
            'workerssignature' => signer_manager::get_signature_base64($courseconfig->trabajadoresid),

            'companylogo' => self::get_company_logo($empresa),
            'empresa' => $empresa,
        ];
    }

    private static function get_course_custom_fields(int $courseid): array {

        global $DB;

        $sql = "
            SELECT
                f.shortname,
                f.type,
                f.configdata,
                d.value
            FROM {customfield_data} d
            INNER JOIN {customfield_field} f ON f.id = d.fieldid
            WHERE d.instanceid = ?
        ";

        $records = $DB->get_records_sql($sql, [$courseid]);

        $fields = [];

        foreach ($records as $record) {

            $value = $record->value;

            if ($record->type === 'select') {

                $config = json_decode($record->configdata);

                if (!empty($config->options)) {

                    $options = preg_split('/\r\n|\r|\n/', $config->options);

                    $index = (int)$value;

                    if (isset($options[$index])) {
                        $value = trim($options[$index]);
                    }
                }
            }

            $fields[$record->shortname] = $value;
        }

        return $fields;
    }

    private static function get_company_logo(string $company): ?string {

        global $CFG;

        $logos = [

            'FARMACOS ESPECIALIZADOS, S.A. DE C.V.' =>
                'f_espec.png',

            'FARMACOS NACIONALES, S.A. DE C.V.' =>
                'fanasa.png',

            'FEFASA CSC, S.A. DE C.V.' =>
                'fefasa_csc.png',

            'GRUPO ACTIFARMA, S.A. DE C.V.' =>
                'actifarma.png',

            'LOGISTICA Y DISTRIBUCION 360, S.A. DE C.V.' =>
                'ld360.png',
        ];

        $filename = $logos[$company] ?? 'default.png';

        $path = $CFG->dirroot .
            '/local/cert_fanafesa/pix/logos/' .
            $filename;

        if (!file_exists($path)) {
            return null;
        }

        return 'data:image/png;base64,' .
            base64_encode(file_get_contents($path));
    }
}