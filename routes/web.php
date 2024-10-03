<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes([
    'register' => env('REGISTRATION_ENABLED', false),
    'verify' => true,
]);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/home', '/watcher')->name('home');

    Route::get('dashboard', 'Dashboard\Index')->name('dashboard');

    Route::prefix('template')->namespace('Template')->name('template.')->group(function () {
        Route::get('/', 'Index')->name('index');
        Route::post('search-by-url', 'SearchByUrl')->name('search-by-url');
        Route::delete('{template}', 'Destroy')->name('destroy');
        Route::put('{template}', 'Update')->name('update');
        Route::get('{template}/edit', 'Edit')->name('edit');
        Route::get('{domain}/search', 'Search')->name('search');
    });

    Route::prefix('watcher')->namespace('Watcher')->name('watcher.')->group(function () {
        Route::get('/', 'Index')->name('index');
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

require __DIR__.'/auth.php';
