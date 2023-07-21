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
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable();
            $table->string('authId')->nullable();
            $table->string('skuCode')->nullable();
            $table->string('attributes')->nullable();
            $table->string('dimensions')->nullable();
            $table->unsignedBigInteger('inventory')->nullable();
            $table->float('productPrice')->nullable();
            $table->float('productSellingPrice')->nullable();
            $table->string('productBrand')->nullable();
            $table->string('imagesId')->nullable();
            $table->unsignedBigInteger('weight')->nullable();
            $table->string('weightUnit')->nullable();
            $table->boolean('inStock')->default('true');
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
        Schema::dropIfExists('variants');
    }
};
