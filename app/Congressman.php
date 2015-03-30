<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Congressman extends Model {

	protected $table = 'congressmen';

	protected $fillable = ['name', 'email', 'party_id', 'url'];

	public function party()
	{
		return $this->belongsTo('App\Party');
	}

}
