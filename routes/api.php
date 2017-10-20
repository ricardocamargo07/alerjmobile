<?php

Route::group(['namespace' => 'Api'], function()
{
    Route::group(['prefix' => 'ldap/'.config('ldap.route_prefix')], function()
    {
        Route::post('/login', 'Ldap@login');
    });
});
