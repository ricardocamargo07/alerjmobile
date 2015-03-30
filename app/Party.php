<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Party extends Model {

	protected $table = 'parties';

	protected $fillable = ['name', 'leader'];

	public function congressmen()
	{
		return $this->hasMany('App\Congressman');
	}

}
