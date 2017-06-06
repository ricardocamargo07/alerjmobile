<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;

class BillVote extends Model {

	protected $table = 'bills_votes';

	protected $fillable = ['bill_id', 'congressman_id', 'vote', 'plenary_session_id'];

	public function bill()
	{
		return $this->belongsTo('App\Bill');
	}

	public function congressman()
	{
		return $this->belongsTo('App\Congressman');
	}

	public function plenarySession()
	{
		return $this->belongsTo('App\PlenarySession');
	}

}
