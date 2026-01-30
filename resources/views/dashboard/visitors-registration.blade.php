@php
// Get the authenticated user
$user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Visitors Registration | Microfinance HR3</title>
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

        .table-row {
            transition: all 0.2s ease-in-out;
        }

        .table-row:hover {
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
                <svg id="visitor-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="visitor-submenu" class="submenu mt-1">
                <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                    <a href="{{ route('visitors.registration') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
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
                <h1 class="text-lg font-bold text-gray-800 hidden md:block">Visitors Registration</h1>
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
                            <h1 class="text-2xl font-bold text-gray-900">Visitor Management</h1>
                            <p class="text-gray-600 mt-1">Register, track, and manage visitor activity</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <button id="addVisitorBtn" class="inline-flex items-center bg-brand-primary hover:bg-brand-primary-hover text-white font-medium rounded-lg px-4 py-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-primary">
                                <i class="fas fa-user-plus mr-2"></i> Register Visitor
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search -->
                <div class="mb-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="relative flex-1 max-w-3xl">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="searchVisitors" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-primary focus:border-brand-primary block w-full pl-10 p-2.5" placeholder="Search visitors, companies, hosts...">
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Today</p>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['total_today'] ?? 0 }}</h3>
                            </div>
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-calendar-day text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Checked In</p>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['checked_in'] ?? 0 }}</h3>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-user-check text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Scheduled Today</p>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['scheduled_today'] ?? 0 }}</h3>
                            </div>
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Checked Out</p>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['checked_out'] ?? 0 }}</h3>
                            </div>
                            <div class="p-3 rounded-full bg-gray-100 text-gray-700">
                                <i class="fas fa-door-open text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Grid -->
                <section class="grid grid-cols-1 gap-6">
                    <!-- Table -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <button class="text-sm font-medium text-brand-primary px-3 py-1.5 rounded-lg bg-green-50 hover:bg-green-100 transition-colors">Today</button>
                                <button class="text-sm text-gray-600 px-3 py-1.5 rounded-lg hover:bg-gray-50 transition-colors">Scheduled</button>
                                <button class="text-sm text-gray-600 px-3 py-1.5 rounded-lg hover:bg-gray-50 transition-colors">All</button>
                            </div>
                            <div id="resultsCount" class="text-xs text-gray-500">0 results</div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visitor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Host</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="visitorsTbody" class="bg-white divide-y divide-gray-200">
                                    @forelse(($visitors ?? []) as $v)
                                        <tr class="table-row">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $v['name'] ?? 'Visitor' }}</div>
                                                <div class="text-xs text-gray-500">ID: {{ $v['id'] ?? '' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $visitorType = $v['visitor_type'] ?? '';
                                                    $typeClasses = [
                                                        'personal' => 'bg-purple-100 text-purple-800',
                                                        'professional' => 'bg-blue-100 text-blue-800',
                                                        'business_partner' => 'bg-green-100 text-green-800',
                                                        'vip' => 'bg-violet-100 text-violet-800',
                                                        'regular' => 'bg-gray-100 text-gray-800'
                                                    ];
                                                    $typeLabel = ucfirst(str_replace('_', ' ', $visitorType));
                                                @endphp
                                                <span class="px-2 py-1 text-xs font-medium rounded-full inline-flex items-center {{ $typeClasses[$visitorType] ?? 'bg-gray-100 text-gray-800' }}">
                                                    @if($visitorType === 'vip')
                                                        <i class="fas fa-crown text-yellow-500 mr-1 text-xs"></i>
                                                    @endif
                                                    {{ $typeLabel }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $v['company'] ?? '—' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $v['host'] ?? '—' }}</div>
                                                <div class="text-xs text-gray-500">{{ $v['host_department'] ?? '' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $__rawTime = $v['check_in_time'] ?? '';
                                                    $__fmtTime = $__rawTime;
                                                    if ($__rawTime) {
                                                        try { $__fmtTime = \Carbon\Carbon::createFromFormat('H:i', $__rawTime)->format('g:i A'); }
                                                        catch (\Exception $e) {
                                                            try { $__fmtTime = \Carbon\Carbon::createFromFormat('H:i:s', $__rawTime)->format('g:i A'); }
                                                            catch (\Exception $e2) { /* leave as-is */ }
                                                        }
                                                    }
                                                @endphp
                                                <div class="text-sm text-gray-900">{{ $__fmtTime }}</div>
                                                <div class="text-xs text-gray-500">{{ $v['check_in_date'] ?? '' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php $st = strtolower($v['status'] ?? 'scheduled'); @endphp
                                                <span class="px-2 py-1 text-xs font-medium rounded-full inline-flex items-center {{ $st === 'checked_in' ? 'bg-green-100 text-green-800' : ($st==='checked_out' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                    {{ ucfirst($st) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="#" class="visitorViewBtn text-brand-primary hover:text-brand-primary-hover mr-3" data-id="{{ $v['id'] ?? '' }}" data-tooltip="View"><i class="fas fa-eye"></i></a>
                                                <a href="#" class="visitorEditBtn text-blue-600 hover:text-blue-900 mr-3" data-id="{{ $v['id'] ?? '' }}" data-tooltip="Edit"><i class="fas fa-edit"></i></a>
                                                @if($st !== 'checked_out' && $st !== 'checked_in')
                                                    <a href="#" class="visitorCheckInBtn text-green-600 hover:text-green-800 mr-3" data-id="{{ $v['id'] ?? '' }}" data-tooltip="Check In"><i class="fas fa-sign-in-alt"></i></a>
                                                @endif
                                                <a href="#" class="visitorDeleteBtn text-red-600 hover:text-red-900" data-id="{{ $v['id'] ?? '' }}" data-tooltip="Delete"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                                No visitors registered yet.
                                                <div class="mt-3">
                                                    <button type="button" onclick="document.getElementById('addVisitorBtn').click()" class="inline-flex items-center bg-brand-primary hover:bg-brand-primary-hover text-white text-xs font-medium rounded-lg px-3 py-2 shadow-sm">
                                                        <i class="fas fa-user-plus mr-2"></i> Register your first visitor
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <!-- Add Visitor Modal -->
    <div id="addVisitorModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="add-visitor-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-[480px] max-w-full mx-4 max-h-[90vh] overflow-y-auto fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
                <h3 id="add-visitor-modal-title" class="font-semibold text-sm text-gray-900">Register New Visitor</h3>
                <button id="closeAddVisitorModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 pt-5 pb-6">
                <form id="addVisitorForm" class="space-y-4 text-sm text-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <h4 class="text-sm font-semibold text-gray-900 mb-2">Visitor Information</h4>
                        </div>
                        <div>
                            <label for="firstName" class="block mb-1 font-medium text-xs">First Name *</label>
                            <input type="text" id="firstName" name="firstName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm" required>
                        </div>
                        <div>
                            <label for="lastName" class="block mb-1 font-medium text-xs">Last Name *</label>
                            <input type="text" id="lastName" name="lastName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm" required>
                        </div>
                        <div class="col-span-2">
                            <label for="email" class="block mb-1 font-medium text-xs">Email Address</label>
                            <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label for="phone" class="block mb-1 font-medium text-xs">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm" required>
                        </div>
                        <div>
                            <label for="company" class="block mb-1 font-medium text-xs">Company *</label>
                            <input type="text" id="company" name="company" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm" required>
                        </div>
                        <div class="col-span-2">
                            <label for="visitorType" class="block mb-1 font-medium text-xs">Visitor Type *</label>
                            <select id="visitorType" name="visitorType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm" required>
                                <option value="">Select visitor type</option>
                                <option value="personal">Personal</option>
                                <option value="professional">Professional</option>
                                <option value="business_partner">Business Partner</option>
                                <option value="vip">VIP</option>
                                <option value="regular">Regular</option>
                            </select>
                        </div>
                        <div class="col-span-2 border-t border-gray-200 pt-4">
                            <h4 class="text-sm font-semibold text-gray-900 mb-2">Visit Details</h4>
                        </div>
                        <div>
                            <label for="hostName" class="block mb-1 font-medium text-xs">Host Name *</label>
                            <input type="text" id="hostName" name="hostName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm" placeholder="e.g., Sarah Johnson" required>
                        </div>
                        <div>
                            <label for="hostDepartment" class="block mb-1 font-medium text-xs">Host Department</label>
                            <input type="text" id="hostDepartment" name="hostDepartment" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm" placeholder="e.g., Procurement">
                        </div>
                        <div>
                            <label for="purpose" class="block mb-1 font-medium text-xs">Purpose of Visit *</label>
                            <select id="purpose" name="purpose" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm" required>
                                <option value="">Select purpose</option>
                                <option value="meeting">Meeting</option>
                                <option value="delivery">Delivery</option>
                                <option value="interview">Interview</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label for="checkInDate" class="block mb-1 font-medium text-xs">Check-In Date *</label>
                            <input type="date" id="checkInDate" name="checkInDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm" required>
                        </div>
                        <div>
                            <label for="checkInTime" class="block mb-1 font-medium text-xs">Check-In Time *</label>
                            <input type="time" id="checkInTime" name="checkInTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm" required>
                        </div>
                        <div class="col-span-2">
                            <label for="notes" class="block mb-1 font-medium text-xs">Additional Notes</label>
                            <textarea id="notes" name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm"></textarea>
                        </div>
                        <div class="col-span-2">
                            <label class="flex items-center space-x-2 text-xs">
                                <input id="sendEmailNotification" name="sendEmailNotification" type="checkbox" checked class="w-4 h-4 text-brand-primary focus:ring-brand-primary border-gray-300 rounded">
                                <span>Send email notification to host</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 pt-2">
                        <button type="button" id="cancelAddVisitor" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover rounded-lg transition-colors">Register Visitor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Visitor Modal -->
    <div id="viewVisitorModal" class="modal hidden" aria-modal="true" role="dialog">
        <div class="bg-white rounded-lg shadow-lg w-[420px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
                <h3 class="font-semibold text-sm text-gray-900">View Visitor</h3>
                <button id="closeViewVisitor" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 pt-5 pb-6">
                <div class="space-y-3 text-sm text-gray-800">
                    <div class="grid grid-cols-2 gap-2">
                        <span class="font-medium text-gray-700">ID:</span>
                        <span id="vvId" class="text-gray-900"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <span class="font-medium text-gray-700">Name:</span>
                        <span id="vvName" class="text-gray-900"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <span class="font-medium text-gray-700">Company:</span>
                        <span id="vvCompany" class="text-gray-900"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <span class="font-medium text-gray-700">Type:</span>
                        <span id="vvType" class="text-gray-900"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <span class="font-medium text-gray-700">Host:</span>
                        <span id="vvHost" class="text-gray-900"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <span class="font-medium text-gray-700">Department:</span>
                        <span id="vvDept" class="text-gray-900"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <span class="font-medium text-gray-700">Date:</span>
                        <span id="vvDate" class="text-gray-900"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <span class="font-medium text-gray-700">Time:</span>
                        <span id="vvTime" class="text-gray-900"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <span class="font-medium text-gray-700">Status:</span>
                        <span id="vvStatus" class="text-gray-900"></span>
                    </div>
                </div>
                <div class="flex justify-end pt-4">
                    <button type="button" id="closeViewVisitor2" class="px-4 py-2 text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover rounded-lg transition-colors">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Visitor Modal -->
    <div id="editVisitorModal" class="modal hidden" aria-modal="true" role="dialog">
        <div class="bg-white rounded-lg shadow-lg w-[460px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
                <h3 class="font-semibold text-sm text-gray-900">Edit Visitor</h3>
                <button id="closeEditVisitor" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 pt-5 pb-6">
                <form id="editVisitorForm" class="space-y-4 text-sm text-gray-700">
                    <input type="hidden" id="evId" />
                    <div>
                        <label class="block mb-1 font-medium text-xs">Company</label>
                        <input type="text" id="evCompany" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block mb-1 font-medium text-xs">Type</label>
                            <select id="evType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                                <option value="">—</option>
                                <option value="client">Client</option>
                                <option value="vendor">Vendor</option>
                                <option value="contractor">Contractor</option>
                                <option value="guest">Guest</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-xs">Purpose</label>
                            <select id="evPurpose" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                                <option value="">—</option>
                                <option value="meeting">Meeting</option>
                                <option value="delivery">Delivery</option>
                                <option value="interview">Interview</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block mb-1 font-medium text-xs">Date</label>
                            <input type="date" id="evDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-xs">Time</label>
                            <input type="time" id="evTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-xs">Status</label>
                        <select id="evStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                            <option value="scheduled">Scheduled</option>
                            <option value="checked_in">Checked In</option>
                            <option value="checked_out">Checked Out</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3 pt-2">
                        <button type="button" id="cancelEditVisitor" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover rounded-lg transition-colors">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Visitor Modal -->
    <div id="deleteVisitorModal" class="modal hidden" aria-modal="true" role="dialog">
        <div class="bg-white rounded-lg shadow-lg w-[420px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
                <h3 class="font-semibold text-sm text-gray-900">Delete Visitor</h3>
                <button id="closeDeleteVisitor" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 pt-5 pb-6">
                <p class="text-sm text-gray-700 mb-3">Are you sure you want to delete visitor <span id="dvText" class="font-semibold text-gray-900"></span>?</p>
                <input type="hidden" id="dvId" />
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelDeleteVisitor" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button type="button" id="confirmDeleteVisitor" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Check In Modal -->
    <div id="checkInModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="check-in-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-[420px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
                <h3 id="check-in-modal-title" class="font-semibold text-sm text-gray-900">Check In Visitor</h3>
                <button id="closeCheckIn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 pt-5 pb-6">
                <p class="text-sm text-gray-700 mb-3">Are you sure you want to check in visitor <span id="ciText" class="font-semibold text-gray-900"></span> now?</p>
                <input type="hidden" id="ciId" />
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelCheckIn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button type="button" id="confirmCheckIn" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">Check In</button>
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

            // Open "Visitor Management" dropdown by default since we're on Visitors Registration page
            const visitorBtn = document.getElementById('visitor-management-btn');
            const visitorSubmenu = document.getElementById('visitor-submenu');
            const visitorArrow = document.getElementById('visitor-arrow');
            
            if (visitorSubmenu && !visitorSubmenu.classList.contains('hidden')) {
                visitorSubmenu.classList.remove('hidden');
                if (visitorArrow) visitorArrow.classList.add('rotate-180');
            }

            // Search functionality
            const searchVisitors = document.getElementById('searchVisitors');
            const resultsCount = document.getElementById('resultsCount');
            
            function updateResultsCount() {
                const tbody = document.getElementById('visitorsTbody');
                if (!tbody || !resultsCount) return;
                const rows = Array.from(tbody.querySelectorAll('tr'));
                const visitorRows = rows.filter(r => !r.querySelector('td[colspan]'));
                const visible = visitorRows.filter(r => !r.classList.contains('hidden')).length;
                resultsCount.textContent = visible + (visible === 1 ? ' result' : ' results');
            }

            function applySearchFilter(query) {
                const q = (query || '').toString().toLowerCase().trim();
                const tbody = document.getElementById('visitorsTbody');
                if (!tbody) return;
                const rows = Array.from(tbody.querySelectorAll('tr'));
                rows.forEach(tr => {
                    const emptyCell = tr.querySelector('td[colspan]');
                    if (emptyCell) { tr.classList.toggle('hidden', q.length > 0); return; }
                    const name = tr.querySelector('td:nth-child(1) .text-sm')?.textContent || '';
                    const id = tr.querySelector('td:nth-child(1) .text-xs')?.textContent || '';
                    const company = tr.querySelector('td:nth-child(3) .text-sm')?.textContent || '';
                    const type = tr.querySelector('td:nth-child(2) .text-xs')?.textContent || '';
                    const host = tr.querySelector('td:nth-child(4) .text-sm')?.textContent || '';
                    const dept = tr.querySelector('td:nth-child(4) .text-xs')?.textContent || '';
                    const hay = (name + ' ' + id + ' ' + company + ' ' + type + ' ' + host + ' ' + dept).toLowerCase();
                    const match = q === '' ? true : hay.includes(q);
                    tr.classList.toggle('hidden', !match);
                });
                updateResultsCount();
            }

            if (searchVisitors) {
                searchVisitors.addEventListener('input', (e) => applySearchFilter(e.target.value));
                applySearchFilter('');
            }

            // Modal Management
            const addVisitorModal = document.getElementById("addVisitorModal");
            const viewVisitorModal = document.getElementById("viewVisitorModal");
            const editVisitorModal = document.getElementById("editVisitorModal");
            const deleteVisitorModal = document.getElementById("deleteVisitorModal");
            const checkInModal = document.getElementById("checkInModal");

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

            function closeAllModals() {
                [addVisitorModal, viewVisitorModal, editVisitorModal, deleteVisitorModal, checkInModal].forEach(modal => {
                    if (modal) {
                        closeModal(modal);
                    }
                });
            }

            // Add Visitor Modal
            const addVisitorBtn = document.getElementById("addVisitorBtn");
            const closeAddVisitorModal = document.getElementById("closeAddVisitorModal");
            const cancelAddVisitor = document.getElementById("cancelAddVisitor");

            if (addVisitorBtn) {
                addVisitorBtn.addEventListener("click", () => {
                    closeAllModals();
                    openModal(addVisitorModal);
                });
            }

            if (closeAddVisitorModal) {
                closeAddVisitorModal.addEventListener("click", () => closeModal(addVisitorModal));
            }

            if (cancelAddVisitor) {
                cancelAddVisitor.addEventListener("click", () => closeModal(addVisitorModal));
            }

            // View Visitor Modal
            document.getElementById("closeViewVisitor")?.addEventListener("click", () => closeModal(viewVisitorModal));
            document.getElementById("closeViewVisitor2")?.addEventListener("click", () => closeModal(viewVisitorModal));

            // Edit Visitor Modal
            document.getElementById("closeEditVisitor")?.addEventListener("click", () => closeModal(editVisitorModal));
            document.getElementById("cancelEditVisitor")?.addEventListener("click", () => closeModal(editVisitorModal));

            // Delete Visitor Modal
            document.getElementById("closeDeleteVisitor")?.addEventListener("click", () => closeModal(deleteVisitorModal));
            document.getElementById("cancelDeleteVisitor")?.addEventListener("click", () => closeModal(deleteVisitorModal));

            // Check In Modal
            document.getElementById("closeCheckIn")?.addEventListener("click", () => closeModal(checkInModal));
            document.getElementById("cancelCheckIn")?.addEventListener("click", () => closeModal(checkInModal));

            // Close modals when clicking outside
            const modals = [addVisitorModal, viewVisitorModal, editVisitorModal, deleteVisitorModal, checkInModal];
            modals.forEach(modal => {
                if (modal) {
                    modal.addEventListener('click', function(e) {
                        if (e.target === this) {
                            closeModal(this);
                        }
                    });
                }
            });

            // Fetch visitor data
            async function fetchVisitor(id) {
                const url = `{{ route('visitor.get') }}?id=${encodeURIComponent(id)}`;
                const res = await fetch(url, { headers: { Accept: "application/json" } });
                if (!res.ok) return null;
                const data = await res.json();
                return data.visitor || null;
            }

            // Delegated clicks for visitor actions
            document.addEventListener("click", async (e) => {
                const vView = e.target.closest(".visitorViewBtn");
                const vEdit = e.target.closest(".visitorEditBtn");
                const vDel = e.target.closest(".visitorDeleteBtn");
                const vIn = e.target.closest(".visitorCheckInBtn");

                if (!vView && !vEdit && !vDel && !vIn) return;
                e.preventDefault();
                const id = (vView || vEdit || vDel || vIn)?.dataset.id;

                if (vView) {
                    const v = await fetchVisitor(id);
                    if (!v) return;
                    document.getElementById("vvId").textContent = v.id || "";
                    document.getElementById("vvName").textContent = v.name || "—";
                    document.getElementById("vvCompany").textContent = v.company || "—";
                    document.getElementById("vvType").textContent = v.visitor_type || "—";
                    document.getElementById("vvHost").textContent = v.host || "—";
                    document.getElementById("vvDept").textContent = v.host_department || "—";
                    document.getElementById("vvDate").textContent = v.check_in_date || "—";
                    document.getElementById("vvTime").textContent = v.check_in_time || "—";
                    document.getElementById("vvStatus").textContent = (v.status || "scheduled").replace("_", " ");
                    openModal(viewVisitorModal);
                    return;
                }

                if (vEdit) {
                    const v = await fetchVisitor(id);
                    if (!v) return;
                    document.getElementById("evId").value = v.id || "";
                    document.getElementById("evCompany").value = v.company || "";
                    document.getElementById("evType").value = v.visitor_type || "";
                    document.getElementById("evPurpose").value = v.purpose || "";
                    document.getElementById("evDate").value = v.check_in_date || "";
                    document.getElementById("evTime").value = v.check_in_time || "";
                    document.getElementById("evStatus").value = v.status || "scheduled";
                    openModal(editVisitorModal);
                    return;
                }

                if (vDel) {
                    document.getElementById("dvId").value = id;
                    document.getElementById("dvText").textContent = id;
                    openModal(deleteVisitorModal);
                    return;
                }

                if (vIn) {
                    e.stopPropagation();
                    document.getElementById("ciId").value = id;
                    document.getElementById("ciText").textContent = id;
                    openModal(checkInModal);
                    return;
                }
            });

            // Form submissions
            const csrf = '{{ csrf_token() }}';

            // Add visitor form
            const addVisitorForm = document.getElementById("addVisitorForm");
            if (addVisitorForm) {
                addVisitorForm.addEventListener("submit", async (e) => {
                    e.preventDefault();
                    const formData = new FormData(addVisitorForm);
                    const payload = Object.fromEntries(formData);
                    
                    try {
                        const res = await fetch(`{{ route('visitor.create') }}`, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrf,
                                "Accept": "application/json",
                            },
                            body: JSON.stringify(payload),
                        });
                        
                        if (res.ok) {
                            closeModal(addVisitorModal);
                            location.reload();
                        } else {
                            throw new Error("Failed to register visitor");
                        }
                    } catch (err) {
                        Swal.fire({
                            icon: "error",
                            title: "Registration failed",
                            text: err.message || "Please try again.",
                            confirmButtonColor: "#059669",
                        });
                    }
                });
            }

            // Edit visitor form
            const editVisitorForm = document.getElementById("editVisitorForm");
            if (editVisitorForm) {
                editVisitorForm.addEventListener("submit", async (e) => {
                    e.preventDefault();
                    const payload = {
                        id: document.getElementById("evId").value,
                        company: document.getElementById("evCompany").value || null,
                        visitor_type: document.getElementById("evType").value || null,
                        purpose: document.getElementById("evPurpose").value || null,
                        check_in_date: document.getElementById("evDate").value || null,
                        check_in_time: document.getElementById("evTime").value || null,
                        status: document.getElementById("evStatus").value || null,
                    };
                    
                    try {
                        const res = await fetch(`{{ route('visitor.update') }}`, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrf,
                                "Accept": "application/json",
                            },
                            body: JSON.stringify(payload),
                        });
                        
                        if (res.ok) {
                            closeModal(editVisitorModal);
                            location.reload();
                        } else {
                            throw new Error("Failed to update visitor");
                        }
                    } catch (err) {
                        Swal.fire({
                            icon: "error",
                            title: "Update failed",
                            text: err.message || "Please try again.",
                            confirmButtonColor: "#059669",
                        });
                    }
                });
            }

            // Confirm delete
            document.getElementById("confirmDeleteVisitor")?.addEventListener("click", async () => {
                const id = document.getElementById("dvId").value;
                const fd = new FormData();
                fd.append("id", id);
                
                try {
                    const res = await fetch(`{{ route('visitor.delete') }}`, {
                        method: "POST",
                        headers: { "X-CSRF-TOKEN": csrf, "Accept": "application/json" },
                        body: fd,
                    });
                    
                    if (res.ok) {
                        closeModal(deleteVisitorModal);
                        location.reload();
                    } else {
                        throw new Error("Failed to delete visitor");
                    }
                } catch (err) {
                    Swal.fire({
                        icon: "error",
                        title: "Deletion failed",
                        text: err.message || "Please try again.",
                        confirmButtonColor: "#059669",
                    });
                }
            });

            // Confirm check in
            document.getElementById("confirmCheckIn")?.addEventListener("click", async () => {
                const id = document.getElementById("ciId").value;
                const now = new Date();
                const pad = (n) => n.toString().padStart(2, "0");
                const date = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;
                const time = `${pad(now.getHours())}:${pad(now.getMinutes())}`;
                
                try {
                    const res = await fetch(`{{ route('visitor.update') }}`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrf,
                            "Accept": "application/json",
                        },
                        body: JSON.stringify({ 
                            id, 
                            status: "checked_in", 
                            check_in_date: date, 
                            check_in_time: time 
                        }),
                    });
                    
                    if (!res.ok) throw new Error("Failed to check in visitor");
                    closeModal(checkInModal);
                    location.reload();
                } catch (err) {
                    Swal.fire({
                        icon: "error",
                        title: "Check-in failed",
                        text: err.message || "Please try again.",
                        confirmButtonColor: "#059669",
                    });
                }
            });
        });
    </script>
</body>
</html>