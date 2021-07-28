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

Route::get('/', [\BristolSU\Module\Typeform\Http\Controllers\Admin\AdminPageController::class, 'index']);
Route::get('/file/{typeform_answer_id_admin}/download', [\BristolSU\Module\Typeform\Http\Controllers\Admin\DownloadFileController::class, 'download']);
