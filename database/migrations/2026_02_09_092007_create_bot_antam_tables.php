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
        Schema::create('bot_antam_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('lm_username')->nullable();
            $table->string('lm_password')->nullable(); // Will be encrypted
            $table->boolean('is_active')->default(false);
            $table->string('telegram_chat_id')->nullable();
            $table->json('target_products')->nullable(); // e.g. ["5g", "10g"]
            $table->timestamps();
        });

        Schema::create('bot_antam_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bot_antam_account_id')->nullable()->constrained('bot_antam_accounts')->nullOnDelete();
            $table->string('event_type'); // CHECK, FOUND, BUY_ATTEMPT, SUCCESS, ERROR
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_antam_logs');
        Schema::dropIfExists('bot_antam_accounts');
    }
};
