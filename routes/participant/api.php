<?php

use Illuminate\Support\Facades\Route;

Route::prefix('response/{typeform_response_id}')->group(function() {
    Route::apiResource('comment', \BristolSU\Module\Typeform\Http\Controllers\ParticipantApi\CommentController::class)->only(['index', 'store'])->parameters(['comment' => 'typeform_comment']);
});
Route::apiResource('comment', \BristolSU\Module\Typeform\Http\Controllers\ParticipantApi\CommentController::class)->only(['update', 'destroy'])->parameters(['comment' => 'typeform_comment']);
