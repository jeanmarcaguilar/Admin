<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = session('last_activity');
            $timeout = config('session.lifetime') * 60; // Convert minutes to seconds
            
            // Check if session has timed out
            if ($lastActivity && (time() - $lastActivity) > $timeout) {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Session expired due to inactivity',
                        'redirect' => route('login')
                    ], 401);
                }
                
                return redirect()->route('login')->with('error', 'Session expired due to inactivity. Please login again.');
            }
            
            // Update last activity time
            session(['last_activity' => time()]);
        }
        
        return $next($request);
    }
}
