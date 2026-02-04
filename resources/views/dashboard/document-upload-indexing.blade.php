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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
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
            cursor: pointer;
            user-select: none;
        }

        .category-card:hover {
            border-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .category-card:active {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .category-card.active {
            border-color: #059669;
            background-color: #f0fdf4;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.15);
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
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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
                        Microfinance Admin
                    </div>
                    <div
                        class="text-[11px] text-gray-500 font-semibold uppercase group-hover:text-brand-primary transition-colors">
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
                <svg id="document-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300 rotate-180"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="document-submenu" class="submenu mt-1">
                <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                    <a href="{{ route('document.upload.indexing') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <svg id="facilities-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="facilities-submenu" class="submenu mt-1 hidden">
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
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    Microfinance Admin © {{ date('Y') }}<br />
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

            </div>

            <div class="flex items-center gap-3 sm:gap-5">
                <!-- Clock pill -->
                <span id="real-time-clock" data-server-timestamp="{{ now()->timestamp * 1000 }}"
                    class="text-xs font-bold text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                    {{ now()->format('g:i:s A') }}
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
                            <button id="uploadDocumentsBtn"
                                class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors font-medium flex items-center">
                                <i class="fas fa-upload mr-2"></i> Upload Documents
                            </button>
                            <button id="exportBtn"
                                class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                <i class="fas fa-download mr-2"></i> Export
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
                        <input type="text" id="searchInput"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-primary focus:border-brand-primary block w-full pl-12 pr-4 py-3"
                            placeholder="Search documents...">
                    </div>
                </div>

                <!-- Enhanced Document Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Total Documents Card -->
                    <div
                        class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div
                            class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                        </div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Total Documents</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ count($documents) }}</p>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="bx bx-file mr-1"></i>
                                        Files
                                    </span>
                                    <span class="text-xs text-gray-500">Total</span>
                                </div>
                            </div>
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="bx bx-file text-white text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Uploads Card -->
                    <div
                        class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div
                            class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                        </div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Recent Uploads</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">
                                    {{ count(array_filter($documents, function ($doc) {
    $uploadDate = $doc['uploaded'] ?? null;
    return $uploadDate && strtotime($uploadDate) > strtotime('-7 days');
})) }}
                                </p>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <i class="bx bx-time-five mr-1"></i>
                                        This Week
                                    </span>
                                    <span class="text-xs text-gray-500">New</span>
                                </div>
                            </div>
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="bx bx-upload text-white text-xl"></i>
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
                        <button type="button"
                            class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3 active"
                            data-category="all">
                            <div
                                class="w-10 h-10 rounded-lg bg-gray-100 text-gray-700 flex items-center justify-center">
                                <i class="bx bx-grid-alt text-xl"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">All Documents</div>
                                <div class="text-xs text-gray-500">View all documents</div>
                            </div>
                        </button>
                        <button type="button"
                            class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3"
                            data-category="financial">
                            <div
                                class="w-10 h-10 rounded-lg bg-green-100 text-green-700 flex items-center justify-center">
                                <span class="text-xl font-bold">₱</span>
                            </div>
                            <div>
                                <div class="font-medium">Financial</div>
                                <div class="text-xs text-gray-500">Budgets, invoices, reports</div>
                            </div>
                        </button>
                        <button type="button"
                            class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3"
                            data-category="hr">
                            <div
                                class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                                <i class="bx bx-id-card text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Human Resources</div>
                                <div class="text-xs text-gray-500">Employee files, policies</div>
                            </div>
                        </button>
                        <button type="button"
                            class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3"
                            data-category="legal">
                            <div
                                class="w-10 h-10 rounded-lg bg-yellow-100 text-yellow-700 flex items-center justify-center">
                                <i class="bx bx-gavel text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Legal</div>
                                <div class="text-xs text-gray-500">Contracts, case files</div>
                            </div>
                        </button>
                        <button type="button"
                            class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3"
                            data-category="operations">
                            <div
                                class="w-10 h-10 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center">
                                <i class="bx bx-cog text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Operations</div>
                                <div class="text-xs text-gray-500">Processes, procedures</div>
                            </div>
                        </button>
                        <button type="button"
                            class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3"
                            data-category="contracts">
                            <div class="w-10 h-10 rounded-lg bg-red-100 text-red-700 flex items-center justify-center">
                                <i class="bx bx-file text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Contracts</div>
                                <div class="text-xs text-gray-500">Agreements, NDAs</div>
                            </div>
                        </button>
                        <button type="button"
                            class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3"
                            data-category="utilities">
                            <div
                                class="w-10 h-10 rounded-lg bg-orange-100 text-orange-700 flex items-center justify-center">
                                <i class="bx bx-bolt text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Utilities</div>
                                <div class="text-xs text-gray-500">Electricity, water, gas</div>
                            </div>
                        </button>
                        <button type="button"
                            class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3"
                            data-category="projects">
                            <div
                                class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center">
                                <i class="bx bx-folder text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Projects</div>
                                <div class="text-xs text-gray-500">Project plans, reports</div>
                            </div>
                        </button>
                        <button type="button"
                            class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3"
                            data-category="procurement">
                            <div
                                class="w-10 h-10 rounded-lg bg-lime-100 text-lime-700 flex items-center justify-center">
                                <i class="bx bx-shopping-bag text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Procurement</div>
                                <div class="text-xs text-gray-500">Vendors, purchases</div>
                            </div>
                        </button>
                        <button type="button"
                            class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3"
                            data-category="it">
                            <div
                                class="w-10 h-10 rounded-lg bg-cyan-100 text-cyan-700 flex items-center justify-center">
                                <i class="bx bx-laptop text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">IT</div>
                                <div class="text-xs text-gray-500">Software, hardware</div>
                            </div>
                        </button>
                        <button type="button"
                            class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3"
                            data-category="payroll">
                            <div
                                class="w-10 h-10 rounded-lg bg-pink-100 text-pink-700 flex items-center justify-center">
                                <i class="bx bx-money text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">Payroll</div>
                                <div class="text-xs text-gray-500">Salaries, compensation</div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Documents Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-900">All Documents</h3>
                            <p class="text-sm text-gray-500">Showing <span
                                    id="visibleCount">{{ count($documents) }}</span> of <span
                                    id="totalCount">{{ count($documents) }}</span> documents</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="text-sm text-brand-primary hover:text-brand-primary-hover font-medium"
                                id="refreshBtn">
                                <i class="fas fa-sync-alt mr-1"></i> Refresh
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="documentsTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Name</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Uploaded</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($documents as $doc)
                                    @php
                                        $dtype = strtoupper($doc['type'] ?? '');
                                        $icon = in_array($dtype, ['PDF']) ? 'bxs-file-pdf text-red-500' : (in_array($dtype, ['WORD', 'DOC', 'DOCX']) ? 'bxs-file-doc text-blue-500' : (in_array($dtype, ['EXCEL', 'XLS', 'XLSX']) ? 'bxs-file-txt text-green-500' : 'bxs-file text-gray-500'));
                                        $rawCategory = $doc['category'] ?? ($doc['type'] ?? 'Other');
                                        $categoryKey = strtolower($rawCategory);
                                        $displayCategory = $categoryKey === 'hr' ? 'HR' : ucfirst($categoryKey);
                                        // Backward compat: if raw type is not a known category, keep original for display
                                        if (!in_array($categoryKey, ['financial', 'hr', 'legal', 'operations'])) {
                                            $displayCategory = $doc['type'] ?? 'Other';
                                        }
                                    @endphp
                                    <tr class="document-row" data-category="{{ $categoryKey }}"
                                        data-type="{{ strtolower($doc['type'] ?? 'other') }}"
                                        data-doc-id="{{ $doc['id'] ?? '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <i class='bx {{ $icon }} text-xl mr-3'></i>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <span class="doc-name"
                                                            data-name="{{ $doc['name'] }}">{{ $doc['name'] }}</span>
                                                    </div>
                                                    <div class="text-xs text-gray-500">{{ ($doc['size'] ?? '') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $dtype }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ $displayCategory }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $doc['uploaded'] ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button onclick="showDownloadDocumentModal({{ json_encode($doc) }})"
                                                class="text-brand-primary hover:text-brand-primary-hover mr-3"
                                                title="Download">
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
                                        <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">No documents
                                            available. Click "Upload Documents" to add your first document.</td>
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
                            <button
                                class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary disabled:opacity-50"
                                disabled>
                                Previous
                            </button>
                            <button
                                class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary disabled:opacity-50"
                                disabled>
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
    <div id="uploadDocumentsModal" class="modal hidden" aria-modal="true" role="dialog"
        aria-labelledby="upload-documents-title">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[80vh] overflow-y-auto mx-4 fade-in"
            role="document">
            <!-- Modal Header -->
            <div
                class="bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 px-4 py-3 rounded-t-xl relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                <div class="relative flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <div
                            class="w-8 h-8 bg-white/25 rounded-lg flex items-center justify-center backdrop-blur-sm shadow">
                            <i class="bx bx-cloud-upload text-white text-sm"></i>
                        </div>
                        <div>
                            <h3 id="upload-documents-title" class="text-base font-bold text-white">Upload Documents</h3>
                            <p class="text-emerald-100 text-xs">Share your files securely</p>
                        </div>
                    </div>
                    <button id="closeUploadDocumentsBtn" type="button"
                        class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-1 transition-all duration-200"
                        aria-label="Close">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form id="uploadForm" class="p-4">
                <!-- Single Column Layout for Better Organization -->
                <div class="space-y-4">
                    <!-- Document Details -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                            <div
                                class="w-6 h-6 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded flex items-center justify-center">
                                <i class="bx bx-file text-white text-xs"></i>
                            </div>
                            <h4 class="text-sm font-bold text-gray-900">Document Details</h4>
                        </div>

                        <div class="space-y-3">
                            <!-- Title Field -->
                            <div>
                                <label for="docTitle"
                                    class="block text-xs font-bold text-gray-700 mb-1 flex items-center gap-1">
                                    <i class="bx bx-edit-alt text-emerald-500 text-xs"></i>
                                    Document Title <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input type="text" id="docTitle" name="docTitle" required
                                    class="w-full border border-gray-200 rounded px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white hover:border-gray-300"
                                    placeholder="Enter document title">
                            </div>

                            <!-- Category and Type Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label for="category"
                                        class="block text-xs font-bold text-gray-700 mb-1 flex items-center gap-1">
                                        <i class="bx bx-category text-emerald-500 text-xs"></i>
                                        Category <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <select id="category" name="category" required
                                        class="w-full border border-gray-200 rounded px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white hover:border-gray-300">
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
                                <div>
                                    <label for="docType"
                                        class="block text-xs font-bold text-gray-700 mb-1 flex items-center gap-1">
                                        <i class="bx bx-file-blank text-emerald-500 text-xs"></i>
                                        Document Type <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <select id="docType" name="docType" required
                                        class="w-full border border-gray-200 rounded px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white hover:border-gray-300">
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
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="docDescription"
                                    class="block text-xs font-bold text-gray-700 mb-1 flex items-center gap-1">
                                    <i class="bx bx-message-square-detail text-emerald-500 text-xs"></i>
                                    Description
                                </label>
                                <textarea id="docDescription" name="docDescription" rows="2"
                                    class="w-full border border-gray-200 rounded px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white resize-none hover:border-gray-300"
                                    placeholder="Enter document description (optional)"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div
                        class="bg-gradient-to-br from-emerald-50 via-white to-teal-50 border border-emerald-200 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-emerald-200">
                            <div
                                class="w-6 h-6 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded flex items-center justify-center">
                                <i class="bx bx-upload text-white text-xs"></i>
                            </div>
                            <h4 class="text-sm font-bold text-gray-900">Upload Files</h4>
                        </div>

                        <div id="dropZone"
                            class="border border-dashed border-emerald-300 rounded-lg p-6 text-center hover:border-emerald-400 hover:bg-emerald-50 transition-all duration-200 cursor-pointer">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="bx bx-cloud-upload text-emerald-600 text-lg"></i>
                            </div>
                            <p class="text-gray-700 font-semibold text-sm mb-1">Drag and drop files here</p>
                            <p class="text-gray-500 text-xs mb-3">or click to browse</p>
                            <input type="file" id="documentFiles" name="documents[]" class="hidden" multiple
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                            <label for="documentFiles"
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded hover:from-emerald-600 hover:to-emerald-700 cursor-pointer transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 shadow hover:shadow-xl transform hover:-translate-y-0.5 text-xs font-medium">
                                <i class="bx bx-folder-open mr-1"></i>
                                Choose Files
                            </label>
                            <p class="text-xs text-gray-500 mt-2">Supports: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX (Max
                                50MB per file)</p>
                            <div id="selectedFiles" class="mt-3 text-left space-y-1"></div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-center gap-3 pt-4 border-t border-gray-100">
                        <button type="button" id="cancelUploadBtn"
                            class="group px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded hover:border-gray-300 hover:bg-gray-50 transition-all duration-200 flex items-center">
                            <i class="bx bx-x text-sm mr-1 group-hover:text-red-500 transition-colors"></i>
                            <span class="font-semibold text-xs">Cancel</span>
                        </button>
                        <button type="submit"
                            class="group px-6 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 flex items-center shadow hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="bx bx-upload text-sm mr-1 group-hover:animate-bounce"></i>
                            <span class="font-bold text-xs">Upload Documents</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Document Details Modal -->
    <div id="documentModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="document-modal-title">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 fade-in transform transition-all duration-300"
            role="document">
            <!-- Modal Header with Gradient -->
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-t-2xl px-6 py-5">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-alt text-white text-lg"></i>
                        </div>
                        <h3 id="document-modal-title" class="text-xl font-bold text-white">Document Details</h3>
                    </div>
                    <button id="closeDocumentModalBtn" type="button"
                        class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all duration-200 backdrop-blur-sm"
                        aria-label="Close">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="p-6 bg-gradient-to-br from-gray-50 to-white" id="documentDetailsContent">
                <!-- Content will be loaded by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Download Document Modal -->
    <div id="downloadDocumentModal" class="modal hidden" aria-modal="true" role="dialog"
        aria-labelledby="download-document-title">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 fade-in transform transition-all duration-300"
            role="document">
            <!-- Modal Header with Gradient -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-t-2xl px-6 py-5">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                            <i class="fas fa-download text-white text-lg"></i>
                        </div>
                        <h3 id="download-document-title" class="text-xl font-bold text-white">Download Document</h3>
                    </div>
                    <button id="closeDownloadModalBtn" type="button"
                        class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all duration-200 backdrop-blur-sm"
                        aria-label="Close">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="p-6 bg-gradient-to-br from-blue-50 to-white">
                <!-- File Preview Card -->
                <div class="bg-white rounded-xl p-4 shadow-sm border border-blue-100 mb-6">
                    <div class="flex items-center space-x-3 mb-3">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file text-blue-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-xs text-gray-500 font-medium">File Name</div>
                            <div id="downloadDocName" class="text-sm font-semibold text-gray-900 truncate">—</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="text-xs text-gray-500 font-medium mb-1">Type</div>
                            <div id="downloadDocType" class="text-sm font-semibold text-gray-900">—</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="text-xs text-gray-500 font-medium mb-1">Size</div>
                            <div id="downloadDocSize" class="text-sm font-semibold text-gray-900">—</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelDownloadBtn"
                        class="px-5 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm">
                        Cancel
                    </button>
                    <button type="button" id="confirmDownloadBtn" onclick="performDownload()"
                        class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl text-sm font-medium hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                        <i class="fas fa-download mr-2"></i>
                        Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Share Document Modal -->
    <div id="shareDocumentModal" class="modal hidden" aria-modal="true" role="dialog"
        aria-labelledby="share-document-title">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 fade-in transform transition-all duration-300"
            role="document">
            <!-- Modal Header with Gradient -->
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-t-2xl px-6 py-5">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                            <i class="fas fa-share-alt text-white text-lg"></i>
                        </div>
                        <h3 id="share-document-title" class="text-xl font-bold text-white">Share Document</h3>
                    </div>
                    <button id="closeShareModalBtn" type="button"
                        class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all duration-200 backdrop-blur-sm"
                        aria-label="Close">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="p-6 bg-gradient-to-br from-purple-50 to-white">
                <!-- File Info Card -->
                <div class="bg-white rounded-xl p-4 shadow-sm border border-purple-100 mb-6">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-purple-100 to-pink-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file text-purple-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-xs text-gray-500 font-medium">File Name</div>
                            <div id="shareDocName" class="text-sm font-semibold text-gray-900 truncate">—</div>
                        </div>
                    </div>
                </div>

                <!-- Email Input -->
                <div class="mb-6">
                    <label for="shareEmail" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-envelope text-purple-500 mr-2"></i>
                        Share with (email)
                    </label>
                    <div class="relative">
                        <input id="shareEmail" type="email" placeholder="name@example.com"
                            class="w-full border-2 border-purple-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 pl-10" />
                        <i class="fas fa-envelope absolute left-3 top-3.5 text-purple-400"></i>
                    </div>
                </div>

                <!-- Share Link -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-link text-purple-500 mr-2"></i>
                        Share link
                    </label>
                    <div class="flex">
                        <div class="relative flex-1">
                            <input id="shareLink" type="text" readonly
                                class="flex-1 border-2 border-purple-200 rounded-l-xl px-4 py-3 text-sm bg-gray-50 font-mono text-xs focus:outline-none" />
                            <i class="fas fa-link absolute left-3 top-3.5 text-purple-400"></i>
                        </div>
                        <button type="button" onclick="copyShareLink()"
                            class="px-4 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-r-xl text-sm font-medium hover:from-purple-600 hover:to-pink-600 transition-all duration-200 flex items-center">
                            <i class="fas fa-copy mr-2"></i>
                            Copy
                        </button>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelShareBtn"
                        class="px-5 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm">
                        Cancel
                    </button>
                    <button type="button" onclick="sendShareInvite()"
                        class="px-5 py-2.5 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-xl text-sm font-medium hover:from-purple-600 hover:to-pink-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Send Invite
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Document Modal -->
    <div id="deleteDocumentModal" class="modal hidden" aria-modal="true" role="dialog"
        aria-labelledby="delete-document-title">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 fade-in transform transition-all duration-300"
            role="document">
            <!-- Modal Header with Gradient -->
            <div class="bg-gradient-to-r from-red-500 to-rose-600 rounded-t-2xl px-6 py-5">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                            <i class="fas fa-trash text-white text-lg"></i>
                        </div>
                        <h3 id="delete-document-title" class="text-xl font-bold text-white">Delete Document</h3>
                    </div>
                    <button type="button"
                        class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all duration-200 backdrop-blur-sm"
                        onclick="closeDeleteDocumentModal()" aria-label="Close">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="p-6 bg-gradient-to-br from-red-50 to-white text-center">
                <!-- Warning Icon -->
                <div
                    class="mx-auto w-20 h-20 bg-gradient-to-br from-red-100 to-rose-100 rounded-full flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-exclamation-triangle text-red-600 text-3xl animate-pulse"></i>
                </div>

                <!-- Warning Message -->
                <h3 class="text-xl font-bold text-gray-900 mb-3">Are you absolutely sure?</h3>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    This action <span class="font-semibold text-red-600">cannot be undone</span>.
                    This will permanently delete the document and remove it from your records.
                </p>

                <!-- Action Buttons -->
                <div class="flex justify-center space-x-4">
                    <button type="button" id="cancelDeleteBtn"
                        class="px-6 py-3 border-2 border-gray-300 rounded-xl text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm">
                        <i class="fas fa-shield-alt mr-2"></i>
                        No, Keep It
                    </button>
                    <button type="button" id="confirmDeleteBtn"
                        class="px-6 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-xl text-sm font-semibold hover:from-red-600 hover:to-rose-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                        <i class="fas fa-trash mr-2"></i>
                        Yes, Delete Document
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- OTP Modal -->
    <div id="otpModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="otp-modal-title">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 fade-in transform transition-all duration-300"
            role="document">
            <!-- Modal Header with Gradient -->
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-t-2xl px-6 py-5">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                            <i class="fas fa-shield-alt text-white text-lg"></i>
                        </div>
                        <h3 id="otp-modal-title" class="text-xl font-bold text-white">Security Verification</h3>
                    </div>
                    <button id="closeOtpModalBtn" type="button"
                        class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all duration-200 backdrop-blur-sm"
                        aria-label="Close">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="p-6 bg-gradient-to-br from-amber-50 to-white">
                <!-- Security Icon -->
                <div
                    class="mx-auto w-16 h-16 bg-gradient-to-br from-amber-100 to-orange-100 rounded-full flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-lock text-amber-600 text-2xl"></i>
                </div>

                <!-- Instructions -->
                <p class="text-gray-600 mb-6 text-center leading-relaxed">
                    Enter the 6-digit code to view confidential document details. This extra step ensures your document
                    security.
                </p>

                <!-- OTP Input -->
                <div class="mb-8">
                    <label for="otpInput"
                        class="block text-sm font-semibold text-gray-700 mb-3 flex items-center justify-center">
                        <i class="fas fa-key text-amber-500 mr-2"></i>
                        One-Time Password (OTP)
                    </label>
                    <div class="relative">
                        <input id="otpInput" type="text" inputmode="numeric" maxlength="6"
                            class="w-full border-2 border-amber-200 rounded-xl px-4 py-4 text-2xl text-center tracking-[0.5em] font-mono font-bold focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-200 bg-white shadow-sm"
                            placeholder="••••••">
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="flex space-x-2">
                                <div class="w-1 h-8 bg-amber-200 rounded-full"></div>
                                <div class="w-1 h-8 bg-amber-200 rounded-full"></div>
                                <div class="w-1 h-8 bg-amber-200 rounded-full"></div>
                                <div class="w-1 h-8 bg-amber-200 rounded-full"></div>
                                <div class="w-1 h-8 bg-amber-200 rounded-full"></div>
                                <div class="w-1 h-8 bg-amber-200 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3">
                    <button id="cancelOtpBtn" type="button"
                        class="px-5 py-2.5 border-2 border-gray-300 rounded-xl text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </button>
                    <button id="verifyOtpBtn" type="button"
                        class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl text-sm font-semibold hover:from-amber-600 hover:to-orange-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        Verify OTP
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>

    <!-- JAVASCRIPT -->
    <script>
        // Simple test to check if JavaScript is working
        console.log('Script loaded successfully');

        document.addEventListener("DOMContentLoaded", () => {
            console.log('DOM Content Loaded - Starting main script');

            // Test basic button functionality
            const uploadBtn = document.getElementById('uploadDocumentsBtn');
            console.log('Upload button found:', !!uploadBtn);

            if (uploadBtn) {
                console.log('Adding click listener to upload button');
                uploadBtn.addEventListener('click', function () {
                    console.log('Upload button clicked!');

                    // Open upload modal instead of alert
                    const uploadModal = document.getElementById('uploadDocumentsModal');
                    if (uploadModal) {
                        uploadModal.classList.remove('hidden');
                        uploadModal.classList.add('active');
                        document.body.style.overflow = 'hidden';
                        console.log('Upload modal opened');
                    } else {
                        console.error('Upload modal not found');
                        alert('Upload modal not found');
                    }
                });
            }

            // Add close functionality for upload modal
            const closeUploadBtn = document.getElementById('closeUploadDocumentsBtn');
            const cancelUploadBtn = document.getElementById('cancelUploadBtn');

            if (closeUploadBtn) {
                closeUploadBtn.addEventListener('click', function () {
                    const uploadModal = document.getElementById('uploadDocumentsModal');
                    if (uploadModal) {
                        uploadModal.classList.remove('active');
                        setTimeout(() => {
                            uploadModal.classList.add('hidden');
                            document.body.style.overflow = '';
                        }, 300);
                        console.log('Upload modal closed');
                    }
                });
            }

            if (cancelUploadBtn) {
                cancelUploadBtn.addEventListener('click', function () {
                    const uploadModal = document.getElementById('uploadDocumentsModal');
                    if (uploadModal) {
                        uploadModal.classList.remove('active');
                        setTimeout(() => {
                            uploadModal.classList.add('hidden');
                            document.body.style.overflow = '';
                        }, 300);
                        console.log('Upload modal cancelled');
                    }
                });
            }

            // Add form submission functionality
            const uploadForm = document.getElementById('uploadForm');
            if (uploadForm) {
                uploadForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    console.log('Upload form submitted');

                    const formData = new FormData(this);
                    const fileInput = document.getElementById('documentFiles');

                    if (fileInput.files.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Files Selected',
                            text: 'Please select at least one file to upload.',
                            confirmButtonColor: '#059669',
                            showClass: {
                                popup: 'animate__animated animate__fadeInDown'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOutUp'
                            }
                        });
                        return;
                    }

                    // Show loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="bx bx-loader-alt animate-spin mr-2"></i> Uploading...';
                    submitBtn.disabled = true;

                    // Submit to backend
                    fetch("{{ route('document.upload.store') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Upload response:', data);

                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Upload Successful!',
                                    html: `
                                    <div class="text-center">
                                        <div class="mb-4">
                                            <i class="bx bx-check-circle text-6xl text-green-500"></i>
                                        </div>
                                        <p class="text-gray-600">${data.message || 'Files uploaded successfully!'}</p>
                                        <div class="mt-3 text-sm text-gray-500">
                                            ${data.files ? data.files.length + ' file(s) uploaded' : ''}
                                        </div>
                                    </div>
                                `,
                                    confirmButtonColor: '#059669',
                                    confirmButtonText: 'Great!',
                                    showClass: {
                                        popup: 'animate__animated animate__fadeInDown'
                                    },
                                    hideClass: {
                                        popup: 'animate__animated animate__fadeOutUp'
                                    }
                                }).then(() => {
                                    // Close modal and reset form
                                    const uploadModal = document.getElementById('uploadDocumentsModal');
                                    uploadModal.classList.remove('active');
                                    setTimeout(() => {
                                        uploadModal.classList.add('hidden');
                                        document.body.style.overflow = '';
                                    }, 300);

                                    uploadForm.reset();
                                    selectedFilesDiv.innerHTML = '';

                                    // Add uploaded documents to table dynamically
                                    if (data.files && data.files.length > 0) {
                                        addDocumentsToTable(data.files);
                                    } else {
                                        // Fallback to reload if no files data returned
                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 1000);
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Upload Failed',
                                    text: data.message || 'Unknown error occurred',
                                    confirmButtonColor: '#dc2626',
                                    showClass: {
                                        popup: 'animate__animated animate__shakeX'
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Upload error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Upload Failed',
                                html: `
                                <div class="text-center">
                                    <div class="mb-4">
                                        <i class="bx bx-error-circle text-6xl text-red-500"></i>
                                    </div>
                                    <p class="text-gray-600">An error occurred during upload</p>
                                    <div class="mt-2 text-sm text-gray-500">
                                        ${error.message || 'Please try again later'}
                                    </div>
                                </div>
                            `,
                                confirmButtonColor: '#dc2626',
                                confirmButtonText: 'Try Again',
                                showClass: {
                                    popup: 'animate__animated animate__shakeX'
                                }
                            });
                        })
                        .finally(() => {
                            // Reset button
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        });
                });
            }

            // Add drag and drop functionality
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('documentFiles');
            const selectedFilesDiv = document.getElementById('selectedFiles');

            if (dropZone && fileInput) {
                // Prevent default drag behaviors
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                // Highlight drop zone when item is dragged over it
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, highlight, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, unhighlight, false);
                });

                function highlight() {
                    dropZone.classList.add('border-brand-primary', 'bg-brand-primary/10');
                }

                function unhighlight() {
                    dropZone.classList.remove('border-brand-primary', 'bg-brand-primary/10');
                }

                // Handle dropped files
                dropZone.addEventListener('drop', handleDrop, false);

                function handleDrop(e) {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    fileInput.files = files;
                    displaySelectedFiles(files);
                }

                // Handle file selection
                fileInput.addEventListener('change', function () {
                    displaySelectedFiles(this.files);
                });

                function displaySelectedFiles(files) {
                    if (selectedFilesDiv) {
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
                }

                // Make removeFile function globally available
                window.removeFile = function (button, fileName) {
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
            }
        });

        // Category filtering
        const categoryCards = document.querySelectorAll('.category-card');
        categoryCards.forEach(card => {
            card.addEventListener('click', function () {
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

            // Show loading state
            const tableContainer = document.querySelector('#documentsTable');
            if (tableContainer) {
                tableContainer.style.opacity = '0.7';
            }

            // Small delay for visual feedback
            setTimeout(() => {
                rows.forEach(row => {
                    if (row.querySelector('td[colspan]')) {
                        // Skip "No documents" row
                        return;
                    }

                    const docCategory = row.dataset.category || '';

                    if (category === 'all') {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        // Check for exact match or partial match for flexibility
                        if (docCategory === category || docCategory.includes(category)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });

                // Update count
                const visibleCountElement = document.getElementById('visibleCount');
                if (visibleCountElement) {
                    visibleCountElement.textContent = visibleCount;
                }

                // Reset opacity
                if (tableContainer) {
                    tableContainer.style.opacity = '1';
                }

                // Show no results message if needed
                const tbody = document.querySelector('#documentsTable tbody');
                const noResultsRow = tbody.querySelector('.no-results-row');

                if (visibleCount === 0 && !noResultsRow) {
                    const noResultsRow = document.createElement('tr');
                    noResultsRow.className = 'no-results-row';
                    noResultsRow.innerHTML = `
                        <td colspan="5" class="px-6 py-8 text-center">
                            <div class="text-gray-500">
                                <i class="bx bx-folder-open text-4xl mb-2"></i>
                                <p class="text-sm">No documents found in this category</p>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(noResultsRow);
                } else if (visibleCount > 0 && noResultsRow) {
                    noResultsRow.remove();
                }
            }, 150);
        }
    </script>
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

            // Dropdown functionality for sidebar
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
                    btn.addEventListener("click", (e) => {
                        e.preventDefault();
                        e.stopPropagation();

                        const isHidden = submenu.classList.contains("hidden");

                        // Close all other dropdowns (except the one being toggled)
                        Object.entries(dropdowns).forEach(([otherBtnId, otherSubmenuId]) => {
                            if (otherSubmenuId !== submenuId) {
                                const otherSubmenu = document.getElementById(otherSubmenuId);
                                const otherArrow = document.getElementById(otherBtnId.replace('-btn', '-arrow'));
                                if (otherSubmenu) {
                                    otherSubmenu.classList.add("hidden");
                                }
                                if (otherArrow) {
                                    otherArrow.classList.remove("rotate-180");
                                }
                            }
                        });

                        // Toggle current dropdown
                        if (isHidden) {
                            // Open dropdown
                            submenu.classList.remove("hidden");
                            if (arrow) arrow.classList.add("rotate-180");
                        } else {
                            // Close dropdown
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
                // Toggle dropdown on button click
                userMenuButton.addEventListener("click", (e) => {
                    e.stopPropagation();
                    e.preventDefault();

                    const isHidden = userMenuDropdown.classList.contains("hidden");

                    if (isHidden) {
                        // Open dropdown
                        userMenuDropdown.classList.remove("hidden", "opacity-0", "translate-y-2", "scale-95", "pointer-events-none");
                        userMenuDropdown.classList.add("opacity-100", "translate-y-0", "scale-100", "pointer-events-auto");
                    } else {
                        // Close dropdown
                        userMenuDropdown.classList.add("opacity-0", "translate-y-2", "scale-95", "pointer-events-none");
                        userMenuDropdown.classList.remove("opacity-100", "translate-y-0", "scale-100", "pointer-events-auto");
                        setTimeout(() => userMenuDropdown.classList.add("hidden"), 200);
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener("click", (e) => {
                    if (!userMenuButton.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                        if (!userMenuDropdown.classList.contains("hidden")) {
                            userMenuDropdown.classList.add("opacity-0", "translate-y-2", "scale-95", "pointer-events-none");
                            userMenuDropdown.classList.remove("opacity-100", "translate-y-0", "scale-100", "pointer-events-auto");
                            setTimeout(() => userMenuDropdown.classList.add("hidden"), 200);
                        }
                    }
                });
            }
            // Real-time clock with high-precision server synchronization
            const clockElement = document.getElementById('real-time-clock');
            let serverTimeOffset = 0; // Offset in milliseconds between server and client
            let isInitialized = false;

            // Initialize with precise server timestamp from data attribute
            if (clockElement) {
                const serverTimestamp = parseInt(clockElement.getAttribute('data-server-timestamp'));
                if (serverTimestamp && !isNaN(serverTimestamp)) {
                    // Calculate initial offset
                    // Note: This accounts for the time between server render and client execution
                    const clientTimestamp = Date.now();
                    serverTimeOffset = serverTimestamp - clientTimestamp;
                    isInitialized = true;

                    console.log('Initial clock sync - Server offset:', serverTimeOffset, 'ms');
                }
            }

            function updateClock() {
                if (!clockElement) return;

                // Get current time with server offset applied
                const now = new Date(Date.now() + serverTimeOffset);

                // Format time in 12-hour format with AM/PM
                let hours = now.getHours();
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';

                // Convert to 12-hour format
                hours = hours % 12;
                hours = hours ? hours : 12; // the hour '0' should be '12'
                const displayHours = String(hours).padStart(2, '0');

                const timeString = `${displayHours}:${minutes}:${seconds} ${ampm}`;

                clockElement.textContent = timeString;
            }

            // Sync with server time with network latency compensation
            async function syncServerTime() {
                try {
                    const requestStart = Date.now();
                    const response = await fetch("{{ route('api.server-time') }}", {
                        method: 'GET',
                        headers: {
                            'Cache-Control': 'no-cache'
                        }
                    });

                    if (response.ok) {
                        const requestEnd = Date.now();
                        const data = await response.json();

                        // Estimate network latency (round-trip time / 2)
                        const networkLatency = (requestEnd - requestStart) / 2;

                        // Server timestamp adjusted for network latency
                        const serverTimestamp = data.timestamp + networkLatency;
                        const clientTimestamp = Date.now();

                        // Update offset with latency compensation
                        serverTimeOffset = serverTimestamp - clientTimestamp;

                        console.log('Clock synced with server. Offset:', serverTimeOffset.toFixed(0), 'ms | Latency:', networkLatency.toFixed(0), 'ms');
                    }
                } catch (error) {
                    console.warn('Failed to sync with server time:', error);
                }
            }

            // Update clock immediately
            updateClock();

            // Use setInterval for consistent 1-second updates
            setInterval(updateClock, 1000);

            // Perform initial sync after page load to refine accuracy
            if (isInitialized) {
                // Wait a moment for page to fully load, then sync
                setTimeout(() => {
                    syncServerTime();
                }, 1000);
            }

            // Sync with server every 5 minutes to prevent drift
            setInterval(syncServerTime, 5 * 60 * 1000);


            // Open "Document Management" dropdown by default
            const documentSubmenu = document.getElementById('document-submenu');
            const documentArrow = document.getElementById('document-arrow');

            if (documentSubmenu) {
                // Ensure it's visible on page load
                documentSubmenu.classList.remove('hidden');
                if (documentArrow) {
                    documentArrow.classList.add('rotate-180');
                }
            }

            // Modal Management
            const uploadModal = document.getElementById("uploadDocumentsModal");
            const documentModal = document.getElementById("documentModal");
            const downloadModal = document.getElementById("downloadDocumentModal");
            const shareModal = document.getElementById("shareDocumentModal");
            const deleteModal = document.getElementById("deleteDocumentModal");
            const otpModal = document.getElementById("otpModal");

            console.log('Modals found:', {
                uploadModal: !!uploadModal,
                documentModal: !!documentModal,
                downloadModal: !!downloadModal,
                shareModal: !!shareModal,
                deleteModal: !!deleteModal,
                otpModal: !!otpModal
            });

            // Open modals
            document.getElementById('uploadDocumentsBtn').addEventListener('click', () => {
                console.log('Upload Documents button clicked');
                openModal(uploadModal);
            });

            // Close modal functions
            function openModal(modal) {
                console.log('openModal called with:', modal);
                if (!modal) {
                    console.error('Modal is null or undefined');
                    return;
                }
                modal.classList.remove('hidden');
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                console.log('Modal opened:', modal.id);
            }

            function closeModal(modal) {
                console.log('closeModal called with:', modal);
                if (!modal) {
                    console.error('Modal is null or undefined');
                    return;
                }
                modal.classList.remove('active');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                    console.log('Modal closed:', modal.id);
                }, 300);
            }

            // Close buttons
            document.getElementById('closeUploadDocumentsBtn').addEventListener('click', () => closeModal(uploadModal));
            document.getElementById('cancelUploadBtn').addEventListener('click', () => closeModal(uploadModal));
            document.getElementById('closeDocumentModalBtn').addEventListener('click', () => {
                const documentModal = document.getElementById('documentModal');
                closeModal(documentModal);
            });
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
                modal.addEventListener('click', function (e) {
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

            fileInput.addEventListener('change', function () {
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

            window.removeFile = function (button, fileName) {
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
            uploadForm.addEventListener('submit', function (e) {
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

                try {
                    // Update header CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    fetch("{{ route('document.upload.store') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Upload Complete',
                                    text: data.message || 'Files uploaded successfully.',
                                    confirmButtonColor: '#059669'
                                }).then(() => {
                                    closeModal(uploadModal);
                                    uploadForm.reset();
                                    selectedFilesDiv.innerHTML = '';

                                    // Add uploaded documents to table dynamically
                                    if (data.files && data.files.length > 0) {
                                        addDocumentsToTable(data.files);
                                    } else {
                                        // Fallback to reload if no files data returned
                                        window.location.reload();
                                    }
                                });
                            } else {
                                throw new Error(data.message || 'Upload failed');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Upload Failed',
                                text: error.message || 'An error occurred during upload.',
                                confirmButtonColor: '#059669'
                            });

                            // Reset button
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        });
                } catch (err) {
                    console.error(err);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const searchTerm = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#documentsTable tbody tr');

                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
            }

            console.log('Document upload functions loaded');

            // Global function to close document details modal
            window.closeDocumentDetailsModal = function () {
                const documentModal = document.getElementById('documentModal');
                if (documentModal) {
                    closeModal(documentModal);
                }
            };

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

            // Document action functions
            let currentDownloadDoc = null;
            let currentShareDoc = null;
            let currentDeleteDoc = null;

            // Add uploaded documents to table dynamically
            window.addDocumentsToTable = function (files) {
                console.log('Adding documents to table:', files);

                const tbody = document.querySelector('#documentsTable tbody');
                if (!tbody) {
                    console.error('Table tbody not found');
                    return;
                }

                // Remove "No documents" row if it exists
                const noDocumentsRow = tbody.querySelector('td[colspan]');
                if (noDocumentsRow) {
                    noDocumentsRow.parentElement.remove();
                }

                files.forEach(file => {
                    // Determine icon based on file type
                    const dtype = file.type ? file.type.toUpperCase() : '';
                    let icon = 'bxs-file text-gray-500';
                    if (dtype.includes('PDF') || file.name.toLowerCase().endsWith('.pdf')) {
                        icon = 'bxs-file-pdf text-red-500';
                    } else if (dtype.includes('WORD') || dtype.includes('DOC') || file.name.toLowerCase().match(/\.(doc|docx)$/)) {
                        icon = 'bxs-file-doc text-blue-500';
                    } else if (dtype.includes('EXCEL') || dtype.includes('XLS') || file.name.toLowerCase().match(/\.(xls|xlsx)$/)) {
                        icon = 'bxs-file-txt text-green-500';
                    }

                    // Format category for display
                    const rawCategory = file.category || 'other';
                    const categoryKey = rawCategory.toLowerCase();
                    let displayCategory = categoryKey === 'hr' ? 'HR' : ucfirst(categoryKey);

                    if (!['financial', 'hr', 'legal', 'operations'].includes(categoryKey)) {
                        displayCategory = file.type || 'Other';
                    }

                    // Create new table row
                    const row = document.createElement('tr');
                    row.className = 'document-row fade-in';
                    row.setAttribute('data-category', categoryKey);
                    row.setAttribute('data-type', dtype.toLowerCase());
                    row.setAttribute('data-doc-id', file.id || '');

                    row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <i class='bx ${icon} text-xl mr-3'></i>
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    <span class="doc-name" data-name="${file.name}">${file.name}</span>
                                </div>
                                <div class="text-xs text-gray-500">${file.size || ''}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">${dtype}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">${displayCategory}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${file.uploaded || '—'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick="showDownloadDocumentModal(${JSON.stringify(file).replace(/"/g, '&quot;')})"
                            class="text-brand-primary hover:text-brand-primary-hover mr-3"
                            title="Download">
                            <i class="bx bx-download"></i>
                        </button>
                        <button onclick="showDocumentDetails(${JSON.stringify(file).replace(/"/g, '&quot;')})"
                            class="text-blue-600 hover:text-blue-800 mr-3" title="View">
                            <i class="bx bx-show"></i>
                        </button>
                        <button onclick="showShareDocumentModal(${JSON.stringify(file).replace(/"/g, '&quot;')})"
                            class="text-green-600 hover:text-green-800 mr-3" title="Share">
                            <i class="bx bx-share-alt"></i>
                        </button>
                        <button onclick="showDeleteDocumentConfirmation('${file.id || ''}')"
                            class="text-red-600 hover:text-red-800" title="Delete">
                            <i class="bx bx-trash"></i>
                        </button>
                    </td>
                `;

                    // Add row to table
                    tbody.appendChild(row);
                });

                // Update document counts
                updateDocumentCounts();

                // Show success animation
                showUploadSuccessAnimation(files.length);
            };

            // Update document counts in the stats cards
            function updateDocumentCounts() {
                const allRows = document.querySelectorAll('#documentsTable tbody tr:not([style*="display: none"])');
                const totalCount = allRows.length;

                // Update visible count in table header
                const visibleCountElement = document.getElementById('visibleCount');
                const totalCountElement = document.getElementById('totalCount');
                if (visibleCountElement) visibleCountElement.textContent = totalCount;
                if (totalCountElement) totalCountElement.textContent = totalCount;

                // Update total documents card
                const totalDocsCard = document.querySelector('.font-bold.text-3xl');
                if (totalDocsCard && totalDocsCard.textContent.includes('@{{ count($documents) }}')) {
                    // Skip if it's still the template string
                    return;
                }

                // Find and update the total documents count
                const totalDocsElements = document.querySelectorAll('.font-bold.text-3xl');
                totalDocsElements.forEach(el => {
                    const currentText = el.textContent;
                    if (!currentText.includes('@{{')) {
                        el.textContent = totalCount;
                    }
                });
            }

            // Show upload success animation
            function showUploadSuccessAnimation(fileCount) {
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 fade-in';
                toast.innerHTML = `
                <div class="flex items-center">
                    <i class="bx bx-check-circle text-xl mr-2"></i>
                    <span>${fileCount} file(s) added to table</span>
                </div>
            `;

                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            console.log('Document upload functions loaded');

            // Show download document modal
            window.showDownloadDocumentModal = function (doc) {
                console.log('showDownloadDocumentModal called with:', doc);
                currentDownloadDoc = doc;

                const downloadModal = document.getElementById('downloadDocumentModal');
                if (!downloadModal) {
                    console.error('Download modal not found');
                    return;
                }

                const nameElement = document.getElementById('downloadDocName');
                const typeElement = document.getElementById('downloadDocType');
                const sizeElement = document.getElementById('downloadDocSize');

                if (nameElement) nameElement.textContent = doc.name || '—';
                if (typeElement) typeElement.textContent = doc.type || '—';
                if (sizeElement) sizeElement.textContent = doc.size || '—';

                openModal(downloadModal);
            };

            // Perform actual download
            window.performDownload = function () {
                console.log('performDownload called');
                if (!currentDownloadDoc) {
                    console.error('No current download document');
                    return;
                }

                const docId = currentDownloadDoc.id || currentDownloadDoc.code;
                if (!docId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Download Failed',
                        text: 'Document ID not found',
                        confirmButtonColor: '#059669'
                    });
                    return;
                }

                // Create download link
                const downloadUrl = "{{ route('document.download', ':id') }}".replace(':id', docId);

                // Trigger download
                const link = document.createElement('a');
                link.href = downloadUrl;
                link.download = currentDownloadDoc.name || 'document';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Show success message and close modal
                Swal.fire({
                    icon: 'success',
                    title: 'Download Started',
                    text: `${currentDownloadDoc.name} is being downloaded.`,
                    timer: 2000,
                    showConfirmButton: false
                });
                closeModal(downloadModal);
            };

            // Show document details modal
            window.showDocumentDetails = function (doc) {
                console.log('showDocumentDetails called with:', doc);

                const docId = doc.id || doc.code;
                if (!docId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Document ID not found',
                        confirmButtonColor: '#059669'
                    });
                    return;
                }

                // Show loading state
                const content = document.getElementById('documentDetailsContent');
                if (!content) {
                    console.error('Document details content not found');
                    return;
                }

                content.innerHTML = `
                <div class="text-center py-8">
                    <i class="bx bx-loader-alt animate-spin text-3xl text-gray-500"></i>
                    <p class="mt-2 text-gray-500">Loading document details...</p>
                </div>
            `;

                const documentModal = document.getElementById('documentModal');
                if (documentModal) {
                    openModal(documentModal);
                }

                // Fetch document details from backend
                const detailsUrl = "{{ route('document.details', ':id') }}".replace(':id', docId);

                fetch(detailsUrl, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const document = data.document;
                            content.innerHTML = `
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-lg font-medium text-gray-900">${document.name}</h4>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">${document.type}</span>
                            </div>
                            <div class="border-t border-b border-gray-200 py-4">
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                                        <dd class="mt-1 text-sm text-gray-900">${document.category || '—'}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Size</dt>
                                        <dd class="mt-1 text-sm text-gray-900">${document.size || '—'}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Uploaded</dt>
                                        <dd class="mt-1 text-sm text-gray-900">${document.uploaded || '—'}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                                        <dd class="mt-1 text-sm text-gray-900">${document.status || 'Active'}</dd>
                                    </div>
                                    ${document.amount ? `
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Amount</dt>
                                        <dd class="mt-1 text-sm text-gray-900">₱${parseFloat(document.amount).toFixed(2)}</dd>
                                    </div>
                                    ` : ''}
                                    ${document.receipt_date ? `
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Receipt Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900">${document.receipt_date}</dd>
                                    </div>
                                    ` : ''}
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Shared</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full ${document.is_shared ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                                                ${document.is_shared ? 'Yes' : 'No'}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                            ${document.description ? `
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">${document.description}</dd>
                            </div>
                            ` : ''}
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" onclick="closeDocumentDetailsModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Close
                            </button>
                        </div>
                    `;
                        } else {
                            content.innerHTML = `
                        <div class="text-center py-8">
                            <i class="bx bx-error-circle text-3xl text-red-500"></i>
                            <p class="mt-2 text-gray-500">${data.message || 'Failed to load document details'}</p>
                        </div>
                    `;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching document details:', error);
                        content.innerHTML = `
                    <div class="text-center py-8">
                        <i class="bx bx-error-circle text-3xl text-red-500"></i>
                        <p class="mt-2 text-gray-500">Error loading document details</p>
                    </div>
                `;
                    });
            };

            // Show share document modal
            window.showShareDocumentModal = function (doc) {
                console.log('showShareDocumentModal called with:', doc);
                currentShareDoc = doc;

                const shareModal = document.getElementById('shareDocumentModal');
                if (!shareModal) {
                    console.error('Share modal not found');
                    return;
                }

                const nameElement = document.getElementById('shareDocName');
                const linkElement = document.getElementById('shareLink');
                const emailElement = document.getElementById('shareEmail');

                if (nameElement) nameElement.textContent = doc.name || '—';
                if (linkElement) linkElement.value = `${window.location.origin}/documents/${doc.id || ''}`;
                if (emailElement) emailElement.value = '';

                openModal(shareModal);
            };

            // Show delete document confirmation
            window.showDeleteDocumentConfirmation = function (docId) {
                console.log('showDeleteDocumentConfirmation called with:', docId);

                if (!docId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Document ID not found',
                        confirmButtonColor: '#059669'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Delete Document',
                    text: 'Are you sure you want to delete this document? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Deleting Document...',
                            text: 'Please wait while we delete the document.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Send delete request to backend
                        const deleteUrl = "{{ route('document.delete', ':id') }}".replace(':id', docId);

                        fetch(deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                Swal.close();

                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: 'Document has been deleted successfully.',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // Remove the document row from table
                                        const row = document.querySelector(`tr[data-doc-id="${docId}"]`);
                                        if (row) {
                                            row.style.opacity = '0';
                                            row.style.transform = 'translateX(-20px)';
                                            setTimeout(() => {
                                                row.remove();
                                                updateDocumentCounts();

                                                // Show no documents message if table is empty
                                                const tbody = document.querySelector('#documentsTable tbody');
                                                const remainingRows = tbody.querySelectorAll('tr:not([style*="display: none"])');
                                                if (remainingRows.length === 0) {
                                                    const noResultsRow = document.createElement('tr');
                                                    noResultsRow.innerHTML = `
                                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                                    No documents available. Click "Upload Documents" to add your first document.
                                                </td>
                                            `;
                                                    tbody.appendChild(noResultsRow);
                                                }
                                            }, 300);
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Delete Failed',
                                        text: data.message || 'Failed to delete document',
                                        confirmButtonColor: '#059669'
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.close();
                                console.error('Error deleting document:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Delete Failed',
                                    text: 'An error occurred while deleting the document',
                                    confirmButtonColor: '#059669'
                                });
                            });
                    }
                });
            };

            // Perform download
            window.performDownload = function () {
                console.log('performDownload called');
                if (currentDownloadDoc) {
                    // Here you would typically trigger the download
                    const link = document.createElement('a');
                    link.href = `/document/${currentDownloadDoc.id || currentDownloadDoc.code}/download`;
                    link.download = currentDownloadDoc.name;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    closeModal(downloadModal);
                } else {
                    console.error('No current download document');
                }
            };

            // Copy share link
            window.copyShareLink = function () {
                console.log('copyShareLink called');
                const shareLink = document.getElementById('shareLink');
                if (!shareLink) {
                    console.error('Share link input not found');
                    return;
                }

                shareLink.select();
                shareLink.setSelectionRange(0, 99999);

                try {
                    document.execCommand('copy');
                    Swal.fire({
                        icon: 'success',
                        title: 'Copied!',
                        text: 'Share link copied to clipboard',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } catch (err) {
                    console.error('Failed to copy:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: 'Could not copy share link',
                        confirmButtonColor: '#059669'
                    });
                }
            };

            // Send share invite
            window.sendShareInvite = function () {
                console.log('sendShareInvite called');
                const email = document.getElementById('shareEmail');
                if (!email) {
                    console.error('Share email input not found');
                    return;
                }

                const emailValue = email.value.trim();
                if (!emailValue) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Email Required',
                        text: 'Please enter an email address',
                        confirmButtonColor: '#059669'
                    });
                    return;
                }

                if (!currentShareDoc) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No document selected for sharing',
                        confirmButtonColor: '#059669'
                    });
                    return;
                }

                const docId = currentShareDoc.id || currentShareDoc.code;
                if (!docId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Document ID not found',
                        confirmButtonColor: '#059669'
                    });
                    return;
                }

                // Show loading state
                Swal.fire({
                    title: 'Sharing Document...',
                    text: 'Please wait while we share the document.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send share request to backend
                const shareUrl = "{{ route('document.share', ':id') }}".replace(':id', docId);

                fetch(shareUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        email: emailValue
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Document Shared!',
                                html: `
                            <div class="text-center">
                                <p class="text-gray-600 mb-3">Document has been shared with <strong>${emailValue}</strong></p>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-xs text-gray-500 mb-1">Share Link:</p>
                                    <p class="text-xs text-blue-600 break-all">${data.share_link || 'Generated'}</p>
                                </div>
                            </div>
                        `,
                                confirmButtonColor: '#059669'
                            }).then(() => {
                                closeModal(shareModal);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Share Failed',
                                text: data.message || 'Failed to share document',
                                confirmButtonColor: '#059669'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.close();
                        console.error('Error sharing document:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Share Failed',
                            text: 'An error occurred while sharing the document',
                            confirmButtonColor: '#059669'
                        });
                    });
            };

            // Profile and Settings buttons
            document.getElementById('openProfileBtn').addEventListener('click', () => {
                console.log('Profile button clicked');
                Swal.fire({
                    icon: 'info',
                    title: 'Profile',
                    text: 'Profile functionality coming soon!',
                    confirmButtonColor: '#059669'
                });
            });

            document.getElementById('openAccountSettingsBtn').addEventListener('click', () => {
                console.log('Account Settings button clicked');
                Swal.fire({
                    icon: 'info',
                    title: 'Account Settings',
                    text: 'Account settings functionality coming soon!',
                    confirmButtonColor: '#059669'
                });
            });

            // Test button functionality
            console.log('All button event listeners attached');

            // Test function to verify basic functionality
            window.testButtons = function () {
                console.log('Testing buttons...');
                const uploadBtn = document.getElementById('uploadDocumentsBtn');
                const exportBtn = document.getElementById('exportBtn');
                const refreshBtn = document.getElementById('refreshBtn');

                console.log('Button elements found:', {
                    uploadBtn: !!uploadBtn,
                    exportBtn: !!exportBtn,
                    refreshBtn: !!refreshBtn
                });

                // Test click handler
                if (uploadBtn) {
                    console.log('Testing upload button click...');
                    uploadBtn.click();
                }
            };
        });
    </script>
</body>

</html>