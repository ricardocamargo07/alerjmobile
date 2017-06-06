<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model {

	protected $table = 'bills';

	protected $fillable = ['name', 'congressman_id'];

	public function congressman()
	{
		return $this->belongsTo('App\Congressman');
	}

}
