<?php

use App\Http\Controllers\FaceitController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('cs')->group(function () {
    Route::get('team-generator', function () {
        return Inertia::render('counterstrike/CS2TeamGenerator');
    })->name('cs.team-generator');

    Route::get('faceit-search', function () {
        return Inertia::render('counterstrike/FaceitSearch');
    })->name('cs.faceit-search');

    Route::prefix('faceit')->name('cs.faceit.')->group(function () {
        Route::get('player', [FaceitController::class, 'getPlayer'])->name('player');
        Route::get('match/{matchId}', [FaceitController::class, 'getMatchDetails'])->name('match');
    });
});
