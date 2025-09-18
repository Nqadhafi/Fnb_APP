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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->nullable()->unique();
            $table->longText('description')->nullable();

            $table->decimal('price', 12, 2);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('is_active')->default(true);

            // Opsi pilihan (Size/Topping) untuk render UI; pilihan user disimpan di order_items.selected_options
            $table->json('options_schema')->nullable();

            // Gambar utama (upload lokal)
            $table->string('main_image_path', 2048)->nullable();
            $table->string('main_image_disk', 50)->default('public');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id','is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
