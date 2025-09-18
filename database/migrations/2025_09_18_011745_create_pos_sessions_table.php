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
        Schema::create('pos_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('counter_name')->default('Front Cashier'); // nama loket/terminal
            $table->foreignId('opened_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('opened_at')->useCurrent();
            $table->decimal('opening_float', 12, 2)->default(0); // modal awal kasir

            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();

            // Rekap saat closure (opsional, diisi di proses tutup kas)
            $table->unsignedInteger('total_transactions')->default(0);
            $table->decimal('cash_total', 12, 2)->default(0);
            $table->decimal('noncash_total', 12, 2)->default(0);
            $table->decimal('expected_cash', 12, 2)->default(0);
            $table->decimal('actual_cash', 12, 2)->default(0);
            $table->decimal('cash_variance', 12, 2)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['opened_at','closed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_sessions');
    }
};
