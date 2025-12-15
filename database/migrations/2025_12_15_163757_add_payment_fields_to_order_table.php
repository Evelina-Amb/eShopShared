<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->string('payment_provider')->nullable()->after('user_id');
            $table->string('payment_reference')->nullable()->after('payment_provider');
            $table->json('shipping_address')->nullable()->after('bendra_suma');

            // statusas values:
            // pending, paid, failed, cancelled
            $table->string('statusas')->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropColumn([
                'payment_provider',
                'payment_reference',
                'shipping_address',
            ]);
        });
    }
};
