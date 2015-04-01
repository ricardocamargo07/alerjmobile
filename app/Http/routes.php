<?php

Route::group(['prefix' => 'api/v1.0', 'namespace' => 'Api'], function()
{
	Route::get('parties', 'Parties@all');

	Route::get('congressman/profile/{id}', 'Congressmen@profile');

	Route::get('documentsPages/{name}', 'Documents@pages');

	Route::get('documentsPages/page/{page_id}', 'Documents@page');
});

Route::any('{any?}', function()
{
	return Redirect::to(env('SITE_MAIN'));
});
