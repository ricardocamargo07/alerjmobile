<?php

namespace App\Http\Controllers\Api;

use App\Congressman;
use App\Http\Controllers\Controller;

class Bills extends Controller {

	public function __construct()
	{

	}

	public function votes($proposition)
	{
		return Congressman::select(['id', 'email', 'name', 'party_id'])->orderBy('name')->get();
	}

}
