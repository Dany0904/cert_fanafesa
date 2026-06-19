<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = [

    'local/cert_fanafesa:manage' => [
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => [
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW
        ]
    ],

];