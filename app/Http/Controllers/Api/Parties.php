<?php

namespace App\Http\Controllers\Api;

use App\Party;
use App\Http\Controllers\Controller;

class Parties extends Controller {

	public function all()
	{
		return Party::with('congressmen')->get();
	}

}
