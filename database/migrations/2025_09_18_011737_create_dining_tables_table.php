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
        Schema::create('dining_tables', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();   // ex: T01, A-05
            $table->string('name')->nullable(); // ex: Meja Taman 1
            $table->unsignedSmallInteger('capacity')->default(2);
            $table->enum('status', ['available','occupied','reserved','disabled'])->default('available');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dining_tables');
    }
};
