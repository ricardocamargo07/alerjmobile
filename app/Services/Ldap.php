<?php

namespace App\Services;

use Adldap\Adldap;

class Ldap
{
    protected $ad;

    protected $message;

    /**
     * Message getter.
     *
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    protected function instantiateLdap($username, $password)
    {
        // Construct new Adldap instance.
        $this->ad = new Adldap();

        // Create a configuration array.
        $config = [
            // Your account suffix, for example: jdoe@corp.acme.org
            'account_suffix'        => config('ldap.account_suffix'),

            // The domain controllers option is an array of your LDAP hosts. You can
            // use the either the host name or the IP address of your host.
            'domain_controllers'    => config('ldap.domain_controllers'),

            // The base distinguished name of your domain.
            'base_dn'               => config('ldap.base_dn'),

            // The account to use for querying / modifying LDAP records. This
            // does not need to be an actual admin account.
            'admin_username'        => $username,
            'admin_password'        => $password,
        ];

        // Add a connection provider to Adldap.
        $this->ad->addProvider($config);
    }

    /**
     * Try to login on ldap.
     *
     * @param $username
     * @param $password
     * @return bool
     */
    public function login($username, $password)
    {
        $this->instantiateLdap($username, $password);

        try {
            $provider = $this->ad->connect();

            return $provider->auth()->attempt($username, $password);
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();

            return false;
        }
    }
}
