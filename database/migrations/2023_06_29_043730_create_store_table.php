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
        Schema::create('store', function (Blueprint $table) {
            $table->id();
            $table->string('storeName',30)->nullable();
            $table->string('storeAddress',50)->nullable();
            $table->string('storeImage')->nullable();
            $table->string('email',30)->nullable();
            $table->string('mobileNumber',12)->nullable();
            $table->string('mobileVerified')->nullable();
            $table->string('country',15)->nullable();
            $table->bigInteger('storeCategoryId')->nullable();
            $table->string('loginThrough',30)->nullable();
            $table->bigInteger('roleId')->nullable();
            $table->bigInteger('subscriptionId')->nullable();
            $table->bigInteger('storeActiveId')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store');
    }
};
