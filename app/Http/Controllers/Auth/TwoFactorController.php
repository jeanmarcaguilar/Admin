<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\TwoFactorCodeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    // POST /two-factor/email
    public function sendEmailCode(Request $request)
    {
        $validated = $request->validate([
            'username' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        // Identify user by username or email (fallback for demo)
        $user = null;
        if (!empty($validated['username'])) {
            $user = User::where('username', $validated['username'])->first();
        }
        if (!$user && !empty($validated['email'])) {
            $user = User::where('email', $validated['email'])->first();
        }

        Log::info('[2FA] Send code request', [
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'] ?? null,
            'user_found' => (bool) $user,
        ]);

        // Generate a 6-digit code
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Cache the code for 10 minutes
        // If user is not found yet, key by hashed username/email to avoid null dereference
        $cacheKey = $user
            ? $this->cacheKey($user->id)
            : '2fa_code_lookup_' . hash('sha256', ($validated['username'] ?? '') . '|' . ($validated['email'] ?? ''));
        Cache::put($cacheKey, [
            'code' => $code,
            'issued_at' => now()->toDateTimeString(),
        ], now()->addMinutes(10));

        // Determine recipient email: prefer resolved user's email, then validated email
        $recipientEmail = $user?->email ?: ($validated['email'] ?? null);
        if (!$recipientEmail) {
            return response()->json(['ok' => false, 'message' => 'User not found or email not provided'], 422);
        }

        // Send email to the target recipient
        try {
            $recipientName = $user ? ($user->name ?? $user->email) : ($validated['username'] ?? 'User');
            Mail::to($recipientEmail)->send(new TwoFactorCodeMail($recipientName, $code));
            Log::info('[2FA] Code emailed successfully');
        } catch (\Throwable $e) {
            Log::warning('[2FA] Primary mail send failed: '.$e->getMessage());
            // Fallback to log mailer (writes email content to storage/logs/laravel.log)
            try {
                Mail::mailer('log')->to($recipientEmail)->send(new TwoFactorCodeMail('User', $code));
                Log::info('[2FA] Code written via log mailer');
            } catch (\Throwable $e2) {
                Log::error('[2FA] Log mailer also failed: '.$e2->getMessage());
            }
            // Still return ok for UX; include hint when debug is on
            if (config('app.debug')) {
                return response()->json(['ok' => true, 'note' => 'mail_failed_fallback_log', 'code' => $code]);
            }
            return response()->json(['ok' => true, 'note' => 'mail_failed']);
        }

        // When debug is on, include code in response to simplify testing
        if (config('app.debug')) {
            return response()->json(['ok' => true, 'code' => $code]);
        }
        return response()->json(['ok' => true]);
    }

    public static function cacheKey($userId): string
    {
        return '2fa_code_user_' . $userId;
    }
}
