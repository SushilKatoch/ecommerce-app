<?php

use Illuminate\Http\Request;
use Modules\Product\Http\Controllers\ProductCategoriesBannerImagesController;
use Modules\Product\Http\Controllers\ProductCategoriesController;
use Modules\Product\Http\Controllers\ProductCategoriesImagesController;

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

Route::group(['namespace' => '\Modules\Product\Http\Controllers', 'as' => 'api', 'middleware' => 'cors'], function () {

  
    Route::post("productCategory/store", [ProductCategoriesController::class,'store']);
    Route::get("productCategory/showAll", [ProductCategoriesController::class,'showAll']);
    Route::get("productCategory/show/{id}", [ProductCategoriesController::class,'show']);
    Route::post("productCategory/update", [ProductCategoriesController::class,'update']);
    Route::post("productCategory/swapOrder", [ProductCategoriesController::class,'swapProductCategoryOrder']);
    Route::delete("productCategory/destroy/{id}", [ProductCategoriesController::class,'destroy']);
    Route::get("productCategory/trashed", [ProductCategoriesController::class,'trashed']);
    Route::patch("productCategory/trashed/{id}", [ProductCategoriesController::class,'restore']);

    //Product Category Images

    Route::post("productCategoryImages/store", [ProductCategoriesImagesController::class,'store']);
    Route::get("productCategoryImages/showAll", [ProductCategoriesImagesController::class,'showAll']);
    Route::get("productCategoryImages/show/{uuid}", [ProductCategoriesImagesController::class,'show']);
    Route::post("productCategoryImages/update", [ProductCategoriesImagesController::class,'update']);
    Route::delete("productCategoryImages/destroy/{uuid}", [ProductCategoriesImagesController::class,'destroy']);
    Route::get("productCategoryImages/trashed", [ProductCategoriesImagesController::class,'trashed']);
    Route::patch("productCategoryImages/trashed/{uuid}", [ProductCategoriesImagesController::class,'restore']);

     //Product Banner Category Images

     Route::post("productCategoryBannerImages/store", [ProductCategoriesBannerImagesController::class,'store']);
     Route::get("productCategoryBannerImages/showAll", [ProductCategoriesBannerImagesController::class,'showAll']);
     Route::get("productCategoryBannerImages/show/{uuid}", [ProductCategoriesBannerImagesController::class,'show']);
     Route::post("productCategoryBannerImages/update", [ProductCategoriesBannerImagesController::class,'update']);
     Route::delete("productCategoryBannerImages/destroy/{uuid}", [ProductCategoriesBannerImagesController::class,'destroy']);
     Route::get("productCategoryBannerImages/trashed", [ProductCategoriesBannerImagesController::class,'trashed']);
     Route::patch("productCategoryBannerImages/trashed/{uuid}", [ProductCategoriesBannerImagesController::class,'restore']);

});