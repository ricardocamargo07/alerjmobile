<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;

class PlenarySessionPresence extends Model {

	protected $table = 'plenary_sessions_presents';

	protected $fillable = ['date'];

	public function plenarySession()
	{
		return $this->belongsTo('App\PlenarySession');
	}

	public function congressman()
	{
		return $this->belongsTo('App\Congressman');
	}

	public function party()
	{
		return $this->belongsTo('App\Party');
	}

}
