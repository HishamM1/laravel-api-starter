<?php

use App\Domain\Auth\Actions\CreateToken;
use App\Domain\Auth\Actions\RevokeToken;
use App\Domain\Notes\Actions\CreateNote;
use App\Domain\Notes\Actions\DeleteNote;
use App\Domain\Notes\Actions\ListNotes;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('auth/token', CreateToken::class)->middleware('throttle:login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('auth/token', RevokeToken::class);

        Route::get('notes', ListNotes::class);
        Route::post('notes', CreateNote::class);
        Route::delete('notes/{note}', DeleteNote::class);
    });
});
