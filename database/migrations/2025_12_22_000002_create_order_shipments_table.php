<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('seller_id');

            $table->string('carrier', 30)->default('omniva');     // omniva / venipak / lpexpress
            $table->string('package_size', 2)->default('S');      // S/M/L
            $table->integer('price_cents')->default(0);

            $table->string('status', 20)->default('pending');     // pending/shipped/delivered
            $table->string('tracking_number')->nullable();

            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('order')->onDelete('cascade');
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['order_id', 'seller_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_shipments');
    }
};
