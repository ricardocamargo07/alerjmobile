<?php

namespace App\Services;

use GuzzleHttp\Client;

class Downloader
{
    private $client;

    private $body;

    public function __construct()
    {
        $this->client = new Client();
    }
    
    public function fetch($url)
    {
        $result = $this->client->request('GET', $url);

        if (($this->statusCode = $result->getStatusCode()) == 200)
        {
            $this->body = (string) $result->getBody();
        }

        return $this;
    }

    public function toArray()
    {
        return json_decode($this->toJson(), true);
    }

    public function toJson()
    {
        return $this->body;
    }

    public function __toString()
    {
        return $this->body;
    }
}
