<?php

namespace App\Http\Controllers\Api;

use DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class Bills extends Controller {

	public function votes($proposition)
	{
		$votes = DB::table('congressmen')
					->select([
						'congressmen.id',
						'congressmen.email',
						'congressmen.name',
						'congressmen.party_id',
						'bills_votes.vote',
					    DB::raw('
					        (CASE WHEN plenary_sessions_presents.id is not NULL THEN
							   TRUE
							else
							   FALSE
							END) as present'
						)
					])
					->leftJoin('bills_votes', 'congressmen.id', '=', 'bills_votes.congressman_id')
					->leftJoin('plenary_sessions', 'plenary_sessions.date', '=', DB::raw("'".Carbon::now()->toDateString()."'"))
					->leftJoin('plenary_sessions_presents', function($join) {
						$join->on('plenary_sessions_presents.plenary_session_id', '=', 'plenary_sessions.id');
						$join->on('plenary_sessions_presents.congressman_id', '=', 'congressmen.id');
					})
					->orderBy('congressmen.name')
					->get();

		$parties = DB::table('parties')
					->get();

		return compact('votes', 'parties');
	}

}
