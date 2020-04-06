<?php

use Illuminate\Support\Facades\Route;

Route::namespace('ParticipantApi')->group(function() {
    Route::prefix('response/{typeform_response_id}')->group(function() {
        Route::apiResource('comment', 'CommentController')->only(['index', 'store'])->parameters(['comment' => 'typeform_comment']);
    });
    Route::apiResource('comment', 'CommentController')->only(['update', 'destroy'])->parameters(['comment' => 'typeform_comment']);
});
