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

    /**
     * @var Request
     */
    private $request;

    public function __construct(LdapService $ldap, Request $request)
    {
        $this->ldap = $ldap;

        $this->request = $request;
    }

    /**
     * @return bool
     */
    private function attempt()
    {
        return $this->ldap->login(
            $this->username = $this->request->get('username'),
            $this->password = $this->request->get('password')
        );
    }

    public function user()
    {
        $this->username = $this->request->get('username');

        return $this->getUserData();
    }

    private function getUserData()
    {
        if (!$data = $this->ldap->findUser($this->username)) {
            abort(404);
        }

        return [
            'name' => $data['displayname'],
            'email' => $data['mail'],
            'memberof' => $data['memberof'],
            'description' => $data['description'],
        ];
    }

    public function login()
    {
        return $this->attempt()
            ? $this->respondWithSuccess($this->getUserData())
            : $this->respondWithError('Attempt failed.', 401)
        ;
    }
}

