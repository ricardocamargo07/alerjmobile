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
        'year',
        'month',
        'authors',
        'number',
        'url',
        'site_url',
    ];

    public static function getColumns()
    {
        return (new static())->getFillable();
    }
}
