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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id')->index();
            $table->unsignedSmallInteger('status')->index();
            $table->unsignedSmallInteger('payment_method');
            $table->unsignedSmallInteger('payment_system')->nullable();
            $table->unsignedSmallInteger('payment_status');
            $table->unsignedSmallInteger('delivery_method');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
