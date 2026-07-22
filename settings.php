<?php

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $pluginname = get_string('pluginname', 'local_cert_fanafesa');

    $settings = new admin_settingpage(
        'local_cert_fanafesa_settings',
        $pluginname
    );

    $ADMIN->add(
        'localplugins',
        $settings
    );

    $settings->add(
        new admin_setting_heading(
            'local_cert_fanafesa_dashboard',
            get_string('pluginname', 'local_cert_fanafesa'),
            html_writer::link(
                new moodle_url('/local/cert_fanafesa/index.php'),
                get_string('gotodashboard', 'local_cert_fanafesa')
            )
        )
    );
}