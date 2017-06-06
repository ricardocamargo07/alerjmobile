<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;

class Discourse extends Model
{
	protected $table = 'discourses';

	protected $fillable = [];

	public function congressman()
	{
		return $this->belongsTo(Congressman::class);
	}
}
