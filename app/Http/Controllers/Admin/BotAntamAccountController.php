<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;

class BotAntamAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all bot accounts with Company information
        $accounts = DB::table('bot_antam_accounts')
            ->join('companies', 'bot_antam_accounts.company_id', '=', 'companies.id')
            ->select(
                'bot_antam_accounts.*',
                'companies.name as company_name',
                'companies.id as company_id'
            )
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($account) {
                // Decrypt password if exists for display (optional, maybe just show placeholder)
                // For admin, we might want to just show "Set" or "Not Set"
                $account->has_password = !empty($account->lm_password);
                unset($account->lm_password); // Don't send encrypted password to frontend
                return $account;
            });

        return Inertia::render('Admin/BotAntam/Index', [
            'accounts' => $accounts
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'lm_username' => 'nullable|string',
            'lm_password' => 'nullable|string',
            'telegram_chat_id' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $updateData = [
            'lm_username' => $request->lm_username,
            'telegram_chat_id' => $request->telegram_chat_id,
            'is_active' => $request->is_active,
            'updated_at' => now(),
        ];

        if ($request->filled('lm_password')) {
            $updateData['lm_password'] = Crypt::encryptString($request->lm_password);
        }

        DB::table('bot_antam_accounts')
            ->where('id', $id)
            ->update($updateData);

        return redirect()->back()->with('success', 'Bot account updated successfully.');
    }
    public function logs($id)
    {
        $logs = DB::table('bot_antam_logs')
            ->where('bot_antam_account_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json($logs);
    }
}
