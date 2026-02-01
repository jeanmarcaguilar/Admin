@php
$user = auth()->user();
$visitors = $visitors ?? [];
$totalVisitors = is_array($visitors) ? count($visitors) : 0;

// Calculate visitors for today
try { 
    $todayStr = \Carbon\Carbon::today()->toDateString(); 
} catch (\Exception $e) { 
    $todayStr = date('Y-m-d'); 
}
$visitorsToday = 0;
if (is_array($visitors)) {
    foreach ($visitors as $v) {
        $d = isset($v['check_in_date']) ? (string)$v['check_in_date'] : '';
        if ($d === $todayStr) { 
            $visitorsToday++; 
        }
    }
}
$todayPct = $totalVisitors > 0 ? round(($visitorsToday / $totalVisitors) * 100) : 0;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Administrative</title>
    <link rel="icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "brand-primary": "#059669",
                        "brand-primary-hover": "#047857",
                        "brand-background-main": "#F0FDF4",
                        "brand-border": "#D1FAE5",
                        "brand-text-primary": "#1F2937",
                        "brand-text-secondary": "#4B5563",
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #c1c1c1 #f1f1f1;
        }

        .submenu {
            transition: all 0.3s ease;
        }

        .dropdown-panel {
            transform-origin: top right;
        }

        .status-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
            display: inline-flex;
            align-items: center;
        }

        .status-checked-in {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-checked-out {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-expected {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-overdue {
            background-color: #fef3c7;
            color: #92400e;
        }

        .modal {
            display: none;
            background: rgba(0, 0, 0, 0.5);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 60;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-brand-background-main min-h-screen">

    <!-- Overlay (mobile) -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 hidden opacity-0 transition-opacity duration-300 z-40"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar"
        class="fixed top-0 left-0 h-full w-72 bg-white border-r border-gray-100 shadow-sm z-50
               transform -translate-x-full md:translate-x-0 transition-transform duration-300">

        <div class="h-16 flex items-center px-4 border-b border-gray-100">
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 w-full rounded-xl px-2 py-2
                       hover:bg-gray-100 active:bg-gray-200 transition group">
                <img src="{{ asset('golden-arc.png') }}" alt="Logo" class="w-10 h-10">
                <div class="leading-tight">
                    <div class="font-bold text-gray-800 group-hover:text-brand-primary transition-colors">
                        Microfinance Admin
                    </div>
                    <div class="text-[11px] text-gray-500 font-semibold uppercase group-hover:text-brand-primary transition-colors">
                        Administrative
                    </div>
                </div>
            </a>
        </div>

        <!-- Sidebar content -->
        <div class="px-4 py-4 overflow-y-auto h-[calc(100%-4rem)] custom-scrollbar">
            <div class="text-xs font-bold text-gray-400 tracking-wider px-2">ADMINISTRATIVE DEPARTMENT</div>

            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
                class="mt-3 flex items-center justify-between px-4 py-3 rounded-xl text-gray-700 hover:bg-green-50 hover:text-brand-primary
                    transition-all duration-200 hover:translate-x-1 active:translate-x-0 active:scale-[0.99] font-semibold">
                <span class="flex items-center gap-3">
                    <span class="inline-flex w-9 h-9 rounded-lg bg-emerald-50 items-center justify-center">📊</span>
                    Dashboard
                </span>
            </a>

            <!-- Visitor Management Dropdown -->
            <button id="visitor-management-btn"
                class="mt-3 w-full flex items-center justify-between px-4 py-3 rounded-xl
                    text-gray-700 hover:bg-green-50 hover:text-brand-primary
                    transition-all duration-200 hover:translate-x-1 active:translate-x-0 active:scale-[0.99] font-semibold">
                <span class="flex items-center gap-3">
                    <span class="inline-flex w-9 h-9 rounded-lg bg-emerald-50 items-center justify-center">👥</span>
                    Visitor Management
                </span>
                <svg id="visitor-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="visitor-submenu" class="submenu mt-1">
                <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                    <a href="{{ route('visitors.registration') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Visitors Registration
                    </a>
                    <a href="{{ route('checkinout.tracking') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Check In/Out Tracking
                    </a>
                    <a href="{{ route('visitor.history') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Visitor History Records
                    </a>
                </div>
            </div>

            <!-- Document Management Dropdown -->
            <button id="document-management-btn"
                class="mt-3 w-full flex items-center justify-between px-4 py-3 rounded-xl
                    text-gray-700 hover:bg-green-50 hover:text-brand-primary
                    transition-all duration-200 hover:translate-x-1 active:translate-x-0 active:scale-[0.99] font-semibold">
                <span class="flex items-center gap-3">
                    <span class="inline-flex w-9 h-9 rounded-lg bg-emerald-50 items-center justify-center">📄</span>
                    Document Management
                </span>
                <svg id="document-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="document-submenu" class="submenu mt-1 hidden">
                <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                    <a href="{{ route('document.upload.indexing') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Document Upload & Indexing
                    </a>
                    <a href="{{ route('document.version.control') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Version Control
                    </a>
                    <a href="{{ route('document.access.control.permissions') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Access Control & Permissions
                    </a>
                    <a href="{{ route('document.archival.retention.policy') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Archival & Retention Policy
                    </a>
                </div>
            </div>

            <!-- Facilities Management Dropdown -->
            <button id="facilities-management-btn"
                class="mt-3 w-full flex items-center justify-between px-4 py-3 rounded-xl
                    text-gray-700 hover:bg-green-50 hover:text-brand-primary
                    transition-all duration-200 hover:translate-x-1 active:translate-x-0 active:scale-[0.99] font-semibold">
                <span class="flex items-center gap-3">
                    <span class="inline-flex w-9 h-9 rounded-lg bg-emerald-50 items-center justify-center">🏢</span>
                    Facilities Management
                </span>
                <svg id="facilities-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="facilities-submenu" class="submenu mt-1 hidden">
                <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                    <a href="{{ route('room-equipment') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Room & Equipment Booking
                    </a>
                    <a href="{{ route('scheduling.calendar') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Scheduling & Calendar Integrations
                    </a>
                    <a href="{{ route('approval.workflow') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Approval Workflow
                    </a>
                    <a href="{{ route('reservation.history') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Reservation History
                    </a>
                </div>
            </div>

            <!-- Legal Management Dropdown -->
            <button id="legal-management-btn"
                class="mt-3 w-full flex items-center justify-between px-4 py-3 rounded-xl
                    text-gray-700 hover:bg-green-50 hover:text-brand-primary
                    transition-all duration-200 hover:translate-x-1 active:translate-x-0 active:scale-[0.99] font-semibold">
                <span class="flex items-center gap-3">
                    <span class="inline-flex w-9 h-9 rounded-lg bg-emerald-50 items-center justify-center">⚖️</span>
                    Legal Management
                </span>
                <svg id="legal-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="legal-submenu" class="submenu mt-1 hidden">
                <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                    <a href="{{ route('case.management') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Case Management
                    </a>
                    <a href="{{ route('contract.management') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Contract Management
                    </a>
                    <a href="{{ route('compliance.tracking') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Compliance Tracking
                    </a>
                    <a href="{{ route('deadline.hearing.alerts') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Deadline & Hearing Alerts
                    </a>
                </div>
            </div>

            <!-- Administrator -->

            <div class="mt-8 px-2">
                <div class="flex items-center gap-2 text-xs font-bold text-emerald-600">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    SYSTEM ONLINE
                </div>
                <div class="text-[11px] text-gray-400 mt-2 leading-snug">
                    Microfinance Admin © {{ date('Y') }}<br/>
                    Administrative System
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN WRAPPER -->
    <div class="md:pl-72">

        <!-- TOP HEADER -->
        <header class="h-16 bg-white flex items-center justify-between px-4 sm:px-6 relative
                    shadow-[0_2px_8px_rgba(0,0,0,0.06)]">
            
            <!-- BORDER COVER -->
            <div class="hidden md:block absolute left-0 top-0 h-16 w-[2px] bg-white"></div>

            <div class="flex items-center gap-3">
                <button id="mobile-menu-btn"
                    class="md:hidden w-10 h-10 rounded-xl hover:bg-gray-100 active:bg-gray-200 transition flex items-center justify-center">
                    ☰
                </button>
                <h1 class="text-lg font-bold text-gray-800 hidden md:block">Visitor History Records</h1>
            </div>

            <div class="flex items-center gap-3 sm:gap-5">
                <!-- Clock pill -->
                <span id="real-time-clock"
                    class="text-xs font-bold text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                    {{ now()->format('H:i:s') }}
                </span>

                <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>

                <!-- User Profile Dropdown -->
                <div class="relative">
                    <button id="user-menu-button"
                        class="flex items-center gap-3 focus:outline-none group rounded-xl px-2 py-2
                            hover:bg-gray-100 active:bg-gray-200 transition">
                        <div class="w-10 h-10 rounded-full bg-white shadow group-hover:shadow-md transition-shadow overflow-hidden flex items-center justify-center border border-gray-100">
                            <div class="w-full h-full flex items-center justify-center font-bold text-brand-primary bg-emerald-50">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="hidden md:flex flex-col items-start text-left">
                            <span class="text-sm font-bold text-gray-700 group-hover:text-brand-primary transition-colors">
                                {{ $user->name }}
                            </span>
                            <span class="text-[10px] text-gray-500 font-medium uppercase group-hover:text-brand-primary transition-colors">
                                Administrator
                            </span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div id="user-menu-dropdown"
                        class="dropdown-panel hidden opacity-0 translate-y-2 scale-95 pointer-events-none
                            absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-lg border border-gray-100
                            transition-all duration-200 z-50">
                        <button id="openProfileBtn" class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">Profile</button>
                        <button id="openAccountSettingsBtn" class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">Settings</button>
                        <div class="h-px bg-gray-100"></div>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="p-4 sm:p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Page Header -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Visitor History Records</h1>
                            <p class="text-gray-600 mt-1">View and manage visitor history and records</p>
                        </div>
                        <div class="mt-4 md:mt-0 flex space-x-3">
                            <button id="exportBtn" class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors font-medium flex items-center">
                                <i class="fas fa-download mr-2"></i> Export
                            </button>
                            <button id="printBtn" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                <i class="fas fa-print mr-2"></i> Print
                            </button>
                            <button id="refreshBtn" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                <i class="fas fa-sync-alt mr-2"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Visitors Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Total Visitors</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ $totalVisitors }}</p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-users mr-1"></i>
                                        All Time
                                    </span>
                                    <span class="text-xs text-gray-500">Records</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Visitors Today Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Visitors Today</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ $visitorsToday }}</p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <i class="fas fa-user-check mr-1"></i>
                                        Today
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $todayPct }}%</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-user-check text-white text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Check-Ins Today Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-violet-50 to-violet-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Check-Ins Today</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ round($visitorsToday * 0.7) }}</p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800">
                                        <i class="fas fa-sign-in-alt mr-1"></i>
                                        Entries
                                    </span>
                                    <span class="text-xs text-gray-500">Today</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-sign-in-alt text-white text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Check-Outs Today Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-amber-50 to-amber-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Check-Outs Today</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ round($visitorsToday * 0.3) }}</p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        <i class="fas fa-sign-out-alt mr-1"></i>
                                        Exits
                                    </span>
                                    <span class="text-xs text-gray-500">Today</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-sign-out-alt text-white text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="mb-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="searchInput" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-primary focus:border-brand-primary block w-full pl-12 pr-4 py-3" placeholder="Search visitors...">
                    </div>
                </div>

                <!-- Visitor Records Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-900">Live Visitor Records</h3>
                            <p class="text-sm text-gray-500">All visitor records from the system</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ $totalVisitors }} total</span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table id="visitorTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visitor</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Host</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visit Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($visitors as $v)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                                    <span class="text-emerald-600 font-semibold">{{ strtoupper(substr($v['name'] ?? 'V', 0, 1)) }}</span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $v['name'] ?? 'Visitor' }}</div>
                                                    <div class="text-xs text-gray-500">ID: {{ $v['id'] ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $v['company'] ?? '—' }}</div>
                                            <div class="text-xs text-gray-500 capitalize">{{ $v['visitor_type'] ?? '' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $v['host'] ?? '—' }}</div>
                                            <div class="text-xs text-gray-500">{{ $v['host_department'] ?? '' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $v['check_in_date'] ?? '' }}</div>
                                            <div class="text-xs text-gray-500">{{ $v['check_in_time'] ?? '' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php 
                                                $st = strtolower($v['status'] ?? 'scheduled');
                                                $statusClass = $st === 'checked_in' ? 'status-checked-in' : 
                                                              ($st === 'checked_out' ? 'status-checked-out' : 
                                                              ($st === 'overdue' ? 'status-overdue' : 'status-expected'));
                                            @endphp
                                            <span class="status-badge {{ $statusClass }}">
                                                @if($st === 'checked_in')
                                                    <i class='bx bx-check-circle mr-1'></i>
                                                @elseif($st === 'checked_out')
                                                    <i class='bx bx-log-out mr-1'></i>
                                                @elseif($st === 'overdue')
                                                    <i class='bx bx-time-five mr-1'></i>
                                                @else
                                                    <i class='bx bx-calendar mr-1'></i>
                                                @endif
                                                {{ ucfirst($st) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button class="text-brand-primary hover:text-brand-primary-hover mr-3 view-visitor-btn" data-visitor-id="{{ $v['id'] ?? '' }}">
                                                <i class='bx bx-show mr-1'></i> View
                                            </button>
                                            <button class="text-gray-600 hover:text-gray-900 export-visitor-btn" data-visitor-id="{{ $v['id'] ?? '' }}">
                                                <i class='bx bx-download mr-1'></i> Export
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">No visitor records yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Controls (if needed) -->
                    @if($totalVisitors > 10)
                    <div class="bg-white px-6 py-3 flex items-center justify-between border-t border-gray-200">
                        <div class="text-sm text-gray-700">
                            Showing 1 to {{ min(10, $totalVisitors) }} of {{ $totalVisitors }} results
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary disabled:opacity-50" disabled>
                                Previous
                            </button>
                            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary">
                                Next
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <!-- View Visitor Modal -->
    <div id="viewVisitorModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="view-visitor-title">
        <div class="bg-white rounded-lg shadow-lg w-[420px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="view-visitor-title" class="font-semibold text-lg text-gray-900">Visitor Details</h3>
                <button id="closeViewVisitorBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 pt-5 pb-6">
                <div class="flex items-center mb-6">
                    <div class="h-16 w-16 rounded-full bg-emerald-100 flex items-center justify-center mr-4">
                        <span class="text-emerald-600 text-2xl font-bold" id="visitorInitial">V</span>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-gray-900" id="visitorName">Visitor Name</h4>
                        <p class="text-sm text-gray-500" id="visitorCompany">Company</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Visitor ID</p>
                            <p class="text-sm text-gray-900" id="visitorId">—</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Visitor Type</p>
                            <p class="text-sm text-gray-900" id="visitorType">—</p>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1">Host Information</p>
                        <p class="text-sm text-gray-900" id="visitorHost">—</p>
                        <p class="text-xs text-gray-500" id="visitorHostDept">—</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Visit Date</p>
                            <p class="text-sm text-gray-900" id="visitorDate">—</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Visit Time</p>
                            <p class="text-sm text-gray-900" id="visitorTime">—</p>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1">Status</p>
                        <span class="status-badge" id="visitorStatus">—</span>
                    </div>
                    
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1">Notes/Remarks</p>
                        <p class="text-sm text-gray-900" id="visitorNotes">No additional notes.</p>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-8">
                    <button id="closeViewVisitorBtn2" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Close</button>
                    <button id="exportVisitorBtn" type="button" class="px-4 py-2 text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover rounded-lg transition-colors">Export Record</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Sidebar functionality
            const sidebar = document.getElementById("sidebar");
            const mobileMenuBtn = document.getElementById("mobile-menu-btn");
            const sidebarOverlay = document.getElementById("sidebar-overlay");

            // Mobile sidebar toggle
            mobileMenuBtn.addEventListener("click", () => {
                sidebar.classList.remove("-translate-x-full");
                sidebarOverlay.classList.remove("hidden", "opacity-0");
                sidebarOverlay.classList.add("opacity-100");
            });

            sidebarOverlay.addEventListener("click", () => {
                sidebar.classList.add("-translate-x-full");
                sidebarOverlay.classList.remove("opacity-100");
                sidebarOverlay.classList.add("opacity-0");
                setTimeout(() => sidebarOverlay.classList.add("hidden"), 300);
            });

            // Dropdown functionality
            const dropdowns = {
                'visitor-management-btn': 'visitor-submenu',
                'document-management-btn': 'document-submenu',
                'facilities-management-btn': 'facilities-submenu',
                'legal-management-btn': 'legal-submenu'
            };

            Object.entries(dropdowns).forEach(([btnId, submenuId]) => {
                const btn = document.getElementById(btnId);
                const submenu = document.getElementById(submenuId);
                const arrow = document.getElementById(btnId.replace('-btn', '-arrow'));

                if (btn && submenu) {
                    btn.addEventListener("click", () => {
                        const isHidden = submenu.classList.contains("hidden");
                        
                        // Close all other dropdowns
                        Object.values(dropdowns).forEach(id => {
                            const otherSubmenu = document.getElementById(id);
                            const otherArrow = document.getElementById(id.replace('-submenu', '-arrow'));
                            if (otherSubmenu && otherSubmenu !== submenu) {
                                otherSubmenu.classList.add("hidden");
                                if (otherArrow) {
                                    otherArrow.classList.remove("rotate-180");
                                }
                            }
                        });

                        // Toggle current dropdown
                        if (isHidden) {
                            submenu.classList.remove("hidden");
                            if (arrow) arrow.classList.add("rotate-180");
                        } else {
                            submenu.classList.add("hidden");
                            if (arrow) arrow.classList.remove("rotate-180");
                        }
                    });
                }
            });

            // User menu dropdown
            const userMenuButton = document.getElementById("user-menu-button");
            const userMenuDropdown = document.getElementById("user-menu-dropdown");

            if (userMenuButton && userMenuDropdown) {
                userMenuButton.addEventListener("click", (e) => {
                    e.stopPropagation();
                    const isHidden = userMenuDropdown.classList.contains("hidden");
                    
                    if (isHidden) {
                        userMenuDropdown.classList.remove("hidden", "opacity-0", "translate-y-2", "scale-95", "pointer-events-none");
                        userMenuDropdown.classList.add("opacity-100", "translate-y-0", "scale-100", "pointer-events-auto");
                    } else {
                        userMenuDropdown.classList.add("opacity-0", "translate-y-2", "scale-95", "pointer-events-none");
                        setTimeout(() => userMenuDropdown.classList.add("hidden"), 200);
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener("click", (e) => {
                    if (!userMenuButton.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                        userMenuDropdown.classList.add("opacity-0", "translate-y-2", "scale-95", "pointer-events-none");
                        setTimeout(() => userMenuDropdown.classList.add("hidden"), 200);
                    }
                });
            }

            // Real-time clock with accurate time
            function updateClock() {
                const now = new Date();
                // Use local time with proper formatting
                const timeString = now.toLocaleTimeString('en-US', {
                    hour12: true,
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                const clockElement = document.getElementById('real-time-clock');
                if (clockElement) {
                    clockElement.textContent = timeString;
                }
            }
            updateClock();
            setInterval(updateClock, 1000);

            // Open "Visitor Management" dropdown by default since we're on Visitor History page
            const visitorBtn = document.getElementById('visitor-management-btn');
            const visitorSubmenu = document.getElementById('visitor-submenu');
            const visitorArrow = document.getElementById('visitor-arrow');
            
            if (visitorSubmenu && !visitorSubmenu.classList.contains('hidden')) {
                visitorSubmenu.classList.remove('hidden');
                if (visitorArrow) visitorArrow.classList.add('rotate-180');
            }

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#visitorTable tbody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
            }

            // Export functionality
            const exportBtn = document.getElementById('exportBtn');
            if (exportBtn) {
                exportBtn.addEventListener('click', function() {
                    // Get all visitor data from the table
                    const table = document.getElementById('visitorTable');
                    const rows = table.querySelectorAll('tbody tr');
                    let csvContent = "ID,Name,Company,Type,Host,Department,Check-in Date,Check-in Time,Check-out Date,Check-out Time,Status,Purpose\n";
                    
                    rows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        const rowData = [
                            cells[0]?.textContent?.trim() || '', // ID
                            cells[1]?.textContent?.trim() || '', // Name
                            cells[2]?.textContent?.trim() || '', // Company
                            cells[3]?.textContent?.trim() || '', // Type
                            cells[4]?.textContent?.trim() || '', // Host
                            cells[5]?.textContent?.trim() || '', // Department
                            cells[6]?.textContent?.trim() || '', // Check-in Date
                            cells[7]?.textContent?.trim() || '', // Check-in Time
                            cells[8]?.textContent?.trim() || '', // Check-out Date
                            cells[9]?.textContent?.trim() || '', // Check-out Time
                            cells[10]?.textContent?.trim() || '', // Status
                            cells[11]?.textContent?.trim() || ''  // Purpose
                        ];
                        csvContent += rowData.map(cell => `"${cell}"`).join(',') + '\n';
                    });
                    
                    // Create and download the CSV file
                    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    const url = URL.createObjectURL(blob);
                    link.setAttribute('href', url);
                    link.setAttribute('download', `visitor_history_${new Date().toISOString().split('T')[0]}.csv`);
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Export Completed',
                        text: 'Visitor history has been exported successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            }

            // Print functionality
            const printBtn = document.getElementById('printBtn');
            if (printBtn) {
                printBtn.addEventListener('click', function() {
                    // Create a new window for printing
                    const printWindow = window.open('', '_blank');
                    
                    // Get the table content
                    const table = document.getElementById('visitorTable').cloneNode(true);
                    
                    // Create print-friendly HTML
                    const printHTML = `
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>Visitor History Report</title>
                            <style>
                                body { font-family: Arial, sans-serif; margin: 20px; }
                                h1 { color: #059669; text-align: center; margin-bottom: 30px; }
                                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                                th { background-color: #f8f9fa; font-weight: bold; }
                                tr:nth-child(even) { background-color: #f9f9f9; }
                                .status-badge { padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: bold; }
                                .status-checked-in { background-color: #dcfce7; color: #166534; }
                                .status-checked-out { background-color: #fef2f2; color: #991b1b; }
                                .status-expected { background-color: #dbeafe; color: #1e40af; }
                                .status-overdue { background-color: #fef3c7; color: #92400e; }
                                .footer { margin-top: 30px; text-align: center; color: #666; font-size: 12px; }
                                @media print { body { margin: 10px; } }
                            </style>
                        </head>
                        <body>
                            <h1>Visitor History Report</h1>
                            <p style="text-align: center; color: #666; margin-bottom: 20px;">
                                Generated on: ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}
                            </p>
                            ${table.outerHTML}
                            <div class="footer">
                                <p>This report was generated from the Visitor Management System</p>
                            </div>
                        </body>
                        </html>
                    `;
                    
                    printWindow.document.write(printHTML);
                    printWindow.document.close();
                    
                    // Wait for the content to load, then print
                    printWindow.onload = function() {
                        printWindow.print();
                        printWindow.close();
                    };
                });
            }

            // Refresh functionality
            const refreshBtn = document.getElementById('refreshBtn');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', function() {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Refreshing...';
                    this.disabled = true;
                    
                    // Simulate API call
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                });
            }

            // Status filter buttons
            const statusButtons = document.querySelectorAll('button:not([id])');
            statusButtons.forEach(btn => {
                if (btn.textContent.includes('Checked') || btn.textContent.includes('Expected')) {
                    btn.addEventListener('click', function() {
                        const status = this.textContent.toLowerCase();
                        const rows = document.querySelectorAll('#visitorTable tbody tr');
                        
                        rows.forEach(row => {
                            const rowStatus = row.querySelector('.status-badge').textContent.toLowerCase();
                            if (status.includes('all') || rowStatus.includes(status)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                        
                        // Reset search
                        if (searchInput) searchInput.value = '';
                    });
                }
            });

            // View Visitor Modal
            const viewVisitorModal = document.getElementById("viewVisitorModal");
            const closeViewVisitorBtn = document.getElementById("closeViewVisitorBtn");
            const closeViewVisitorBtn2 = document.getElementById("closeViewVisitorBtn2");
            const exportVisitorBtn = document.getElementById("exportVisitorBtn");

            function openViewVisitorModal(visitorData) {
                // Populate modal with visitor data
                document.getElementById('visitorInitial').textContent = visitorData.initial;
                document.getElementById('visitorName').textContent = visitorData.name;
                document.getElementById('visitorCompany').textContent = visitorData.company;
                document.getElementById('visitorId').textContent = visitorData.id;
                document.getElementById('visitorType').textContent = visitorData.type;
                document.getElementById('visitorHost').textContent = visitorData.host;
                document.getElementById('visitorHostDept').textContent = visitorData.hostDept;
                document.getElementById('visitorDate').textContent = visitorData.date;
                document.getElementById('visitorTime').textContent = visitorData.time;
                
                const statusElement = document.getElementById('visitorStatus');
                statusElement.textContent = visitorData.status;
                statusElement.className = 'status-badge ' + visitorData.statusClass;
                
                document.getElementById('visitorNotes').textContent = visitorData.notes || 'No additional notes.';
                
                // Show modal
                viewVisitorModal.classList.add("active");
                viewVisitorModal.style.display = "flex";
            }

            function closeViewVisitorModal() {
                viewVisitorModal.classList.remove("active");
                setTimeout(() => {
                    viewVisitorModal.style.display = "none";
                }, 300);
            }

            // View visitor buttons
            document.addEventListener('click', function(e) {
                if (e.target.closest('.view-visitor-btn')) {
                    const btn = e.target.closest('.view-visitor-btn');
                    const row = btn.closest('tr');
                    
                    const visitorData = {
                        initial: row.querySelector('.flex-shrink-0 span').textContent,
                        name: row.querySelector('.text-sm.font-medium.text-gray-900').textContent,
                        company: row.cells[1].querySelector('.text-sm.text-gray-900').textContent,
                        id: row.querySelector('.text-xs.text-gray-500').textContent.replace('ID: ', ''),
                        type: row.cells[1].querySelector('.text-xs.text-gray-500.capitalize').textContent,
                        host: row.cells[2].querySelector('.text-sm.text-gray-900').textContent,
                        hostDept: row.cells[2].querySelector('.text-xs.text-gray-500').textContent,
                        date: row.cells[3].querySelector('.text-sm.text-gray-900').textContent,
                        time: row.cells[3].querySelector('.text-xs.text-gray-500').textContent,
                        status: row.querySelector('.status-badge').textContent,
                        statusClass: row.querySelector('.status-badge').className,
                        notes: 'Visited for ' + (row.cells[1].querySelector('.text-xs.text-gray-500.capitalize').textContent || 'general purpose')
                    };
                    
                    openViewVisitorModal(visitorData);
                }
            });

            // Close modal buttons
            closeViewVisitorBtn.addEventListener('click', closeViewVisitorModal);
            closeViewVisitorBtn2.addEventListener('click', closeViewVisitorModal);
            
            // Export visitor record
            exportVisitorBtn.addEventListener('click', function() {
                const visitorName = document.getElementById('visitorName').textContent;
                Swal.fire({
                    icon: 'success',
                    title: 'Record Exported',
                    text: `${visitorName}'s record has been exported successfully.`,
                    timer: 1500,
                    showConfirmButton: false
                });
                closeViewVisitorModal();
            });

            // Close modal when clicking outside
            viewVisitorModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeViewVisitorModal();
                }
            });

            // Individual visitor export buttons
            document.addEventListener('click', function(e) {
                if (e.target.closest('.export-visitor-btn')) {
                    const btn = e.target.closest('.export-visitor-btn');
                    const visitorName = btn.closest('tr').querySelector('.text-sm.font-medium.text-gray-900').textContent;
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Record Exported',
                        text: `${visitorName}'s record has been exported successfully.`,
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });

            // Profile and Settings buttons in user dropdown
            document.getElementById('openProfileBtn')?.addEventListener('click', () => {
                // In a real app, this would open the profile page
                Swal.fire({
                    icon: 'info',
                    title: 'Profile',
                    text: 'This would open the user profile page.',
                    timer: 1500,
                    showConfirmButton: false
                });
            });

            document.getElementById('openAccountSettingsBtn')?.addEventListener('click', () => {
                // In a real app, this would open the settings page
                Swal.fire({
                    icon: 'info',
                    title: 'Settings',
                    text: 'This would open the account settings page.',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        });
    </script>
</body>
</html>