<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('watcher', 'Api\Watcher\Store')->middleware('auth.basic.api-key')->name('api.watcher.store');
Route::post('watcher/check', 'Watcher\Check')->middleware('auth.basic.api-key')->name('api.check');
Route::post('template/search-by-url', 'Template\SearchByUrl')->middleware('auth.basic.api-key')->name('api.template.search-by-url');
