<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\InvestorsController;

Route::prefix('v1')->group(function () {

    // Feedback Routes
    Route::apiResource('feedbacks', FeedbackController::class);

    // Recommendation Routes
    Route::apiResource('recommendations', RecommendationController::class);

    // Investors Routes
    Route::apiResource('investors', InvestorsController::class);

});


