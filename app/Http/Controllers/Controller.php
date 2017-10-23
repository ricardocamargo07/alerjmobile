<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController
{
	use ValidatesRequests;

	public function response($result, $status = 200)
	{
		$response = response(json_encode((array) $result), $status);

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

    public function respondWithSuccess($data)
    {
        return $this->response(
            $this->responseArray(
                true, 200, null, $data
            )
        );
    }

    public function respondWithError($message = '', $code = 302)
    {
        return $this->response(
            $this->responseArray(false, $code, $message),
            $code
        );
    }

    public function responseArray($success = true, $code = 200, $message = null, $data = [])
    {
        return [
            'success' => $success,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];
    }
}
