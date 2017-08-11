<?php

namespace App\Http\Controllers\Api;

use Cache;
use Route;
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

    private function rebuildQueryString($string)
    {
        return collect(explode('&', $string))->reject(function($item) {
            return starts_with($item, '_=');
        })->implode('&', $string);
    }

    public function service($service)
    {
        $parameters = Route::current()->parameters();

        $queryString = $this->rebuildQueryString($this->request->getQueryString());

        $key = $this->makeCacheKey(
            $url = static::ALERJ_SERVICE_BASE_URL."/".implode('/', $parameters)."?$queryString"
        );

        return Cache::remember($key, 10, function() use ($service, $url) {
            info('CACHE MISS for '.$url);

            return response(
                $this->readFromAlerjService($url),
                200
            )->header('Content-Type', 'text/json');;
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
