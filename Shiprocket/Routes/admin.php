<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['web', 'admin'],
    'prefix'     => 'admin',
], function () {

    Route::get('shiprocket/config', [
        'as'   => 'admin.shiprocket.config',
        'uses' => 'Webkul\Shiprocket\Http\Controllers\Admin\ShiprocketController@config',
    ]);

    Route::post('shiprocket/config', [
        'as'   => 'admin.shiprocket.save',
        'uses' => 'Webkul\Shiprocket\Http\Controllers\Admin\ShiprocketController@save',
    ]);

    Route::get('shiprocket/fetch', [
        'as'   => 'admin.shiprocket.fetch',
        'uses' => 'Webkul\Shiprocket\Http\Controllers\Admin\ShiprocketController@fetch',
    ]);

    Route::post('shiprocket/test', [
        'as'   => 'admin.shiprocket.test',
        'uses' => 'Webkul\Shiprocket\Http\Controllers\Admin\ShiprocketController@test',
    ]);

    Route::post('shiprocket/test-api', [
        'as'   => 'admin.shiprocket.test.api',
        'uses' => 'Webkul\Shiprocket\Http\Controllers\Admin\ShiprocketController@testApi',
    ]);

    Route::post('shiprocket/test-channel', [
        'as'   => 'admin.shiprocket.test.channel',
        'uses' => 'Webkul\Shiprocket\Http\Controllers\Admin\ShiprocketController@testChannel',
    ]);

   Route::post('shiprocket/test-api', [
    'as'   => 'admin.shiprocket.test.api',
    'uses' => 'Webkul\Shiprocket\Http\Controllers\Admin\ShiprocketController@testApi',
]);


Route::get('shiprocket/pickups', [
    'as'   => 'admin.shiprocket.pickups',
    'uses' => 'Webkul\Shiprocket\Http\Controllers\Admin\ShiprocketController@pickupLocations',
]);

});
