@php
    // Get the authenticated user
    $user = auth()->user();

    // Use the combined bookings and approvals data passed from the route
    $bookings = $bookings ?? [];
    $approvalMap = $approvalMap ?? [];
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            opacity: 0;
            transform: translateY(10px);
            pointer-events: none;
            transition: all 0.2s ease-in-out;
            transform-origin: top right;
        }

        .dropdown-panel:not(.hidden) {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
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

        /* Loading screen progress animation */
        @keyframes progress {
            0% {
                width: 0%;
                opacity: 0.5;
            }

            50% {
                width: 80%;
                opacity: 1;
            }

            100% {
                width: 100%;
                opacity: 0.5;
            }
        }

        /* Loading screen transitions */
        #loadingScreen {
            transition: opacity 0.3s ease-in-out;
        }

        #loadingScreen.opacity-100 {
            opacity: 1;
        }

        #loadingScreen.opacity-0 {
            opacity: 0;
        }
    </style>
</head>

<body class="bg-brand-background-main min-h-screen">

    <!-- Loading Screen (Login Style) -->
    <div id="loadingScreen" class="fixed inset-0 z-[9999]">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gradient-to-br from-brand-primary via-emerald-600 to-teal-600"></div>

        <!-- Loading Content -->
        <div class="fixed inset-0 flex flex-col items-center justify-center p-4">
            <!-- Logo -->
            <div class="mb-8">
                <img src="{{ asset('golden-arc.png') }}" alt="Logo"
                    class="w-24 h-24 mx-auto rounded-full border-4 border-white/30 shadow-2xl animate-pulse">
            </div>

            <!-- Lottie Animation -->
            <div class="mb-8">
                <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.11/dist/dotlottie-wc.js"
                    type="module"></script>
                <dotlottie-wc src="https://lottie.host/5378ba62-7703-4273-a14a-3a999385cf7f/s5Vm9nkLqj.lottie"
                    style="width: 300px;height: 300px" autoplay loop></dotlottie-wc>
            </div>

            <!-- Loading Text -->
            <div class="text-center text-white">
                <h2 class="text-2xl font-bold mb-2">Loading Reservation History</h2>
                <p class="text-white/80 text-sm mb-4">Preparing reservation records and loading booking history...</p>

                <!-- Loading Dots -->
                <div class="flex justify-center space-x-2">
                    <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                    <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                    <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="w-64 h-1 bg-white/20 rounded-full mt-8 overflow-hidden">
                <div class="h-full bg-white rounded-full animate-pulse"
                    style="width: 60%; animation: progress 2s ease-in-out infinite;"></div>
            </div>
        </div>
    </div>

    <!-- Main Content (initially hidden) -->
    <div id="mainContent" class="opacity-0 transition-opacity duration-500">

        <!-- Overlay (mobile) -->
        <div id="sidebar-overlay"
            class="fixed inset-0 bg-black/30 hidden opacity-0 transition-opacity duration-300 z-40">
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
                    <svg id="visitor-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <svg id="document-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <svg id="facilities-arrow"
                        class="w-4 h-4 text-emerald-400 transition-transform duration-300 rotate-180" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="facilities-submenu" class="submenu mt-1">
                    <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
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
                        Microfinance Admin Â© {{ date('Y') }}<br />
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
                    <span id="real-time-clock"
                        class="text-xs font-bold text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                        {{ now()->format('H:i:s') }}
                    </span>

                    <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>

                    <!-- User Profile Dropdown -->
                    <div class="relative">
                        <button id="userMenuBtn" class="flex items-center gap-3 focus:outline-none group rounded-xl px-2 py-2
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
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-primary transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </button>

                        <div id="userMenuDropdown" class="dropdown-panel hidden absolute right-0 mt-3 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50">
                            <div class="py-4 px-6 border-b border-gray-100 text-center">
                                <div
                                    class="w-14 h-14 rounded-full bg-[#28644c] text-white mx-auto flex items-center justify-center mb-2">
                                    <i class="fas fa-user-circle text-3xl"></i>
                                </div>
                                <p class="font-semibold text-[#28644c]">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400">{{ ucfirst($user->role) }}</p>
                            </div>
                            <ul class="text-sm text-gray-700">
                                <li><button id="openProfileBtn"
                                    class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none"><i
                                        class="fas fa-user-circle mr-2"></i> My Profile</button></li>
                                <li><button id="openSignOutBtn"
                                    class="w-full text-left flex items-center px-6 py-2 text-red-600 hover:bg-gray-100 focus:outline-none"><i
                                        class="fas fa-sign-out-alt mr-2"></i> Sign Out</button></li>
                            </ul>
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
                                    <input id="reservationSearch" type="text" placeholder="Search reservations..."
                                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent w-full md:w-64"
                                        aria-label="Search reservations">
                                    <i
                                        class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                </div>
                                <button id="exportReservationsBtn"
                                    class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors font-medium flex items-center">
                                    <i class="fas fa-download mr-2"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Reservation Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- Total Reservations Card -->
                        <div
                            class="group relative bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 rounded-full bg-blue-50 blur-3xl opacity-60 group-hover:opacity-100 transition-opacity">
                            </div>
                            <div class="relative flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 mb-1">Total Reservations</p>
                                    <h3 class="text-3xl font-bold text-gray-900">{{ count($bookings) }}</h3>
                                    <div
                                        class="mt-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                                        Overview
                                    </div>
                                </div>
                                <div
                                    class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center shadow-blue-200 shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                    <i class="bx bx-calendar text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Approvals Card -->
                        <div
                            class="group relative bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 rounded-full bg-amber-50 blur-3xl opacity-60 group-hover:opacity-100 transition-opacity">
                            </div>
                            <div class="relative flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 mb-1">Pending Approvals</p>
                                    <h3 class="text-3xl font-bold text-gray-900">
                                        {{ count(array_filter($bookings, fn($b) => $b['status'] === 'pending')) }}
                                    </h3>
                                    <div
                                        class="mt-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                        Review Required
                                    </div>
                                </div>
                                <div
                                    class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-500 text-white flex items-center justify-center shadow-amber-200 shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                    <i class="bx bx-time-five text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- This Week Card -->
                        <div
                            class="group relative bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 rounded-full bg-emerald-50 blur-3xl opacity-60 group-hover:opacity-100 transition-opacity">
                            </div>
                            <div class="relative flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 mb-1">This Week</p>
                                    <h3 class="text-3xl font-bold text-gray-900">
                                        {{ count(array_filter($bookings, function ($booking) {
    $date = $booking['date'] ?? '';
    $startOfWeek = now()->startOfWeek();
    $endOfWeek = now()->endOfWeek();
    return $date && strtotime($date) >= $startOfWeek->timestamp && strtotime($date) <= $endOfWeek->timestamp;
})) }}
                                    </h3>
                                    <div
                                        class="mt-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Active
                                    </div>
                                </div>
                                <div
                                    class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-500 text-white flex items-center justify-center shadow-emerald-200 shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                    <i class="bx bx-calendar-event text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Rooms vs Equipment Card -->
                        <div
                            class="group relative bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 rounded-full bg-violet-50 blur-3xl opacity-60 group-hover:opacity-100 transition-opacity">
                            </div>
                            <div class="relative flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 mb-1">Rooms vs Equipment</p>
                                    <h3 class="text-3xl font-bold text-gray-900">
                                        {{ count(array_filter($bookings, fn($b) => $b['type'] === 'room')) }} /
                                        {{ count(array_filter($bookings, fn($b) => $b['type'] === 'equipment')) }}
                                    </h3>
                                    <div
                                        class="mt-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-50 text-violet-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-violet-500 mr-1.5"></span>
                                        Ratio
                                    </div>
                                </div>
                                <div
                                    class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-500 to-violet-600 text-white flex items-center justify-center shadow-violet-200 shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                    <i class="bx bx-building-house text-2xl"></i>
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
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Reservation ID</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Booking Code</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Facility</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Facilitator / Requested By</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date & Time</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lead Time</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Decision Notes</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($bookings as $reservation)
                                        <tr class="reservation-row" data-id="{{ $reservation['id'] ?? '' }}"
                                            data-type="{{ $reservation['type'] ?? '' }}"
                                            data-status="{{ $reservation['status'] ?? '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                #{{ $reservation['request_id'] ?? $reservation['id'] ?? 'N/A' }}
                                                @if($reservation['is_external'] ?? false)
                                                    <span
                                                        class="ml-2 text-xs text-orange-600 bg-orange-50 px-2 py-1 rounded">External</span>
                                                @elseif($reservation['is_approval'] ?? false)
                                                    <span
                                                        class="ml-2 text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded">Approval</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                                {{ $reservation['booking_code'] ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div
                                                        class="flex-shrink-0 h-10 w-10 rounded-full {{ ($reservation['is_external'] ?? false) ? 'bg-orange-100' : (($reservation['is_approval'] ?? false) ? 'bg-purple-100' : 'bg-blue-100') }} flex items-center justify-center">
                                                        <i
                                                            class="{{ ($reservation['is_external'] ?? false) ? 'bx bx-cloud-download text-orange-600' : (($reservation['is_approval'] ?? false) ? 'bx bx-clipboard-check text-purple-600' : (($reservation['type'] ?? 'room') === 'room' ? 'bx bx-building-house text-blue-600' : 'bx bx-video-recording text-blue-600')) }}"></i>
                                                    </div>
                                                    <div class="ml-4">
                                                        @php
                                                            $title = $reservation['title'] ?? ($reservation['name'] ?? 'Booking');
                                                            $facilityType = $reservation['type'] ?? 'room';
                                                        @endphp
                                                        <div class="text-sm font-medium text-gray-900">{{ $title }}</div>
                                                        <div class="text-xs text-gray-500">
                                                            @if($reservation['is_external'] ?? false)
                                                                <span class="flex items-center gap-1">
                                                                    <i class="fas fa-map-marker-alt text-[10px]"></i>
                                                                    {{ $reservation['location'] ?? 'External' }}
                                                                </span>
                                                            @else
                                                                {{ ucfirst($facilityType) }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 font-medium">
                                                    @if($reservation['is_external'] ?? false)
                                                        {{ $reservation['facilitator'] ?? $reservation['requested_by'] ?? 'N/A' }}
                                                    @else
                                                        {{ $reservation['requested_by'] ?? ($user->name ?? 'User') }}
                                                    @endif
                                                </div>
                                                @if(!($reservation['is_external'] ?? false))
                                                    <div class="text-xs text-gray-500">Internal Staff</div>
                                                @else
                                                    <div class="text-xs text-gray-500">External Facilitator</div>
                                                @endif
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
                                                <div class="text-sm text-gray-500">{{ $timeStr ?: '”' }}</div>
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
                                                    // Handle decision notes for approval requests
                                                    $isApproval = $reservation['is_approval'] ?? false;
                                                    $note = null;
                                                    $isRejected = $status === 'rejected';
                                                    $isApproved = $status === 'approved';

                                                    if ($isApproval) {
                                                        if ($isRejected && $reservation['rejected_by']) {
                                                            $note = 'Rejected by ' . $reservation['rejected_by'];
                                                            if ($reservation['rejected_at']) {
                                                                $note .= ' on ' . \Carbon\Carbon::parse($reservation['rejected_at'])->format('M d, Y \a\t g:i A');
                                                            }
                                                            if (($reservation['description'] ?? null) && str_contains($reservation['description'], 'Rejection reason:')) {
                                                                $note .= '. Reason: ' . trim(explode('Rejection reason:', $reservation['description'])[1]);
                                                            }
                                                        } elseif ($isApproved && $reservation['approved_by']) {
                                                            $note = 'Approved by ' . $reservation['approved_by'];
                                                            if ($reservation['approved_at']) {
                                                                $note .= ' on ' . \Carbon\Carbon::parse($reservation['approved_at'])->format('M d, Y \a\t g:i A');
                                                            }
                                                        }
                                                    } else {
                                                        // Handle regular bookings
                                                        $req = $approvalMap[$reservation['id']] ?? null;
                                                        $note = $req['decision_reason'] ?? $req['reason'] ?? ($reservation['decision_note'] ?? ($reservation['reason'] ?? null));
                                                        if (!$note && $isApproved) {
                                                            $note = 'Approved: meets booking qualifications';
                                                        }
                                                        if (!$note && $isRejected) {
                                                            $note = 'Rejected';
                                                        }
                                                    }
                                                @endphp
                                                @if($note)
                                                    @if($isRejected)
                                                        <div class="inline-flex items-start max-w-xs md:max-w-md lg:max-w-lg">
                                                            <div
                                                                class="bg-red-50 border border-red-200 text-red-800 rounded-md px-3 py-2 leading-snug">
                                                                <span
                                                                    class="block text-xs font-semibold tracking-wide uppercase mb-0.5">Rejection
                                                                    Reason</span>
                                                                <span class="text-sm">{{ $note }}</span>
                                                            </div>
                                                        </div>
                                                    @elseif($isApproved)
                                                        <div class="inline-flex items-start max-w-xs md:max-w-md lg:max-w-lg">
                                                            <div
                                                                class="bg-green-50 border border-green-200 text-green-800 rounded-md px-3 py-2 leading-snug">
                                                                <span
                                                                    class="block text-xs font-semibold tracking-wide uppercase mb-0.5">Approval
                                                                    Note</span>
                                                                <span class="text-sm">{{ $note }}</span>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="text-gray-700">{{ $note }}</div>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">”</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button type="button"
                                                    class="text-blue-600 hover:text-blue-900 mr-3 view-reservation"
                                                    data-id="{{ $reservation['id'] ?? '' }}"
                                                    data-request-id="{{ $reservation['request_id'] ?? $reservation['id'] ?? '' }}"
                                                    data-booking-code="{{ $reservation['booking_code'] ?? 'N/A' }}"
                                                    data-title="{{ $title }}" data-type="{{ $facilityType }}"
                                                    data-date="{{ $reservation['date'] ?? '' }}" data-start="{{ $start12 }}"
                                                    data-end="{{ $end12 }}"
                                                    data-status="{{ strtolower($reservation['status'] ?? 'pending') }}"
                                                    data-requested-by="{{ $reservation['requested_by'] ?? ($user->name ?? 'User') }}"
                                                    data-facilitator="{{ $reservation['facilitator'] ?? 'N/A' }}"
                                                    data-location="{{ $reservation['location'] ?? 'N/A' }}"
                                                    data-is-approval="{{ $isApproval ? 'true' : 'false' }}"
                                                    data-is-external="{{ ($reservation['is_external'] ?? false) ? 'true' : 'false' }}"
                                                    data-purpose="{{ $reservation['purpose'] ?? ($reservation['notes'] ?? ($reservation['description'] ?? 'No description provided')) }}">
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
                                Showing {{ count($bookings) > 0 ? 1 : 0 }} to {{ count($bookings) }} of
                                {{ count($bookings) }} results
                            </div>
                            <div class="flex space-x-2">
                                <button disabled
                                    class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary disabled:opacity-50"
                                    disabled>
                                    Previous
                                </button>
                                <button disabled
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
                        <div class="text-gray-500 font-medium">Request ID</div>
                        <div class="col-span-2 text-gray-900" id="resId">”</div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="text-gray-500 font-medium">Booking Code</div>
                        <div class="col-span-2 text-gray-900 font-mono" id="resBookingCode">”</div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="text-gray-500 font-medium">Title</div>
                        <div class="col-span-2 text-gray-900 font-semibold" id="resTitle">”</div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="text-gray-500 font-medium">Facility / Type</div>
                        <div class="col-span-2 text-gray-900" id="resType">”</div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="text-gray-500 font-medium">Location</div>
                        <div class="col-span-2 text-gray-900" id="resLocation">”</div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="text-gray-500 font-medium">Date</div>
                        <div class="col-span-2 text-gray-900" id="resDate">”</div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="text-gray-500 font-medium">Time</div>
                        <div class="col-span-2 text-gray-900" id="resTime">”</div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="text-gray-500 font-medium">Status</div>
                        <div class="col-span-2 font-bold" id="resStatus">”</div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="text-gray-500 font-medium" id="labelRequestedBy">Requested By</div>
                        <div class="col-span-2 text-gray-900" id="resRequestedBy"”</div>
                    </div>
                    <div id="facilitatorRow" class="grid grid-cols-3 gap-2 hidden">
                        <div class="text-gray-500 font-medium">Facilitator</div>
                        <div class="col-span-2 text-gray-900" id="resFacilitator">”</div>
                    </div>
                    <div class="border-t border-gray-100 my-4 pt-4">
                        <div class="text-gray-500 font-medium mb-1">Purpose / Notes</div>
                        <div class="text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-100 italic"
                            id="resPurpose">
                            No purpose provided
                        </div>
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
                const userMenuBtn = document.getElementById("userMenuBtn");
                const userMenuDropdown = document.getElementById("userMenuDropdown");

                // Toggle user dropdown
                if (userMenuBtn && userMenuDropdown) {
                    userMenuBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        userMenuDropdown.classList.toggle('hidden');
                    });
                }

                // Close dropdown when clicking outside
                document.addEventListener('click', (e) => {
                    if (userMenuDropdown && !userMenuDropdown.contains(e.target) && userMenuBtn && !userMenuBtn.contains(e.target)) {
                        userMenuDropdown.classList.add('hidden');
                    }
                });

                // Modal functionality
                const openProfileBtn = document.getElementById('openProfileBtn');
                const closeProfileBtn = document.getElementById('closeProfileBtn');
                const closeProfileBtn2 = document.getElementById('closeProfileBtn2');
                const profileModal = document.getElementById('profileModal');

                // Profile modal
                if (openProfileBtn) {
                    openProfileBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        profileModal.classList.add('active');
                        userMenuDropdown.classList.add('hidden');
                    });
                }

                // Close profile modal buttons
                if (closeProfileBtn) {
                    closeProfileBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        profileModal.classList.remove('active');
                    });
                }

                if (closeProfileBtn2) {
                    closeProfileBtn2.addEventListener('click', (e) => {
                        e.stopPropagation();
                        profileModal.classList.remove('active');
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
                    searchInput.addEventListener('input', function () {
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
                    exportBtn.addEventListener('click', function (e) {
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

                        const id = btn.getAttribute('data-id') || '”';
                        const requestId = btn.getAttribute('data-request-id') || id;
                        const bookingCode = btn.getAttribute('data-booking-code') || 'N/A';
                        const title = btn.getAttribute('data-title') || '”';
                        const type = btn.getAttribute('data-type') || '”';
                        const date = btn.getAttribute('data-date') || '';
                        const start = btn.getAttribute('data-start') || '';
                        const end = btn.getAttribute('data-end') || '';
                        const status = btn.getAttribute('data-status') || 'pending';
                        const requestedBy = btn.getAttribute('data-requested-by') || '”';
                        const facilitator = btn.getAttribute('data-facilitator') || 'N/A';
                        const location = btn.getAttribute('data-location') || 'N/A';
                        const purpose = btn.getAttribute('data-purpose') || 'No description provided';
                        const isExternal = btn.getAttribute('data-is-external') === 'true';

                        document.getElementById('resId').textContent = `#${requestId}`;
                        document.getElementById('resBookingCode').textContent = bookingCode;
                        document.getElementById('resTitle').textContent = title;
                        document.getElementById('resType').textContent = type.charAt(0).toUpperCase() + type.slice(1);
                        document.getElementById('resDate').textContent = date ? new Date(date).toLocaleDateString(undefined, {
                            year: 'numeric',
                            month: 'short',
                            day: '2-digit'
                        }) : '”';

                        const start12 = to12h(start);
                        const end12 = to12h(end);
                        const timeStr = start12 && end12 ? `${start12} - ${end12}` : (start12 || end12 || '”');
                        document.getElementById('resTime').textContent = timeStr;

                        const statusElem = document.getElementById('resStatus');
                        statusElem.textContent = status.charAt(0).toUpperCase() + status.slice(1);

                        // Style status based on value
                        statusElem.className = 'col-span-2 font-bold';
                        if (status === 'approved') statusElem.classList.add('text-emerald-600');
                        else if (status === 'rejected') statusElem.classList.add('text-red-600');
                        else if (status === 'pending') statusElem.classList.add('text-amber-600');

                        document.getElementById('resRequestedBy').textContent = requestedBy;
                        document.getElementById('resLocation').textContent = location;
                        document.getElementById('resPurpose').textContent = purpose;

                        const facilitatorRow = document.getElementById('facilitatorRow');
                        if (isExternal) {
                            facilitatorRow.classList.remove('hidden');
                            document.getElementById('resFacilitator').textContent = facilitator;
                            document.getElementById('labelRequestedBy').textContent = 'Created By';
                        } else {
                            facilitatorRow.classList.add('hidden');
                            document.getElementById('labelRequestedBy').textContent = 'Requested By';
                        }

                        openModal(reservationModal);
                    });
                });

                closeReservationDetails.addEventListener('click', () => {
                    closeModal(reservationModal);
                });

                // Close modal when clicking outside
                reservationModal.addEventListener('click', function (e) {
                    if (e.target === this) {
                        closeModal(this);
                    }
                });

            });
        </script>

    </div>

    <!-- Loading Screen JavaScript -->
    <script>
        // Loading Screen Functions (matching login page style)
        function showLoadingScreen() {
            const loadingScreen = document.getElementById('loadingScreen');
            loadingScreen.classList.remove('hidden');
            // Add fade-in animation
            setTimeout(() => {
                loadingScreen.classList.add('opacity-100');
            }, 10);
        }

        function hideLoadingScreen() {
            const loadingScreen = document.getElementById('loadingScreen');
            const mainContent = document.getElementById('mainContent');

            loadingScreen.classList.add('opacity-0');
            setTimeout(() => {
                loadingScreen.classList.add('hidden');
                if (mainContent) {
                    mainContent.style.opacity = '1';
                }
            }, 300);
        }

        // Hide loading screen and show main content after page loads
        window.addEventListener('load', function () {
            setTimeout(function () {
                hideLoadingScreen();
            }, 2000); // 2 second delay for better UX
        });

        // Fallback in case window.load doesn't fire properly
        document.addEventListener('DOMContentLoaded', function () {
            // Additional fallback after 5 seconds
            setTimeout(function () {
                const loadingScreen = document.getElementById('loadingScreen');
                const mainContent = document.getElementById('mainContent');

                if (loadingScreen && !loadingScreen.classList.contains('hidden')) {
                    hideLoadingScreen();
                }
            }, 5000);
        });

        // Initialize loading screen on page load
        document.addEventListener('DOMContentLoaded', function () {
            showLoadingScreen();
        });
    </script>

    <!-- Global Loading Scripts -->
    @include('components.loading-scripts')

    @auth
        @include('partials.session-timeout-modal')
    @endauth

  <!-- Profile Modal -->
  <div id="profileModal" class="modal hidden" aria-modal="true" role="dialog">
    <div class="bg-white rounded-lg shadow-lg w-[600px] max-w-full mx-4 fade-in" role="document">
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">My Profile</h3>
        <button type="button" class="text-gray-400 hover:text-gray-600" id="closeProfileBtn">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="p-6">
        <!-- Profile Header -->
        <div class="text-center mb-6">
          <div class="w-24 h-24 rounded-full bg-[#28644c] text-white mx-auto flex items-center justify-center mb-4">
            <i class="fas fa-user text-5xl"></i>
          </div>
          <h4 class="text-xl font-bold text-gray-900">{{ $user->name }}</h4>
          <p class="text-sm text-gray-500">{{ ucfirst($user->role) }}</p>
        </div>
        
        <!-- Profile Information -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name:</label>
            <p class="text-sm text-gray-900">{{ $user->name }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Username:</label>
            <p class="text-sm text-gray-900">{{ $user->username ?? 'admin' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
            <p class="text-sm text-gray-900">{{ $user->email }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone:</label>
            <p class="text-sm text-gray-900">+63 917 123 4567</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Department:</label>
            <p class="text-sm text-gray-900">Administrative</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Location:</label>
            <p class="text-sm text-gray-900">Manila, Philippines</p>
          </div>
          <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Joined:</label>
            <p class="text-sm text-gray-900">October 18, 2025</p>
          </div>
        </div>
      </div>
      <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" id="closeProfileBtn2">Cancel</button>
        <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-brand-primary rounded-md hover:bg-brand-primary-hover">Save Changes</button>
      </div>
    </div>
  </div>

  <!-- Account Settings Modal -->
  <div id="accountSettingsModal" class="modal hidden" aria-modal="true" role="dialog">
    <div class="bg-white rounded-lg shadow-lg w-[500px] max-w-full mx-4 fade-in" role="document">
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Account Settings</h3>
        <button type="button" class="text-gray-400 hover:text-gray-600" id="closeAccountSettingsBtn">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="p-6">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
            <input type="text" value="{{ $user->name }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-primary">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" value="{{ $user->email }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-primary">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Language</label>
            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-primary">
              <option>English</option>
            </select>
          </div>
        </div>
      </div>
      <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" id="cancelAccountSettingsBtn">Cancel</button>
        <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-brand-primary rounded-md hover:bg-brand-primary-hover">Save Changes</button>
      </div>
    </div>
  </div>

  <!-- Privacy & Security Modal -->
  <div id="privacySecurityModal" class="modal hidden" aria-modal="true" role="dialog">
    <div class="bg-white rounded-lg shadow-lg w-[500px] max-w-full mx-4 fade-in" role="document">
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Privacy & Security</h3>
        <button type="button" class="text-gray-400 hover:text-gray-600" id="closePrivacySecurityBtn">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="p-6">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
            <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-primary">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
            <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-primary">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
            <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-primary">
          </div>
        </div>
      </div>
      <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" id="cancelPrivacySecurityBtn">Cancel</button>
        <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-brand-primary rounded-md hover:bg-brand-primary-hover">Update Password</button>
      </div>
    </div>
  </div>

  <!-- Sign Out Modal -->
  <div id="signOutModal" class="modal hidden" aria-modal="true" role="dialog">
    <div class="bg-white rounded-lg shadow-lg w-[400px] max-w-full mx-4 fade-in" role="document">
      <div class="p-6">
        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
          <i class="fas fa-sign-out-alt text-red-600"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Sign Out</h3>
        <p class="text-sm text-gray-600 text-center mb-6">Are you sure you want to sign out of your account?</p>
      </div>
      <div class="flex justify-center gap-3 p-6 border-t border-gray-200">
        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" id="cancelSignOutBtn">Cancel</button>
        <form method="POST" action="{{ route('logout') }}" class="inline">
          @csrf
          <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Sign Out</button>
        </form>
      </div>
    </div>
  </div>
</body>

</html>


