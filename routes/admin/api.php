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
    Route::post('webhook/responses', 'IncomingWebhookController@store');
    Route::apiResource('connection', 'ConnectionController')->only(['index']);
    Route::apiResource('webhook', 'WebhookController')->only(['index', 'store']);
});
