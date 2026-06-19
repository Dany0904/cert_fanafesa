<?php

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $ADMIN->add(
        'localplugins',
        new admin_externalpage(
            'local_cert_fanafesa',
            get_string('pluginname', 'local_cert_fanafesa'),
            new moodle_url('/local/cert_fanafesa/index.php'),
            'local/cert_fanafesa:manage'
        )
    );

}