<?php

use App\Http\Controllers\AnimeController;
use App\Http\Controllers\ConnectionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::prefix('/anime')->group(function() {
        Route::get('/', [AnimeController::class, 'index'])->name('anime.index');
        Route::get('/{id}', [AnimeController::class, 'show'])->name('anime.show');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::prefix('/user')->group(function () {
        Route::prefix('/connections')->group(function() {
            Route::get('/', [ConnectionController::class, 'index'])->name('connections.index');
            Route::prefix('/mal')->group(function() {
                Route::get('/sync', [ConnectionController::class, 'malSync'])->name('connections.mal.sync');

                Route::post('/connect', [ConnectionController::class, 'malConnect']);
                Route::post('/disconnect', [ConnectionController::class, 'malDisconnect']);
            });
        });
    });
});
