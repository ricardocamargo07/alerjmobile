<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController {

	use DispatchesCommands, ValidatesRequests;

	public function response($result)
	{
		$response = response()->json($result);

		if (Input::get('callback'))
		{
			$response->setCallback(Input::get('callback'));
		}

		return $response;
	}

}
