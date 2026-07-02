<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureExambro
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika proteksi Exambro dimatikan di file .env, izinkan semua browser masuk
        if (!env('ENFORCE_EXAMBROWSER', true)) {
            return $next($request);
        }

        // Ambal header dengan beberapa fallback
        $userAgent = $request->userAgent() 
            ?: ($request->header('User-Agent') 
            ?: ($request->server('HTTP_USER_AGENT') 
            ?: 'Tidak terdeteksi'));

        $requestedWith = $request->header('X-Requested-With') 
            ?: ($request->server('HTTP_X_REQUESTED_WITH') 
            ?: 'Tidak terdeteksi');

        $userAgentLower = strtolower($userAgent);
        $requestedWithLower = strtolower($requestedWith);

        // Deteksi apakah request berasal dari Exambro
        $isExambro = str_contains($userAgentLower, 'mtssyafiiyah-exambrowser') ||
                     str_contains($userAgentLower, 'exambro') ||
                     str_contains($userAgentLower, 'exambrowser') ||
                     str_contains($userAgentLower, 'seb') || // Safe Exam Browser (Windows/macOS/iOS)
                     str_contains($userAgentLower, 'safeexambrowser') ||
                     str_contains($userAgentLower, '; wv') || // Signature WebView Android
                     str_contains($userAgentLower, ' wv') ||
                     str_contains($userAgentLower, 'mtssyafiiyah') ||
                     str_contains($userAgentLower, 'androidmobile') ||
                     $requestedWithLower === 'com.mtssyafiiyah.app';

        // Log untuk keperluan debug jika deteksi gagal
        if (!$isExambro) {
            \Illuminate\Support\Facades\Log::warning('Deteksi Exambro Gagal', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $userAgent,
                'x_requested_with' => $requestedWith
            ]);

            return response()->view('errors.exambro_required', [
                'userAgent' => $userAgent,
                'requestedWith' => $requestedWith
            ], 403);
        }

        return $next($request);
    }
}
