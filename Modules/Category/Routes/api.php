<?php

use Illuminate\Http\Request;
use Modules\Category\Http\Controllers\CategoryController;

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

Route::group(['namespace' => '\Modules\Category\Http\Controllers', 'as' => 'api', 'middleware' => 'cors'], function () {

  
    Route::post("category/store", [CategoryController::class,'store']);
    Route::get("category/showAll", [CategoryController::class,'showAll']);
    Route::get("category/show/{id}", [CategoryController::class,'show']);
    Route::post("category/update", [CategoryController::class,'update']);
    Route::delete("category/destroy/{id}", [CategoryController::class,'destroy']);
    Route::get("category/trashed", [CategoryController::class,'trashed']);
    Route::patch("category/trashed/{id}", [CategoryController::class,'restore']);


});