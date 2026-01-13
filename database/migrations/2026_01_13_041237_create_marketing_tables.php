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
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type'); // email, whatsapp, social
            $table->enum('status', ['draft', 'active', 'paused', 'completed'])->default('draft');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('target_audience_count')->default(0);
            $table->timestamps();
        });

        Schema::create('marketing_blasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('marketing_campaign_id')->nullable()->constrained()->onDelete('set null');
            $table->string('subject')->nullable();
            $table->text('content');
            $table->string('channel'); // email, whatsapp
            $table->enum('status', ['pending', 'sending', 'sent', 'failed'])->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('success_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->timestamps();
        });

        Schema::create('marketing_automations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('trigger_event'); // e.g., customer_registered, order_placed
            $table->json('actions'); // sequence of actions
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'lead_score')) {
                $table->integer('lead_score')->default(0)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('lead_score');
        });
        Schema::dropIfExists('marketing_automations');
        Schema::dropIfExists('marketing_blasts');
        Schema::dropIfExists('marketing_campaigns');
    }
};
