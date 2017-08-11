<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Proderj as ProderjService;

class Proderj extends Controller
{
    public function service($service)
    {
        return app(ProderjService::class)->service($service);
	}
}
