<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Services\AdmUser as AdmUserService;

class AdmUser extends Controller
{
    private function cache($key, $result)
    {
        Cache::put($key, $result, 10);
    }

    private function getCached($key)
    {
        return Cache::get($key);
    }

    private function makeKey($string)
    {
        return sha1($string);
    }

    public function permissions(AdmUserService $admUserService, Request $request)
    {
        $key = $this->makeKey('permissions-'.($username = $request->get('username')).($system = $request->get('system')));

        if ($cached = $this->getCached($key))
        {
            return $cached;
        }

        $result = $admUserService->getPermissions($username, $system);

        $result = array_utf8_converter($result);

        $this->cache($key, $result);

        $result = json_encode($result);

        return $result;
    }

    public function profiles(AdmUserService $admUserService, Request $request)
    {
        $key = $this->makeKey('profiles-'.($username = $request->get('username')).($system = $request->get('system')));

        if ($cached = $this->getCached($key))
        {
            return $cached;
        }

        $result = $admUserService->getProfiles($username, $system);

        $result = array_utf8_converter($result);

        $this->cache($key, $result);

        $result = json_encode($result);

        return $result;
    }
}
