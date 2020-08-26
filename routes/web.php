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

Auth::routes([
    'register' => env('REGISTRATION_ENABLED', false),
    'verify' => true,
]);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', 'HomeController@index')->name('home');

    Route::prefix('template')->namespace('Template')->name('template.')->group(function () {
        Route::get('/', 'Index')->name('index');
        Route::post('search-by-url', 'SearchByUrl')->name('search-by-url');
        Route::delete('{template}', 'Destroy')->name('destroy');
        Route::get('{template}/edit', 'Edit')->name('edit');
        Route::get('{domain}/search', 'Search')->name('search');
    });

    Route::prefix('watcher')->namespace('Watcher')->name('watcher.')->group(function () {
        Route::post('/', 'Store')->name('store');
        Route::post('check', 'Check')->name('check');
        Route::get('create', 'Create')->name('create');
        Route::delete('{watcher}', 'Destroy')->name('destroy')->middleware('can:update,watcher');
        Route::get('{watcher}', 'Show')->name('show')->middleware('can:view,watcher');
        Route::put('{watcher}', 'Update')->name('update')->middleware('can:update,watcher');
        Route::get('{watcher}/edit', 'Edit')->name('edit')->middleware('can:update,watcher');
        Route::get('{watcher}/sync', 'Sync')->name('sync')->middleware('can:update,watcher');
        Route::get('{watcher}/logs', 'Logs')->name('logs')->middleware('can:view,watcher');
    });
});

