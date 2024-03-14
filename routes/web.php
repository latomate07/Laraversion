<?php

use Illuminate\Support\Facades\Route;
use Laraversion\Laraversion\Controllers\LaraversionController;

Route::group(['prefix' => 'laraversion', 'as' => 'laraversion.', 'middleware' => config('laraversion.middleware')], function () {
    Route::get('/', [LaraversionController::class, 'index'])->name('index');
    Route::post('/revert', [LaraversionController::class, 'revert'])->name('revert');
});