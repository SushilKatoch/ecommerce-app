<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('send-mail',[AuthController::class,'userEmailOtp']);

Route::group(['namespace' => 'App\Http\Controllers\Auth', 'as' => 'auth','middleware' =>['cors']], function () {
    Route::post("register",[AuthController::class,"register"]);
    Route::get("find-email/{email}",[AuthController::class,"getUserEmail"]);
    Route::post('forgot-password', [AuthController::class, 'resetPassword'])->name('forgot-password');
});
