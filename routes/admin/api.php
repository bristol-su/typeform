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
    Route::post('response/{typeform_response_id}/approve', 'StatusController@approve');
    Route::post('response/{typeform_response_id}/reject', 'StatusController@reject');
});
