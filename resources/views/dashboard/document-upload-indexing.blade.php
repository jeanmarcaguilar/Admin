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

        .category-card {
            transition: all 0.2s ease-in-out;
            border: 1px solid #e5e7eb;
        }

        .category-card:hover {
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

        .drop-zone {
            border: 2px dashed #d1d5db;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
        }

        .drop-zone.dragover {
            border-color: #059669;
            background-color: #f0fdf4;
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
                    <a href="{{ route('document.upload.indexing') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
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
                <h1 class="text-lg font-bold text-gray-800 hidden md:block">Document Upload & Indexing</h1>
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
                            <h1 class="text-2xl font-bold text-gray-900">Document Upload & Indexing</h1>
                            <p class="text-gray-600 mt-1">Upload, organize, and index documents with metadata</p>
                        </div>
                        <div class="mt-4 md:mt-0 flex space-x-3">
                            <button id="uploadDocumentsBtn" class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors font-medium flex items-center">
                                <i class="fas fa-upload mr-2"></i> Upload Documents
                            </button>
                            <button id="exportBtn" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                <i class="fas fa-download mr-2"></i> Export
                            </button>
                        </div>
                    </div>

                    <!-- Confidential Banner -->
                    <div class="mt-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-lg px-4 py-3">
                        <div class="text-sm font-medium">Confidential: Authorized personnel only. OTP required for sensitive actions.</div>
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
                                <i class='bx bx-check-circle mr-1'></i> Verified
                            </button>
                            <button class="px-3 py-1.5 text-sm font-medium bg-amber-50 text-amber-700 rounded-full hover:bg-amber-100 transition-colors">
                                <i class='bx bx-time-five mr-1'></i> Pending
                            </button>
                            <button class="px-3 py-1.5 text-sm font-medium bg-red-50 text-red-700 rounded-full hover:bg-red-100 transition-colors">
                                <i class='bx bx-error mr-1'></i> Expired
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Document Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Total Documents Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Total Documents</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ count($documents) }}</p>
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

                    <!-- Recent Uploads Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Recent Uploads</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">
                                    {{ count(array_filter($documents, function($doc) {
                                        $uploadDate = $doc['uploaded'] ?? null;
                                        return $uploadDate && strtotime($uploadDate) > strtotime('-7 days');
                                    })) }}
                                </p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <i class="bx bx-time-five mr-1"></i>
                                        This Week
                                    </span>
                                    <span class="text-xs text-gray-500">New</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="bx bx-upload text-white text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Review Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-violet-50 to-violet-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Pending Review</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">
                                    {{ count(array_filter($documents, function($doc) {
                                        return ($doc['status'] ?? '') === 'pending';
                                    })) }}
                                </p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800">
                                        <i class="bx bx-time mr-1"></i>
                                        Waiting
                                    </span>
                                    <span class="text-xs text-gray-500">Review</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="bx bx-check-shield text-white text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Browse -->
                <div class="mb-8">
                    <h3 class="font-semibold text-lg text-gray-900 mb-4">
                        <i class='bx bx-category mr-2'></i>Browse by Category
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 gap-4">
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3 active" data-category="all">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-700 flex items-center justify-center">
                                <i class="bx bx-grid-alt text-xl"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">All Documents</div>
                                <div class="text-xs text-gray-500">View all documents</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="financial">
                            <div class="w-10 h-10 rounded-lg bg-green-100 text-green-700 flex items-center justify-center">
                                <i class="bx bx-dollar text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Financial</div>
                                <div class="text-xs text-gray-500">Budgets, invoices, reports</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="hr">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                                <i class="bx bx-id-card text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Human Resources</div>
                                <div class="text-xs text-gray-500">Employee files, policies</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="legal">
                            <div class="w-10 h-10 rounded-lg bg-yellow-100 text-yellow-700 flex items-center justify-center">
                                <i class="bx bx-gavel text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Legal</div>
                                <div class="text-xs text-gray-500">Contracts, case files</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="operations">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center">
                                <i class="bx bx-cog text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Operations</div>
                                <div class="text-xs text-gray-500">Processes, procedures</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="contracts">
                            <div class="w-10 h-10 rounded-lg bg-red-100 text-red-700 flex items-center justify-center">
                                <i class="bx bx-file text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Contracts</div>
                                <div class="text-xs text-gray-500">Agreements, NDAs</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="marketing">
                            <div class="w-10 h-10 rounded-lg bg-pink-100 text-pink-700 flex items-center justify-center">
                                <i class="bx bx-megaphone text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Marketing</div>
                                <div class="text-xs text-gray-500">Campaigns, materials</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="it">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center">
                                <i class="bx bx-laptop text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">IT & Technology</div>
                                <div class="text-xs text-gray-500">Software, hardware, systems</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="compliance">
                            <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center">
                                <i class="bx bx-shield text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Compliance</div>
                                <div class="text-xs text-gray-500">Regulations, audits</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="research">
                            <div class="w-10 h-10 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center">
                                <i class="bx bx-search text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Research</div>
                                <div class="text-xs text-gray-500">Studies, analysis, data</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="training">
                            <div class="w-10 h-10 rounded-lg bg-cyan-100 text-cyan-700 flex items-center justify-center">
                                <i class="bx bx-book text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Training</div>
                                <div class="text-xs text-gray-500">Manuals, guides, courses</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="safety">
                            <div class="w-10 h-10 rounded-lg bg-orange-100 text-orange-700 flex items-center justify-center">
                                <i class="bx bx-shield-alt text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Safety</div>
                                <div class="text-xs text-gray-500">Procedures, incidents</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="procurement">
                            <div class="w-10 h-10 rounded-lg bg-lime-100 text-lime-700 flex items-center justify-center">
                                <i class="bx bx-shopping-bag text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Procurement</div>
                                <div class="text-xs text-gray-500">Vendors, purchases</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="quality">
                            <div class="w-10 h-10 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center">
                                <i class="bx bx-award text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Quality</div>
                                <div class="text-xs text-gray-500">Standards, testing</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="project">
                            <div class="w-10 h-10 rounded-lg bg-violet-100 text-violet-700 flex items-center justify-center">
                                <i class="bx bx-folder text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Project</div>
                                <div class="text-xs text-gray-500">Plans, reports, deliverables</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="administrative">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                                <i class="bx bx-building text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Administrative</div>
                                <div class="text-xs text-gray-500">Forms, records, policies</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="communication">
                            <div class="w-10 h-10 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center">
                                <i class="bx bx-envelope text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Communication</div>
                                <div class="text-xs text-gray-500">Letters, memos, notices</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="facilities">
                            <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                <i class="bx bx-home text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Facilities</div>
                                <div class="text-xs text-gray-500">Maintenance, utilities</div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Documents Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-900">All Documents</h3>
                            <p class="text-sm text-gray-500">Showing <span id="visibleCount">{{ count($documents) }}</span> of <span id="totalCount">{{ count($documents) }}</span> documents</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="text-sm text-brand-primary hover:text-brand-primary-hover font-medium" id="refreshBtn">
                                <i class="fas fa-sync-alt mr-1"></i> Refresh
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table id="documentsTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($documents as $doc)
                                    @php
                                        $dtype = strtoupper($doc['type'] ?? '');
                                        $icon = in_array($dtype, ['PDF']) ? 'bxs-file-pdf text-red-500' : (in_array($dtype, ['WORD','DOC','DOCX']) ? 'bxs-file-doc text-blue-500' : (in_array($dtype, ['EXCEL','XLS','XLSX']) ? 'bxs-file-txt text-green-500' : 'bxs-file text-gray-500'));
                                        $rawCategory = $doc['category'] ?? ($doc['type'] ?? 'Other');
                                        $categoryKey = strtolower($rawCategory);
                                        $displayCategory = $categoryKey === 'hr' ? 'HR' : ucfirst($categoryKey);
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
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <span class="doc-name" data-name="{{ $doc['name'] }}">{{ $doc['name'] }}</span>
                                                    </div>
                                                    <div class="text-xs text-gray-500">{{ ($doc['size'] ?? '') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $dtype }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ $displayCategory }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $doc['uploaded'] ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button onclick="showDownloadDocumentModal({{ json_encode($doc) }})" 
                                                    class="text-brand-primary hover:text-brand-primary-hover mr-3" title="Download">
                                                <i class="bx bx-download"></i>
                                            </button>
                                            <button onclick="showDocumentDetails({{ json_encode($doc) }})" 
                                                    class="text-blue-600 hover:text-blue-800 mr-3" title="View">
                                                <i class="bx bx-show"></i>
                                            </button>
                                            <button onclick="showShareDocumentModal({{ json_encode($doc) }})" 
                                                    class="text-green-600 hover:text-green-800 mr-3" title="Share">
                                                <i class="bx bx-share-alt"></i>
                                            </button>
                                            <button onclick="showDeleteDocumentConfirmation('{{ $doc['id'] ?? '' }}')" 
                                                    class="text-red-600 hover:text-red-800" title="Delete">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">No documents available. Click "Upload Documents" to add your first document.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="bg-white px-6 py-3 flex items-center justify-between border-t border-gray-200">
                        <div class="text-sm text-gray-700">
                            Showing 1 to {{ count($documents) }} of {{ count($documents) }} results
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary disabled:opacity-50" disabled>
                                Previous
                            </button>
                            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary disabled:opacity-50" disabled>
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODALS -->

    <!-- Upload Documents Modal -->
    <div id="uploadDocumentsModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="upload-documents-title">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="upload-documents-title" class="text-lg font-semibold text-gray-900">Upload Documents</h3>
                <button id="closeUploadDocumentsBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="uploadForm" class="p-6 space-y-6">
                <!-- Document Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="docTitle" class="block text-sm font-medium text-gray-700 mb-2">Document Title *</label>
                        <input type="text" id="docTitle" name="docTitle" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent"
                               placeholder="Enter document title">
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select id="category" name="category" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                            <option value="">Select Category</option>
                            <option value="financial">Financial</option>
                            <option value="hr">HR</option>
                            <option value="legal">Legal</option>
                            <option value="operations">Operations</option>
                            <option value="contracts">Contracts</option>
                            <option value="utilities">Utilities</option>
                            <option value="projects">Projects</option>
                            <option value="procurement">Procurement</option>
                            <option value="it">IT</option>
                            <option value="payroll">Payroll</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="docType" class="block text-sm font-medium text-gray-700 mb-2">Document Type *</label>
                        <select id="docType" name="docType" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                            <option value="">Select Document Type</option>
                            <option value="internal">Internal Document</option>
                            <option value="payment">Payment</option>
                            <option value="vendor">Vendor Document</option>
                            <option value="release_of_funds">Release of Funds</option>
                            <option value="purchase">Purchase Order</option>
                            <option value="disbursement">Disbursement</option>
                            <option value="receipt">Receipt</option>
                        </select>
                    </div>
                    <div>
                        <label for="docStatus" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="docStatus" name="docStatus"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                            <option value="active">Active</option>
                            <option value="pending">Pending Review</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="docDescription" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="docDescription" name="docDescription" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent"
                              placeholder="Enter document description (optional)"></textarea>
                </div>

                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Files *</label>
                    <div id="dropZone" class="drop-zone rounded-xl p-8 text-center hover:border-gray-400 transition-colors">
                        <i class="bx bx-cloud-upload text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-600 mb-4">Drag and drop files here or click to browse</p>
                        <input type="file" id="documentFiles" name="documentFiles[]" class="hidden" multiple 
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                        <label for="documentFiles" class="inline-flex items-center px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover cursor-pointer transition-colors focus:outline-none focus:ring-2 focus:ring-brand-primary focus:ring-offset-2">
                            <i class="bx bx-upload mr-2"></i>
                            Select Files
                        </label>
                        <p class="text-xs text-gray-500 mt-3">Supports PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX (Max 50MB per file)</p>
                        <div id="selectedFiles" class="mt-4 text-left space-y-2"></div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" id="cancelUploadBtn" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover focus:outline-none focus:ring-2 focus:ring-brand-primary">
                        <i class="bx bx-upload mr-2"></i>
                        Upload Documents
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Document Details Modal -->
    <div id="documentModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="document-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="document-modal-title" class="text-lg font-semibold text-gray-900">Document Details</h3>
                <button id="closeDocumentModalBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6" id="documentDetailsContent">
                <!-- Content will be loaded by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Download Document Modal -->
    <div id="downloadDocumentModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="download-document-title">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="download-document-title" class="text-lg font-semibold text-gray-900">Download Document</h3>
                <button id="closeDownloadModalBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <div class="text-sm text-gray-500">File</div>
                    <div id="downloadDocName" class="text-sm font-medium text-gray-900">—</div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <div class="text-xs text-gray-500">Type</div>
                        <div id="downloadDocType" class="text-sm text-gray-900">—</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Size</div>
                        <div id="downloadDocSize" class="text-sm text-gray-900">—</div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelDownloadBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                    <button type="button" id="confirmDownloadBtn" onclick="performDownload()" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover">Download</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Share Document Modal -->
    <div id="shareDocumentModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="share-document-title">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="share-document-title" class="text-lg font-semibold text-gray-900">Share Document</h3>
                <button id="closeShareModalBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <div class="text-sm text-gray-500">File</div>
                    <div id="shareDocName" class="text-sm font-medium text-gray-900">—</div>
                </div>
                <div class="mb-4">
                    <label for="shareEmail" class="block text-xs text-gray-500 mb-1">Share with (email)</label>
                    <input id="shareEmail" type="email" placeholder="name@example.com" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary" />
                </div>
                <div class="mb-6">
                    <label class="block text-xs text-gray-500 mb-1">Share link</label>
                    <div class="flex">
                        <input id="shareLink" type="text" readonly class="flex-1 border border-gray-300 rounded-l-lg px-3 py-2 text-sm bg-gray-50" />
                        <button type="button" onclick="copyShareLink()" class="px-3 py-2 border border-gray-300 rounded-r-lg text-sm bg-white hover:bg-gray-50">Copy</button>
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelShareBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                    <button type="button" onclick="sendShareInvite()" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover">Send Invite</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Document Modal -->
    <div id="deleteDocumentModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="delete-document-title">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4 fade-in" role="document">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation text-red-600 text-xl"></i>
                </div>
                <h3 id="delete-document-title" class="text-lg font-medium text-gray-900 mb-2">Delete Document</h3>
                <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete this document? This action cannot be undone.</p>
                <div class="flex justify-center space-x-4">
                    <button type="button" id="cancelDeleteBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        No, Keep It
                    </button>
                    <button type="button" id="confirmDeleteBtn" class="px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                        Yes, Delete Document
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- OTP Modal -->
    <div id="otpModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="otp-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="otp-modal-title" class="text-lg font-semibold text-gray-900">Security Verification</h3>
                <button id="closeOtpModalBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-600 mb-4">Enter the 6-digit code to view confidential document details.</p>
                <div class="mb-4">
                    <label for="otpInput" class="block text-sm font-medium text-gray-700 mb-2">One-Time Password (OTP)</label>
                    <input
                        id="otpInput"
                        type="text"
                        inputmode="numeric"
                        maxlength="6"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-center tracking-[0.4em] focus:outline-none focus:ring-2 focus:ring-brand-primary"
                        placeholder="••••••"
                    >
                </div>
                <div class="flex justify-end space-x-3">
                    <button id="cancelOtpBtn" type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button id="verifyOtpBtn" type="button" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover">
                        Verify OTP
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
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

            // Open "Document Management" dropdown by default
            const documentBtn = document.getElementById('document-management-btn');
            const documentSubmenu = document.getElementById('document-submenu');
            const documentArrow = document.getElementById('document-arrow');
            
            if (documentSubmenu && !documentSubmenu.classList.contains('hidden')) {
                documentSubmenu.classList.remove('hidden');
                if (documentArrow) documentArrow.classList.add('rotate-180');
            }

            // Modal Management
            const uploadModal = document.getElementById("uploadDocumentsModal");
            const documentModal = document.getElementById("documentModal");
            const downloadModal = document.getElementById("downloadDocumentModal");
            const shareModal = document.getElementById("shareDocumentModal");
            const deleteModal = document.getElementById("deleteDocumentModal");
            const otpModal = document.getElementById("otpModal");

            // Open modals
            document.getElementById('uploadDocumentsBtn').addEventListener('click', () => openModal(uploadModal));
            
            // Close modal functions
            function openModal(modal) {
                modal.classList.remove('hidden');
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeModal(modal) {
                modal.classList.remove('active');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                }, 300);
            }

            // Close buttons
            document.getElementById('closeUploadDocumentsBtn').addEventListener('click', () => closeModal(uploadModal));
            document.getElementById('cancelUploadBtn').addEventListener('click', () => closeModal(uploadModal));
            document.getElementById('closeDocumentModalBtn').addEventListener('click', () => closeModal(documentModal));
            document.getElementById('closeDownloadModalBtn').addEventListener('click', () => closeModal(downloadModal));
            document.getElementById('cancelDownloadBtn').addEventListener('click', () => closeModal(downloadModal));
            document.getElementById('closeShareModalBtn').addEventListener('click', () => closeModal(shareModal));
            document.getElementById('cancelShareBtn').addEventListener('click', () => closeModal(shareModal));
            document.getElementById('closeOtpModalBtn').addEventListener('click', () => closeModal(otpModal));
            document.getElementById('cancelOtpBtn').addEventListener('click', () => closeModal(otpModal));
            document.getElementById('cancelDeleteBtn').addEventListener('click', () => closeModal(deleteModal));
            document.getElementById('closeDownloadModalBtn').addEventListener('click', () => closeModal(downloadModal));

            // Close modals when clicking outside
            const modals = [uploadModal, documentModal, downloadModal, shareModal, deleteModal, otpModal];
            modals.forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeModal(this);
                    }
                });
            });

            // File upload functionality
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('documentFiles');
            const selectedFilesDiv = document.getElementById('selectedFiles');

            // Drag and drop
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                dropZone.classList.add('dragover');
            }

            function unhighlight() {
                dropZone.classList.remove('dragover');
            }

            dropZone.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                fileInput.files = files;
                displaySelectedFiles(files);
            }

            fileInput.addEventListener('change', function() {
                displaySelectedFiles(this.files);
            });

            function displaySelectedFiles(files) {
                selectedFilesDiv.innerHTML = '';
                if (files.length > 0) {
                    Array.from(files).forEach(file => {
                        const fileItem = document.createElement('div');
                        fileItem.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
                        
                        const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                        fileItem.innerHTML = `
                            <div class="flex items-center">
                                <i class="bx bx-file text-gray-500 mr-2"></i>
                                <span class="text-sm text-gray-700">${file.name}</span>
                                <span class="text-xs text-gray-500 ml-2">(${fileSize})</span>
                            </div>
                            <button type="button" class="text-red-500 hover:text-red-700" onclick="removeFile(this, '${file.name}')">
                                <i class="bx bx-x"></i>
                            </button>
                        `;
                        selectedFilesDiv.appendChild(fileItem);
                    });
                }
            }

            window.removeFile = function(button, fileName) {
                const dt = new DataTransfer();
                const input = document.getElementById('documentFiles');
                const { files } = input;
                
                for (let i = 0; i < files.length; i++) {
                    if (files[i].name !== fileName) {
                        dt.items.add(files[i]);
                    }
                }
                
                input.files = dt.files;
                displaySelectedFiles(input.files);
            };

            // Form submission
            const uploadForm = document.getElementById('uploadForm');
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const files = fileInput.files;
                
                if (files.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'No Files Selected',
                        text: 'Please select at least one file to upload.',
                        confirmButtonColor: '#059669'
                    });
                    return;
                }
                
                // Show loading
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="bx bx-loader-alt animate-spin mr-2"></i> Uploading...';
                submitBtn.disabled = true;
                
                // Simulate upload (replace with actual API call)
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Upload Complete',
                        text: `${files.length} file(s) uploaded successfully.`,
                        confirmButtonColor: '#059669'
                    }).then(() => {
                        closeModal(uploadModal);
                        uploadForm.reset();
                        selectedFilesDiv.innerHTML = '';
                        window.location.reload();
                    });
                }, 2000);
            });

            // Category filtering
            const categoryCards = document.querySelectorAll('.category-card');
            categoryCards.forEach(card => {
                card.addEventListener('click', function() {
                    const selectedCategory = this.dataset.category;
                    
                    // Update active state
                    categoryCards.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Filter documents
                    filterDocumentsByCategory(selectedCategory);
                });
            });

            function filterDocumentsByCategory(category) {
                const rows = document.querySelectorAll('#documentsTable tbody tr');
                let visibleCount = 0;
                
                rows.forEach(row => {
                    if (row.querySelector('td[colspan]')) {
                        // Skip "No documents" row
                        return;
                    }
                    
                    const docCategory = row.dataset.category || '';
                    
                    if (category === 'all' || docCategory === category) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Update count
                document.getElementById('visibleCount').textContent = visibleCount;
            }

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#documentsTable tbody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
            }

            // Document actions
            window.showDocumentDetails = function(doc) {
                const contentDiv = document.getElementById('documentDetailsContent');
                contentDiv.innerHTML = `
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-medium text-gray-900">${doc.name}</h4>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">${doc.type}</span>
                        </div>
                        <div class="border-t border-b border-gray-200 py-4">
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Category</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${doc.category || '—'}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Size</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${doc.size || '—'}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Uploaded</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${doc.uploaded || '—'}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900">Active</dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">${doc.description || 'No description provided.'}</dd>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal(documentModal)" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Close
                        </button>
                    </div>
                `;
                openModal(documentModal);
            };

            let currentDownloadDoc = null;
            window.showDownloadDocumentModal = function(doc) {
                currentDownloadDoc = doc;
                document.getElementById('downloadDocName').textContent = doc.name || '—';
                document.getElementById('downloadDocType').textContent = doc.type || '—';
                document.getElementById('downloadDocSize').textContent = doc.size || '—';
                openModal(downloadModal);
            };

            window.performDownload = function() {
                if (!currentDownloadDoc) return;
                
                Swal.fire({
                    icon: 'success',
                    title: 'Download Started',
                    text: `${currentDownloadDoc.name} is being downloaded.`,
                    timer: 2000,
                    showConfirmButton: false
                });
                closeModal(downloadModal);
            };

            let currentShareDoc = null;
            window.showShareDocumentModal = function(doc) {
                currentShareDoc = doc;
                document.getElementById('shareDocName').textContent = doc.name || '—';
                document.getElementById('shareLink').value = `${window.location.origin}/documents/${doc.id}`;
                document.getElementById('shareEmail').value = '';
                openModal(shareModal);
            };

            window.copyShareLink = function() {
                const input = document.getElementById('shareLink');
                input.select();
                document.execCommand('copy');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Copied!',
                    text: 'Link copied to clipboard.',
                    timer: 1500,
                    showConfirmButton: false
                });
            };

            window.sendShareInvite = function() {
                const email = document.getElementById('shareEmail').value.trim();
                if (!email) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Email Required',
                        text: 'Please enter an email address.',
                        confirmButtonColor: '#059669'
                    });
                    return;
                }
                
                Swal.fire({
                    icon: 'success',
                    title: 'Invite Sent',
                    text: `Share invite sent to ${email}.`,
                    timer: 2000,
                    showConfirmButton: false
                });
                closeModal(shareModal);
            };

            let currentDeleteId = null;
            window.showDeleteDocumentConfirmation = function(docId) {
                currentDeleteId = docId;
                openModal(deleteModal);
            };

            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Document Deleted',
                    text: 'The document has been deleted successfully.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    closeModal(deleteModal);
                    // In real app, you would make an API call here
                });
            });

            // Lock/Unlock functionality
            const lockAllBtn = document.getElementById('lockAllDocsBtn');
            const unlockAllBtn = document.getElementById('unlockAllBtn');
            
            if (lockAllBtn) {
                lockAllBtn.addEventListener('click', () => {
                    // Show OTP modal for verification
                    openModal(otpModal);
                });
            }

            if (unlockAllBtn) {
                unlockAllBtn.addEventListener('click', () => {
                    // Show OTP modal for verification
                    openModal(otpModal);
                });
            }

            document.getElementById('verifyOtpBtn').addEventListener('click', () => {
                const otp = document.getElementById('otpInput').value;
                if (otp.length === 6) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Verified',
                        text: 'OTP verified successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        closeModal(otpModal);
                        // Toggle lock state
                        const isLocked = lockAllBtn.innerHTML.includes('Lock');
                        if (isLocked) {
                            lockAllBtn.innerHTML = '<i class="fas fa-unlock mr-2"></i> Unlock All';
                            unlockAllBtn.style.display = 'none';
                        } else {
                            lockAllBtn.innerHTML = '<i class="fas fa-lock mr-2"></i> Lock All';
                            unlockAllBtn.style.display = 'inline-flex';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid OTP',
                        text: 'Please enter a valid 6-digit code.',
                        confirmButtonColor: '#059669'
                    });
                }
            });

            // Export functionality
            document.getElementById('exportBtn').addEventListener('click', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Export Started',
                    text: 'Your document list export has been queued. You will receive an email when it\'s ready.',
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            // Refresh functionality
            document.getElementById('refreshBtn').addEventListener('click', () => {
                window.location.reload();
            });
        });
    </script>
</body>
</html>