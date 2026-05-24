<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsApplicant
{
    /**
     * Handle an incoming request.
     * Ensures only users with APPLICANT role can access the portal.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('applicant.login')
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses portal pendaftaran.');
        }

        // If user is admin, redirect to admin dashboard
        if (Auth::user()->hasRole('Developer') || Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Kepala Tata Usaha') || Auth::user()->hasRole('Staf Administrasi TU')) {
            return redirect()->route('admin.dashboard')
                ->with('info', 'Anda sudah login sebagai administrator.');
        }

        // Check if user has APPLICANT role
        if (!Auth::user()->hasRole('Applicant')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('applicant.login')
                ->with('error', 'Akun Anda tidak memiliki akses ke portal ini.');
        }

        return $next($request);
    }
}
