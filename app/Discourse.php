<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discourse extends Model {

	protected $table = 'discourses';

	protected $fillable = [];

	public function congressman()
	{
		return $this->belongsTo('App\Congressman');
	}
}
