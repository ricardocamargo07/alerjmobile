<?php

namespace App\Services;

class HttpClient
{
	public function getArray($url)
	{
        return $this->toJson($this->getRaw($url));
	}

	public function getRaw($url)
	{
		return file_get_contents($url);
	}

    private function sanitizeJson($data)
    {
        $data = str_replace('=\>', ' - ', $data);

        return $data;
    }

    /**
     * @param $data
     * @return mixed
     */
    private function toJson($data)
    {
        $result = json_decode($data, true);

        if (! $result)
        {
            $result = json_decode($this->sanitizeJson($data));
        }

        return $result;
    }
}
