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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('authId')->nullable();
            $table->string('uuid')->nullable();
            $table->string('name',50)->nullable();
            $table->string('slug',50)->nullable();
            $table->bigInteger('imagesId')->nullable();
            $table->bigInteger('bannerImageId')->nullable();
            $table->bigInteger('bannerImageMobileId')->nullable();
            $table->text('description')->nullable();
            $table->enum('isActive', ['true','false'])->default('false');
            $table->string('inStock',20)->nullable();
            $table->unsignedBigInteger('parentId')->nullable();
            $table->string('parentName')->nullable();
            $table->unsignedBigInteger('orderBy')->nullable();
            $table->json('seoData')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
