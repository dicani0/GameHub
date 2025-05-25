<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('cs')->group(function () {
    Route::get('team-generator', function () {
        return Inertia::render('CS2TeamGenerator');
    })->name('cs.team-generator');
});