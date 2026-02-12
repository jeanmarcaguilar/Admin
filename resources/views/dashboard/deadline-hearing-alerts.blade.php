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

        .alert-card {
            transition: all 0.2s ease-in-out;
            border: 1px solid #e5e7eb;
        }

        .alert-card:hover {
            border-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .table-row {
            transition: all 0.2s ease;
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
                <h2 class="text-2xl font-bold mb-2">Loading Deadline & Hearing Alerts</h2>
                <p class="text-white/80 text-sm mb-4">Preparing deadline system and loading hearing alerts...</p>

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
                    <svg id="facilities-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="facilities-submenu" class="submenu mt-1 hidden">
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
                    <svg id="legal-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300 rotate-180"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="legal-submenu" class="submenu mt-1">
                    <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                        <a href="{{ route('case.management') }}"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1"
                            onclick="return openCaseWithConfGate(this.href)">
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
                        <a href="{{ route('document.compliance.tracking') }}"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                            <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                            Compliance Tracking
                        </a>
                        <a href="{{ route('deadline.hearing.alerts') }}"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <div class="hidden md:flex flex-col items-start">
                                <span
                                    class="text-sm font-bold text-gray-700 group-hover:text-brand-primary transition-colors">
                                    {{ $user->name }}
                                </span>
                                <span
                                    class="text-[10px] text-gray-500 font-medium uppercase group-hover:text-brand-primary transition-colors">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-primary transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </button>

                        <div id="userMenuDropdown"
                            class="dropdown-panel hidden absolute right-0 mt-3 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50">
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
                                <h1 class="text-2xl font-bold text-gray-900">Deadline & Hearing Alerts</h1>
                                <p class="text-gray-600 mt-1">Track and manage all legal deadlines, court hearings, and
                                    compliance dates</p>
                            </div>
                            <div class="mt-4 md:mt-0 flex space-x-3">
                                <button id="exportBtn"
                                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                    <i class="fas fa-download mr-2"></i> Export
                                </button>
                                <button id="printBtn"
                                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                    <i class="fas fa-print mr-2"></i> Print
                                </button>
                                <button id="addAlertBtn"
                                    class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors font-medium flex items-center">
                                    <i class="fas fa-plus mr-2"></i> Add Alert
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- Today's Deadlines Card -->
                        <div
                            class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                            </div>
                            <div class="relative flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-gray-600 font-semibold text-sm mb-2">Today's Deadlines</p>
                                    <p class="font-bold text-3xl text-gray-900 mb-1">{{ $counts['today'] ?? 0 }}</p>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            <i class="fas fa-calendar-day mr-1"></i>
                                            Today
                                        </span>
                                        <span class="text-xs text-gray-500">Due</span>
                                    </div>
                                </div>
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="fas fa-calendar-day text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2 rounded-full transition-all duration-500"
                                        style="width: {{ min(($counts['today'] ?? 0) * 20, 100) }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Hearings Card -->
                        <div
                            class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                            </div>
                            <div class="relative flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-gray-600 font-semibold text-sm mb-2">Upcoming Hearings</p>
                                    <p class="font-bold text-3xl text-gray-900 mb-1">{{ $counts['upcoming'] ?? 0 }}</p>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-gavel mr-1"></i>
                                            Scheduled
                                        </span>
                                        <span class="text-xs text-gray-500">30 days</span>
                                    </div>
                                </div>
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="fas fa-gavel text-white text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Overdue Card -->
                        <div
                            class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-red-50 to-red-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                            </div>
                            <div class="relative flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-gray-600 font-semibold text-sm mb-2">Overdue</p>
                                    <p class="font-bold text-3xl text-gray-900 mb-1">{{ $counts['overdue'] ?? 0 }}</p>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Critical
                                        </span>
                                        <span class="text-xs text-gray-500">Urgent</span>
                                    </div>
                                </div>
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Completed Card -->
                        <div
                            class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-violet-50 to-violet-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                            </div>
                            <div class="relative flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-gray-600 font-semibold text-sm mb-2">Completed</p>
                                    <p class="font-bold text-3xl text-gray-900 mb-1">{{ $counts['completed'] ?? 0 }}</p>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Done
                                        </span>
                                        <span class="text-xs text-gray-500">Month</span>
                                    </div>
                                </div>
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="fas fa-check-circle text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-xs text-gray-500">
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 bg-violet-500 rounded-full mr-1"></span>
                                        This month
                                    </span>
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
                                <input type="text" id="searchInput"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-primary focus:border-brand-primary block w-full pl-10 p-2.5"
                                    placeholder="Search alerts...">
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    class="px-3 py-1.5 text-sm font-medium bg-blue-50 text-blue-700 rounded-full hover:bg-blue-100 transition-colors">
                                    All Alerts
                                </button>
                                <button
                                    class="px-3 py-1.5 text-sm font-medium bg-green-50 text-green-700 rounded-full hover:bg-green-100 transition-colors">
                                    <i class='bx bx-check-circle mr-1'></i> Today
                                </button>
                                <button
                                    class="px-3 py-1.5 text-sm font-medium bg-amber-50 text-amber-700 rounded-full hover:bg-amber-100 transition-colors">
                                    <i class='bx bx-time-five mr-1'></i> Upcoming
                                </button>
                                <button
                                    class="px-3 py-1.5 text-sm font-medium bg-red-50 text-red-700 rounded-full hover:bg-red-100 transition-colors">
                                    <i class='bx bx-error mr-1'></i> Overdue
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Alerts Table -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <div>
                                <h3 class="font-semibold text-lg text-gray-900">All Alerts</h3>
                                <p class="text-sm text-gray-500">Manage and track all your legal deadlines and hearings
                                </p>
                            </div>
                            <button class="text-sm text-brand-primary hover:text-brand-primary-hover font-medium">View
                                Calendar</button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Title</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Type</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Due Date</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Priority</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse((isset($alerts) ? $alerts : []) as $alert)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div
                                                            class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                                            <i class="fas fa-bell text-emerald-600"></i>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $alert['title'] ?? 'Untitled Alert' }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $alert['description'] ?? 'No description' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $alert['type'] ?? 'General' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $alert['due_date'] ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        @if(($alert['status'] ?? '') === 'completed') bg-green-100 text-green-800
                                                        @elseif(($alert['status'] ?? '') === 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif(($alert['status'] ?? '') === 'overdue') bg-red-100 text-red-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst($alert['status'] ?? 'Unknown') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        @if(($alert['priority'] ?? '') === 'high') bg-red-100 text-red-800
                                                        @elseif(($alert['priority'] ?? '') === 'medium') bg-yellow-100 text-yellow-800
                                                        @else bg-green-100 text-green-800 @endif">
                                                    {{ ucfirst($alert['priority'] ?? 'Normal') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button
                                                    class="text-brand-primary hover:text-brand-primary-hover mr-3">Edit</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center">
                                                <div class="text-gray-500">
                                                    <i class="fas fa-inbox text-4xl mb-4 block text-gray-300"></i>
                                                    <p class="text-lg font-medium">No alerts found</p>
                                                    <p class="text-sm mt-1">Get started by creating your first alert</p>
                                                    <button
                                                        class="mt-4 px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors font-medium">
                                                        <i class="fas fa-plus mr-2"></i> Add Alert
                                                    </button>
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

        <!-- Add Alert Modal -->
        <div id="addAlertModal" class="modal hidden" aria-modal="true" role="dialog"
            aria-labelledby="add-alert-modal-title">
            <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4 fade-in" role="document">
                <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
                    <h3 id="add-alert-modal-title" class="font-semibold text-sm text-gray-900">Add New Alert</h3>
                    <button id="closeAddAlertModal" type="button"
                        class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200"
                        aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-6 pt-5 pb-6">
                    <form id="addAlertForm" class="space-y-4 text-xs text-gray-700">
                        <div>
                            <label for="alertTitle" class="block text-xs font-medium text-gray-700 mb-1">Title *</label>
                            <input type="text" id="alertTitle" name="alertTitle"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent"
                                required>
                        </div>
                        <div>
                            <label for="alertType" class="block text-xs font-medium text-gray-700 mb-1">Type *</label>
                            <select id="alertType" name="alertType"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent"
                                required>
                                <option value="">Select a type</option>
                                <option value="court_hearing">Court Hearing</option>
                                <option value="filing_deadline">Filing Deadline</option>
                                <option value="compliance_deadline">Compliance Deadline</option>
                                <option value="contract">Contract</option>
                                <option value="meeting">Meeting</option>
                            </select>
                        </div>
                        <div>
                            <label for="dueDate" class="block text-xs font-medium text-gray-700 mb-1">Due Date *</label>
                            <input type="date" id="dueDate" name="dueDate"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent"
                                required>
                        </div>
                        <div>
                            <label for="priority" class="block text-xs font-medium text-gray-700 mb-1">Priority
                                *</label>
                            <select id="priority" name="priority"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent"
                                required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <label for="description"
                                class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="description" name="description" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent"></textarea>
                        </div>
                        <div>
                            <label for="relatedTo" class="block text-xs font-medium text-gray-700 mb-1">Related To
                                (Optional)</label>
                            <input type="text" id="relatedTo" name="relatedTo"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent"
                                placeholder="Case #, Contract #, etc.">
                        </div>
                        <div class="flex justify-end space-x-3 pt-2">
                            <button type="button" id="cancelAddAlert"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover rounded-lg transition-colors">Save
                                Alert</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Settings Modal -->
        <div id="accountSettingsModal" class="modal hidden" aria-modal="true" role="dialog"
            aria-labelledby="account-settings-modal-title">
            <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
                <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                    <h3 id="account-settings-modal-title" class="font-semibold text-sm text-gray-900 select-none">Account Settings
                    </h3>
                    <button id="closeAccountSettingsBtn" type="button"
                        class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200"
                        aria-label="Close">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                <div class="px-8 pt-6 pb-8">
                    <form class="space-y-4 text-xs text-gray-700" action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="username" class="block mb-1 font-semibold">Username</label>
                            <input id="username" name="username" type="text" value="{{ $user->name }}"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" />
                        </div>
                        <div>
                            <label for="emailAccount" class="block mb-1 font-semibold">Email</label>
                            <input id="emailAccount" name="email" type="email" value="{{ $user->email }}"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" />
                        </div>
                        <div>
                            <label for="language" class="block mb-1 font-semibold">Language</label>
                            <select id="language" name="language"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]">
                                <option selected>English</option>
                            </select>
                        </div>
                        <div>
                            <label for="timezone" class="block mb-1 font-semibold">Time Zone</label>
                            <select id="timezone" name="timezone"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]">
                                <option selected>Philippine Time (GMT+8)</option>
                            </select>
                        </div>
                        <fieldset class="space-y-1">
                            <legend class="font-semibold text-xs mb-1">Notifications</legend>
                            <div class="flex items-center space-x-2">
                                <input id="email-notifications" name="email_notifications" type="checkbox" checked
                                    class="w-3.5 h-3.5 text-[#2f855A] focus:ring-[#2f855A] border-gray-300 rounded" />
                                <label for="email-notifications" class="text-xs">Email notifications</label>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input id="browser-notifications" name="browser_notifications" type="checkbox" checked
                                    class="w-3.5 h-3.5 text-[#2f855A] focus:ring-[#2f855A] border-gray-300 rounded" />
                                <label for="browser-notifications" class="text-xs">Browser notifications</label>
                            </div>
                        </fieldset>
                        <div class="flex justify-end space-x-3 pt-2">
                            <button type="button" id="cancelAccountSettingsBtn"
                                class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
                            <button type="submit"
                                class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200">Save
                                Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- JavaScript -->
        <script>
                (function () {
                    if (typeof window.openCaseWithConfGate !== 'function') {
                        window.openCaseWithConfGate = function (href) {
                            try { if (window.sessionStorage) sessionStorage.setItem('confOtpPending', '1'); } catch (_) { }
                            if (href) { window.location.href = href; }
                            return false;
                        };
                    }
                })();

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

                if (userMenuBtn && userMenuDropdown) {
                    userMenuBtn.addEventListener("click", (e) => {
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
                        if (!userMenuBtn.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                            userMenuDropdown.classList.add("opacity-0", "translate-y-2", "scale-95", "pointer-events-none");
                            setTimeout(() => userMenuDropdown.classList.add("hidden"), 200);
                        }
                    });
                }

                // Profile dropdown buttons functionality
                const openProfileBtn = document.getElementById('openProfileBtn');
                const openAccountSettingsBtn = document.getElementById('openAccountSettingsBtn');
                const openPrivacySecurityBtn = document.getElementById('openPrivacySecurityBtn');
                const openSignOutBtn = document.getElementById('openSignOutBtn');

                if (openProfileBtn) {
                    openProfileBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        userMenuDropdown.classList.add("opacity-0", "translate-y-2", "scale-95", "pointer-events-none");
                        setTimeout(() => userMenuDropdown.classList.add("hidden"), 200);
                        // Add profile modal functionality here if needed
                    });
                }

                if (openAccountSettingsBtn) {
                    openAccountSettingsBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        userMenuDropdown.classList.add("opacity-0", "translate-y-2", "scale-95", "pointer-events-none");
                        setTimeout(() => userMenuDropdown.classList.add("hidden"), 200);
                        openModal('accountSettingsModal');
                    });
                }

                if (openPrivacySecurityBtn) {
                    openPrivacySecurityBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        userMenuDropdown.classList.add("opacity-0", "translate-y-2", "scale-95", "pointer-events-none");
                        setTimeout(() => userMenuDropdown.classList.add("hidden"), 200);
                        // Add privacy & security modal functionality here if needed
                    });
                }

                if (openSignOutBtn) {
                    openSignOutBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        userMenuDropdown.classList.add("opacity-0", "translate-y-2", "scale-95", "pointer-events-none");
                        setTimeout(() => userMenuDropdown.classList.add("hidden"), 200);
                        // Submit logout form
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("logout") }}';
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);
                        document.body.appendChild(form);
                        form.submit();
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

                // Account Settings Modal Event Listeners
                const closeAccountSettingsBtn = document.getElementById('closeAccountSettingsBtn');
                const cancelAccountSettingsBtn = document.getElementById('cancelAccountSettingsBtn');

                if (closeAccountSettingsBtn) {
                    closeAccountSettingsBtn.addEventListener('click', () => {
                        closeModal('accountSettingsModal');
                    });
                }

                if (cancelAccountSettingsBtn) {
                    cancelAccountSettingsBtn.addEventListener('click', () => {
                        closeModal('accountSettingsModal');
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

                // Open "Legal Management" dropdown by default
                const legalBtn = document.getElementById('legal-management-btn');
                const legalSubmenu = document.getElementById('legal-submenu');
                const legalArrow = document.getElementById('legal-arrow');

                if (legalSubmenu && !legalSubmenu.classList.contains('hidden')) {
                    legalSubmenu.classList.remove('hidden');
                    if (legalArrow) legalArrow.classList.add('rotate-180');
                }

                // Search functionality
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.addEventListener('input', function () {
                        const searchTerm = this.value.toLowerCase();
                        const rows = document.querySelectorAll('table tbody tr');

                        rows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            row.style.display = text.includes(searchTerm) ? '' : 'none';
                        });
                    });
                }

                // Export functionality
                const exportBtn = document.getElementById('exportBtn');
                if (exportBtn) {
                    exportBtn.addEventListener('click', function () {
                        Swal.fire({
                            icon: 'success',
                            title: 'Export Started',
                            text: 'Your alerts export has been queued.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    });
                }

                // Print functionality
                const printBtn = document.getElementById('printBtn');
                if (printBtn) {
                    printBtn.addEventListener('click', function () {
                        window.print();
                    });
                }

                // Modal Management
                const addAlertModal = document.getElementById("addAlertModal");

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

                // Add Alert Modal
                const addAlertBtn = document.getElementById('addAlertBtn');
                if (addAlertBtn) {
                    addAlertBtn.addEventListener('click', () => openModal(addAlertModal));
                }

                document.getElementById('closeAddAlertModal').addEventListener('click', () => closeModal(addAlertModal));
                document.getElementById('cancelAddAlert').addEventListener('click', () => closeModal(addAlertModal));

                document.getElementById('addAlertForm').addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const form = e.target;
                    const title = (form.querySelector('#alertTitle')?.value || '').trim();
                    const type = form.querySelector('#alertType')?.value || '';
                    const due_date = form.querySelector('#dueDate')?.value || '';
                    const priority = form.querySelector('#priority')?.value || 'medium';
                    const description = form.querySelector('#description')?.value || '';
                    const related_to = (form.querySelector('#relatedTo')?.value || '').trim();

                    try {
                        const resp = await fetch('{{ route('hearings.create') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({ title, type, due_date, priority, description, related_to })
                        });
                        const data = await resp.json();

                        if (!data || !data.success) {
                            throw new Error((data && data.message) || 'Failed to create alert');
                        }

                        const h = data.hearing || {};
                        const status = (h.status || 'upcoming').toLowerCase();
                        const statusCls = status === 'today' ? 'bg-yellow-100 text-yellow-800' :
                            status === 'overdue' ? 'bg-red-100 text-red-800' :
                                status === 'completed' ? 'bg-green-100 text-green-800' :
                                    'bg-blue-100 text-blue-800';

                        const prioKey = (h.priority || 'Normal').toString().toLowerCase();
                        const prioCls = prioKey === 'high' ? 'bg-red-100 text-red-800' :
                            prioKey === 'low' ? 'bg-green-100 text-green-800' :
                                'bg-yellow-100 text-yellow-800';

                        const tbody = document.querySelector('table tbody');
                        if (tbody) {
                            const tr = document.createElement('tr');
                            tr.className = 'table-row';
                            tr.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">${h.title || ''}</div>
                                <div class="text-xs text-gray-500">Case ${h.number || ''}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">${h.type || 'Hearing'}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${h.date || '-'}</div>
                                ${h.time ? `<div class="text-xs text-gray-500">${h.time}</div>` : ''}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusCls}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${prioCls}">${h.priority || 'Normal'}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="#" class="text-brand-primary hover:text-brand-primary-hover mr-3" title="View"><i class="fas fa-eye"></i></a>
                                <a href="#" class="text-blue-600 hover:text-blue-900 mr-3" title="Edit"><i class="fas fa-edit"></i></a>
                            </td>`;

                            const emptyRowCell = tbody.querySelector('tr td[colspan]');
                            if (emptyRowCell) emptyRowCell.parentElement?.remove();
                            tbody.insertBefore(tr, tbody.firstChild);
                        }

                        // Update stats
                        const statusToUpdate = status;
                        const countElementIds = {
                            'today': 'countToday',
                            'upcoming': 'countUpcoming',
                            'overdue': 'countOverdue',
                            'completed': 'countCompleted'
                        };

                        if (countElementIds[statusToUpdate]) {
                            const countElement = document.getElementById(countElementIds[statusToUpdate]);
                            if (countElement) {
                                const currentCount = parseInt(countElement.textContent || '0', 10);
                                countElement.textContent = String(currentCount + 1);
                            }
                        }

                        form.reset();
                        closeModal(addAlertModal);

                        Swal.fire({
                            icon: 'success',
                            title: 'Alert Added',
                            text: 'The alert has been added successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } catch (err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: err?.message || 'Failed to add alert.',
                            confirmButtonColor: '#059669'
                        });
                    }
                });

                // Close modal when clicking outside
                addAlertModal.addEventListener('click', function (e) {
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
</body>

</html>