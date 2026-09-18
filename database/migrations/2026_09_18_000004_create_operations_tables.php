<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_plans', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('slug')->unique(); $table->text('description')->nullable();
            $table->text('cancellation_policy')->nullable(); $table->unsignedSmallInteger('minimum_stay')->default(1); $table->unsignedSmallInteger('maximum_stay')->nullable(); $table->unsignedSmallInteger('advance_days')->default(0); $table->boolean('is_active')->default(true); $table->timestamps();
        });
        Schema::create('room_rates', function (Blueprint $table) {
            $table->id(); $table->foreignId('rate_plan_id')->constrained()->cascadeOnDelete(); $table->foreignId('room_type_id')->constrained()->cascadeOnDelete(); $table->date('starts_on'); $table->date('ends_on'); $table->decimal('price', 12, 2); $table->timestamps(); $table->index(['room_type_id', 'starts_on', 'ends_on']);
        });
        Schema::create('services', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->text('description')->nullable(); $table->string('pricing_unit'); $table->boolean('is_active')->default(true); $table->timestamps();
        });
        Schema::create('service_prices', function (Blueprint $table) {
            $table->id(); $table->foreignId('service_id')->constrained()->cascadeOnDelete(); $table->date('starts_on'); $table->date('ends_on'); $table->decimal('price', 12, 2); $table->timestamps(); $table->index(['service_id', 'starts_on', 'ends_on']);
        });
        Schema::create('coupons', function (Blueprint $table) {
            $table->id(); $table->string('code')->unique(); $table->string('description')->nullable(); $table->string('discount_type'); $table->decimal('discount_value', 12, 2); $table->date('starts_on'); $table->date('ends_on'); $table->unsignedInteger('usage_limit')->nullable(); $table->unsignedInteger('per_customer_limit')->nullable(); $table->decimal('minimum_amount', 12, 2)->default(0); $table->boolean('is_active')->default(true); $table->timestamps();
        });
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id(); $table->foreignId('coupon_id')->constrained()->cascadeOnDelete(); $table->foreignId('user_id')->constrained()->cascadeOnDelete(); $table->foreignId('reservation_id')->constrained()->cascadeOnDelete(); $table->decimal('discount_amount', 12, 2); $table->timestamp('used_at'); $table->timestamps(); $table->unique(['coupon_id', 'reservation_id']);
        });
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); $table->foreignId('reservation_id')->constrained()->cascadeOnDelete(); $table->decimal('amount', 12, 2); $table->char('currency', 3)->default('USD'); $table->string('payment_method'); $table->string('transaction_id')->nullable()->unique(); $table->string('gateway')->default('manual'); $table->string('status')->index(); $table->timestamp('paid_at')->nullable(); $table->decimal('refunded_amount', 12, 2)->default(0); $table->timestamp('refunded_at')->nullable(); $table->timestamps();
        });
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id(); $table->foreignId('payment_id')->constrained()->cascadeOnDelete(); $table->string('type'); $table->decimal('amount', 12, 2); $table->string('provider_reference')->nullable(); $table->json('payload')->nullable(); $table->timestamps();
        });
        Schema::create('invoices', function (Blueprint $table) {
            $table->id(); $table->string('invoice_number')->unique(); $table->foreignId('reservation_id')->constrained()->cascadeOnDelete(); $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); $table->decimal('subtotal', 12, 2); $table->decimal('taxes', 12, 2); $table->decimal('discounts', 12, 2); $table->decimal('total', 12, 2); $table->decimal('amount_paid', 12, 2)->default(0); $table->decimal('balance', 12, 2); $table->string('status')->index(); $table->timestamp('issued_at'); $table->timestamps();
        });
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('invoice_id')->constrained()->cascadeOnDelete(); $table->string('item_type'); $table->string('description'); $table->unsignedInteger('quantity'); $table->decimal('unit_price', 12, 2); $table->decimal('total', 12, 2); $table->timestamps();
        });
        Schema::create('housekeeping_tasks', function (Blueprint $table) {
            $table->id(); $table->foreignId('room_id')->constrained()->cascadeOnDelete(); $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); $table->string('status')->index(); $table->string('priority')->default('normal'); $table->timestamp('started_at')->nullable(); $table->timestamp('completed_at')->nullable(); $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id(); $table->foreignId('room_id')->constrained()->cascadeOnDelete(); $table->foreignId('reported_by')->constrained('users')->restrictOnDelete(); $table->text('description'); $table->string('priority')->default('normal'); $table->string('status')->index(); $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); $table->timestamp('completed_at')->nullable(); $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); $table->string('action'); $table->string('entity_type'); $table->unsignedBigInteger('entity_id')->nullable(); $table->json('old_values')->nullable(); $table->json('new_values')->nullable(); $table->ipAddress('ip_address')->nullable(); $table->timestamp('created_at')->useCurrent(); $table->index(['entity_type', 'entity_id']);
        });
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary(); $table->string('type'); $table->morphs('notifiable'); $table->text('data'); $table->timestamp('read_at')->nullable(); $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['notifications', 'audit_logs', 'maintenance_requests', 'housekeeping_tasks', 'invoice_items', 'invoices', 'payment_transactions', 'payments', 'coupon_usages', 'coupons', 'service_prices', 'services', 'room_rates', 'rate_plans'] as $table) Schema::dropIfExists($table);
    }
};