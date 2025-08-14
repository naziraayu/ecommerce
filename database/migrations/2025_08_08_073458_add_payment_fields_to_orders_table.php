<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_type')->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('midtrans_transaction_id')->nullable();
            $table->json('midtrans_response')->nullable();
            $table->timestamp('paid_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_type', 
                'payment_status', 
                'midtrans_transaction_id',
                'midtrans_response',
                'paid_at'
            ]);
        });
    }
};
