<?php

namespace App\Http\Controllers\Api;

use App\Regiment as RegimentModel;
use App\Http\Controllers\Controller;

class Regiment extends Controller {

	public function all()
	{
		return RegimentModel::orderBy('id')->get();
	}

	public function find($id)
	{
		return RegimentModel::find($id);
	}

}
