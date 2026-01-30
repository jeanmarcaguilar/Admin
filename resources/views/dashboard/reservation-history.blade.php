@php
// Get the authenticated user
$user = auth()->user();

// Use provided $bookings (from route) or fallback to session store
$bookings = $bookings ?? session('calendar_bookings', [
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
// Map approval requests to enrich "Requested By" when available
$approvalMap = collect(session('approval_requests', []))->keyBy('id');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reservation History | Microfinance HR3</title>
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

        .dashboard-card {
            transition: all 0.2s ease-in-out;
            border: 1px solid #e5e7eb;
        }

        .dashboard-card:hover {
            border-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .reservation-row {
            transition: all 0.2s ease;
        }

        .reservation-row:hover {
            background-color: #f9fafb;
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

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .status-completed {
            background-color: #e0f2fe;
            color: #075985;
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
                        Microfinance HR
                    </div>
                    <div class="text-[11px] text-gray-500 font-semibold uppercase group-hover:text-brand-primary transition-colors">
                        HUMAN RESOURCE III
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
                <svg id="visitor-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="visitor-submenu" class="submenu mt-1 hidden">
                <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                    <a href="{{ route('visitors.registration') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Visitors Registration
                    </a>
                    <a href="{{ route('checkinout.tracking') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Check In/Out Tracking
                    </a>
                    <a href="{{ route('visitor.history.records') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
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
                <svg id="facilities-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="facilities-submenu" class="submenu mt-1">
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
                    <a href="{{ route('reservation.history') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
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
            <a href="#"
                class="mt-3 flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700
                    hover:bg-green-50 hover:text-brand-primary
                    transition-all duration-200 hover:translate-x-1 active:scale-[0.99] font-semibold">
                <span class="inline-flex w-9 h-9 rounded-lg bg-emerald-50 items-center justify-center">👤</span>
                Administrator
            </a>

            <div class="mt-8 px-2">
                <div class="flex items-center gap-2 text-xs font-bold text-emerald-600">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    SYSTEM ONLINE
                </div>
                <div class="text-[11px] text-gray-400 mt-2 leading-snug">
                    Microfinance HR © {{ date('Y') }}<br/>
                    Human Resource III System
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
                <h1 class="text-lg font-bold text-gray-800 hidden md:block">Reservation History</h1>
            </div>

            <div class="flex items-center gap-3 sm:gap-5">
                <!-- Clock pill -->
                <span id="real-time-clock"
                    class="text-xs font-bold text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                    {{ now()->format('H:i:s') }}
                </span>

                <!-- Notification Bell -->
                <button id="notificationBtn"
                    class="w-10 h-10 rounded-xl hover:bg-gray-100 active:bg-gray-200 transition flex items-center justify-center relative">
                    <i class="fas fa-bell text-gray-600"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-4 h-4 flex items-center justify-center">3</span>
                </button>

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
                            <h1 class="text-2xl font-bold text-gray-900">Reservation History</h1>
                            <p class="text-gray-600 mt-1">View and manage all reservation records</p>
                        </div>
                        <div class="mt-4 md:mt-0 flex space-x-3">
                            <div class="relative">
                                <input id="reservationSearch" type="text" placeholder="Search reservations..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent w-full md:w-64" aria-label="Search reservations">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <button id="exportReservationsBtn" class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors font-medium flex items-center">
                                <i class="fas fa-download mr-2"></i> Export
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Reservation Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Reservations -->
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Reservations</p>
                                <h3 class="text-2xl font-bold text-gray-900">{{ count($bookings) }}</h3>
                            </div>
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class='bx bx-calendar text-xl'></i>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            <span class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                {{ count(array_filter($bookings, fn($b) => $b['status'] === 'approved')) }} Approved
                            </span>
                        </div>
                    </div>

                    <!-- Pending Approvals -->
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Pending Approvals</p>
                                <h3 class="text-2xl font-bold text-amber-600">
                                    {{ count(array_filter($bookings, fn($b) => $b['status'] === 'pending')) }}
                                </h3>
                            </div>
                            <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                                <i class='bx bx-time-five text-xl'></i>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Awaiting review</p>
                    </div>

                    <!-- This Week -->
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">This Week</p>
                                <h3 class="text-2xl font-bold text-green-600">
                                    {{ count(array_filter($bookings, function($booking) {
                                        $date = $booking['date'] ?? '';
                                        $startOfWeek = now()->startOfWeek();
                                        $endOfWeek = now()->endOfWeek();
                                        return $date && strtotime($date) >= $startOfWeek->timestamp && strtotime($date) <= $endOfWeek->timestamp;
                                    })) }}
                                </h3>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class='bx bx-calendar-event text-xl'></i>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Scheduled events</p>
                    </div>

                    <!-- Rooms vs Equipment -->
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Rooms vs Equipment</p>
                                <h3 class="text-2xl font-bold text-gray-900">
                                    {{ count(array_filter($bookings, fn($b) => $b['type'] === 'room')) }} / {{ count(array_filter($bookings, fn($b) => $b['type'] === 'equipment')) }}
                                </h3>
                            </div>
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class='bx bx-building-house text-xl'></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <div class="flex flex-wrap gap-1">
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                    {{ count(array_filter($bookings, fn($b) => $b['type'] === 'room')) }} Rooms
                                </span>
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">
                                    {{ count(array_filter($bookings, fn($b) => $b['type'] === 'equipment')) }} Equipment
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-lg text-gray-900">All Reservations</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reservation ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Facility</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested By</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lead Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Decision Notes</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($bookings as $reservation)
                                <tr class="reservation-row" data-id="{{ $reservation['id'] ?? '' }}" data-type="{{ $reservation['type'] ?? '' }}" data-status="{{ $reservation['status'] ?? '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $reservation['id'] ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <i class="{{ ($reservation['type'] ?? 'room') === 'room' ? 'bx bx-building-house' : 'bx bx-video-recording' }} text-blue-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                @php
                                                    $title = $reservation['name'] ?? ($reservation['title'] ?? 'Booking');
                                                    $facilityType = $reservation['type'] ?? 'room';
                                                @endphp
                                                <div class="text-sm font-medium text-gray-900">{{ $title }}</div>
                                                <div class="text-sm text-gray-500">{{ ucfirst($facilityType) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $req = $approvalMap[$reservation['id']] ?? null;
                                            $requestedBy = $req['requested_by'] ?? ($user->name ?? 'User');
                                        @endphp
                                        <div class="text-sm text-gray-900">{{ $requestedBy }}</div>
                                        <div class="text-sm text-gray-500">&nbsp;</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $dateStr = isset($reservation['date']) ? \Carbon\Carbon::parse($reservation['date'])->format('M d, Y') : 'N/A';
                                            $start = $reservation['start_time'] ?? '';
                                            $end = $reservation['end_time'] ?? '';
                                            $start12 = $start ? \Carbon\Carbon::parse($start)->format('g:i A') : '';
                                            $end12 = $end ? \Carbon\Carbon::parse($end)->format('g:i A') : '';
                                            $timeStr = $start12 && $end12 ? ($start12 . ' - ' . $end12) : ($start12 ?: '');
                                        @endphp
                                        <div class="text-sm text-gray-900">{{ $dateStr }}</div>
                                        <div class="text-sm text-gray-500">{{ $timeStr ?: '—' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $leadTime = $reservation['lead_time'] ?? null;
                                            $leadTimeDisplay = $leadTime ? $leadTime . ' days' : 'Not specified';
                                        @endphp
                                        <div class="text-sm text-gray-900">{{ $leadTimeDisplay }}</div>
                                        @if($leadTime)
                                            <div class="text-xs text-gray-500">Preparation time</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $status = strtolower($reservation['status'] ?? 'pending');
                                            $statusClasses = [
                                                'approved' => 'status-badge status-approved',
                                                'pending' => 'status-badge status-pending',
                                                'rejected' => 'status-badge status-rejected',
                                                'completed' => 'status-badge status-completed'
                                            ];
                                            $statusClass = $statusClasses[$status] ?? 'status-badge status-pending';
                                        @endphp
                                        <span class="{{ $statusClass }}">{{ ucfirst($status) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            // Try to read decision notes/reasons from approval map or reservation payload
                                            $req = $approvalMap[$reservation['id']] ?? null;
                                            $note = $req['decision_reason'] ?? $req['reason'] ?? ($reservation['decision_note'] ?? ($reservation['reason'] ?? null));
                                            $isRejected = $status === 'rejected';
                                            $isApproved = $status === 'approved';
                                            // Defaults if nothing stored
                                            if (!$note && $isApproved) { $note = 'Approved: meets booking qualifications'; }
                                            if (!$note && $isRejected) { $note = 'Rejected'; }
                                        @endphp
                                        @if($note)
                                            @if($isRejected)
                                                <div class="inline-flex items-start max-w-xs md:max-w-md lg:max-w-lg">
                                                    <div class="bg-red-50 border border-red-200 text-red-800 rounded-md px-3 py-2 leading-snug">
                                                        <span class="block text-xs font-semibold tracking-wide uppercase mb-0.5">Rejection Reason</span>
                                                        <span class="text-sm">{{ $note }}</span>
                                                    </div>
                                                </div>
                                            @elseif($isApproved)
                                                <div class="inline-flex items-start max-w-xs md:max-w-md lg:max-w-lg">
                                                    <div class="bg-green-50 border border-green-200 text-green-800 rounded-md px-3 py-2 leading-snug">
                                                        <span class="block text-xs font-semibold tracking-wide uppercase mb-0.5">Approval Note</span>
                                                        <span class="text-sm">{{ $note }}</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-gray-700">{{ $note }}</div>
                                            @endif
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button type="button"
                                                class="text-blue-600 hover:text-blue-900 mr-3 view-reservation"
                                                data-id="{{ $reservation['id'] ?? '' }}"
                                                data-title="{{ $title }}"
                                                data-type="{{ $facilityType }}"
                                                data-date="{{ $reservation['date'] ?? '' }}"
                                                data-start="{{ $start12 }}"
                                                data-end="{{ $end12 }}"
                                                data-status="{{ strtolower($reservation['status'] ?? 'pending') }}"
                                                data-requested-by="{{ $requestedBy }}">
                                            View
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                        <p class="text-sm">No reservation history found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="bg-white px-6 py-3 flex items-center justify-between border-t border-gray-200">
                        <div class="text-sm text-gray-700">
                            Showing {{ count($bookings) > 0 ? 1 : 0 }} to {{ count($bookings) }} of {{ count($bookings) }} results
                        </div>
                        <div class="flex space-x-2">
                            <button disabled class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary disabled:opacity-50" disabled>
                                Previous
                            </button>
                            <button disabled class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary disabled:opacity-50" disabled>
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <!-- Reservation Details Modal -->
    <div id="reservationDetailsModal" class="modal hidden" aria-modal="true" role="dialog">
        <div class="bg-white rounded-lg w-full max-w-xl max-h-[90vh] overflow-y-auto fade-in">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 class="text-xl font-semibold text-gray-900">Reservation Details</h3>
                <button id="closeReservationDetails" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-3 text-sm" id="reservationDetailsContent">
                <div class="grid grid-cols-3 gap-2">
                    <div class="text-gray-500">Reservation ID</div>
                    <div class="col-span-2 font-medium" id="resId">—</div>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div class="text-gray-500">Title</div>
                    <div class="col-span-2 font-medium" id="resTitle">—</div>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div class="text-gray-500">Facility</div>
                    <div class="col-span-2 font-medium" id="resType">—</div>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div class="text-gray-500">Date</div>
                    <div class="col-span-2 font-medium" id="resDate">—</div>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div class="text-gray-500">Time</div>
                    <div class="col-span-2 font-medium" id="resTime">—</div>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div class="text-gray-500">Status</div>
                    <div class="col-span-2 font-medium" id="resStatus">—</div>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div class="text-gray-500">Requested By</div>
                    <div class="col-span-2 font-medium" id="resRequestedBy">—</div>
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
                        userMenuDropdown.classList.remove("opacity-100", "translate-y-0", "scale-100", "pointer-events-auto");
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

            // Real-time clock
            function updateClock() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('en-US', {
                    hour12: false,
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

            // Open "Facilities Management" dropdown by default since we're on Reservation History page
            const facilitiesBtn = document.getElementById('facilities-management-btn');
            const facilitiesSubmenu = document.getElementById('facilities-submenu');
            const facilitiesArrow = document.getElementById('facilities-arrow');
            
            if (facilitiesSubmenu && facilitiesSubmenu.classList.contains('hidden')) {
                facilitiesSubmenu.classList.remove('hidden');
                if (facilitiesArrow) facilitiesArrow.classList.add('rotate-180');
            }

            // Search functionality
            const searchInput = document.getElementById('reservationSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const rows = document.querySelectorAll('tbody tr.reservation-row');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
            }

            // Export functionality
            const exportBtn = document.getElementById('exportReservationsBtn');
            if (exportBtn) {
                exportBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const table = document.querySelector('table.min-w-full');
                    if (!table) return;
                    
                    const ths = Array.from(table.querySelectorAll('thead th'))
                        .map(th => th.textContent.trim());
                    // Exclude the last column (Actions)
                    const headers = ths.slice(0, ths.length - 1);
                    
                    const rows = Array.from(table.querySelectorAll('tbody tr:not([style*="display: none"])'))
                        .map(tr => Array.from(tr.querySelectorAll('td')).slice(0, ths.length - 1)
                            .map(td => td.textContent.replace(/\s+/g, ' ').trim())
                        );
                    
                    if (!rows.length) return;
                    
                    const escapeCSV = (v) => '"' + String(v).replace(/"/g, '""') + '"';
                    const csvLines = [headers.map(escapeCSV).join(',')];
                    rows.forEach(r => csvLines.push(r.map(escapeCSV).join(',')));
                    
                    const csv = '\uFEFF' + csvLines.join('\r\n'); // BOM for Excel
                    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'reservation-history.csv';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                });
            }

            // Modal Management
            const reservationModal = document.getElementById("reservationDetailsModal");
            const closeReservationDetails = document.getElementById("closeReservationDetails");
            const resId = document.getElementById("resId");
            const resTitle = document.getElementById("resTitle");
            const resType = document.getElementById("resType");
            const resDate = document.getElementById("resDate");
            const resTime = document.getElementById("resTime");
            const resStatus = document.getElementById("resStatus");
            const resRequestedBy = document.getElementById("resRequestedBy");

            function openModal(modal) {
                modal.classList.add("active");
                modal.style.display = "flex";
            }

            function closeModal(modal) {
                modal.classList.remove("active");
                setTimeout(() => {
                    modal.style.display = "none";
                }, 300);
            }

            function to12h(t) {
                if (!t) return "";
                const s = String(t).trim();
                // Already AM/PM like 2:00 PM or 02:00 pm
                const ampmMatch = s.match(/^(\d{1,2}):(\d{2})(?::(\d{2}))?\s*([ap]m)$/i);
                if (ampmMatch) {
                    const h = parseInt(ampmMatch[1], 10);
                    const m = ampmMatch[2];
                    const mer = ampmMatch[4].toUpperCase();
                    return `${h}:${m} ${mer}`;
                }
                // 24h with or without seconds, e.g., 14:00 or 14:00:00
                const hMatch = s.match(/^(\d{1,2}):(\d{2})(?::\d{2})?$/);
                if (hMatch) {
                    let h = parseInt(hMatch[1], 10);
                    const m = hMatch[2];
                    const mer = h >= 12 ? "PM" : "AM";
                    const hh = ((h + 11) % 12) + 1;
                    return `${hh}:${m} ${mer}`;
                }
                // Compact HHmm like 1400
                const compact = s.match(/^(\d{2})(\d{2})$/);
                if (compact) {
                    let h = parseInt(compact[1], 10);
                    const m = compact[2];
                    const mer = h >= 12 ? "PM" : "AM";
                    const hh = ((h + 11) % 12) + 1;
                    return `${hh}:${m} ${mer}`;
                }
                return s;
            }

            // View Reservation Details
            document.querySelectorAll('.view-reservation').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const id = btn.getAttribute('data-id') || '—';
                    const title = btn.getAttribute('data-title') || '—';
                    const type = btn.getAttribute('data-type') || '—';
                    const date = btn.getAttribute('data-date') || '';
                    const start = btn.getAttribute('data-start') || '';
                    const end = btn.getAttribute('data-end') || '';
                    const status = btn.getAttribute('data-status') || 'pending';
                    const requestedBy = btn.getAttribute('data-requested-by') || '—';

                    resId.textContent = `#${id}`;
                    resTitle.textContent = title;
                    resType.textContent = type.charAt(0).toUpperCase() + type.slice(1);
                    resDate.textContent = date ? new Date(date).toLocaleDateString(undefined, { 
                        year: 'numeric', 
                        month: 'short', 
                        day: '2-digit' 
                    }) : '—';
                    
                    const start12 = to12h(start);
                    const end12 = to12h(end);
                    const timeStr = start12 && end12 ? `${start12} - ${end12}` : (start12 || end12 || '—');
                    resTime.textContent = timeStr;
                    
                    resStatus.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                    resRequestedBy.textContent = requestedBy;

                    openModal(reservationModal);
                });
            });

            closeReservationDetails.addEventListener('click', () => {
                closeModal(reservationModal);
            });

            // Close modal when clicking outside
            reservationModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this);
                }
            });

            // Reservation lock/unlock functionality
            function updateReservationHistoryLockState(isLocked) {
                const tableRows = document.querySelectorAll('tbody tr.reservation-row');
                
                tableRows.forEach(row => {
                    const idCell = row.querySelector('td:nth-child(1)');
                    const facilityCell = row.querySelector('td:nth-child(2) .text-sm.font-medium');
                    const typeCell = row.querySelector('td:nth-child(2) .text-sm.text-gray-500');
                    const requestedByCell = row.querySelector('td:nth-child(3) .text-sm');
                    const dateCell = row.querySelector('td:nth-child(4) .text-sm:first-child');
                    const timeCell = row.querySelector('td:nth-child(4) .text-sm:last-child');
                    const leadTimeCell = row.querySelector('td:nth-child(5) .text-sm');
                    const statusCell = row.querySelector('td:nth-child(6) .status-badge');
                    const decisionNoteCell = row.querySelector('td:nth-child(7)');
                    const viewButton = row.querySelector('td:nth-child(8) button');
                    
                    if (isLocked) {
                        // Store original data if not already stored
                        if (!row.dataset.originalData) {
                            row.dataset.originalData = JSON.stringify({
                                id: idCell?.textContent || '',
                                facility: facilityCell?.textContent || '',
                                type: typeCell?.textContent || '',
                                requestedBy: requestedByCell?.textContent || '',
                                date: dateCell?.textContent || '',
                                time: timeCell?.textContent || '',
                                leadTime: leadTimeCell?.textContent || '',
                                status: statusCell?.textContent || '',
                                statusClass: statusCell?.className || '',
                                decisionNote: decisionNoteCell?.innerHTML || ''
                            });
                        }
                        
                        // Mask the data
                        if (idCell) idCell.textContent = '****';
                        if (facilityCell) {
                            facilityCell.innerHTML = '**** <i class="fas fa-lock text-red-500 text-xs ml-1"></i>';
                        }
                        if (typeCell) typeCell.textContent = '****';
                        if (requestedByCell) requestedByCell.textContent = '****';
                        if (dateCell) dateCell.textContent = '** ** ****';
                        if (timeCell && timeCell.textContent !== '—') {
                            timeCell.textContent = '**:**** - **:****';
                        }
                        if (leadTimeCell) {
                            const text = leadTimeCell.textContent;
                            leadTimeCell.textContent = text.includes('days') ? '** days' : '****';
                        }
                        if (statusCell) {
                            statusCell.textContent = '****';
                            statusCell.className = 'status-badge bg-gray-100 text-gray-800';
                        }
                        if (decisionNoteCell && !decisionNoteCell.querySelector('.text-gray-400')) {
                            decisionNoteCell.innerHTML = '<span class="text-gray-400">****</span>';
                        }
                        
                        // Disable view button
                        if (viewButton) {
                            viewButton.disabled = true;
                            viewButton.style.opacity = '0.5';
                            viewButton.style.cursor = 'not-allowed';
                            viewButton.style.pointerEvents = 'none';
                        }
                        
                        // Add lock styling to row
                        row.style.opacity = '0.7';
                        row.classList.add('locked-row');
                    } else {
                        // Restore original data
                        if (row.dataset.originalData) {
                            try {
                                const originalData = JSON.parse(row.dataset.originalData);
                                
                                if (idCell) idCell.textContent = originalData.id;
                                if (facilityCell) facilityCell.innerHTML = originalData.facility;
                                if (typeCell) typeCell.textContent = originalData.type;
                                if (requestedByCell) requestedByCell.textContent = originalData.requestedBy;
                                if (dateCell) dateCell.textContent = originalData.date;
                                if (timeCell) timeCell.textContent = originalData.time;
                                if (leadTimeCell) leadTimeCell.textContent = originalData.leadTime;
                                if (statusCell) {
                                    statusCell.textContent = originalData.status;
                                    statusCell.className = originalData.statusClass;
                                }
                                if (decisionNoteCell && originalData.decisionNote) {
                                    decisionNoteCell.innerHTML = originalData.decisionNote;
                                }
                            } catch (e) {
                                console.error('Error restoring original data:', e);
                            }
                        }
                        
                        // Restore view button
                        if (viewButton) {
                            viewButton.disabled = false;
                            viewButton.style.opacity = '1';
                            viewButton.style.cursor = 'pointer';
                            viewButton.style.pointerEvents = 'auto';
                        }
                        
                        // Remove lock styling from row
                        row.style.opacity = '1';
                        row.classList.remove('locked-row');
                    }
                });
            }

            // Check and apply lock state on page load
            function checkAndApplyLockState() {
                const isLocked = localStorage.getItem('reservationsLocked') === 'true';
                updateReservationHistoryLockState(isLocked);
            }

            // Listen for storage changes (for cross-tab synchronization)
            window.addEventListener('storage', (e) => {
                if (e.key === 'reservationsLocked') {
                    const isLocked = e.newValue === 'true';
                    updateReservationHistoryLockState(isLocked);
                }
            });

            // Apply lock state on page load
            checkAndApplyLockState();
        });
    </script>
</body>
</html>