<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class TVAlerj extends Controller
{
    public function data()
    {
        return response()->json([
            'video' => [
                'id' => config('app.tv.video.id')
            ]
        ]);
    }
}
