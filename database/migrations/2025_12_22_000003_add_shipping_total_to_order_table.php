<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->integer('shipping_total_cents')->default(0)->after('small_order_fee_cents');
        });
    }

    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropColumn('shipping_total_cents');
        });
    }
};
