<?php

namespace App\Http\Controllers\Api;

use DB;
use App\Bill;
use Carbon\Carbon;
use App\PlenarySession;
use App\Http\Controllers\Controller;

class Bills extends Controller {

	public function votes($proposition)
	{
		$bill = Bill::first();

		$plenarySession = PlenarySession::first();

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

//		DB::listen(function($sql, $bindings, $time) { dd($sql); dd($bindings); });

		$parties = DB::table('parties')
					->select([
						'parties.id',
						'parties.name',
						DB::raw('(select count(*) from congressmen as c_ where c_.party_id = parties.id) as congressmen_count'),
						DB::raw("(select count(*) from bills_votes where bills_votes.bill_id = ".$bill->id." and bills_votes.vote = 'yes'  and bills_votes.congressman_id in (select id from congressmen as c_ where c_.party_id = parties.id)) as yes"),
						DB::raw("(select count(*) from bills_votes where bills_votes.bill_id = ".$bill->id." and bills_votes.vote = 'no' and bills_votes.congressman_id in (select id from congressmen as c_ where c_.party_id = parties.id)) as no"),
						DB::raw("(select count(*) from bills_votes where bills_votes.bill_id = ".$bill->id." and bills_votes.vote = 'refrained' and bills_votes.congressman_id in (select id from congressmen as c_ where c_.party_id = parties.id)) as refrained"),
						DB::raw('(select count(*) from congressmen where congressmen.id in (select id from congressmen as c_ where c_.party_id = parties.id) and congressmen.id not in (select congressman_id from bills_votes where bill_id = '.$bill->id.') and congressmen.id     in (select congressman_id from plenary_sessions_presents where plenary_session_id = '.$plenarySession->id.')) as notvoted'),
						DB::raw('(select count(*) from congressmen where congressmen.id in (select id from congressmen as c_ where c_.party_id = parties.id) and congressmen.id not in (select congressman_id from bills_votes where bill_id = '.$bill->id.') and congressmen.id not in (select congressman_id from plenary_sessions_presents where plenary_session_id = '.$plenarySession->id.')) as absent'),
					])
					->orderBy('congressmen_count', 'desc')
					->orderBy('parties.name')
					->get();

		return compact('votes', 'parties');
	}

}
