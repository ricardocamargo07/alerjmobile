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
        $key = $this->makeKey(($username = $request->get('username')).($system = $request->get('system')));

        if ($cached = $this->getCached($key))
        {
            return $cached;
        }

        $result = $admUserService->getPermissions($username, $system);

        $this->cache($key, $result);

        return $result;
    }
}
