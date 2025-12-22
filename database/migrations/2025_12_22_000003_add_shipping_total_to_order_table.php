<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order', function (Blueprint $table) {
            if (!Schema::hasColumn('order', 'shipping_total_cents')) {
                $table->integer('shipping_total_cents')->nullable()->after('small_order_fee_cents');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            if (Schema::hasColumn('order', 'shipping_total_cents')) {
                $table->dropColumn('shipping_total_cents');
            }
        });
    }
};
