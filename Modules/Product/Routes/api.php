<?php

use Illuminate\Http\Request;
use Modules\Product\Http\Controllers\ProductCategoriesController;
use Modules\Product\Http\Controllers\ProductCategoriesImagesController;
use Modules\Product\Http\Controllers\ProductCategoriesBannerImagesController;
use Modules\Product\Http\Controllers\ProductController;

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
    Route::get("productCategory/show/{uuid}", [ProductCategoriesController::class,'show']);
    Route::post("productCategory/update", [ProductCategoriesController::class,'update']);
    Route::delete("productCategory/destroy/{uuid}", [ProductCategoriesController::class,'destroy']);
    Route::get("productCategory/trashed", [ProductCategoriesController::class,'trashed']);
    Route::patch("productCategory/trashed/{uuid}", [ProductCategoriesController::class,'restore']);
    Route::post("productCategory/swapOrder", [ProductCategoriesController::class,'swapProductCategoryOrder']);
    Route::get("productCategory/status/{id}", [ProductCategoriesController::class,'active']);
        //Product Category Images

    Route::post("storeImage", [ProductCategoriesImagesController::class,'store']);
    Route::get("showAllImages", [ProductCategoriesImagesController::class,'showAll']);
    Route::get("showImage/{id}", [ProductCategoriesImagesController::class,'show']);
    Route::post("updateImage", [ProductCategoriesImagesController::class,'update']);
    Route::delete("deleteImage/{id}", [ProductCategoriesImagesController::class,'destroy']);
    Route::get("trashedImages", [ProductCategoriesImagesController::class,'trashed']);
    Route::patch("restoreImage/{id}", [ProductCategoriesImagesController::class,'restore']);
    
         //Product Banner Category Images

     Route::post("productCategoryBannerImages/store", [ProductCategoriesBannerImagesController::class,'store']);
     Route::get("productCategoryBannerImages/showAll", [ProductCategoriesBannerImagesController::class,'showAll']);
     Route::get("productCategoryBannerImages/show/{uuid}", [ProductCategoriesBannerImagesController::class,'show']);
     Route::post("productCategoryBannerImages/update", [ProductCategoriesBannerImagesController::class,'update']);
     Route::delete("productCategoryBannerImages/destroy/{uuid}", [ProductCategoriesBannerImagesController::class,'destroy']);
     Route::get("productCategoryBannerImages/trashed", [ProductCategoriesBannerImagesController::class,'trashed']);
     Route::patch("productCategoryBannerImages/trashed/{uuid}", [ProductCategoriesBannerImagesController::class,'restore']);

    // Products
    Route::post("product/store", [ProductController::class,'store']);
    Route::get("product/showAll", [ProductController::class,'showAll']);
    Route::get("product/show/{uuid}", [ProductController::class,'show']);
    Route::post("product/update", [ProductController::class,'update']);
    Route::delete("product/destroy/{uuid}", [ProductController::class,'destroy']);
    Route::get("product/trashed", [ProductController::class,'trashed']);
    Route::patch("product/trashed/{uuid}", [ProductController::class,'restore']);
    Route::post("product/swapOrder", [ProductController::class,'swapProductOrder']);
    Route::get("product/status/{id}", [ProductController::class,'active']);
});