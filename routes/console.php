<?php

use App\Services\Scrapers\Import;
use Illuminate\Foundation\Inspiring;
use Illuminate\Contracts\Queue\Factory as FactoryContract;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('app:import', function () {
    app(Import::class)->execute(
        'congressmen',
        'http://apialerj.rj.gov.br/api/deputadoservice'
    );
})->describe('Import congressmen from Proderj');

Artisan::command('queue:clear', function () {
    $count = 0;

    $connection = config('queue.default');

    $queue = config('queue.connections.' . $connection  . '.queue');

    $connection = app(FactoryContract::class)->connection($connection);

    while ($job = $connection->pop($queue)) {
        $job->delete();
        $count++;
    }

    $this->info('Deleted jobs: '.$count);
})->describe('Import congressmen from Proderj');


Artisan::command('app:test', function () {

})->describe('Import congressmen from Proderj');
