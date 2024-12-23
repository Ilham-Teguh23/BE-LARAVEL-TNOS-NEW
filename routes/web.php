<?php

use App\Http\Controllers\Api\OrderConsultationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

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

Route::get('/', function () {
    return view('welcome');
});
// clear
Route::get('/config-clear', function() {
    Artisan::call('config:clear');
    return "config:cache";
});
Route::get('/cache-clear', function() {
    Artisan::call('cache:clear');
    return "cache:clear";
});
// Route::get('/xendit', [OrderConsultationController::class, 'createInvoicePayment']);

Route::prefix("test")->group(function() {
    Route::get("/template", function() {
        return view("template");
    });
    Route::get("/encrypt", function() {
        return view("encrypt");
    });
});
