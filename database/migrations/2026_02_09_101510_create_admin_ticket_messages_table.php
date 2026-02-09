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
        Schema::create('admin_ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_ticket_id')->constrained('admin_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Nullable for system messages or if user deleted
            $table->text('message');
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_ticket_messages');
    }
};
