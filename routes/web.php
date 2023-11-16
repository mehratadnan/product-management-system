<?php

use App\Http\Controllers\ImportRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'import-request'], function () {
    Route::post('/add', [ImportRequestController::class, 'createImportRequest']);
    Route::delete('/delete', [ImportRequestController::class, 'deleteImportRequest']);
    Route::get('/list', [ImportRequestController::class, 'listImportRequest']);
    Route::get('/select', [ImportRequestController::class, 'selectImportRequest']);
    Route::put('/update', [ImportRequestController::class, 'updateImportRequest']);
});
