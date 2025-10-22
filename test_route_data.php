<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing case management route data\n";
echo "==================================\n";

// Simulate the route logic
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
    return in_array($s, ['active','in progress', 'urgent']);
})->count();

$pendingTasks = collect($cases)->filter(function ($c) {
    $s = strtolower($c['status'] ?? '');
    return in_array($s, ['pending']);
})->count();

$closedCount = collect($cases)->filter(function ($c) {
    $s = strtolower($c['status'] ?? '');
    return in_array($s, ['closed', 'completed']);
})->count();

$urgentCount = collect($cases)->filter(function ($c) {
    $s = strtolower($c['status'] ?? '');
    return $s === 'urgent';
})->count();

$today = \Carbon\Carbon::today();
$upcoming = collect($cases)->filter(function ($c) use ($today) {
    if (empty($c['hearing_date'])) return false;
    try { 
        $d = \Carbon\Carbon::parse($c['hearing_date']); 
        return $d->gte($today);
    } catch (\Exception $e) { 
        return false; 
    }
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

$successRate = $total > 0 ? round(($closedCount / $total) * 100) : 0;
$pendingProgress = $total > 0 ? round(($pendingTasks / $total) * 100) : 0;
$activeProgress = $total > 0 ? round(($activeCount / $total) * 100) : 0;

$stats = [
    'active_cases' => $activeCount,
    'upcoming_hearings' => $upcomingCount,
    'pending_tasks' => $pendingTasks,
    'closed_cases' => $closedCount,
    'urgent_cases' => $urgentCount,
    'total_cases' => $total,
    'success_rate' => $successRate,
    'pending_progress' => $pendingProgress,
    'active_progress' => $activeProgress,
    'success_progress' => $successRate,
    'next_hearing' => $nextHearing,
];

echo "Stats calculated:\n";
echo "Active cases: " . $stats['active_cases'] . "\n";
echo "Upcoming hearings: " . $stats['upcoming_hearings'] . "\n";
echo "Pending tasks: " . $stats['pending_tasks'] . "\n";
echo "Urgent cases: " . $stats['urgent_cases'] . "\n";
echo "Total cases: " . $stats['total_cases'] . "\n";

if ($nextHearing) {
    echo "Next hearing: " . $nextHearing['name'] . " on " . $nextHearing['date'] . "\n";
} else {
    echo "No next hearing found\n";
}

echo "\nUpcoming hearings list:\n";
foreach ($upcoming as $u) {
    echo "- " . $u['name'] . " (" . $u['number'] . ") - " . $u['date'] . " " . $u['time'] . "\n";
}

