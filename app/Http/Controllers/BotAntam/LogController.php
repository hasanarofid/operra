<?php

namespace App\Http\Controllers\BotAntam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $accountId = DB::table('bot_antam_accounts')->where('company_id', $companyId)->value('id');

        if (!$accountId) {
             return response()->json([]);
        }

        $logs = DB::table('bot_antam_logs')
            ->where('bot_antam_account_id', $accountId)
            ->latest()
            ->take(50)
            ->get();

        return response()->json($logs);
    }
}
