<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;

class TVAlerj extends Model
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
