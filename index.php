<?php

require('../../config.php');

require_login();

$context = context_system::instance();

/* require_capability(
    'local/cert_fanafesa:manage',
    $context
); */

$PAGE->set_context($context);

$PAGE->set_url(
    new moodle_url('/local/cert_fanafesa/index.php')
);

$PAGE->set_pagelayout('admin');

$PAGE->set_title('Certificados Fanafesa');

$PAGE->set_heading('Certificados Fanafesa');

echo $OUTPUT->header();

echo html_writer::tag(
    'h2',
    'Certificados Fanafesa'
);

echo html_writer::tag(
    'p',
    'Plugin en construcción'
);

echo html_writer::link(
    new moodle_url(
        '/local/cert_fanafesa/signers.php'
    ),
    'Administrar firmantes',
    ['class' => 'btn btn-primary']
);

echo $OUTPUT->footer();