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
        info('line 1');
        $key = $this->makeKey(($username = $request->get('username')).($system = $request->get('system')));

        info('line 2');
//        if ($cached = $this->getCached($key))
//        {
//            info('line 3');
//            return $cached;
//        }

        info('line 4');
        info("---- username: $username -- system: $system -- key: ".gettype($key));
        info('---- type = $key == '.gettype($key));
        info($key);

        $result = $admUserService->getPermissions($username, $system);

        info('line 4.5');
        info('---- type = $key == '.gettype($result));
        info($result);

        $result = array_utf8_converter($result);

        info('line 5');
        info('---- type = $result '.gettype($result));
        info($result);

        $this->cache($key, $result);

        $result = json_encode($result);

        info('line 6');
        info('---- type = $result '.gettype($result));
        info('---- json error = '.json_last_error_msg());
        info($result);

        return $result;
    }
}
