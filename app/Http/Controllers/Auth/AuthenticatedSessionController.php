<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Http\Controllers\Auth\TwoFactorController;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Enforce 2FA code verification BEFORE authenticating
        $username = (string) $request->input('username');
        $twoFaInput = trim((string) $request->input('two_factor_code', ''));

        // Require code presence
        if ($twoFaInput === '') {
            return back()
                ->withErrors(['two_factor_code' => 'Verification code is required.'])
                ->withInput($request->except('password'));
        }

        // Find user by username (login identifier)
        $user = User::where('username', $username)->first();
        if (!$user) {
            // Fall back to default auth flow; this will ultimately fail on credentials
            return back()
                ->withErrors(['username' => 'These credentials do not match our records.'])
                ->withInput($request->except('password'));
        }

        // Load cached 2FA code
        $cacheKey = TwoFactorController::cacheKey($user->id);
        $cached = Cache::get($cacheKey);
        $expected = is_array($cached) ? ($cached['code'] ?? null) : null;
        if (!$expected || $twoFaInput !== $expected) {
            return back()
                ->withErrors(['two_factor_code' => 'Invalid verification code.'])
                ->withInput($request->except('password'));
        }

        // Optionally consume the code (one-time use)
        Cache::forget($cacheKey);

        // Proceed with normal authentication
        $request->authenticate();
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
