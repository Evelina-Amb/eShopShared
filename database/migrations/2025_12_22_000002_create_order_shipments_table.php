<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_shipments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('order')
                ->cascadeOnDelete();

            $table->foreignId('seller_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('carrier', 20);        
            $table->string('package_size', 2);     

        
            $table->integer('shipping_cents');      // what buyer paid & seller will be reimbursed

            // Shipment lifecycle
            $table->enum('status', [
                'pending',      // waiting for seller to ship
                'shipped',      // seller uploaded tracking/proof
                'approved',     // admin or auto-approved
                'reimbursed'    // shipping money sent to seller
            ])->default('pending');

            $table->string('tracking_number')->nullable();
            $table->string('proof_path')->nullable(); // image/pdf receipt

            // Stripe reimbursement
            $table->string('reimbursement_transfer_id')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'seller_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_shipments');
    }
};
