<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id(); $table->string('order_number')->unique(); $table->foreignId('reservation_id')->constrained()->cascadeOnDelete(); $table->foreignId('user_id')->constrained()->restrictOnDelete(); $table->string('category')->index(); $table->string('status')->default('new')->index(); $table->text('notes')->nullable(); $table->string('delivery_location')->nullable(); $table->timestamp('requested_at'); $table->timestamps();
        });
        Schema::create('service_order_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('service_order_id')->constrained()->cascadeOnDelete(); $table->foreignId('service_id')->constrained('services')->restrictOnDelete(); $table->string('description'); $table->unsignedInteger('quantity'); $table->decimal('unit_price', 12, 2); $table->decimal('total', 12, 2); $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('folio_transactions', function (Blueprint $table) {
            $table->id(); $table->foreignId('reservation_id')->constrained()->cascadeOnDelete(); $table->foreignId('service_order_id')->nullable()->constrained()->nullOnDelete(); $table->string('type')->index(); $table->string('description'); $table->decimal('amount', 12, 2); $table->string('status')->default('posted')->index(); $table->timestamp('posted_at'); $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('folio_transactions');
        Schema::dropIfExists('service_order_items');
        Schema::dropIfExists('service_orders');
    }
};
