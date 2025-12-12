<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('address', function (Blueprint $table) {
            $table->id();
            $table->string('gatve', 100)->nullable();;
            $table->string('namo_nr', 10)->nullable();
            $table->string('buto_nr', 10)->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->timestamps();

            $table->foreign('city_id')->references('id')->on('city')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('address');
    }
};

