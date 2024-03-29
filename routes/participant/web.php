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

    Route::get('/', [\BristolSU\Module\Typeform\Http\Controllers\Participant\ParticipantPageController::class, 'index']);
    Route::get('/file/{typeform_answer_id_user}/download', [\BristolSU\Module\Typeform\Http\Controllers\Participant\DownloadFileController::class, 'download']);
