<?php

namespace App\Services;

class HttpClient {

	public function getArray($url)
	{
		return json_decode($this->getRaw($url), true);
	}

	public function getRaw($url)
	{
		return file_get_contents($url);
	}

}
