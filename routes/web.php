<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\TwoFactorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use App\Mail\TwoFactorCodeMail;
use App\Models\Visitor;
use App\Http\Controllers\QRController;

if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

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
        return in_array($s, ['active', 'in progress', 'urgent']);
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
        $recentActivities[] = (object) [
            'id' => $visitor->code,
            'description' => 'New visitor registered: ' . $visitor->name,
            'created_at' => $visitor->created_at,
            'type' => 'visitor_registered'
        ];
    }

    // Add recent documents
    $recentDocuments = $documents->sortByDesc('created_at')->take(1);
    foreach ($recentDocuments as $document) {
        $recentActivities[] = (object) [
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
        $upcomingEvents[] = (object) [
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
require __DIR__ . '/auth.php';

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

// Server time API endpoint (for clock synchronization)
Route::get('/api/server-time', function () {
    return response()->json([
        'timestamp' => now()->timestamp * 1000, // milliseconds
        'time' => now()->format('H:i:s'),
        'timezone' => config('app.timezone', 'Asia/Manila')
    ]);
})->middleware('auth')->name('api.server-time');

// Session timeout routes
Route::post('/session/extend', function () {
    if (Auth::check()) {
        session(['last_activity' => time()]);
        return response()->json([
            'success' => true,
            'message' => 'Session extended'
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Not authenticated'
    ], 401);
})->name('session.extend');

Route::get('/session/check', function () {
    if (Auth::check()) {
        return response()->json([
            'authenticated' => true,
            'last_activity' => session('last_activity'),
            'timeout' => config('session.lifetime') * 60
        ]);
    }

    return response()->json([
        'authenticated' => false
    ], 401);
})->name('session.check');

Route::middleware('auth')->group(function () {

    // Financial proposal status overrides (persist across refresh)
    Route::get('/api/financial-proposals/status-overrides', function (Request $request) {
        $overrides = \App\Models\FinancialProposalStatusOverride::query()
            ->get(['ref_no', 'status'])
            ->mapWithKeys(fn($row) => [trim((string) $row->ref_no) => $row->status])
            ->toArray();

        return response()->json([
            'success' => true,
            'overrides' => $overrides,
        ]);
    })->name('api.financial.status_overrides');

    Route::post('/api/financial-proposals/{refNo}/status', function (Request $request, string $refNo) {
        $request->validate([
            'status' => 'required|string|max:50',
        ]);

        $status = $request->input('status');
        $normalizedRefNo = trim($refNo);

        $row = \App\Models\FinancialProposalStatusOverride::updateOrCreate(
            ['ref_no' => $normalizedRefNo],
            ['status' => $status, 'updated_by' => auth()->id()]
        );

        return response()->json([
            'success' => true,
            'ref_no' => $row->ref_no,
            'status' => $row->status,
        ]);
    })->name('api.financial.set_status');

    // Document Upload & Indexing
    Route::get('/document-upload-indexing', function () {
        // Get documents from database
        $documents = \App\Models\Document::orderByDesc('created_at')->get()->map(function ($d) {
            return [
                'id' => $d->code ?? '', // Use code for frontend compatibility
                'db_id' => $d->id, // Add actual database ID
                'code' => $d->code ?? '',
                'name' => $d->name ?? '',
                'type' => $d->type ?? '',
                'category' => $d->category ?? '',
                'size' => $d->size ?? '0 MB',
                'uploaded' => ($d->uploaded_on ?: ($d->created_at?->toDateString() ?? now()->toDateString())),
                'status' => $d->status ?? 'Indexed',
            ];
        })->toArray();

        return view('dashboard.document-upload-indexing', [
            'user' => auth()->user(),
            'documents' => $documents
        ]);
    })->name('document.upload.indexing');

    // Document Upload Store
    Route::post('/document-upload-indexing/upload', function (Request $request) {
        $request->validate([
            'documents.*' => 'required|file|max:51200', // 50MB max
            'category' => 'nullable|string|in:financial,hr,legal,operations,contracts,utilities,projects,procurement,it,payroll',
            'docType' => 'nullable|string|in:internal,payment,vendor,release_of_funds,purchase,disbursement,receipt',
            'status' => 'nullable|string|max:50',
            'dateRange' => 'nullable|string|max:100',
        ]);

        $uploadedFiles = [];

        foreach ($request->file('documents') as $file) {
            if ($file->isValid()) {
                // Store file
                $path = $file->store('documents', 'public');

                // Get file extension and map to valid ENUM type
                $extension = strtolower($file->getClientOriginalExtension());
                $typeMapping = [
                    'pdf' => 'internal',
                    'doc' => 'internal',
                    'docx' => 'internal',
                    'xls' => 'internal',
                    'xlsx' => 'internal',
                    'ppt' => 'internal',
                    'pptx' => 'internal',
                ];

                $inferredType = $typeMapping[$extension] ?? 'internal';

                // Use provided docType or inferred type
                $documentType = $request->input('docType', $inferredType);

                // Validate that the type is a valid ENUM value
                $validTypes = ['internal', 'payment', 'vendor', 'release_of_funds', 'purchase', 'disbursement', 'receipt'];
                if (!in_array($documentType, $validTypes)) {
                    $documentType = 'internal';
                }

                // Generate document code
                $code = 'DOC-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

                // Create document record - only use columns that exist in database
                $document = \App\Models\Document::create([
                    'name' => $file->getClientOriginalName(),
                    'type' => $documentType,
                    'category' => $request->input('category', 'internal'),
                    'size' => formatBytes($file->getSize()),
                    'file_path' => $path,
                    'status' => $request->input('status', 'Indexed'),
                ]);

                // Try to add additional columns if they exist (to avoid errors)
                try {
                    if (Schema::hasColumn('documents', 'code')) {
                        $document->code = $code;
                        $document->save();
                    }
                    if (Schema::hasColumn('documents', 'file_type')) {
                        $document->file_type = $file->getMimeType();
                        $document->save();
                    }
                    if (Schema::hasColumn('documents', 'uploaded_on')) {
                        $document->uploaded_on = now()->toDateString();
                        $document->save();
                    }
                } catch (\Exception $e) {
                    // Ignore errors for optional columns
                }

                $uploadedFiles[] = [
                    'id' => $document->id,
                    'name' => $file->getClientOriginalName(),
                    'size' => formatBytes($file->getSize()),
                    'type' => $documentType,
                    'category' => $request->input('category', 'internal'),
                    'path' => $path,
                    'code' => $code,
                    'uploaded' => now()->toDateString()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($uploadedFiles) . ' file(s) uploaded successfully',
            'files' => $uploadedFiles
        ]);
    })->name('document.upload.store');

    // Document Download Route
    Route::get('/document/{id}/download', function ($id) {
        // Debug logging
        error_log("Download route called with ID: " . $id);

        // Find document by code or ID
        $document = \App\Models\Document::where('code', $id)->orWhere('id', $id)->first();

        if (!$document) {
            error_log("Document not found in database with ID: " . $id);

            // Handle virtual documents for integrated financial proposals
            if (str_starts_with($id, 'PROP-') || str_starts_with($id, 'financial_')) {
                try {
                    $apiUrl = 'https://finance.microfinancial-1.com/api/manage_proposals.php';
                    $apiResponse = @file_get_contents($apiUrl);
                    if ($apiResponse) {
                        $apiData = json_decode($apiResponse, true);
                        $proposals = $apiData['data'] ?? [];

                        $proposal = collect($proposals)->firstWhere('ref_no', $id);

                        if ($proposal) {
                            $projName = $proposal['project'] ?? 'Proposal';
                            $content = "FINANCIAL PROPOSAL DETAILS\n";
                            $content .= "==========================\n\n";
                            $content .= "Project Name:    " . $projName . "\n";
                            $content .= "Reference ID:    " . ($proposal['ref_no'] ?? $id) . "\n";
                            $content .= "Department:      " . ($proposal['department'] ?? 'N/A') . "\n";
                            $content .= "Proposed Amount: PHP " . number_format(floatval($proposal['amount'] ?? 0), 2) . "\n";
                            $content .= "Current Status:  " . ($proposal['status'] ?? 'Pending') . "\n";
                            $content .= "Date Posted:     " . ($proposal['date_posted'] ?? 'N/A') . "\n";
                            $content .= "\n------------------------------------------\n";
                            $content .= "Generated from Admin Dashboard\n";
                            $content .= "Date: " . now()->format('F d, Y H:i:s') . "\n";

                            $filename = $projName . "_" . $id . ".txt";
                            $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

                            return response($content)
                                ->header('Content-Type', 'text/plain')
                                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                        }
                    }
                } catch (\Exception $e) {
                    error_log("Error generating virtual document: " . $e->getMessage());
                }
            }
            abort(404, 'Document not found');
        }

        error_log("Document found: " . $document->name . " (ID: " . $document->id . ", Code: " . $document->code . ")");

        $filePath = null;
        $fileName = $document->name;

        // First try the stored file path
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            $filePath = Storage::disk('public')->path($document->file_path);
            error_log("Using stored file path: " . $document->file_path);
        } else {
            error_log("Stored file path not found, searching for alternatives...");
            // If stored path doesn't exist, try to find any file in documents directory
            $files = Storage::disk('public')->allFiles('documents');
            error_log("Available files: " . implode(', ', $files));

            if (!empty($files)) {
                // Try to match by file extension or use the first available file
                $documentExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                $matchingFiles = array_filter($files, function ($file) use ($documentExtension) {
                    $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
                    return strtolower($fileExtension) === strtolower($documentExtension);
                });

                if (!empty($matchingFiles)) {
                    $filePath = Storage::disk('public')->path(array_values($matchingFiles)[0]);
                    error_log("Found matching file by extension: " . array_values($matchingFiles)[0]);
                } elseif (!empty($files)) {
                    // Fallback to first available file
                    $filePath = Storage::disk('public')->path($files[0]);
                    error_log("Using fallback file: " . $files[0]);
                }
            }
        }

        if (!$filePath) {
            error_log("No file path found for document: " . $document->name);
            abort(404, 'File not found - no downloadable files available');
        }

        error_log("Final file path: " . $filePath);

        // Determine file type
        $fileType = $document->file_type ?? mime_content_type($filePath) ?? 'application/octet-stream';

        // Return file download response
        return response()->download($filePath, $fileName, [
            'Content-Type' => $fileType,
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    })->name('document.download');

    // Document Delete Route
    Route::delete('/document/{id}/delete', function ($id) {
        try {
            // Find document by code or ID
            $document = \App\Models\Document::where('code', $id)->orWhere('id', $id)->first();

            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found'
                ], 404);
            }

            // Delete file from storage if it exists
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Delete database record
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting document: ' . $e->getMessage()
            ], 500);
        }
    })->name('document.delete');

    // Document View/Details Route
    Route::get('/document/{id}/details', function ($id) {
        try {
            // Find document by code or ID
            $document = \App\Models\Document::where('code', $id)->orWhere('id', $id)->first();

            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'document' => [
                    'id' => $document->id,
                    'code' => $document->code,
                    'name' => $document->name,
                    'type' => $document->type,
                    'category' => $document->category,
                    'size' => $document->size,
                    'description' => $document->description,
                    'uploaded' => $document->uploaded_on ?: $document->created_at->toDateString(),
                    'status' => $document->status,
                    'file_type' => $document->file_type,
                    'is_shared' => $document->is_shared,
                    'amount' => $document->amount,
                    'receipt_date' => $document->receipt_date
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching document details: ' . $e->getMessage()
            ], 500);
        }
    })->name('document.details');

    // Document Share Route
    Route::post('/document/{id}/share', function ($id, Request $request) {
        try {
            // Find document by code or ID
            $document = \App\Models\Document::where('code', $id)->orWhere('id', $id)->first();

            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found'
                ], 404);
            }

            $email = $request->input('email');

            // Here you would typically:
            // 1. Generate a shareable link
            // 2. Send email notification
            // 3. Log the share action

            // For now, just mark as shared and return success
            $document->is_shared = true;
            $document->save();

            return response()->json([
                'success' => true,
                'message' => 'Document shared successfully',
                'share_link' => url('/document/' . $document->code . '/download')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sharing document: ' . $e->getMessage()
            ], 500);
        }
    })->name('document.share');

    // Document Approve Route
    Route::post('/document/{id}/approve', function ($id) {
        try {
            // Find document by code or ID
            $document = \App\Models\Document::where('code', $id)->orWhere('id', $id)->first();

            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found'
                ], 404);
            }

            // Update document status to approved
            $document->status = 'Approved';
            $document->save();

            return response()->json([
                'success' => true,
                'message' => 'Document approved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving document: ' . $e->getMessage()
            ], 500);
        }
    })->name('document.approve');

    // Document Reject Route
    Route::post('/document/{id}/reject', function ($id) {
        try {
            // Find document by code or ID
            $document = \App\Models\Document::where('code', $id)->orWhere('id', $id)->first();

            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found'
                ], 404);
            }

            // Update document status to rejected
            $document->status = 'Rejected';
            $document->save();

            return response()->json([
                'success' => true,
                'message' => 'Document rejected successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting document: ' . $e->getMessage()
            ], 500);
        }
    })->name('document.reject');

    // Test Download Route (for debugging)
    Route::get('/test-download', function () {
        // Get documents from database
        $documents = \App\Models\Document::orderByDesc('created_at')->get()->map(function ($d) {
            return [
                'id' => $d->code ?? '',
                'db_id' => $d->id,
                'code' => $d->code ?? '',
                'name' => $d->name ?? '',
                'type' => $d->type ?? '',
                'category' => $d->category ?? '',
                'size' => $d->size ?? '0 MB',
                'uploaded' => ($d->uploaded_on ?: ($d->created_at?->toDateString() ?? now()->toDateString())),
                'status' => $d->status ?? 'Indexed',
            ];
        })->toArray();

        return view('test_download', ['documents' => $documents]);
    })->name('test.download');

    // Visitor Registration Route
    Route::get('/visitor-registration', function () {
        $visitors = \App\Models\Visitor::orderByDesc('created_at')->get()->map(function ($v) {
            return [
                'id' => $v->id,
                'code' => $v->code ?? '',
                'name' => $v->name ?? '',
                'email' => $v->email ?? '',
                'phone' => $v->phone ?? '',
                'company' => $v->company ?? '',
                'visitor_type' => $v->visitor_type ?? 'Guest',
                'host' => $v->host ?? '',
                'host_department' => $v->host_department ?? '',
                'check_in_date' => $v->check_in_date ? (is_string($v->check_in_date) ? $v->check_in_date : $v->check_in_date->toDateString()) : '',
                'check_in_time' => $v->check_in_time ?? '',
                'check_out_date' => $v->check_out_date ? (is_string($v->check_out_date) ? $v->check_out_date : $v->check_out_date->toDateString()) : '',
                'check_out_time' => $v->check_out_time ?? '',
                'purpose' => $v->purpose ?? '',
                'status' => $v->status ?? 'pending',
                'notes' => $v->notes ?? '',
                'created_at' => $v->created_at ? (is_string($v->created_at) ? $v->created_at : $v->created_at->toDateString()) : now()->toDateString(),
            ];
        })->toArray();

        // Calculate statistics
        $allVisitors = \App\Models\Visitor::all();
        $now = now();
        $todayVisitors = \App\Models\Visitor::whereDate('check_in_date', $now->toDateString())->count();
        $checkedInVisitors = \App\Models\Visitor::where('status', 'checked_in')->count();
        $pendingVisitors = \App\Models\Visitor::where('status', 'pending')->count();

        $stats = [
            'total_visitors' => $allVisitors->count(),
            'today_visitors' => $todayVisitors,
            'checked_in' => $checkedInVisitors,
            'pending' => $pendingVisitors,
            'checked_out' => $allVisitors->where('status', 'checked_out')->count(),
            'expired' => $allVisitors->where('status', 'expired')->count(),
        ];

        return view('dashboard.visitors-registration', [
            'user' => auth()->user(),
            'visitors' => $visitors,
            'stats' => $stats
        ]);
    })->name('visitors.registration');

    Route::get('/visitor-history', function () {
        // Get visitors from database for history records
        $visitors = \App\Models\Visitor::orderByDesc('created_at')->get()->map(function ($v) {
            return [
                'id' => $v->id,
                'code' => $v->code ?? '',
                'name' => $v->name ?? '',
                'email' => $v->email ?? '',
                'phone' => $v->phone ?? '',
                'company' => $v->company ?? '',
                'visitor_type' => $v->visitor_type ?? 'Guest',
                'host' => $v->host ?? '',
                'host_department' => $v->host_department ?? '',
                'check_in_date' => $v->check_in_date ? (is_string($v->check_in_date) ? $v->check_in_date : $v->check_in_date->toDateString()) : '',
                'check_in_time' => $v->check_in_time ?? '',
                'check_out_date' => $v->check_out_date ? (is_string($v->check_out_date) ? $v->check_out_date : $v->check_out_date->toDateString()) : '',
                'check_out_time' => $v->check_out_time ?? '',
                'purpose' => $v->purpose ?? '',
                'status' => $v->status ?? 'pending',
                'notes' => $v->notes ?? '',
                'created_at' => $v->created_at ? (is_string($v->created_at) ? $v->created_at : $v->created_at->toDateString()) : now()->toDateString(),
            ];
        })->toArray();

        // Calculate history statistics
        $allVisitors = \App\Models\Visitor::all();
        $now = now();
        $todayVisitors = \App\Models\Visitor::whereDate('check_in_date', $now->toDateString())->count();
        $checkedInVisitors = \App\Models\Visitor::where('status', 'checked_in')->count();
        $checkedOutVisitors = \App\Models\Visitor::where('status', 'checked_out')->count();
        $pendingVisitors = \App\Models\Visitor::where('status', 'pending')->count();
        $overdueVisitors = \App\Models\Visitor::where('status', 'expired')->count();

        $stats = [
            'total_visitors' => $allVisitors->count(),
            'today_visitors' => $todayVisitors,
            'checked_in' => $checkedInVisitors,
            'checked_out' => $checkedOutVisitors,
            'pending' => $pendingVisitors,
            'overdue' => $overdueVisitors,
        ];

        return view('dashboard.visitor-history', [
            'user' => auth()->user(),
            'visitors' => $visitors,
            'stats' => $stats
        ]);
    })->name('visitor.history');

    Route::get('/visitor-history-records', function () {
        // Get visitors from database for history records
        $visitors = \App\Models\Visitor::orderByDesc('created_at')->get()->map(function ($v) {
            return [
                'id' => $v->id,
                'code' => $v->code ?? '',
                'name' => $v->name ?? '',
                'email' => $v->email ?? '',
                'phone' => $v->phone ?? '',
                'company' => $v->company ?? '',
                'visitor_type' => $v->visitor_type ?? 'Guest',
                'host' => $v->host ?? '',
                'host_department' => $v->host_department ?? '',
                'check_in_date' => $v->check_in_date ? (is_string($v->check_in_date) ? $v->check_in_date : $v->check_in_date->toDateString()) : '',
                'check_in_time' => $v->check_in_time ?? '',
                'check_out_date' => $v->check_out_date ? (is_string($v->check_out_date) ? $v->check_out_date : $v->check_out_date->toDateString()) : '',
                'check_out_time' => $v->check_out_time ?? '',
                'purpose' => $v->purpose ?? '',
                'status' => $v->status ?? 'pending',
                'notes' => $v->notes ?? '',
                'created_at' => $v->created_at ? (is_string($v->created_at) ? $v->created_at : $v->created_at->toDateString()) : now()->toDateString(),
            ];
        })->toArray();

        // Calculate history statistics
        $allVisitors = \App\Models\Visitor::all();
        $now = now();
        $todayVisitors = \App\Models\Visitor::whereDate('check_in_date', $now->toDateString())->count();
        $checkedInVisitors = \App\Models\Visitor::where('status', 'checked_in')->count();
        $checkedOutVisitors = \App\Models\Visitor::where('status', 'checked_out')->count();
        $pendingVisitors = \App\Models\Visitor::where('status', 'pending')->count();
        $overdueVisitors = \App\Models\Visitor::where('status', 'expired')->count();

        $stats = [
            'total_visitors' => $allVisitors->count(),
            'today_visitors' => $todayVisitors,
            'checked_in' => $checkedInVisitors,
            'checked_out' => $checkedOutVisitors,
            'pending' => $pendingVisitors,
            'overdue' => $overdueVisitors,
        ];

        return view('dashboard.visitor-history', [
            'user' => auth()->user(),
            'visitors' => $visitors,
            'stats' => $stats
        ]);
    })->name('visitor.history.records');

    // Document Management Routes
    Route::get('/document-version-control', function () {
        // Get documents from database
        $documents = \App\Models\Document::orderByDesc('created_at')->get()->map(function ($d) {
            return [
                'id' => $d->code ?? '',
                'name' => $d->name ?? '',
                'type' => $d->type ?? '',
                'category' => $d->category ?? '',
                'size' => $d->size ?? '0 MB',
                'uploaded' => ($d->uploaded_on ?: ($d->created_at?->toDateString() ?? now()->toDateString())),
                'status' => $d->status ?? 'Indexed',
                'version' => $d->data_type ?? '1.0', // Version from data_type field
                'description' => $d->description ?? '',
            ];
        })->toArray();

        return view('dashboard.version-control', [
            'user' => auth()->user(),
            'documents' => $documents
        ]);
    })->name('document.version.control');

    Route::post('/document-version-upload', function (Request $request) {
        $request->validate([
            'documents.*' => 'required|file|max:51200', // 50MB max
            'version' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
            'docType' => 'nullable|string|in:internal,payment,vendor,release_of_funds,purchase,disbursement,receipt',
            'category' => 'nullable|string|in:financial,hr,legal,operations,contracts,utilities,projects,procurement,it,payroll',
            'status' => 'nullable|string|max:50',
        ]);

        $uploadedFiles = [];

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                if ($file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('documents', $filename, 'public');

                    // Generate document code
                    $code = 'DOC-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

                    // Create document record with version info
                    $document = \App\Models\Document::create([
                        'code' => $code,
                        'name' => $file->getClientOriginalName(),
                        'type' => $request->input('docType', 'internal'),
                        'category' => $request->input('category', 'financial'),
                        'size' => formatBytes($file->getSize()),
                        'file_path' => $path,
                        'file_type' => $file->getMimeType(),
                        'status' => $request->input('status', 'Indexed'),
                        'uploaded_on' => now()->toDateString(),
                        'description' => $request->input('description', ''),
                        'data_type' => $request->input('version', '1.0'), // Store version in data_type
                    ]);

                    $uploadedFiles[] = [
                        'name' => $file->getClientOriginalName(),
                        'size' => formatBytes($file->getSize()),
                        'type' => $request->input('docType', 'internal'),
                        'version' => $request->input('version', '1.0'),
                        'path' => $path
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($uploadedFiles) . ' file(s) uploaded successfully',
            'files' => $uploadedFiles
        ]);
    })->name('document.version.upload');

    Route::get('/document-access-control', function () {
        // Get permissions from database
        $permissions = \App\Models\Permission::with(['user', 'document'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($p) {
                // Get user info
                $user = $p->user;
                $userName = $user ? $user->name : 'Unknown User';
                $userEmail = $user ? $user->email : 'unknown@example.com';

                // Map access level to role
                $roleMap = [
                    'admin' => 'Admin',
                    'write' => 'Editor',
                    'read' => 'Viewer'
                ];
                $role = $roleMap[$p->access_level] ?? 'Custom';

                // Get document type
                $documentType = 'All Documents';
                if ($p->document) {
                    $documentType = $p->document->category ?? 'Other';
                }

                // Determine permissions array
                $permissionsArray = [];
                if ($p->access_level === 'admin') {
                    $permissionsArray = ['view', 'edit', 'delete', 'share'];
                } elseif ($p->access_level === 'write') {
                    $permissionsArray = ['view', 'edit'];
                } else {
                    $permissionsArray = ['view'];
                }

                // Check if permission is expired
                $status = 'active';
                if ($p->expires_at && $p->expires_at->isPast()) {
                    $status = 'expired';
                }

                return [
                    'id' => $p->id,
                    'name' => $userName,
                    'email' => $userEmail,
                    'type' => $user ? 'User' : 'System',
                    'role' => $role,
                    'document_type' => $documentType,
                    'permissions' => $permissionsArray,
                    'status' => $status,
                    'created_at' => $p->created_at?->toDateString(),
                    'expires_at' => $p->expires_at?->toDateString(),
                    'description' => $p->description,
                ];
            })->toArray();

        // Get all users for dropdowns
        $allUsers = \App\Models\User::orderBy('name')->get(['id', 'name', 'email']);

        return view('dashboard.access-control', [
            'user' => auth()->user(),
            'permissions' => $permissions,
            'allUsers' => $allUsers
        ]);
    })->name('document.access.control.permissions');

    Route::post('/permissions/store', function (Request $request) {
        $request->validate([
            'permissionType' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'user_id' => 'nullable|exists:users,id',
            'document_id' => 'nullable|exists:documents,id',
            'access_level' => 'required|string|in:read,write,admin',
            'expires_at' => 'nullable|date|after:today',
        ]);

        // Create permission record (assuming a Permission model exists)
        $permission = \App\Models\Permission::create([
            'type' => $request->permissionType,
            'description' => $request->description,
            'user_id' => $request->user_id,
            'document_id' => $request->document_id,
            'access_level' => $request->access_level,
            'expires_at' => $request->expires_at,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission created successfully!',
            'permission' => $permission
        ]);
    })->name('permissions.store');

    Route::delete('/permissions/{id}', function ($id) {
        $permission = \App\Models\Permission::find($id);

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'Permission not found.'
            ], 404);
        }

        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission deleted successfully!'
        ]);
    })->name('permissions.destroy');

    // QR AI Routes
    Route::get('/qr-ai', [QRController::class, 'index'])->name('qr.dashboard');
    Route::get('/qr-ai/registration', [QRController::class, 'registration'])->name('qr.registration');
    Route::get('/qr-ai/scanner', [QRController::class, 'scanner'])->name('qr.scanner');

    // QR AI API Routes
    Route::post('/api/qr/register', [QRController::class, 'storeRegistration'])->name('api.qr.register');
    Route::post('/api/qr/verify', [QRController::class, 'verifyQR'])->name('api.qr.verify');
    Route::get('/api/qr/recent', [QRController::class, 'getRecentRegistrations'])->name('api.qr.recent');

    Route::get('/permissions/{id}', function ($id) {
        $permission = \App\Models\Permission::find($id);

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'Permission not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'permission' => $permission
        ]);
    })->name('permissions.show');

    Route::put('/permissions/{id}', function (Request $request, $id) {
        $permission = \App\Models\Permission::find($id);

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'Permission not found.'
            ], 404);
        }

        $request->validate([
            'permissionType' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'user_id' => 'nullable|exists:users,id',
            'document_id' => 'nullable|exists:documents,id',
            'access_level' => 'required|string|in:read,write,admin',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $permission->update([
            'type' => $request->permissionType,
            'description' => $request->description,
            'user_id' => $request->user_id,
            'document_id' => $request->document_id,
            'access_level' => $request->access_level,
            'expires_at' => $request->expires_at,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully!',
            'permission' => $permission
        ]);
    })->name('permissions.update');

    Route::get('/document-archival-retention', function () {
        // Get documents from database
        $documents = \App\Models\Document::orderByDesc('created_at')->get()->map(function ($d) {
            return [
                'id' => $d->code ?? '',
                'name' => $d->name ?? '',
                'type' => $d->type ?? '',
                'category' => $d->category ?? '',
                'size' => $d->size ?? '0 MB',
                'uploaded' => ($d->uploaded_on ?: ($d->created_at?->toDateString() ?? now()->toDateString())),
                'status' => $d->status ?? 'Indexed',
                'description' => $d->description ?? '',
                'created_at' => $d->created_at?->toDateString() ?? now()->toDateString(),
            ];
        })->toArray();

        // Get archived documents (those with Archived status)
        $archivedDocuments = \App\Models\Document::where('status', 'Archived')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->code ?? '',
                    'name' => $d->name ?? '',
                    'type' => $d->type ?? '',
                    'category' => $d->category ?? '',
                    'size' => $d->size ?? '0 MB',
                    'uploaded' => ($d->uploaded_on ?: ($d->created_at?->toDateString() ?? now()->toDateString())),
                    'status' => $d->status ?? 'Archived',
                    'description' => $d->description ?? '',
                    'created_at' => $d->created_at?->toDateString() ?? now()->toDateString(),
                ];
            })->toArray();

        // Default retention settings
        $settings = [
            'default_retention' => '5',
            'auto_archive' => true,
            'notification_emails' => '',
            'default_lead_time' => '7',
        ];

        return view('dashboard.archival-retention', [
            'user' => auth()->user(),
            'documents' => $documents,
            'archivedDocuments' => $archivedDocuments,
            'settings' => $settings
        ]);
    })->name('document.archival.retention.policy');

    // Facilities Management Routes
    Route::get('/room-equipment', function () {
        return view('dashboard.room-equipment', [
            'user' => auth()->user()
        ]);
    })->name('room-equipment');

    Route::get('/scheduling-calendar', function () {
        // Get bookings from database
        $calendarBookings = \App\Models\Booking::orderBy('date', 'asc')
            ->get()
            ->map(function ($b) {
                // Handle date field properly - convert to Carbon if it's a string
                $date = null;
                if ($b->date) {
                    if (is_string($b->date)) {
                        $date = \Carbon\Carbon::parse($b->date);
                    } else {
                        $date = $b->date;
                    }
                }

                return [
                    'id' => $b->id,
                    'code' => $b->code ?? '',
                    'name' => $b->name ?? '',
                    'title' => $b->name ?? '', // Use name as title for consistency
                    'type' => $b->type ?? 'room',
                    'date' => $date ? $date->toDateString() : '',
                    'start_time' => $b->start_time ?? '',
                    'end_time' => $b->end_time ?? '',
                    'purpose' => $b->purpose ?? '',
                    'status' => $b->status ?? 'pending',
                    'created_at' => $b->created_at?->toDateString(),
                    'updated_at' => $b->updated_at?->toDateString(),
                    'room' => $b->name ?? '', // Add room field for calendar display
                    'is_external' => false
                ];
            })->toArray();

        // Get approval requests from database
        $approvalBookings = \App\Models\Approval::orderBy('date', 'asc')
            ->get()
            ->map(function ($approval) {
                return [
                    'id' => 'APP-' . $approval->id,
                    'code' => $approval->request_id ?? '',
                    'name' => $approval->title,
                    'title' => $approval->title,
                    'type' => $approval->type ?? 'room',
                    'date' => $approval->date,
                    'start_time' => '09:00', // Default time for approvals
                    'end_time' => '17:00',   // Default time for approvals
                    'purpose' => $approval->description,
                    'status' => $approval->status,
                    'room' => $approval->title, // Use title as room for display
                    'requested_by' => $approval->requested_by,
                    'approved_by' => $approval->approved_by,
                    'rejected_by' => $approval->rejected_by,
                    'approved_at' => $approval->approved_at,
                    'rejected_at' => $approval->rejected_at,
                    'rejection_reason' => $approval->rejection_reason,
                    'is_external' => false
                ];
            })->toArray();

        // Get external bookings from API
        $externalBookings = [];
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get('https://hr2.microfinancial-1.com/api/training-room-bookings');

            if ($response->successful()) {
                $bookings = $response->json();

                if (!empty($bookings) && is_array($bookings)) {
                    $bookingsData = $bookings['data'] ?? $bookings;
                    $externalBookings = collect($bookingsData)->map(function ($booking) {
                        // Check if we have stored status for this external booking
                        $storedStatus = \Cache::get('external_booking_status_' . $booking['id'], $booking['status'] ?? 'pending');
                        $approvedBy = \Cache::get('external_booking_approved_by_' . $booking['id']);
                        $rejectedBy = \Cache::get('external_booking_rejected_by_' . $booking['id']);
                        $approvedAt = \Cache::get('external_booking_approved_at_' . $booking['id']);
                        $rejectedAt = \Cache::get('external_booking_rejected_at_' . $booking['id']);
                        $rejectionReason = \Cache::get('external_booking_reason_' . $booking['id']);

                        return [
                            'id' => 'EXT-' . ($booking['id'] ?? uniqid()),
                            'code' => $booking['booking_code'] ?? $booking['code'] ?? $booking['request_id'] ?? ('TRB-' . ($booking['id'] ?? '000')),
                            'name' => $booking['title'] ?? $booking['course_name'] ?? $booking['name'] ?? 'External Training',
                            'title' => $booking['title'] ?? $booking['course_name'] ?? $booking['name'] ?? 'External Training',
                            'type' => $booking['type'] ?? 'room',
                            'date' => $booking['date'] ?? $booking['session_date'] ?? $booking['booking_date'] ?? now()->toDateString(),
                            'start_time' => $booking['start_time'] ?? $booking['time_start'] ?? $booking['begin_time'] ?? $booking['start'] ?? '09:00',
                            'end_time' => $booking['end_time'] ?? $booking['time_end'] ?? $booking['finish_time'] ?? $booking['end'] ?? '17:00',
                            'purpose' => $booking['description'] ?? $booking['notes'] ?? $booking['purpose'] ?? 'External training session',
                            'status' => $storedStatus,
                            'room' => $booking['location'] ?? $booking['venue'] ?? $booking['room'] ?? 'External Room',
                            'booking_code' => $booking['booking_code'] ?? $booking['code'] ?? $booking['request_id'] ?? ('TRB-' . ($booking['id'] ?? '000')),
                            'facilitator' => $booking['facilitator'] ?? $booking['instructor'] ?? $booking['trainer'] ?? $booking['requested_by'] ?? $booking['created_by'] ?? $booking['user'] ?? null,
                            'requested_by' => $booking['requested_by'] ?? $booking['created_by'] ?? $booking['user'] ?? null,
                            'approved_by' => $approvedBy,
                            'rejected_by' => $rejectedBy,
                            'approved_at' => $approvedAt,
                            'rejected_at' => $rejectedAt,
                            'rejection_reason' => $rejectionReason,
                            'is_external' => true
                        ];
                    })->toArray();
                }
            }
        } catch (\Exception $e) {
            // Add sample external bookings for testing if API fails
            $externalBookings = [
                [
                    'id' => 'EXT-001',
                    'code' => 'TRB-001',
                    'name' => 'External Training Room A',
                    'title' => 'External Training Room A',
                    'type' => 'room',
                    'date' => '2025-02-05',
                    'start_time' => '10:00',
                    'end_time' => '16:00',
                    'purpose' => 'External API Training Session',
                    'status' => \Cache::get('external_booking_status_EXT-001', 'approved'),
                    'room' => 'External Training Room A',
                    'booking_code' => 'TRB-001',
                    'facilitator' => 'External Trainer',
                    'requested_by' => 'External Trainer',
                    'approved_by' => \Cache::get('external_booking_approved_by_EXT-001'),
                    'rejected_by' => \Cache::get('external_booking_rejected_by_EXT-001'),
                    'approved_at' => \Cache::get('external_booking_approved_at_EXT-001'),
                    'rejected_at' => \Cache::get('external_booking_rejected_at_EXT-001'),
                    'rejection_reason' => \Cache::get('external_booking_reason_EXT-001'),
                    'is_external' => true
                ],
                [
                    'id' => 'EXT-002',
                    'code' => 'TRB-002',
                    'name' => 'Conference Hall B',
                    'title' => 'Conference Hall B',
                    'type' => 'room',
                    'date' => '2025-02-06',
                    'start_time' => '13:00',
                    'end_time' => '17:00',
                    'purpose' => 'Workshop from External System',
                    'status' => \Cache::get('external_booking_status_EXT-002', 'pending'),
                    'room' => 'Conference Hall B',
                    'booking_code' => 'TRB-002',
                    'facilitator' => 'Guest Speaker',
                    'requested_by' => 'Guest Speaker',
                    'approved_by' => \Cache::get('external_booking_approved_by_EXT-002'),
                    'rejected_by' => \Cache::get('external_booking_rejected_by_EXT-002'),
                    'approved_at' => \Cache::get('external_booking_approved_at_EXT-002'),
                    'rejected_at' => \Cache::get('external_booking_rejected_at_EXT-002'),
                    'rejection_reason' => \Cache::get('external_booking_reason_EXT-002'),
                    'is_external' => true
                ]
            ];
        }

        // Merge all bookings
        $allBookings = array_merge($calendarBookings, $approvalBookings, $externalBookings);

        return view('dashboard.scheduling-calendar', [
            'user' => auth()->user(),
            'calendarBookings' => $allBookings
        ]);
    })->name('scheduling.calendar');

    Route::get('/booking/combined', function () {
        return redirect()->route('room-equipment')->with('info', 'Please use the booking form to submit bookings.');
    });

    // API Route to fetch training room bookings
    Route::get('/api/training-room-bookings', function () {
        try {
            // Make HTTP request to external API
            $response = \Illuminate\Support\Facades\Http::get('https://hr2.microfinancial-1.com/api/training-room-bookings');

            if ($response->successful()) {
                $bookings = $response->json();

                // Format the response data to match our expected structure
                $bookingsData = $bookings['data'] ?? $bookings;
                $formattedBookings = collect($bookingsData)->map(function ($booking) {
                    return [
                        'id' => $booking['id'] ?? null,
                        'request_id' => $booking['request_id'] ?? $booking['booking_code'] ?? ('TRB-' . ($booking['id'] ?? '000')),
                        'title' => $booking['title'] ?? $booking['course_name'] ?? $booking['name'] ?? 'Training Room Booking',
                        'name' => $booking['name'] ?? $booking['course_name'] ?? 'Training Room',
                        'type' => $booking['type'] ?? 'room',
                        'date' => $booking['date'] ?? $booking['session_date'] ?? $booking['booking_date'] ?? now()->toDateString(),
                        'start_time' => $booking['start_time'] ?? $booking['time_start'] ?? '09:00',
                        'end_time' => $booking['end_time'] ?? $booking['time_end'] ?? '17:00',
                        'purpose' => $booking['purpose'] ?? $booking['notes'] ?? $booking['description'] ?? 'Training session',
                        'status' => $booking['status'] ?? 'pending',
                        'requested_by' => $booking['requested_by'] ?? $booking['created_by'] ?? $booking['user'] ?? null,
                        'facilitator' => $booking['facilitator'] ?? $booking['instructor'] ?? $booking['trainer'] ?? null,
                        'lead_time' => $booking['lead_time'] ?? $booking['duration'] ?? 1,
                        'created_at' => $booking['created_at'] ?? now()->toDateTimeString(),
                        'updated_at' => $booking['updated_at'] ?? now()->toDateTimeString(),
                        'source' => 'external_api', // Mark as external data
                    ];
                })->toArray();

                return response()->json([
                    'success' => true,
                    'data' => $formattedBookings,
                    'message' => 'Training room bookings fetched successfully',
                    'count' => count($formattedBookings)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch training room bookings',
                    'error' => 'External API returned status: ' . $response->status(),
                    'response_body' => $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching training room bookings: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    })->name('api.training-room-bookings');

    // Test route to debug external API
    Route::get('/test-external-api', function () {
        try {
            $apiUrl = 'https://hr2.microfinancial-1.com/api/training-room-bookings';

            return response()->json([
                'testing_url' => $apiUrl,
                'timestamp' => now()->toDateTimeString(),
                'attempting_request' => true
            ]);

            $response = \Illuminate\Support\Facades\Http::timeout(5)->get($apiUrl);

            return response()->json([
                'url' => $apiUrl,
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body(),
                'json' => $response->json(),
                'headers' => $response->headers()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    })->name('test.external.api');

    Route::post('/booking/combined', function (Request $request) {
        $request->validate([
            'booking_type' => 'required|string|in:room,equipment,both',
            'room_id' => 'nullable|exists:rooms,id',
            'equipment_id' => 'nullable|exists:equipment,id',
            'name' => 'required|string|max:255',
            'purpose' => 'required|string|max:500',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|string',
            'end_time' => 'required|string|after:start_time',
            'attendees' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Create booking record
        $booking = \App\Models\Booking::create([
            'code' => 'BK-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'name' => $request->name,
            'type' => $request->booking_type,
            'room_id' => $request->room_id,
            'equipment_id' => $request->equipment_id,
            'purpose' => $request->purpose,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'attendees' => $request->attendees ?? 1,
            'notes' => $request->notes,
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking submitted successfully! Your booking code is: ' . $booking->code,
            'booking' => $booking
        ]);
    })->name('booking.combined');

    Route::get('/approval-workflow', function () {
        // Get real approval requests from database
        $approvals = \App\Models\Approval::orderByDesc('created_at')->get()->map(function ($approval) {
            return [
                'id' => $approval->id, // Use numeric ID for routes
                'request_id' => $approval->request_id, // Keep request_id for display
                'title' => $approval->title,
                'type' => $approval->type,
                'requested_by' => $approval->requested_by,
                'date' => $approval->date ? $approval->date->toDateString() : now()->toDateString(),
                'status' => $approval->status,
                'lead_time' => $approval->lead_time,
                'description' => $approval->description,
                'approved_by' => $approval->approved_by,
                'rejected_by' => $approval->rejected_by,
                'approved_at' => $approval->approved_at ? $approval->approved_at->toDateTimeString() : null,
                'rejected_at' => $approval->rejected_at ? $approval->rejected_at->toDateTimeString() : null,
                'is_approval' => true, // Mark as approval request
            ];
        })->toArray();

        // Fetch training room bookings from external API
        $externalBookings = [];
        try {
            $apiUrl = 'https://hr2.microfinancial-1.com/api/training-room-bookings';

            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($apiUrl);

            if ($response->successful()) {
                $bookings = $response->json();

                if (!empty($bookings) && is_array($bookings)) {
                    $bookingsData = $bookings['data'] ?? $bookings;
                    $externalBookings = collect($bookingsData)->map(function ($booking) {
                        return [
                            'id' => $booking['id'] ?? ('EXT-' . uniqid()),
                            'booking_code' => $booking['booking_code'] ?? $booking['code'] ?? $booking['request_id'] ?? ('TRB-' . ($booking['id'] ?? '000')),
                            'location' => $booking['location'] ?? $booking['venue'] ?? $booking['room'] ?? $booking['course_name'] ?? $booking['title'] ?? $booking['name'] ?? 'Training Room',
                            'facilitator' => $booking['facilitator'] ?? $booking['instructor'] ?? $booking['trainer'] ?? $booking['requested_by'] ?? $booking['created_by'] ?? $booking['user'] ?? null,
                            'status' => $booking['status'] ?? 'pending',
                            'start_time' => $booking['start_time'] ?? $booking['time_start'] ?? $booking['begin_time'] ?? $booking['start'] ?? '09:00',
                            'end_time' => $booking['end_time'] ?? $booking['time_end'] ?? $booking['finish_time'] ?? $booking['end'] ?? '17:00',
                            'request_id' => $booking['request_id'] ?? $booking['booking_code'] ?? ('TRB-' . ($booking['id'] ?? '000')),
                            'title' => $booking['title'] ?? $booking['course_name'] ?? $booking['name'] ?? 'Training Room Booking',
                            'type' => $booking['type'] ?? 'room',
                            'requested_by' => $booking['requested_by'] ?? $booking['created_by'] ?? $booking['user'] ?? null,
                            'date' => $booking['date'] ?? $booking['session_date'] ?? $booking['booking_date'] ?? now()->toDateString(),
                            'lead_time' => $booking['lead_time'] ?? $booking['duration'] ?? 1,
                            'description' => $booking['purpose'] ?? $booking['notes'] ?? $booking['description'] ?? 'External training session',
                            'approved_by' => $booking['approved_by'] ?? null,
                            'rejected_by' => $booking['rejected_by'] ?? null,
                            'approved_at' => $booking['approved_at'] ?? null,
                            'rejected_at' => $booking['rejected_at'] ?? null,
                            'rejection_reason' => $booking['reason'] ?? $booking['rejection_reason'] ?? null,
                            'is_external' => true,
                        ];
                    })->toArray();

                    \Log::info('External API bookings loaded: ' . count($externalBookings));
                } else {
                    \Log::warning('No bookings data received from external API');
                }
            } else {
                \Log::error('External API request failed with status: ' . $response->status());
            }
        } catch (\Exception $e) {
            \Log::error('Failed to fetch external bookings: ' . $e->getMessage());

            $apiUrl = 'https://hr2.microfinancial-1.com/api/training-room-bookings';

            $response = \Illuminate\Support\Facades\Http::timeout(5)->get($apiUrl);

            if ($response->successful()) {
                $bookings = $response->json();

                if (!empty($bookings) && is_array($bookings)) {
                    $bookingsData = $bookings['data'] ?? $bookings;
                    $externalBookings = collect($bookingsData)->map(function ($booking) {
                        return [
                            'id' => $booking['id'] ?? ('EXT-' . uniqid()),
                            'booking_code' => $booking['booking_code'] ?? $booking['code'] ?? $booking['request_id'] ?? ('TRB-' . ($booking['id'] ?? '000')),
                            'location' => $booking['location'] ?? $booking['venue'] ?? $booking['room'] ?? $booking['course_name'] ?? $booking['title'] ?? $booking['name'] ?? 'Training Room',
                            'facilitator' => $booking['facilitator'] ?? $booking['instructor'] ?? $booking['trainer'] ?? $booking['requested_by'] ?? $booking['created_by'] ?? $booking['user'] ?? null,
                            'status' => $booking['status'] ?? 'pending',
                            'start_time' => $booking['start_time'] ?? $booking['time_start'] ?? $booking['begin_time'] ?? $booking['start'] ?? '09:00',
                            'end_time' => $booking['end_time'] ?? $booking['time_end'] ?? $booking['finish_time'] ?? $booking['end'] ?? '17:00',
                            'is_external' => true,
                            'approved_by' => $booking['approved_by'] ?? null,
                            'rejected_by' => $booking['rejected_by'] ?? null,
                            'approved_at' => $booking['approved_at'] ?? null,
                            'rejected_at' => $booking['rejected_at'] ?? null,
                            'rejection_reason' => $booking['reason'] ?? $booking['rejection_reason'] ?? null,
                        ];
                    })->toArray();
                }
            } else {
                \Log::error('External API debug request failed: ' . $response->status());
            }

            \Log::info('Using sample external bookings for testing: ' . count($externalBookings));
        }

        // Combine approval requests and external bookings
        $allRequests = array_merge($approvals, $externalBookings);

        // Sort by date descending (newest first)
        usort($allRequests, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        $pendingCount = collect($allRequests)->where('status', 'pending')->count();
        $externalCount = collect($allRequests)->where('is_external', true)->count();

        // Debug logging
        \Log::info('Total requests: ' . count($allRequests));
        \Log::info('External count: ' . $externalCount);
        \Log::info('Pending count: ' . $pendingCount);
        \Log::info('Sample request data: ' . json_encode(array_slice($allRequests, 0, 2)));

        return view('dashboard.approval-workflow', [
            'user' => auth()->user(),
            'requests' => $allRequests,
            'pendingCount' => $pendingCount,
            'externalCount' => $externalCount
        ]);
    })->name('approval.workflow');

    // Debug route to test approval workflow data
    Route::get('/debug-approval-data', function () {
        try {
            // Fetch training room bookings from external API
            $externalBookings = [];
            $apiUrl = 'https://hr2.microfinancial-1.com/api/training-room-bookings';

            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($apiUrl);

            if ($response->successful()) {
                $bookings = $response->json();

                if (!empty($bookings) && is_array($bookings)) {
                    $bookingsData = $bookings['data'] ?? $bookings;
                    $externalBookings = collect($bookingsData)->map(function ($booking) {
                        return [
                            'id' => $booking['id'] ?? ('EXT-' . uniqid()),
                            'booking_code' => $booking['booking_code'] ?? $booking['code'] ?? $booking['request_id'] ?? ('TRB-' . ($booking['id'] ?? '000')),
                            'location' => $booking['location'] ?? $booking['venue'] ?? $booking['room'] ?? $booking['course_name'] ?? $booking['title'] ?? $booking['name'] ?? 'Training Room',
                            'facilitator' => $booking['facilitator'] ?? $booking['instructor'] ?? $booking['trainer'] ?? $booking['requested_by'] ?? $booking['created_by'] ?? $booking['user'] ?? null,
                            'status' => $booking['status'] ?? 'pending',
                            'start_time' => $booking['start_time'] ?? $booking['time_start'] ?? $booking['begin_time'] ?? $booking['start'] ?? '09:00',
                            'end_time' => $booking['end_time'] ?? $booking['time_end'] ?? $booking['finish_time'] ?? $booking['end'] ?? '17:00',
                            'is_external' => true,
                            'approved_by' => $booking['approved_by'] ?? null,
                            'rejected_by' => $booking['rejected_by'] ?? null,
                            'approved_at' => $booking['approved_at'] ?? null,
                            'rejected_at' => $booking['rejected_at'] ?? null,
                            'rejection_reason' => $booking['reason'] ?? $booking['rejection_reason'] ?? null,
                        ];
                    })->toArray();
                }
            }

            return response()->json([
                'success' => true,
                'external_bookings_count' => count($externalBookings),
                'external_bookings' => $externalBookings,
                'api_status' => $response->status() ?? 'N/A',
                'api_response' => $response->successful() ? 'success' : 'failed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    })->name('debug.approval.data');

    // Approval Workflow Actions
    Route::post('/approval/approve/{id}', function ($id, Request $request) {
        try {
            // Find the approval request
            $approval = \App\Models\Approval::find($id);

            if (!$approval) {
                return response()->json([
                    'success' => false,
                    'message' => 'Approval request not found'
                ], 404);
            }

            // Validate that request is still pending
            if ($approval->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request has already been ' . $approval->status
                ], 400);
            }

            // Validate date is not in the past for room/equipment bookings
            if ($approval->date && \Carbon\Carbon::parse($approval->date)->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot approve requests with past dates'
                ], 400);
            }

            // Update approval status
            $approval->status = 'approved';
            $approval->approver_id = auth()->id();
            $approval->approved_by = auth()->user()->name;
            $approval->approved_at = now();
            $approval->save();

            return response()->json([
                'success' => true,
                'message' => 'Request approved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving request: ' . $e->getMessage()
            ], 500);
        }
    })->name('approval.approve');

    Route::post('/approval/reject/{id}', function ($id, Request $request) {
        try {
            // Validate request - reason is required for rejection
            $request->validate([
                'reason' => 'required|string|max:500'
            ]);

            // Find the approval request
            $approval = \App\Models\Approval::find($id);

            if (!$approval) {
                return response()->json([
                    'success' => false,
                    'message' => 'Approval request not found'
                ], 404);
            }

            // Validate that request is still pending
            if ($approval->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request has already been ' . $approval->status
                ], 400);
            }

            // Update approval status
            $approval->status = 'rejected';
            $approval->approver_id = auth()->id();
            $approval->rejected_by = auth()->user()->name;
            $approval->rejected_at = now();
            $approval->description = $approval->description . "\n\nRejection reason: " . $request->reason;
            $approval->save();

            return response()->json([
                'success' => true,
                'message' => 'Request rejected successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting request: ' . $e->getMessage()
            ], 500);
        }
    })->name('approval.reject');

    // External Approval Workflow Actions
    Route::post('/approval/external/approve/{id}', function ($id, Request $request) {
        try {
            // First, attempt to update the status in the external API
            // Corrected endpoint: PATCH /api/training-room-bookings/{id}
            $apiUrl = 'https://hr2.microfinancial-1.com/api/training-room-bookings/' . $id;

            $apiResponse = \Illuminate\Support\Facades\Http::timeout(5)->patch($apiUrl, [
                'status' => 'approved',
                'approved_by' => auth()->user()->name,
                'approved_at' => now()->toDateTimeString()
            ]);

            \Log::info('External API Approval Response: ' . $apiResponse->status() . ' - ' . $apiResponse->body());

            if (!$apiResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'External API failed to update booking: ' . ($apiResponse->json('message') ?? 'Unknown error')
                ], $apiResponse->status());
            }

            // For external bookings, we'll store the approval status in cache

            // Log the external approval action
            \Log::info('External booking approved locally', [
                'booking_id' => $id,
                'approved_by' => auth()->user()->name,
                'approved_at' => now()->toDateTimeString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'External booking approved successfully',
                'approved_by' => auth()->user()->name,
                'approved_at' => now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving external booking: ' . $e->getMessage()
            ], 500);
        }
    })->name('approval.external.approve');

    Route::post('/approval/external/reject/{id}', function ($id, Request $request) {
        try {
            $request->validate([
                'reason' => 'required|string|max:500'
            ]);

            // First, attempt to update the status in the external API
            // Corrected endpoint: PATCH /api/training-room-bookings/{id}
            $apiUrl = 'https://hr2.microfinancial-1.com/api/training-room-bookings/' . $id;

            $apiResponse = \Illuminate\Support\Facades\Http::timeout(5)->patch($apiUrl, [
                'status' => 'rejected',
                'rejected_by' => auth()->user()->name,
                'rejected_at' => now()->toDateTimeString(),
                'reason' => $request->reason
            ]);

            \Log::info('External API Rejection Response: ' . $apiResponse->status() . ' - ' . $apiResponse->body());

            if (!$apiResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'External API failed to reject booking: ' . ($apiResponse->json('message') ?? 'Unknown error')
                ], $apiResponse->status());
            }


            // Log the external rejection action
            \Log::info('External booking rejected locally', [
                'booking_id' => $id,
                'rejected_by' => auth()->user()->name,
                'rejected_at' => now()->toDateTimeString(),
                'rejection_reason' => $request->reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'External booking rejected successfully',
                'rejected_by' => auth()->user()->name,
                'rejected_at' => now()->toDateTimeString(),
                'reason' => $request->reason
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting external booking: ' . $e->getMessage()
            ], 500);
        }
    })->name('approval.external.reject');

    Route::get('/reservation-history', function () {
        // Get existing bookings from session or use defaults
        $localBookings = session('calendar_bookings', [
            [
                'id' => 'RES-001',
                'name' => 'Conference Room A',
                'type' => 'room',
                'date' => '2025-01-25',
                'start_time' => '09:00',
                'end_time' => '11:00',
                'status' => 'approved',
                'lead_time' => '3',
                'purpose' => 'Team meeting'
            ],
            [
                'id' => 'RES-002',
                'name' => 'Projector',
                'type' => 'equipment',
                'date' => '2025-01-26',
                'start_time' => '14:00',
                'end_time' => '16:00',
                'status' => 'pending',
                'lead_time' => '2',
                'purpose' => 'Client presentation'
            ],
            [
                'id' => 'RES-003',
                'name' => 'Training Room B',
                'type' => 'room',
                'date' => '2025-01-28',
                'start_time' => '10:00',
                'end_time' => '17:00',
                'status' => 'completed',
                'lead_time' => '7',
                'purpose' => 'Employee training'
            ],
            [
                'id' => 'RES-004',
                'name' => 'Audio System',
                'type' => 'equipment',
                'date' => '2025-01-30',
                'start_time' => '13:00',
                'end_time' => '15:00',
                'status' => 'rejected',
                'lead_time' => '1',
                'purpose' => 'Company event'
            ],
            [
                'id' => 'RES-005',
                'name' => 'Meeting Room C',
                'type' => 'room',
                'date' => '2025-02-02',
                'start_time' => '15:00',
                'end_time' => '17:00',
                'status' => 'pending',
                'lead_time' => '5',
                'purpose' => 'Board meeting'
            ]
        ]);

        // Fetch training room bookings from external API
        $externalBookings = [];
        try {
            $apiUrl = 'https://hr2.microfinancial-1.com/api/training-room-bookings';

            // Add debugging
            \Log::info('Attempting to fetch external bookings from: ' . $apiUrl);

            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($apiUrl);

            \Log::info('API Response Status: ' . $response->status());
            \Log::info('API Response Body: ' . $response->body());

            if ($response->successful()) {
                $bookings = $response->json();
                \Log::info('Decoded bookings: ' . json_encode($bookings));

                if (!empty($bookings) && is_array($bookings)) {
                    $bookingsData = $bookings['data'] ?? $bookings;
                    $externalBookings = collect($bookingsData)->map(function ($booking) {
                        return [
                            'id' => $booking['id'] ?? ('EXT-' . uniqid()),
                            'booking_code' => $booking['booking_code'] ?? $booking['code'] ?? $booking['request_id'] ?? ('TRB-' . ($booking['id'] ?? '000')),
                            'location' => $booking['location'] ?? $booking['venue'] ?? $booking['room'] ?? $booking['course_name'] ?? $booking['title'] ?? $booking['name'] ?? 'Training Room',
                            'facilitator' => $booking['facilitator'] ?? $booking['instructor'] ?? $booking['trainer'] ?? $booking['requested_by'] ?? $booking['created_by'] ?? $booking['user'] ?? null,
                            'status' => $booking['status'] ?? 'pending',
                            'start_time' => $booking['start_time'] ?? $booking['time_start'] ?? $booking['begin_time'] ?? $booking['start'] ?? '09:00',
                            'end_time' => $booking['end_time'] ?? $booking['time_end'] ?? $booking['finish_time'] ?? $booking['end'] ?? '17:00',
                            'name' => $booking['title'] ?? $booking['course_name'] ?? $booking['name'] ?? 'Training Room',
                            'type' => $booking['type'] ?? 'room',
                            'date' => $booking['date'] ?? $booking['session_date'] ?? $booking['booking_date'] ?? now()->toDateString(),
                            'lead_time' => $booking['lead_time'] ?? $booking['duration'] ?? 1,
                            'purpose' => $booking['purpose'] ?? $booking['notes'] ?? $booking['description'] ?? 'External training session',
                            'requested_by' => $booking['requested_by'] ?? $booking['created_by'] ?? $booking['user'] ?? null,
                            'is_external' => true,
                            'approved_by' => $booking['approved_by'] ?? null,
                            'rejected_by' => $booking['rejected_by'] ?? null,
                            'approved_at' => $booking['approved_at'] ?? null,
                            'rejected_at' => $booking['rejected_at'] ?? null,
                            'rejection_reason' => $booking['reason'] ?? $booking['rejection_reason'] ?? null,
                        ];
                    })->toArray();

                    \Log::info('Formatted external bookings count: ' . count($externalBookings));
                } else {
                    \Log::warning('No bookings data received from external API');
                }
            } else {
                \Log::error('External API request failed with status: ' . $response->status());
            }
        } catch (\Exception $e) {
            \Log::error('Failed to fetch external bookings: ' . $e->getMessage());
            \Log::error('Exception trace: ' . $e->getTraceAsString());
            \Log::warning('External API request failed in reservation history');
        }

        // Get approval requests from database
        $approvals = \App\Models\Approval::orderByDesc('created_at')->get()->map(function ($approval) {
            return [
                'id' => $approval->request_id, // Use request_id for display
                'title' => $approval->title,
                'type' => $approval->type,
                'requested_by' => $approval->requested_by,
                'date' => $approval->date ? $approval->date->toDateString() : now()->toDateString(),
                'status' => $approval->status,
                'lead_time' => $approval->lead_time,
                'purpose' => $approval->description,
                'approved_by' => $approval->approved_by,
                'rejected_by' => $approval->rejected_by,
                'approved_at' => $approval->approved_at ? $approval->approved_at->toDateTimeString() : null,
                'rejected_at' => $approval->rejected_at ? $approval->rejected_at->toDateTimeString() : null,
                'is_approval' => true, // Flag to distinguish approval requests
            ];
        })->toArray();

        // Combine all data: local bookings + external bookings + approvals
        $allReservations = array_merge($localBookings, $externalBookings, $approvals);

        // Create approval map for decision notes lookup
        $approvalMap = collect($approvals)->keyBy('id');

        return view('dashboard.reservation-history', [
            'user' => auth()->user(),
            'bookings' => $allReservations,
            'approvalMap' => $approvalMap
        ]);
    })->name('reservation.history');

    // Legal Management Routes
    Route::get('/case-management', function () {
        // Debug: Check total cases first
        $totalCases = \App\Models\CaseFile::count();
        \Log::info('Total cases in database: ' . $totalCases);

        // Get cases from database with relationships
        $cases = \App\Models\CaseFile::with(['client', 'assignedUser'])->orderByDesc('created_at')->get()->map(function ($c) {
            return [
                'id' => $c->id,
                'number' => $c->number ?? '',
                'code' => $c->number ?? '',  // Use number as code
                'name' => $c->name ?? '',
                'title' => $c->name ?? '',  // Use name as title
                'description' => $c->contract_notes ?? '',
                'client_id' => $c->client_id ?? '',
                'client' => (is_object($c->client) && $c->client) ? $c->client->name : $c->client ?? 'N/A',
                'client_name' => (is_object($c->client) && $c->client) ? $c->client->name : $c->client ?? 'N/A',
                'client_org' => $c->client_org ?? '',
                'client_initials' => $c->client_initials ?? '',
                'case_type' => $c->type_label ?? '',
                'type_label' => $c->type_label ?? '',
                'type_badge' => $c->type_badge ?? 'civil',
                'priority' => 'medium',  // Default since priority field doesn't exist
                'status' => $c->status ?? 'open',
                'assigned_to' => $c->assigned_to ?? '',
                'assigned_name' => (is_object($c->assignedUser) && $c->assignedUser) ? $c->assignedUser->name : 'Unassigned',
                'hearing_date' => $c->hearing_date ? $c->hearing_date->toDateString() : '',
                'hearing_time' => $c->hearing_time ?? '',
                'location' => '',  // Default since location field doesn't exist
                'notes' => $c->contract_notes ?? '',
                'contract_type' => $c->contract_type ?? '',
                'contract_number' => $c->contract_number ?? '',
                'contract_date' => $c->contract_date ? $c->contract_date->toDateString() : '',
                'contract_expiration' => $c->contract_expiration ? $c->contract_expiration->toDateString() : '',
                'contract_status' => $c->contract_status ?? '',
                'filed' => $c->filed_date ? $c->filed_date->toDateString() : $c->created_at?->toDateString() ?? now()->toDateString(),
                'created_by' => $c->created_by ?? '',
                'created_at' => $c->created_at?->toDateString() ?? now()->toDateString(),
                'updated_at' => $c->updated_at?->toDateString() ?? now()->toDateString(),
            ];
        })->toArray();

        \Log::info('Mapped cases count: ' . count($cases));
        if (!empty($cases)) {
            \Log::info('First case data: ' . json_encode($cases[0]));
        }

        // Get upcoming hearings with relationships
        $upcoming = \App\Models\CaseFile::with(['client'])
            ->whereNotNull('hearing_date')
            ->where('hearing_date', '>=', now()->subDays(30)->toDateString()) // Include recent past dates for demo
            ->orderBy('hearing_date', 'asc')
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'code' => $c->number ?? '',
                    'title' => $c->name ?? '',
                    'client_name' => (is_object($c->client) && $c->client) ? $c->client->name : $c->client ?? 'N/A',
                    'hearing_date' => $c->hearing_date ? $c->hearing_date->toDateString() : '',
                    'hearing_time' => $c->hearing_time ?? '',
                    'location' => '', // Default since location field doesn't exist
                    'priority' => 'medium', // Default since priority field doesn't exist
                ];
            })->toArray();

        // Calculate statistics
        $allCases = \App\Models\CaseFile::all();
        $upcomingHearings = collect($upcoming);

        $stats = [
            'total_cases' => $allCases->count(),
            'active_cases' => $allCases->whereIn('status', ['open', 'active'])->count(),
            'pending_cases' => $allCases->whereIn('status', ['pending', 'in_progress'])->count(),
            'pending_tasks' => $allCases->where('status', 'in_progress')->count(),
            'urgent_cases' => 0, // No priority field exists
            'closed_cases' => $allCases->where('status', 'closed')->count(),
            'archived_cases' => $allCases->where('status', 'archived')->count(),
            'upcoming_hearings' => $upcomingHearings->count(),
            'next_hearing' => $upcomingHearings->sortBy('hearing_date')->first(),
        ];

        return view('dashboard.case-management', [
            'user' => auth()->user(),
            'cases' => $cases,
            'upcoming' => $upcoming,
            'stats' => $stats
        ]);
    })->name('case.management');

    Route::get('/document-case-management', function () {
        // Debug: Check total cases first
        $totalCases = \App\Models\CaseFile::count();
        \Log::info('Total cases in database: ' . $totalCases);

        // Get cases from database with relationships
        $cases = \App\Models\CaseFile::with(['client', 'assignedUser'])->orderByDesc('created_at')->get()->map(function ($c) {
            return [
                'id' => $c->id,
                'number' => $c->number ?? '',
                'code' => $c->number ?? '',  // Use number as code
                'name' => $c->name ?? '',
                'title' => $c->name ?? '',  // Use name as title
                'description' => $c->contract_notes ?? '',
                'client_id' => $c->client_id ?? '',
                'client' => (is_object($c->client) && $c->client) ? $c->client->name : $c->client ?? 'N/A',
                'client_name' => (is_object($c->client) && $c->client) ? $c->client->name : $c->client ?? 'N/A',
                'client_org' => $c->client_org ?? '',
                'client_initials' => $c->client_initials ?? '',
                'case_type' => $c->type_label ?? '',
                'type_label' => $c->type_label ?? '',
                'type_badge' => $c->type_badge ?? 'civil',
                'priority' => 'medium',  // Default since priority field doesn't exist
                'status' => $c->status ?? 'open',
                'assigned_to' => $c->assigned_to ?? '',
                'assigned_name' => (is_object($c->assignedUser) && $c->assignedUser) ? $c->assignedUser->name : 'Unassigned',
                'hearing_date' => $c->hearing_date ? $c->hearing_date->toDateString() : '',
                'hearing_time' => $c->hearing_time ?? '',
                'location' => '',  // Default since location field doesn't exist
                'notes' => $c->contract_notes ?? '',
                'contract_type' => $c->contract_type ?? '',
                'contract_number' => $c->contract_number ?? '',
                'contract_date' => $c->contract_date ? $c->contract_date->toDateString() : '',
                'contract_expiration' => $c->contract_expiration ? $c->contract_expiration->toDateString() : '',
                'contract_status' => $c->contract_status ?? '',
                'filed' => $c->filed_date ? $c->filed_date->toDateString() : $c->created_at?->toDateString() ?? now()->toDateString(),
                'created_by' => $c->created_by ?? '',
                'created_at' => $c->created_at?->toDateString() ?? now()->toDateString(),
                'updated_at' => $c->updated_at?->toDateString() ?? now()->toDateString(),
            ];
        })->toArray();

        \Log::info('Mapped cases count: ' . count($cases));
        if (!empty($cases)) {
            \Log::info('First case data: ' . json_encode($cases[0]));
        }

        // Get upcoming hearings with relationships
        $upcoming = \App\Models\CaseFile::with(['client'])
            ->whereNotNull('hearing_date')
            ->where('hearing_date', '>=', now()->subDays(30)->toDateString()) // Include recent past dates for demo
            ->orderBy('hearing_date', 'asc')
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'code' => $c->number ?? '',
                    'title' => $c->name ?? '',
                    'client_name' => (is_object($c->client) && $c->client) ? $c->client->name : $c->client ?? 'N/A',
                    'hearing_date' => $c->hearing_date ? $c->hearing_date->toDateString() : '',
                    'hearing_time' => $c->hearing_time ?? '',
                    'location' => '', // Default since location field doesn't exist
                    'priority' => 'medium', // Default since priority field doesn't exist
                ];
            })->toArray();

        // Calculate statistics
        $allCases = \App\Models\CaseFile::all();
        $upcomingHearings = collect($upcoming);

        $stats = [
            'total_cases' => $allCases->count(),
            'active_cases' => $allCases->whereIn('status', ['open', 'active'])->count(),
            'pending_cases' => $allCases->whereIn('status', ['pending', 'in_progress'])->count(),
            'pending_tasks' => $allCases->where('status', 'in_progress')->count(),
            'urgent_cases' => 0, // No priority field exists
            'closed_cases' => $allCases->where('status', 'closed')->count(),
            'archived_cases' => $allCases->where('status', 'archived')->count(),
            'upcoming_hearings' => $upcomingHearings->count(),
            'next_hearing' => $upcomingHearings->sortBy('hearing_date')->first(),
        ];

        return view('dashboard.case-management', [
            'user' => auth()->user(),
            'cases' => $cases,
            'upcoming' => $upcoming,
            'stats' => $stats
        ]);
    })->name('document.case.management');

    Route::get('/contract-management', function () {
        // Get contracts from database
        $contracts = \App\Models\Contract::with('client')->orderByDesc('created_at')->get();

        // Calculate statistics
        $allContracts = \App\Models\Contract::all();
        $now = now();

        $stats = [
            'total' => $allContracts->count(),
            'active' => $allContracts->whereIn('status', ['active', 'signed'])->count(),
            'pending' => $allContracts->whereIn('status', ['draft', 'pending', 'review'])->count(),
            'expiring' => $allContracts->where('end_date', '>', $now)
                ->where('end_date', '<=', $now->copy()->addDays(30))
                ->count(),
        ];

        return view('dashboard.contract-management', [
            'user' => auth()->user(),
            'contracts' => $contracts,
            'stats' => $stats
        ]);
    })->name('contract.management');

    Route::post('/contracts/create', function (Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:100',
            'status' => 'required|string|in:draft,active,signed,expired,terminated,renewed,pending',
            'start_date' => 'nullable|date',
            'expiration' => 'nullable|date',
            'value' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        // Generate contract code
        $contractCode = 'CT-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Create contract record
        $contract = \App\Models\Contract::create([
            'code' => $contractCode,
            'title' => $request->title,
            'client_id' => null, // Will be updated if client relationship is needed
            'type' => $request->type,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->expiration,
            'value' => $request->value,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contract created successfully!',
            'contract' => $contract
        ]);
    })->name('contracts.create');

    Route::post('/contracts/update', function (Request $request) {
        $request->validate([
            'code' => 'required|string|exists:contracts,code',
            'title' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:100',
            'status' => 'required|string|in:draft,active,signed,expired,terminated,renewed,pending',
            'expiration' => 'nullable|date',
        ]);

        $contract = \App\Models\Contract::where('code', $request->code)->first();
        if ($contract) {
            $contract->update([
                'title' => $request->title,
                'type' => $request->type,
                'status' => $request->status,
                'end_date' => $request->expiration,
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contract updated successfully!',
                'contract' => $contract
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Contract not found.'
        ], 404);
    })->name('contracts.update');

    Route::post('/contracts/delete', function (Request $request) {
        $request->validate([
            'code' => 'required|string|exists:contracts,code'
        ]);

        $contract = \App\Models\Contract::where('code', $request->code)->first();
        if ($contract) {
            $contract->delete();
            return response()->json([
                'success' => true,
                'message' => 'Contract deleted successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Contract not found.'
        ], 404);
    })->name('contracts.delete');

    // Case Management Routes
    Route::post('/case/create', function (Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'case_type' => 'required|string|max:100',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'status' => 'required|string|in:open,in_progress,closed,archived',
            'hearing_date' => 'nullable|date',
            'hearing_time' => 'nullable|string',
            'client' => 'required|string|max:255',
            'court' => 'nullable|string|max:255',
            'judge' => 'nullable|string|max:255',
            'contract_type' => 'nullable|string|in:employee,employment,service,other',
        ]);

        // Generate case number
        $caseNumber = 'C-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Create case record
        $case = \App\Models\CaseFile::create([
            'number' => $caseNumber,
            'name' => $request->title,
            'type_label' => ucfirst($request->case_type),
            'type_badge' => ucfirst($request->case_type),
            'client' => $request->client,
            'status' => $request->status,
            'hearing_date' => $request->hearing_date,
            'hearing_time' => $request->hearing_time,
            'contract_type' => $request->contract_type,
            'contract_notes' => $request->description,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Case created successfully!',
            'case' => $case
        ]);
    })->name('case.create');

    Route::post('/case/update', function (Request $request) {
        $request->validate([
            'id' => 'required|exists:case_files,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'case_type' => 'required|string|max:100',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'status' => 'required|string|in:open,in_progress,closed,archived',
            'hearing_date' => 'nullable|date',
            'hearing_time' => 'nullable|string',
            'client' => 'required|string|max:255',
            'court' => 'nullable|string|max:255',
            'judge' => 'nullable|string|max:255',
            'contract_type' => 'nullable|string|in:employee,employment,service,other',
        ]);

        $case = \App\Models\CaseFile::find($request->id);
        if ($case) {
            $case->update([
                'name' => $request->title,
                'type_label' => ucfirst($request->case_type),
                'type_badge' => ucfirst($request->case_type),
                'client' => $request->client,
                'status' => $request->status,
                'hearing_date' => $request->hearing_date,
                'hearing_time' => $request->hearing_time,
                'contract_type' => $request->contract_type,
                'contract_notes' => $request->description,
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Case updated successfully!',
                'case' => $case
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Case not found.'
        ], 404);
    })->name('case.update');

    Route::post('/case/delete', function (Request $request) {
        $request->validate([
            'number' => 'required|exists:case_files,code'
        ]);

        $case = \App\Models\CaseFile::where('code', $request->number)->first();
        if ($case) {
            $case->delete();
            return response()->json([
                'success' => true,
                'message' => 'Case deleted successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Case not found.'
        ], 404);
    })->name('case.delete');

    Route::get('/checkinout-tracking', function () {
        // Get visitors from database for check-in/out tracking
        $allVisitors = \App\Models\Visitor::orderByDesc('check_in_date')->get()->map(function ($v) {
            return [
                'id' => $v->id,
                'code' => $v->code ?? '',
                'name' => $v->name ?? '',
                'email' => $v->email ?? '',
                'phone' => $v->phone ?? '',
                'company' => $v->company ?? '',
                'visitor_type' => $v->visitor_type ?? 'Guest',
                'host' => $v->host ?? '',
                'host_department' => $v->host_department ?? '',
                'check_in_date' => $v->check_in_date ? (is_string($v->check_in_date) ? $v->check_in_date : $v->check_in_date->toDateString()) : '',
                'check_in_time' => $v->check_in_time ?? '',
                'check_out_date' => $v->check_out_date ? (is_string($v->check_out_date) ? $v->check_out_date : $v->check_out_date->toDateString()) : '',
                'check_out_time' => $v->check_out_time ?? '',
                'purpose' => $v->purpose ?? '',
                'status' => $v->status ?? 'pending',
                'notes' => $v->notes ?? '',
                'created_at' => $v->created_at ? (is_string($v->created_at) ? $v->created_at : $v->created_at->toDateString()) : now()->toDateString(),
            ];
        })->toArray();

        // Get currently checked-in visitors
        $currentCheckIns = \App\Models\Visitor::where('status', 'checked_in')->orderByDesc('check_in_date')->get()->map(function ($v) {
            return [
                'id' => $v->id,
                'code' => $v->code ?? '',
                'name' => $v->name ?? '',
                'email' => $v->email ?? '',
                'phone' => $v->phone ?? '',
                'company' => $v->company ?? '',
                'visitor_type' => $v->visitor_type ?? 'Guest',
                'host' => $v->host ?? '',
                'host_department' => $v->host_department ?? '',
                'check_in_date' => $v->check_in_date ? (is_string($v->check_in_date) ? $v->check_in_date : $v->check_in_date->toDateString()) : '',
                'check_in_time' => $v->check_in_time ?? '',
                'check_out_date' => $v->check_out_date ? (is_string($v->check_out_date) ? $v->check_out_date : $v->check_out_date->toDateString()) : '',
                'check_out_time' => $v->check_out_time ?? '',
                'purpose' => $v->purpose ?? '',
                'status' => $v->status ?? 'pending',
                'notes' => $v->notes ?? '',
                'created_at' => $v->created_at ? (is_string($v->created_at) ? $v->created_at : $v->created_at->toDateString()) : now()->toDateString(),
            ];
        })->toArray();

        // Calculate check-in/out statistics
        $allVisitorsCount = \App\Models\Visitor::all();
        $now = now();
        $todayVisitors = \App\Models\Visitor::whereDate('check_in_date', $now->toDateString())->count();
        $checkedInVisitors = \App\Models\Visitor::where('status', 'checked_in')->count();
        $checkedOutVisitors = \App\Models\Visitor::where('status', 'checked_out')->count();
        $pendingVisitors = \App\Models\Visitor::where('status', 'pending')->count();
        $overdueVisitors = \App\Models\Visitor::where('status', 'expired')->count();

        // Calculate duration for checked-in visitors
        $avgDuration = 0;
        $checkedInWithTime = $allVisitorsCount->where('status', 'checked_out')
            ->filter(function ($v) {
                return $v->check_in_time && $v->check_out_time;
            });

        if ($checkedInWithTime->count() > 0) {
            $totalMinutes = $checkedInWithTime->sum(function ($v) {
                $checkIn = strtotime($v->check_in_date . ' ' . $v->check_in_time);
                $checkOut = strtotime($v->check_out_date . ' ' . $v->check_out_time);
                return ($checkOut - $checkIn) / 60; // Convert to minutes
            });
            $avgDuration = round($totalMinutes / $checkedInWithTime->count());
        }

        $stats = [
            'currently_checked_in' => $checkedInVisitors,
            'todays_checkins' => $todayVisitors,
            'total_visitors' => $allVisitorsCount->count(),
            'checked_in' => $checkedInVisitors,
            'checked_out' => $checkedOutVisitors,
            'pending' => $pendingVisitors,
            'overdue' => $overdueVisitors,
            'avg_duration_minutes' => $avgDuration,
            'peak_hour' => '10:00', // Could be calculated from actual data
        ];

        return view('dashboard.check-in-out-tracking', [
            'user' => auth()->user(),
            'allVisitors' => $allVisitors,
            'currentCheckIns' => $currentCheckIns,
            'stats' => $stats
        ]);
    })->name('checkinout.tracking');

    Route::get('/compliance-tracking', function () {
        $complianceItems = \App\Models\ComplianceTracking::latest('due_date')->get();

        $stats = [
            'active' => $complianceItems->where('status', 'active')->count(),
            'pending' => $complianceItems->where('status', 'pending')->count(),
        ];

        return view('dashboard.compliance-tracking', [
            'user' => auth()->user(),
            'complianceItems' => $complianceItems,
            'stats' => $stats
        ]);
    })->name('compliance.tracking');

    Route::post('/compliance/create', function (Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|max:100',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'status' => 'required|string|in:pending,in_progress,completed,overdue',
            'due_date' => 'required|date|after:today',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        // Generate compliance task code
        $taskCode = 'CP-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Create compliance task record
        $compliance = \App\Models\Compliance::create([
            'code' => $taskCode,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'priority' => $request->priority,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'assigned_to' => $request->assigned_to,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Compliance task created successfully!',
            'compliance' => $compliance
        ]);
    })->name('compliance.create');

    Route::post('/compliance/update', function (Request $request) {
        $request->validate([
            'id' => 'required|exists:compliances,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|max:100',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'status' => 'required|string|in:pending,in_progress,completed,overdue',
            'due_date' => 'required|date|after:today',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $compliance = \App\Models\Compliance::find($request->id);
        if ($compliance) {
            $compliance->update([
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type,
                'priority' => $request->priority,
                'status' => $request->status,
                'due_date' => $request->due_date,
                'assigned_to' => $request->assigned_to,
                'notes' => $request->notes,
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Compliance task updated successfully!',
                'compliance' => $compliance
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Compliance task not found.'
        ], 404);
    })->name('compliance.update');

    Route::post('/compliance/delete', function (Request $request) {
        $request->validate([
            'id' => 'required|exists:compliances,id'
        ]);

        $compliance = \App\Models\Compliance::find($request->id);
        if ($compliance) {
            $compliance->delete();
            return response()->json([
                'success' => true,
                'message' => 'Compliance task deleted successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Compliance task not found.'
        ], 404);
    })->name('compliance.delete');

    Route::get('/document-compliance-tracking', function () {
        $complianceItems = \App\Models\ComplianceTracking::latest('due_date')->get();

        $stats = [
            'active' => $complianceItems->where('status', 'active')->count(),
            'pending' => $complianceItems->where('status', 'pending')->count(),
        ];

        return view('dashboard.compliance-tracking', [
            'user' => auth()->user(),
            'complianceItems' => $complianceItems,
            'stats' => $stats
        ]);
    })->name('document.compliance.tracking');

    Route::get('/deadline-hearing-alerts', function () {
        // Get hearings from database
        $hearings = \App\Models\Hearing::orderBy('hearing_date', 'asc')->get()->map(function ($h) {
            return [
                'id' => $h->id,
                'title' => $h->title ?? '',
                'description' => $h->description ?? '',
                'number' => $h->case_number ?? '', // Added for view compatibility
                'case_number' => $h->case_number ?? '',
                'date' => $h->hearing_date ? $h->hearing_date->toDateString() : '', // Added for view compatibility
                'due_date' => $h->hearing_date ? $h->hearing_date->toDateString() : '', // Mapped for alerts view
                'time' => $h->hearing_time ?? '', // Added for view compatibility
                'hearing_date' => $h->hearing_date ? $h->hearing_date->toDateString() : '',
                'hearing_time' => $h->hearing_time ?? '',
                'court_location' => $h->court_location ?? '',
                'priority' => $h->priority ?? 'medium',
                'judge' => $h->judge ?? '',
                'type' => $h->type ?? 'Hearing',
                'status' => $h->status ?? 'scheduled',
                'responsible_lawyer' => $h->responsible_lawyer ?? '',
                'client_name' => $h->client_name ?? '',
                'case_type' => $h->case_type ?? '',
                'reminder_sent' => $h->reminder_sent ?? false,
                'created_at' => $h->created_at?->toDateString() ?? now()->toDateString(),
            ];
        })->toArray();

        // Calculate statistics
        $now = now();
        $upcomingCount = \App\Models\Hearing::where('hearing_date', '>=', $now->toDateString())->count();
        $todayCount = \App\Models\Hearing::where('hearing_date', '=', $now->toDateString())->count();
        $overdueCount = \App\Models\Hearing::where('hearing_date', '<', $now->toDateString())->count();

        $counts = [
            'upcoming' => $upcomingCount,
            'today' => $todayCount,
            'overdue' => $overdueCount,
            'total' => count($hearings),
        ];

        return view('dashboard.deadline-hearing-alerts', [
            'user' => auth()->user(),
            'alerts' => $hearings,
            'counts' => $counts
        ]);
    })->name('deadline.hearing.alerts');

    Route::post('/hearings/create', function (Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'case_number' => 'nullable|string|max:255',
            'hearing_date' => 'required|date|after_or_equal:today',
            'hearing_time' => 'required|string',
            'court_location' => 'nullable|string|max:255',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'judge' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
            'responsible_lawyer' => 'nullable|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'case_type' => 'nullable|string|max:100',
        ]);

        // Create hearing record
        $hearing = \App\Models\Hearing::create([
            'title' => $request->title,
            'description' => $request->description,
            'case_number' => $request->case_number,
            'hearing_date' => $request->hearing_date,
            'hearing_time' => $request->hearing_time,
            'court_location' => $request->court_location,
            'priority' => $request->priority,
            'judge' => $request->judge,
            'type' => $request->type,
            'status' => $request->status ?? 'scheduled',
            'responsible_lawyer' => $request->responsible_lawyer,
            'client_name' => $request->client_name,
            'case_type' => $request->case_type,
            'reminder_sent' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hearing created successfully!',
            'hearing' => $hearing
        ]);
    })->name('hearings.create');

    Route::post('/visitor/update', function (Request $request) {
        $request->validate([
            'id' => 'required|exists:visitors,id',
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'visitor_type' => 'nullable|string|max:100',
            'host' => 'nullable|string|max:255',
            'host_department' => 'nullable|string|max:255',
            'purpose' => 'sometimes|string|max:500',
            'visit_date' => 'sometimes|date',
            'check_in_date' => 'sometimes|date', // Accept either
            'check_in_time' => 'nullable|string',
            'check_out_time' => 'nullable|string',
            'status' => 'sometimes|string|in:pending,checked_in,checked_out,expired,scheduled',
            'notes' => 'nullable|string',
        ]);

        $visitor = \App\Models\Visitor::find($request->id);
        if ($visitor) {
            $data = [];
            if ($request->has('name'))
                $data['name'] = $request->name;
            if ($request->has('email'))
                $data['email'] = $request->email;
            if ($request->has('phone'))
                $data['phone'] = $request->phone;
            if ($request->has('company'))
                $data['company'] = $request->company;
            if ($request->has('visitor_type'))
                $data['visitor_type'] = $request->visitor_type;
            if ($request->has('host'))
                $data['host'] = $request->host;
            if ($request->has('host_department'))
                $data['host_department'] = $request->host_department;
            if ($request->has('purpose'))
                $data['purpose'] = $request->purpose;

            // Handle date mapping
            if ($request->has('visit_date')) {
                $data['check_in_date'] = $request->visit_date;
                $data['check_out_date'] = $request->visit_date; // Assume same day visit
            } elseif ($request->has('check_in_date')) {
                $data['check_in_date'] = $request->check_in_date;
                if (!$visitor->check_out_date) {
                    $data['check_out_date'] = $request->check_in_date;
                }
            }

            if ($request->has('check_in_time'))
                $data['check_in_time'] = $request->check_in_time;
            if ($request->has('check_out_time'))
                $data['check_out_time'] = $request->check_out_time;
            if ($request->has('status'))
                $data['status'] = $request->status;
            if ($request->has('notes'))
                $data['notes'] = $request->notes;

            $data['updated_by'] = auth()->id();
            $data['updated_at'] = now();

            $visitor->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Visitor updated successfully!',
                'visitor' => $visitor
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Visitor not found.'
        ], 404);
    })->name('visitor.update');

    Route::get('/visitor/get', function (Request $request) {
        $request->validate([
            'id' => 'required|exists:visitors,id'
        ]);

        $visitor = \App\Models\Visitor::find($request->id);
        if ($visitor) {
            return response()->json([
                'success' => true,
                'visitor' => $visitor
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Visitor not found.'
        ], 404);
    })->name('visitor.get');

    Route::post('/visitor/delete', function (Request $request) {
        $request->validate([
            'id' => 'required|exists:visitors,id'
        ]);

        $visitor = \App\Models\Visitor::find($request->id);
        if ($visitor) {
            $visitor->delete();
            return response()->json([
                'success' => true,
                'message' => 'Visitor deleted successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Visitor not found.'
        ], 404);
    })->name('visitor.delete');

    Route::post('/visitor/create', function (Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'visitor_type' => 'nullable|string|max:100',
            'host' => 'nullable|string|max:255',
            'host_department' => 'nullable|string|max:255',
            'purpose' => 'required|string|max:500',
            'visit_date' => 'required|date',
            'check_in_time' => 'nullable|string',
            'check_out_time' => 'nullable|string',
            'status' => 'required|string|in:pending,checked_in,checked_out,expired,scheduled',
            'notes' => 'nullable|string',
        ]);

        // Generate visitor code
        $visitorCode = 'V-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Create visitor record
        $visitor = \App\Models\Visitor::create([
            'code' => $visitorCode,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'visitor_type' => $request->visitor_type,
            'host' => $request->host,
            'host_department' => $request->host_department,
            'purpose' => $request->purpose,
            'check_in_date' => $request->visit_date,
            'check_in_time' => $request->check_in_time,
            'check_out_date' => $request->visit_date,
            'check_out_time' => $request->check_out_time,
            'status' => $request->status,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Visitor created successfully!',
            'visitor' => $visitor
        ]);
    })->name('visitor.create');

    Route::post('/account/password/change-request', function (Request $request) {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Generate and send verification code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put('password_change_' . $user->id, $code, now()->addMinutes(10));

        // Send code to user's email
        Mail::to($user->email)->send(new TwoFactorCodeMail($user->name, $code));

        return back()->with('success', 'Verification code sent to your email.');
    })->name('account.password.change.request');

    Route::post('/account/password/change-verify', function (Request $request) {
        $request->validate([
            'code' => 'required|string|size:6',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();
        $cachedCode = Cache::get('password_change_' . $user->id);

        if (!$cachedCode || $cachedCode !== $request->code) {
            return back()->withErrors(['code' => 'Invalid or expired verification code.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Clear the verification code
        Cache::forget('password_change_' . $user->id);

        return back()->with('success', 'Password changed successfully.');
    })->name('account.password.change.verify');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/account/update', [ProfileController::class, 'update'])->name('account.update');
    Route::patch('/privacy/update', [ProfileController::class, 'updatePrivacy'])->name('privacy.update');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/security', [ProfileController::class, 'updateSecurity'])->name('profile.security');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});