<?php

$capabilities = array(
    'local/helloworld:postmessage' => array(
        'riskbitmask'  => RISK_SPAM | RISK_XSS,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => array(
            'user'     => CAP_ALLOW
        )
    ),
    'local/helloworld:viewmessage' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => array(
            'user'     => CAP_ALLOW
        )
    ),
    'local/helloworld:deleteanymessage' => array(
        'riskbitmask'  => RISK_DATALOSS,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => array(
            'manager'  => CAP_ALLOW
        )
    ),
);