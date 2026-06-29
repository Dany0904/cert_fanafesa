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
        $companyinfo = self::get_company_info($empresa);

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

     /*    if (
            empty($instructorid) ||
            empty($patronid) ||
            empty($trabajadoresid)
        ) {

            throw new \moodle_exception(
                'missingsigners',
                'local_cert_fanafesa'
            );
        }
 */
        /*
         * Custom fields del curso
         */
        $customfields = self::get_course_custom_fields($courseid);

        $lastname = trim($user->lastname ?? '');
        $firstname = trim($user->firstname ?? '');

        $fullname = trim($lastname . ' ' . $firstname);

        $periodoinicio = !empty($customfields['periodoinicio'])
            ? userdate((int)$customfields['periodoinicio'], '%d/%m/%Y')
            : '';

        $periodofinal = !empty($customfields['periodofinal'])
            ? userdate((int)$customfields['periodofinal'], '%d/%m/%Y')
            : '';

        [$di, $mi, $ai] = array_pad(
            explode('/', $periodoinicio),
            3,
            ''
        );

        [$df, $mf, $af] = array_pad(
            explode('/', $periodofinal),
            3,
            ''
        );

        return [
            // Usuario
            'fullname' => $fullname,
            'curp' => $profile->curp ?? '',
            'puesto' => $profile->puesto ?? '',
            'ocupacion' => $profile->ocupacion ?? '',

            // Curso
            'coursename' => $course->fullname,
            'duracion' => $customfields['duracion'] ?? '',
            'periodoinicio' => $periodoinicio,
            'periodofinal' => $periodofinal,

            'inicioanio' => $ai,
            'iniciomes' => $mi,
            'iniciodia' => $di,

            'finalanio' => $af,
            'finalmes' => $mf,
            'finaldia' => $df,

            'areatematica' => $customfields['areatematica'] ?? '',

            // Firmantes (texto)
            'instructor' => $instructor->fullname ?? '',
            'patron' => $patron->fullname ?? '',
            'trabajadores' => $trabajadores->fullname ?? '',

            // Firmas (URLs)
            'instructorsignature' => signer_manager::get_signature_base64($courseconfig->instructorid),
            'patronsignature' => signer_manager::get_signature_base64($courseconfig->patronid),
            'workerssignature' => signer_manager::get_signature_base64($courseconfig->trabajadoresid),

            'companylogo' => $companyinfo['logo'],
            'empresa' => $empresa,
            'rfcempresa' => $companyinfo['rfc'],
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

    private static function get_company_info(string $company): array {

        global $CFG;

        $companies = [

            'FARMACOS ESPECIALIZADOS, S.A. DE C.V.' => [

                'logo' => 'f_espec.png',

                'rfc' => 'FES-840823-HH0',

            ],

            'FARMACOS NACIONALES, S.A. DE C.V.' => [

                'logo' => 'fanasa.png',

                'rfc' => 'FNA-951220-DA9',

            ],

            'FEFASA CSC, S.A. DE C.V.' => [

                'logo' => 'fefasa_csc.png',

                'rfc' => 'OSO-210414-QC3',

            ],

            'GRUPO ACTIFARMA, S.A. DE C.V.' => [

                'logo' => 'actifarma.png',

                'rfc' => 'GAC-091015-R25',

            ],

            'LOGISTICA Y DISTRIBUCION 360, S.A. DE C.V.' => [

                'logo' => 'ld360.png',

                'rfc' => 'LDT-190625-G71',

            ],

        ];

        $info = $companies[$company] ?? [

            'logo' => 'default.png',

            'rfc' => '',

        ];

        $path = $CFG->dirroot .
            '/local/cert_fanafesa/pix/logos/' .
            $info['logo'];

        $logo = null;

        if (file_exists($path)) {

            $logo = 'data:image/png;base64,' .
                base64_encode(
                    file_get_contents($path)
                );

        }

        return [

            'logo' => $logo,

            'rfc' => $info['rfc'],

        ];

    }
}