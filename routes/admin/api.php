<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::post('response/refresh', [\BristolSU\Module\Typeform\Http\Controllers\AdminApi\ResponseRefreshController::class, 'refresh']);
Route::prefix('response/{typeform_response_id}')->group(function() {
    Route::post('approve', [\BristolSU\Module\Typeform\Http\Controllers\AdminApi\StatusController::class, 'approve']);
    Route::post('reject', [\BristolSU\Module\Typeform\Http\Controllers\AdminApi\StatusController::class, 'reject']);
    Route::apiResource('comment', \BristolSU\Module\Typeform\Http\Controllers\AdminApi\CommentController::class, ['as' => 'admin'])->only(['index', 'store'])->parameters(['comment' => 'typeform_comment']);
});
Route::apiResource('comment', \BristolSU\Module\Typeform\Http\Controllers\AdminApi\CommentController::class, ['as' => 'admin'])->only(['update', 'destroy'])->parameters(['comment' => 'typeform_comment']);
