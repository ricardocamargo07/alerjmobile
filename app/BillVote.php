<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillVote extends Model {

	protected $table = 'bills_votes';

	protected $fillable = ['bill_id', 'congressman_id', 'vote'];

	public function bill()
	{
		return $this->belongsTo('App\Bill');
	}

	public function congressman()
	{
		return $this->belongsTo('App\Congressman');
	}

}
