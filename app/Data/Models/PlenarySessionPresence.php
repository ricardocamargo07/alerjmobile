<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;

class PlenarySessionPresence extends Model
{
	protected $table = 'plenary_sessions_presents';

	protected $fillable = ['date'];

	public function plenarySession()
	{
		return $this->belongsTo(PlenarySession::class);
	}

	public function congressman()
	{
		return $this->belongsTo(Congressman::class);
	}

	public function party()
	{
		return $this->belongsTo(Party::class);
	}
}
