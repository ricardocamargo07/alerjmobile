<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Ldap as LdapService;

class Ldap extends Controller
{
    /**
     * @var LdapService
     */
    private $ldap;

    public function __construct(LdapService $ldap)
    {
        $this->ldap = $ldap;
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function attempt(Request $request)
    {
        return $this->ldap->login(
            $request->get('username'),
            $request->get('password')
        );
    }

    public function login(Request $request)
    {
        return $this->attempt($request)
            ? $this->respondWithSuccess()
            : $this->respondWithError('Attempt failed.', 401)
        ;
    }
}
