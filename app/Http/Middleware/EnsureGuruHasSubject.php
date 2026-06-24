<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\MataPelajaran;

class EnsureGuruHasSubject
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        if ($user->role === 'guru') {
            $hasSubject = MataPelajaran::where('guru_id', $user->id)->exists();
            if (!$hasSubject) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda belum dikaitkan dengan mata pelajaran apapun. Silakan hubungi Administrator.'
                    ], 403);
                }

                return redirect()->route('guru.dashboard')->with('error', 'Anda belum dikaitkan dengan mata pelajaran apapun. Silakan hubungi Administrator untuk mengatur mata pelajaran Anda.');
            }
        }

        return $next($request);
    }
}
