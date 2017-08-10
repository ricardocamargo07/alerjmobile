<?php

namespace App\Services\Scrapers;

use Cache;
use Carbon\Carbon;
use App\Services\HttpClient;

class Import
{
    public function __construct()
    {
        $this->httpClient = new HttpClient;
    }

    public function execute($table, $url)
    {
        $data = $this->requestData($url);

        if ($data['success']) {
            $this->importData($table, $data, $table == 'deputadoservice');
        }
	}

    private function importData($table, $data, $deletePrevious)
    {

    }

    private function requestData($url)
    {
        $response = $this->httpClient->requestJson($url);

        if ($response['success']) {

        }
    }
}
