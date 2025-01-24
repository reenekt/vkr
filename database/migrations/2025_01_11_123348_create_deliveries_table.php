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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained();
            $table->unsignedSmallInteger('delivery_status');
            $table->decimal('delivery_price', 8, 2)->default(0);
            $table->string('pickup_store_id')->nullable();
            $table->string('delivery_company_id')->nullable();
            $table->json('delivery_company_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
