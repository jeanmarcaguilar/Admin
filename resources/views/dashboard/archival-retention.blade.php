@php
// Get the authenticated user
$user = auth()->user();
// Use variables passed from route if available; otherwise fallback to session
$documents = isset($documents) ? $documents : session('uploaded_documents', []);
$archivedDocuments = isset($archivedDocuments) ? $archivedDocuments : session('archived_documents', []);
$settings = isset($settings) ? $settings : [
    'default_retention' => '5',
    'auto_archive' => true,
    'notification_emails' => '',
    'default_lead_time' => '7', // Default lead time in days before archival
];
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

        .policy-card {
            transition: all 0.2s ease-in-out;
            border: 1px solid #e5e7eb;
        }

        .policy-card:hover {
            border-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .document-row {
            transition: all 0.2s ease;
        }

        .document-row:hover {
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
                <svg id="document-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="document-submenu" class="submenu mt-1">
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
                    <a href="{{ route('document.archival.retention.policy') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
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
                <h1 class="text-lg font-bold text-gray-800 hidden md:block">Archival & Retention Policy</h1>
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
                            <h1 class="text-2xl font-bold text-gray-900">Archival & Retention Policy</h1>
                            <p class="text-gray-600 mt-1">Manage document retention policies and archival schedules</p>
                        </div>
                        <div class="mt-4 md:mt-0 flex space-x-3">
                            <button id="exportBtn" class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors font-medium flex items-center">
                                <i class="fas fa-download mr-2"></i> Export
                            </button>
                            <button id="printBtn" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                <i class="fas fa-print mr-2"></i> Print
                            </button>
                            <button id="archivesBtn" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                <i class="fas fa-archive mr-2"></i> Archives
                            </button>
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
                            <input type="text" id="searchInput" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-primary focus:border-brand-primary block w-full pl-10 p-2.5" placeholder="Search documents...">
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button class="px-3 py-1.5 text-sm font-medium bg-blue-50 text-blue-700 rounded-full hover:bg-blue-100 transition-colors">
                                All Documents
                            </button>
                            <button class="px-3 py-1.5 text-sm font-medium bg-green-50 text-green-700 rounded-full hover:bg-green-100 transition-colors">
                                <i class='bx bx-check-circle mr-1'></i> Active
                            </button>
                            <button class="px-3 py-1.5 text-sm font-medium bg-amber-50 text-amber-700 rounded-full hover:bg-amber-100 transition-colors">
                                <i class='bx bx-time-five mr-1'></i> Upcoming Archive
                            </button>
                            <button class="px-3 py-1.5 text-sm font-medium bg-purple-50 text-purple-700 rounded-full hover:bg-purple-100 transition-colors">
                                <i class='bx bx-archive mr-1'></i> Archived
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Document Retention Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Documents Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Total Documents</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ count($documents) + count($archivedDocuments ?? []) }}</p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="bx bx-file mr-1"></i>
                                        Files
                                    </span>
                                    <span class="text-xs text-gray-500">Total</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="bx bx-file text-white text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Archivals Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-amber-50 to-amber-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Upcoming Archivals</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">
                                    {{ count(array_filter($documents, function($doc) {
                                        $expiryDate = $doc['expiry_date'] ?? null;
                                        return $expiryDate && 
                                               strtotime($expiryDate) > time() && 
                                               strtotime($expiryDate) < strtotime('+30 days');
                                    })) }}
                                </p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        <i class="bx bx-time-five mr-1"></i>
                                        Pending
                                    </span>
                                    <span class="text-xs text-gray-500">30 days</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="bx bx-time-five text-white text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Expiring Soon Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-red-50 to-red-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Expiring Soon</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">
                                    {{ count(array_filter($documents, function($doc) {
                                        $expiryDate = $doc['expiry_date'] ?? null;
                                        return $expiryDate && 
                                               strtotime($expiryDate) < time() && 
                                               strtotime($expiryDate) > strtotime('-7 days');
                                    })) }}
                                </p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="bx bx-error mr-1"></i>
                                        Expired
                                    </span>
                                    <span class="text-xs text-gray-500">Past due</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="bx bx-error text-white text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Categories Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-violet-50 to-violet-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Categories</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ count(array_unique(array_column($documents, 'category'))) }}</p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800">
                                        <i class="bx bx-category-alt mr-1"></i>
                                        Types
                                    </span>
                                    <span class="text-xs text-gray-500">Groups</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="bx bx-category-alt text-white text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Retention Policies -->
                @php
                    // Aggregate counts per policy category based on upcoming documents
                    $policyCategories = [
                        'financial' => ['label' => 'Financial', 'retention' => '7 years', 'icon' => 'bx-money-withdraw', 'color' => 'blue'],
                        'hr'        => ['label' => 'Employee Records', 'retention' => '7 years', 'icon' => 'bx-group', 'color' => 'green'],
                        'legal'     => ['label' => 'Legal Contracts', 'retention' => '10+ years', 'icon' => 'bx-gavel', 'color' => 'purple'],
                        'loan'      => ['label' => 'Loan Documents', 'retention' => '7 years', 'icon' => 'bx-credit-card', 'color' => 'amber'],
                        'client'    => ['label' => 'Client Documents', 'retention' => '5 years', 'icon' => 'bx-user-check', 'color' => 'cyan'],
                        'vendor'    => ['label' => 'Vendor Documents', 'retention' => '5 years', 'icon' => 'bx-store', 'color' => 'orange'],
                        'internal'  => ['label' => 'Internal Documents', 'retention' => '3 years', 'icon' => 'bx-buildings', 'color' => 'gray'],
                    ];
                    $policyCounts = array_fill_keys(array_keys($policyCategories), 0);
                    foreach ($documents as $docItem) {
                        $docCatKey = strtolower($docItem['category'] ?? '');
                        if (isset($policyCounts[$docCatKey])) {
                            $policyCounts[$docCatKey]++;
                        }
                    }
                @endphp

                <div class="mb-8">
                    <h3 class="font-semibold text-lg text-gray-900 mb-4">
                        <i class='bx bx-time-five mr-2'></i>Document Retention Policies
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($policyCategories as $categoryKey => $policy)
                            @php
                                $colorClasses = [
                                    'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'text-blue-500'],
                                    'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'text-green-500'],
                                    'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'icon' => 'text-purple-500'],
                                    'amber' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'icon' => 'text-amber-500'],
                                    'cyan' => ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-800', 'icon' => 'text-cyan-500'],
                                    'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'icon' => 'text-orange-500'],
                                    'gray' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'text-gray-500'],
                                ];
                                $colorClass = $colorClasses[$policy['color']] ?? $colorClasses['blue'];
                            @endphp
                            <div class="policy-card bg-white rounded-xl p-6 cursor-pointer group" data-policy-category="{{ $categoryKey }}">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-medium text-gray-900">
                                        <i class='bx {{ $policy['icon'] }} {{ $colorClass['icon'] }} mr-2'></i>
                                        {{ $policy['label'] }}
                                    </h4>
                                    <span class="{{ $colorClass['bg'] }} {{ $colorClass['text'] }} text-xs font-medium px-2.5 py-0.5 rounded-full group-hover:opacity-90 transition-colors">{{ $policy['retention'] }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mb-4">
                                    @if($categoryKey === 'financial')
                                        Tax returns, financial statements, audit reports, and related documents.
                                    @elseif($categoryKey === 'hr')
                                        Employee records, contracts, performance reviews, and related HR documents.
                                    @elseif($categoryKey === 'legal')
                                        Contracts, agreements, legal correspondence, and compliance documents.
                                    @elseif($categoryKey === 'loan')
                                        Loan applications, agreements, promissory notes, and related documents.
                                    @elseif($categoryKey === 'client')
                                        Client receipts, contracts, KYC documents, and related client records.
                                    @elseif($categoryKey === 'vendor')
                                        Vendor contracts, agreements, and related documentation.
                                    @else
                                        Internal memos, policies, procedures, and other company documents.
                                    @endif
                                </p>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <div class="flex items-center">
                                            <i class='bx bx-file mr-1'></i>
                                            <span>{{ $policyCounts[$categoryKey] ?? 0 }} documents</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                            <span>Compliant</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <div class="flex items-center">
                                            <i class='bx bx-time mr-1'></i>
                                            <span>Lead Time: {{ $settings['default_lead_time'] ?? '7' }} days</span>
                                        </div>
                                        <button class="text-brand-primary hover:text-brand-primary-hover text-xs font-medium">Configure</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Upcoming Archivals Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-900">Upcoming for Archival</h3>
                            <p class="text-sm text-gray-500">Documents scheduled for archival in the next 30 days</p>
                        </div>
                        <button class="text-sm text-brand-primary hover:text-brand-primary-hover font-medium">View All</button>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table id="upcomingTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Retention Period</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled For</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    // Pagination logic for upcoming archivals
                                    $upcomingDocuments = array_filter($documents, function($doc) {
                                        $expiryDate = $doc['expiry_date'] ?? null;
                                        return $expiryDate && 
                                               strtotime($expiryDate) > time() && 
                                               strtotime($expiryDate) < strtotime('+30 days');
                                    });
                                    
                                    $perPage = 10;
                                    $currentPage = request()->get('page', 1);
                                    $totalUpcoming = count($upcomingDocuments);
                                    $totalPages = ceil($totalUpcoming / $perPage);
                                    $offset = ($currentPage - 1) * $perPage;
                                    $upcomingPaginated = array_slice($upcomingDocuments, $offset, $perPage);
                                @endphp
                                
                                @forelse($upcomingPaginated as $doc)
                                    @php
                                        $dtype = strtoupper($doc['type'] ?? '');
                                        $icon = in_array($dtype, ['PDF']) ? 'bxs-file-pdf text-red-500' : (in_array($dtype, ['WORD','DOC','DOCX']) ? 'bxs-file-doc text-blue-500' : (in_array($dtype, ['EXCEL','XLS','XLSX']) ? 'bxs-file-txt text-green-500' : 'bxs-file text-gray-500'));
                                        $rawCategory = $doc['category'] ?? ($doc['type'] ?? 'Other');
                                        $categoryKey = strtolower($rawCategory);
                                        $displayCategory = $categoryKey === 'hr' ? 'HR' : ucfirst($categoryKey);
                                        $retention = match($categoryKey) {
                                            'financial' => '7 years',
                                            'hr' => '7 years',
                                            'legal' => '10+ years',
                                            default => '5 years',
                                        };
                                        // Backward compat: if raw type is not a known category, keep original for display
                                        if (!in_array($categoryKey, ['financial','hr','legal','operations'])) {
                                            $displayCategory = $doc['type'] ?? 'Other';
                                        }
                                    @endphp
                                    <tr class="document-row" data-category="{{ $categoryKey }}" data-type="{{ strtolower($doc['type'] ?? 'other') }}" data-doc-id="{{ $doc['id'] ?? '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <i class='bx {{ $icon }} text-xl mr-3'></i>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900"><span class="doc-name" data-name="{{ $doc['name'] }}">{{ $doc['name'] }}</span></div>
                                                    <div class="text-xs text-gray-500">{{ ($doc['type'] ?? 'File') }} • {{ ($doc['size'] ?? '') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $displayCategory }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $retention }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::now()->addMonths(6)->format('M d, Y') }}</div>
                                            <div class="text-xs text-gray-500">in 6 months</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button class="text-brand-primary hover:text-brand-primary-hover mr-3 extend-btn"
                                                    data-name="{{ $doc['name'] }}"
                                                    data-category="{{ $categoryKey }}"
                                                    data-retention="{{ $retention }}">
                                                Extend
                                            </button>
                                            <button class="text-gray-600 hover:text-gray-900 archive-btn"
                                                    data-name="{{ $doc['name'] }}"
                                                    data-category="{{ $categoryKey }}">
                                                Archive Now
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">No documents available. Upload documents in "Document Upload & Indexing" to manage archival.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Controls -->
                    @if($totalUpcoming > $perPage)
                    <div class="bg-white px-6 py-3 flex items-center justify-between border-t border-gray-200">
                        <div class="text-sm text-gray-700">
                            Showing {{ $offset + 1 }} to {{ min($offset + $perPage, $totalUpcoming) }} of {{ $totalUpcoming }} results
                        </div>
                        <div class="flex space-x-2">
                            @if($currentPage > 1)
                                <a href="{{ request()->url() }}?page={{ $currentPage - 1 }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary">
                                    Previous
                                </a>
                            @else
                                <button disabled class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary disabled:opacity-50" disabled>
                                    Previous
                                </button>
                            @endif
                            
                            @if($currentPage < $totalPages)
                                <a href="{{ request()->url() }}?page={{ $currentPage + 1 }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary">
                                    Next
                                </a>
                            @else
                                <button disabled class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary disabled:opacity-50" disabled>
                                    Next
                                </button>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Archived Documents (Initially Hidden) -->
                <div id="archivedSection" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-900">Recently Archived</h3>
                            <p class="text-sm text-gray-500">Documents that have been archived</p>
                        </div>
                        <button class="text-sm text-brand-primary hover:text-brand-primary-hover font-medium">View All</button>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table id="archivedTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archived On</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Deletion</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($archivedDocuments as $adoc)
                                    @php
                                        $acat = ucfirst(strtolower($adoc['category'] ?? ($adoc['type'] ?? 'Other')));
                                    @endphp
                                    <tr class="document-row">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <i class='bx bxs-file text-gray-400 text-xl mr-3 opacity-50'></i>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-500">{{ $adoc['name'] ?? 'Document' }}</div>
                                                    <div class="text-xs text-gray-400">{{ ($adoc['type'] ?? 'File') }} • {{ ($adoc['size'] ?? '—') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $acat }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $adoc['archived_on'] ?? '—' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $adoc['scheduled_deletion'] ?? '—' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button class="text-brand-primary hover:text-brand-primary-hover mr-3 restore-archived-btn" data-name="{{ $adoc['name'] ?? '' }}">Restore</button>
                                            <button class="text-red-600 hover:text-red-800 delete-archived-btn" data-name="{{ $adoc['name'] ?? '' }}">Delete</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">No archived documents yet.</td>
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
    <!-- Extend Retention Modal -->
    <div id="extendRetentionModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="extend-retention-title">
        <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
                <h3 id="extend-retention-title" class="font-semibold text-sm text-gray-900">Extend Retention</h3>
                <button id="closeExtendRetentionBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 pt-5 pb-6">
                <p class="text-sm text-gray-700 mb-3">Document: <span id="extendDocName" class="font-semibold text-gray-900"></span></p>
                <div class="mb-4">
                    <label for="extendNewPeriod" class="block text-xs font-medium text-gray-700 mb-1">New Retention Period</label>
                    <select id="extendNewPeriod" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <option value="+6 months">Extend by 6 months</option>
                        <option value="+1 year">Extend by 1 year</option>
                        <option value="+3 years">Extend by 3 years</option>
                        <option value="custom">Custom (manual review)</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 mt-5">
                    <button id="cancelExtendRetentionBtn" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button id="confirmExtendRetentionBtn" type="button" class="px-4 py-2 text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover rounded-lg transition-colors">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Archive Now Modal -->
    <div id="archiveNowModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="archive-now-title">
        <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
                <h3 id="archive-now-title" class="font-semibold text-sm text-gray-900">Archive Document</h3>
                <button id="closeArchiveNowBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 pt-5 pb-6">
                <p class="text-sm text-gray-700 mb-3">Document: <span id="archiveDocName" class="font-semibold text-gray-900"></span></p>
                <div class="mb-4">
                    <label for="archiveReason" class="block text-xs font-medium text-gray-700 mb-1">Reason (optional)</label>
                    <textarea id="archiveReason" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" placeholder="Add a note for archiving"></textarea>
                </div>
                <div class="flex justify-end space-x-3 mt-5">
                    <button id="cancelArchiveNowBtn" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button id="confirmArchiveNowBtn" type="button" class="px-4 py-2 text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover rounded-lg transition-colors">Archive</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Restore Archived Modal -->
    <div id="restoreArchivedModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="restore-archived-title">
        <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
                <h3 id="restore-archived-title" class="font-semibold text-sm text-gray-900">Restore Document</h3>
                <button id="closeRestoreArchivedBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 pt-5 pb-6">
                <p class="text-sm text-gray-700 mb-3">Document: <span id="restoreDocName" class="font-semibold text-gray-900"></span></p>
                <p class="text-xs text-gray-500 mb-4">This will move the document back to the Upcoming list.</p>
                <div class="flex justify-end space-x-3 mt-5">
                    <button id="cancelRestoreArchivedBtn" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button id="confirmRestoreArchivedBtn" type="button" class="px-4 py-2 text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover rounded-lg transition-colors">Restore</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Archived Modal -->
    <div id="deleteArchivedModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="delete-archived-title">
        <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
                <h3 id="delete-archived-title" class="font-semibold text-sm text-gray-900">Delete Archived Document</h3>
                <button id="closeDeleteArchivedBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 pt-5 pb-6">
                <p class="text-sm text-gray-700 mb-3">Document: <span id="deleteDocName" class="font-semibold text-gray-900"></span></p>
                <p class="text-xs text-gray-500 mb-4">This permanently removes the archived entry from the list.</p>
                <div class="flex justify-end space-x-3 mt-5">
                    <button id="cancelDeleteArchivedBtn" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button id="confirmDeleteArchivedBtn" type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">Delete</button>
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

            // Open "Document Management" dropdown by default since we're on Archival & Retention Policy page
            const documentBtn = document.getElementById('document-management-btn');
            const documentSubmenu = document.getElementById('document-submenu');
            const documentArrow = document.getElementById('document-arrow');
            
            if (documentSubmenu && !documentSubmenu.classList.contains('hidden')) {
                documentSubmenu.classList.remove('hidden');
                if (documentArrow) documentArrow.classList.add('rotate-180');
            }

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#upcomingTable tbody tr, #archivedTable tbody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
            }

            // Policy card filter
            const policyCards = document.querySelectorAll('.policy-card');
            policyCards.forEach(card => {
                card.addEventListener('click', function() {
                    const category = this.dataset.policyCategory;
                    const rows = document.querySelectorAll('#upcomingTable tbody tr');
                    
                    rows.forEach(row => {
                        const rowCategory = row.dataset.category;
                        row.style.display = (!category || rowCategory === category) ? '' : 'none';
                    });
                    
                    // Reset search
                    if (searchInput) searchInput.value = '';
                });
            });

            // Export functionality
            const exportBtn = document.getElementById('exportBtn');
            if (exportBtn) {
                exportBtn.addEventListener('click', function() {
                    // In a real app, this would make an API call
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

            // Archives toggle
            const archivesBtn = document.getElementById('archivesBtn');
            const archivedSection = document.getElementById('archivedSection');
            if (archivesBtn && archivedSection) {
                archivesBtn.addEventListener('click', function() {
                    const isHidden = archivedSection.classList.contains('hidden');
                    archivedSection.classList.toggle('hidden', !isHidden);
                    
                    if (!isHidden) {
                        archivedSection.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            }

            // Modal Management
            const extendRetentionModal = document.getElementById("extendRetentionModal");
            const archiveNowModal = document.getElementById("archiveNowModal");
            const restoreArchivedModal = document.getElementById("restoreArchivedModal");
            const deleteArchivedModal = document.getElementById("deleteArchivedModal");

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

            // Extend Retention Modal
            document.addEventListener('click', function(e) {
                if (e.target.closest('.extend-btn')) {
                    const btn = e.target.closest('.extend-btn');
                    const docName = btn.dataset.name;
                    document.getElementById('extendDocName').textContent = docName;
                    openModal(extendRetentionModal);
                }
            });

            document.getElementById('closeExtendRetentionBtn').addEventListener('click', () => closeModal(extendRetentionModal));
            document.getElementById('cancelExtendRetentionBtn').addEventListener('click', () => closeModal(extendRetentionModal));
            document.getElementById('confirmExtendRetentionBtn').addEventListener('click', () => {
                const period = document.getElementById('extendNewPeriod').value;
                const docName = document.getElementById('extendDocName').textContent;
                closeModal(extendRetentionModal);
                Swal.fire({
                    icon: 'success',
                    title: 'Retention Extended',
                    text: `${docName} retention updated (${period}).`,
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            // Archive Now Modal
            document.addEventListener('click', function(e) {
                if (e.target.closest('.archive-btn')) {
                    const btn = e.target.closest('.archive-btn');
                    const docName = btn.dataset.name;
                    document.getElementById('archiveDocName').textContent = docName;
                    document.getElementById('archiveReason').value = '';
                    openModal(archiveNowModal);
                }
            });

            document.getElementById('closeArchiveNowBtn').addEventListener('click', () => closeModal(archiveNowModal));
            document.getElementById('cancelArchiveNowBtn').addEventListener('click', () => closeModal(archiveNowModal));
            document.getElementById('confirmArchiveNowBtn').addEventListener('click', () => {
                const docName = document.getElementById('archiveDocName').textContent;
                const reason = document.getElementById('archiveReason').value;
                
                // In a real app, this would make an API call
                closeModal(archiveNowModal);
                Swal.fire({
                    icon: 'success',
                    title: 'Document Archived',
                    text: `${docName} has been archived successfully.`,
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            // Restore Archived Modal
            document.addEventListener('click', function(e) {
                if (e.target.closest('.restore-archived-btn')) {
                    const btn = e.target.closest('.restore-archived-btn');
                    const docName = btn.dataset.name;
                    document.getElementById('restoreDocName').textContent = docName;
                    openModal(restoreArchivedModal);
                }
            });

            document.getElementById('closeRestoreArchivedBtn').addEventListener('click', () => closeModal(restoreArchivedModal));
            document.getElementById('cancelRestoreArchivedBtn').addEventListener('click', () => closeModal(restoreArchivedModal));
            document.getElementById('confirmRestoreArchivedBtn').addEventListener('click', () => {
                const docName = document.getElementById('restoreDocName').textContent;
                closeModal(restoreArchivedModal);
                Swal.fire({
                    icon: 'success',
                    title: 'Document Restored',
                    text: `${docName} has been restored to upcoming list.`,
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            // Delete Archived Modal
            document.addEventListener('click', function(e) {
                if (e.target.closest('.delete-archived-btn')) {
                    const btn = e.target.closest('.delete-archived-btn');
                    const docName = btn.dataset.name;
                    document.getElementById('deleteDocName').textContent = docName;
                    openModal(deleteArchivedModal);
                }
            });

            document.getElementById('closeDeleteArchivedBtn').addEventListener('click', () => closeModal(deleteArchivedModal));
            document.getElementById('cancelDeleteArchivedBtn').addEventListener('click', () => closeModal(deleteArchivedModal));
            document.getElementById('confirmDeleteArchivedBtn').addEventListener('click', () => {
                const docName = document.getElementById('deleteDocName').textContent;
                closeModal(deleteArchivedModal);
                Swal.fire({
                    icon: 'success',
                    title: 'Document Deleted',
                    text: `${docName} has been permanently deleted.`,
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            // Close modals when clicking outside
            const modals = [extendRetentionModal, archiveNowModal, restoreArchivedModal, deleteArchivedModal];
            modals.forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeModal(this);
                    }
                });
            });

            // Document lock/unlock functionality (similar to Access Control)
            function applyDocumentMasking() {
                const isLocked = localStorage.getItem('documentsLocked') === 'true';
                const docNameElements = document.querySelectorAll('.doc-name');
                
                docNameElements.forEach(el => {
                    const realName = el.dataset.name || el.textContent;
                    if (isLocked) {
                        // Store original if not already
                        if (!el.dataset.original) {
                            el.dataset.original = realName;
                        }
                        // Apply masking
                        const masked = realName.replace(/./g, '*');
                        el.textContent = masked;
                        el.classList.add('locked');
                    } else {
                        // Restore original
                        if (el.dataset.original) {
                            el.textContent = el.dataset.original;
                            el.classList.remove('locked');
                        }
                    }
                });
            }

            // Check on load
            applyDocumentMasking();

            // Listen for changes
            window.addEventListener('storage', function(e) {
                if (e.key === 'documentsLocked') {
                    applyDocumentMasking();
                }
            });
        });
    </script>
</body>
</html>