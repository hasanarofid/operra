<?php

namespace App\Http\Controllers\BotAntam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch config and stats
        $companyId = auth()->user()->company_id;
        $account = DB::table('bot_antam_accounts')->where('company_id', $companyId)->first();

        return Inertia::render('BotAntam/Dashboard', [
             'account' => $account
        ]);
    }
}
