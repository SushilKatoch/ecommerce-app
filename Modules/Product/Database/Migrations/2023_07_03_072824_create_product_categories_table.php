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
            $table->string('name',50)->nullable();
            $table->string('slug',50)->nullable();
            $table->string('images')->nullable();
            $table->string('bannerImage')->nullable();
            $table->string('bannerImageMobile')->nullable();
            $table->text('description')->nullable();
            $table->string('isActive',10)->nullable();
            $table->string('inStock',10)->nullable();
            $table->unsignedBigInteger('parentId')->nullable();
            $table->string('parentName')->nullable();
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
