<?php

namespace App\Http\Controllers\Api;

use Cache;
use App\Congressman;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Proderj extends Controller
{
    const ALERJ_SERVICE_BASE_URL = 'http://apialerj.rj.gov.br/api';

    /**
     * @var Request
     */
    private $request;

    private $guzzle;

    public function __construct(Guzzle $guzzle, Request $request)
    {
        $this->guzzle = $guzzle;

        $this->request = $request;
    }

    private function makeCacheKey($string)
    {
        return hash('sha256', $string);
    }

    public function service($service)
    {
        $queryString = $this->request->getQueryString();

        $key = $this->makeCacheKey(
            $url = static::ALERJ_SERVICE_BASE_URL."/{$service}?$queryString"
        );

        return Cache::remember($key, 10, function() use ($service, $url) {
            return $this->readFromAlerjService($url);
        });
	}

    private function readFromAlerjService($url)
    {
        $response = $this->guzzle->request('GET', $url);

        if (! $response->getStatusCode() == 200) {
            return [];
        }

        return (string) $response->getBody();
    }
}
