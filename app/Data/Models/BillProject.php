<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;

class BillProject extends Model
{
	protected $table = 'bill_projects';

	protected $fillable = [
        'code',
        'description',
        'date',
        'url',
        'year',
        'month',
        'number',
    ];
}
