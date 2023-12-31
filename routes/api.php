<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Store\StoreController;

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




Route::group(['namespace' => 'App\Http\Controllers\Auth', 'as' => 'api','middleware' =>['cors']], function () {
    Route::post('requestOtp',[AuthController::class,'requestOtp']);
 
    Route::post('login/otp/verify', [AuthController::class,"verifyOTP"]);
    Route::post('store/kyc', [AuthController::class,"storeDetail"]);
    Route::post("register",[AuthController::class,"register"]);
    Route::post("verifyToken",[AuthController::class,"verifyToken"]);
    Route::get("getUser",[AuthController::class,"getUser"]);
    Route::get("logout",[AuthController::class,"logout"]);

    //Store
    Route::post('gstin/kyc', [StoreController::class,"gstKyc"]);
    Route::post('pickupAddress/kyc', [StoreController::class,"pickupAddressKyc"]);
    Route::post('bankDetails/kyc', [StoreController::class,"bankDetailsKyc"]);
    Route::post('supplierDetails/kyc', [StoreController::class,"supplierDetailsKyc"]);
});
