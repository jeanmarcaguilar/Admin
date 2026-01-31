@php
// Get the authenticated user
$user = auth()->user();
// Get calendar bookings from database (passed from route)
$calendarBookings = $calendarBookings ?? [];
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
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

        .calendar-day {
            transition: all 0.2s ease;
        }

        .calendar-day:hover {
            background-color: #f0fdf4;
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
            color: #991b1b;
        }

        .calendar-event {
            transition: all 0.2s ease;
        }

        .calendar-event:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
                    <a href="{{ route('scheduling.calendar') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
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
                <h1 class="text-lg font-bold text-gray-800 hidden md:block">Scheduling & Calendar Integrations</h1>
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
                            <h1 class="text-2xl font-bold text-gray-900">Scheduling & Calendar Integrations</h1>
                            <p class="text-gray-600 mt-1">Manage room bookings, calendar events, and integrations</p>
                        </div>
                        <div class="mt-4 md:mt-0 flex space-x-3">
                            <a href="{{ route('room-equipment') }}" class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors font-medium flex items-center">
                                <i class="fas fa-plus mr-2"></i> New Booking
                            </a>
                            <button id="exportBtn" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                <i class="fas fa-download mr-2"></i> Export
                            </button>
                            <button id="printBtn" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                <i class="fas fa-print mr-2"></i> Print
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Calendar and Overview Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Calendar View (2/3 width) -->
                    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-semibold text-lg text-gray-900">Calendar View</h3>
                            <div class="flex items-center gap-2">
                                <button id="todayBtn" class="px-3 py-1.5 text-sm font-medium bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors">
                                    Today
                                </button>
                                <button id="prevMonthBtn" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-chevron-left text-sm"></i>
                                </button>
                                <span id="monthLabel" class="text-sm font-semibold text-gray-700 min-w-[120px] text-center">
                                    {{ now()->format('F Y') }}
                                </span>
                                <button id="nextMonthBtn" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-chevron-right text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Calendar Grid -->
                        <div class="grid grid-cols-7 gap-2 mb-3">
                            <div class="text-center text-xs font-medium text-gray-500 py-2">Sun</div>
                            <div class="text-center text-xs font-medium text-gray-500 py-2">Mon</div>
                            <div class="text-center text-xs font-medium text-gray-500 py-2">Tue</div>
                            <div class="text-center text-xs font-medium text-gray-500 py-2">Wed</div>
                            <div class="text-center text-xs font-medium text-gray-500 py-2">Thu</div>
                            <div class="text-center text-xs font-medium text-gray-500 py-2">Fri</div>
                            <div class="text-center text-xs font-medium text-gray-500 py-2">Sat</div>
                        </div>

                        <div id="calendarGrid" class="grid grid-cols-7 gap-2"></div>

                        <!-- Calendar Legend -->
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <div class="flex items-center gap-4 text-xs text-gray-600">
                                <span class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-green-100 border border-green-300"></span>
                                    <span>Approved</span>
                                </span>
                                <span class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-yellow-100 border border-yellow-300"></span>
                                    <span>Pending</span>
                                </span>
                                <span class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-red-100 border border-red-300"></span>
                                    <span>Rejected</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Panels (1/3 width) -->
                    <div class="space-y-6">
                        <!-- Quick Actions -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="font-semibold text-lg text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <button id="exportCalendarBtn" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                                    <i class="fas fa-download"></i> Export Calendar
                                </button>
                                @if (Route::has('calendar.clear'))
                                    <form method="POST" action="{{ route('calendar.clear') }}" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors text-sm font-medium">
                                            <i class="fas fa-trash"></i> Clear All
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Upcoming Events -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-lg text-gray-900">Upcoming Events</h3>
                                <span id="upcomingCount" class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                    {{ count($calendarBookings) }}
                                </span>
                            </div>
                            
                            <div id="upcomingEventsList" class="max-h-80 overflow-y-auto custom-scrollbar">
                                @if (!empty($calendarBookings))
                                    <div class="space-y-3">
                                        @foreach ($calendarBookings as $booking)
                                            @php
                                                $date = isset($booking['date']) ? \Carbon\Carbon::parse($booking['date']) : null;
                                                $day = $date ? $date->format('d') : '--';
                                                $monthShort = $date ? $date->format('M') : '';
                                                $time = isset($booking['start_time']) && $booking['start_time']
                                                    ? (\Carbon\Carbon::createFromFormat('H:i', $booking['start_time'])->format('g:i A'))
                                                    : '';
                                                $title = $booking['name'] ?? ($booking['title'] ?? 'Booking');
                                                $status = strtolower($booking['status'] ?? 'pending');
                                                $statusClass = [
                                                    'pending' => 'status-pending',
                                                    'approved' => 'status-approved',
                                                    'rejected' => 'status-rejected',
                                                ][$status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <div class="calendar-event p-3 rounded-lg border border-gray-100 hover:border-green-200 cursor-pointer" onclick="showEventDetails({{ json_encode($booking) }})">
                                                <div class="flex items-start space-x-3">
                                                    <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-lg bg-green-50 text-green-600 font-bold">
                                                        {{ $day }}
                                                    </div>
                                                    <div class="flex-grow">
                                                        <div class="flex items-center justify-between">
                                                            <span class="font-medium text-gray-900 text-sm">{{ Str::limit($title, 20) }}</span>
                                                            <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $statusClass }}">
                                                                {{ ucfirst($status) }}
                                                            </span>
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-1">{{ $day }} {{ $monthShort }} @if($time) · {{ $time }} @endif</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-6">
                                        <i class='bx bx-calendar text-3xl text-gray-300 mb-2'></i>
                                        <p class="text-sm text-gray-500">No upcoming events</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                                            </div>
                </div>

                            </div>
        </main>
    </div>

    <!-- Event Details Modal -->
    <div id="eventDetailsModal" class="modal hidden" aria-modal="true" role="dialog">
        <div class="bg-white rounded-lg shadow-lg w-[400px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Event Details</h3>
                <button onclick="closeEventDetails()" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="eventDetailsContent" class="p-6">
                <!-- Content will be loaded by JavaScript -->
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

            // Open "Facilities Management" dropdown by default since we're on Scheduling & Calendar page
            const facilitiesBtn = document.getElementById('facilities-management-btn');
            const facilitiesSubmenu = document.getElementById('facilities-submenu');
            const facilitiesArrow = document.getElementById('facilities-arrow');
            
            if (facilitiesSubmenu && !facilitiesSubmenu.classList.contains('hidden')) {
                facilitiesSubmenu.classList.remove('hidden');
                if (facilitiesArrow) facilitiesArrow.classList.add('rotate-180');
            }

            // Calendar functionality
            const calendarGrid = document.getElementById('calendarGrid');
            const monthLabel = document.getElementById('monthLabel');
            const prevMonthBtn = document.getElementById('prevMonthBtn');
            const nextMonthBtn = document.getElementById('nextMonthBtn');
            const todayBtn = document.getElementById('todayBtn');
            const exportCalendarBtn = document.getElementById('exportCalendarBtn');
            const lockAllBtn = document.getElementById('lockAllBtn');

            let currentDate = new Date();
            const sessionBookings = @json($calendarBookings);

            function daysInMonth(year, monthIndex) {
                return new Date(year, monthIndex + 1, 0).getDate();
            }

            function renderCalendar() {
                console.log('Rendering calendar...');
                const year = currentDate.getFullYear();
                const monthIndex = currentDate.getMonth();
                const today = new Date();

                // Update month label
                const monthName = currentDate.toLocaleString('default', { month: 'long' });
                monthLabel.textContent = `${monthName} ${year}`;

                // Prepare grid
                calendarGrid.innerHTML = '';
                console.log('Calendar grid cleared');

                const firstDayOfMonth = new Date(year, monthIndex, 1);
                const startWeekday = firstDayOfMonth.getDay();
                const totalDays = daysInMonth(year, monthIndex);
                const totalCells = 42;

                // Leading empty cells
                for (let i = 0; i < startWeekday; i++) {
                    const cell = document.createElement('div');
                    cell.className = 'h-24 border border-gray-200 rounded-lg p-2 bg-gray-50';
                    calendarGrid.appendChild(cell);
                }

                // Day cells
                console.log(`Creating ${totalDays} day cells`);
                for (let day = 1; day <= totalDays; day++) {
                    const cell = document.createElement('div');
                    const isToday = today.getFullYear() === year && today.getMonth() === monthIndex && today.getDate() === day;
                    cell.className = `h-24 border border-gray-200 rounded-lg p-2 calendar-day ${
                        isToday ? 'ring-2 ring-brand-primary bg-brand-primary/5' : 'bg-white'
                    }`;

                    const badge = document.createElement('span');
                    badge.className = `text-sm font-medium ${
                        isToday ? 'text-brand-primary' : 'text-gray-700'
                    }`;
                    badge.textContent = day;
                    cell.appendChild(badge);

                    // Render bookings for this day
                    const events = (sessionBookings || []).filter(b => {
                        if (!b.date) return false;
                        const d = new Date(b.date);
                        return d.getFullYear() === year && d.getMonth() === monthIndex && d.getDate() === day;
                    });

                    if (events.length) {
                        const container = document.createElement('div');
                        container.className = 'mt-1 space-y-1';
                        events.forEach(ev => {
                            const pill = document.createElement('div');
                            const status = (ev.status || '').toLowerCase();
                            let color = '';
                            if (status === 'approved') color = 'bg-green-100 text-green-800 border border-green-200';
                            else if (status === 'rejected') color = 'bg-red-100 text-red-800 border border-red-200';
                            else if (status === 'pending') color = 'bg-yellow-100 text-yellow-800 border border-yellow-200';
                            else color = 'bg-blue-100 text-blue-800 border border-blue-200';

                            const start = ev.start_time ? ev.start_time.substring(0,5) : '';
                            const timeStr = start ? ` (${start})` : '';

                            pill.className = `text-xs px-2 py-1 rounded truncate ${color} cursor-pointer hover:opacity-80`;
                            pill.title = (ev.name || ev.title || 'Booking') + (status ? ` [${status}]` : '');
                            pill.textContent = `${ev.name || ev.title || 'Booking'}${timeStr}`;
                            pill.onclick = () => showEventDetails(ev);
                            container.appendChild(pill);
                        });
                        cell.appendChild(container);
                    }

                    calendarGrid.appendChild(cell);
                }

                // Trailing empty cells
                const usedCells = startWeekday + totalDays;
                for (let i = usedCells; i < totalCells; i++) {
                    const cell = document.createElement('div');
                    cell.className = 'h-24 border border-gray-200 rounded-lg p-2 bg-gray-50';
                    calendarGrid.appendChild(cell);
                }
            }

            // Navigation buttons
            prevMonthBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar();
            });

            nextMonthBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar();
            });

            todayBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                currentDate = new Date();
                renderCalendar();
            });

            // Export functionality
            exportCalendarBtn?.addEventListener('click', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Export Started',
                    text: 'Calendar export has been queued. You will receive an email when ready.',
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            // Print functionality
            const printBtn = document.getElementById('printBtn');
            if (printBtn) {
                printBtn.addEventListener('click', function() {
                    window.print();
                });
            }

            // Initial render
            renderCalendar();

            // Event details modal functions
            window.showEventDetails = function(event) {
                const modal = document.getElementById('eventDetailsModal');
                const content = document.getElementById('eventDetailsContent');
                
                const status = (event.status || '').toLowerCase();
                const statusClass = {
                    'pending': 'status-pending',
                    'approved': 'status-approved',
                    'rejected': 'status-rejected',
                }[status] || 'bg-gray-100 text-gray-800';
                
                const date = event.date ? new Date(event.date).toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                }) : 'No date specified';
                
                const time = event.start_time && event.end_time ? 
                    `${event.start_time} - ${event.end_time}` : 
                    event.start_time || 'All day';
                
                content.innerHTML = `
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Title</h4>
                            <p class="text-base text-gray-900 font-medium">${event.name || event.title || 'Booking'}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Status</h4>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">
                                ${status.charAt(0).toUpperCase() + status.slice(1)}
                            </span>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Date</h4>
                            <p class="text-base text-gray-900">${date}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Time</h4>
                            <p class="text-base text-gray-900">${time}</p>
                        </div>
                        ${event.room ? `
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Room</h4>
                            <p class="text-base text-gray-900">${event.room}</p>
                        </div>
                        ` : ''}
                        ${event.equipment ? `
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Equipment</h4>
                            <p class="text-base text-gray-900">${event.equipment}</p>
                        </div>
                        ` : ''}
                        ${event.purpose ? `
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Purpose</h4>
                            <p class="text-base text-gray-900">${event.purpose}</p>
                        </div>
                        ` : ''}
                    </div>
                `;
                
                modal.classList.remove('hidden');
                modal.classList.add('active');
                modal.style.display = 'flex';
            };

            window.closeEventDetails = function() {
                const modal = document.getElementById('eventDetailsModal');
                modal.classList.add('hidden');
                modal.classList.remove('active');
                modal.style.display = 'none';
            };

                    confirmButtonText: 'Verify & Unlock',
                    confirmButtonColor: '#059669',
                    showLoaderOnConfirm: true,
                    preConfirm: (otp) => {
                        if (!otp || otp.length !== 6) {
                            Swal.showValidationMessage('Please enter a valid 6-digit code');
                            return false;
                        }
                        return true;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Reservations Unlocked',
                            text: 'All reservations have been successfully unlocked and normal access restored.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Update button
                        const lockBtn = document.getElementById('lockAllBtn');
                        if (lockBtn) {
                            lockBtn.innerHTML = '<i class="fas fa-lock"></i> Lock All Reservations';
                            lockBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                            lockBtn.classList.add('bg-amber-600', 'hover:bg-amber-700');
                            lockBtn.setAttribute('onclick', 'lockAllReservations()');
                            localStorage.setItem('reservationsLocked', 'false');
                        }
                    }
                });
            };

            // Check and restore lock state on page load
            function restoreLockState() {
                const isLocked = localStorage.getItem('reservationsLocked') === 'true';
                const lockBtn = document.getElementById('lockAllBtn');
                if (lockBtn) {
                    if (isLocked) {
                        lockBtn.innerHTML = '<i class="fas fa-lock-open"></i> Unlock Reservations';
                        lockBtn.classList.remove('bg-amber-600', 'hover:bg-amber-700');
                        lockBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                        lockBtn.setAttribute('onclick', 'unlockAllReservations()');
                    }
                }
            }

            // Restore on load
            restoreLockState();

            // Flash success from server
            const flashSuccess = @json(session('success'));
            if (flashSuccess) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: flashSuccess,
                    confirmButtonColor: '#059669'
                });
            }

            // Close modals when clicking outside
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.add('hidden');
                        this.classList.remove('active');
                        this.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>