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
        Schema::table('bot_antam_accounts', function (Blueprint $table) {
            $table->string('target_butik')->nullable()->after('lm_password');
            $table->time('queue_time')->nullable()->after('target_butik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bot_antam_accounts', function (Blueprint $table) {
            $table->dropColumn(['target_butik', 'queue_time']);
        });
    }
};
