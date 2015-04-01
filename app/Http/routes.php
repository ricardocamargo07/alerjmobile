<?php

Route::group(['prefix' => 'api/v1.0', 'namespace' => 'Api'], function()
{
	Route::get('parties', 'Parties@all');

	Route::get('congressman/profile/{id}', 'Congressmen@profile');

	Route::get('regiment', 'Regiment@all');

	Route::get('regiment/{id}', 'Regiment@find');
});

Route::any('{any?}', function()
{
	return Redirect::to(env('SITE_MAIN'));
});
