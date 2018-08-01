<?php

return [

    'route_prefix' => env('LDAP_ROUTE_PREFIX'),

    'user' => env('LDAP_USER'),

    'password' => env('LDAP_PASSWORD'),

    // Your account suffix, for example: jdoe@corp.acme.org
    'account_suffix'        => '@alerj.gov.br',

    // The domain controllers option is an array of your LDAP hosts. You can
    // use the either the host name or the IP address of your host.
    'domain_controllers'    => [
        '10.17.90.14',
        '10.17.90.15',
        '10.17.90.24',
        '10.17.90.25',
    ],

    // The base distinguished name of your domain.
    'base_dn' => 'dc=alerj,dc=gov,dc=br',

];
