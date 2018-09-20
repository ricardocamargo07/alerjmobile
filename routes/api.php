<?php

Route::group(['namespace' => 'Api'], function()
{
    Route::group(['prefix' => 'ldap/'.config('ldap.route_prefix')], function()
    {
        Route::post('/login', 'Ldap@login');
        Route::post('/user', 'Ldap@user');
    });

    Route::group(['prefix' => 'adm-user/'.config('adm_user.route_prefix')], function()
    {
        Route::post('permissions', 'AdmUser@permissions');

        Route::post('profiles', 'AdmUser@profiles');
    });
});
