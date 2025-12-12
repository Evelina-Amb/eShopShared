<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('vardas', 50);
            $table->string('pavarde', 50);
            $table->string('el_pastas', 100)->unique();
            $table->string('slaptazodis', 255);
            $table->string('telefonas', 30)->nullable();
            $table->unsignedBigInteger('address_id')->nullable();
            $table->string('role', 20)->default('buyer');
            $table->timestamps();
            $table->rememberToken();
            $table->foreign('address_id')->references('id')->on('address')->onDelete('cascade');
           
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
