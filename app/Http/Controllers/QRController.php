<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Visitor;
use Illuminate\Support\Str;
use Carbon\Carbon;

class QRController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $visitors = Visitor::all();
        $checkedInCount = $visitors->where('status', 'checked_in')->count();
        $totalToday = $visitors->where('check_in_date', now()->toDateString())->count();

        $recentActivities = $visitors->where('created_at', '>=', now()->subDays(1))
            ->sortByDesc('created_at')
            ->take(5);

        return view('dashboard.qr.index', [
            'user' => $user,
            'checkedInCount' => $checkedInCount,
            'totalToday' => $totalToday,
            'recentActivities' => $recentActivities
        ]);
    }

    public function registration()
    {
        $user = auth()->user();
        return view('dashboard.qr.registration', [
            'user' => $user
        ]);
    }

    public function scanner()
    {
        $user = auth()->user();
        return view('dashboard.qr.scanner', [
            'user' => $user
        ]);
    }

    public function storeRegistration(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'access_level' => 'required|string',
            'visit_date' => 'required|date',
            'purpose' => 'nullable|string',
        ]);

        $visitor = Visitor::create([
            'code' => 'VIS-' . strtoupper(Str::random(10)),
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'company' => $validated['company'] ?? 'N/A',
            'visitor_type' => $validated['access_level'],
            'check_in_date' => $validated['visit_date'],
            'check_in_time' => now()->format('H:i:s'),
            'purpose' => $validated['purpose'] ?? 'N/A',
            'status' => 'pending',
            'host' => 'N/A', // Default or from user input if added
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Visitor registered successfully',
            'visitor' => $visitor
        ]);
    }

    public function verifyQR(Request $request)
    {
        $id = $request->input('id');

        $visitor = Visitor::where('code', $id)->first();

        if (!$visitor) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR Code'
            ]);
        }

        if ($visitor->status === 'checked_in') {
            return response()->json([
                'success' => false,
                'message' => 'Visitor already checked in'
            ]);
        }

        $visitor->update(['status' => 'checked_in']);

        return response()->json([
            'success' => true,
            'message' => 'Access Granted',
            'visitor' => $visitor
        ]);
    }

    public function getRecentRegistrations()
    {
        $visitors = Visitor::orderBy('created_at', 'desc')->take(10)->get();
        return response()->json($visitors);
    }
}
