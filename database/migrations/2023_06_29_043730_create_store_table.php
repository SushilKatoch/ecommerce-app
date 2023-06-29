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
            $table->string('fullName',30)->nullable();
            $table->string('storeAddress')->nullable();
            $table->string('warehouseAddress')->nullable();
            $table->string('gst',15)->nullable();
            $table->string('ifscCode',15)->nullable();
            $table->string('account_number',50)->nullable();
            $table->string('storeImage')->nullable();
            $table->string('email',30)->nullable();
            $table->string('mobileNumber',12)->nullable();
            $table->bigInteger('otp')->nullable();
            $table->dateTime('otpExpiresIn')->nullable();
            $table->string('mobileVerified')->nullable();
            $table->string('country',15)->nullable();
            $table->bigInteger('storeCategoryId')->nullable();
            $table->string('loginThrough',30)->nullable();
            $table->string('verified',10)->nullable();
            $table->bigInteger('roleId')->nullable();
            $table->bigInteger('subscriptionId')->nullable();
            $table->bigInteger('storeActiveId')->nullable();
            $table->softDeletes();
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
