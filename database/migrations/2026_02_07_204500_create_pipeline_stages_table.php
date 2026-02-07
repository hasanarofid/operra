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
        Schema::create('pipeline_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('name');
            $table->integer('position')->default(0);
            $table->string('color')->default('#cbd5e1'); // Default slate-300
            $table->integer('probability')->default(0); // 0-100%
            $table->timestamps();

            // Index for faster queries
            $table->index(['company_id', 'position']);
        });

        // Add stage_id to sales_orders
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('stage_id')->nullable()->after('company_id');
            $table->date('expected_close_date')->nullable()->after('updated_at');
            $table->integer('probability')->nullable()->after('expected_close_date');
            
            // Foreign key constraint (optional but recommended)
            // $table->foreign('stage_id')->references('id')->on('pipeline_stages')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn(['stage_id', 'expected_close_date', 'probability']);
        });

        Schema::dropIfExists('pipeline_stages');
    }
};
