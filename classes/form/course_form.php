<?php

namespace local_cert_fanafesa\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class course_form extends \moodleform {

    public function definition() {

        $mform = $this->_form;

        $instructors =
            $this->_customdata['instructors'];

        $patrons =
            $this->_customdata['patrons'];

        $trabajadores =
            $this->_customdata['trabajadores'];

        $mform->addElement(
            'hidden',
            'courseid'
        );

        $mform->setType(
            'courseid',
            PARAM_INT
        );

        $mform->addElement(
            'select',
            'instructorid',
            get_string('instructor', 'local_cert_fanafesa'),
            $instructors
        );

        $mform->addElement(
            'select',
            'patronid',
            get_string('patron', 'local_cert_fanafesa'),
            $patrons
        );

        $mform->addElement(
            'select',
            'trabajadoresid',
            get_string('trabajadores', 'local_cert_fanafesa'),
            $trabajadores
        );

        $this->add_action_buttons();
    }
}