<?php

namespace App\Services;

use GuzzleHttp\Client as Guzzle;

class HttpClient
{
    private $guzzle;

    public function __construct()
    {
        $this->instantiateGuzzle();
    }

    private function bodyToArray($body)
    {
        return json_decode($this->bodyToJson($body), true);
    }

    private function bodyToJson($body)
    {
        return $body;
    }

    public function request($method, $uri = '', array $options = [])
    {
        return $this->guzzle->request($method, $uri, $options);
    }

    public function requestJson($url)
    {
        $response = $this->request('GET', $url);

        $data = [
            'status_code' => $response->getStatusCode(),
            'success' => ($success = $response->getStatusCode() == 200),
            'data' => [],
        ];

        if ($success) {
            $data['data'] = $this->bodyToArray((string) $response->getBody());
        }

        return $data;
    }

    public function getArray($url)
    {
        return $this->toJson($this->getRaw($url));
    }

    public function getRaw($url)
    {
        $response = $this->request('GET', $url, [
            'connect_timeout' => 20,
            'read_timeout' => 20,
        ]);

        return (string) $response->getBody();
    }

    private function instantiateGuzzle()
    {
        $this->guzzle = new Guzzle();
    }

    private function sanitizeJson($data)
    {
        $data = str_replace("\n", '', $data);

        $data = str_replace('=\>', ' - ', $data);

        return $data;
    }

    /**
     * @param $data
     * @return mixed
     */
    private function toJson($data)
    {
        return json_decode($this->sanitizeJson($data), true);
    }
}
