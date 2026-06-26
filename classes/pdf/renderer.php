<?php

namespace local_cert_fanafesa\pdf;

defined('MOODLE_INTERNAL') || die();

class renderer {

    public static function render(array $data): string {
        global $OUTPUT;

        $view = certificate_view_builder::build($data);

        return $OUTPUT->render_from_template(
            'local_cert_fanafesa/plantilla',
            $view
        );
    }
}