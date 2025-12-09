<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orderItem', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('listing_id');
            $table->decimal('kaina', 10, 2);
            $table->integer('kiekis')->default(1);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('order')->onDelete('cascade');
            $table->foreign('listing_id')->references('id')->on('listing')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orderItem');
    }
};

