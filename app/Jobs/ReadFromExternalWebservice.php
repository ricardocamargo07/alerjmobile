<?php

namespace App\Jobs;

use App\Services\Proderj;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ReadFromExternalWebservice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var
     */
    public $service;

    /**
     * @var
     */
    public $key;

    /**
     * @var
     */
    public $url;

    /**
     * Create a new job instance.
     *
     * @param $service
     * @param $key
     * @param $url
     */
    public function __construct($service, $key, $url)
    {
        $this->service = $service;

        $this->key = $key;

        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app(Proderj::class)->readAndCache($this->service, $this->key, $this->url);
    }
}
