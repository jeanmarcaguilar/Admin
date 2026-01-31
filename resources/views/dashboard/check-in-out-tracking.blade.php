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

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-in-progress {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-completed {
            background-color: #e5e7eb;
            color: #4b5563;
        }

        .status-overdue {
            background-color: #fef3c7;
            color: #92400e;
        }

        .stats-card {
            transition: all 0.2s ease-in-out;
            border: 1px solid #e5e7eb;
        }

        .stats-card:hover {
            border-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
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
                    <a href="{{ route('visitors.registration') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Visitors Registration
                    </a>
                    <a href="{{ route('checkinout.tracking') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
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
                    <a href="{{ route('case.management') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1" onclick="return openCaseWithConfGate(this.href)">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Case Management
                    </a>
                    <a href="{{ route('contract.management') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Contract Management
                    </a>
                    <a href="{{ route('document.compliance.tracking') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
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
                <h1 class="text-lg font-bold text-gray-800 hidden md:block">Check In/Out Tracking</h1>
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
                            <h1 class="text-2xl font-bold text-gray-900">Check In/Out Tracking</h1>
                            <p class="text-gray-600 mt-1">Track and manage visitor check-ins and check-outs in real-time</p>
                        </div>
                        <div class="mt-4 md:mt-0 flex space-x-3">
                            <button id="exportBtn" class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors font-medium flex items-center">
                                <i class="fas fa-download mr-2"></i> Export
                            </button>
                            <button id="printBtn" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                <i class="fas fa-print mr-2"></i> Print
                            </button>
                            <button id="newCheckInBtn" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                <i class="fas fa-plus mr-2"></i> New Check-In
                            </button>
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

                <!-- Enhanced Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Currently Checked In Card -->
                    <div class="group relative bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full -mr-12 -mt-12 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-base mb-3">Currently Checked In</p>
                                <p class="font-bold text-4xl text-gray-900 mb-2">{{ $stats['currently_checked_in'] ?? 0 }}</p>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-user-check mr-2"></i>
                                        Active
                                    </span>
                                    <span class="text-sm text-gray-500">Visitors</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-user-check text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Check-Ins Card -->
                    <div class="group relative bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-full -mr-12 -mt-12 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-base mb-3">Today's Check-Ins</p>
                                <p class="font-bold text-4xl text-gray-900 mb-2">{{ $stats['todays_checkins'] ?? 0 }}</p>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                                        <i class="fas fa-sign-in-alt mr-2"></i>
                                        Today
                                    </span>
                                    <span class="text-sm text-gray-500">Entries</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-sign-in-alt text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Check-Ins Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-900">Recent Check-Ins</h3>
                            <p class="text-sm text-gray-500">List of visitors currently in the premises</p>
                        </div>
                        <button class="text-sm text-brand-primary hover:text-brand-primary-hover font-medium">View All</button>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table id="checkinsTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visitor</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Host</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-In Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php $__rows = $currentCheckIns ?? []; @endphp
                                @forelse($__rows as $v)
                                    <tr class="table-row">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="ml-0">
                                                    <div class="text-sm font-medium text-gray-900">{{ $v['name'] ?? 'Visitor' }}</div>
                                                    <div class="text-xs text-gray-500">{{ $v['company'] ?? '—' }} @if(!empty($v['visitor_type'])) • {{ ucfirst($v['visitor_type']) }} @endif</div>
                                                </div>
                                            </div>
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
                                            <span class="status-badge status-in-progress">In Progress</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button class="view-btn text-brand-primary hover:text-brand-primary-hover mr-3" data-id="{{ $v['id'] ?? '' }}" data-tooltip="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-900 check-out-btn" data-id="{{ $v['id'] ?? '' }}" data-tooltip="Check Out">
                                                <i class="fas fa-sign-out-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    @forelse(($allVisitors ?? []) as $v)
                                        <tr class="table-row">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="ml-0">
                                                        <div class="text-sm font-medium text-gray-900">{{ $v['name'] ?? 'Visitor' }}</div>
                                                        <div class="text-xs text-gray-500">{{ $v['company'] ?? '—' }} @if(!empty($v['visitor_type'])) • {{ ucfirst($v['visitor_type']) }} @endif</div>
                                                    </div>
                                                </div>
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
                                                <span class="status-badge {{ $st === 'checked_in' ? 'status-in-progress' : ($st==='checked_out' ? 'status-completed' : 'status-overdue') }}">{{ ucfirst($st) }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button class="view-btn text-brand-primary hover:text-brand-primary-hover mr-3" data-id="{{ $v['id'] ?? '' }}" data-tooltip="View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="text-red-600 hover:text-red-900 check-out-btn" data-id="{{ $v['id'] ?? '' }}" data-tooltip="Check Out">
                                                    <i class="fas fa-sign-out-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">No check-ins yet.</td>
                                        </tr>
                                    @endforelse
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if(count($__rows ?: $allVisitors ?? []) > 10)
                    <div class="bg-white px-6 py-3 flex items-center justify-between border-t border-gray-200">
                        <div class="text-sm text-gray-700">
                            Showing 1 to 10 of {{ count($__rows ?: $allVisitors ?? []) }} results
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary">
                                Previous
                            </button>
                            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary">
                                Next
                            </button>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Check-Out History Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-900">Recent Check-Outs</h3>
                            <p class="text-sm text-gray-500">History of recent visitor check-outs</p>
                        </div>
                        <button class="text-sm text-brand-primary hover:text-brand-primary-hover font-medium">View All</button>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table id="checkoutsTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visitor</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Host</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visit Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse(($recentCheckOuts ?? []) as $v)
                                    <tr class="table-row">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $v['name'] ?? 'Visitor' }}</div>
                                            <div class="text-xs text-gray-500">{{ $v['company'] ?? '—' }} @if(!empty($v['visitor_type'])) • {{ ucfirst($v['visitor_type']) }} @endif</div>
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
                                            <div class="text-sm text-gray-900">{{ $v['duration'] ?? '—' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="status-badge status-completed">Completed</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">No check-outs yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <!-- View Visitor Modal -->
    <div id="viewVisitorModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="view-visitor-title">
        <div class="bg-white rounded-lg shadow-lg w-[480px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="view-visitor-title" class="text-lg font-medium text-gray-900">Visitor Details</h3>
                <button id="closeViewVisitorModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 pt-5 pb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Visitor ID</label>
                        <div id="view_id" class="text-sm font-semibold text-gray-900">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Name</label>
                        <div id="view_name" class="text-sm text-gray-900">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Company</label>
                        <div id="view_company" class="text-sm text-gray-900">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Type</label>
                        <div id="view_type" class="text-sm text-gray-900">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Host</label>
                        <div id="view_host" class="text-sm text-gray-900">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Department</label>
                        <div id="view_department" class="text-sm text-gray-900">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Check-In Date</label>
                        <div id="view_checkin_date" class="text-sm text-gray-900">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Check-In Time</label>
                        <div id="view_checkin_time" class="text-sm text-gray-900">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Purpose</label>
                        <div id="view_purpose" class="text-sm text-gray-900">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Status</label>
                        <div id="view_status" class="text-sm text-gray-900">—</div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                    <button id="closeViewVisitorModalFooter" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Check-Out Modal -->
    <div id="checkOutModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="check-out-title">
        <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="check-out-title" class="text-lg font-medium text-gray-900">Check Out Visitor</h3>
                <button id="closeCheckOutModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 pt-5 pb-6">
                <p class="text-sm text-gray-700 mb-3">Are you sure you want to check out
                    <span id="check_out_visitor_name" class="font-semibold text-gray-900">this visitor</span>?
                </p>
                <div class="flex justify-end space-x-3 mt-5">
                    <button id="cancelCheckOut" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button id="confirmCheckOut" type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">Check Out</button>
                </div>
            </div>
        </div>
    </div>

    <!-- New Check-In Modal -->
    <div id="newCheckInModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="new-checkin-title">
        <div class="bg-white rounded-lg shadow-lg w-[480px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="new-checkin-title" class="text-lg font-medium text-gray-900">New Check-In</h3>
                <button id="closeNewCheckInModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 pt-5 pb-6">
                <form id="newCheckInForm" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="newVisitorName" class="block text-sm font-medium text-gray-700 mb-1">Visitor Name *</label>
                            <input type="text" id="newVisitorName" name="visitorName" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" required>
                        </div>
                        <div>
                            <label for="newHostName" class="block text-sm font-medium text-gray-700 mb-1">Host *</label>
                            <select id="newHostName" name="hostName" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" required>
                                <option value="">Select a host</option>
                                <option value="John Smith">John Smith (Marketing)</option>
                                <option value="Lisa Wang">Lisa Wang (IT Department)</option>
                            </select>
                        </div>
                        <div>
                            <label for="newCheckInTime" class="block text-sm font-medium text-gray-700 mb-1">Check-In Time *</label>
                            <input type="time" id="newCheckInTime" name="checkInTime" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" required>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                        <button id="cancelNewCheckIn" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover rounded-lg transition-colors">Save</button>
                    </div>
                </form>
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

            // Open "Visitor Management" dropdown by default since we're on Check In/Out Tracking page
            const visitorBtn = document.getElementById('visitor-management-btn');
            const visitorSubmenu = document.getElementById('visitor-submenu');
            const visitorArrow = document.getElementById('visitor-arrow');
            
            if (visitorSubmenu && visitorSubmenu.classList.contains('hidden')) {
                visitorSubmenu.classList.remove('hidden');
                if (visitorArrow) visitorArrow.classList.add('rotate-180');
            }

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#checkinsTable tbody tr, #checkoutsTable tbody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
            }

            // Filter buttons
            const filterButtons = document.querySelectorAll('.bg-white button:not(#searchInput)');
            const tableRows = document.querySelectorAll('#checkinsTable tbody tr');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => {
                        btn.classList.remove('bg-brand-primary', 'text-white');
                        // Reset to original colors based on button type
                        if (btn.textContent.toLowerCase().includes('all visitors')) {
                            btn.classList.add('bg-blue-50', 'text-blue-700');
                        } else if (btn.textContent.toLowerCase().includes('checked in')) {
                            btn.classList.add('bg-green-50', 'text-green-700');
                        } else if (btn.textContent.toLowerCase().includes('overdue')) {
                            btn.classList.add('bg-amber-50', 'text-amber-700');
                        } else if (btn.textContent.toLowerCase().includes('checked out')) {
                            btn.classList.add('bg-gray-100', 'text-gray-700');
                        }
                    });
                    
                    // Add active class to clicked button
                    this.classList.remove('bg-blue-50', 'text-blue-700', 'bg-green-50', 'text-green-700', 'bg-amber-50', 'text-amber-700', 'bg-gray-100', 'text-gray-700');
                    this.classList.add('bg-brand-primary', 'text-white');
                    
                    // Apply filter logic
                    const filterType = this.textContent.toLowerCase();
                    let filterValue = '';
                    
                    if (filterType.includes('checked in')) {
                        filterValue = 'checked_in';
                    } else if (filterType.includes('checked out')) {
                        filterValue = 'checked_out';
                    } else if (filterType.includes('overdue')) {
                        filterValue = 'overdue';
                    } else if (filterType.includes('all visitors')) {
                        filterValue = 'all';
                    }
                    
                    // Filter table rows
                    tableRows.forEach(row => {
                        if (filterValue === 'all') {
                            row.style.display = '';
                        } else {
                            const statusCell = row.querySelector('td:nth-child(6)'); // Status column
                            if (statusCell) {
                                const statusText = statusCell.textContent.toLowerCase().trim();
                                if (filterValue === 'overdue') {
                                    // Show overdue and checked_in (since checked_in might be overdue)
                                    row.style.display = (statusText.includes('overdue') || statusText.includes('checked_in')) ? '' : 'none';
                                } else {
                                    row.style.display = statusText.includes(filterValue) ? '' : 'none';
                                }
                            }
                        }
                    });
                });
            });

            // Export functionality
            const exportBtn = document.getElementById('exportBtn');
            if (exportBtn) {
                exportBtn.addEventListener('click', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Export Started',
                        text: 'Your data export has been queued. You will receive an email when it\'s ready.',
                        timer: 2000,
                        showConfirmButton: false
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

            // New Check-In button
            const newCheckInBtn = document.getElementById('newCheckInBtn');
            const newCheckInModal = document.getElementById('newCheckInModal');
            if (newCheckInBtn && newCheckInModal) {
                newCheckInBtn.addEventListener('click', function() {
                    openModal(newCheckInModal);
                });
            }

            // Modal Management
            const viewVisitorModal = document.getElementById("viewVisitorModal");
            const checkOutModal = document.getElementById("checkOutModal");
            const newCheckInModalElem = document.getElementById("newCheckInModal");

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

            // View Visitor Modal
            document.addEventListener('click', function(e) {
                if (e.target.closest('.view-btn')) {
                    const btn = e.target.closest('.view-btn');
                    const visitorId = btn.dataset.id;
                    
                    // In a real app, fetch visitor details via AJAX
                    // For this example, we'll populate with dummy data
                    const visitorData = {
                        id: visitorId || "VIS-001",
                        name: "John Doe",
                        company: "ABC Corporation",
                        type: "Client",
                        host: "Jane Smith",
                        department: "Marketing",
                        checkin_date: "{{ date('Y-m-d') }}",
                        checkin_time: "{{ date('H:i') }}",
                        purpose: "Business Meeting",
                        status: "Checked In"
                    };
                    
                    document.getElementById('view_id').textContent = visitorData.id;
                    document.getElementById('view_name').textContent = visitorData.name;
                    document.getElementById('view_company').textContent = visitorData.company;
                    document.getElementById('view_type').textContent = visitorData.type;
                    document.getElementById('view_host').textContent = visitorData.host;
                    document.getElementById('view_department').textContent = visitorData.department;
                    document.getElementById('view_checkin_date').textContent = visitorData.checkin_date;
                    document.getElementById('view_checkin_time').textContent = visitorData.checkin_time;
                    document.getElementById('view_purpose').textContent = visitorData.purpose;
                    document.getElementById('view_status').textContent = visitorData.status;
                    
                    openModal(viewVisitorModal);
                }
            });

            document.getElementById('closeViewVisitorModal').addEventListener('click', () => closeModal(viewVisitorModal));
            document.getElementById('closeViewVisitorModalFooter').addEventListener('click', () => closeModal(viewVisitorModal));

            // Check-Out Modal
            document.addEventListener('click', function(e) {
                if (e.target.closest('.check-out-btn')) {
                    const btn = e.target.closest('.check-out-btn');
                    const visitorId = btn.dataset.id;
                    const visitorName = btn.closest('tr').querySelector('.text-sm.font-medium').textContent;
                    
                    document.getElementById('check_out_visitor_name').textContent = visitorName;
                    openModal(checkOutModal);
                }
            });

            document.getElementById('closeCheckOutModal').addEventListener('click', () => closeModal(checkOutModal));
            document.getElementById('cancelCheckOut').addEventListener('click', () => closeModal(checkOutModal));
            document.getElementById('confirmCheckOut').addEventListener('click', () => {
                const visitorName = document.getElementById('check_out_visitor_name').textContent;
                closeModal(checkOutModal);
                Swal.fire({
                    icon: 'success',
                    title: 'Visitor Checked Out',
                    text: `${visitorName} has been checked out successfully.`,
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            // New Check-In Modal
            if (newCheckInModalElem) {
                document.getElementById('closeNewCheckInModal').addEventListener('click', () => closeModal(newCheckInModalElem));
                document.getElementById('cancelNewCheckIn').addEventListener('click', () => closeModal(newCheckInModalElem));
                
                const newCheckInForm = document.getElementById('newCheckInForm');
                if (newCheckInForm) {
                    newCheckInForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        closeModal(newCheckInModalElem);
                        Swal.fire({
                            icon: 'success',
                            title: 'Check-In Registered',
                            text: 'The visitor check-in has been registered successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    });
                }
            }

            // Close modals when clicking outside
            const modals = [viewVisitorModal, checkOutModal, newCheckInModalElem];
            modals.forEach(modal => {
                if (modal) {
                    modal.addEventListener('click', function(e) {
                        if (e.target === this) {
                            closeModal(this);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>