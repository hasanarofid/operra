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
        // WhatsApp Accounts (Multi-user, 3rd party integration, Verified Badge)
        Schema::create('whatsapp_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Customer Service Utama"
            $table->string('phone_number')->unique();
            $table->string('provider')->default('official'); // official, third_party_api
            $table->text('api_credentials')->nullable(); // Integration with 3rd party
            $table->boolean('is_verified')->default(false); // Verified Badge (Centang Hijau)
            $table->enum('status', ['active', 'inactive', 'disconnected'])->default('active');
            $table->timestamps();
        });

        // WhatsApp Agents for Auto-assignment (Round Robin)
        Schema::create('whatsapp_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('whatsapp_account_id')->constrained()->onDelete('cascade');
            $table->boolean('is_available')->default(true);
            $table->timestamp('last_assigned_at')->nullable();
            $table->integer('active_chats_count')->default(0);
            $table->timestamps();
        });

        // Chat Sessions (Shared Team Inbox, Permission-based Access)
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_account_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['open', 'pending', 'closed', 'resolved'])->default('open');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        // Chat Messages (Centrally stored history)
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null'); // Null if sender is customer
            $table->string('sender_type'); // 'user', 'customer', 'system'
            $table->text('message_body');
            $table->string('message_type')->default('text'); // text, image, file, audio
            $table->string('attachment_path')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // CRM Lead management additions to customers table
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'status')) {
                $table->string('status')->default('lead')->after('address'); // lead, prospect, customer, lost
            }
            if (!Schema::hasColumn('customers', 'lead_source')) {
                $table->string('lead_source')->nullable()->after('status'); // whatsapp, website, organic
            }
            if (!Schema::hasColumn('customers', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->after('lead_source')->constrained('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['status', 'lead_source', 'assigned_to']);
        });
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_sessions');
        Schema::dropIfExists('whatsapp_agents');
        Schema::dropIfExists('whatsapp_accounts');
    }
};
