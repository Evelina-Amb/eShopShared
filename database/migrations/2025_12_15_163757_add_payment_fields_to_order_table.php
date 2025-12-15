<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->string('payment_provider')->nullable()->index();
            $table->string('payment_reference')->nullable();
            $table->string('payment_intent_id')->nullable()->unique();
            $table->json('shipping_address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropColumn([
                'payment_provider',
                'payment_reference',
                'payment_intent_id',
                'shipping_address',
            ]);
        });
    }
};
