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
        Schema::table('companies', function (Blueprint $col) {
            $col->foreignId('pricing_plan_id')->nullable()->constrained('pricing_plans')->nullOnDelete();
            $col->timestamp('subscription_ends_at')->nullable();
            $col->boolean('is_system_owner')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $col) {
            $col->dropForeign(['pricing_plan_id']);
            $col->dropColumn(['pricing_plan_id', 'subscription_ends_at', 'is_system_owner']);
        });
    }
};
