<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('listing_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listing_id');
            $table->string('failo_url');
            $table->timestamps();
            $table->foreign('listing_id')
                  ->references('id')
                  ->on('listing')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_photos');
    }
};