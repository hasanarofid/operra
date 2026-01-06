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
        // Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->decimal('total_amount', 15, 2);
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        // Inventory
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('sku')->unique();
            $table->integer('stock_quantity');
            $table->decimal('unit_price', 15, 2);
            $table->timestamps();
        });

        // Staff Activities
        Schema::create('staff_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('activity_type');
            $table->text('description');
            $table->timestamps();
        });

        // Approvals
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->string('request_type'); // e.g., 'expense', 'leave', 'discount'
            $table->foreignId('requester_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('approver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Subscriptions & Revenue
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name');
            $table->decimal('monthly_price', 15, 2);
            $table->integer('active_customers')->default(0);
            $table->decimal('mrr', 15, 2)->default(0); // Monthly Recurring Revenue
            $table->timestamps();
        });

        // AI Usage & Cost
        Schema::create('ai_usages', function (Blueprint $table) {
            $table->id();
            $table->string('model_name'); // e.g., 'gpt-4', 'gemini-pro'
            $table->integer('tokens_used');
            $table->decimal('estimated_cost', 10, 6);
            $table->timestamp('usage_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_usages');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('staff_activities');
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('orders');
    }
};
