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
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Basic, Pro, Enterprise
            $table->string('slug')->unique();
            $table->decimal('price', 15, 2);
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly
            $table->json('features');
            $table->boolean('is_popular')->default(false);
            $table->string('badge')->nullable(); // e.g., "Best Value"
            $table->string('cta_text')->default('Mulai Sekarang');
            $table->timestamps();
        });

        Schema::create('leads_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company_name');
            $table->string('email');
            $table->string('phone');
            $table->string('business_type'); // UMKM, Enterprise, Government
            $table->text('message');
            $table->string('status')->default('new'); // new, contacted, closed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads_requests');
        Schema::dropIfExists('pricing_plans');
    }
};
