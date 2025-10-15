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
    
    // Sample data for the dashboard
    $stats = [
        'total_cases' => \App\Models\CaseFile::count(),
        'pending_approvals' => 0, // Default value since we don't have data yet
        'upcoming_hearings' => 0, // Default value since we don't have data yet
        'documents_pending' => 0, // Default value since we don't have data yet
    ];
    
    // Sample recent activities
    $recentActivities = [
        (object)[
            'id' => 1,
            'description' => 'New case filed by John Doe',
            'created_at' => now()->subMinutes(15),
            'type' => 'case_created'
        ],
        (object)[
            'id' => 2,
            'description' => 'Document approved by Jane Smith',
            'created_at' => now()->subHours(1),
            'type' => 'document_approved'
        ],
        (object)[
            'id' => 3,
            'description' => 'New user registered: michael@example.com',
            'created_at' => now()->subHours(3),
            'type' => 'user_registered'
        ]
    ];
    
    // Sample upcoming events
    $upcomingEvents = [
        (object)[
            'id' => 1,
            'title' => 'Case Hearing: Smith vs Johnson',
            'start_date' => now()->addDays(1),
            'location' => 'Courtroom 5B',
            'description' => 'Preliminary hearing for case #2023-045'
        ],
        (object)[
            'id' => 2,
            'title' => 'Team Meeting',
            'start_date' => now()->addDays(2),
            'location' => 'Conference Room A',
            'description' => 'Weekly team sync'
        ]
    ];
    
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

Route::middleware('auth')->group(function () {
    // Case Management
    Route::get('/case-management', function () {
        $cases = session('cases', []);
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
        return view('dashboard.compliance-tracking');
    })->name('compliance.tracking');
    
    // Contract Management
    Route::get('/contract-management', function () {
        return view('dashboard.contract-management');
    })->name('contract.management');

    // Deadline & Hearing Alerts (connected to session-backed cases)
    Route::get('/deadline-hearing-alerts', function () {
        $cases = session('cases', []);
        $today = \Carbon\Carbon::today();

        // Build a normalized hearings list from cases that have hearing_date
        $hearings = collect($cases)->filter(function ($c) {
            return !empty($c['hearing_date']);
        })->map(function ($c) {
            $d = null;
            try { $d = $c['hearing_date'] ? \Carbon\Carbon::parse($c['hearing_date']) : null; } catch (\Exception $e) {}
            return [
                'number' => $c['number'] ?? '',
                'title' => $c['name'] ?? 'Hearing',
                'type' => $c['type_badge'] ?? 'Case',
                'date' => $d ? $d->format('M d, Y') : '-',
                'time' => $c['hearing_time'] ?? '',
                'priority' => strtolower($c['status'] ?? '') === 'urgent' ? 'High' : 'Normal',
                'status' => null, // filled below once we know relative to today
                'carbon' => $d,
            ];
        })->map(function ($h) use ($today) {
            if (!$h['carbon']) { $h['status'] = 'upcoming'; return $h; }
            if ($h['carbon']->isSameDay($today)) {
                $h['status'] = 'today';
            } elseif ($h['carbon']->lessThan($today)) {
                $h['status'] = 'overdue';
            } else {
                $h['status'] = 'upcoming';
            }
            return $h;
        })->sortBy('carbon')->values()->all();

        // Counts
        $counts = [
            'today' => collect($hearings)->where('status','today')->count(),
            'upcoming' => collect($hearings)->where('status','upcoming')->count(),
            'overdue' => collect($hearings)->where('status','overdue')->count(),
        ];

        return view('dashboard.deadline-hearing-alerts', compact('hearings','counts'));
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
        $scheduledToday = collect($visitors)->filter(fn($v) => ($v['check_in_date'] ?? '') === $today && strtolower($v['status'] ?? '') === 'scheduled')->count();
        $pendingApprovals = 0;
        $stats = [
            'total_today' => $totalToday,
            'checked_in' => $checkedIn,
            'scheduled_today' => $scheduledToday,
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
            'hostId' => 'required|string|in:1,2,3,4',
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

        $id = 'V-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $fullName = trim($validated['firstName'] . ' ' . $validated['lastName']);
        $hostInfo = $hostMap[$validated['hostId']] ?? ['name' => 'Host', 'department' => ''];

        $visitor = Visitor::create([
            'code' => $id,
            'name' => $fullName,
            'company' => $validated['company'],
            'visitor_type' => $validated['visitorType'],
            'host' => $hostInfo['name'],
            'host_department' => $hostInfo['department'],
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
        return view('dashboard.scheduling-calendar', [
            'user' => auth()->user()
        ]);
    })->name('scheduling.calendar');
    
    // Approval Workflow
    Route::get('/approval-workflow', function () {
        $requests = session('approval_requests', []);
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
        $documents = session('uploaded_documents', []);
        return view('dashboard.document-upload-indexing', compact('documents'));
    })->name('document.upload.indexing');
    // Upload documents (AJAX)
    Route::post('/document-upload-indexing/upload', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'documents' => 'required|array|min:1',
            'documents.*' => 'file|max:51200', // 50MB
        ]);

        $uploaded = [];
        $existing = session('uploaded_documents', []);
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
            $doc = [
                'id' => 'DOC-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'name' => $file->getClientOriginalName(),
                'type' => $type,
                'category' => $category,
                'size' => number_format($file->getSize() / (1024 * 1024), 1) . ' MB',
                'uploaded' => now()->toDateString(),
                'status' => 'Indexed',
            ];
            $uploaded[] = $doc;
            $existing[] = $doc;
        }

        // Persist to session so they render after refresh
        session(['uploaded_documents' => $existing]);

        return response()->json([
            'success' => true,
            'message' => count($uploaded) . ' file(s) uploaded successfully!',
            'documents' => $uploaded,
        ]);
    })->name('document.upload.store');

    // Delete document (AJAX)
    Route::post('/document/{id}/delete', function ($id) {
        $docs = session('uploaded_documents', []);
        $filtered = array_values(array_filter($docs, function ($d) use ($id) {
            return ($d['id'] ?? null) !== $id;
        }));
        session(['uploaded_documents' => $filtered]);

        return response()->json([
            'success' => true,
            'message' => 'Document #' . $id . ' deleted successfully.',
        ]);
    })->name('document.delete');

    // Download document (placeholder stream)
    Route::get('/document/{id}/download', function ($id) {
        $docs = session('uploaded_documents', []);
        $doc = collect($docs)->firstWhere('id', $id);
        $fileName = ($doc['name'] ?? ($id . '.txt'));
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
    
    // Create a new case (session-backed demo endpoint)
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
        $typeBadge = $typeMap[$validated['case_type']]['badge'] ?? ucfirst($validated['case_type']);

        $client = $validated['client_name'];
        $initials = collect(explode(' ', $client))->map(fn($p) => strtoupper(substr($p,0,1)))->implode('');

        $payload = [
            'number' => $number,
            'filed' => now()->toDateString(),
            'name' => $validated['case_name'],
            'type_label' => $typeLabel,
            'type_badge' => $typeBadge,
            'client' => $client,
            'client_org' => '',
            'client_initials' => $initials ?: '--',
            'status' => $validated['status'],
            'hearing_date' => $validated['hearing_date'] ?? null,
            'hearing_time' => $validated['hearing_time'] ?? null,
        ];

        // Persist to session so the case remains after refresh (demo storage)
        $existing = session('cases', []);
        array_unshift($existing, $payload);
        session(['cases' => $existing]);

        return response()->json([
            'success' => true,
            'case' => $payload,
        ]);
    })->name('case.create');

    // Update a case (session-backed)
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
        $cases = session('cases', []);
        foreach ($cases as &$c) {
            if (($c['number'] ?? '') === $validated['number']) {
                $typeMap = [
                    'civil' => ['label' => 'Civil', 'badge' => 'Civil'],
                    'criminal' => ['label' => 'Criminal Defense', 'badge' => 'Criminal'],
                    'family' => ['label' => 'Family Law', 'badge' => 'Family'],
                    'corporate' => ['label' => 'Corporate', 'badge' => 'Corporate'],
                    'ip' => ['label' => 'Intellectual Property', 'badge' => 'IP'],
                ];
                $typeLabel = $typeMap[$validated['case_type']]['label'] ?? ucfirst($validated['case_type']);
                $typeBadge = $typeMap[$validated['case_type']]['badge'] ?? ucfirst($validated['case_type']);
                $client = $validated['client_name'];
                $initials = collect(explode(' ', $client))->map(fn($p) => strtoupper(substr($p,0,1)))->implode('');
                $c['name'] = $validated['case_name'];
                $c['client'] = $client;
                $c['client_initials'] = $initials ?: '--';
                $c['type_label'] = $typeLabel;
                $c['type_badge'] = $typeBadge;
                $c['status'] = $validated['status'];
                $c['hearing_date'] = $validated['hearing_date'] ?? null;
                $c['hearing_time'] = $validated['hearing_time'] ?? null;
                break;
            }
        }
        session(['cases' => $cases]);
        return response()->json(['success' => true, 'case' => $validated['number']]);
    })->name('case.update');

    // Delete a case (session-backed)
    Route::post('/case/delete', function (\Illuminate\Http\Request $request) {
        $request->validate(['number' => 'required|string']);
        $cases = session('cases', []);
        $filtered = array_values(array_filter($cases, fn($c) => ($c['number'] ?? '') !== $request->input('number')));
        session(['cases' => $filtered]);
        return response()->json(['success' => true]);
    })->name('case.delete');

    // Fetch a case by number (AJAX helper)
    Route::get('/case/get', function (\Illuminate\Http\Request $request) {
        $request->validate(['number' => 'required|string']);
        $cases = session('cases', []);
        $case = collect($cases)->firstWhere('number', $request->query('number'));
        if (!$case) {
            return response()->json(['success' => false, 'message' => 'Case not found'], 404);
        }
        return response()->json(['success' => true, 'case' => $case]);
    })->name('case.get');

    // Update only hearing date/time (AJAX helper)
    Route::post('/case/update-hearing', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'number' => 'required|string',
            'hearing_date' => 'nullable|date',
            'hearing_time' => 'nullable|date_format:H:i',
        ]);
        $cases = session('cases', []);
        $updated = false;
        foreach ($cases as &$c) {
            if (($c['number'] ?? '') === $validated['number']) {
                $c['hearing_date'] = $validated['hearing_date'] ?? null;
                $c['hearing_time'] = $validated['hearing_time'] ?? null;
                $updated = true;
                break;
            }
        }
        if ($updated) {
            session(['cases' => $cases]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Case not found'], 404);
    })->name('case.update.hearing');
    
    Route::get('/version/control', function () {
        $documents = session('uploaded_documents', []);
        return view('dashboard.version-control', compact('documents'));
    })->name('document.version.control');
    
    Route::get('/access/control', function () {
        return view('dashboard.access-control', [
            'user' => auth()->user()
        ]);
    })->name('document.access.control.permissions');
    
    Route::get('/archival/policy', function () {
        // Provide settings and documents from session
        $settings = session('archival_settings', [
            'default_retention' => '5',
            'auto_archive' => true,
            'notification_emails' => '',
        ]);
        $documents = session('uploaded_documents', []);
        $archivedDocuments = session('archived_documents', []);
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
        return view('dashboard.compliance-tracking');
    })->name('document.compliance.tracking');
    
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
    
    // Reservation History - session-backed
    Route::get('/reservation-history', function () {
        $bookings = session('calendar_bookings', []);
        return view('dashboard.reservation-history', compact('bookings'));
    })->name('reservation.history');
    
    // Room & Equipment Booking
    Route::get('/room-equipment', function () {
        // Get the authenticated user's bookings
        $bookings = [
            [
                'id' => 'BK-2023-001',
                'type' => 'room',
                'name' => 'Conference Room',
                'date' => '2023-10-25',
                'start_time' => '10:00',
                'end_time' => '11:30',
                'status' => 'confirmed',
                'purpose' => 'Team meeting'
            ],
            [
                'id' => 'BK-2023-002',
                'type' => 'equipment',
                'name' => 'Projector',
                'date' => '2023-10-26',
                'return_date' => '2023-10-27',
                'quantity' => 1,
                'status' => 'pending',
                'purpose' => 'Client presentation'
            ]
        ];
        
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
            
            $roomBooking = [
                'id' => 'BK-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'type' => 'room',
                'name' => ucfirst($roomValidated['room']) . ' Room',
                'date' => $roomValidated['date'],
                'start_time' => $roomValidated['start_time'],
                'end_time' => $roomValidated['end_time'],
                'status' => 'pending',
                'purpose' => $roomValidated['purpose']
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
                    
                    $equipmentBooking = [
                        'id' => 'EQ-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                        'type' => 'equipment',
                        'name' => ucfirst($equipment) . ($quantity > 1 ? ' (x' . $quantity . ')' : ''),
                        'date' => $equipmentValidated['date'],
                        'start_time' => $equipmentValidated['start_time'],
                        'end_time' => $equipmentValidated['end_time'],
                        'status' => 'pending',
                        'purpose' => $equipmentValidated['purpose']
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
        
        // Persist into session calendar store so calendar renders reliably after redirect
        $existing = session('calendar_bookings', []);
        $merged = array_merge($existing, $bookings);
        session(['calendar_bookings' => $merged]);

        // Queue approval requests for each booking
        $existingRequests = session('approval_requests', []);
        $userName = optional(auth()->user())->name ?? 'User';
        foreach ($bookings as $b) {
            $existingRequests[] = [
                'id' => $b['id'],
                'type' => 'event',
                'title' => ($b['name'] ?? 'Booking') . (isset($b['purpose']) ? (': ' . $b['purpose']) : ''),
                'requested_by' => $userName,
                'date' => $b['date'] ?? now()->toDateString(),
                'status' => 'pending',
            ];
        }
        session(['approval_requests' => $existingRequests]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'messages' => $successMessages,
                'bookings' => $bookings
            ]);
        }
        
        return redirect()->route('scheduling.calendar')->with([
            'success' => implode(' ', $successMessages),
            'new_bookings' => $bookings
        ]);
    })->name('booking.combined');

    // Approve/Reject endpoints update both approval requests and calendar bookings
    Route::post('/approval/{id}/approve', function (\Illuminate\Http\Request $request, $id) {
        $requests = session('approval_requests', []);
        foreach ($requests as &$req) {
            if ($req['id'] === $id) {
                $req['status'] = 'approved';
                break;
            }
        }
        session(['approval_requests' => $requests]);

        $bookings = session('calendar_bookings', []);
        foreach ($bookings as &$bk) {
            if (($bk['id'] ?? null) === $id) {
                $bk['status'] = 'approved';
            }
        }
        session(['calendar_bookings' => $bookings]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Request approved.'
            ]);
        }
        return back()->with('success', 'Request approved.');
    })->name('approval.approve');

    Route::post('/approval/{id}/reject', function (\Illuminate\Http\Request $request, $id) {
        $requests = session('approval_requests', []);
        foreach ($requests as &$req) {
            if ($req['id'] === $id) {
                $req['status'] = 'rejected';
                break;
            }
        }
        session(['approval_requests' => $requests]);

        $bookings = session('calendar_bookings', []);
        foreach ($bookings as &$bk) {
            if (($bk['id'] ?? null) === $id) {
                $bk['status'] = 'rejected';
            }
        }
        session(['calendar_bookings' => $bookings]);

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
                'user' => 'required_if:permission_type,user|nullable|exists:users,id',
                'group' => 'required_if:permission_type,group,department|nullable|exists:groups,id',
                'role' => 'required|in:admin,editor,viewer,custom',
                'document_type' => 'required|in:all,financial,hr,legal,other',
                'permissions' => 'required_if:role,custom|array',
                'permissions.*' => 'string|in:view,edit,delete,share,download,print',
                'notes' => 'nullable|string|max:500'
            ]);
            
            // In a real application, you would save this to the database
            $permission = [
                'id' => 'PERM-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'type' => $validated['permission_type'],
                'user_id' => $validated['user'] ?? null,
                'group_id' => $validated['group'] ?? null,
                'role' => $validated['role'],
                'document_type' => $validated['document_type'],
                'permissions' => $validated['permissions'] ?? [],
                'notes' => $validated['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully',
                'permission' => $permission
            ]);
        })->name('permissions.store');
        
        // Update an existing permission
        Route::put('/{id}', function (\Illuminate\Http\Request $request, $id) {
            $validated = $request->validate([
                'permission_type' => 'required|in:user,group,department',
                'user' => 'required_if:permission_type,user|nullable|exists:users,id',
                'group' => 'required_if:permission_type,group,department|nullable|exists:groups,id',
                'role' => 'required|in:admin,editor,viewer,custom',
                'document_type' => 'required|in:all,financial,hr,legal,other',
                'permissions' => 'required_if:role,custom|array',
                'permissions.*' => 'string|in:view,edit,delete,share,download,print',
                'notes' => 'nullable|string|max:500',
                'status' => 'sometimes|in:active,inactive'
            ]);
            
            // In a real application, you would update the permission in the database
            $permission = [
                'id' => $id,
                'type' => $validated['permission_type'],
                'user_id' => $validated['user'] ?? null,
                'group_id' => $validated['group'] ?? null,
                'role' => $validated['role'],
                'document_type' => $validated['document_type'],
                'permissions' => $validated['permissions'] ?? [],
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'] ?? 'active',
                'updated_at' => now()
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully',
                'permission' => $permission
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
            // In a real application, you would fetch the permission from the database
            $permission = [
                'id' => $id,
                'type' => 'user',
                'user_id' => 1,
                'user_name' => 'John Doe',
                'user_email' => 'john.doe@example.com',
                'role' => 'admin',
                'document_type' => 'all',
                'permissions' => ['view', 'edit', 'delete', 'share', 'download', 'print'],
                'status' => 'active',
                'notes' => 'Full access to all documents',
                'created_at' => now()->subDays(10)->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s')
            ];
            
            return response()->json([
                'success' => true,
                'permission' => $permission
            ]);
        })->name('permissions.show');
    });
    
    // Add your other protected routes here
});
