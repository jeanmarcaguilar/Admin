@php
// Get the authenticated user
$user = auth()->user();
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
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        .case-card {
            transition: all 0.2s ease-in-out;
            border: 1px solid #e5e7eb;
        }

        .case-card:hover {
            border-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .case-row {
            transition: all 0.2s ease;
        }

        .case-row:hover {
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
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-confirmed { background-color: #d1fae5; color: #065f46; }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }
        .status-completed { background-color: #dbeafe; color: #1e40af; }
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
                <svg id="legal-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="legal-submenu" class="submenu mt-1">
                <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                    <a href="{{ route('case.management') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
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
                <h1 class="text-lg font-bold text-gray-800 hidden md:block">Case Management</h1>
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
                        <button id="openPrivacySecurityBtn" class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">Privacy & Security</button>
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
        <div id="notificationDropdown" class="dropdown-panel hidden opacity-0 translate-y-2 scale-95 pointer-events-none
            absolute right-4 mt-3 w-80 bg-white rounded-xl shadow-lg border border-gray-100 z-50">
            <div class="flex justify-between items-center px-4 py-3 border-b border-gray-100">
                <span class="font-semibold text-sm">Notifications</span>
                <span class="bg-red-600 text-white text-xs font-semibold rounded-full px-2 py-0.5">3 new</span>
            </div>
            <ul class="divide-y divide-gray-100 max-h-72 overflow-y-auto custom-scrollbar">
                <li class="flex items-start px-4 py-3 space-x-3 hover:bg-gray-50">
                    <div class="flex-shrink-0 mt-1">
                        <div class="bg-green-200 text-green-700 rounded-full p-2">
                            <i class="fas fa-gavel"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">New Case Assignment</p>
                        <p class="text-sm text-gray-500">Case #C-2023-045 has been assigned to you</p>
                        <p class="text-xs text-gray-400 mt-1">10 minutes ago</p>
                    </div>
                </li>
                <li class="flex items-start px-4 py-3 space-x-3 hover:bg-gray-50">
                    <div class="flex-shrink-0 mt-1">
                        <div class="bg-blue-200 text-blue-700 rounded-full p-2">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">Upcoming Hearing</p>
                        <p class="text-sm text-gray-500">Hearing for Case #C-2023-042 is tomorrow at 10:00 AM</p>
                        <p class="text-xs text-gray-400 mt-1">1 hour ago</p>
                    </div>
                </li>
                <li class="flex items-start px-4 py-3 space-x-3 hover:bg-gray-50">
                    <div class="flex-shrink-0 mt-1">
                        <div class="bg-yellow-200 text-yellow-700 rounded-full p-2">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">Deadline Approaching</p>
                        <p class="text-sm text-gray-500">Filing deadline for Case #C-2023-040 is in 2 days</p>
                        <p class="text-xs text-gray-400 mt-1">3 hours ago</p>
                    </div>
                </li>
            </ul>
            <div class="bg-gray-50 px-4 py-2 text-center">
                <a href="#" class="text-sm font-medium text-brand-primary hover:text-brand-primary-hover">View all notifications</a>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <main class="p-4 sm:p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Page Header -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Case Management</h1>
                            <p class="text-gray-600 mt-1">Manage all legal cases, track progress, and monitor deadlines in one place.</p>
                        </div>
                        <div class="mt-4 md:mt-0 flex space-x-3">
                            <button id="printBtn" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                <i class="fas fa-print mr-2"></i> Print
                            </button>
                            <button id="newCaseBtn" onclick="openNewCaseModal()" class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors font-medium flex items-center">
                                <i class="fas fa-plus mr-2"></i> New Case
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Case Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                    <!-- Active Cases Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Active Cases</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['active_cases'] }}</p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <i class="fas fa-gavel mr-1"></i>
                                        Open
                                    </span>
                                    <span class="text-xs text-gray-500">Active</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-gavel text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            @php 
                                $pct = $stats['total_cases'] > 0 ? min(100, round(($stats['active_cases'] / $stats['total_cases']) * 100)) : 0;
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">{{ $pct }}% of total cases</p>
                        </div>
                    </div>

                    <!-- Pending Cases Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Pending</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['pending_tasks'] ?? 0 }}</p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Waiting
                                    </span>
                                    <span class="text-xs text-gray-500">Progress</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-clock text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            @php 
                                $pct = $stats['total_cases'] > 0 ? min(100, round(($stats['pending_tasks'] / $stats['total_cases']) * 100)) : 0;
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">{{ $pct }}% of total cases</p>
                        </div>
                    </div>

                    <!-- Urgent Cases Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-amber-50 to-amber-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Urgent</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['urgent_cases'] ?? 0 }}</p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Priority
                                    </span>
                                    <span class="text-xs text-gray-500">High</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            @php 
                                $pct = $stats['total_cases'] > 0 ? min(100, round(($stats['urgent_cases'] / $stats['total_cases']) * 100)) : 0;
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-gradient-to-r from-amber-400 to-orange-500 h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $pct }}% of total cases</p>
                        </div>
                    </div>

                    <!-- Upcoming Hearings Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-violet-50 to-violet-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Upcoming Hearings</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['upcoming_hearings'] ?? 0 }}</p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        Scheduled
                                    </span>
                                    <span class="text-xs text-gray-500">30 days</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-calendar-alt text-white text-xl"></i>
                            <div class="p-4 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg">
                                <i class="fas fa-chart-line text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            @php 
                                $pct = $stats['total_cases'] > 0 ? min(100, round(($stats['active_cases'] / $stats['total_cases']) * 100)) : 0;
                                $trend = $stats['total_cases'] > 0 ? round(($stats['active_cases'] / $stats['total_cases']) * 100) - 50 : 0;
                            @endphp
                            <div class="w-full bg-white rounded-full h-3 overflow-hidden shadow-inner">
                                <div class="bg-gradient-to-r from-blue-400 to-indigo-600 h-3 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="flex justify-between items-center mt-2">
                                <p class="text-xs text-gray-600">{{ $pct }}% of total cases</p>
                                @if($trend > 0)
                                    <span class="text-green-600 text-xs font-medium flex items-center">
                                        <i class="fas fa-arrow-up mr-1"></i> {{ abs($trend) }}%
                                    </span>
                                @else
                                    <span class="text-gray-500 text-xs font-medium flex items-center">
                                        <i class="fas fa-minus mr-1"></i> Stable
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="mb-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="relative flex-1 max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="searchInput" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-primary focus:border-brand-primary block w-full pl-10 p-2.5" placeholder="Search cases...">
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button class="px-3 py-1.5 text-sm font-medium bg-blue-50 text-blue-700 rounded-full hover:bg-blue-100 transition-colors">
                                All Cases
                            </button>
                            <button class="px-3 py-1.5 text-sm font-medium bg-green-50 text-green-700 rounded-full hover:bg-green-100 transition-colors">
                                <i class='bx bx-check-circle mr-1'></i> Active
                            </button>
                            <button class="px-3 py-1.5 text-sm font-medium bg-amber-50 text-amber-700 rounded-full hover:bg-amber-100 transition-colors">
                                <i class='bx bx-time-five mr-1'></i> Pending
                            </button>
                            <button class="px-3 py-1.5 text-sm font-medium bg-purple-50 text-purple-700 rounded-full hover:bg-purple-100 transition-colors">
                                <i class='bx bx-gavel mr-1'></i> Urgent
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Hearings -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-900">
                                <i class="fas fa-calendar-day mr-2"></i>Upcoming Hearings
                            </h3>
                            <p class="text-sm text-gray-500">Hearings scheduled in the next 30 days</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="text-xs text-gray-500">{{ isset($stats['upcoming_hearings']) ? $stats['upcoming_hearings'] : 0 }} total</span>
                            <button id="lockAllHearingsBtn" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-700 hover:bg-gray-800 text-white transition-colors duration-200 flex items-center">
                                <i class='bx bx-lock mr-1'></i>Lock All
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="upcomingList" class="bg-white divide-y divide-gray-200">
                                @forelse(($upcoming ?? []) as $u)
                                    <li class="py-3 flex items-center justify-between hearing-item px-6" data-hearing-id="{{ $u['id'] ?? 'unknown' }}">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 hearing-title">{{ $u['title'] }}</div>
                                            <div class="text-xs text-gray-500 hearing-code">{{ $u['code'] }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm text-gray-900 hearing-date">{{ $u['hearing_date'] ?? '-' }}</div>
                                            @if(!empty($u['hearing_time']))
                                                @php
                                                    try { $__ut_disp = \Carbon\Carbon::parse($u['hearing_time'])->format('g:i A'); }
                                                    catch (\Exception $e) { $__ut_disp = $u['hearing_time']; }
                                                @endphp
                                                <div class="text-xs text-gray-500 hearing-time">{{ $__ut_disp }}</div>
                                            @endif
                                        </div>
                                    </li>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">No upcoming hearings</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Cases Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-900">All Cases</h3>
                            <p class="text-sm text-gray-500">Manage and track all legal cases</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button id="exportCasesBtn" class="px-3 py-1.5 text-sm font-medium bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                <i class="fas fa-download mr-1"></i> Export
                            </button>
                            <button id="lockAllCasesBtn" class="px-3 py-1.5 text-sm font-medium bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition-colors flex items-center">
                                <i class='bx bx-lock mr-1'></i> Lock All
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case Number</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contract Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Hearing</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            
                            <tbody id="casesTbody" class="bg-white divide-y divide-gray-200">
                                @if(!empty($cases))
                                    @foreach($cases as $c)
                                        @php $typeKey = strtolower($c['type_badge'] ?? 'civil'); @endphp
                                        @php
                                            $__ht_raw = $c['hearing_time'] ?? '';
                                            try { $__ht_norm = $__ht_raw ? \Carbon\Carbon::parse($__ht_raw)->format('H:i') : ''; }
                                            catch (\Exception $e) { $__ht_norm = preg_match('/^\d{2}:\d{2}$/', (string)$__ht_raw) ? $__ht_raw : ''; }
                                        @endphp
                                        <tr class="case-row hover:bg-gray-50"
                                            data-number="{{ $c['number'] }}"
                                            data-name="{{ $c['name'] }}"
                                            data-client="{{ $c['client'] }}"
                                            data-type="{{ $typeKey }}"
                                            data-status="{{ $c['status'] }}"
                                            data-hearing-date="{{ $c['hearing_date'] ?? '' }}"
                                            data-hearing-time="{{ $__ht_norm }}"
                                        >
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center space-x-2">
                                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                                        <i class='bx bx-briefcase text-sm'></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-bold text-gray-900">{{ $c['number'] }}</div>
                                                        <div class="text-xs text-gray-500">Filed: {{ $c['filed'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if(!empty($c['contract_type']))
                                                    @php
                                                        $statusClass = 'bg-gray-100 text-gray-800';
                                                        $statusText = 'Unknown';
                                                        
                                                        // Set status class and text based on contract status
                                                        if (isset($c['contract_status'])) {
                                                            $rawContractStatus = strtolower((string) $c['contract_status']);
                                                            if ($rawContractStatus === 'inactive') $rawContractStatus = 'active';
                                                            $statusMap = [
                                                                'active' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Active'],
                                                                'expired' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Expired'],
                                                                'terminated' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Terminated'],
                                                                'renewed' => ['class' => 'bg-blue-100 text-blue-800', 'text' => 'Renewed'],
                                                                'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'text' => 'Pending'],
                                                                'upcoming' => ['class' => 'bg-blue-100 text-blue-800', 'text' => 'Upcoming']
                                                            ];
                                                            
                                                            $statusInfo = $statusMap[$rawContractStatus] ?? ['class' => 'bg-gray-100 text-gray-800', 'text' => ucfirst($rawContractStatus)];
                                                            $statusClass = $statusInfo['class'];
                                                            $statusText = $statusInfo['text'];
                                                        } else {
                                                            $statusText = 'Active';
                                                            $statusClass = 'bg-green-100 text-green-800';
                                                        }
                                                        
                                                        // Get contract type label
                                                        $contractLabel = $c['contract_type_label'] ?? (
                                                            [
                                                                'employee' => 'Employee Contract',
                                                                'employment' => 'Employment Agreement',
                                                                'service' => 'Service Contract',
                                                                'other' => 'Other Agreement'
                                                            ][$c['contract_type']] ?? 'Contract'
                                                        );
                                                    @endphp
                                                    
                                                    <div class="flex flex-col space-y-1">
                                                        <div class="flex items-center">
                                                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }} mr-2">
                                                                {{ $statusText }}
                                                            </span>
                                                            <span class="text-sm font-medium text-gray-900">
                                                                {{ $contractLabel }}
                                                            </span>
                                                        </div>
                                                        @if(isset($c['contract_expiration']))
                                                            <div class="text-xs text-gray-500">
                                                                @if($c['contract_status'] === 'expired')
                                                                    Expired on {{ \Carbon\Carbon::parse($c['contract_expiration'])->format('M d, Y') }}
                                                                @else
                                                                    Expires on {{ \Carbon\Carbon::parse($c['contract_expiration'])->format('M d, Y') }}
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        No Contract
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $c['name'] }}</div>
                                                <div class="text-xs text-gray-500">{{ $c['type_label'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-medium text-sm mr-2">{{ $c['client_initials'] ?? '--' }}</div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $c['client'] }}</div>
                                                        <div class="text-xs text-gray-500">{{ $c['client_org'] ?? '' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $c['type_badge'] }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusColor = 'gray';
                                                    $statusText = $c['status'] ?? 'Unknown';
                                                    if (in_array(strtolower($statusText), ['active', 'open', 'confirmed'])) $statusColor = 'green';
                                                    elseif (in_array(strtolower($statusText), ['pending', 'in progress'])) $statusColor = 'yellow';
                                                    elseif (in_array(strtolower($statusText), ['closed', 'completed'])) $statusColor = 'blue';
                                                    elseif (in_array(strtolower($statusText), ['cancelled'])) $statusColor = 'red';
                                                @endphp
                                                <span class="status-badge status-{{ $statusColor }}">
                                                    {{ $statusText }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $c['hearing_date'] ?? '-' }}</div>
                                                @php
                                                    $__ht = $c['hearing_time'] ?? '';
                                                    try { $__ht_disp = $__ht ? \Carbon\Carbon::parse($__ht)->format('g:i A') : ''; }
                                                    catch (\Exception $e) { $__ht_disp = $__ht; }
                                                @endphp
                                                <div class="text-xs text-gray-500">{{ $__ht_disp }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button class="viewCaseBtn text-brand-primary hover:text-brand-primary-hover mr-3"
                                                       title="View Details"
                                                       data-number="{{ $c['number'] }}"
                                                       data-name="{{ $c['name'] }}"
                                                       data-client="{{ $c['client'] }}"
                                                       data-type="{{ $typeKey }}"
                                                       data-type-label="{{ $c['type_label'] }}"
                                                       data-status="{{ $c['status'] }}"
                                                       data-hearing-date="{{ $c['hearing_date'] ?? '' }}"
                                                       data-hearing-time="{{ (function($t){ try { return $t ? \Carbon\Carbon::parse($t)->format('g:i A') : ''; } catch (\Exception $e) { return $t; } })($c['hearing_time'] ?? '') }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="editCaseBtn text-blue-600 hover:text-blue-800 mr-3"
                                                       title="Edit"
                                                       data-number="{{ $c['number'] }}"
                                                       data-name="{{ $c['name'] }}"
                                                       data-client="{{ $c['client'] }}"
                                                       data-type="{{ $typeKey }}"
                                                       data-status="{{ $c['status'] }}"
                                                       data-hearing-date="{{ $c['hearing_date'] ?? '' }}"
                                                       data-hearing-time="{{ (function($t){ try { return $t ? \Carbon\Carbon::parse($t)->format('g:i A') : ''; } catch (\Exception $e) { return $t; } })($c['hearing_time'] ?? '') }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="deleteCaseBtn text-red-600 hover:text-red-800" title="Delete"
                                                       data-number="{{ $c['number'] }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="px-6 py-6 text-center text-sm text-gray-500">No cases found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="bg-white px-6 py-3 flex items-center justify-between border-t border-gray-200">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </a>
                            <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </a>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">{{ count($cases) }}</span> results</p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Previous</span>
                                        <i class="fas fa-chevron-left h-5 w-5"></i>
                                    </a>
                                    <a href="#" aria-current="page" class="z-10 bg-brand-primary border-brand-primary text-white relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                        1
                                    </a>
                                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                        2
                                    </a>
                                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                        3
                                    </a>
                                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                        ...
                                    </span>
                                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                        8
                                    </a>
                                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Next</span>
                                        <i class="fas fa-chevron-right h-5 w-5"></i>
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <!-- New Case Modal -->
    <div id="newCaseModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="new-case-title">
        <div class="bg-white rounded-lg shadow-lg w-[480px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="new-case-title" class="font-semibold text-lg text-gray-900">Create New Case</h3>
                <button type="button" onclick="closeModal('newCaseModal')" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <form id="newCaseForm" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700">Case Title *</label>
                            <input type="text" name="title" id="title" required class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                            <textarea name="description" id="description" required rows="3" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm"></textarea>
                        </div>
                        <div>
                            <label for="case_type" class="block text-sm font-medium text-gray-700">Case Type *</label>
                            <select id="case_type" name="case_type" required class="mt-1 block w-full border border-gray-300 bg-white rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                                <option value="">Select case type</option>
                                <option value="civil">Civil</option>
                                <option value="criminal">Criminal</option>
                                <option value="family">Family Law</option>
                                <option value="corporate">Corporate</option>
                                <option value="contract">Contract</option>
                                <option value="labor">Labor</option>
                            </select>
                        </div>
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700">Priority *</label>
                            <select id="priority" name="priority" required class="mt-1 block w-full border border-gray-300 bg-white rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                                <option value="">Select priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                            <select id="status" name="status" required class="mt-1 block w-full border border-gray-300 bg-white rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                                <option value="">Select status</option>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="closed">Closed</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                        <div>
                            <label for="hearing_date" class="block text-sm font-medium text-gray-700">Next Hearing Date</label>
                            <input type="date" id="hearing_date" name="hearing_date" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label for="hearing_time" class="block text-sm font-medium text-gray-700">Next Hearing Time</label>
                            <input type="time" id="hearing_time" name="hearing_time" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label for="client" class="block text-sm font-medium text-gray-700">Client Name *</label>
                            <input type="text" id="client" name="client" required placeholder="Enter client name" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm" />
                        </div>
                        
                        <div>
                            <label for="court" class="block text-sm font-medium text-gray-700">Court</label>
                            <input type="text" id="court" name="court" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label for="judge" class="block text-sm font-medium text-gray-700">Judge</label>
                            <input type="text" id="judge" name="judge" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                        </div>

                        <div>
                            <label for="contract_type" class="block text-sm font-medium text-gray-700">Contract Type</label>
                            <select id="contract_type" name="contract_type" class="mt-1 block w-full border border-gray-300 bg-white rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                                <option value="">Select contract type</option>
                                <option value="employee">Employee Contract</option>
                                <option value="employment">Employment Agreement</option>
                                <option value="service">Service Contract</option>
                                <option value="other">Other Agreement</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeModal('newCaseModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                        <button type="button" onclick="submitNewCase()" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover">Create Case</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Case Modal -->
    <div id="viewCaseModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="view-case-title">
        <div class="bg-white rounded-lg shadow-lg w-[480px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="view-case-title" class="font-semibold text-lg text-gray-900">Case Details</h3>
                <button id="closeViewCaseBtn" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 text-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-500">Case Number</p>
                        <p id="vcNumber" class="font-medium text-gray-900">—</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Status</p>
                        <p id="vcStatus" class="font-medium text-gray-900">—</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-gray-500">Case Name</p>
                        <p id="vcName" class="font-medium text-gray-900">—</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Client</p>
                        <p id="vcClient" class="font-medium text-gray-900">—</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Type</p>
                        <p id="vcType" class="font-medium text-gray-900">—</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-gray-500">Next Hearing</p>
                        <p id="vcHearing" class="font-medium text-gray-900">—</p>
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button id="closeViewCaseBtn2" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Case Modal -->
    <div id="editCaseModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="edit-case-title">
        <div class="bg-white rounded-lg shadow-lg w-[480px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="edit-case-title" class="font-semibold text-lg text-gray-900">Edit Case</h3>
                <button id="closeEditCaseBtn" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editCaseForm" class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecNumber">Case Number</label>
                        <input id="ecNumber" name="number" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" readonly />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecStatus">Status</label>
                        <select id="ecStatus" name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                            <option value="In Progress">In Progress</option>
                            <option value="Active">Active</option>
                            <option value="Pending">Pending</option>
                            <option value="Closed">Closed</option>
                            <option value="appeal">On Appeal</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs text-gray-600 mb-1" for="ecName">Case Name</label>
                        <input id="ecName" name="name" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecClient">Client</label>
                        <select id="ecClient" name="client" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                            <option value="">Select client</option>
                            @foreach($clients ?? [] as $client)
                                <option value="{{ $client->name }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecType">Type</label>
                        <select id="ecType" name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                            <option value="civil">Civil</option>
                            <option value="criminal">Criminal</option>
                            <option value="family">Family</option>
                            <option value="corporate">Corporate</option>
                            <option value="contract">Contract</option>
                            <option value="ip">IP</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecHearingDate">Next Hearing Date</label>
                        <input id="ecHearingDate" name="hearing_date" type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecHearingTime">Next Hearing Time</label>
                        <input id="ecHearingTime" name="hearing_time" type="time" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" />
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" id="cancelEditCaseBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Case Modal -->
    <div id="deleteCaseModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="delete-case-title">
        <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="delete-case-title" class="font-semibold text-lg text-gray-900">Delete Case</h3>
                <button id="closeDeleteCaseBtn" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 text-sm text-gray-700">
                <p>Are you sure you want to delete case <span class="font-semibold" id="delCaseNumberText">—</span>? This action cannot be undone.</p>
            </div>
            <div class="px-6 pb-6 flex justify-end gap-3">
                <button id="cancelDeleteCaseBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                <button id="confirmDeleteCaseBtn" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700">Delete</button>
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

            // Open "Legal Management" dropdown by default since we're on Case Management page
            const legalBtn = document.getElementById('legal-management-btn');
            const legalSubmenu = document.getElementById('legal-submenu');
            const legalArrow = document.getElementById('legal-arrow');
            
            if (legalSubmenu && !legalSubmenu.classList.contains('hidden')) {
                legalSubmenu.classList.remove('hidden');
                if (legalArrow) legalArrow.classList.add('rotate-180');
            }

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
                    const isHidden = notificationDropdown.classList.contains("hidden");
                    
                    if (isHidden) {
                        notificationDropdown.classList.remove("hidden", "opacity-0", "translate-y-2", "scale-95", "pointer-events-none");
                        notificationDropdown.classList.add("opacity-100", "translate-y-0", "scale-100", "pointer-events-auto");
                    } else {
                        notificationDropdown.classList.add("opacity-0", "translate-y-2", "scale-95", "pointer-events-none");
                        notificationDropdown.classList.remove("opacity-100", "translate-y-0", "scale-100", "pointer-events-auto");
                        setTimeout(() => notificationDropdown.classList.add("hidden"), 200);
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener("click", (e) => {
                    if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                        notificationDropdown.classList.add("opacity-0", "translate-y-2", "scale-95", "pointer-events-none");
                        setTimeout(() => notificationDropdown.classList.add("hidden"), 200);
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

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#casesTbody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
            }

            // Print functionality
            const printBtn = document.getElementById('printBtn');
            if (printBtn) {
                printBtn.addEventListener('click', function() {
                    window.print();
                });
            }

            // Export cases functionality
            const exportCasesBtn = document.getElementById('exportCasesBtn');
            if (exportCasesBtn) {
                exportCasesBtn.addEventListener('click', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Export Started',
                        text: 'Your cases export has been queued. You will receive an email when it\'s ready.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            }

            // Modal Management Functions
            function openModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add("active");
                    modal.style.display = "flex";
                }
            }

            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove("active");
                    setTimeout(() => {
                        modal.style.display = "none";
                    }, 300);
                }
            }

            // Close modals when clicking outside
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal')) {
                    e.target.classList.remove('active');
                    setTimeout(() => {
                        e.target.style.display = 'none';
                    }, 300);
                }
            });

            // New Case Modal
            window.openNewCaseModal = function() {
                try {
                    var now = new Date();
                    var randomNum = Math.floor(1000 + Math.random() * 9000);
                    var caseNumEl = document.getElementById('caseNumber');
                    if (caseNumEl) caseNumEl.value = 'C-' + now.getFullYear() + '-' + randomNum;
                    var filingDateEl = document.getElementById('filingDate');
                    if (filingDateEl) filingDateEl.valueAsDate = new Date();
                    
                    openModal('newCaseModal');
                } catch(e) {
                    console.error(e);
                }
            };

            window.submitNewCase = async function() {
                var form = document.getElementById('newCaseForm');
                if (!form) return;
                
                var submitBtn = form.querySelector('button[type="button"]');
                var originalBtnText = submitBtn ? submitBtn.innerHTML : '';
                if (submitBtn){ 
                    submitBtn.disabled = true; 
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating...'; 
                }
                
                try {
                    // Get all form data including contract_type
                    var formData = new FormData(form);
                    var formObj = {};
                    formData.forEach((value, key) => {
                        // Handle form data properly, especially for checkboxes and selects
                        if (formObj[key]) {
                            if (!Array.isArray(formObj[key])) {
                                formObj[key] = [formObj[key]];
                            }
                            formObj[key].push(value);
                        } else {
                            formObj[key] = value;
                        }
                    });

                    // Ensure contract_type is included even if empty
                    if (!formObj.contract_type) {
                        formObj.contract_type = '';
                    }

                    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                    var csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';
                    
                    var response = await fetch('{{ route("case.create") }}', {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': csrf, 
                            'Accept': 'application/json', 
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });
                    
                    var data = await response.json();
                    if (!response.ok) { 
                        console.error('Server error:', data);
                        throw new Error(data.message || 'Failed to create case. ' + (data.error || '')); 
                    }
                    
                    await Swal.fire({ 
                        icon: 'success', 
                        title: 'Success!', 
                        text: 'Case has been created successfully.', 
                        showConfirmButton: false, 
                        timer: 1500 
                    });
                    
                    form.reset();
                    closeModal('newCaseModal');
                    window.location.reload();
                } catch(error) {
                    console.error('Error:', error);
                    // Check if this is a validation error with error details
                    let errorMessage = (error && error.message) || 'Failed to create case. Please try again.';
                    
                    // If it's a validation error from the server, extract the error messages
                    if (error.response && error.response.data && error.response.data.errors) {
                        const errors = error.response.data.errors;
                        errorMessage = Object.values(errors).flat().join('\n');
                    }
                    
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'Error', 
                        html: errorMessage.replace(/\n/g, '<br>'),
                        confirmButtonColor: '#059669',
                        width: '500px'
                    });
                } finally {
                    if (submitBtn){ submitBtn.disabled = false; submitBtn.innerHTML = originalBtnText; }
                }
            };

            // View Case Modal handler
            function openViewCaseModal(btn) {
                if (!btn) return;
                const d = btn.dataset || {};
                
                const vcNumber = document.getElementById('vcNumber');
                const vcStatus = document.getElementById('vcStatus');
                const vcName = document.getElementById('vcName');
                const vcClient = document.getElementById('vcClient');
                const vcType = document.getElementById('vcType');
                const vcHearing = document.getElementById('vcHearing');
                
                if (vcNumber) vcNumber.textContent = d.number || '—';
                if (vcStatus) vcStatus.textContent = d.status || '—';
                if (vcName) vcName.textContent = d.name || '—';
                if (vcClient) vcClient.textContent = d.client || '—';
                if (vcType) vcType.textContent = (d.typeLabel || d.type || '—').split('_').join(' ');
                if (vcHearing) vcHearing.textContent = (d.hearingDate ? d.hearingDate : '—') + (d.hearingTime ? ' • ' + d.hearingTime : '');
                
                openModal('viewCaseModal');
            }

            // Edit Case Modal handler
            function openEditCaseModal(btn) {
                if (!btn) return;
                const d = btn.dataset || {};
                const tr = btn.closest('tr');
                const rd = tr ? tr.dataset || {} : {};
                
                const ecNumber = document.getElementById('ecNumber');
                const ecStatus = document.getElementById('ecStatus');
                const ecName = document.getElementById('ecName');
                const ecClient = document.getElementById('ecClient');
                const ecType = document.getElementById('ecType');
                const ecHearingDate = document.getElementById('ecHearingDate');
                const ecHearingTime = document.getElementById('ecHearingTime');
                
                if (ecNumber) ecNumber.value = rd.number || d.number || '';
                if (ecStatus) ecStatus.value = rd.status || d.status || '';
                if (ecName) ecName.value = rd.name || d.name || '';
                if (ecClient) {
                    ecClient.value = rd.client || d.client || '';
                    if (ecClient.value === '' && (rd.client || d.client)) {
                        const opt = document.createElement('option');
                        opt.value = rd.client || d.client;
                        opt.text = rd.client || d.client;
                        ecClient.appendChild(opt);
                        ecClient.value = rd.client || d.client;
                    }
                }
                if (ecType) ecType.value = rd.type || d.type || '';
                if (ecHearingDate) ecHearingDate.value = rd.hearingDate || d.hearingDate || '';
                if (ecHearingTime) {
                    let ht = rd.hearingTime || d.hearingTime || '';
                    if (ht) {
                        const hhmmMatch = /^\d{2}:\d{2}(:\d{2})?$/.test(ht);
                        if (hhmmMatch) {
                            ecHearingTime.value = ht.substring(0,5);
                        } else {
                            const m = ht.match(/^(\d{1,2}):(\d{2})\s*([AaPp][Mm])$/);
                            if (m) {
                                let h = parseInt(m[1],10);
                                const min = m[2];
                                const ampm = m[3].toUpperCase();
                                if (ampm === 'PM' && h !== 12) h += 12;
                                if (ampm === 'AM' && h === 12) h = 0;
                                const hh = String(h).padStart(2,'0');
                                ecHearingTime.value = `${hh}:${min}`;
                            } else {
                                ecHearingTime.value = '';
                            }
                        }
                    } else {
                        ecHearingTime.value = '';
                    }
                }
                
                openModal('editCaseModal');
            }

            // Delete Case Modal handler
            function openDeleteCaseModal(btn) {
                if (!btn) return;
                const number = btn.dataset.number || '';
                const txtEl = document.getElementById('delCaseNumberText');
                if (txtEl) txtEl.textContent = number || '—';
                
                const confirmBtn = document.getElementById('confirmDeleteCaseBtn');
                if (confirmBtn) confirmBtn.dataset.number = number || '';
                
                openModal('deleteCaseModal');
            }

            // Event delegation for view, edit, and delete case buttons
            document.addEventListener("click", (e) => {
                const viewBtn = e.target.closest(".viewCaseBtn");
                const editBtn = e.target.closest(".editCaseBtn");
                const delBtn = e.target.closest(".deleteCaseBtn");
                
                if (viewBtn) {
                    e.preventDefault();
                    openViewCaseModal(viewBtn);
                } else if (editBtn) {
                    e.preventDefault();
                    openEditCaseModal(editBtn);
                } else if (delBtn) {
                    e.preventDefault();
                    openDeleteCaseModal(delBtn);
                }
            });

            // Close modal buttons
            document.getElementById('closeViewCaseBtn')?.addEventListener('click', () => closeModal('viewCaseModal'));
            document.getElementById('closeViewCaseBtn2')?.addEventListener('click', () => closeModal('viewCaseModal'));
            document.getElementById('closeEditCaseBtn')?.addEventListener('click', () => closeModal('editCaseModal'));
            document.getElementById('cancelEditCaseBtn')?.addEventListener('click', () => closeModal('editCaseModal'));
            document.getElementById('closeDeleteCaseBtn')?.addEventListener('click', () => closeModal('deleteCaseModal'));
            document.getElementById('cancelDeleteCaseBtn')?.addEventListener('click', () => closeModal('deleteCaseModal'));

            // Delete case confirmation
            document.getElementById('confirmDeleteCaseBtn')?.addEventListener('click', async function() {
                const number = this.dataset.number || '';
                if (!number) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Missing case number to delete.' });
                    return;
                }
                
                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';
                const fd = new FormData();
                fd.append('number', number);
                fd.append('_token', csrf);
                
                // Loading state
                const original = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Deleting...';
                
                try {
                    const res = await fetch('{{ route("case.delete") }}', {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: fd
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok || data.success === false) {
                        throw new Error((data && (data.message || data.error)) || 'Failed to delete case');
                    }
                    
                    await Swal.fire({ icon: 'success', title: 'Deleted', text: 'Case has been deleted.', showConfirmButton: false, timer: 1200 });
                    closeModal('deleteCaseModal');
                    window.location.reload();
                } catch(err) {
                    console.error('Delete failed:', err);
                    Swal.fire({ icon: 'error', title: 'Error', text: (err && err.message) || 'Failed to delete case. Please try again.' });
                } finally {
                    this.disabled = false;
                    this.innerHTML = original;
                }
            });

            // Edit case form submission
            const editCaseForm = document.getElementById('editCaseForm');
            if (editCaseForm) {
                editCaseForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    let submitBtn = this.querySelector('button[type="submit"]');
                    let original = submitBtn ? submitBtn.innerHTML : '';
                    
                    try {
                        if (submitBtn) { 
                            submitBtn.disabled = true; 
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...'; 
                        }

                        const payload = {
                            number: document.getElementById('ecNumber')?.value || '',
                            case_name: document.getElementById('ecName')?.value || '',
                            client_name: document.getElementById('ecClient')?.value || '',
                            case_type: document.getElementById('ecType')?.value || '',
                            status: document.getElementById('ecStatus')?.value || '',
                            hearing_date: document.getElementById('ecHearingDate')?.value || '',
                            hearing_time: document.getElementById('ecHearingTime')?.value || '',
                        };

                        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                        const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';
                        const fd = new FormData();
                        fd.append('number', payload.number);
                        fd.append('case_name', payload.case_name);
                        fd.append('client_name', payload.client_name);
                        fd.append('case_type', payload.case_type);
                        fd.append('status', payload.status);
                        fd.append('hearing_date', payload.hearing_date);
                        fd.append('hearing_time', payload.hearing_time);
                        fd.append('_token', csrf);

                        const res = await fetch('{{ route("case.update") }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: fd
                        });
                        
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok || data.success === false) {
                            throw new Error((data && (data.message || data.error)) || 'Failed to update case');
                        }

                        await Swal.fire({ icon: 'success', title: 'Saved', text: 'Case has been updated.', showConfirmButton: false, timer: 1200 });
                        closeModal('editCaseModal');
                        window.location.reload();
                    } catch(err) {
                        console.error('Update failed:', err);
                        Swal.fire({ icon: 'error', title: 'Error', text: (err && err.message) || 'Failed to update case. Please try again.', confirmButtonColor: '#059669' });
                    } finally {
                        if (submitBtn) { 
                            submitBtn.disabled = false; 
                            submitBtn.innerHTML = original || 'Save'; 
                        }
                    }
                });
            }

            // Lock/Unlock functionality
            const lockAllCasesBtn = document.getElementById('lockAllCasesBtn');
            const lockAllHearingsBtn = document.getElementById('lockAllHearingsBtn');

            // Case lock functionality
            if (lockAllCasesBtn) {
                lockAllCasesBtn.addEventListener('click', () => {
                    const isLocked = localStorage.getItem('casesLocked') === 'true';
                    
                    if (isLocked) {
                        // Currently locked, so unlock
                        if (typeof window.unlockAllCases === 'function') {
                            window.unlockAllCases();
                        } else {
                            // Simple unlock if function doesn't exist
                            const tableRows = document.querySelectorAll('#casesTbody tr');
                            tableRows.forEach(row => {
                                if (row.dataset.originalData) {
                                    try {
                                        const originalData = JSON.parse(row.dataset.originalData);
                                        // Restore original data
                                        // This is a simplified version - you'd want to restore each cell
                                    } catch(e) {}
                                }
                                row.style.opacity = '1';
                                row.classList.remove('locked-row');
                            });
                            localStorage.setItem('casesLocked', 'false');
                            lockAllCasesBtn.innerHTML = '<i class="bx bx-lock mr-1"></i> Lock All';
                            lockAllCasesBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                            lockAllCasesBtn.classList.add('bg-gray-700', 'hover:bg-gray-800');
                        }
                    } else {
                        // Currently unlocked, so lock
                        Swal.fire({
                            title: 'Lock All Cases?',
                            text: 'Are you sure you want to lock all cases for confidentiality?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, Lock',
                            cancelButtonText: 'No, Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                if (typeof window.lockAllCases === 'function') {
                                    window.lockAllCases();
                                } else {
                                    // Simple lock if function doesn't exist
                                    const tableRows = document.querySelectorAll('#casesTbody tr');
                                    tableRows.forEach(row => {
                                        // Store original data
                                        if (!row.dataset.originalData) {
                                            const cells = row.querySelectorAll('td');
                                            const originalData = {};
                                            cells.forEach((cell, index) => {
                                                originalData[`cell${index}`] = cell.innerHTML;
                                            });
                                            row.dataset.originalData = JSON.stringify(originalData);
                                        }
                                        // Apply masking
                                        row.style.opacity = '0.7';
                                        row.classList.add('locked-row');
                                    });
                                    localStorage.setItem('casesLocked', 'true');
                                    lockAllCasesBtn.innerHTML = '<i class="bx bx-lock-open mr-1"></i> Unlock All';
                                    lockAllCasesBtn.classList.remove('bg-gray-700', 'hover:bg-gray-800');
                                    lockAllCasesBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                                }
                            }
                        });
                    }
                });
            }

            // Hearing lock functionality
            if (lockAllHearingsBtn) {
                lockAllHearingsBtn.addEventListener('click', () => {
                    const isLocked = localStorage.getItem('hearingsLocked') === 'true';
                    
                    if (isLocked) {
                        // Currently locked, so unlock
                        const hearingItems = document.querySelectorAll('.hearing-item');
                        hearingItems.forEach(item => {
                            if (item.dataset.originalData) {
                                try {
                                    const originalData = JSON.parse(item.dataset.originalData);
                                    // Restore original data
                                } catch(e) {}
                            }
                            item.style.opacity = '1';
                            item.classList.remove('locked-hearing');
                        });
                        localStorage.setItem('hearingsLocked', 'false');
                        lockAllHearingsBtn.innerHTML = '<i class="bx bx-lock mr-1"></i> Lock All';
                    } else {
                        // Currently unlocked, so lock
                        Swal.fire({
                            title: 'Lock All Hearings?',
                            text: 'Are you sure you want to lock all hearings for confidentiality?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, Lock',
                            cancelButtonText: 'No, Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const hearingItems = document.querySelectorAll('.hearing-item');
                                hearingItems.forEach(item => {
                                    // Store original data
                                    if (!item.dataset.originalData) {
                                        const originalData = {
                                            title: item.querySelector('.hearing-title')?.textContent || '',
                                            code: item.querySelector('.hearing-code')?.textContent || '',
                                            date: item.querySelector('.hearing-date')?.textContent || '',
                                            time: item.querySelector('.hearing-time')?.textContent || ''
                                        };
                                        item.dataset.originalData = JSON.stringify(originalData);
                                    }
                                    // Apply masking
                                    item.style.opacity = '0.7';
                                    item.classList.add('locked-hearing');
                                });
                                localStorage.setItem('hearingsLocked', 'true');
                                lockAllHearingsBtn.innerHTML = '<i class="bx bx-lock-open mr-1"></i> Unlock All';
                            }
                        });
                    }
                });
            }

            // Initialize lock states
            function initializeLockStates() {
                // Cases
                const casesLocked = localStorage.getItem('casesLocked') === 'true';
                if (casesLocked && lockAllCasesBtn) {
                    lockAllCasesBtn.innerHTML = '<i class="bx bx-lock-open mr-1"></i> Unlock All';
                    lockAllCasesBtn.classList.remove('bg-gray-700', 'hover:bg-gray-800');
                    lockAllCasesBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                }

                // Hearings
                const hearingsLocked = localStorage.getItem('hearingsLocked') === 'true';
                if (hearingsLocked && lockAllHearingsBtn) {
                    lockAllHearingsBtn.innerHTML = '<i class="bx bx-lock-open mr-1"></i> Unlock All';
                }
            }

            initializeLockStates();
        });
    </script>
</body>
</html>