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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();          // ex: ORD-20250918-0001
            $table->string('receipt_no')->nullable();  // nomor struk POS (opsional)

            $table->enum('order_type', ['dine_in','takeaway'])->default('dine_in');
            $table->enum('status', [
                'open','pending','paid','preparing','ready','served','completed','cancelled'
            ])->default('open');

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // customer (opsional)
            $table->foreignId('table_session_id')->nullable()->constrained('table_sessions')->nullOnDelete();
            $table->foreignId('cart_id')->nullable()->constrained('carts')->nullOnDelete();

            // keterkaitan dengan sesi POS saat pembayaran
            $table->foreignId('pos_session_id')->nullable()->constrained('pos_sessions')->nullOnDelete();

            // ringkasan biaya
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('service_charge', 12, 2)->default(0); // service fee restoran
            $table->decimal('tax_total', 12, 2)->default(0);      // PPN/Tax
            $table->decimal('grand_total', 12, 2)->default(0);

            $table->string('payment_method')->nullable(); // cash, transfer, e-wallet
            $table->timestamp('paid_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status','order_type']);
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
