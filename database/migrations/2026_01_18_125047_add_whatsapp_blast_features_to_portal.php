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
        // 1. Customer Statuses Table
        Schema::create('customer_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('color')->default('#cbd5e1');
            $table->integer('order')->default(0);
            $table->timestamps();
            
            // Unik per company
            $table->unique(['company_id', 'name']);
        });

        // 2. Update Customers Table
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'customer_status_id')) {
                $table->foreignId('customer_status_id')->nullable()->after('status')->constrained('customer_statuses')->onDelete('set null');
            }
            if (!Schema::hasColumn('customers', 'lead_source')) {
                $table->string('lead_source')->nullable()->after('customer_status_id');
            }
            if (!Schema::hasColumn('customers', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->after('lead_source')->constrained('users')->onDelete('set null');
            }
        });

        // 3. WhatsApp Templates Table
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('whatsapp_account_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('language')->default('id');
            $table->string('category')->nullable(); // MARKETING, UTILITY, AUTHENTICATION
            $table->json('components')->nullable(); // Header, Body, Footer, Buttons
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. WhatsApp Campaigns Table
        Schema::create('whatsapp_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('whatsapp_account_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('message_template'); // Content or Template Name
            $table->string('template_name')->nullable(); // For Official Meta API
            $table->json('template_data')->nullable(); // Variables for template
            $table->enum('status', ['draft', 'scheduled', 'processing', 'completed', 'failed'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->timestamps();
        });

        // 5. WhatsApp Campaign Logs Table
        Schema::create('whatsapp_campaign_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('phone_number');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        // 6. External Apps Table
        Schema::create('external_apps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('app_key')->unique();
            $table->string('app_secret')->unique();
            $table->string('webhook_url')->nullable();
            $table->json('widget_settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 7. Update Chat Messages Table
        Schema::table('chat_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_messages', 'vendor_message_id')) {
                $table->string('vendor_message_id')->nullable()->after('id')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('vendor_message_id');
        });
        Schema::dropIfExists('external_apps');
        Schema::dropIfExists('whatsapp_campaign_logs');
        Schema::dropIfExists('whatsapp_campaigns');
        Schema::dropIfExists('whatsapp_templates');
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['customer_status_id']);
            $table->dropColumn(['assigned_to', 'lead_source', 'customer_status_id']);
        });
        Schema::dropIfExists('customer_statuses');
    }
};
