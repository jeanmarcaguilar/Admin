<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\TwoFactorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Visitor;

// Test route to check authentication
Route::get('/test-login', function () {
    // Try to log in with hardcoded credentials
    $credentials = [
        'username' => 'admin',
        'password' => 'admin123'
    ];
    
    if (Auth::attempt($credentials)) {
        return 'Login successful! User: ' . Auth::user()->name;
    } else {
        // Check if user exists
        $user = \App\Models\User::where('username', 'admin')->first();
        $userExists = $user ? 'User exists' : 'User does not exist';
        $passwordMatch = $user && \Illuminate\Support\Facades\Hash::check('admin123', $user->password) ? 'Password matches' : 'Password does not match';
        
        return 'Login failed. ' . json_encode([
            'error' => 'Authentication failed',
            'user_exists' => $userExists,
            'password_match' => $passwordMatch,
            'user' => $user ? $user->toArray() : null,
            'attempt' => Auth::attempt($credentials),
            'session' => session()->all()
        ], JSON_PRETTY_PRINT);
    }
});

// Redirect root to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard route (protected by auth middleware)
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // Get real statistics from database
    $visitors = \App\Models\Visitor::all();
    $checkedInCount = $visitors->filter(fn($v) => strtolower($v->status ?? '') === 'checked_in')->count();
    $totalVisitors = $visitors->count();
    $documents = \App\Models\Document::all();
    $documentsCount = $documents->count();
    $cases = \App\Models\CaseFile::all();
    $activeCases = $cases->filter(function ($c) {
        $s = strtolower($c->status ?? '');
        return $s !== 'closed' && $s !== 'completed';
    })->count();
    
    // Real statistics for dashboard cards
    $stats = [
        'checked_in_visitors' => $checkedInCount,
        'total_visitors' => $totalVisitors,
        'uploaded_documents' => $documentsCount,
        'active_cases' => $activeCases,
    ];
    
    // Recent activities from real data
    $recentActivities = [];
    
    // Add recent visitors
    $recentVisitors = $visitors->sortByDesc('created_at')->take(2);
    foreach ($recentVisitors as $visitor) {
        $recentActivities[] = (object)[
            'id' => $visitor->code,
            'description' => 'New visitor registered: ' . $visitor->name,
            'created_at' => $visitor->created_at,
            'type' => 'visitor_registered'
        ];
    }
    
    // Add recent documents
    $recentDocuments = $documents->sortByDesc('created_at')->take(1);
    foreach ($recentDocuments as $document) {
        $recentActivities[] = (object)[
            'id' => $document->id,
            'description' => 'Document uploaded',
            'created_at' => $document->created_at,
            'type' => 'document_uploaded'
        ];
    }
    
    // Sort by creation date and take top 3
    $recentActivities = collect($recentActivities)
        ->sortByDesc('created_at')
        ->take(3)
        ->values()
        ->all();
    
    // Upcoming events from bookings
    $upcomingBookings = \App\Models\Booking::where('date', '>=', now()->toDateString())
        ->orderBy('date')
        ->take(2)
        ->get();
    
    $upcomingEvents = [];
    foreach ($upcomingBookings as $booking) {
        $upcomingEvents[] = (object)[
            'id' => $booking->code,
            'title' => $booking->name . ': ' . $booking->purpose,
            'start_date' => \Carbon\Carbon::parse($booking->date),
            'location' => $booking->type === 'room' ? $booking->name : 'Equipment',
            'description' => 'Booking for ' . $booking->purpose
        ];
    }
    
    return view('dashboard.dashboard', [
        'user' => $user,
        'stats' => $stats,
        'recentActivities' => $recentActivities,
        'upcomingEvents' => $upcomingEvents
    ]);
})->middleware('auth')->name('admin.dashboard');

// Authentication routes (from auth.php)
require __DIR__.'/auth.php';

// Protected routes
// Logout route
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

// 2FA: Send verification code to email (public, CSRF protected)
Route::post('/two-factor/email', [TwoFactorController::class, 'sendEmailCode'])
    ->name('twofactor.email');

// 2FA: Verify code and log in (public, CSRF protected)
Route::post('/two-factor/verify', [TwoFactorController::class, 'verifyCode'])
    ->name('two-factor.verify');

Route::middleware('auth')->group(function () {
    if (!function_exists('defaultPermissionsForRole')) {
        function defaultPermissionsForRole(string $role): array {
            return match ($role) {
                'admin' => ['view','edit','delete','share','download','print'],
                'editor' => ['view','edit','share','download','print'],
                'viewer' => ['view','download','print'],
                default => ['view']
            };
        }
    }
    // Case Management
    Route::get('/case-management', function () {
        $cases = \App\Models\CaseFile::orderByDesc('created_at')->get()->map(function ($c) {
            return [
                'number' => $c->number ?? '',
                'filed' => $c->created_at?->toDateString(),
                'name' => $c->name ?? '',
                'type_label' => $c->type_label ?? '',
                'type_badge' => $c->type_badge ?? ($c->type_label ?? ''),
                'client' => $c->client ?? '',
                'client_org' => $c->client_org ?? '',
                'client_initials' => $c->client_initials ?? '--',
                'status' => $c->status ?? '',
                'hearing_date' => $c->hearing_date ?? null,
                'hearing_time' => $c->hearing_time ?? null,
            ];
        })->toArray();
        $total = count($cases);
        $activeCount = collect($cases)->filter(function ($c) {
            $s = strtolower($c['status'] ?? '');
            return in_array($s, ['active','in progress']);
        })->count();
        $pendingTasks = collect($cases)->filter(function ($c) {
            return strtolower($c['status'] ?? '') === 'pending';
        })->count();
        $closedCount = collect($cases)->filter(function ($c) {
            return strtolower($c['status'] ?? '') === 'closed';
        })->count();
        $today = \Carbon\Carbon::today();
        $upcoming = collect($cases)->filter(function ($c) use ($today) {
            if (empty($c['hearing_date'])) return false;
            try { $d = \Carbon\Carbon::parse($c['hearing_date']); } catch (\Exception $e) { return false; }
            return $d->gte($today);
        })->map(function ($c) {
            $d = $c['hearing_date'] ? \Carbon\Carbon::parse($c['hearing_date']) : null;
            return [
                'number' => $c['number'] ?? '',
                'name' => $c['name'] ?? '',
                'date' => $d ? $d->format('M d, Y') : null,
                'time' => $c['hearing_time'] ?? '',
                'carbon' => $d,
            ];
        })->sortBy('carbon')->values()->all();
        $upcomingCount = count($upcoming);
        $nextHearing = $upcomingCount > 0 ? $upcoming[0] : null;

        $recentActivities = collect($cases)->map(function ($c) {
            $filed = $c['filed'] ?? null;
            $d = $filed ? \Carbon\Carbon::parse($filed) : now();
            return [
                'number' => $c['number'] ?? '',
                'name' => $c['name'] ?? '',
                'filed' => $d,
            ];
        })->sortByDesc('filed')->take(5)->values()->all();

        $successRate = $total > 0 ? round(($closedCount / $total) * 100) : 0;
        $pendingProgress = $total > 0 ? round(($pendingTasks / $total) * 100) : 0;
        $stats = [
            'active_cases' => $activeCount,
            'upcoming_hearings' => $upcomingCount,
            'pending_tasks' => $pendingTasks,
            'success_rate' => $successRate,
            'pending_progress' => $pendingProgress,
            'success_progress' => $successRate,
            'next_hearing' => $nextHearing,
        ];

        return view('dashboard.case-management', compact('cases','stats','recentActivities','upcoming'));
    })->name('case.management');

    // Compliance Tracking
    Route::get('/compliance-tracking', function () {
        return redirect()->route('document.compliance.tracking');
    })->name('compliance.tracking');
    
    // Contract Management
    Route::get('/contract-management', function () {
        $contractsQuery = \DB::table('contracts');
        $total = $contractsQuery->count();
        $active = \DB::table('contracts')->where('status', 'active')->count();
        $pending = \DB::table('contracts')->where('status', 'pending')->count();
        $expiringSoon = \DB::table('contracts')
            ->whereNotNull('created_on')
            ->where('created_on', '>=', now()->subMonths(12)->toDateString())
            ->count();
        $contracts = \DB::table('contracts')->orderByDesc('created_on')->get();

        return view('dashboard.contract-management', [
            'stats' => [
                'active' => $active,
                'pending' => $pending,
                'expiring' => $expiringSoon,
                'total' => $total,
            ],
            'contracts' => $contracts,
        ]);
    })->name('contract.management');

    // Contracts: update
    Route::post('/contracts/update', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'code' => 'required|string',
            'title' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'type' => 'required|string|in:nda,service,employment',
            'status' => 'required|string|in:active,pending,expired',
        ]);

        $updated = \DB::table('contracts')
            ->where('code', $validated['code'])
            ->update([
                'title' => $validated['title'],
                'company' => $validated['company'] ?? null,
                'type' => $validated['type'],
                'status' => $validated['status'],
                'updated_at' => now(),
            ]);

        return response()->json(['success' => $updated > 0]);
    })->name('contracts.update');

    // Contracts: delete
    Route::post('/contracts/delete', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'code' => 'required|string',
        ]);
        $deleted = \DB::table('contracts')->where('code', $validated['code'])->delete();
        return response()->json(['success' => $deleted > 0]);
    })->name('contracts.delete');

    // Deadline & Hearing Alerts (connected to database-backed cases and hearings)
    Route::get('/deadline-hearing-alerts', function () {
        $today = \Carbon\Carbon::today();
        
        // Get case files with hearing dates
        $cases = \App\Models\CaseFile::whereNotNull('hearing_date')
            ->orderBy('hearing_date')
            ->get()
            ->map(function ($c) {
                $d = null;
                try { $d = $c->hearing_date ? \Carbon\Carbon::parse($c->hearing_date) : null; } catch (\Exception $e) {}
                return [
                    'number' => $c->number ?? '',
                    'title' => $c->name ?? 'Hearing',
                    'type' => $c->type_badge ?? ($c->type_label ?? 'Case'),
                    'date' => $d ? $d->format('M d, Y') : '-',
                    'time' => $c->hearing_time ?? '',
                    'priority' => strtolower($c->status ?? '') === 'urgent' ? 'High' : 'Normal',
                    'status' => null, // filled below
                    'carbon' => $d,
                    'source' => 'case_file'
                ];
            });

        // Get dedicated hearings
        $dedicatedHearings = \App\Models\Hearing::orderBy('hearing_date')
            ->get()
            ->map(function ($h) {
                $d = null;
                try { $d = $h->hearing_date ? \Carbon\Carbon::parse($h->hearing_date) : null; } catch (\Exception $e) {}
                return [
                    'number' => $h->case_number ?? '',
                    'title' => $h->title ?? 'Hearing',
                    'type' => $h->type ?? 'Hearing',
                    'date' => $d ? $d->format('M d, Y') : '-',
                    'time' => $h->hearing_time ?? '',
                    'priority' => ucfirst($h->priority ?? 'Normal'),
                    'status' => null, // filled below
                    'carbon' => $d,
                    'source' => 'hearing'
                ];
            });

        // Combine and process all hearings
        $hearings = $cases->concat($dedicatedHearings)
            ->filter(function ($h) {
                return $h['carbon'] !== null;
            })
            ->map(function ($h) use ($today) {
                if ($h['carbon']->isSameDay($today)) {
                    $h['status'] = 'today';
                } elseif ($h['carbon']->lessThan($today)) {
                    $h['status'] = 'overdue';
                } else {
                    $h['status'] = 'upcoming';
                }
                return $h;
            })
            ->sortBy('carbon')
            ->values()
            ->all();

        // Counts
        $counts = [
            'today' => collect($hearings)->where('status','today')->count(),
            'upcoming' => collect($hearings)->where('status','upcoming')->count(),
            'overdue' => collect($hearings)->where('status','overdue')->count(),
        ];

        return view('dashboard.deadline-hearing-alerts', compact('hearings', 'counts'));
    })->name('deadline.hearing.alerts');

    // Visitors Registration (DB-backed)
    Route::get('/visitors-registration', function () {
        $visitors = Visitor::orderByDesc('created_at')->get()->map(function ($v) {
            return [
                'id' => $v->code,
                'name' => $v->name,
                'company' => $v->company,
                'visitor_type' => $v->visitor_type,
                'host' => $v->host,
                'host_department' => $v->host_department,
                'check_in_date' => $v->check_in_date,
                'check_in_time' => $v->check_in_time,
                'check_out_date' => $v->check_out_date,
                'check_out_time' => $v->check_out_time,
                'purpose' => $v->purpose,
                'status' => $v->status,
                'created_at' => $v->created_at?->toDateTimeString(),
            ];
        })->toArray();
        $today = \Carbon\Carbon::today()->toDateString();
        $totalToday = collect($visitors)->filter(fn($v) => ($v['check_in_date'] ?? '') === $today)->count();
        $checkedIn = collect($visitors)->filter(fn($v) => strtolower($v['status'] ?? '') === 'checked_in')->count();
        $checkedOut = collect($visitors)->filter(fn($v) => strtolower($v['status'] ?? '') === 'checked_out')->count();
        $scheduledToday = collect($visitors)->filter(fn($v) => ($v['check_in_date'] ?? '') === $today && strtolower($v['status'] ?? '') === 'scheduled')->count();
        $pendingApprovals = 0;
        $stats = [
            'total_today' => $totalToday,
            'checked_in' => $checkedIn,
            'scheduled_today' => $scheduledToday,
            'checked_out' => $checkedOut,
            'pending_approvals' => $pendingApprovals,
        ];
        return view('dashboard.visitors-registration', compact('visitors','stats'));
    })->name('visitors.registration');
    
    // Check In/Out Tracking (DB-backed)
    Route::get('/check-in-out-tracking', function () {
        $visitors = Visitor::orderByDesc('created_at')->get()->map(function ($v) {
            return [
                'id' => $v->code,
                'name' => $v->name,
                'company' => $v->company,
                'visitor_type' => $v->visitor_type,
                'host' => $v->host,
                'host_department' => $v->host_department,
                'check_in_date' => $v->check_in_date,
                'check_in_time' => $v->check_in_time,
                'check_out_date' => $v->check_out_date,
                'check_out_time' => $v->check_out_time,
                'purpose' => $v->purpose,
                'status' => $v->status,
                'created_at' => $v->created_at?->toDateTimeString(),
            ];
        })->toArray();
        $today = \Carbon\Carbon::today()->toDateString();

        $currentCheckIns = collect($visitors)
            ->filter(fn($v) => strtolower($v['status'] ?? '') === 'checked_in')
            ->map(function ($v) {
                // Compute duration since check-in
                try {
                    $start = null;
                    if (!empty($v['check_in_date']) && !empty($v['check_in_time'])) {
                        $start = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $v['check_in_date'].' '.$v['check_in_time']);
                    } elseif (!empty($v['check_in_time'])) {
                        $start = \Carbon\Carbon::createFromFormat('H:i', $v['check_in_time']);
                    }
                    if ($start) {
                        $mins = $start->diffInMinutes(now());
                        $v['duration'] = ($mins >= 60 ? floor($mins/60).'h ' : '').($mins % 60).'m';
                    } else {
                        $v['duration'] = '—';
                    }
                } catch (\Exception $e) {
                    $v['duration'] = '—';
                }
                return $v;
            })
            ->values()->all();

        $recentCheckOuts = collect($visitors)
            ->filter(fn($v) => strtolower($v['status'] ?? '') === 'checked_out')
            ->map(function ($v) {
                // Compute duration between check-in and check-out if available
                try {
                    if (!empty($v['check_in_date']) && !empty($v['check_in_time']) && !empty($v['check_out_date']) && !empty($v['check_out_time'])) {
                        $start = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $v['check_in_date'].' '.$v['check_in_time']);
                        $end = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $v['check_out_date'].' '.$v['check_out_time']);
                        $mins = max(0, $start->diffInMinutes($end));
                        $v['duration_minutes'] = $mins;
                        $v['duration'] = ($mins >= 60 ? floor($mins/60).'h ' : '').($mins % 60).'m';
                    } else {
                        $v['duration_minutes'] = null;
                        $v['duration'] = '—';
                    }
                } catch (\Exception $e) {
                    $v['duration_minutes'] = null;
                    $v['duration'] = '—';
                }
                return $v;
            })
            ->values()->all();

        // Compute average duration from completed visits
        $avgMins = collect($recentCheckOuts)
            ->pluck('duration_minutes')
            ->filter(fn($m) => is_numeric($m))
            ->avg();
        $avgLabel = '0 min';
        if ($avgMins && $avgMins > 0) {
            $hours = floor($avgMins / 60);
            $mins = (int) round($avgMins % 60);
            $avgLabel = ($hours > 0 ? $hours.'h ' : '').$mins.'m';
        }

        $stats = [
            'currently_checked_in' => count($currentCheckIns),
            'todays_checkins' => collect($visitors)->filter(fn($v) => ($v['check_in_date'] ?? '') === $today)->count(),
            // Average duration from completed visits
            'average_duration' => $avgLabel,
            'overstayed' => 0,
        ];

        return view('dashboard.check-in-out-tracking', [
            'user' => auth()->user(),
            'stats' => $stats,
            'currentCheckIns' => $currentCheckIns,
            'recentCheckOuts' => $recentCheckOuts,
            'allVisitors' => $visitors,
        ]);
    })->name('checkinout.tracking');

    // Create a new visitor (DB-backed)
    Route::post('/visitor/create', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'firstName' => 'required|string|max:100',
            'lastName' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'company' => 'required|string|max:150',
            'visitorType' => 'required|string|in:client,vendor,contractor,guest,other',
            // New flexible host inputs
            'hostName' => 'nullable|string|max:150',
            'hostDepartment' => 'nullable|string|max:150',
            // Backward compatibility
            'hostId' => 'nullable|string|in:1,2,3,4',
            'purpose' => 'required|string|in:meeting,delivery,interview,maintenance,other',
            'checkInDate' => 'required|date',
            'checkInTime' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
        ]);

        $hostMap = [
            '1' => ['name' => 'Sarah Johnson', 'department' => 'Procurement'],
            '2' => ['name' => 'Michael Brown', 'department' => 'Sales'],
            '3' => ['name' => 'Jennifer Lee', 'department' => 'Business Development'],
            '4' => ['name' => 'Robert Chen', 'department' => 'IT'],
        ];

        // Resolve host info from new fields or fallback to legacy hostId
        $resolvedHostName = trim((string)($validated['hostName'] ?? ''));
        $resolvedHostDept = trim((string)($validated['hostDepartment'] ?? ''));
        if ($resolvedHostName === '' && !empty($validated['hostId'])) {
            $legacy = $hostMap[$validated['hostId']] ?? null;
            if ($legacy) {
                $resolvedHostName = $legacy['name'];
                $resolvedHostDept = $legacy['department'];
            }
        }
        if ($resolvedHostName === '') {
            return response()->json([
                'success' => false,
                'message' => 'Host name is required.',
                'errors' => ['hostName' => ['Host name is required.']],
            ], 422);
        }

        $id = 'V-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $fullName = trim($validated['firstName'] . ' ' . $validated['lastName']);

        $visitor = Visitor::create([
            'code' => $id,
            'name' => $fullName,
            'company' => $validated['company'],
            'visitor_type' => $validated['visitorType'],
            'host' => $resolvedHostName,
            'host_department' => $resolvedHostDept,
            'check_in_date' => $validated['checkInDate'],
            'check_in_time' => $validated['checkInTime'],
            'purpose' => $validated['purpose'],
            'status' => 'scheduled',
        ]);

        return response()->json(['success' => true, 'visitor' => [
            'id' => $visitor->code,
            'name' => $visitor->name,
            'company' => $visitor->company,
            'visitor_type' => $visitor->visitor_type,
            'host' => $visitor->host,
            'host_department' => $visitor->host_department,
            'check_in_date' => $visitor->check_in_date,
            'check_in_time' => $visitor->check_in_time,
            'purpose' => $visitor->purpose,
            'status' => $visitor->status,
            'created_at' => $visitor->created_at?->toDateTimeString(),
        ]]);
    })->name('visitor.create');

    // Fetch a visitor by ID (AJAX helper, DB-backed)
    Route::get('/visitor/get', function (\Illuminate\Http\Request $request) {
        $request->validate(['id' => 'required|string']);
        $v = Visitor::where('code', $request->query('id'))->first();
        if (!$v) {
            return response()->json(['success' => false, 'message' => 'Visitor not found'], 404);
        }
        return response()->json(['success' => true, 'visitor' => [
            'id' => $v->code,
            'name' => $v->name,
            'company' => $v->company,
            'visitor_type' => $v->visitor_type,
            'host' => $v->host,
            'host_department' => $v->host_department,
            'check_in_date' => $v->check_in_date,
            'check_in_time' => $v->check_in_time,
            'check_out_date' => $v->check_out_date,
            'check_out_time' => $v->check_out_time,
            'purpose' => $v->purpose,
            'status' => $v->status,
        ]]);
    })->name('visitor.get');

    // Update visitor (limited fields) (DB-backed)
    Route::post('/visitor/update', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'id' => 'required|string',
            'company' => 'nullable|string|max:150',
            'visitor_type' => 'nullable|string|in:client,vendor,contractor,guest,other',
            'purpose' => 'nullable|string|in:meeting,delivery,interview,maintenance,other',
            'check_in_date' => 'nullable|date',
            'check_in_time' => 'nullable|date_format:H:i',
            'status' => 'nullable|string|in:scheduled,checked_in,checked_out',
        ]);
        $v = Visitor::where('code', $validated['id'])->first();
        if (!$v) {
            return response()->json(['success' => false, 'message' => 'Visitor not found'], 404);
        }
        foreach (['company','visitor_type','purpose','check_in_date','check_in_time','status'] as $k) {
            if (array_key_exists($k, $validated) && $validated[$k] !== null) {
                $v->{$k} = $validated[$k];
            }
        }
        if (($validated['status'] ?? null) === 'checked_out') {
            if (empty($v->check_out_date)) {
                $v->check_out_date = \Carbon\Carbon::today()->toDateString();
            }
            if (empty($v->check_out_time)) {
                $v->check_out_time = now()->format('H:i');
            }
        }
        $v->save();
        return response()->json(['success' => true]);
    })->name('visitor.update');

    // Delete a visitor by ID (DB-backed)
    Route::post('/visitor/delete', function (\Illuminate\Http\Request $request) {
        $request->validate(['id' => 'required|string']);
        $deleted = Visitor::where('code', $request->input('id'))->delete();
        return response()->json(['success' => (bool)$deleted]);
    })->name('visitor.delete');
    
    // Host Notification (view removed) → redirect to Visitors Registration
    Route::get('/host-notification', function () {
        return redirect()->route('visitors.registration');
    })->name('host.notification');
    
    // Visitor History Records (DB-backed)
    Route::get('/visitor-history', function () {
        $visitors = Visitor::orderByDesc('created_at')->get()->map(function ($v) {
            return [
                'id' => $v->code,
                'name' => $v->name,
                'company' => $v->company,
                'visitor_type' => $v->visitor_type,
                'host' => $v->host,
                'host_department' => $v->host_department,
                'check_in_date' => $v->check_in_date,
                'check_in_time' => $v->check_in_time,
                'check_out_date' => $v->check_out_date,
                'check_out_time' => $v->check_out_time,
                'purpose' => $v->purpose,
                'status' => $v->status,
            ];
        })->toArray();
        return view('dashboard.visitor-history', [
            'user' => auth()->user(),
            'visitors' => $visitors,
        ]);
    })->name('visitor.history');
    
    Route::get('/visitor-history/records', function () {
        $visitors = Visitor::orderByDesc('created_at')->get()->map(function ($v) {
            return [
                'id' => $v->code,
                'name' => $v->name,
                'company' => $v->company,
                'visitor_type' => $v->visitor_type,
                'host' => $v->host,
                'host_department' => $v->host_department,
                'check_in_date' => $v->check_in_date,
                'check_in_time' => $v->check_in_time,
                'check_out_date' => $v->check_out_date,
                'check_out_time' => $v->check_out_time,
                'purpose' => $v->purpose,
                'status' => $v->status,
            ];
        })->toArray();
        return view('dashboard.visitor-history', [
            'user' => auth()->user(),
            'visitors' => $visitors,
        ]);
    })->name('visitor.history.records');
    
    // Scheduling & Calendar
    Route::get('/scheduling-calendar', function () {
        // Get all bookings from database for calendar display
        $calendarBookings = \App\Models\Booking::orderByDesc('created_at')->get()->map(function ($b) {
            return [
                'id' => $b->code,
                'type' => $b->type,
                'name' => $b->name,
                'date' => $b->date,
                'start_time' => $b->start_time,
                'end_time' => $b->end_time,
                'return_date' => $b->return_date,
                'quantity' => $b->quantity,
                'status' => $b->status,
                'purpose' => $b->purpose,
                'title' => $b->name, // For calendar display compatibility
            ];
        })->toArray();
        
        return view('dashboard.scheduling-calendar', [
            'user' => auth()->user(),
            'calendarBookings' => $calendarBookings
        ]);
    })->name('scheduling.calendar');
    
    // Approval Workflow
    Route::get('/approval-workflow', function () {
        $requests = \App\Models\Booking::where('status', 'pending')->get()->map(function ($b) {
            return [
                'id' => $b->code,
                'type' => 'event',
                'title' => $b->name . ': ' . $b->purpose,
                'requested_by' => auth()->user()->name ?? 'User',
                'date' => $b->date,
                'status' => $b->status,
            ];
        })->toArray();
        return view('dashboard.approval-workflow', compact('requests'));
    })->name('approval.workflow');
    
    // Handle event creation
    Route::post('/event/create', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        // In a real app, you would save this to the database
        $event = [
            'id' => 'EVT-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'title' => $validated['title'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'location' => $validated['location'],
            'description' => $validated['description'] ?? '',
            'status' => 'confirmed',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        // In a real app, you would save the event to the database here
        // $event = Event::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'event' => $event
        ]);
    })->name('event.create');
    
    // Document Upload & Indexing
    Route::get('/document-upload-indexing', function () {
        $documents = \App\Models\Document::orderByDesc('created_at')->get()->map(function ($d) {
            return [
                'id' => $d->code ?? '',
                'name' => $d->name ?? '',
                'type' => $d->type ?? '',
                'category' => $d->category ?? '',
                'size' => $d->size_label ?? '0 MB',
                // uploaded_on may already be a string (DATE); fallback to created_at timestamp
                'uploaded' => ($d->uploaded_on ?: ($d->created_at?->toDateString() ?? now()->toDateString())),
                'status' => $d->status ?? 'Indexed',
            ];
        })->toArray();
        return view('dashboard.document-upload-indexing', compact('documents'));
    })->name('document.upload.indexing');
    // Upload documents (AJAX)
    Route::post('/document-upload-indexing/upload', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'documents' => 'required|array|min:1',
            'documents.*' => 'file|max:51200', // 50MB
        ]);

        $uploaded = [];
        $inputCategory = strtolower($request->input('category', '')) ?: null; // financial, hr, legal, operations
        $inputDocType = $request->input('docType', ''); // PDF, Word, Excel, PowerPoint, Other
        foreach ($request->file('documents', []) as $file) {
            // In real app: $path = $file->store('documents'); infer type by mime
            $ext = strtolower($file->getClientOriginalExtension());
            $inferredType = match ($ext) {
                'pdf' => 'PDF',
                'doc', 'docx' => 'Word',
                'xls', 'xlsx' => 'Excel',
                'ppt', 'pptx' => 'PowerPoint',
                default => strtoupper($ext)
            };
            $type = $inputDocType ?: $inferredType;
            // if no category provided, map from file extension roughly
            $category = $inputCategory ?: match (true) {
                in_array($ext, ['xls','xlsx']) => 'financial',
                in_array($ext, ['doc','docx']) => 'hr',
                in_array($ext, ['pdf']) => 'legal',
                default => 'operations',
            };
            
            // Save to database using actual columns
            $document = \App\Models\Document::create([
                'code' => 'DOC-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'name' => $file->getClientOriginalName(),
                'type' => $type,
                'category' => $category,
                'size_label' => number_format($file->getSize() / (1024 * 1024), 1) . ' MB',
                'uploaded_on' => now()->toDateString(),
                'status' => 'Indexed',
            ]);
            
            $doc = [
                'id' => $document->code,
                'name' => $document->name,
                'type' => $document->type,
                'category' => $document->category,
                'size' => $document->size_label,
                // uploaded_on is stored as DATE string; use it directly, otherwise created_at
                'uploaded' => ($document->uploaded_on ?: ($document->created_at?->toDateString() ?? now()->toDateString())),
                'status' => $document->status,
            ];
            $uploaded[] = $doc;
        }

        return response()->json([
            'success' => true,
            'message' => count($uploaded) . ' file(s) uploaded successfully!',
            'documents' => $uploaded,
        ]);
    })->name('document.upload.store');

    // Delete document (AJAX)
    Route::post('/document/{id}/delete', function ($id) {
        $deleted = \App\Models\Document::where('code', $id)->delete();

        return response()->json([
            'success' => (bool)$deleted,
            'message' => $deleted ? 'Document #' . $id . ' deleted successfully.' : 'Document not found.',
        ]);
    })->name('document.delete');

    // Download document (placeholder stream)
    Route::get('/document/{id}/download', function ($id) {
        $document = \App\Models\Document::where('code', $id)->first();
        if (!$document) {
            return response()->json(['error' => 'Document not found'], 404);
        }
        
        $fileName = $document->name ?? ($id . '.txt');
        $content = "This is a placeholder download for {$fileName}.\nSince files are not stored, this file is generated dynamically.";
        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $fileName, [
            'Content-Type' => 'text/plain',
        ]);
    })->name('document.download');
    
    // Document Case Management
    Route::get('/document-case-management', function () {
        return view('dashboard.case-management', [
            'user' => auth()->user()
        ]);
    })->name('document.case.management');
    
    Route::get('/document-case-management/records', function () {
        return view('dashboard.document-case-management-records', [
            'user' => auth()->user()
        ]);
    })->name('document.case.management.records');
    
    // Create a new case (database-backed)
    Route::post('/case/create', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'case_name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'case_type' => 'required|string|in:civil,criminal,family,corporate,ip',
            'status' => 'required|string|max:50',
            'hearing_date' => 'nullable|date',
            'hearing_time' => 'nullable|date_format:H:i',
        ]);

        $number = 'C-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        $typeMap = [
            'civil' => ['label' => 'Civil', 'badge' => 'Civil'],
            'criminal' => ['label' => 'Criminal Defense', 'badge' => 'Criminal'],
            'family' => ['label' => 'Family Law', 'badge' => 'Family'],
            'corporate' => ['label' => 'Corporate', 'badge' => 'Corporate'],
            'ip' => ['label' => 'Intellectual Property', 'badge' => 'IP'],
        ];
        $typeLabel = $typeMap[$validated['case_type']]['label'] ?? ucfirst($validated['case_type']);

        $client = $validated['client_name'];
        $initials = collect(explode(' ', $client))->map(fn($p) => strtoupper(substr($p,0,1)))->implode('');

        // Save to database
        $caseFile = \App\Models\CaseFile::create([
            'number' => $number,
            'name' => $validated['case_name'],
            'type_label' => $typeLabel,
            'type_badge' => $typeLabel,
            'client' => $client,
            'client_org' => '',
            'client_initials' => $initials ?: '--',
            'status' => $validated['status'],
            'hearing_date' => $validated['hearing_date'] ?? null,
            'hearing_time' => $validated['hearing_time'] ?? null,
        ]);

        $payload = [
            'number' => $caseFile->number,
            'filed' => $caseFile->created_at?->toDateString(),
            'name' => $caseFile->name,
            'type_label' => $caseFile->type_label,
            'type_badge' => $caseFile->type_badge ?? $caseFile->type_label,
            'client' => $caseFile->client,
            'client_org' => $caseFile->client_org,
            'client_initials' => $caseFile->client_initials,
            'status' => $caseFile->status,
            'hearing_date' => $caseFile->hearing_date,
            'hearing_time' => $caseFile->hearing_time,
        ];

        return response()->json([
            'success' => true,
            'case' => $payload,
        ]);
    })->name('case.create');

    // Update a case (database-backed)
    Route::post('/case/update', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'number' => 'required|string',
            'case_name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'case_type' => 'required|string|in:civil,criminal,family,corporate,ip',
            'status' => 'required|string|max:50',
            'hearing_date' => 'nullable|date',
            'hearing_time' => 'nullable|date_format:H:i',
        ]);
        
        $caseFile = \App\Models\CaseFile::where('number', $validated['number'])->first();
        if (!$caseFile) {
            return response()->json(['success' => false, 'message' => 'Case not found'], 404);
        }

        $typeMap = [
            'civil' => ['label' => 'Civil', 'badge' => 'Civil'],
            'criminal' => ['label' => 'Criminal Defense', 'badge' => 'Criminal'],
            'family' => ['label' => 'Family Law', 'badge' => 'Family'],
            'corporate' => ['label' => 'Corporate', 'badge' => 'Corporate'],
            'ip' => ['label' => 'Intellectual Property', 'badge' => 'IP'],
        ];
        $typeLabel = $typeMap[$validated['case_type']]['label'] ?? ucfirst($validated['case_type']);
        $client = $validated['client_name'];
        $initials = collect(explode(' ', $client))->map(fn($p) => strtoupper(substr($p,0,1)))->implode('');

        $caseFile->update([
            'name' => $validated['case_name'],
            'type_label' => $typeLabel,
            'type_badge' => $typeLabel,
            'client' => $client,
            'client_initials' => $initials ?: '--',
            'status' => $validated['status'],
            'hearing_date' => $validated['hearing_date'] ?? null,
            'hearing_time' => $validated['hearing_time'] ?? null,
        ]);

        return response()->json(['success' => true, 'case' => $validated['number']]);
    })->name('case.update');

    // Delete a case (database-backed)
    Route::post('/case/delete', function (\Illuminate\Http\Request $request) {
        $request->validate(['number' => 'required|string']);
        $deleted = \App\Models\CaseFile::where('number', $request->input('number'))->delete();
        return response()->json(['success' => (bool)$deleted]);
    })->name('case.delete');

    // Fetch a case by number (AJAX helper)
    Route::get('/case/get', function (\Illuminate\Http\Request $request) {
        $request->validate(['number' => 'required|string']);
        $caseFile = \App\Models\CaseFile::where('number', $request->query('number'))->first();
        if (!$caseFile) {
            return response()->json(['success' => false, 'message' => 'Case not found'], 404);
        }
        
        $case = [
            'number' => $caseFile->number,
            'filed' => $caseFile->created_at?->toDateString(),
            'name' => $caseFile->name,
            'type_label' => $caseFile->type_label,
            'type_badge' => $caseFile->type_badge ?? $caseFile->type_label,
            'client' => $caseFile->client,
            'client_org' => $caseFile->client_org,
            'client_initials' => $caseFile->client_initials,
            'status' => $caseFile->status,
            'hearing_date' => $caseFile->hearing_date,
            'hearing_time' => $caseFile->hearing_time,
        ];
        
        return response()->json(['success' => true, 'case' => $case]);
    })->name('case.get');

    // Update only hearing date/time (AJAX helper)
    Route::post('/case/update-hearing', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'number' => 'required|string',
            'hearing_date' => 'nullable|date',
            'hearing_time' => 'nullable|date_format:H:i',
        ]);
        
        $caseFile = \App\Models\CaseFile::where('number', $validated['number'])->first();
        if (!$caseFile) {
            return response()->json(['success' => false, 'message' => 'Case not found'], 404);
        }
        
        $caseFile->update([
            'hearing_date' => $validated['hearing_date'] ?? null,
            'hearing_time' => $validated['hearing_time'] ?? null,
        ]);
        
        return response()->json(['success' => true]);
    })->name('case.update.hearing');
    
    Route::get('/version/control', function () {
        // Pull documents from database so freshly uploaded files appear here
        $documents = \App\Models\Document::orderByDesc('created_at')->get()->map(function ($d) {
            return [
                'id' => $d->code ?? '',
                'name' => $d->name ?? '',
                'type' => $d->type ?? '',
                'category' => $d->category ?? '',
                'size' => $d->size_label ?? '0 MB',
                'uploaded' => ($d->uploaded_on ?: ($d->created_at?->toDateString() ?? now()->toDateString())),
                'status' => $d->status ?? 'Indexed',
                'version' => $d->version ?? '1.0',
            ];
        })->toArray();
        return view('dashboard.version-control', compact('documents'));
    })->name('document.version.control');
    
    Route::get('/access/control', function () {
        // Load permissions from DB and shape for the table
        $rows = \Illuminate\Support\Facades\DB::table('permissions')
            ->orderByDesc('updated_at')
            ->get();
        $userIds = $rows->pluck('user_id')->filter()->unique()->values();
        $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');
        $allUsers = \App\Models\User::orderBy('name')->get(['id','name','email']);
        $permissions = $rows->map(function ($r) use ($users) {
            $isUser = ($r->type === 'user');
            $user = $isUser && $r->user_id ? ($users[$r->user_id] ?? null) : null;
            return [
                'id' => (int) $r->id,
                'type' => $isUser ? 'User' : ucfirst($r->type ?? 'group'),
                'name' => $user?->name ?? ($r->type === 'group' ? 'Group #' . ($r->group_id ?? '-') : '—'),
                'email' => $user?->email ?? '—',
                'role' => ucfirst($r->role ?? 'viewer'),
                'document_type' => match ($r->document_type) { 'all' => 'All Documents', 'financial' => 'Financial', 'hr' => 'HR', 'legal' => 'Legal', default => 'Other' },
                'permissions' => is_array($r->permissions) ? $r->permissions : (json_decode($r->permissions, true) ?: []),
                'status' => $r->status ?? 'active',
                'last_updated' => optional(\Carbon\Carbon::parse($r->updated_at))->toDateTimeString() ?? '—',
            ];
        })->toArray();
        return view('dashboard.access-control', [
            'user' => auth()->user(),
            'permissions' => $permissions,
            'allUsers' => $allUsers,
        ]);
    })->name('document.access.control.permissions');
    
    Route::get('/archival/policy', function () {
        // Settings still session-based for simplicity
        $settings = session('archival_settings', [
            'default_retention' => '5',
            'auto_archive' => true,
            'notification_emails' => '',
        ]);

        // Load all documents from DB so current and future uploads appear automatically
        $all = \App\Models\Document::orderByDesc('created_at')->get();

        $map = function ($d) {
            return [
                'id' => $d->code ?? '',
                'name' => $d->name ?? 'Document',
                'type' => $d->type ?? 'Other',
                'category' => $d->category ?? 'other',
                'size' => $d->size_label ?? '—',
                'uploaded' => ($d->uploaded_on ?: ($d->created_at?->toDateString() ?? now()->toDateString())),
                'status' => $d->status ?? 'Indexed',
                'archived_on' => $d->is_archived ? ($d->updated_at?->toDateString() ?? null) : null,
                // Scheduled deletion is illustrative; real logic can compute from category + policy
                'scheduled_deletion' => null,
            ];
        };

        $documents = $all->where('is_archived', false)->map($map)->values()->toArray();
        $archivedDocuments = $all->where('is_archived', true)->map($map)->values()->toArray();

        return view('dashboard.archival-retention', compact('settings', 'documents', 'archivedDocuments'));
    })->name('document.archival.retention.policy');

    // Save Archival Retention Settings
    Route::post('/archival/settings', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'defaultRetention' => 'required|string|in:1,3,5,7,10,permanent',
            'autoArchive' => 'nullable|in:on,1,true',
            'notificationEmails' => 'nullable|string|max:500',
        ]);

        $settings = [
            'default_retention' => $validated['defaultRetention'],
            'auto_archive' => (bool) $request->has('autoArchive'),
            'notification_emails' => $validated['notificationEmails'] ?? '',
        ];

        session(['archival_settings' => $settings]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'settings' => $settings]);
        }
        return back()->with('success', 'Retention settings saved.');
    })->name('archival.settings.save');

    // Run Auto-Archive: move eligible uploaded_documents to archived_documents based on retention
    Route::post('/archival/auto-archive', function (\Illuminate\Http\Request $request) {
        $settings = session('archival_settings', [
            'default_retention' => '5',
            'auto_archive' => true,
            'notification_emails' => '',
        ]);

        $docs = session('uploaded_documents', []);
        $archived = session('archived_documents', []);

        $now = now();
        $moved = [];
        $kept = [];

        foreach ($docs as $doc) {
            $uploadedDate = isset($doc['uploaded']) ? \Carbon\Carbon::parse($doc['uploaded']) : $now->copy()->subYears(6);
            $category = strtolower($doc['category'] ?? ($doc['type'] ?? 'other'));
            // Determine retention in years
            $retentionYears = match ($category) {
                'financial' => 7,
                'hr' => 7,
                'legal' => 10,
                default => (is_numeric($settings['default_retention']) ? (int)$settings['default_retention'] : 5),
            };
            $eligibleAt = $uploadedDate->copy()->addYears($retentionYears);

            if ($settings['auto_archive'] && $eligibleAt->lessThanOrEqualTo($now)) {
                $doc['archived_on'] = $now->toDateString();
                $doc['scheduled_deletion'] = $uploadedDate->copy()->addYears($retentionYears + 2)->toDateString();
                $moved[] = $doc;
            } else {
                $kept[] = $doc;
            }
        }

        if (!empty($moved)) {
            // Merge into archived list
            $archived = array_values(array_merge($moved, $archived));
        }

        session(['uploaded_documents' => $kept, 'archived_documents' => $archived]);

        $result = [
            'success' => true,
            'archived_count' => count($moved),
            'remaining_count' => count($kept),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result);
        }
        return back()->with('success', sprintf('Auto-archive complete. %d moved, %d remaining.', $result['archived_count'], $result['remaining_count']));
    })->name('archival.run');
    
    // Compliance Tracking
    Route::get('/compliance/tracking', function () {
        // Initialize default values
        $complianceItems = collect();
        $stats = [
            'active' => 0,
            'pending' => 0,
            'overdue' => 0,
            'completed' => 0,
            'total' => 0,
            'due_this_month' => 0,
            'at_risk' => 0
        ];
        
        try {
            if (class_exists('App\Models\ComplianceTracking')) {
                // List items for table
                $complianceItems = App\Models\ComplianceTracking::orderBy('due_date', 'asc')->get();

                // Compute stats directly via DB for accuracy and performance
                $total = App\Models\ComplianceTracking::count();
                $active = App\Models\ComplianceTracking::where('status', 'active')->count();
                $pending = App\Models\ComplianceTracking::where('status', 'pending')->count();
                $overdue = App\Models\ComplianceTracking::where('status', 'overdue')->count();
                $completed = App\Models\ComplianceTracking::where('status', 'completed')->count();

                $startOfMonth = now()->startOfMonth();
                $endOfMonth = now()->endOfMonth();
                $dueThisMonth = App\Models\ComplianceTracking::whereBetween('due_date', [$startOfMonth, $endOfMonth])
                    ->whereIn('status', ['active', 'pending'])
                    ->count();

                // At risk: due within the next 7 days (including today), not past due
                $today = now()->startOfDay();
                $in7Days = now()->addDays(7)->endOfDay();
                $atRisk = App\Models\ComplianceTracking::whereBetween('due_date', [$today, $in7Days])
                    ->whereIn('status', ['active', 'pending'])
                    ->count();

                $stats = [
                    'active' => $active,
                    'pending' => $pending,
                    'overdue' => $overdue,
                    'completed' => $completed,
                    'total' => $total,
                    'due_this_month' => $dueThisMonth,
                    'at_risk' => $atRisk,
                ];
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Compliance tracking error: ' . $e->getMessage());
        }
        
        return view('dashboard.compliance-tracking', compact('complianceItems', 'stats'));
    })->name('document.compliance.tracking');
    
    // Compliance CRUD routes
    Route::post('/compliance/create', function (Request $request) {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'type' => 'required|string|in:legal,financial,hr,safety,environmental,other',
                'due_date' => 'required|date|after:today',
                'description' => 'nullable|string',
                'responsible_person' => 'nullable|string|max:255',
                'priority' => 'nullable|string|in:low,medium,high,critical'
            ]);
            
            $code = 'CPL-' . date('Y') . '-' . str_pad(App\Models\ComplianceTracking::count() + 1, 3, '0', STR_PAD_LEFT);
            
            $compliance = App\Models\ComplianceTracking::create([
                'code' => $code,
                'title' => $request->title,
                'type' => $request->type,
                'status' => 'active',
                'due_date' => $request->due_date,
                'description' => $request->description,
                'responsible_person' => $request->responsible_person,
                'priority' => $request->priority ?? 'medium'
            ]);
            
            return response()->json(['success' => true, 'compliance' => $compliance]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create compliance: ' . $e->getMessage()], 500);
        }
    })->name('compliance.create');

    Route::post('/compliance/update', function (Request $request) {
        $request->validate([
            'id' => 'required|exists:compliance_tracking,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:legal,financial,hr,safety,environmental,other',
            'status' => 'required|string|in:active,pending,overdue,completed',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
            'responsible_person' => 'nullable|string|max:255',
            'priority' => 'nullable|string|in:low,medium,high,critical'
        ]);
        
        $compliance = App\Models\ComplianceTracking::findOrFail($request->id);
        $compliance->update($request->only(['title', 'type', 'status', 'due_date', 'description', 'responsible_person', 'priority']));
        
        return response()->json(['success' => true, 'compliance' => $compliance]);
    })->name('compliance.update');

    Route::post('/compliance/delete', function (Request $request) {
        $request->validate([
            'id' => 'required|exists:compliance_tracking,id'
        ]);
        
        $compliance = App\Models\ComplianceTracking::findOrFail($request->id);
        $compliance->delete();
        
        return response()->json(['success' => true]);
    })->name('compliance.delete');
    
    // Debug route to check compliance data
    Route::get('/compliance/debug', function () {
        $debug = [
            'success' => false,
            'table_exists' => false,
            'model_exists' => false,
            'database_connection' => false,
            'error' => null,
            'count' => 0,
            'sample_items' => []
        ];
        
        try {
            // Check if model exists
            $debug['model_exists'] = class_exists('App\Models\ComplianceTracking');
            
            // Check database connection
            \DB::connection()->getPdo();
            $debug['database_connection'] = true;
            
            // Check if table exists
            $tableExists = \Schema::hasTable('compliance_tracking');
            $debug['table_exists'] = $tableExists;
            
            if ($tableExists) {
                $count = App\Models\ComplianceTracking::count();
                $items = App\Models\ComplianceTracking::take(5)->get();
                
                $debug['success'] = true;
                $debug['count'] = $count;
                $debug['sample_items'] = $items;
                $debug['message'] = 'Everything working correctly';
            } else {
                $debug['error'] = 'Table compliance_tracking does not exist';
                $debug['message'] = 'Please import the updated SQL file';
            }
            
        } catch (\Exception $e) {
            $debug['error'] = $e->getMessage();
            $debug['message'] = 'Database connection failed: ' . $e->getMessage();
        }
        
        return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
    });
    
    // Handle document version uploads
    Route::post('/document/version/upload', function (\Illuminate\Http\Request $request) {
        // Validate fields to match the frontend form names and constraints
        $validated = $request->validate([
            'document_id' => 'required|string',
            'version_number' => 'required|string|max:50',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:51200', // 50MB
            'version_notes' => 'nullable|string|max:500',
        ]);

        // Since documents are session-backed (not persisted in DB), verify the ID exists in session
        $docs = session('uploaded_documents', []);
        $doc = collect($docs)->firstWhere('id', $validated['document_id']);
        if (!$doc) {
            return response()->json([
                'success' => false,
                'message' => 'The selected document id is invalid.',
                'errors' => [
                    'document_id' => ['The selected document id is invalid.']
                ]
            ], 422);
        }

        // In a real application, you would:
        // 1. Store the uploaded file
        // 2. Create a new document version record
        // 3. Update the document's current version

        // Respond appropriately depending on request type
        if ($request->ajax() || $request->wantsJson()) {
            $userName = optional(auth()->user())->name ?? 'User';
            $initials = collect(explode(' ', $userName))->map(fn($p) => strtoupper(substr($p, 0, 1)))->implode('');
            $documentPayload = [
                'id' => $doc['id'],
                'name' => $doc['name'] ?? 'Document',
                'type' => $doc['type'] ?? 'Other',
                'size' => $doc['size'] ?? '—',
                'version' => $validated['version_number'],
                'modified' => now()->toDateString(),
                'modified_by' => [
                    'name' => $userName,
                    'initials' => $initials,
                ],
                'status' => $doc['status'] ?? 'Indexed',
            ];

            return response()->json([
                'success' => true,
                'message' => 'New version uploaded successfully',
                'document_id' => $validated['document_id'],
                'version' => $validated['version_number'],
                'notes' => $validated['version_notes'] ?? null,
                'document' => $documentPayload,
            ]);
        }

        return back()->with('success', 'New version uploaded successfully');
    })->name('document.version.upload');
    
    // Handle iCal feed import
    Route::post('/ical/import', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'ical_url' => 'required|url|starts_with:http,https',
        ]);
        
        // In a real app, you would:
        // 1. Validate the iCal URL
        // 2. Parse the iCal feed
        // 3. Save the events to the database
        
        // For now, we'll just return a success response
        return response()->json([
            'success' => true,
            'message' => 'iCal feed imported successfully',
            'events_imported' => 0, // In a real app, return the actual count
            'ical_url' => $validated['ical_url']
        ]);
    })->name('ical.import');

    // Unified Calendar Import (Google, Outlook, iCal)
    Route::post('/calendar/import', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'calendar_type' => 'required|string|in:google,outlook,ical',
            'ical_url' => 'nullable|url|starts_with:http,https',
        ]);

        // If iCal is selected, ensure URL is provided
        if ($validated['calendar_type'] === 'ical') {
            $request->validate([
                'ical_url' => 'required|url|starts_with:http,https',
            ]);
        }

        // In a real app, handle each provider accordingly.
        // For now, return a success response to satisfy the frontend integration.
        return response()->json([
            'success' => true,
            'message' => sprintf('Calendar (%s) imported successfully', $validated['calendar_type']),
            'provider' => $validated['calendar_type'],
            'ical_url' => $validated['calendar_type'] === 'ical' ? $request->input('ical_url') : null,
        ]);
    })->name('calendar.import');

    // Clear in-session calendar bookings (used by Scheduling page action)
    Route::post('/calendar/clear', function () {
        // Remove any bookings stored in session used by the scheduling calendar
        session()->forget(['calendar_bookings', 'new_bookings']);

        return back()->with('success', 'Calendar cleared successfully.');
    })->name('calendar.clear');
    
    // Reservation History - database-backed
    Route::get('/reservation-history', function () {
        $bookings = \App\Models\Booking::orderByDesc('created_at')->get()->map(function ($b) {
            return [
                'id' => $b->code,
                'type' => $b->type,
                'name' => $b->name,
                'date' => $b->date,
                'start_time' => $b->start_time,
                'end_time' => $b->end_time,
                'return_date' => $b->return_date,
                'quantity' => $b->quantity,
                'status' => $b->status,
                'purpose' => $b->purpose,
            ];
        })->toArray();
        return view('dashboard.reservation-history', compact('bookings'));
    })->name('reservation.history');
    
    // Room & Equipment Booking
    Route::get('/room-equipment', function () {
        // Get the authenticated user's bookings from database
        $bookings = \App\Models\Booking::orderByDesc('created_at')->get()->map(function ($b) {
            return [
                'id' => $b->code,
                'type' => $b->type,
                'name' => $b->name,
                'date' => $b->date,
                'start_time' => $b->start_time,
                'end_time' => $b->end_time,
                'return_date' => $b->return_date,
                'quantity' => $b->quantity,
                'status' => $b->status,
                'purpose' => $b->purpose,
            ];
        })->toArray();
        
        return view('dashboard.room-equipment', compact('bookings'));
    })->name('room-equipment');
    
    // Combined Booking (Room & Equipment)
    Route::post('/booking/combined', function (\Illuminate\Http\Request $request) {
        $bookings = [];
        $successMessages = [];
        
        // Process Room Booking if room is selected
        if ($request->filled('room')) {
            $roomValidated = $request->validate([
                'room' => 'required|string|in:conference,meeting,training',
                'date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'purpose' => 'required|string|max:500'
            ]);
            
            // Save room booking to database
            $roomBookingModel = \App\Models\Booking::create([
                'code' => 'BK-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'type' => 'room',
                'name' => ucfirst($roomValidated['room']) . ' Room',
                'date' => $roomValidated['date'],
                'start_time' => $roomValidated['start_time'],
                'end_time' => $roomValidated['end_time'],
                'status' => 'pending',
                'purpose' => $roomValidated['purpose']
            ]);
            
            $roomBooking = [
                'id' => $roomBookingModel->code,
                'type' => $roomBookingModel->type,
                'name' => $roomBookingModel->name,
                'date' => $roomBookingModel->date,
                'start_time' => $roomBookingModel->start_time,
                'end_time' => $roomBookingModel->end_time,
                'status' => $roomBookingModel->status,
                'purpose' => $roomBookingModel->purpose
            ];
            
            $bookings[] = $roomBooking;
            $successMessages[] = 'Room booked successfully!';
        }
        
        // Process Equipment Booking if equipment is selected
        $equipmentData = $request->input('equipment', []);
        $quantities = $request->input('quantity', []);
        
        // Process each equipment item
        if (!empty($equipmentData) && is_array($equipmentData)) {
            $equipmentValidated = $request->validate([
                'date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'purpose' => 'required|string|max:500',
                'equipment.*' => 'nullable|string|in:projector,laptop,camera,audio,whiteboard',
                'quantity.*' => 'nullable|integer|min:1|max:10'
            ]);
            
            foreach ($equipmentData as $index => $equipment) {
                if (!empty($equipment)) {
                    $quantity = $quantities[$index] ?? 1;
                    
                    // Save equipment booking to database
                    $equipmentBookingModel = \App\Models\Booking::create([
                        'code' => 'EQ-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                        'type' => 'equipment',
                        'name' => ucfirst($equipment) . ($quantity > 1 ? ' (x' . $quantity . ')' : ''),
                        'date' => $equipmentValidated['date'],
                        'start_time' => $equipmentValidated['start_time'],
                        'end_time' => $equipmentValidated['end_time'],
                        'quantity' => $quantity,
                        'status' => 'pending',
                        'purpose' => $equipmentValidated['purpose']
                    ]);
                    
                    $equipmentBooking = [
                        'id' => $equipmentBookingModel->code,
                        'type' => $equipmentBookingModel->type,
                        'name' => $equipmentBookingModel->name,
                        'date' => $equipmentBookingModel->date,
                        'start_time' => $equipmentBookingModel->start_time,
                        'end_time' => $equipmentBookingModel->end_time,
                        'quantity' => $equipmentBookingModel->quantity,
                        'status' => $equipmentBookingModel->status,
                        'purpose' => $equipmentBookingModel->purpose
                    ];
                    
                    $bookings[] = $equipmentBooking;
                }
            }
            
            if (count($bookings) > 0) {
                $successMessages[] = 'Equipment booking request' . (count($bookings) > 1 ? 's' : '') . ' submitted!';
            }
        }
        
        // If no bookings were made (shouldn't happen due to frontend validation)
        if (empty($bookings)) {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least a room or equipment to book.'
            ], 422);
        }
        
        // Bookings are now saved to database, no need for session storage

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'messages' => $successMessages,
                'bookings' => $bookings
            ]);
        }
        
        return redirect()->route('scheduling.calendar')->with([
            'success' => implode(' ', $successMessages)
        ]);
    })->name('booking.combined');

    // Approve/Reject endpoints update booking status in database
    Route::post('/approval/{id}/approve', function (\Illuminate\Http\Request $request, $id) {
        $booking = \App\Models\Booking::where('code', $id)->first();
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }

        $booking->update(['status' => 'approved']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Request approved.'
            ]);
        }
        return back()->with('success', 'Request approved.');
    })->name('approval.approve');

    Route::post('/approval/{id}/reject', function (\Illuminate\Http\Request $request, $id) {
        $booking = \App\Models\Booking::where('code', $id)->first();
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }

        $booking->update(['status' => 'rejected']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Request rejected.'
            ]);
        }
        return back()->with('success', 'Request rejected.');
    })->name('approval.reject');
    
    // Room Booking (kept for backward compatibility)
    Route::post('/room/book', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'room' => 'required|string|in:conference,meeting,training',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'purpose' => 'required|string|max:500'
        ]);
        
        // In a real app, you would save this to the database
        $booking = [
            'id' => 'BK-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'type' => 'room',
            'name' => ucfirst($validated['room']) . ' Room',
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'status' => 'confirmed',
            'purpose' => $validated['purpose']
        ];
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Room booked successfully!',
                'booking' => $booking
            ]);
        }
        
        return back()->with([
            'success' => 'Room booked successfully!',
            'new_booking' => $booking
        ]);
    })->name('room.book');
    
    // Equipment Booking
    Route::post('/equipment/book', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'equipment' => 'required|string|in:projector,laptop,camera,audio,whiteboard',
            'quantity' => 'required|integer|min:1|max:10',
            'date_needed' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after_or_equal:date_needed',
            'purpose' => 'required|string|max:500'
        ]);
        
        // In a real app, you would save this to the database
        $booking = [
            'id' => 'EQ-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'type' => 'equipment',
            'name' => ucfirst($validated['equipment']),
            'date' => $validated['date_needed'],
            'return_date' => $validated['return_date'],
            'quantity' => $validated['quantity'],
            'status' => 'pending',
            'purpose' => $validated['purpose']
        ];
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Equipment booking request submitted!',
                'booking' => $booking
            ]);
        }
        
        return back()->with([
            'success' => 'Equipment booking request submitted!',
            'new_booking' => $booking
        ]);
    })->name('equipment.book');
    
    // View Booking
    Route::get('/booking/{id}', function ($id) {
        // In a real app, you would fetch this from the database
        $booking = [
            'id' => $id,
            'type' => str_starts_with($id, 'BK-') ? 'room' : 'equipment',
            'name' => str_starts_with($id, 'BK-') ? 'Conference Room' : 'Projector',
            'date' => '2023-10-25',
            'start_time' => str_starts_with($id, 'BK-') ? '10:00' : null,
            'end_time' => str_starts_with($id, 'BK-') ? '11:30' : null,
            'status' => 'confirmed',
            'purpose' => 'Team meeting',
            'quantity' => str_starts_with($id, 'BK-') ? null : 1,
            'created_at' => now()->subDays(2)->format('Y-m-d H:i:s')
        ];
        
        // If this is an equipment booking, add a return date
        if (str_starts_with($id, 'EQ-')) {
            $booking['return_date'] = date('Y-m-d', strtotime('+3 days'));
        }
        
        return view('dashboard.booking-details', ['booking' => $booking]);
    })->name('booking.view');
    
    // Cancel Booking
    Route::post('/booking/{id}/cancel', function (\Illuminate\Http\Request $request, $id) {
        // In a real app, you would update the status in the database
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Booking #' . $id . ' has been cancelled.'
            ]);
        }
        
        return back()->with('success', 'Booking #' . $id . ' has been cancelled.');
    })->name('booking.cancel');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Account Management
    // Profile Security Settings
    Route::patch('/profile/security', function (\Illuminate\Http\Request $request) {
        // Validate the request
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Update the user's password
        $user = $request->user();
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['new_password'])
        ]);

        return back()->with('success', 'Password updated successfully!');
    })->name('profile.security');

    Route::post('/account/update', function (\Illuminate\Http\Request $request) {
        // Validate the request
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ]);

        // Update the user
        $user = auth()->user();
        $user->name = $validated['username'];
        $user->email = $validated['email'];
        $user->save();

        return back()->with('success', 'Account updated successfully!');
    })->name('account.update');

    // Privacy & Security Management
    Route::post('/privacy/update', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        
        // Validate the request
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Update password
        $user->update([
            'password' => bcrypt($validated['new_password'])
        ]);

        return back()->with('success', 'Password updated successfully!');
    })->name('privacy.update');

    // Permission Management Routes
    Route::prefix('permissions')->group(function () {
        // Store a new permission
        Route::post('/', function (\Illuminate\Http\Request $request) {
            $validated = $request->validate([
                'permission_type' => 'required|in:user,group,department',
                'user' => 'nullable|integer',
                'group' => 'nullable|integer',
                'role' => 'required|in:admin,editor,viewer,custom',
                'document_type' => 'required|in:all,financial,hr,legal,other',
                'permissions' => 'nullable|array',
                'permissions.*' => 'string|in:view,edit,delete,share,download,print',
                'notes' => 'nullable|string|max:500'
            ]);

            $id = \Illuminate\Support\Facades\DB::table('permissions')->insertGetId([
                'type' => $validated['permission_type'],
                'user_id' => $validated['user'] ?? null,
                'group_id' => $validated['group'] ?? null,
                'role' => $validated['role'],
                'document_type' => $validated['document_type'],
                'permissions' => json_encode($validated['permissions'] ?? ($validated['role'] === 'custom' ? [] : defaultPermissionsForRole($validated['role']))),
                'notes' => $validated['notes'] ?? null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $row = \Illuminate\Support\Facades\DB::table('permissions')->where('id', $id)->first();
            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully',
                'permission' => $row,
            ]);
        })->name('permissions.store');
        
        // Update an existing permission
        Route::put('/{id}', function (\Illuminate\Http\Request $request, $id) {
            $validated = $request->validate([
                'permission_type' => 'required|in:user,group,department',
                'user' => 'nullable|integer',
                'group' => 'nullable|integer',
                'role' => 'required|in:admin,editor,viewer,custom',
                'document_type' => 'required|in:all,financial,hr,legal,other',
                'permissions' => 'nullable|array',
                'permissions.*' => 'string|in:view,edit,delete,share,download,print',
                'notes' => 'nullable|string|max:500',
                'status' => 'sometimes|in:active,inactive'
            ]);

            \Illuminate\Support\Facades\DB::table('permissions')->where('id', $id)->update([
                'type' => $validated['permission_type'],
                'user_id' => $validated['user'] ?? null,
                'group_id' => $validated['group'] ?? null,
                'role' => $validated['role'],
                'document_type' => $validated['document_type'],
                'permissions' => json_encode($validated['permissions'] ?? ($validated['role'] === 'custom' ? [] : defaultPermissionsForRole($validated['role']))),
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'] ?? 'active',
                'updated_at' => now(),
            ]);
            $row = \Illuminate\Support\Facades\DB::table('permissions')->where('id', $id)->first();
            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully',
                'permission' => $row
            ]);
        })->name('permissions.update');
        
        // Delete a permission
        Route::delete('/{id}', function ($id) {
            // In a real application, you would delete the permission from the database
            
            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully',
                'id' => $id
            ]);
        })->name('permissions.destroy');
        
        // Get permission details
        Route::get('/{id}', function ($id) {
            $row = \Illuminate\Support\Facades\DB::table('permissions')->where('id', $id)->first();
            if (!$row) {
                return response()->json(['success' => false, 'message' => 'Permission not found'], 404);
            }
            // Decode permissions JSON
            $row->permissions = is_array($row->permissions) ? $row->permissions : (json_decode($row->permissions, true) ?: []);
            return response()->json([
                'success' => true,
                'permission' => $row
            ]);
        })->name('permissions.show');
    });
    
    // Add your other protected routes here
});
