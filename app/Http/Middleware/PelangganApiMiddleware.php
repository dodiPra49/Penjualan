<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class PelangganApiMiddleware
{
    /**
     * Handle an incoming request for Pelanggan API.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Memastikan header Accept adalah application/json
        if (!$request->expectsJson()) {
            $request->headers->set('Accept', 'application/json');
        }

        // Catat waktu mulai proses
        $startTime = microtime(true);

        // 2. Lanjutkan ke Controller / Next Middleware
        $response = $next($request);

        // Hitung durasi eksekusi API
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        // 3. Tambahkan Custom Header pada Response API
        $response->headers->set('X-Api-Module', 'Pelanggan-CRUD');
        $response->headers->set('X-Execution-Time-Ms', (string)$executionTime);

        // 4. Log aktivitas request untuk auditing
        Log::info(sprintf(
            '[Pelanggan API] %s %s | IP: %s | Status: %s | Time: %sms',
            $request->method(),
            $request->fullUrl(),
            $request->ip(),
            $response->getStatusCode(),
            $executionTime
        ));

        return $response;
    }
}
