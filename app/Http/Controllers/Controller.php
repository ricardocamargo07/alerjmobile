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

		if ($callback = Input::get('callback'))
		{
			$response->setCallback($callback);
		}

		return $response;
	}

	public function removeUneededLinks($text)
	{
		$part = '<a href="#TOPO"><img src="/icons/2-barrinha-topo.gif" border="0"></a></center>';

		$text = str_replace($part, '', $text);

		return $text;
	}

    public function respondWithError($message = '', $code = 200)
    {
        return $this->response(
            $this->responseArray(false, 302, $message)
        );
    }

    public function responseArray($success = true, $code = 200, $message = null)
    {
        return [
            'message' => $message,
            'success' => $success,
            'code' => $code,
        ];
    }
}
