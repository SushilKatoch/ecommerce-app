<?php

use Illuminate\Http\Request;
use Modules\Unit\Http\Controllers\UnitController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => '\Modules\Unit\Http\Controllers', 'as' => 'api', 'middleware' => 'cors'], function () {

    //unit
    Route::post("unit/store", [UnitController::class,'store']);
    Route::get("unit/showAll", [UnitController::class,'showAll']);
    Route::get("unit/show/{uuid}", [UnitController::class,'show']);
    Route::post("unit/update", [UnitController::class,'update']);
    Route::delete("unit/destroy/{uuid}", [UnitController::class,'destroy']);
    Route::get("unit/trashed", [UnitController::class,'trashed']);
    Route::patch("unit/trashed/{uuid}", [UnitController::class,'restore']);


});;