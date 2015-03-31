<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Regiment extends Model {

	protected $table = 'regiment';

	protected $fillable = ['position', 'level', 'document_id', 'title', 'page'];

	public function congressmen()
	{
		return $this->hasMany('App\Congressman');
	}

}
