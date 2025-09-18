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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

            $table->enum('status', ['pending','verified','rejected'])->default('pending');
            $table->string('method')->nullable(); // cash, transfer, e-wallet
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamp('paid_at')->nullable();

            // Bukti transfer/e-wallet (opsional)
            $table->string('proof_path', 2048)->nullable();
            $table->string('proof_disk', 50)->default('public');

            // POS — tunai: uang diterima & kembalian
            $table->decimal('cash_received', 12, 2)->nullable();
            $table->decimal('change_given', 12, 2)->nullable();

            // verifikasi oleh siapa (kasir/admin)
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['order_id','status']);
            $table->index('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
