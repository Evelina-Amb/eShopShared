<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('listing', function (Blueprint $table) {
            $table->integer('kiekis')->default(1);   // stock quantity
            $table->boolean('is_renewable')->default(0); // 0 = one time item
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('listing', function (Blueprint $table) {
            $table->dropColumn(['kiekis', 'is_renewable']);
        });
    }   
};
