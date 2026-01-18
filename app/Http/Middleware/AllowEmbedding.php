<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowEmbedding
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Remove X-Frame-Options to allow embedding
        $response->headers->remove('X-Frame-Options');
        
        // Allow all for flexibility
        $response->headers->set('Content-Security-Policy', "frame-ancestors *", false);

        return $response;
    }
}

