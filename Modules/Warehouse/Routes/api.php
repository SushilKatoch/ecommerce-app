<?php

use Illuminate\Http\Request;
use Modules\Warehouse\Http\Controllers\InventoryController;
use Modules\Warehouse\Http\Controllers\WarehouseController;

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

Route::group(['namespace' => '\Modules\Warehouse\Http\Controllers', 'as' => 'api', 'middleware' => 'cors'], function () {

    //warehouse
    Route::post("warehouse/store", [WarehouseController::class,'store']);
    Route::get("warehouse/showAll", [WarehouseController::class,'showAll']);
    Route::get("warehouse/show/{uuid}", [WarehouseController::class,'show']);
    Route::post("warehouse/update", [WarehouseController::class,'update']);
    Route::delete("warehouse/destroy/{uuid}", [WarehouseController::class,'destroy']);
    Route::get("warehouse/trashed", [WarehouseController::class,'trashed']);
    Route::patch("warehouse/trashed/{uuid}", [WarehouseController::class,'restore']);


    //inventory
    Route::post("inventory/store", [InventoryController::class,'store']);
    Route::get("inventory/showAll", [InventoryController::class,'showAll']);
    Route::get("inventory/show/{uuid}", [InventoryController::class,'show']);
    Route::post("inventory/update", [InventoryController::class,'update']);
    Route::delete("inventory/destroy/{uuid}", [InventoryController::class,'destroy']);
    Route::get("inventory/trashed", [InventoryController::class,'trashed']);
    Route::patch("inventory/trashed/{uuid}", [InventoryController::class,'restore']);


});