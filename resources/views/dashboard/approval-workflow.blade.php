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
    <title>Administrative</title>
    <link rel="icon" type="image/png"
        href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
    <link rel="shortcut icon" type="image/png"
        href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
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
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 hidden opacity-0 transition-opacity duration-300 z-40">
    </div>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed top-0 left-0 h-full w-72 bg-white border-r border-gray-100 shadow-sm z-50
            transform -translate-x-full md:translate-x-0 transition-transform duration-300">

        <div class="h-16 flex items-center px-4 border-b border-gray-100">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 w-full rounded-xl px-2 py-2
                    hover:bg-gray-100 active:bg-gray-200 transition group">
                <img src="{{ asset('golden-arc.png') }}" alt="Logo" class="w-10 h-10">
                <div class="leading-tight">
                    <div class="font-bold text-gray-800 group-hover:text-brand-primary transition-colors">
                        Microfinance HR
                    </div>
                    <div
                        class="text-[11px] text-gray-500 font-semibold uppercase group-hover:text-brand-primary transition-colors">
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
                <svg id="visitor-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="visitor-submenu" class="submenu mt-1 hidden">
                <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                    <a href="{{ route('visitors.registration') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        Visitors Registration
                    </a>
                    <a href="{{ route('checkinout.tracking') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        Check In/Out Tracking
                    </a>
                    <a href="{{ route('visitor.history.records') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
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
                <svg id="document-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="document-submenu" class="submenu mt-1 hidden">
                <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                    <a href="{{ route('document.upload.indexing') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        Document Upload & Indexing
                    </a>
                    <a href="{{ route('document.version.control') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        Version Control
                    </a>
                    <a href="{{ route('document.access.control.permissions') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        Access Control & Permissions
                    </a>
                    <a href="{{ route('document.archival.retention.policy') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
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
                <svg id="facilities-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300 rotate-180"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="facilities-submenu" class="submenu mt-1">
                <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                    <a href="{{ route('room-equipment') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        Room & Equipment Booking
                    </a>
                    <a href="{{ route('scheduling.calendar') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        Scheduling & Calendar Integrations
                    </a>
                    <a href="{{ route('approval.workflow') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        Approval Workflow
                    </a>
                    <a href="{{ route('reservation.history') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
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
                <svg id="legal-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="legal-submenu" class="submenu mt-1 hidden">
                <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                    <a href="{{ route('case.management') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        Case Management
                    </a>
                    <a href="{{ route('contract.management') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        Contract Management
                    </a>
                    <a href="{{ route('compliance.tracking') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        Compliance Tracking
                    </a>
                    <a href="{{ route('deadline.hearing.alerts') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
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
                    Microfinance HR © {{ date('Y') }}<br />
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

                <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>

                <!-- User Profile Dropdown -->
                <div class="relative">
                    <button id="user-menu-button" class="flex items-center gap-3 focus:outline-none group rounded-xl px-2 py-2
                            hover:bg-gray-100 active:bg-gray-200 transition">
                        <div
                            class="w-10 h-10 rounded-full bg-white shadow group-hover:shadow-md transition-shadow overflow-hidden flex items-center justify-center border border-gray-100">
                            <div
                                class="w-full h-full flex items-center justify-center font-bold text-brand-primary bg-emerald-50">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="hidden md:flex flex-col items-start text-left">
                            <span
                                class="text-sm font-bold text-gray-700 group-hover:text-brand-primary transition-colors">
                                {{ $user->name }}
                            </span>
                            <span
                                class="text-[10px] text-gray-500 font-medium uppercase group-hover:text-brand-primary transition-colors">
                                Administrator
                            </span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-primary transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>

                    <div id="user-menu-dropdown" class="dropdown-panel hidden opacity-0 translate-y-2 scale-95 pointer-events-none
                            absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-lg border border-gray-100
                            transition-all duration-200 z-50">
                        <button id="openProfileBtn"
                            class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">Profile</button>
                        <button id="openAccountSettingsBtn"
                            class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">Settings</button>
                        <div class="h-px bg-gray-100"></div>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Notification Dropdown -->
        <div id="notificationDropdown"
            class="hidden absolute right-4 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50"
            style="top: 4rem;">
            <div class="flex justify-between items-center px-4 py-3 border-b border-gray-200">
                <span class="font-semibold text-sm text-gray-800">Notifications</span>
                @if($pendingCount > 0)
                    <span class="bg-red-600 text-white text-xs font-semibold rounded-full px-2 py-1">{{ $pendingCount }}
                        new</span>
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
                                <p class="text-gray-600 leading-tight text-xs">{{ $request['requested_by'] }} requested approval
                                </p>
                                <p class="text-gray-400 text-xs mt-0.5">
                                    {{ \Carbon\Carbon::parse($request['date'])->diffForHumans() }}
                                </p>
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
                <!-- Enhanced Approval Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Requests Card -->
                    <div
                        class="group relative bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 overflow-hidden">
                        <div
                            class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 rounded-full bg-blue-50 blur-3xl opacity-60 group-hover:opacity-100 transition-opacity">
                        </div>
                        <div class="relative flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Total Requests</p>
                                <h3 class="text-3xl font-bold text-gray-900">{{ count($requests) }}</h3>
                                <div
                                    class="mt-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                                    Overview
                                </div>
                            </div>
                            <div
                                class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center shadow-blue-200 shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                <i class="bx bx-file text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Approval Card -->
                    <div
                        class="group relative bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 overflow-hidden">
                        <div
                            class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 rounded-full bg-amber-50 blur-3xl opacity-60 group-hover:opacity-100 transition-opacity">
                        </div>
                        <div class="relative flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Pending Approval</p>
                                <h3 class="text-3xl font-bold text-gray-900">
                                    {{ collect($requests)->where('status', 'pending')->count() }}
                                </h3>
                                <div
                                    class="mt-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                    Action Required
                                </div>
                            </div>
                            <div
                                class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-500 text-white flex items-center justify-center shadow-amber-200 shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                <i class="bx bx-time-five text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Approved Card -->
                    <div
                        class="group relative bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 overflow-hidden">
                        <div
                            class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 rounded-full bg-emerald-50 blur-3xl opacity-60 group-hover:opacity-100 transition-opacity">
                        </div>
                        <div class="relative flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Approved</p>
                                <h3 class="text-3xl font-bold text-gray-900">
                                    {{ collect($requests)->where('status', 'approved')->count() }}
                                </h3>
                                <div
                                    class="mt-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    Completed
                                </div>
                            </div>
                            <div
                                class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-500 text-white flex items-center justify-center shadow-emerald-200 shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                <i class="bx bx-check-circle text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Rejected Card -->
                    <div
                        class="group relative bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 overflow-hidden">
                        <div
                            class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 rounded-full bg-red-50 blur-3xl opacity-60 group-hover:opacity-100 transition-opacity">
                        </div>
                        <div class="relative flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Rejected</p>
                                <h3 class="text-3xl font-bold text-gray-900">
                                    {{ collect($requests)->where('status', 'rejected')->count() }}
                                </h3>
                                <div
                                    class="mt-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                    Denied
                                </div>
                            </div>
                            <div
                                class="w-14 h-14 rounded-2xl bg-gradient-to-br from-red-500 to-red-600 text-white flex items-center justify-center shadow-red-200 shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                <i class="bx bx-x-circle text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

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
                    <div class="mt-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="bg-gray-100/80 p-1.5 rounded-xl inline-flex space-x-1">
                            <button onclick="switchTab('pending')" id="tab-pending"
                                class="px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm bg-white text-gray-900 transition-all duration-200 flex items-center gap-2">
                                Pending Approval
                                <span
                                    class="bg-brand-primary text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $pendingCount }}</span>
                            </button>
                            <button onclick="switchTab('my-requests')" id="tab-my-requests"
                                class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-500 hover:text-gray-900 hover:bg-white/50 transition-all duration-200">
                                My Requests
                            </button>
                            <button onclick="switchTab('history')" id="tab-history"
                                class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-500 hover:text-gray-900 hover:bg-white/50 transition-all duration-200">
                                History
                            </button>
                        </div>

                        <!-- Search/Filter (Optional for improved UX) -->
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bx bx-search text-gray-400"></i>
                            </div>
                            <input type="text" id="requestSearch"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary sm:text-sm transition duration-150 ease-in-out"
                                placeholder="Search requests...">
                        </div>
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
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Request</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Requested By</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($requests as $request)
                                    <tr class="activity-item hover:bg-gray-50" data-request-id="{{ $request['id'] }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center {{ $request['type'] === 'room' ? 'bg-blue-50' : 'bg-purple-50' }}">
                                                    <i
                                                        class='{{ $request['type'] === 'room' ? 'bx bx-door-open text-blue-600' : 'bx bx-cube text-purple-600' }}'></i>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $request['title'] }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">#{{ $request['id'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $request['requested_by'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse($request['date'])->format('M d, Y') }}
                                            </div>
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
                                                class="px-3 py-1.5 bg-gradient-to-r from-teal-500 to-cyan-500 text-white rounded-lg text-xs font-semibold hover:from-teal-600 hover:to-cyan-600 transition-all duration-200 shadow hover:shadow-md transform hover:-translate-y-0.5 mr-2 flex items-center">
                                                <i class="fas fa-eye mr-1"></i>
                                                View
                                            </button>
                                            @if($request['status'] === 'pending')
                                                <button type="button"
                                                    onclick="showActionConfirmation('{{ $request['id'] }}', 'approve')"
                                                    class="px-3 py-1.5 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-lg text-xs font-semibold hover:from-green-600 hover:to-emerald-600 transition-all duration-200 shadow hover:shadow-md transform hover:-translate-y-0.5 mr-2 flex items-center">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Approve
                                                </button>
                                                <button type="button"
                                                    onclick="showActionConfirmation('{{ $request['id'] }}', 'reject')"
                                                    class="px-3 py-1.5 bg-gradient-to-r from-red-500 to-rose-500 text-white rounded-lg text-xs font-semibold hover:from-red-600 hover:to-rose-600 transition-all duration-200 shadow hover:shadow-md transform hover:-translate-y-0.5 flex items-center">
                                                    <i class="fas fa-times-circle mr-1"></i>
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
    <div id="viewRequestModal"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 hidden overflow-y-auto h-full w-full p-4">
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto mx-4 transform transition-all duration-500 scale-95 opacity-0">
            <!-- Modal Header with Enhanced Gradient -->
            <div class="bg-gradient-to-r from-teal-600 via-cyan-600 to-blue-600 px-6 py-6 rounded-t-3xl relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.4"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-size: 60px 60px;"></div>
                </div>
                
                <div class="relative flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <i class="fas fa-clipboard-check text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white">Request Details</h3>
                    </div>
                    <button onclick="closeModal('viewRequestModal')" 
                        class="text-white/80 hover:text-white hover:bg-white/20 rounded-xl p-2 transition-all duration-200 backdrop-blur-sm">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="p-8 bg-gradient-to-br from-teal-50 to-white" id="requestDetailsContent">
                <!-- Content will be loaded by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Action Confirmation Modal -->
    <div id="actionConfirmationModal"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 hidden overflow-y-auto h-full w-full p-4">
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-500 scale-95 opacity-0" id="actionModalContent">
            <!-- Modal Header with Dynamic Gradient -->
            <div class="px-6 py-6 rounded-t-3xl relative overflow-hidden" id="actionModalHeader">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.4"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-size: 60px 60px;"></div>
                </div>
                
                <div class="relative flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center" id="actionModalIcon">
                            <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white" id="actionModalTitle">Confirm Action</h3>
                    </div>
                    <button onclick="closeModal('actionConfirmationModal')" 
                        class="text-white/80 hover:text-white hover:bg-white/20 rounded-xl p-2 transition-all duration-200 backdrop-blur-sm">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="p-8 bg-gradient-to-br from-gray-50 to-white text-center">
                <!-- Dynamic Icon -->
                <div class="mx-auto w-20 h-20 bg-gradient-to-br from-red-100 to-rose-100 rounded-full flex items-center justify-center mb-6 shadow-lg" id="actionModalIconContainer">
                    <i class="fas fa-exclamation-triangle text-red-600 text-3xl animate-pulse" id="actionModalIconLarge"></i>
                </div>
                
                <!-- Warning Message -->
                <h3 class="text-xl font-bold text-gray-900 mb-3">Are you absolutely sure?</h3>
                <p class="text-gray-600 mb-8 leading-relaxed" id="actionModalMessage">
                    This action <span class="font-semibold text-red-600">cannot be undone</span>. 
                    Please review your decision carefully before proceeding.
                </p>
                
                <!-- Action Buttons -->
                <div class="flex justify-center space-x-4">
                    <button onclick="closeModal('actionConfirmationModal')" 
                        class="px-6 py-3 border-2 border-gray-300 rounded-xl text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Cancel
                    </button>
                    <button type="button" id="confirmActionBtn"
                        class="px-6 py-3 bg-gradient-to-r from-red-600 to-rose-600 text-white rounded-xl text-sm font-semibold hover:from-red-700 hover:to-rose-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                        <i class="fas fa-check mr-2"></i>
                        <span id="confirmBtnText">Confirm</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message Toast -->
    @if(session('success'))
        <div id="successToast"
            class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center justify-between z-50"
            style="min-width: 300px;">
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

            // Modal functions
            window.openModal = function (modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    
                    // Add animation
                    setTimeout(() => {
                        const modalContent = modal.querySelector('.transform');
                        if (modalContent) {
                            modalContent.classList.remove('scale-95', 'opacity-0');
                            modalContent.classList.add('scale-100', 'opacity-100');
                        }
                    }, 10);
                }
            }

            window.closeModal = function (modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    const modalContent = modal.querySelector('.transform');
                    if (modalContent) {
                        modalContent.classList.remove('scale-100', 'opacity-100');
                        modalContent.classList.add('scale-95', 'opacity-0');
                        
                        setTimeout(() => {
                            modal.classList.add('hidden');
                            document.body.style.overflow = '';
                        }, 300);
                    } else {
                        modal.classList.add('hidden');
                        document.body.style.overflow = '';
                    }
                }
            }

            // Function to close view modal and open action confirmation
            window.closeViewModalAndOpenAction = function (action, requestId) {
                // Close the view modal first
                const viewModal = document.getElementById('viewRequestModal');
                if (viewModal) {
                    const modalContent = viewModal.querySelector('.transform');
                    if (modalContent) {
                        modalContent.classList.remove('scale-100', 'opacity-100');
                        modalContent.classList.add('scale-95', 'opacity-0');
                        
                        setTimeout(() => {
                            viewModal.classList.add('hidden');
                            document.body.style.overflow = '';
                            
                            // Open action confirmation modal after view modal is closed
                            setTimeout(() => {
                                showActionConfirmation(requestId, action);
                            }, 100);
                        }, 300);
                    } else {
                        viewModal.classList.add('hidden');
                        document.body.style.overflow = '';
                        
                        // Open action confirmation modal immediately
                        setTimeout(() => {
                            showActionConfirmation(requestId, action);
                        }, 100);
                    }
                } else {
                    // If view modal doesn't exist, open action confirmation directly
                    showActionConfirmation(requestId, action);
                }
            }

            // Close modals when clicking outside or pressing Escape
            document.addEventListener('click', function (event) {
                if (event.target.classList.contains('fixed') &&
                    event.target.classList.contains('inset-0') &&
                    event.target.classList.contains('bg-black')) {
                    closeModal(event.target.id);
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    const openModal = document.querySelector('.fixed:not(.hidden)');
                    if (openModal) {
                        closeModal(openModal.id);
                    }
                }
            });

            // Show request details in modal
            window.showRequestDetails = function (request) {
                const contentDiv = document.getElementById('requestDetailsContent');

                // Format dates
                const requestDate = new Date(request.date);
                const formattedDate = requestDate.toLocaleDateString('en-US', {
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric'
                });

                // Status badge with enhanced styling
                const statusConfig = {
                    'pending': {
                        class: 'bg-amber-100 text-amber-800 border-amber-200',
                        icon: 'fas fa-clock',
                        text: 'Pending'
                    },
                    'approved': {
                        class: 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        icon: 'fas fa-check-circle',
                        text: 'Approved'
                    },
                    'rejected': {
                        class: 'bg-red-100 text-red-800 border-red-200',
                        icon: 'fas fa-times-circle',
                        text: 'Rejected'
                    }
                };
                const status = statusConfig[request.status] || statusConfig['pending'];

                // Type icon
                const typeConfig = {
                    'room': {
                        icon: 'fas fa-door-open',
                        color: 'text-blue-600',
                        bg: 'bg-blue-100'
                    },
                    'equipment': {
                        icon: 'fas fa-laptop',
                        color: 'text-purple-600',
                        bg: 'bg-purple-100'
                    }
                };
                const type = typeConfig[request.type] || typeConfig['room'];

                // Build the enhanced content
                contentDiv.innerHTML = `
                    <div class="space-y-6">
                        <!-- Header with Title and Status -->
                        <div class="flex items-center justify-between pb-6 border-b border-gray-200">
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">${request.title}</h3>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium border ${status.class}">
                                        <i class="${status.icon} mr-2"></i>
                                        ${status.text}
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium ${type.bg} ${type.color}">
                                        <i class="${type.icon} mr-2"></i>
                                        ${request.type.charAt(0).toUpperCase() + request.type.slice(1)}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Request Information Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-4 border border-blue-100 hover:shadow-md transition-all duration-200">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-hashtag text-white text-sm"></i>
                                    </div>
                                    <label class="text-xs font-bold text-blue-600 uppercase tracking-wider">Request ID</label>
                                </div>
                                <p class="text-gray-900 font-semibold text-lg">${request.id}</p>
                            </div>
                            
                            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl p-4 border border-emerald-100 hover:shadow-md transition-all duration-200">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                    <label class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Requested By</label>
                                </div>
                                <p class="text-gray-900 font-semibold text-lg">${request.requested_by}</p>
                            </div>
                            
                            <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl p-4 border border-amber-100 hover:shadow-md transition-all duration-200">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-calendar text-white text-sm"></i>
                                    </div>
                                    <label class="text-xs font-bold text-amber-600 uppercase tracking-wider">Date</label>
                                </div>
                                <p class="text-gray-900 font-semibold text-lg">${formattedDate}</p>
                            </div>
                            
                            <div class="bg-gradient-to-r from-rose-50 to-pink-50 rounded-2xl p-4 border border-rose-100 hover:shadow-md transition-all duration-200">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-8 h-8 bg-rose-500 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-hourglass-half text-white text-sm"></i>
                                    </div>
                                    <label class="text-xs font-bold text-rose-600 uppercase tracking-wider">Lead Time</label>
                                </div>
                                <p class="text-gray-900 font-semibold text-lg">${request.lead_time ? request.lead_time + ' days' : 'Not specified'}</p>
                            </div>
                        </div>
                        
                        <!-- Description Section -->
                        <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-2xl p-6 border border-gray-200">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-8 h-8 bg-gray-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-align-left text-white text-sm"></i>
                                </div>
                                <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">Description</label>
                            </div>
                            <p class="text-gray-900 font-medium whitespace-pre-line">${request.description || 'No description provided'}</p>
                        </div>
                    </div>

                    <!-- Enhanced Action Buttons -->
                    <div class="mt-8 flex justify-end space-x-4">
                        ${request.status === 'pending' ? `
                        <button type="button" onclick="closeViewModalAndOpenAction('approve', '${request.id}')" 
                            class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl text-sm font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            Approve
                        </button>
                        <button type="button" onclick="closeViewModalAndOpenAction('reject', '${request.id}')" 
                            class="px-6 py-3 bg-gradient-to-r from-red-600 to-rose-600 text-white rounded-xl text-sm font-semibold hover:from-red-700 hover:to-rose-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                            <i class="fas fa-times-circle mr-2"></i>
                            Reject
                        </button>` : ''}
                        <button type="button" onclick="closeModal('viewRequestModal')" 
                            class="px-6 py-3 border-2 border-gray-300 rounded-xl text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm">
                            <i class="fas fa-times mr-2"></i>
                            Close
                        </button>
                    </div>
                `;

                openModal('viewRequestModal');
            }

            // Action confirmation functionality
            let currentRequestId = '';
            let currentAction = '';
            let currentActionUrl = '';

            window.showActionConfirmation = function (requestId, action) {
                currentRequestId = requestId;
                currentAction = action;
                currentActionUrl = action === 'approve'
                    ? `{{ url('approval') }}/${requestId}/approve`
                    : `{{ url('approval') }}/${requestId}/reject`;

                const confirmBtn = document.getElementById('confirmActionBtn');
                const modalTitle = document.getElementById('actionModalTitle');
                const modalMessage = document.getElementById('actionModalMessage');
                const modalHeader = document.getElementById('actionModalHeader');
                const modalIcon = document.getElementById('actionModalIcon');
                const modalIconLarge = document.getElementById('actionModalIconLarge');
                const modalIconContainer = document.getElementById('actionModalIconContainer');
                const confirmBtnText = document.getElementById('confirmBtnText');

                // Dynamic styling based on action
                if (action === 'approve') {
                    modalTitle.textContent = 'Approve Request';
                    modalMessage.textContent = `Are you sure you want to approve this request? This action cannot be undone.`;
                    confirmBtnText.textContent = 'Approve Request';
                    
                    // Green theme for approve
                    modalHeader.className = 'px-6 py-6 rounded-t-3xl relative overflow-hidden bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600';
                    modalIcon.className = 'w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center';
                    modalIcon.innerHTML = '<i class="fas fa-check-circle text-white text-xl"></i>';
                    modalIconContainer.className = 'mx-auto w-20 h-20 bg-gradient-to-br from-green-100 to-emerald-100 rounded-full flex items-center justify-center mb-6 shadow-lg';
                    modalIconLarge.className = 'fas fa-check-circle text-green-600 text-3xl animate-pulse';
                    confirmBtn.className = 'px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl text-sm font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center';
                } else {
                    modalTitle.textContent = 'Reject Request';
                    modalMessage.textContent = `Are you sure you want to reject this request? This action cannot be undone.`;
                    confirmBtnText.textContent = 'Reject Request';
                    
                    // Red theme for reject
                    modalHeader.className = 'px-6 py-6 rounded-t-3xl relative overflow-hidden bg-gradient-to-r from-red-600 via-rose-600 to-pink-600';
                    modalIcon.className = 'w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center';
                    modalIcon.innerHTML = '<i class="fas fa-times-circle text-white text-xl"></i>';
                    modalIconContainer.className = 'mx-auto w-20 h-20 bg-gradient-to-br from-red-100 to-rose-100 rounded-full flex items-center justify-center mb-6 shadow-lg';
                    modalIconLarge.className = 'fas fa-times-circle text-red-600 text-3xl animate-pulse';
                    confirmBtn.className = 'px-6 py-3 bg-gradient-to-r from-red-600 to-rose-600 text-white rounded-xl text-sm font-semibold hover:from-red-700 hover:to-rose-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center';
                }

                confirmBtn.onclick = handleActionRequest;
                openModal('actionConfirmationModal');
            }

            window.handleActionRequest = async function () {
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
            document.addEventListener('DOMContentLoaded', function () {
                // Approve button confirmation
                document.querySelectorAll('button[onclick*="showActionConfirmation"][onclick*="approve"]').forEach(button => {
                    button.addEventListener('click', function (e) {
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
                    button.addEventListener('click', async function (e) {
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

        // Tab Switching Logic
        }

        // Search Functionality
        const searchInput = document.getElementById('requestSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                // Trigger re-filtering based on current tab and new search term
                // We'll just call switchTab with the currently active tab name
                // To do this, we need to know the active tab.
                const activeBtn = document.querySelector('button[id^="tab-"].shadow-sm.bg-white'); // Our active tab has these unique classes
                if (activeBtn) {
                    const tabId = activeBtn.id.replace('tab-', '');
                    switchTab(tabId);
                }
            });
        }

        // Updated Tab Switching Logic (Integrated with Search)
        window.switchTab = function (tabName) {
            // 1. Update Tabs Styling
            const tabs = ['pending', 'my-requests', 'history'];
            tabs.forEach(t => {
                const btn = document.getElementById('tab-' + t);
                if (btn) {
                    if (t === tabName) {
                        // Active Style
                        btn.className = "px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm bg-white text-gray-900 transition-all duration-200 flex items-center gap-2";
                        if (t === 'pending') {
                            const badge = btn.querySelector('span');
                            if (badge) badge.className = "bg-brand-primary text-white text-[10px] px-1.5 py-0.5 rounded-full";
                        }
                    } else {
                        // Inactive Style
                        btn.className = "px-5 py-2.5 rounded-lg text-sm font-medium text-gray-500 hover:text-gray-900 hover:bg-white/50 transition-all duration-200 flex items-center gap-2";
                        if (t === 'pending') {
                            const badge = btn.querySelector('span');
                            if (badge) badge.className = "bg-gray-200 text-gray-600 text-[10px] px-1.5 py-0.5 rounded-full";
                        }
                    }
                }
            });

            // 2. Filter Table Rows
            const rows = document.querySelectorAll('tbody tr');
            const currentUserName = "{{ $user->name }}";
            const searchTerm = document.getElementById('requestSearch')?.value.toLowerCase() || '';
            let visibleCount = 0;

            rows.forEach(row => {
                // Skip empty message rows if they exist
                if (row.querySelector('td[colspan]')) return;

                const title = row.querySelector('td:nth-child(1) .text-sm.font-medium')?.innerText.trim().toLowerCase();
                const id = row.querySelector('td:nth-child(1) .text-xs')?.innerText.trim().toLowerCase();
                const requestedBy = row.querySelector('td:nth-child(2)')?.innerText.trim();
                const normalizedRequestedBy = requestedBy.toLowerCase();
                const statusBadge = row.querySelector('td:nth-child(4) .status-badge');

                if (!statusBadge || !requestedBy) return;

                const status = statusBadge.innerText.trim().toLowerCase();

                // Tab Filter
                let matchesTab = false;
                if (tabName === 'pending') {
                    matchesTab = status === 'pending';
                } else if (tabName === 'my-requests') {
                    matchesTab = requestedBy === currentUserName;
                } else if (tabName === 'history') {
                    matchesTab = status === 'approved' || status === 'rejected';
                }

                // Search Filter
                // Default to match if no search term, otherwise check columns
                let matchesSearch = true;
                if (searchTerm) {
                    matchesSearch = (title && title.includes(searchTerm)) ||
                        (id && id.includes(searchTerm)) ||
                        (normalizedRequestedBy.includes(searchTerm));
                }

                if (matchesTab && matchesSearch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Handle "No records" state
            const noRecordsRow = document.getElementById('no-records-row');
            if (visibleCount === 0) {
                if (!noRecordsRow) {
                    const tbody = document.querySelector('tbody');
                    const tr = document.createElement('tr');
                    tr.id = 'no-records-row';
                    tr.innerHTML = `
                         <td colspan="5" class="px-6 py-8 text-center">
                            <div class="text-gray-400">
                                <i class='bx bx-search text-4xl mb-2'></i>
                                <p class="text-sm">No matching requests found.</p>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                } else {
                    noRecordsRow.style.display = '';
                }
            } else {
                if (noRecordsRow) noRecordsRow.style.display = 'none';
            }
        }
    </script>
</body>

</html>