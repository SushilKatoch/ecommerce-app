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
        Schema::create('product_categories_banner_images', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable();
            $table->string('authId')->nullable();
            $table->string('name')->nullable();
            $table->string('path')->nullable();
            $table->string('alt')->nullable();
            $table->string('imageDescription')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories_banner_images');
    }
};
