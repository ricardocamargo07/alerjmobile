<?php

use App\Services\Scrapers\Discourse;

Route::get('backup', 'Backup@execute');

Route::group(['prefix' => 'api/v1.0', 'namespace' => 'Api'], function () {
    Route::get('/parties', 'Parties@all');

    Route::get('/congressman/profile/{id}', 'Congressmen@profile');

    Route::get('/documentsPages/page/{page_id}', 'Documents@page');

    Route::get('/documentsPages/{name}/{includePage?}', 'Documents@pages');

    Route::get('/bills', 'Bills@all');

    Route::get('/bill-projects', 'BillProjects@all');

    Route::get('/schedule', 'Schedule@all');

    Route::get('/schedule/{item}', 'Schedule@item');

    Route::get('/bills/{proposition}/votes', 'Bills@votes');

    Route::get('/tv', 'TVAlerj@data');

    Route::group(['prefix' => 'proderj/api'], function () {
        Route::get(
            '/{service}/{param1?}/{param2?}/{param3?}/{param4?}',
            'Proderj@service'
        );
    });
});

Route::get('schedule', function (Discourse $discourse) {
    $discourse->scrapeToDatabase();

    return 'yes';
});

Route::get('/{any?}', 'HomeController@index');
