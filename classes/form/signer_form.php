<?php

namespace local_cert_fanafesa\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class signer_form extends \moodleform {

    public function definition() {

        $mform = $this->_form;

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
            'text',
            'position',
            'Puesto'
        );

        $mform->setType(
            'position',
            PARAM_TEXT
        );

        $mform->addRule(
            'position',
            null,
            'required'
        );

        $this->add_action_buttons(
            true,
            'Guardar'
        );
    }
}