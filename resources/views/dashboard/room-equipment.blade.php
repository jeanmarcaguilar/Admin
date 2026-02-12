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
            transform-origin: top right;
        }

        .booking-card {
            transition: all 0.2s ease-in-out;
            border: 1px solid #e5e7eb;
        }

        .booking-card:hover {
            border-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .booking-row {
            transition: all 0.2s ease;
        }

        .booking-row:hover {
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
                <h2 class="text-2xl font-bold mb-2">Loading Facilities Hub</h2>
                <p class="text-white/80 text-sm mb-4">Preparing facilities management system and loading resources...
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
                    <svg id="facilities-arrow"
                        class="w-4 h-4 text-emerald-400 transition-transform duration-300 rotate-180" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="facilities-submenu" class="submenu mt-1">
                    <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                        <a href="{{ route('room-equipment') }}"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            <!-- MAIN CONTENT -->
            <main class="p-4 sm:p-6">
                <div class="max-w-7xl mx-auto">
                    <!-- Page Header -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Facilities Management Hub</h1>
                                <p class="text-gray-600 mt-1">Manage rooms, equipment, scheduling, and approvals</p>
                            </div>
                            <div class="mt-4 md:mt-0 flex space-x-3">
                                <button onclick="window.location.href='{{ route('scheduling.calendar') }}'"
                                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                    <i class='bx bx-calendar mr-2'></i> View Calendar
                                </button>
                                <button onclick="window.location.href='{{ route('approval.workflow') }}'"
                                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                    <i class='bx bx-check-circle mr-2'></i> Approvals
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Facilities Management Hub -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                        <!-- Room & Equipment Booking Card -->
                        <a href="{{ route('room-equipment') }}"
                            class="group relative bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:scale-105">
                            <div
                                class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 rounded-full bg-white/20 blur-2xl group-hover:opacity-100 transition-opacity">
                            </div>
                            <div class="relative text-white">
                                <div
                                    class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                    <i class='bx bx-calendar-plus text-3xl'></i>
                                </div>
                                <h3 class="text-xl font-bold mb-2">Room & Equipment</h3>
                                <p class="text-emerald-100 text-sm mb-4">Book rooms and equipment for meetings</p>
                                <div class="flex items-center text-emerald-100 text-sm font-medium">
                                    <span>Go to Booking</span>
                                    <i
                                        class='bx bx-right-arrow-alt ml-2 group-hover:translate-x-1 transition-transform'></i>
                                </div>
                            </div>
                        </a>

                        <!-- Scheduling Calendar Card -->
                        <a href="{{ route('scheduling.calendar') }}"
                            class="group relative bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:scale-105">
                            <div
                                class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 rounded-full bg-white/20 blur-2xl group-hover:opacity-100 transition-opacity">
                            </div>
                            <div class="relative text-white">
                                <div
                                    class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                    <i class='bx bx-calendar text-3xl'></i>
                                </div>
                                <h3 class="text-xl font-bold mb-2">Scheduling</h3>
                                <p class="text-blue-100 text-sm mb-4">Calendar & event management</p>
                                <div class="flex items-center text-blue-100 text-sm font-medium">
                                    <span>View Calendar</span>
                                    <i
                                        class='bx bx-right-arrow-alt ml-2 group-hover:translate-x-1 transition-transform'></i>
                                </div>
                            </div>
                        </a>

                        <!-- Approval Workflow Card -->
                        <a href="{{ route('approval.workflow') }}"
                            class="group relative bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:scale-105">
                            <div
                                class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 rounded-full bg-white/20 blur-2xl group-hover:opacity-100 transition-opacity">
                            </div>
                            <div class="relative text-white">
                                <div
                                    class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                    <i class='bx bx-check-circle text-3xl'></i>
                                </div>
                                <h3 class="text-xl font-bold mb-2">Approvals</h3>
                                <p class="text-amber-100 text-sm mb-4">Review and approve requests</p>
                                <div class="flex items-center text-amber-100 text-sm font-medium">
                                    <span>Manage Approvals</span>
                                    <i
                                        class='bx bx-right-arrow-alt ml-2 group-hover:translate-x-1 transition-transform'></i>
                                </div>
                            </div>
                        </a>

                        <!-- Reservation History Card -->
                        <a href="{{ route('reservation.history') }}"
                            class="group relative bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:scale-105">
                            <div
                                class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 rounded-full bg-white/20 blur-2xl group-hover:opacity-100 transition-opacity">
                            </div>
                            <div class="relative text-white">
                                <div
                                    class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                    <i class='bx bx-history text-3xl'></i>
                                </div>
                                <h3 class="text-xl font-bold mb-2">History</h3>
                                <p class="text-purple-100 text-sm mb-4">View reservation records</p>
                                <div class="flex items-center text-purple-100 text-sm font-medium">
                                    <span>View History</span>
                                    <i
                                        class='bx bx-right-arrow-alt ml-2 group-hover:translate-x-1 transition-transform'></i>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                        <h3 class="font-semibold text-lg text-gray-900 mb-6">Quick Actions</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <a href="{{ route('room-equipment') }}"
                                class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl hover:bg-emerald-100 transition-colors">
                                <div class="w-10 h-10 rounded-lg bg-emerald-500 flex items-center justify-center">
                                    <i class='bx bx-plus text-white text-lg'></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">New Booking</p>
                                    <p class="text-sm text-gray-600">Create reservation</p>
                                </div>
                            </a>

                            <a href="{{ route('scheduling.calendar') }}"
                                class="flex items-center gap-3 p-4 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 transition-colors">
                                <div class="w-10 h-10 rounded-lg bg-blue-500 flex items-center justify-center">
                                    <i class='bx bx-calendar text-white text-lg'></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">View Calendar</p>
                                    <p class="text-sm text-gray-600">Check schedule</p>
                                </div>
                            </a>

                            <a href="{{ route('approval.workflow') }}"
                                class="flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 transition-colors">
                                <div class="w-10 h-10 rounded-lg bg-amber-500 flex items-center justify-center">
                                    <i class='bx bx-check-circle text-white text-lg'></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Pending</p>
                                    <p class="text-sm text-gray-600">Review requests</p>
                                </div>
                            </a>

                            <a href="{{ route('reservation.history') }}"
                                class="flex items-center gap-3 p-4 bg-purple-50 border border-purple-200 rounded-xl hover:bg-purple-100 transition-colors">
                                <div class="w-10 h-10 rounded-lg bg-purple-500 flex items-center justify-center">
                                    <i class='bx bx-history text-white text-lg'></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">History</p>
                                    <p class="text-sm text-gray-600">View records</p>
                                </div>
                            </a>
                        </div>
                    </div>

                </div>
            </main>
        </div>

        <!-- Modals -->
        <!-- View Booking Details Modal -->
        <div id="viewBookingModal" class="modal hidden" aria-modal="true" role="dialog">
            <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center border-b px-6 py-4">
                    <h3 class="text-xl font-semibold text-gray-900">Booking Details</h3>
                    <button onclick="closeModal('viewBookingModal')" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                <div class="p-6" id="bookingDetailsContent">
                    <!-- Content will be loaded by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Cancel Booking Confirmation Modal -->
        <div id="cancelBookingModal" class="modal hidden" aria-modal="true" role="dialog">
            <div class="bg-white rounded-lg w-full max-w-md">
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Cancel Booking</h3>
                    <p class="text-sm text-gray-500 mb-6">Are you sure you want to cancel this booking? This action
                        cannot
                        be undone.</p>
                    <div class="flex justify-center space-x-4">
                        <button type="button" onclick="closeModal('cancelBookingModal')"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            No, Keep It
                        </button>
                        <button type="button" id="confirmCancelBtn"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Yes, Cancel Booking
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Message Toast -->
        @if(session('success'))
            <div id="successToast"
                class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center justify-between z-50"
                style="min-width: 300px;">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="document.getElementById('successToast').remove()"
                    class="ml-4 text-white hover:text-gray-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

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

                // Open "Facilities Management" dropdown by default since we're on Room & Equipment Booking page
                const facilitiesBtn = document.getElementById('facilities-management-btn');
                const facilitiesSubmenu = document.getElementById('facilities-submenu');
                const facilitiesArrow = document.getElementById('facilities-arrow');

                if (facilitiesSubmenu && !facilitiesSubmenu.classList.contains('hidden')) {
                    facilitiesSubmenu.classList.remove('hidden');
                    if (facilitiesArrow) facilitiesArrow.classList.add('rotate-180');
                }

                // History button
                const historyBtn = document.getElementById('historyBtn');
                if (historyBtn) {
                    historyBtn.addEventListener('click', function () {
                        // Redirect to reservation history
                        window.location.href = '{{ route("reservation.history") }}';
                    });
                }

                // Form submission handler
                const bookingForm = document.getElementById('combinedBookingForm');
                if (bookingForm) {
                    bookingForm.addEventListener('submit', async function (e) {
                        e.preventDefault();

                        // Client-side validation
                        const bookingType = this.querySelector('select[name="booking_type"]').value;
                        const name = this.querySelector('input[name="name"]').value.trim();
                        const roomId = this.querySelector('select[name="room_id"]').value;
                        const equipmentId = this.querySelector('select[name="equipment_id"]').value;
                        const date = this.querySelector('input[name="date"]').value;
                        const startTime = this.querySelector('input[name="start_time"]').value;
                        const endTime = this.querySelector('input[name="end_time"]').value;
                        const purpose = this.querySelector('textarea[name="purpose"]').value.trim();

                        // Validation checks
                        const errors = [];

                        if (!bookingType) {
                            errors.push('Booking type is required');
                        }

                        if (!name) {
                            errors.push('Name is required');
                        }

                        if (!roomId) {
                            errors.push('Room selection is required');
                        }

                        if (!date) {
                            errors.push('Date is required');
                        }

                        if (!startTime) {
                            errors.push('Start time is required');
                        }

                        if (!endTime) {
                            errors.push('End time is required');
                        }

                        if (startTime && endTime) {
                            // Convert times to Date objects for proper comparison
                            const today = new Date().toDateString();
                            const startDateTime = new Date(today + ' ' + startTime);
                            const endDateTime = new Date(today + ' ' + endTime);

                            if (startDateTime >= endDateTime) {
                                errors.push('End time must be after start time');
                            }
                        }

                        if (!purpose) {
                            errors.push('Purpose is required');
                        }

                        // Show validation errors if any
                        if (errors.length > 0) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                html: `<div class="text-left">${errors.map(error => `<div class="mb-1">• ${error}</div>`).join('')}</div>`,
                                confirmButtonColor: '#059669'
                            });
                            return;
                        }

                        // Disable submit button to prevent double submission
                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalBtnText = submitBtn.innerHTML;
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';

                        try {
                            // Get form data
                            const formData = new FormData(this);
                            const formObject = {};

                            // Process equipment data (simplified - single equipment)
                            const equipment = formData.get('equipment[]');
                            const equipmentData = [];

                            if (equipment && equipment !== 'No equipment needed') {
                                equipmentData.push({
                                    name: equipment,
                                    quantity: 1 // Default quantity for simplified form
                                });
                            }

                            // Build the form object with proper structure
                            formObject.booking_type = formData.get('booking_type') || null;
                            formObject.name = formData.get('name') || null;
                            formObject.room_id = formData.get('room_id') || null;
                            formObject.equipment_id = formData.get('equipment_id') || null;
                            formObject.purpose = formData.get('purpose') || 'Not specified';
                            formObject.date = formData.get('date') || new Date().toISOString().split('T')[0];
                            formObject.start_time = formData.get('start_time') || null;
                            formObject.end_time = formData.get('end_time') || null;
                            formObject.attendees = 1; // Default attendees
                            formObject.notes = ''; // Default notes
                            formObject.status = 'pending'; // Default status

                            // Generate a temporary ID for the new booking
                            formObject.id = 'temp_' + Date.now();

                            // Get CSRF token
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                                document.querySelector('input[name="_token"]')?.value;

                            if (!csrfToken) {
                                throw new Error('CSRF token not found');
                            }

                            // Send request to server
                            const response = await fetch(this.action, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify(formObject)
                            });

                            // Check if response is JSON
                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                // If not JSON, get the HTML response to see what's wrong
                                const htmlText = await response.text();
                                console.error('Server returned HTML instead of JSON:', htmlText);
                                throw new Error('Server returned HTML instead of JSON. Check console for details.');
                            }

                            const responseData = await response.json();

                            if (response.ok) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Booking Created',
                                    text: 'Your booking has been submitted successfully!',
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Reset form
                                this.reset();

                                // Redirect to calendar page to see the new booking
                                setTimeout(() => {
                                    window.location.href = '{{ route("scheduling.calendar") }}';
                                }, 1500);
                            } else {
                                // Handle validation errors
                                if (responseData.errors) {
                                    const errorMessages = Object.values(responseData.errors)
                                        .flat()
                                        .map(error => `<li class="text-sm text-red-600">${error}</li>`)
                                        .join('');
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Validation Error',
                                        html: `<ul class="list-disc pl-4 text-left">${errorMessages}</ul>`,
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: responseData.message || 'Failed to create booking',
                                    });
                                }
                            }
                        } catch (error) {
                            console.error('Booking error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error: ' + (error.message || 'An unexpected error occurred'),
                            });
                        } finally {
                            // Re-enable submit button
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalBtnText;
                            }
                        }
                    });
                }

                // Auto-hide success toast after 5 seconds
                const successToast = document.getElementById('successToast');
                if (successToast) {
                    setTimeout(() => {
                        successToast.remove();
                    }, 5000);
                }

                // Conflict checking (from original code)
                const form = document.getElementById('combinedBookingForm');
                if (!form) return;

                const roomSelect = document.getElementById('roomSelect');
                const dateInput = document.getElementById('bookingDate');
                const startInput = document.getElementById('startTime');
                const endInput = document.getElementById('endTime');

                const existing = @json(session('calendar_bookings', []));

                function toMinutes(t) {
                    if (!t) return null;
                    const [h, m] = t.split(':').map(Number);
                    if (Number.isNaN(h) || Number.isNaN(m)) return null;
                    return h * 60 + m;
                }

                function normalizeRoomKey(v) {
                    return (v || '').toString().toLowerCase();
                }

                function bookingMatchesRoom(b, key) {
                    const type = (b.type || '').toString().toLowerCase();
                    const name = normalizeRoomKey(b.name || b.title || '');
                    return (type === 'room') && (name.includes(key) || key.includes(name));
                }

                function overlaps(aStart, aEnd, bStart, bEnd) {
                    return aStart < bEnd && aEnd > bStart; // strict overlap
                }

                form.addEventListener('submit', (e) => {
                    const roomKey = normalizeRoomKey(roomSelect?.value);
                    const dateVal = dateInput?.value;
                    const startVal = startInput?.value;
                    const endVal = endInput?.value;

                    if (!roomKey || !dateVal || !startVal || !endVal) return; // let HTML5 required handle

                    const startMins = toMinutes(startVal);
                    const endMins = toMinutes(endVal);
                    if (startMins === null || endMins === null || endMins <= startMins) return; // basic validation handled elsewhere

                    const conflicts = (existing || []).filter(b => {
                        try {
                            const bDate = (b.date || '').slice(0, 10);
                            const bStatus = (b.status || '').toString().toLowerCase();
                            // Consider pending/approved as occupying
                            const occupying = ['approved', 'pending', 'occupied'].includes(bStatus) || bStatus === '';
                            if (!occupying) return false;
                            if (bDate !== dateVal) return false;
                            if (!bookingMatchesRoom(b, roomKey)) return false;
                            const bs = toMinutes((b.start_time || '').slice(0, 5));
                            const be = toMinutes((b.end_time || '').slice(0, 5));
                            if (bs === null || be === null) return false;
                            return overlaps(startMins, endMins, bs, be);
                        } catch (_) { return false; }
                    });

                    if (conflicts.length > 0) {
                        e.preventDefault();
                        const first = conflicts[0];
                        const range = `${(first.start_time || '').slice(0, 5)} - ${(first.end_time || '').slice(0, 5)}`;
                        Swal.fire({
                            icon: 'error',
                            title: 'Room is occupied',
                            html: `<div class="text-left">The selected room is already booked on <b>${dateVal}</b> between <b>${range}</b>.<br/><div class="mt-2">Please choose a different time slot or room.</div></div>`,
                        });
                    }
                });
            });

            // Modal functions from original code
            window.closeModal = function (modalId) {
                document.getElementById(modalId).classList.add('hidden');
            };

            window.showBookingDetails = function (booking) {
                const modal = document.getElementById('viewBookingModal');
                const content = document.getElementById('bookingDetailsContent');
                let details = `
                <div class="space-y-4">
                    <div>
                        <h4 class="font-semibold text-gray-700">Booking ID</h4>
                        <p class="text-sm text-gray-600">#${booking.id}</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-700">Type</h4>
                        <p class="text-sm text-gray-600">${booking?.type ? booking.type.charAt(0).toUpperCase() + booking.type.slice(1) : 'N/A'}</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-700">${booking.type === 'room' ? 'Room' : 'Equipment'}</h4>
                        <p class="text-sm text-gray-600">${booking.name || (booking.type === 'room' ? booking.room : booking.equipment)}</p>
                    </div>
            `;

                if (booking.quantity && booking.type === 'equipment') {
                    details += `
                    <div>
                        <h4 class="font-semibold text-gray-700">Quantity</h4>
                        <p class="text-sm text-gray-600">${booking.quantity}</p>
                    </div>
                `;
                }

                details += `
                    <div>
                        <h4 class="font-semibold text-gray-700">Date</h4>
                        <p class="text-sm text-gray-600">${new Date(booking.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
                    </div>
            `;

                if (booking.return_date && booking.return_date !== booking.date) {
                    details += `
                    <div>
                        <h4 class="font-semibold text-gray-700">Return Date</h4>
                        <p class="text-sm text-gray-600">${new Date(booking.return_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
                    </div>
                `;
                }

                if (booking.start_time && booking.end_time) {
                    details += `
                    <div>
                        <h4 class="font-semibold text-gray-700">Time</h4>
                        <p class="text-sm text-gray-600">${new Date('2000-01-01T' + booking.start_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })} - ${new Date('2000-01-01T' + booking.end_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</p>
                    </div>
                `;
                }

                details += `
                    <div>
                        <h4 class="font-semibold text-gray-700">Status</h4>
                        <p class="text-sm text-gray-600">${booking?.status ? booking.status.charAt(0).toUpperCase() + booking.status.slice(1) : 'N/A'}</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-700">Purpose</h4>
                        <p class="text-sm text-gray-600">${booking.purpose || 'Not specified'}</p>
                    </div>
                </div>
            `;

                content.innerHTML = details;
                modal.classList.remove('hidden');
            };
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