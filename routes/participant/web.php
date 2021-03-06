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

Route::namespace('Participant')->group(function() {
    Route::get('/', 'ParticipantPageController@index');
    Route::get('/file/{typeform_answer_id_user}/download', 'DownloadFileController@download');
});