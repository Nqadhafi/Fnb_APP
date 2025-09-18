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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();

            // snapshot
            $table->string('product_name');
            $table->decimal('unit_price', 12, 2);
            $table->unsignedInteger('qty');
            $table->json('selected_options')->nullable();
            $table->text('notes')->nullable();

            // status persiapan per item (untuk layar dapur/bar)
            $table->enum('prep_status', ['queued','preparing','ready','served','void'])->default('queued');

            $table->decimal('line_total', 12, 2); // isi via kode saat insert/update

            $table->timestamps();

            $table->index(['order_id','prep_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
