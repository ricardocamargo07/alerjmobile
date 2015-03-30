<?php

namespace App\Http\Controllers\Api;

use App\Congressman;
use App\Http\Controllers\Controller;

class Congressmen extends Controller {

	public function profile($id)
	{
		return Congressman::where('alerj_id', $id)->first()->page;
	}

}
