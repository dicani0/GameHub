<?php

use App\Http\Controllers\FaceitController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('cs')->name('cs.')->group(function () {
    Route::get('team-generator', function () {
        return Inertia::render('counterstrike/CS2TeamGenerator');
    })->name('cs.team-generator');


    Route::prefix('faceit')->name('faceit.')->group(function () {
        Route::get('search', function () {
            return Inertia::render('counterstrike/FaceitSearch');
        })->name('search');

        Route::get('results', [FaceitController::class, 'getPlayerResults'])->name('results');
        Route::get('match/{matchId}', [FaceitController::class, 'getMatchDetails'])->name('match');
    });
});
