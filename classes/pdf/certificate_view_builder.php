<?php

namespace local_cert_fanafesa\pdf;

defined('MOODLE_INTERNAL') || die();

class certificate_view_builder {

    public static function build(array $data): array {

        return [
            // datos base
            'fullname' => $data['fullname'],
            'curp' => $data['curp'],
            'puesto' => $data['puesto'],
            'ocupacion' => $data['ocupacion'],

            'coursename' => $data['coursename'],
            'duracion' => $data['duracion'],
            'periodoinicio' => $data['periodoinicio'],
            'periodofinal' => $data['periodofinal'],
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

            'companylogo' => $data['companylogo']
        ];
    }
}