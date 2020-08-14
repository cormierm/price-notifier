<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function() {
    Route::get('/home', 'HomeController@index')->name('home');

    Route::prefix('watcher')->namespace('Watcher')->name('watcher.')->group(function() {
        Route::post('/', 'Store')->name('store');
        Route::get('create', 'Create')->name('create');
        Route::put('{watcher}', 'Update')->name('update');
        Route::get('{watcher}/edit', 'Edit')->name('edit');
        Route::get('{watcher}/sync', 'Sync')->name('sync');
    });
});

