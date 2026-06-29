<?php

namespace local_cert_fanafesa\pdf;

defined('MOODLE_INTERNAL') || die();

class certificate_view_builder {

    public static function build(array $data): array {

        $curpchars = [];

        $curp = strtoupper($data['curp']);

        for ($i = 0; $i < 18; $i++) {

            $curpchars[] = [
                'char' => $curp[$i] ?? '',
                'first' => ($i === 0)
            ];
        }

        return [
            // datos base
            'fullname' => $data['fullname'],
            'curp' => $data['curp'],
            'curpchars' => $curpchars,
            'puesto' => $data['puesto'],
            'ocupacion' => $data['ocupacion'],

            'coursename' => $data['coursename'],
            'duracion' => $data['duracion'],
            'periodoinicio' => $data['periodoinicio'],
            'periodofinal' => $data['periodofinal'],
            'inicioanio' => $data['inicioanio'],
            'iniciomes' => $data['iniciomes'],
            'iniciodia' => $data['iniciodia'],

            'finalanio' => $data['finalanio'],
            'finalmes' => $data['finalmes'],
            'finaldia' => $data['finaldia'],
            'areatematica' => $data['areatematica'],

            // nombres firmantes
            'instructor' => $data['instructor'],
            'patron' => $data['patron'],
            'worker' => $data['trabajadores'],

            // firmas (YA VIENEN LISTAS)
            'instructorsignature' => $data['instructorsignature'],
            'patronsignature' => $data['patronsignature'],
            'workerssignature' => $data['workerssignature'],

            'empresa' => $data['empresa'],
            'companylogo' => $data['companylogo'],
            'rfcempresa' => $data['rfcempresa']
        ];
    }
}