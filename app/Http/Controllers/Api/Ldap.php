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

    private $username;
    private $password;

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
            $this->username = $request->get('username'),
            $this->password = $request->get('password')
        );
    }

    private function getUserData()
    {
        $data = $this->ldap->findUser($this->username);

        return [
            'name' => $data['displayname'],
            'email' => $data['mail'],
            'memberof' => $data['memberof'],
            'description' => $data['description'],
        ];
    }

    public function login(Request $request)
    {
        return $this->attempt($request)
            ? $this->respondWithSuccess($this->getUserData())
            : $this->respondWithError('Attempt failed.', 401)
        ;
    }
}
