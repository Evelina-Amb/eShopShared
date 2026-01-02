<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('order_shipments', 'proof_path')) {
            Schema::table('order_shipments', function (Blueprint $table) {
                $table->string('proof_path')->nullable()->after('tracking_number');
            });
        }

        DB::statement("
            ALTER TABLE order_shipments
            MODIFY status ENUM(
                'pending',
                'needs_review',
                'approved',
                'reimbursed'
            ) NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE order_shipments
            MODIFY status ENUM(
                'pending',
                'approved',
                'reimbursed'
            ) NOT NULL DEFAULT 'pending'
        ");

        if (Schema::hasColumn('order_shipments', 'proof_path')) {
            Schema::table('order_shipments', function (Blueprint $table) {
                $table->dropColumn('proof_path');
            });
        }
    }
};
