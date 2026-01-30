@php
// Get the authenticated user
$user = auth()->user();

// Initialize requests array if not set
$requests = $requests ?? [
    [
        'id' => 'REQ-001',
        'title' => 'Meeting Room Booking',
        'type' => 'room',
        'requested_by' => 'John Smith',
        'date' => '2025-01-25',
        'status' => 'pending',
        'lead_time' => '3',
        'description' => 'Quarterly team meeting for Q1 planning'
    ],
    [
        'id' => 'REQ-002', 
        'title' => 'Projector Request',
        'type' => 'equipment',
        'requested_by' => 'Sarah Johnson',
        'date' => '2025-01-26',
        'status' => 'pending',
        'lead_time' => '2',
        'description' => 'Need projector for client presentation'
    ],
    [
        'id' => 'REQ-003',
        'title' => 'Training Room Setup',
        'type' => 'room',
        'requested_by' => 'Mike Wilson',
        'date' => '2025-01-28',
        'status' => 'approved',
        'lead_time' => '7',
        'description' => 'New employee training session'
    ],
    [
        'id' => 'REQ-004',
        'title' => 'Audio System',
        'type' => 'equipment', 
        'requested_by' => 'Emily Davis',
        'date' => '2025-01-30',
        'status' => 'pending',
        'lead_time' => '1',
        'description' => 'Audio system for company event'
    ],
    [
        'id' => 'REQ-005',
        'title' => 'Conference Room',
        'type' => 'room',
        'requested_by' => 'David Brown',
        'date' => '2025-02-02',
        'status' => 'rejected',
        'lead_time' => '5',
        'description' => 'Board meeting with investors'
    ]
];
$pendingCount = collect($requests)->where('status', 'pending')->count();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Approval Workflow | Microfinance HR3</title>
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

        .activity-item {
            transition: all 0.2s ease-in-out;
        }

        .activity-item:hover {
            background-color: rgba(5, 150, 105, 0.05);
            transform: translateX(2px);
        }

        .status-badge {
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
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
                    <a href="{{ route('approval.workflow') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
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
                <h1 class="text-lg font-bold text-gray-800 hidden md:block">Approval Workflow</h1>
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
                    @if($pendingCount > 0)
                    <span class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-red-500 border-2 border-white"></span>
                    @endif
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

        <!-- Notification Dropdown -->
        <div id="notificationDropdown" class="hidden absolute right-4 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50" style="top: 4rem;">
            <div class="flex justify-between items-center px-4 py-3 border-b border-gray-200">
                <span class="font-semibold text-sm text-gray-800">Notifications</span>
                @if($pendingCount > 0)
                <span class="bg-red-600 text-white text-xs font-semibold rounded-full px-2 py-1">{{ $pendingCount }} new</span>
                @endif
            </div>
            <ul class="divide-y divide-gray-200 max-h-72 overflow-y-auto">
                @foreach($requests as $request)
                    @if($request['status'] === 'pending')
                    <li class="flex items-start px-4 py-3 space-x-3 hover:bg-gray-50">
                        <div class="flex-shrink-0 mt-1">
                            <div class="bg-green-100 text-green-600 rounded-full p-2">
                                <i class="fas fa-calendar-check text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-grow text-sm">
                            <p class="font-semibold text-gray-900 leading-tight">{{ $request['title'] }}</p>
                            <p class="text-gray-600 leading-tight text-xs">{{ $request['requested_by'] }} requested approval</p>
                            <p class="text-gray-400 text-xs mt-0.5">{{ \Carbon\Carbon::parse($request['date'])->diffForHumans() }}</p>
                        </div>
                    </li>
                    @endif
                @endforeach
            </ul>
            <div class="text-center py-2 border-t border-gray-200">
                <a class="text-brand-primary text-xs font-semibold hover:underline" href="#">View all notifications</a>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <main class="p-4 sm:p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Page Header -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Approval Workflow</h1>
                            <p class="text-gray-600 mt-1">Review and manage pending approval requests</p>
                        </div>
                        <div class="mt-4 md:mt-0 flex items-center gap-3">
                            <span class="text-sm text-gray-600">{{ $pendingCount }} pending requests</span>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="border-b border-gray-200 mt-6">
                        <nav class="-mb-px flex space-x-8">
                            <a href="#"
                                class="border-brand-primary text-brand-primary whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Pending Approval ({{ $pendingCount }})
                            </a>
                            <a href="#"
                                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                My Requests
                            </a>
                            <a href="#"
                                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                History
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Approval Requests Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class='bx bx-list-ul mr-2 text-brand-primary'></i>Pending Approval Requests
                        </h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested By</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($requests as $request)
                                <tr class="activity-item hover:bg-gray-50" data-request-id="{{ $request['id'] }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center {{ $request['type'] === 'room' ? 'bg-blue-50' : 'bg-purple-50' }}">
                                                <i class='{{ $request['type'] === 'room' ? 'bx bx-door-open text-blue-600' : 'bx bx-cube text-purple-600' }}'></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $request['title'] }}</div>
                                                <div class="text-xs text-gray-500">#{{ $request['id'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $request['requested_by'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($request['date'])->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClasses = [
                                                'pending' => 'status-pending',
                                                'approved' => 'status-approved',
                                                'rejected' => 'status-rejected'
                                            ];
                                            $statusClass = $statusClasses[$request['status']] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="status-badge {{ $statusClass }}">
                                            {{ ucfirst($request['status']) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button type="button" onclick='showRequestDetails(@json($request))' 
                                            class="text-brand-primary hover:text-brand-primary-hover mr-4 font-medium">
                                            View
                                        </button>
                                        @if($request['status'] === 'pending')
                                        <button type="button" onclick="showActionConfirmation('{{ $request['id'] }}', 'approve')"
                                            class="text-green-600 hover:text-green-700 mr-4 font-medium">
                                            Approve
                                        </button>
                                        <button type="button" onclick="showActionConfirmation('{{ $request['id'] }}', 'reject')"
                                            class="text-red-600 hover:text-red-700 font-medium">
                                            Reject
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center">
                                        <div class="text-gray-400">
                                            <i class='bx bx-check-circle text-4xl mb-2'></i>
                                            <p class="text-sm">No pending requests found.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- View Request Details Modal -->
    <div id="viewRequestModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto mx-4">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 class="text-xl font-semibold text-gray-900">Request Details</h3>
                <button onclick="closeModal('viewRequestModal')" class="text-gray-400 hover:text-gray-500 rounded-lg p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6" id="requestDetailsContent">
                <!-- Content will be loaded by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Action Confirmation Modal -->
    <div id="actionConfirmationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl w-full max-w-md mx-4">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2" id="actionModalTitle">Confirm Action</h3>
                <p class="text-sm text-gray-500 mb-6" id="actionModalMessage">Are you sure you want to take this action? This cannot be undone.</p>
                <div class="flex justify-center space-x-4">
                    <button type="button" onclick="closeModal('actionConfirmationModal')" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="button" id="confirmActionBtn"
                        class="px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message Toast -->
    @if(session('success'))
    <div id="successToast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center justify-between z-50" style="min-width: 300px;">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="document.getElementById('successToast').remove()" class="ml-4 text-white hover:text-gray-100">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('successToast');
            if (toast) {
                toast.remove();
            }
        }, 5000);
    </script>
    @endif

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

            // Notification dropdown
            const notificationBtn = document.getElementById("notificationBtn");
            const notificationDropdown = document.getElementById("notificationDropdown");

            if (notificationBtn && notificationDropdown) {
                notificationBtn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    notificationDropdown.classList.toggle("hidden");
                });

                // Close dropdown when clicking outside
                document.addEventListener("click", (e) => {
                    if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                        notificationDropdown.classList.add("hidden");
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

            // Modal functions
            window.openModal = function(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            }

            window.closeModal = function(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            }

            // Close modals when clicking outside or pressing Escape
            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('fixed') && 
                    event.target.classList.contains('inset-0') && 
                    event.target.classList.contains('bg-black')) {
                    closeModal(event.target.id);
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    const openModal = document.querySelector('.fixed:not(.hidden)');
                    if (openModal) {
                        closeModal(openModal.id);
                    }
                }
            });

            // Show request details in modal
            window.showRequestDetails = function(request) {
                const contentDiv = document.getElementById('requestDetailsContent');

                // Format dates
                const requestDate = new Date(request.date);
                const formattedDate = requestDate.toLocaleDateString('en-US', {
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric'
                });

                // Status badge
                const statusClasses = {
                    'pending': 'status-pending',
                    'approved': 'status-approved',
                    'rejected': 'status-rejected'
                };
                const statusClass = statusClasses[request.status] || 'bg-gray-100 text-gray-800';

                // Build the content
                contentDiv.innerHTML = `
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">${request.title}</h3>
                            <span class="status-badge ${statusClass}">
                                ${request.status.charAt(0).toUpperCase() + request.status.slice(1)}
                            </span>
                        </div>

                        <div class="border-t border-b border-gray-200 py-6">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div class="mb-4">
                                    <dt class="text-sm font-medium text-gray-500">Request ID</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${request.id}</dd>
                                </div>
                                <div class="mb-4">
                                    <dt class="text-sm font-medium text-gray-500">Requested By</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${request.requested_by}</dd>
                                </div>
                                <div class="mb-4">
                                    <dt class="text-sm font-medium text-gray-500">Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${formattedDate}</dd>
                                </div>
                                <div class="mb-4">
                                    <dt class="text-sm font-medium text-gray-500">Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${request.type.charAt(0).toUpperCase() + request.type.slice(1)}</dd>
                                </div>
                                <div class="mb-4">
                                    <dt class="text-sm font-medium text-gray-500">Lead Time</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${request.lead_time ? request.lead_time + ' days' : 'Not specified'}</dd>
                                </div>
                                <div class="col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">${request.description || 'No description provided'}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('viewRequestModal')" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Close
                        </button>
                        ${request.status === 'pending' ? `
                        <button type="button" onclick="closeModal('viewRequestModal'); showActionConfirmation('${request.id}', 'approve')" 
                            class="px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Approve
                        </button>
                        <button type="button" onclick="closeModal('viewRequestModal'); showActionConfirmation('${request.id}', 'reject')" 
                            class="px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Reject
                        </button>` : ''}
                    </div>
                `;

                openModal('viewRequestModal');
            }

            // Action confirmation functionality
            let currentRequestId = '';
            let currentAction = '';
            let currentActionUrl = '';

            window.showActionConfirmation = function(requestId, action) {
                currentRequestId = requestId;
                currentAction = action;
                currentActionUrl = action === 'approve'
                    ? `{{ url('approval') }}/${requestId}/approve`
                    : `{{ url('approval') }}/${requestId}/reject`;

                const confirmBtn = document.getElementById('confirmActionBtn');
                const modalTitle = document.getElementById('actionModalTitle');
                const modalMessage = document.getElementById('actionModalMessage');

                modalTitle.textContent = action === 'approve' ? 'Approve Request' : 'Reject Request';
                modalMessage.textContent = `Are you sure you want to ${action} this request? This action cannot be undone.`;
                confirmBtn.textContent = action === 'approve' ? 'Approve Request' : 'Reject Request';
                confirmBtn.className = `px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white ${action === 'approve' ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500' : 'bg-red-600 hover:bg-red-700 focus:ring-red-500'} focus:outline-none focus:ring-2 focus:ring-offset-2`;

                confirmBtn.onclick = handleActionRequest;
                openModal('actionConfirmationModal');
            }

            window.handleActionRequest = async function() {
                const confirmBtn = document.getElementById('confirmActionBtn');
                const originalText = confirmBtn.innerHTML;

                try {
                    confirmBtn.disabled = true;
                    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

                    const response = await fetch(currentActionUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                        body: null
                    });

                    let data;
                    const contentType = response.headers.get('content-type') || '';
                    if (contentType.includes('application/json')) {
                        data = await response.json();
                    } else {
                        // Fallback for non-JSON
                        data = { success: response.ok, message: response.ok ? 'Action completed.' : 'Request failed.' };
                    }

                    if (response.ok && data.success !== false) {
                        const requestRow = document.querySelector(`tr[data-request-id="${currentRequestId}"]`);

                        if (requestRow) {
                            const statusCell = requestRow.querySelector('.status-badge');
                            if (statusCell) {
                                const statusClass = currentAction === 'approve' ? 'status-approved' : 'status-rejected';
                                statusCell.className = `status-badge ${statusClass}`;
                                statusCell.textContent = currentAction === 'approve' ? 'Approved' : 'Rejected';
                            }

                            const actionButtons = requestRow.querySelectorAll('td:nth-child(5) button');
                            actionButtons.forEach(button => {
                                if (button.textContent === 'Approve' || button.textContent === 'Reject') {
                                    button.remove();
                                }
                            });
                        }

                        closeModal('actionConfirmationModal');
                        showSuccessMessage(data.message || `Request has been ${currentAction}d successfully`);
                    } else {
                        throw new Error(data.message || `Failed to ${currentAction} request`);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert(error.message || `An error occurred while ${currentAction}ing the request`);
                } finally {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = originalText;
                }
            }

            // Show success message
            function showSuccessMessage(message) {
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center justify-between z-50';
                toast.style.minWidth = '300px';
                toast.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>${message}</span>
                    </div>
                    <button class="ml-4 text-white hover:text-gray-100">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 5000);

                toast.querySelector('button').addEventListener('click', () => {
                    toast.remove();
                });
            }

            // Add SweetAlert2 confirmation for actions
            document.addEventListener('DOMContentLoaded', function() {
                // Approve button confirmation
                document.querySelectorAll('button[onclick*="showActionConfirmation"][onclick*="approve"]').forEach(button => {
                    button.addEventListener('click', function(e) {
                        const requestId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
                        const row = this.closest('tr');
                        const dateText = row.querySelector('td:nth-child(3) .text-sm').textContent;
                        const requestDate = new Date(dateText);
                        
                        if (requestDate < new Date()) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Cannot Approve',
                                text: 'Booking date is in the past.',
                            });
                            e.preventDefault();
                            return false;
                        }
                    });
                });

                // Reject button confirmation with reason
                document.querySelectorAll('button[onclick*="showActionConfirmation"][onclick*="reject"]').forEach(button => {
                    button.addEventListener('click', async function(e) {
                        e.preventDefault();
                        const requestId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
                        
                        const { value: reason } = await Swal.fire({
                            title: 'Reject Request',
                            input: 'textarea',
                            inputLabel: 'Reason for rejection',
                            inputPlaceholder: 'Enter your reason here...',
                            inputAttributes: {
                                'aria-label': 'Type your reason here'
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Reject',
                            cancelButtonText: 'Cancel',
                            inputValidator: (value) => {
                                if (!value) {
                                    return 'Please provide a reason for rejection';
                                }
                            }
                        });

                        if (reason) {
                            // You can handle the rejection with reason here
                            showActionConfirmation(requestId, 'reject');
                        }
                    });
                });
            });

            // Open "Facilities Management" dropdown by default since we're on Approval Workflow page
            const facilitiesBtn = document.getElementById('facilities-management-btn');
            const facilitiesSubmenu = document.getElementById('facilities-submenu');
            const facilitiesArrow = document.getElementById('facilities-arrow');
            
            if (facilitiesSubmenu && !facilitiesSubmenu.classList.contains('hidden')) {
                facilitiesSubmenu.classList.remove('hidden');
                if (facilitiesArrow) facilitiesArrow.classList.add('rotate-180');
            }
        });
    </script>
</body>
</html>