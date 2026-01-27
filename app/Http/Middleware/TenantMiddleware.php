<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module = null): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            abort(403, 'Akun Anda belum terhubung dengan perusahaan mana pun. Silahkan hubungi admin.');
        }

        // Cek Masa Berlaku Langganan
        // if ($company->subscription_ends_at && $company->subscription_ends_at->isPast()) {
        //     abort(403, 'Masa berlaku langganan Anda telah berakhir. Silahkan lakukan perpanjangan.');
        // }

        // Jika ada parameter module, cek apakah module tersebut aktif untuk company ini
        if ($module && !$company->isModuleEnabled($module)) {
            abort(403, "Modul '$module' tidak aktif untuk perusahaan Anda.");
        }


        return $next($request);
    }
}
