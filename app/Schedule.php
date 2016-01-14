<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
	protected $table = 'schedule';

	protected $fillable = ['alerj_id', 'title', 'datetime', 'document'];
}
