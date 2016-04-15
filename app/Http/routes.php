<?php

use App\Services\DownloadFromPortal;

Route::any('debug', function()
{
    $downloader = new DownloadFromPortal();

    $downloader->downloadDeputies();
    $downloader->downloadNews();
    $downloader->downloadSchedule();

    return '<h1>done</h1>';
});

Route::group(['prefix' => 'api/v1.0', 'namespace' => 'Api'], function()
{
	Route::get('parties', 'Parties@all');

	Route::get('congressman/profile/{id}', 'Congressmen@profile');

	Route::get('documentsPages/page/{page_id}', 'Documents@page');

    Route::get('documentsPages/{name}/{includePage?}', 'Documents@pages');

    Route::get('bills', 'Bills@all');

    Route::get('schedule', 'Schedule@all');

	Route::get('schedule/{item}', 'Schedule@item');

	Route::get('bills/{proposition}/votes', 'Bills@votes');
});

Route::any('{any?}', function()
{
	return Redirect::to(env('SITE_MAIN'));
});
