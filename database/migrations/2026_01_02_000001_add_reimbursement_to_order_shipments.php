<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('order_shipments', 'reimbursement_transfer_id')) {
                $table
                    ->string('reimbursement_transfer_id')
                    ->nullable()
                    ->after('tracking_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_shipments', function (Blueprint $table) {
            if (Schema::hasColumn('order_shipments', 'reimbursement_transfer_id')) {
                $table->dropColumn('reimbursement_transfer_id');
            }
        });
    }
};
