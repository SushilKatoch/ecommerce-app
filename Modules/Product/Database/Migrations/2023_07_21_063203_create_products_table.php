<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable();
            $table->string('authId')->nullable();
            $table->string('productSkuCode')->nullable();
            $table->string('productName')->nullable();
            $table->string('slug')->nullable();
            $table->string('productCategory')->nullable();
            $table->string('productCondition')->nullable();
            $table->text('productDescription')->nullable();
            $table->float('productPrice')->nullable();
            $table->float('productSellingPrice')->nullable();
            $table->string('productBrand')->nullable();
            $table->string('productAttributes')->nullable();
            $table->string('imagesId')->nullable();
            $table->string('productCategoryId')->nullable();
            $table->string('productUnit')->nullable();
            $table->unsignedBigInteger('productQuantity')->nullable();
            $table->string('unit')->nullable();
            $table->unsignedBigInteger('productWeight')->nullable();
            $table->string('weightUnit')->nullable();
            $table->string('shipmentWeight')->nullable();
            $table->string('hsnCode')->nullable();
            $table->string('gstRate')->nullable();
            $table->boolean('inStock')->default(0);
            $table->boolean('isActive')->default(0);
            $table->boolean('isTaxable')->default(1);
            $table->string('variantId')->nullable();
            $table->string('orderBy')->nullable();
            $table->string('tags')->nullable();
            $table->string('storeUuid')->nullable();
            $table->string('countryOfOrigin')->nullable();
            $table->string('manufacturingAddress')->nullable();
            $table->string('seoData')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
