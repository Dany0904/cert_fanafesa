<?php

namespace local_cert_fanafesa\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class signer_form extends \moodleform {

    public function definition() {

        $mform = $this->_form;

        $mform->addElement(
            'hidden',
            'id'
        );

        $mform->setType(
            'id',
            PARAM_INT
        );

        $mform->addElement(
            'text',
            'fullname',
            'Nombre completo'
        );

        $mform->setType(
            'fullname',
            PARAM_TEXT
        );

        $mform->addRule(
            'fullname',
            null,
            'required'
        );

        $mform->addElement(
            'select',
            'role',
            'Tipo de firmante',
            [
                'instructor'   => 'Instructor o tutor',
                'patron'       => 'Patrón o representante legal',
                'trabajadores' => 'Representante de los trabajadores'
            ]
        );

        $mform->addRule(
            'role',
            null,
            'required'
        );

        $mform->addElement(
            'filemanager',
            'signature',
            'Firma',
            null,
            [
                'subdirs' => 0,
                'maxfiles' => 1,
                'accepted_types' => ['.png', '.jpg', '.jpeg']
            ]
        );

        $this->add_action_buttons(
            true,
            'Guardar'
        );
    }
}