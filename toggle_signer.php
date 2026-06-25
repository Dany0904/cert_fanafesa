<?php

require('../../config.php');

$id = required_param(
    'id',
    PARAM_INT
);

$active = required_param(
    'active',
    PARAM_INT
);

require_login();

\local_cert_fanafesa\signer_manager::set_active(
    $id,
    $active
);

redirect(
    new moodle_url(
        '/local/cert_fanafesa/signers.php'
    )
);