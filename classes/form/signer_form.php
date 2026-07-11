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
            get_string('fullname', 'local_cert_fanafesa')
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
            get_string('signertype', 'local_cert_fanafesa'),
            [
                'instructor'   => get_string('instructor', 'local_cert_fanafesa'),
                'patron'       => get_string('patron', 'local_cert_fanafesa'),
                'trabajadores' => get_string('trabajadores', 'local_cert_fanafesa')
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
            get_string('signature', 'local_cert_fanafesa'),
            null,
            [
                'subdirs' => 0,
                'maxfiles' => 1,
                'accepted_types' => ['.png', '.jpg', '.jpeg']
            ]
        );

        $this->add_action_buttons(
            true,
            get_string('save', 'local_cert_fanafesa')
        );
    }
}