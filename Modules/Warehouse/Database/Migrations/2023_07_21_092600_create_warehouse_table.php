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
        Schema::create('warehouse', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('authId')->nullable();
            $table->string('uuid')->nullable();
            $table->string('addressLine1')->nullable();
            $table->string('addressLine2')->nullable();
            $table->string('city')->nullable();
            $table->string('contactPersonName')->nullable();
            $table->string('fssaiNumber')->nullable();
            $table->string('gstNumber')->nullable();
            $table->boolean('isActive')->default(true);
            $table->boolean('isPrimaryWarehouse')->nullable();
            $table->string('mobileNumber')->nullable();
            $table->unsignedBigInteger('skuId')->nullable();
            $table->unsignedBigInteger('quantityAvailable')->nullable();
            $table->string('name')->nullable();
            $table->string('pincode')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('regionDelivery')->nullable();
            $table->boolean('termsChecked')->default(true);
            $table->unsignedBigInteger('orderBy')->nullable();
            $table->boolean('wantsHyperLocalDelivery')->default(false);
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
        Schema::dropIfExists('warehouse');
    }
};
