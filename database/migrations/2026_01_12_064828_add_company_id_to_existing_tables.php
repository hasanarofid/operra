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
        $tables = [
            'users',
            'customers',
            'orders',
            'inventories',
            'whatsapp_accounts',
            'products',
            'sales_orders',
            'invoices',
            'warehouses',
            'suppliers',
            'staff_activities',
            'approvals',
            'ai_usages',
            'chat_sessions',
            'stock_movements',
            'settings',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                if (!Schema::hasColumn($table->getTable(), 'company_id')) {
                    $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'stock_movements',
            'chat_sessions',
            'ai_usages',
            'approvals',
            'staff_activities',
            'suppliers',
            'warehouses',
            'invoices',
            'sales_orders',
            'products',
            'whatsapp_accounts',
            'inventories',
            'orders',
            'customers',
            'users',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'company_id')) {
                    $table->dropForeign(['company_id']);
                    $table->dropColumn('company_id');
                }
            });
        }
    }
};
