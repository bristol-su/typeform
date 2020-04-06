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

Route::namespace('AdminApi')->group(function() {
    Route::post('response/refresh', 'ResponseRefreshController@refresh');
    Route::prefix('response/{typeform_response_id}')->group(function() {
        Route::post('approve', 'StatusController@approve');
        Route::post('reject', 'StatusController@reject');
        Route::apiResource('comment', 'CommentController')->only(['index', 'store'])->parameters(['comment' => 'typeform_comment']);
    });
    Route::apiResource('comment', 'CommentController')->only(['update', 'destroy'])->parameters(['comment' => 'typeform_comment']);
    
});
