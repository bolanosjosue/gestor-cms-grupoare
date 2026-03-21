<?php

use App\Http\Controllers\Api\PublicArticleController;
use App\Http\Controllers\Api\PublicStaffController;
use Illuminate\Support\Facades\Route;

Route::prefix('public')->group(function () {
    Route::get('staff', [PublicStaffController::class, 'index']);
    Route::get('articles', [PublicArticleController::class, 'index']);
    Route::get('articles/{slug}', [PublicArticleController::class, 'show']);
});

