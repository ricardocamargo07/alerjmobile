<?php

namespace App\Services;

use Cache;
use Route;
use App\Congressman;
use Illuminate\Http\Request;
use GuzzleHttp\Client as Guzzle;
use App\Jobs\ReadFromExternalWebservice;

class Proderj
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

    /**
     * @param $service
     * @param $key
     * @param $url
     * @return mixed
     */
    protected function getCached($service, $key, $url)
    {
        if ($cached = Cache::get($key)) {
            dispatch(new ReadFromExternalWebservice($service, $key, $url));

            info('CACHE HIT for ' . $url);

            return $cached;
        }

        info('CACHE MISS for ' . $url);

        return $this->readAndCache($key, $service, $url);
    }

    /**
     * @param $service
     * @param $url
     * @return \Closure
     */
    protected function getReaderClosure($service, $url)
    {
        return function () use ($service, $url) {
            return response(
                $this->readFromAlerjService($url),
                200
            )->header('Content-Type', 'text/json');
        };
    }

    private function makeCacheKey($string)
    {
        return hash('sha256', $string);
    }

    public function readAndCache($key, $service, $url)
    {
        $callable = $this->getReaderClosure($service, $url);

        $value = $callable();

        Cache::forever($key, $value);

        return $value;
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

        return $this->getCached($service, $key, $url);
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
