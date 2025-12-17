<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order', function (Blueprint $table) {
            if (!Schema::hasColumn('order', 'payment_intents')) {
                $table->json('payment_intents')->nullable()->after('payment_intent_id');
            }

            if (!Schema::hasColumn('order', 'amount_charged_cents')) {
                $table->integer('amount_charged_cents')->nullable()->after('bendra_suma');
            }

            if (!Schema::hasColumn('order', 'platform_fee_cents')) {
                $table->integer('platform_fee_cents')->nullable()->after('amount_charged_cents');
            }

            if (!Schema::hasColumn('order', 'small_order_fee_cents')) {
                $table->integer('small_order_fee_cents')->nullable()->after('platform_fee_cents');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            if (Schema::hasColumn('order', 'payment_intents')) {
                $table->dropColumn('payment_intents');
            }

            if (Schema::hasColumn('order', 'amount_charged_cents')) {
                $table->dropColumn('amount_charged_cents');
            }

            if (Schema::hasColumn('order', 'platform_fee_cents')) {
                $table->dropColumn('platform_fee_cents');
            }

            if (Schema::hasColumn('order', 'small_order_fee_cents')) {
                $table->dropColumn('small_order_fee_cents');
            }
        });
    }
};
