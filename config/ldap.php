<?php

return [

    'route_prefix' => env('LDAP_ROUTE_PREFIX'),

    // Your account suffix, for example: jdoe@corp.acme.org
    'account_suffix'        => '@alerj.gov.br',

    // The domain controllers option is an array of your LDAP hosts. You can
    // use the either the host name or the IP address of your host.
    'domain_controllers'    => [
        'al15.alerj.gov.br'
    ],

    // The base distinguished name of your domain.
    'base_dn' => 'dc=alerj,dc=gov,dc=br',

    // The account to use for querying / modifying LDAP records. This
    // does not need to be an actual admin account.
    'admin_username' => 'admin',
    'admin_password' => 'password',

];
