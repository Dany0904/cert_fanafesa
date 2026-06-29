<?php

namespace local_cert_fanafesa\output;

defined('MOODLE_INTERNAL') || die();

class renderer extends \mod_customcert\output\renderer {

    /**
     * Override del render principal de la vista del certificado
     */
    public function render_view_page(\mod_customcert\output\view_page $page) {

        global $PAGE;

        // Render original de customcert
        $output = parent::render_view_page($page);

        // Solo en página correcta (extra seguridad)
        if ($PAGE->pagetype !== 'mod-customcert-view') {
            return $output;
        }

        $cmid = $page->cm->id ?? null;
        if (!$cmid) {
            return $output;
        }

        // Tu URL personalizada
        $url = new \moodle_url('/local/cert_fanafesa/download.php', [
            'cmid' => $cmid
        ]);

        // Tu botón
        $custombutton = \html_writer::link(
            $url,
            'Descargar certificado FANAFESA',
            [
                'class' => 'btn btn-success ml-2',
                'target' => '_blank'
            ]
        );

        /**
         * Inyectamos el botón al HTML final
         * (esto evita tocar templates o JS)
         */
        $output .= \html_writer::div(
            $custombutton,
            'fanafesa-custom-download'
        );

        return $output;
    }
}