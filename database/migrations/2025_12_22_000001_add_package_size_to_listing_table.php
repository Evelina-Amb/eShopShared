<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('listing', function (Blueprint $table) {
            if (!Schema::hasColumn('listing', 'package_size')) {
                $table->string('package_size', 2)->default('S')->after('kiekis');
            }
        });
    }

    public function down(): void
    {
        Schema::table('listing', function (Blueprint $table) {
            if (Schema::hasColumn('listing', 'package_size')) {
                $table->dropColumn('package_size');
            }
        });
    }
};
