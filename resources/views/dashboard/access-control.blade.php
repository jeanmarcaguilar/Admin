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
    <style>
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .submenu {
            transition: all 0.3s ease;
        }

        .rotate-180 {
            transform: rotate(180deg);
        }

        .dropdown-panel {
            transform-origin: top right;
        }

        .permission-toggle {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .permission-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .permission-toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e5e7eb;
            transition: .3s;
            border-radius: 34px;
        }

        .permission-toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }

        input:checked+.permission-toggle-slider {
            background-color: #059669;
        }

        input:checked+.permission-toggle-slider:before {
            transform: translateX(20px);
        }

        .role-card {
            transition: all 0.2s ease-in-out;
            border: 1px solid #e5e7eb;
        }

        .role-card:hover {
            border-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
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
                <h2 class="text-2xl font-bold mb-2">Loading Access Control</h2>
                <p class="text-white/80 text-sm mb-4">Preparing access management system and loading user permissions...
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
                <a href="/dashboard" class="flex items-center gap-3 w-full rounded-xl px-2 py-2
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
                <a href="/dashboard"
                    class="mt-3 flex items-center justify-between px-4 py-3 rounded-xl
                       text-gray-700 hover:bg-green-50 hover:text-brand-primary
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
                            class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        Microfinance Admin © 2026<br />
                        Administrative System
                    </div>
                </div>
            </div>
        </aside>

        <!-- ✅ MAIN WRAPPER (header starts after sidebar width) -->
        <div class="md:pl-72">

            <!-- ✅ TOP HEADER (ONLY RIGHT SIDE AREA) -->
            <header class="h-16 bg-white flex items-center justify-between px-4 sm:px-6 relative
                       shadow-[0_2px_8px_rgba(0,0,0,0.06)]">

                <!-- ✅ BORDER COVER (removes the vertical line only in header height) -->
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

                        <!-- User Dropdown Menu -->
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

            <!-- Main Content Area -->
            <main class="p-6">
                <div class="max-w-7xl mx-auto">
                    <!-- Access Control Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Total Users Card -->
                        <div
                            class="group relative bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full -mr-12 -mt-12 opacity-50 group-hover:opacity-75 transition-opacity">
                            </div>
                            <div class="relative flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-gray-600 font-semibold text-base mb-3">Total Users</p>
                                    <p class="font-bold text-4xl text-gray-900 mb-2">{{ App\Models\User::count() }}</p>
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            <i class="bx bx-user mr-2"></i>
                                            Active
                                        </span>
                                        <span class="text-sm text-gray-500">Users</span>
                                    </div>
                                </div>
                                <div
                                    class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="bx bx-user text-white text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- User Roles Card -->
                        <div
                            class="group relative bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-full -mr-12 -mt-12 opacity-50 group-hover:opacity-75 transition-opacity">
                            </div>
                            <div class="relative flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-gray-600 font-semibold text-base mb-3">User Roles</p>
                                    <p class="font-bold text-4xl text-gray-900 mb-2">4</p>
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                                            <i class="bx bx-shield mr-2"></i>
                                            Types
                                        </span>
                                        <span class="text-sm text-gray-500">Roles</span>
                                    </div>
                                </div>
                                <div
                                    class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="bx bx-shield text-white text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Page Header with Search -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Access Control</h1>
                                <p class="text-gray-600 mt-1">Manage user permissions and access levels</p>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="userSearch"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-primary focus:border-brand-primary block w-full pl-10 pr-3 py-2.5"
                                    placeholder="Search users...">
                            </div>
                        </div>
                    </div>

                    <!-- User List Section -->
                    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">User Access List</h2>
                            <p class="text-sm text-gray-500 mt-1">Manage individual user permissions</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            User</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Role</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Department</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Last Login</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach(App\Models\User::all() as $user)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div
                                                        class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <span
                                                            class="text-blue-600 text-sm font-medium">{{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}</span>
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm font-medium text-gray-900">{{ $user->roles }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $user->department ?? 'Not Assigned' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button
                                                    class="view-user-btn text-brand-primary hover:text-brand-primary-hover"
                                                    data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}"
                                                    data-user-email="{{ $user->email }}" data-user-role="{{ $user->role }}"
                                                    data-user-department="{{ $user->department ?? 'Not Assigned' }}"
                                                    data-user-last-login="{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}">View</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-500">
                                    Showing {{ App\Models\User::count() }} users
                                </div>
                                <div class="flex space-x-2">
                                    <button
                                        class="px-3 py-1 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50">
                                        Previous
                                    </button>
                                    <button
                                        class="px-3 py-1 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50">
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
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
                        btn.addEventListener("click", (e) => {
                            e.stopPropagation();

                            // Close all other dropdowns first
                            Object.entries(dropdowns).forEach(([otherBtnId, otherSubmenuId]) => {
                                if (otherBtnId !== btnId) {
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
                            const isHidden = submenu.classList.contains("hidden");

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

                // Close dropdowns when clicking outside
                document.addEventListener("click", (e) => {
                    Object.entries(dropdowns).forEach(([btnId, submenuId]) => {
                        const btn = document.getElementById(btnId);
                        const submenu = document.getElementById(submenuId);
                        const arrow = document.getElementById(btnId.replace('-btn', '-arrow'));

                        if (btn && submenu && !btn.contains(e.target) && !submenu.contains(e.target)) {
                            submenu.classList.add("hidden");
                            if (arrow) arrow.classList.remove("rotate-180");
                        }
                    });
                });

                // User dropdown menu
                const userMenuBtn = document.getElementById('userMenuBtn');
                const userMenuDropdown = document.getElementById('userMenuDropdown');

                // Toggle user dropdown
                if (userMenuBtn && userMenuDropdown) {
                    userMenuBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        userMenuDropdown.classList.toggle('hidden');
                    });
                }

                // Close dropdowns when clicking outside
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

                const openAccountSettingsBtn = document.getElementById('openAccountSettingsBtn');
                const closeAccountSettingsBtn = document.getElementById('closeAccountSettingsBtn');
                const cancelAccountSettingsBtn = document.getElementById('cancelAccountSettingsBtn');
                const accountSettingsModal = document.getElementById('accountSettingsModal');

                const openPrivacySecurityBtn = document.getElementById('openPrivacySecurityBtn');
                const closePrivacySecurityBtn = document.getElementById('closePrivacySecurityBtn');
                const cancelPrivacySecurityBtn = document.getElementById('cancelPrivacySecurityBtn');
                const privacySecurityModal = document.getElementById('privacySecurityModal');

                const openSignOutBtn = document.getElementById('openSignOutBtn');
                const cancelSignOutBtn = document.getElementById('cancelSignOutBtn');
                const cancelSignOutBtn2 = document.getElementById('cancelSignOutBtn2');
                const signOutModal = document.getElementById('signOutModal');

                // Profile modal
                if (openProfileBtn) {
                    openProfileBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        profileModal.classList.add('active');
                        userMenuDropdown.classList.add('hidden');
                    });
                }

                if (closeProfileBtn) {
                    closeProfileBtn.addEventListener('click', () => {
                        profileModal.classList.remove('active');
                    });
                }

                if (closeProfileBtn2) {
                    closeProfileBtn2.addEventListener('click', () => {
                        profileModal.classList.remove('active');
                    });
                }

                // Account settings modal
                if (openAccountSettingsBtn) {
                    openAccountSettingsBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        accountSettingsModal.classList.add('active');
                        userMenuDropdown.classList.add('hidden');
                    });
                }

                if (closeAccountSettingsBtn) {
                    closeAccountSettingsBtn.addEventListener('click', () => {
                        accountSettingsModal.classList.remove('active');
                    });
                }

                if (cancelAccountSettingsBtn) {
                    cancelAccountSettingsBtn.addEventListener('click', () => {
                        accountSettingsModal.classList.remove('active');
                    });
                }

                // Privacy & security modal
                if (openPrivacySecurityBtn) {
                    openPrivacySecurityBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        privacySecurityModal.classList.add('active');
                        userMenuDropdown.classList.add('hidden');
                    });
                }

                if (closePrivacySecurityBtn) {
                    closePrivacySecurityBtn.addEventListener('click', () => {
                        privacySecurityModal.classList.remove('active');
                    });
                }

                if (cancelPrivacySecurityBtn) {
                    cancelPrivacySecurityBtn.addEventListener('click', () => {
                        privacySecurityModal.classList.remove('active');
                    });
                }

                // Sign out modal
                if (openSignOutBtn) {
                    openSignOutBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        signOutModal.classList.add('active');
                        userMenuDropdown.classList.add('hidden');
                    });
                }

                if (cancelSignOutBtn) {
                    cancelSignOutBtn.addEventListener('click', () => {
                        signOutModal.classList.remove('active');
                    });
                }

                if (cancelSignOutBtn2) {
                    cancelSignOutBtn2.addEventListener('click', () => {
                        signOutModal.classList.remove('active');
                    });
                }

                // Close modals when clicking outside
                window.addEventListener('click', (e) => {
                    if (profileModal && !profileModal.contains(e.target) && openProfileBtn && !openProfileBtn.contains(e.target)) {
                        profileModal.classList.remove('active');
                    }
                    if (accountSettingsModal && !accountSettingsModal.contains(e.target) && openAccountSettingsBtn && !openAccountSettingsBtn.contains(e.target)) {
                        accountSettingsModal.classList.remove('active');
                    }
                    if (privacySecurityModal && !privacySecurityModal.contains(e.target) && openPrivacySecurityBtn && !openPrivacySecurityBtn.contains(e.target)) {
                        privacySecurityModal.classList.remove('active');
                    }
                    if (signOutModal && !signOutModal.contains(e.target)) {
                        signOutModal.classList.remove('active');
                    }
                });

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

                // Permission toggle functionality
                document.querySelectorAll('.permission-toggle input').forEach(toggle => {
                    toggle.addEventListener('change', function () {
                        const permissionName = this.closest('.flex').querySelector('span').textContent;
                        const isEnabled = this.checked;

                        // In a real application, you would make an API call here
                        console.log(`Permission "${permissionName}" ${isEnabled ? 'enabled' : 'disabled'}`);

                        // Update permission summary
                        updatePermissionSummary();
                    });
                });

                function updatePermissionSummary() {
                    const totalPermissions = document.querySelectorAll('.permission-toggle input').length;
                    const enabledPermissions = document.querySelectorAll('.permission-toggle input:checked').length;
                    const percentage = Math.round((enabledPermissions / totalPermissions) * 100);

                    // Update summary display
                    const summaryElement = document.querySelector('.text-green-600');
                    const progressBar = document.querySelector('.bg-green-500');

                    if (summaryElement && progressBar) {
                        summaryElement.textContent = `${percentage}% Enabled`;
                        progressBar.style.width = `${percentage}%`;

                        // Also update the text
                        const textElement = document.querySelector('.text-xs.text-gray-500.mt-1');
                        if (textElement) {
                            textElement.textContent = `Total permissions: ${totalPermissions} | Enabled: ${enabledPermissions}`;
                        }
                    }
                }

                // Initialize permission summary
                updatePermissionSummary();

                // User search functionality
                const userSearchInput = document.getElementById('userSearch');
                if (userSearchInput) {
                    userSearchInput.addEventListener('input', function () {
                        const searchTerm = this.value.toLowerCase();
                        const userRows = document.querySelectorAll('tbody tr');

                        console.log('Search term:', searchTerm);
                        console.log('Found rows:', userRows.length);

                        userRows.forEach((row, index) => {
                            const text = row.textContent.toLowerCase();
                            const shouldShow = text.includes(searchTerm);
                            row.style.display = shouldShow ? '' : 'none';

                            if (searchTerm.length > 0) {
                                console.log(`Row ${index}: ${shouldShow ? 'SHOW' : 'HIDE'} - "${text.substring(0, 50)}..."`);
                            }
                        });
                    });
                } else {
                    console.log('Search input not found');
                }

                // User modal functionality
                const userModal = document.getElementById('userModal');
                const closeUserModal = document.getElementById('closeUserModal');
                const closeUserModalBtn = document.getElementById('closeUserModalBtn');

                // View button click handlers
                document.querySelectorAll('.view-user-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        const userId = this.dataset.userId;
                        const userName = this.dataset.userName;
                        const userEmail = this.dataset.userEmail;
                        const userRole = this.dataset.userRole;
                        const userDepartment = this.dataset.userDepartment;
                        const userLastLogin = this.dataset.userLastLogin;

                        // Generate user initials for avatar
                        const nameParts = userName.split(' ');
                        const initials = nameParts.length > 1
                            ? nameParts[0][0].toUpperCase() + nameParts[1][0].toUpperCase()
                            : userName.substring(0, 2).toUpperCase();

                        // Populate modal with user data
                        document.getElementById('modalUserName').textContent = userName;
                        document.getElementById('modalUserRole').textContent = userRole;
                        document.getElementById('modalUserEmail').textContent = userEmail;
                        document.getElementById('modalUserDepartment').textContent = userDepartment;
                        document.getElementById('modalUserLastLogin').textContent = userLastLogin;
                        document.getElementById('modalUserAvatar').textContent = initials;

                        // Show modal with animation
                        userModal.classList.remove('hidden');
                        setTimeout(() => {
                            const modalContent = document.getElementById('modalContent');
                            modalContent.classList.remove('scale-95', 'opacity-0');
                            modalContent.classList.add('scale-100', 'opacity-100');
                        }, 10);
                    });
                });

                // Close modal handlers with animation
                function closeModal() {
                    const modalContent = document.getElementById('modalContent');
                    modalContent.classList.remove('scale-100', 'opacity-100');
                    modalContent.classList.add('scale-95', 'opacity-0');

                    setTimeout(() => {
                        userModal.classList.add('hidden');
                    }, 300);
                }

                closeUserModal.addEventListener('click', closeModal);
                closeUserModalBtn.addEventListener('click', closeModal);

                // Close modal when clicking outside
                userModal.addEventListener('click', (e) => {
                    if (e.target === userModal) {
                        closeModal();
                    }
                });
            });
        </script>

        <!-- User Details Modal -->
        <div id="userModal"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full transform transition-all duration-500 scale-95 opacity-0"
                id="modalContent">
                <!-- Modal Header with Enhanced Gradient -->
                <div
                    class="bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 px-6 py-6 rounded-t-3xl relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="
                            60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg" %3E%3Cg fill="none"
                            fill-rule="evenodd" %3E%3Cg fill="%23ffffff" fill-opacity="0.4" %3E%3Ccircle cx="30" cy="30"
                            r="2" /%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-size: 60px 60px;"></div>
                    </div>

                    <div class="relative flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <i class="fas fa-user-circle text-white text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-white">User Details</h3>
                        </div>
                        <button id="closeUserModal"
                            class="text-white/80 hover:text-white hover:bg-white/20 rounded-xl p-2 transition-all duration-200 backdrop-blur-sm">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body with Enhanced Design -->
                <div class="p-8 bg-gradient-to-br from-gray-50 to-white space-y-6">
                    <!-- Enhanced User Avatar and Basic Info -->
                    <div class="flex items-center gap-5 pb-6 border-b border-gray-200">
                        <div class="relative">
                            <div
                                class="w-20 h-20 bg-gradient-to-br from-violet-500 via-purple-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-xl ring-4 ring-white/50">
                                <span id="modalUserAvatar" class="text-white text-2xl font-bold"></span>
                            </div>
                            <div
                                class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-3 border-white shadow-lg">
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 id="modalUserName" class="text-xl font-bold text-gray-900 mb-1"></h4>
                            <div class="flex items-center gap-2">
                                <p id="modalUserRole"
                                    class="text-sm text-purple-600 font-semibold bg-purple-100 px-3 py-1 rounded-full">
                                </p>
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced User Information Grid -->
                    <div class="grid grid-cols-1 gap-4">
                        <div
                            class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-4 border border-blue-100 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-envelope text-white text-sm"></i>
                                </div>
                                <label class="text-xs font-bold text-blue-600 uppercase tracking-wider">Email
                                    Address</label>
                            </div>
                            <p id="modalUserEmail" class="text-gray-900 font-semibold text-sm mt-1"></p>
                        </div>

                        <div
                            class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl p-4 border border-emerald-100 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-building text-white text-sm"></i>
                                </div>
                                <label
                                    class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Department</label>
                            </div>
                            <p id="modalUserDepartment" class="text-gray-900 font-semibold text-sm mt-1"></p>
                        </div>

                        <div
                            class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl p-4 border border-amber-100 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clock text-white text-sm"></i>
                                </div>
                                <label class="text-xs font-bold text-amber-600 uppercase tracking-wider">Last
                                    Login</label>
                            </div>
                            <p id="modalUserLastLogin" class="text-gray-900 font-semibold text-sm mt-1"></p>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Modal Footer -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-5 rounded-b-3xl border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <i class="fas fa-shield-alt text-green-500"></i>
                            <span>Secure Information</span>
                        </div>
                        <button id="closeUserModalBtn"
                            class="px-6 py-3 bg-gradient-to-r from-violet-600 to-indigo-600 text-white rounded-xl text-sm font-semibold hover:from-violet-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center gap-2">
                            <i class="fas fa-check"></i>
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

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

    <!-- MODALS (Keep your existing modals - just styled to match new design) -->
    <div id="profileModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="profile-modal-title">
      <div class="bg-white rounded-lg shadow-lg w-[480px] max-w-full mx-4" role="document">
        <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
          <h3 id="profile-modal-title" class="font-semibold text-sm text-gray-900 select-none">My Profile</h3>
          <button id="closeProfileBtn" type="button"
            class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200"
            aria-label="Close">
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>
        <div class="px-8 pt-6 pb-8">
          <div class="flex flex-col items-center mb-4">
            <div class="bg-[#28644c] rounded-full w-20 h-20 flex items-center justify-center mb-3">
              <i class="fas fa-user text-white text-3xl"></i>
            </div>
            <p class="font-semibold text-gray-900 text-base leading-5 mb-0.5">{{ $user->name }}</p>
            <p class="text-xs text-gray-500 leading-4">{{ ucfirst($user->role) }}</p>
          </div>
          <form class="space-y-4" action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="nameProfile" class="block text-xs font-semibold text-gray-700 mb-1">Full Name</label>
                <input id="nameProfile" name="name" type="text" value="{{ old('name', $user->name) }}"
                  class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" />
              </div>
              <div>
                <label for="usernameProfile" class="block text-xs font-semibold text-gray-700 mb-1">Username</label>
                <input id="usernameProfile" name="username" type="text" value="{{ old('username', $user->username) }}"
                  class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" />
              </div>
              <div>
                <label for="emailProfile" class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                <input id="emailProfile" name="email" type="email" value="{{ old('email', $user->email) }}"
                  class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" />
              </div>
              <div>
                <label for="phoneProfile" class="block text-xs font-semibold text-gray-700 mb-1">Phone</label>
                <input id="phoneProfile" name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                  placeholder="+63 9xx xxx xxxx"
                  class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" />
              </div>
              <div>
                <label for="department" class="block text-xs font-semibold text-gray-700 mb-1">Department</label>
                <input id="department" type="text" value="Administrative" readonly
                  class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-gray-50" />
              </div>
              <div>
                <label for="location" class="block text-xs font-semibold text-gray-700 mb-1">Location</label>
                <input id="location" type="text" value="Manila, Philippines" readonly
                  class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-gray-50" />
              </div>
              <div>
                <label for="joined" class="block text-xs font-semibold text-gray-700 mb-1">Joined</label>
                <input id="joined" type="text" value="{{ $user->created_at->format('F d, Y') }}" readonly
                  class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-gray-50" />
              </div>
            </div>
            <div class="flex justify-end space-x-3 pt-2">
              <button id="closeProfileBtn2" type="button"
                class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
              <button type="submit"
                class="bg-[#28644c] hover:bg-[#2f855A] text-white text-sm font-semibold rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">Save
                Changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>

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

    <div id="privacySecurityModal" class="modal hidden" aria-modal="true" role="dialog"
      aria-labelledby="privacy-security-modal-title">
      <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
        <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
          <h3 id="privacy-security-modal-title" class="font-semibold text-sm text-gray-900 select-none">Privacy &
            Security</h3>
          <button id="closePrivacySecurityBtn" type="button"
            class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200"
            aria-label="Close">
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>
        <div class="px-8 pt-6 pb-8">
          <form id="changePasswordForm" action="{{ route('account.password.change.request') }}" method="POST"
            class="space-y-3">
            @csrf
            <fieldset>
              <legend class="font-semibold mb-2 select-none">Change Password</legend>
              <label class="block mb-1 font-normal select-none" for="current-password">Current Password</label>
              <input
                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]"
                id="current-password" name="current_password" type="password" />
              <label class="block mt-3 mb-1 font-normal select-none" for="new-password">New Password</label>
              <input
                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]"
                id="new-password" name="new_password" type="password" />
              <label class="block mt-3 mb-1 font-normal select-none" for="confirm-password">Confirm New Password</label>
              <input
                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]"
                id="confirm-password" name="new_password_confirmation" type="password" />
            </fieldset>
            <div id="verifySection" class="hidden">
              <label class="block mt-2 mb-1 font-normal select-none" for="pw-verify-code">Enter Verification
                Code</label>
              <input
                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]"
                id="pw-verify-code" name="code" type="text" maxlength="6" />
              <button id="verifyPasswordBtn" type="button"
                data-verify-action="{{ route('account.password.change.verify') }}"
                class="mt-3 w-full bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200">Verify
                Code & Update</button>
            </div>
            <div class="text-xs" id="pwChangeMsg"></div>
            <div class="flex justify-end space-x-3 pt-2">
              <button
                class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200"
                id="cancelPrivacySecurityBtn" type="button">Cancel</button>
              <button id="submitPasswordBtn"
                class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200"
                type="submit">Send Code</button>
            </div>
          </form>
          <fieldset>
            <legend class="font-semibold mb-1 select-none">Two-Factor Authentication</legend>
            <p class="text-[10px] mb-1 select-none">Enhance your account security</p>
            <div class="flex items-center justify-between">
              <span class="text-[10px] text-[#2f855A] font-semibold select-none">Status: Enabled</span>
              <button
                class="text-[10px] bg-gray-200 text-gray-700 rounded-lg px-3 py-1.5 font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200"
                type="button">Configure</button>
            </div>
          </fieldset>
          <fieldset>
            <legend class="font-semibold mb-1 select-none">Session Management</legend>
            <div class="bg-gray-100 rounded px-3 py-2 text-[10px] text-gray-700 select-none">
              <div class="font-semibold">Current Session</div>
              <div class="text-[9px] text-gray-500">Manila, Philippines • Chrome</div>
              <div
                class="inline-block mt-1 bg-green-100 text-green-700 text-[9px] font-semibold rounded px-2 py-0.5 select-none">
                Active</div>
            </div>
          </fieldset>
          <fieldset>
            <legend class="font-semibold mb-1 select-none">Privacy Settings</legend>
            <label class="flex items-center space-x-2 text-[10px] select-none">
              <input checked class="w-3 h-3" type="checkbox" name="show_profile" />
              <span>Show my profile to all employees</span>
            </label>
            <label class="flex items-center space-x-2 text-[10px] select-none mt-1">
              <input checked class="w-3 h-3" type="checkbox" name="log_activity" />
              <span>Log my account activity</span>
            </label>
          </fieldset>
        </div>
      </div>
    </div>

    <div id="signOutModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="sign-out-modal-title">
      <div class="bg-white rounded-md shadow-lg w-[360px] max-w-full mx-4 text-center" role="document">
        <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
          <h3 id="sign-out-modal-title" class="font-semibold text-sm text-gray-900 select-none">Sign Out</h3>
          <button id="cancelSignOutBtn" type="button"
            class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200"
            aria-label="Close">
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>
        <div class="px-8 pt-6 pb-8">
          <div class="mx-auto mb-4 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
            <i class="fas fa-sign-out-alt text-red-600 text-xl"></i>
          </div>
          <p class="text-xs text-gray-600 mb-6">Are you sure you want to sign out of your account?</p>
          <div class="flex justify-center space-x-4">
            <button id="cancelSignOutBtn2"
              class="bg-gray-200 text-gray-800 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit"
                class="bg-red-600 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-sm transition-all duration-200">Sign
                Out</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Global Loading Scripts -->
    @include('components.loading-scripts')

    @auth
        @include('partials.session-timeout-modal')
    @endauth
</body>

</html>