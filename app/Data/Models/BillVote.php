<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;

class BillVote extends Model
{
	protected $table = 'bills_votes';

	protected $fillable = ['bill_id', 'congressman_id', 'vote', 'plenary_session_id'];

	public function bill()
	{
		return $this->belongsTo(Bill::class);
	}

	public function congressman()
	{
		return $this->belongsTo(Congressman::class);
	}

	public function plenarySession()
	{
		return $this->belongsTo(PlenarySession::class);
	}
}
