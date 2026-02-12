@php
    // Get the authenticated user
    $user = auth()->user();
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

        .contract-card {
            transition: all 0.2s ease-in-out;
            border: 1px solid #e5e7eb;
        }

        .contract-card:hover {
            border-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .contract-row {
            transition: all 0.2s ease;
        }

        .contract-row:hover {
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
                <h2 class="text-2xl font-bold mb-2">Loading Contract Management</h2>
                <p class="text-white/80 text-sm mb-4">Preparing contract management system and loading contract data...
                </p>

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
                    <svg id="legal-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300 rotate-180"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="legal-submenu" class="submenu mt-1">
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
                            class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-primary transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </button>

                        <div id="user-menu-dropdown" class="dropdown-panel hidden absolute right-0 mt-3 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50">
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
                                <h1 class="text-2xl font-bold text-gray-900">Contract Management</h1>
                                <p class="text-gray-600 mt-1">Manage and track all legal contracts in one place</p>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <button id="addContractBtn"
                                    class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors font-medium flex items-center">
                                    <i class="fas fa-plus mr-2"></i> Add New Contract
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Contract Stats Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- Total Contracts Card -->
                        <div
                            class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                            </div>
                            <div class="relative flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-gray-600 font-semibold text-sm mb-2">Total Contracts</p>
                                    <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['total'] ?? 0 }}</p>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-file-contract mr-1"></i>
                                            All
                                        </span>
                                        <span class="text-xs text-gray-500">Contracts</span>
                                    </div>
                                </div>
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="fas fa-file-contract text-white text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Active Contracts Card -->
                        <div
                            class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                            </div>
                            <div class="relative flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-gray-600 font-semibold text-sm mb-2">Active</p>
                                    <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['active'] ?? 0 }}</p>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Live
                                        </span>
                                        <span class="text-xs text-gray-500">Running</span>
                                    </div>
                                </div>
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="fas fa-check-circle text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                @php
                                    $activePercent = $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 0;
                                @endphp
                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-full transition-all duration-500"
                                        style="width: {{ $activePercent }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 text-right">{{ $activePercent }}% of total</p>
                            </div>
                        </div>

                        <!-- Pending Review Card -->
                        <div
                            class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-amber-50 to-amber-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                            </div>
                            <div class="relative flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-gray-600 font-semibold text-sm mb-2">Pending Review</p>
                                    <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['pending'] ?? 0 }}</p>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Waiting
                                        </span>
                                        <span class="text-xs text-gray-500">Review</span>
                                    </div>
                                </div>
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="fas fa-clock text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                @php
                                    $pendingPercent = $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100) : 0;
                                @endphp
                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-amber-400 to-amber-600 rounded-full transition-all duration-500"
                                        style="width: {{ $pendingPercent }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 text-right">{{ $pendingPercent }}% of total</p>
                            </div>
                        </div>

                        <!-- Expiring Soon Card -->
                        <div
                            class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-red-50 to-red-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                            </div>
                            <div class="relative flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-gray-600 font-semibold text-sm mb-2">Expiring Soon</p>
                                    <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['expiring'] ?? 0 }}</p>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Alert
                                        </span>
                                        <span class="text-xs text-gray-500">Urgent</span>
                                    </div>
                                </div>
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                @php
                                    $expiringPercent = $stats['total'] > 0 ? round(($stats['expiring'] / $stats['total']) * 100) : 0;
                                @endphp
                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-red-400 to-red-600 rounded-full transition-all duration-500"
                                        style="width: {{ $expiringPercent }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 text-right">{{ $expiringPercent }}% of total</p>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="mb-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="relative flex-1 max-w-md">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="searchInput"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-primary focus:border-brand-primary block w-full pl-10 p-2.5"
                                    placeholder="Search contracts...">
                            </div>
                            <div class="flex space-x-3">
                                <select id="filterStatus"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-primary focus:border-brand-primary block w-full p-2.5">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="expired">Expired</option>
                                </select>
                                <select id="filterType"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-primary focus:border-brand-primary block w-full p-2.5">
                                    <option value="">All Types</option>
                                    <option value="nda">NDA</option>
                                    <option value="service">Service</option>
                                    <option value="employment">Employment</option>
                                    <option value="employee">Employee</option>
                                    <option value="consultancy">Consultancy</option>
                                    <option value="internship">Internship</option>
                                    <option value="probation">Probation</option>
                                    <option value="vendor">Vendor</option>
                                    <option value="supplier">Supplier</option>
                                    <option value="lease">Lease</option>
                                    <option value="license">License</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="purchase">Purchase</option>
                                    <option value="sales">Sales</option>
                                    <option value="partnership">Partnership</option>
                                    <option value="loan">Loan</option>
                                </select>
                                <button
                                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                    <i class="fas fa-filter text-gray-600 mr-2"></i>
                                    <span>Filter</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Contracts Table -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="font-semibold text-lg text-gray-900">Contracts</h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Code</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Title</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Type</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Company</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Start Date</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            End Date</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse(($contracts ?? []) as $c)
                                        @php
                                            // Initialize status value with default 'draft' if not set
                                            $statusValue = strtolower($c->contract_status ?? $c->status ?? 'draft');

                                            // Set default dates if not set
                                            $startDate = $c->contract_start_date ?? $c->start_date ?? null;
                                            $endDate = $c->contract_end_date ?? $c->end_date ?? $c->expires_on ?? $c->contract_expiration ?? null;

                                            // Convert string dates to Carbon instances if they exist
                                            $startDate = $startDate ? \Carbon\Carbon::parse($startDate) : null;
                                            $endDate = $endDate ? \Carbon\Carbon::parse($endDate) : null;

                                            $startDateDisplay = (!empty($c->formatted_start_date) && $c->formatted_start_date !== 'N/A')
                                                ? $c->formatted_start_date
                                                : ($startDate ? $startDate->format('M d, Y') : null);
                                            $endDateDisplay = (!empty($c->formatted_end_date) && $c->formatted_end_date !== 'N/A')
                                                ? $c->formatted_end_date
                                                : ($endDate ? $endDate->format('M d, Y') : null);

                                            // Calculate days remaining and status
                                            $daysRemaining = null;
                                            $isExpired = false;
                                            $isExpiringSoon = false;

                                            if ($endDate) {
                                                $now = now();
                                                $daysRemaining = $now->diffInDays($endDate, false);
                                                $isExpired = $daysRemaining < 0;
                                                $isExpiringSoon = $daysRemaining >= 0 && $daysRemaining <= 30;

                                                // Auto-update status based on dates if not explicitly set
                                                if ($isExpired && $statusValue !== 'terminated') {
                                                    $statusValue = 'expired';
                                                } elseif ($isExpiringSoon && $statusValue === 'active') {
                                                    $statusValue = 'active';
                                                }
                                            }

                                            // Ensure status is one of the expected values
                                            $validStatuses = ['draft', 'active', 'pending', 'expired', 'terminated', 'renewed'];
                                            if (!in_array($statusValue, $validStatuses)) {
                                                $statusValue = 'draft'; // Default to draft if status is invalid
                                            }

                                            $statusConfig = [
                                                'draft' => [
                                                    'bg' => 'bg-gray-100',
                                                    'text' => 'text-gray-800',
                                                    'ring' => 'ring-gray-300',
                                                    'icon' => 'fa-file-lines',
                                                    'label' => 'Draft'
                                                ],
                                                'active' => [
                                                    'bg' => 'bg-green-50',
                                                    'text' => 'text-green-800',
                                                    'ring' => 'ring-green-600/20',
                                                    'icon' => 'fa-circle-check',
                                                    'label' => 'Active'
                                                ],
                                                'expired' => [
                                                    'bg' => 'bg-red-50',
                                                    'text' => 'text-red-800',
                                                    'ring' => 'ring-red-600/20',
                                                    'icon' => 'fa-clock-rotate-left',
                                                    'label' => 'Expired'
                                                ],
                                                'terminated' => [
                                                    'bg' => 'bg-red-50',
                                                    'text' => 'text-red-800',
                                                    'ring' => 'ring-red-600/20',
                                                    'icon' => 'fa-ban',
                                                    'label' => 'Terminated'
                                                ],
                                                'renewed' => [
                                                    'bg' => 'bg-blue-50',
                                                    'text' => 'text-blue-800',
                                                    'ring' => 'ring-blue-600/20',
                                                    'icon' => 'fa-rotate',
                                                    'label' => 'Renewed'
                                                ],
                                                'pending' => [
                                                    'bg' => 'bg-yellow-50',
                                                    'text' => 'text-yellow-800',
                                                    'ring' => 'ring-yellow-600/20',
                                                    'icon' => 'fa-clock',
                                                    'label' => 'Pending'
                                                ]
                                            ];

                                            // Get status config or default to draft
                                            $statusInfo = $statusConfig[$statusValue] ?? [
                                                'bg' => 'bg-gray-50',
                                                'text' => 'text-gray-700',
                                                'ring' => 'ring-0',
                                                'icon' => 'fa-circle-info',
                                                'label' => ucfirst($statusValue)
                                            ];

                                            $statusClasses = "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$statusInfo['bg']} {$statusInfo['text']} ring-1 ring-inset {$statusInfo['ring']}";
                                            $statusLabel = $statusInfo['label'];
                                            $statusIcon = $statusInfo['icon'];
                                        @endphp
                                        <tr class="contract-row" data-id="{{ $c->id }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $c->code ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $c->title ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ ucfirst($c->type ?? 'N/A') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="{{ $statusClasses }}">
                                                    <i class="fas {{ $statusIcon }} mr-1.5"></i>
                                                    {{ $statusLabel }}
                                                </span>
                                                @if($isExpired && $statusValue !== 'expired')
                                                    <span
                                                        class="ml-2 px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800 border border-red-200">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>Expired
                                                    </span>
                                                @elseif($isExpiringSoon && $statusValue === 'active')
                                                    <span
                                                        class="ml-2 px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                        <i class="fas fa-clock mr-1"></i>Expiring Soon
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $c->company ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($startDateDisplay)
                                                    <div class="flex items-center">
                                                        <i class="fas fa-calendar-day mr-2 text-gray-400"></i>
                                                        <span>{{ $startDateDisplay }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">Not set</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($endDateDisplay)
                                                    <div class="flex flex-col">
                                                        <div class="flex items-center">
                                                            <i class="fas fa-calendar-check mr-2 text-gray-400"></i>
                                                            <span>{{ $endDateDisplay }}</span>
                                                        </div>
                                                        @if($isExpired)
                                                            <span class="text-xs text-red-600 mt-1">
                                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                                Expired {{ abs($daysRemaining) }} days ago
                                                            </span>
                                                        @elseif($isExpiringSoon)
                                                            <span class="text-xs text-yellow-600 mt-1">
                                                                <i class="fas fa-clock mr-1"></i>
                                                                {{ $daysRemaining }} days left
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">No end date</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button
                                                    class="text-brand-primary hover:text-brand-primary-hover mr-3 viewContractBtn"
                                                    data-id="{{ $c->id }}" data-code="{{ $c->code }}"
                                                    data-title="{{ $c->title }}" data-type="{{ $c->type }}"
                                                    data-status="{{ $statusValue }}" data-start-date="{{ $startDate }}"
                                                    data-end-date="{{ $endDate }}" data-value="{{ $c->value ?? '' }}"
                                                    data-notes="{{ $c->notes ?? '' }}"
                                                    data-created-at="{{ is_object($c->created_at) ? $c->created_at->format('M d, Y') : (is_string($c->created_at) ? \Carbon\Carbon::parse($c->created_at)->format('M d, Y') : 'N/A') }}"
                                                    data-updated-at="{{ is_object($c->updated_at) ? $c->updated_at->format('M d, Y') : (is_string($c->updated_at) ? \Carbon\Carbon::parse($c->updated_at)->format('M d, Y') : 'N/A') }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="text-blue-600 hover:text-blue-800 mr-3 editContractBtn"
                                                    data-id="{{ $c->id }}" data-code="{{ $c->code }}"
                                                    data-title="{{ $c->title }}" data-type="{{ $c->type }}"
                                                    data-status="{{ $statusValue }}" data-start-date="{{ $startDate }}"
                                                    data-end-date="{{ $endDate }}" data-value="{{ $c->value ?? '' }}"
                                                    data-notes="{{ $c->notes ?? '' }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="text-red-600 hover:text-red-800 deleteContractBtn"
                                                    data-id="{{ $c->id }}" data-title="{{ $c->title }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-6 text-center text-sm text-gray-500">
                                                No contracts found.
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

        <!-- Modals -->
        <!-- Add Contract Modal -->
        <div id="addContractModal" class="modal hidden" aria-modal="true" role="dialog"
            aria-labelledby="add-contract-title">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl mx-4 fade-in" role="document">
                <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                    <h3 id="add-contract-title" class="font-semibold text-lg text-gray-900">Add New Contract</h3>
                    <button id="closeAddContractModal" type="button"
                        class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200"
                        aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6">
                    <form id="addContractForm" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="contractCode" class="block text-sm font-medium text-gray-700 mb-1">Contract
                                    Code</label>
                                <div class="relative">
                                    <input type="text" id="contractCode" name="code" readonly
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-700"
                                        value="CTR-{{ strtoupper(uniqid()) }}">
                                    <button type="button" id="regenerateCode"
                                        class="absolute inset-y-0 right-0 px-3 flex items-center text-brand-primary hover:text-brand-primary-hover">
                                        <i class="fas fa-sync-alt text-sm"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label for="contractTitle" class="block text-sm font-medium text-gray-700 mb-1">Title
                                    <span class="text-red-500">*</span></label>
                                <input type="text" id="contractTitle" name="title" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent"
                                    placeholder="Contract Title">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="contractType" class="block text-sm font-medium text-gray-700 mb-1">Type
                                    <span class="text-red-500">*</span></label>
                                <select id="contractType" name="type" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                                    <option value="">Select Type</option>
                                    <option value="nda">NDA</option>
                                    <option value="service">Service</option>
                                    <option value="employment">Employment</option>
                                    <option value="employee">Employee</option>
                                    <option value="consultancy">Consultancy</option>
                                    <option value="internship">Internship</option>
                                    <option value="probation">Probation</option>
                                    <option value="vendor">Vendor</option>
                                    <option value="supplier">Supplier</option>
                                    <option value="lease">Lease</option>
                                    <option value="license">License</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="purchase">Purchase</option>
                                    <option value="sales">Sales</option>
                                    <option value="partnership">Partnership</option>
                                    <option value="loan">Loan</option>
                                </select>
                            </div>
                            <div>
                                <label for="contractCompany"
                                    class="block text-sm font-medium text-gray-700 mb-1">Company
                                    <span class="text-red-500">*</span></label>
                                <input type="text" id="contractCompany" name="company" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent"
                                    placeholder="Company Name">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="contractStatus" class="block text-sm font-medium text-gray-700 mb-1">Status
                                    <span class="text-red-500">*</span></label>
                                <select id="contractStatus" name="status" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                                    <option value="draft">Draft</option>
                                    <option value="active" selected>Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="expired">Expired</option>
                                    <option value="terminated">Terminated</option>
                                </select>
                            </div>
                            <div>
                                <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Start Date
                                    <span class="text-red-500">*</span></label>
                                <input type="date" id="startDate" name="start_date" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                            </div>
                            <div>
                                <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">End
                                    Date</label>
                                <input type="date" id="endDate" name="end_date"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="contractFile" class="block text-sm font-medium text-gray-700 mb-2">Contract
                                Document</label>
                            <div class="flex items-center justify-center w-full">
                                <label
                                    class="flex flex-col w-full h-32 border-2 border-dashed border-gray-300 hover:border-gray-400 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <div class="flex flex-col items-center justify-center pt-7">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="pt-1 text-sm tracking-wider text-gray-500">
                                            Upload a file or drag and drop
                                        </p>
                                        <p class="text-xs text-gray-500">PDF, DOC, DOCX up to 10MB</p>
                                    </div>
                                    <input id="contractFile" name="file" type="file" class="opacity-0">
                                </label>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="contractDescription"
                                class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="contractDescription" name="description" rows="2"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent"
                                placeholder="Brief description of the contract"></textarea>
                        </div>
                    </form>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" id="cancelAddContract"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" form="addContractForm"
                        class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors text-sm font-medium">
                        Save Contract
                    </button>
                </div>
            </div>
        </div>

        <!-- View Contract Modal -->
        <div id="viewContractModal" class="modal hidden" aria-modal="true" role="dialog"
            aria-labelledby="view-contract-title">
            <div class="bg-white rounded-lg shadow-lg w-[380px] max-w-full mx-4 fade-in" role="document">
                <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
                    <h3 id="view-contract-title" class="font-semibold text-sm text-gray-900">Contract Details</h3>
                    <button id="closeViewContractModal" type="button"
                        class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200"
                        aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-6 pt-5 pb-6 text-sm text-gray-700 space-y-3">
                    <div><span class="font-semibold">Contract ID:</span> <span id="viewContractId"
                            class="text-gray-900"></span></div>
                    <div><span class="font-semibold">Title:</span> <span id="viewContractTitle"
                            class="text-gray-900"></span></div>
                    <div><span class="font-semibold">Company:</span> <span id="viewContractCompany"
                            class="text-gray-900"></span></div>
                    <div><span class="font-semibold">Type:</span> <span id="viewContractType"
                            class="text-gray-900"></span>
                    </div>
                    <div><span class="font-semibold">Status:</span> <span id="viewContractStatus"
                            class="text-gray-900"></span></div>
                    <div><span class="font-semibold">Start Date:</span> <span id="viewContractStartDate"
                            class="text-gray-900"></span></div>
                    <div><span class="font-semibold">End Date:</span> <span id="viewContractEndDate"
                            class="text-gray-900"></span></div>
                    <div><span class="font-semibold">Created:</span> <span id="viewContractCreated"
                            class="text-gray-900"></span></div>
                    <div class="pt-4 flex justify-end">
                        <button id="closeViewContractModal2" type="button"
                            class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors text-sm font-medium">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Contract Modal -->
        <div id="editContractModal" class="modal hidden" aria-modal="true" role="dialog"
            aria-labelledby="edit-contract-title">
            <div class="bg-white rounded-lg shadow-lg w-[380px] max-w-full mx-4 fade-in" role="document">
                <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
                    <h3 id="edit-contract-title" class="font-semibold text-sm text-gray-900">Edit Contract</h3>
                    <button id="closeEditContractModal" type="button"
                        class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200"
                        aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-6 pt-5 pb-6">
                    <form id="editContractForm" class="space-y-3 text-sm text-gray-700">
                        <input type="hidden" id="editContractId">
                        <div>
                            <label for="editContractTitle"
                                class="block text-xs font-medium text-gray-700 mb-1">Title</label>
                            <input type="text" id="editContractTitle"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent"
                                required>
                        </div>
                        <div>
                            <label for="editContractCompany"
                                class="block text-xs font-medium text-gray-700 mb-1">Company</label>
                            <input type="text" id="editContractCompany"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent"
                                required>
                        </div>
                        <div>
                            <label for="editContractType"
                                class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                            <select id="editContractType"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent"
                                required>
                                <option value="nda">NDA</option>
                                <option value="service">Service</option>
                                <option value="employment">Employment</option>
                                <option value="employee">Employee</option>
                                <option value="consultancy">Consultancy</option>
                                <option value="internship">Internship</option>
                                <option value="probation">Probation</option>
                                <option value="vendor">Vendor</option>
                                <option value="supplier">Supplier</option>
                                <option value="lease">Lease</option>
                                <option value="license">License</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="purchase">Purchase</option>
                                <option value="sales">Sales</option>
                                <option value="partnership">Partnership</option>
                                <option value="loan">Loan</option>
                            </select>
                        </div>
                        <div>
                            <label for="editContractStatus"
                                class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                            <select id="editContractStatus"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent"
                                required>
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-3 pt-2">
                            <button type="button" id="cancelEditContract"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover rounded-lg transition-colors">Save
                                Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Contract Modal -->
        <div id="deleteContractModal" class="modal hidden" aria-modal="true" role="dialog"
            aria-labelledby="delete-contract-title">
            <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4 fade-in" role="document">
                <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
                    <h3 id="delete-contract-title" class="font-semibold text-sm text-gray-900">Delete Contract</h3>
                    <button id="closeDeleteContractModal" type="button"
                        class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200"
                        aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-6 pt-5 pb-6 text-center">
                    <div class="mx-auto mb-4 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-700 mb-3">Are you sure you want to delete <span class="font-semibold"
                            id="deleteContractTitle"></span>?</p>
                    <p class="text-xs text-gray-500 mb-4">Contract ID: <span id="deleteContractId"
                            class="font-mono"></span>
                    </p>
                    <div class="flex justify-center space-x-3">
                        <button type="button" id="cancelDeleteContract"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                        <button type="button" id="confirmDeleteContract"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">Delete</button>
                    </div>
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

                // Open "Legal Management" dropdown by default since we're on Contract Management page
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

                // Filter functionality
                const filterStatus = document.getElementById('filterStatus');
                const filterType = document.getElementById('filterType');

                function applyFilters() {
                    const status = filterStatus.value;
                    const type = filterType.value;
                    const rows = document.querySelectorAll('table tbody tr');

                    rows.forEach(row => {
                        let show = true;

                        if (status) {
                            const rowStatus = row.querySelector('.contract-status')?.textContent.toLowerCase() || '';
                            if (!rowStatus.includes(status)) {
                                show = false;
                            }
                        }

                        if (type && show) {
                            const rowType = row.cells[2]?.textContent.toLowerCase() || '';
                            if (!rowType.includes(type)) {
                                show = false;
                            }
                        }

                        row.style.display = show ? '' : 'none';
                    });
                }

                if (filterStatus) filterStatus.addEventListener('change', applyFilters);
                if (filterType) filterType.addEventListener('change', applyFilters);

                // Modal Management
                const addContractModal = document.getElementById("addContractModal");
                const viewContractModal = document.getElementById("viewContractModal");
                const editContractModal = document.getElementById("editContractModal");
                const deleteContractModal = document.getElementById("deleteContractModal");

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

                // Add Contract Modal
                document.getElementById('addContractBtn').addEventListener('click', () => {
                    openModal(addContractModal);
                });

                document.getElementById('closeAddContractModal').addEventListener('click', () => closeModal(addContractModal));
                document.getElementById('cancelAddContract').addEventListener('click', () => closeModal(addContractModal));

                // Regenerate contract code
                document.getElementById('regenerateCode').addEventListener('click', function () {
                    const timestamp = new Date().getTime().toString(36);
                    const random = Math.random().toString(36).substr(2, 6).toUpperCase();
                    document.getElementById('contractCode').value = `CTR-${timestamp}-${random}`;

                    const icon = this.querySelector('i');
                    icon.classList.add('animate-spin');
                    setTimeout(() => {
                        icon.classList.remove('animate-spin');
                    }, 500);
                });

                // View Contract Modal
                document.addEventListener('click', function (e) {
                    if (e.target.closest('.viewContractBtn')) {
                        const btn = e.target.closest('.viewContractBtn');
                        document.getElementById('viewContractId').textContent = btn.dataset.code || 'N/A';
                        document.getElementById('viewContractTitle').textContent = btn.dataset.title || 'N/A';
                        document.getElementById('viewContractCompany').textContent = btn.dataset.company || 'N/A';
                        document.getElementById('viewContractType').textContent = btn.dataset.type || 'N/A';
                        document.getElementById('viewContractStatus').textContent = btn.dataset.status || 'N/A';
                        document.getElementById('viewContractStartDate').textContent = btn.dataset.startDate || 'N/A';
                        document.getElementById('viewContractEndDate').textContent = btn.dataset.endDate || 'N/A';
                        document.getElementById('viewContractCreated').textContent = btn.dataset.createdAt || 'N/A';
                        openModal(viewContractModal);
                    }
                });

                document.getElementById('closeViewContractModal').addEventListener('click', () => closeModal(viewContractModal));
                document.getElementById('closeViewContractModal2').addEventListener('click', () => closeModal(viewContractModal));

                // Edit Contract Modal
                document.addEventListener('click', function (e) {
                    if (e.target.closest('.editContractBtn')) {
                        const btn = e.target.closest('.editContractBtn');
                        document.getElementById('editContractId').value = btn.dataset.id;
                        document.getElementById('editContractTitle').value = btn.dataset.title || '';
                        document.getElementById('editContractCompany').value = btn.dataset.company || '';
                        document.getElementById('editContractType').value = btn.dataset.type || '';
                        document.getElementById('editContractStatus').value = btn.dataset.status || '';
                        openModal(editContractModal);
                    }
                });

                document.getElementById('closeEditContractModal').addEventListener('click', () => closeModal(editContractModal));
                document.getElementById('cancelEditContract').addEventListener('click', () => closeModal(editContractModal));

                // Delete Contract Modal
                document.addEventListener('click', function (e) {
                    if (e.target.closest('.deleteContractBtn')) {
                        const btn = e.target.closest('.deleteContractBtn');
                        document.getElementById('deleteContractTitle').textContent = btn.dataset.title || 'N/A';
                        document.getElementById('deleteContractId').textContent = btn.dataset.id || 'N/A';
                        openModal(deleteContractModal);
                    }
                });

                document.getElementById('closeDeleteContractModal').addEventListener('click', () => closeModal(deleteContractModal));
                document.getElementById('cancelDeleteContract').addEventListener('click', () => closeModal(deleteContractModal));

                // Close modals when clicking outside
                const modals = [addContractModal, viewContractModal, editContractModal, deleteContractModal];
                modals.forEach(modal => {
                    modal.addEventListener('click', function (e) {
                        if (e.target === this) {
                            closeModal(this);
                        }
                    });
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