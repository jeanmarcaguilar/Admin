@php
    // Get the authenticated user
    $user = auth()->user();
    // Get calendar bookings from database (passed from route)
    $calendarBookings = $calendarBookings ?? [];
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
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

        .calendar-day {
            transition: all 0.2s ease;
        }

        .calendar-day:hover {
            background-color: #f0fdf4;
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
            color: #991b1b;
        }

        .calendar-event {
            transition: all 0.2s ease;
        }

        .calendar-event:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* 3D Calendar Styles */
        .calendar-3d-container {
            perspective: 1000px;
        }

        .calendar-day-3d {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
            position: relative;
            top: 0;
            background-color: white;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 0 0 #e5e7eb, 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .calendar-day-3d:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 0 0 #e5e7eb, 0 8px 10px -2px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .calendar-day-3d:active {
            transform: translateY(2px);
            box-shadow: 0 2px 0 0 #e5e7eb, 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }

        .calendar-day-3d.is-today {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            border-color: #047857;
            box-shadow: 0 4px 0 0 #064e3b, 0 4px 6px -1px rgba(5, 150, 105, 0.4);
        }

        .calendar-day-3d.is-today .day-number {
            color: white;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .calendar-day-3d.is-today:hover {
            box-shadow: 0 6px 0 0 #064e3b, 0 8px 10px -2px rgba(5, 150, 105, 0.4);
        }

        /* Empty slot styling */
        .calendar-day-empty {
            background-color: #f9fafb;
            border: 1px dashed #e5e7eb;
            opacity: 0.6;
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
        <img src="{{ asset('golden-arc.png') }}" alt="Logo" class="w-24 h-24 mx-auto rounded-full border-4 border-white/30 shadow-2xl animate-pulse">
      </div>
      
      <!-- Lottie Animation -->
      <div class="mb-8">
        <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.11/dist/dotlottie-wc.js" type="module"></script>
        <dotlottie-wc src="https://lottie.host/5378ba62-7703-4273-a14a-3a999385cf7f/s5Vm9nkLqj.lottie" style="width: 300px;height: 300px" autoplay loop></dotlottie-wc>
      </div>
      
      <!-- Loading Text -->
      <div class="text-center text-white">
        <h2 class="text-2xl font-bold mb-2">Loading Scheduling Calendar</h2>
        <p class="text-white/80 text-sm mb-4">Preparing calendar system and loading scheduled events...</p>
        
        <!-- Loading Dots -->
        <div class="flex justify-center space-x-2">
          <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 0ms"></div>
          <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 150ms"></div>
          <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 300ms"></div>
        </div>
      </div>
      
      <!-- Progress Bar -->
      <div class="w-64 h-1 bg-white/20 rounded-full mt-8 overflow-hidden">
        <div class="h-full bg-white rounded-full animate-pulse" style="width: 60%; animation: progress 2s ease-in-out infinite;"></div>
      </div>
    </div>
  </div>

  <!-- Main Content (initially hidden) -->
  <div id="mainContent" class="opacity-0 transition-opacity duration-500">

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
                    <span class="inline-flex w-9 h-9 rounded-lg bg-emerald-50 items-center justify-center">📁</span>
                    Document Management
                </span>
                <svg id="document-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
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
                <svg id="facilities-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300 rotate-180"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="facilities-submenu" class="submenu mt-1">
                <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                    <a href="{{ route('scheduling.calendar') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    ?
                </button>

            </div>

            <div class="flex items-center gap-3 sm:gap-5">
                <!-- Clock pill -->
                <span id="real-time-clock"
                    class="text-xs font-bold text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                    {{ now()->format('h:i:s A') }}
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
                            <h1 class="text-2xl font-bold text-gray-900">Scheduling & Calendar Integrations</h1>
                            <p class="text-gray-600 mt-1">Manage room bookings, calendar events, and integrations</p>
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
                        </div>
                    </div>
                </div>

                <!-- Calendar and Overview Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Calendar View (2/3 width) -->
                    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-semibold text-lg text-gray-900">Calendar View</h3>
                            <div class="flex items-center gap-2">
                                <button id="todayBtn"
                                    class="px-3 py-1.5 text-sm font-medium bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors">
                                    Today
                                </button>
                                <button id="prevMonthBtn"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-chevron-left text-sm"></i>
                                </button>
                                <span id="monthLabel"
                                    class="text-sm font-semibold text-gray-700 min-w-[120px] text-center">
                                    {{ now()->format('F Y') }}
                                </span>
                                <button id="nextMonthBtn"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-chevron-right text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Calendar Grid -->
                        <div class="grid grid-cols-7 gap-2 mb-3">
                            <div class="text-center text-xs font-medium text-gray-500 py-2">Sun</div>
                            <div class="text-center text-xs font-medium text-gray-500 py-2">Mon</div>
                            <div class="text-center text-xs font-medium text-gray-500 py-2">Tue</div>
                            <div class="text-center text-xs font-medium text-gray-500 py-2">Wed</div>
                            <div class="text-center text-xs font-medium text-gray-500 py-2">Thu</div>
                            <div class="text-center text-xs font-medium text-gray-500 py-2">Fri</div>
                            <div class="text-center text-xs font-medium text-gray-500 py-2">Sat</div>
                        </div>

                        <div id="calendarGrid" class="calendar-3d-container grid grid-cols-7 gap-3 mb-3 p-2"
                            style="min-height: 500px;"></div>

                        <!-- Calendar Legend -->
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <div class="flex items-center gap-4 text-xs text-gray-600">
                                <span class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-green-100 border border-green-300"></span>
                                    <span>Approved</span>
                                </span>
                                <span class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-yellow-100 border border-yellow-300"></span>
                                    <span>Pending</span>
                                </span>
                                <span class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-red-100 border border-red-300"></span>
                                    <span>Rejected</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Panels (1/3 width) -->
                    <div class="space-y-6">
                        <!-- Quick Actions -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="font-semibold text-lg text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <button id="exportCalendarBtn"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                                    <i class="fas fa-download"></i> Export Calendar
                                </button>
                                @if (Route::has('calendar.clear'))
                                    <form method="POST" action="{{ route('calendar.clear') }}" class="w-full">
                                        @csrf
                                        <button type="submit"
                                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors text-sm font-medium">
                                            <i class="fas fa-trash"></i> Clear All
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Upcoming Events -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-lg text-gray-900">Upcoming Events</h3>
                                <span id="upcomingCount"
                                    class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                    {{ count($calendarBookings) }}
                                </span>
                            </div>

                            <div id="upcomingEventsList" class="max-h-80 overflow-y-auto custom-scrollbar">
                                @if (!empty($calendarBookings))
                                    <div class="space-y-3">
                                        @foreach ($calendarBookings as $booking)
                                            @php
                                                $date = isset($booking['date']) ? \Carbon\Carbon::parse($booking['date']) : null;
                                                $day = $date ? $date->format('d') : '--';
                                                $monthShort = $date ? $date->format('M') : '';
                                                $time = isset($booking['start_time']) && $booking['start_time']
                                                    ? (\Carbon\Carbon::parse($booking['start_time'])->format('g:i A'))
                                                    : '';
                                                $title = $booking['name'] ?? ($booking['title'] ?? 'Booking');
                                                $status = strtolower($booking['status'] ?? 'pending');
                                                $statusMap = array(
                                                    'pending' => 'status-pending',
                                                    'approved' => 'status-approved',
                                                    'rejected' => 'status-rejected',
                                                );
                                                $statusClass = $statusMap[$status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <div class="calendar-event p-3 rounded-lg border border-gray-100 hover:border-green-200 cursor-pointer"
                                                onclick="showEventDetails({{ json_encode($booking) }})">
                                                <div class="flex items-start space-x-3">
                                                    <div
                                                        class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-lg bg-green-50 text-green-600 font-bold">
                                                        {{ $day }}
                                                    </div>
                                                    <div class="flex-grow">
                                                        <div class="flex items-center justify-between">
                                                            <span
                                                                class="font-medium text-gray-900 text-sm">{{ Str::limit($title, 20) }}</span>
                                                            <span
                                                                class="px-2 py-0.5 text-xs font-medium rounded-full {{ $statusClass }}">
                                                                {{ ucfirst($status) }}
                                                            </span>
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-1">{{ $day }} {{ $monthShort }}
                                                            @if($time) · {{ $time }} @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-6">
                                        <i class='bx bx-calendar text-3xl text-gray-300 mb-2'></i>
                                        <p class="text-sm text-gray-500">No upcoming events</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Event Details Modal -->
    <div id="eventDetailsModal" class="modal hidden" aria-modal="true" role="dialog">
        <div class="bg-white rounded-lg shadow-lg w-[400px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Event Details</h3>
                <button onclick="closeEventDetails()"
                    class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="eventDetailsContent" class="p-6">
                <!-- Content will be loaded by JavaScript -->
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

            console.log('Setting up dropdowns:', dropdowns);

            Object.entries(dropdowns).forEach(([btnId, submenuId]) => {
                const btn = document.getElementById(btnId);
                const submenu = document.getElementById(submenuId);
                // Fix arrow ID to match actual HTML (visitor-arrow instead of visitor-management-arrow)
                const arrowId = btnId.replace('-management-btn', '-arrow');
                const arrow = document.getElementById(arrowId);

                console.log(`Dropdown setup for ${btnId}:`, {
                    btn: !!btn,
                    submenu: !!submenu,
                    arrow: !!arrow,
                    arrowId: arrowId
                });

                if (btn && submenu) {
                    btn.addEventListener("click", (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log(`Clicked dropdown: ${btnId}`);

                        const isHidden = submenu.classList.contains("hidden");
                        console.log(`Dropdown ${btnId} is hidden: ${isHidden}`);

                        // Close all other dropdowns
                        Object.values(dropdowns).forEach(id => {
                            const otherSubmenu = document.getElementById(id);
                            const otherArrowId = id.replace('-submenu', '-arrow');
                            const otherArrow = document.getElementById(otherArrowId);
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
                            console.log(`Opened dropdown: ${btnId}`);
                        } else {
                            submenu.classList.add("hidden");
                            if (arrow) arrow.classList.remove("rotate-180");
                            console.log(`Closed dropdown: ${btnId}`);
                        }
                    });
                } else {
                    console.warn(`Missing elements for dropdown: ${btnId}`, { btn: !!btn, submenu: !!submenu });
                }
            });

            // User menu dropdown
            const userMenuButton = document.getElementById("user-menu-button");
            const userMenuDropdown = document.getElementById("user-menu-dropdown");

            if (userMenuButton && userMenuDropdown) {
                userMenuButton.addEventListener("click", (e) => {
                    e.stopPropagation();
                    userMenuDropdown.classList.toggle("hidden");
                });

                // Close dropdown when clicking outside
                document.addEventListener("click", (e) => {
                    if (!userMenuButton.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                        userMenuDropdown.classList.add("hidden");
                    }
                });
            }

            // Real-time clock with accurate time
            function updateClock() {
                const now = new Date();
                let hours = now.getHours();
                const minutes = now.getMinutes().toString().padStart(2, '0');
                const seconds = now.getSeconds().toString().padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12; // the hour '0' should be '12'
                const hoursStr = hours.toString().padStart(2, '0');
                const timeString = `${hoursStr}:${minutes}:${seconds} ${ampm}`;

                const clockElement = document.getElementById('real-time-clock');
                if (clockElement) {
                    clockElement.textContent = timeString;
                }
            }
            // Update immediately and then every second
            updateClock();
            setInterval(updateClock, 1000);

            // Open "Facilities Management" dropdown by default since we're on Scheduling & Calendar page
            const facilitiesBtn = document.getElementById('facilities-management-btn');
            const facilitiesSubmenu = document.getElementById('facilities-submenu');
            const facilitiesArrow = document.getElementById('facilities-arrow');

            if (facilitiesBtn && facilitiesSubmenu) {
                // Force open the facilities dropdown
                facilitiesSubmenu.classList.remove('hidden');
                if (facilitiesArrow) {
                    facilitiesArrow.classList.add('rotate-180');
                }
            }

            // Calendar functionality
            const calendarGrid = document.getElementById('calendarGrid');
            const monthLabel = document.getElementById('monthLabel');
            const prevMonthBtn = document.getElementById('prevMonthBtn');
            const nextMonthBtn = document.getElementById('nextMonthBtn');
            const todayBtn = document.getElementById('todayBtn');
            const exportCalendarBtn = document.getElementById('exportCalendarBtn');
            const lockAllBtn = document.getElementById('lockAllBtn');

            console.log('Calendar elements found:', {
                calendarGrid: !!calendarGrid,
                monthLabel: !!monthLabel,
                prevMonthBtn: !!prevMonthBtn,
                nextMonthBtn: !!nextMonthBtn,
                todayBtn: !!todayBtn
            });

            let currentDate = new Date();
            const rawBookings = @json($calendarBookings);
            // Ensure we have an array, even if PHP sending associative array
            const sessionBookings = Array.isArray(rawBookings) ? rawBookings : Object.values(rawBookings || {});

            console.log('Bookings loaded:', sessionBookings.length);

            function daysInMonth(year, monthIndex) {
                return new Date(year, monthIndex + 1, 0).getDate();
            }

            function renderCalendar() {
                console.log('Rendering calendar...');
                const year = currentDate.getFullYear();
                const monthIndex = currentDate.getMonth();
                const today = new Date();

                // Update month label
                const monthName = currentDate.toLocaleString('default', { month: 'long' });
                monthLabel.textContent = `${monthName} ${year}`;

                // Prepare grid
                calendarGrid.innerHTML = '';
                console.log('Calendar grid cleared');

                const firstDayOfMonth = new Date(year, monthIndex, 1);
                const startWeekday = firstDayOfMonth.getDay();
                const totalDays = daysInMonth(year, monthIndex);
                const totalCells = 42;

                // Leading empty cells
                for (let i = 0; i < startWeekday; i++) {
                    const cell = document.createElement('div');
                    cell.className = 'h-28 rounded-xl p-2 calendar-day-empty';
                    calendarGrid.appendChild(cell);
                }

                // Day cells
                console.log(`Creating ${totalDays} day cells`);
                for (let day = 1; day <= totalDays; day++) {
                    const cell = document.createElement('div');
                    const isToday = today.getFullYear() === year && today.getMonth() === monthIndex && today.getDate() === day;

                    cell.className = `h-28 rounded-xl p-2 calendar-day-3d flex flex-col justify-between cursor-pointer ${isToday ? 'is-today' : ''
                        }`;

                    // Header: Day number + Add button (visible on hover)
                    const header = document.createElement('div');
                    header.className = 'flex justify-between items-start';

                    const badge = document.createElement('span');
                    badge.className = `day-number text-lg font-bold ${isToday ? 'text-white' : 'text-gray-700'
                        }`;
                    badge.textContent = day;

                    header.appendChild(badge);
                    cell.appendChild(header);

                    // Render bookings for this day
                    const events = (sessionBookings || []).filter(b => {
                        if (!b.date) return false;

                        // Robust date comparing: "YYYY-MM-DD"
                        // Split string to avoid timezone offset issues (e.g. UTC -> Local)
                        // If date is "2026-02-01", split gives ["2026", "02", "01"]
                        let y, m, d;

                        if (b.date.includes('T')) {
                            const datePart = b.date.split('T')[0];
                            [y, m, d] = datePart.split('-').map(Number);
                        } else if (b.date.includes(' ')) {
                            const datePart = b.date.split(' ')[0];
                            [y, m, d] = datePart.split('-').map(Number);
                        } else {
                            [y, m, d] = b.date.split('-').map(Number);
                        }

                        // Compare with current cell: year, monthIndex (0-11), day
                        return y === year && (m - 1) === monthIndex && d === day;
                    });

                    const eventsContainer = document.createElement('div');
                    eventsContainer.className = 'space-y-1 overflow-y-auto custom-scrollbar mt-1 max-h-[4.5rem]';

                    if (events.length) {
                        events.forEach(ev => {
                            const pill = document.createElement('div');
                            const status = (ev.status || '').toLowerCase();
                            let colorClass = '';

                            // 3D effect for events too
                            if (status === 'approved') colorClass = 'bg-green-100 text-green-800 border-l-2 border-green-500';
                            else if (status === 'rejected') colorClass = 'bg-red-100 text-red-800 border-l-2 border-red-500';
                            else if (status === 'pending') colorClass = 'bg-yellow-100 text-yellow-800 border-l-2 border-yellow-500';
                            else colorClass = 'bg-blue-100 text-blue-800 border-l-2 border-blue-500';

                            const start = ev.start_time ? ev.start_time.substring(0, 5) : '';

                            // Determine the best display text
                            // explicit name > title > purpose > room > 'Booking'
                            let displayText = ev.name || ev.title || ev.purpose || ev.room || 'Booking';

                            // If just "Booking" (generic) and we have a room, append it
                            if (displayText === 'Booking' && ev.room) {
                                displayText += ` (${ev.room})`;
                            }

                            pill.className = `text-[11px] font-semibold px-1.5 py-1 rounded shadow-sm mb-1 truncate ${colorClass} hover:opacity-80 transition-opacity`;
                            pill.title = `${displayText}` + (status ? ` [${status}]` : '') + (ev.room ? ` @ ${ev.room}` : '');
                            pill.textContent = `${start ? start + ' ' : ''}${displayText}`;

                            pill.onclick = (e) => {
                                e.stopPropagation();
                                showEventDetails(ev);
                            };
                            eventsContainer.appendChild(pill);
                        });
                    }

                    cell.appendChild(eventsContainer);
                    calendarGrid.appendChild(cell);
                }

                // Trailing empty cells
                const usedCells = startWeekday + totalDays;
                // Fill up to the end of the last row, or extra row to maintain grid shape
                const remaining = 7 - (usedCells % 7);
                if (remaining < 7) {
                    for (let i = 0; i < remaining; i++) {
                        const cell = document.createElement('div');
                        cell.className = 'h-28 rounded-xl p-2 calendar-day-empty';
                        calendarGrid.appendChild(cell);
                    }
                }
            }

            // Navigation buttons
            prevMonthBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar();
            });

            nextMonthBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar();
            });

            todayBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                currentDate = new Date();
                renderCalendar();
            });

            // Export functionality
            exportCalendarBtn?.addEventListener('click', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Export Started',
                    text: 'Calendar export has been queued. You will receive an email when ready.',
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            // Print functionality
            const printBtn = document.getElementById('printBtn');
            if (printBtn) {
                printBtn.addEventListener('click', function () {
                    window.print();
                });
            }

            // Initial render
            console.log('About to render calendar...');
            renderCalendar();
            console.log('Calendar render called');

            // Event details modal functions
            window.showEventDetails = function (event) {
                const modal = document.getElementById('eventDetailsModal');
                const content = document.getElementById('eventDetailsContent');

                const status = (event.status || '').toLowerCase();
                const statusClass = {
                    'pending': 'status-pending',
                    'approved': 'status-approved',
                    'rejected': 'status-rejected',
                }[status] || 'bg-gray-100 text-gray-800';

                const date = event.date ? new Date(event.date).toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }) : 'No date specified';

                const time = event.start_time && event.end_time ?
                    `${event.start_time} - ${event.end_time}` :
                    event.start_time || 'All day';

                content.innerHTML = `
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Title</h4>
                            <p class="text-base text-gray-900 font-medium">${event.name || event.title || 'Booking'}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Status</h4>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">
                                ${status.charAt(0).toUpperCase() + status.slice(1)}
                            </span>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Date</h4>
                            <p class="text-base text-gray-900">${date}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Time</h4>
                            <p class="text-base text-gray-900">${time}</p>
                        </div>
                        ${event.room ? `
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Room</h4>
                            <p class="text-base text-gray-900">${event.room}</p>
                        </div>
                        ` : ''}
                        ${event.equipment ? `
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Equipment</h4>
                            <p class="text-base text-gray-900">${event.equipment}</p>
                        </div>
                        ` : ''}
                        ${event.purpose ? `
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Purpose</h4>
                            <p class="text-base text-gray-900">${event.purpose}</p>
                        </div>
                        ` : ''}
                    </div>
                `;

                modal.classList.remove('hidden');
                modal.classList.add('active');
                modal.style.display = 'flex';
            };

            window.closeEventDetails = function () {
                const modal = document.getElementById('eventDetailsModal');
                modal.classList.add('hidden');
                modal.classList.remove('active');
                modal.style.display = 'none';
            };

            // Check and restore lock state on page load
            function restoreLockState() {
                const isLocked = localStorage.getItem('reservationsLocked') === 'true';
                const lockBtn = document.getElementById('lockAllBtn');
                if (lockBtn) {
                    if (isLocked) {
                        lockBtn.innerHTML = '<i class="fas fa-lock-open"></i> Unlock Reservations';
                        lockBtn.classList.remove('bg-amber-600', 'hover:bg-amber-700');
                        lockBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                        lockBtn.setAttribute('onclick', 'unlockAllReservations()');
                    }
                }
            }

            // Restore on load
            restoreLockState();

            // Flash success from server
            const flashSuccess = @json(session('success'));
            if (flashSuccess) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: flashSuccess,
                    confirmButtonColor: '#059669'
                });
            }


            // Modal handlers
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
            // Close modals when clicking outside
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.addEventListener('click', function (e) {
                    if (e.target === this) {
                        this.classList.add('hidden');
                        this.classList.remove('active');
                        this.style.display = 'none';
                    }
                });
            });

            // Close modals when clicking outside (for new modals)
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
    window.addEventListener('load', function() {
      setTimeout(function() {
        hideLoadingScreen();
      }, 2000); // 2 second delay for better UX
    });
    
    // Fallback in case window.load doesn't fire properly
    document.addEventListener('DOMContentLoaded', function() {
      // Additional fallback after 5 seconds
      setTimeout(function() {
        const loadingScreen = document.getElementById('loadingScreen');
        const mainContent = document.getElementById('mainContent');
        
        if (loadingScreen && !loadingScreen.classList.contains('hidden')) {
          hideLoadingScreen();
        }
      }, 5000);
    });

    // Initialize loading screen on page load
    document.addEventListener('DOMContentLoaded', function() {
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
    <div class="bg-white rounded-lg shadow-lg w-[500px] max-w-full mx-4 fade-in" role="document">
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">My Profile</h3>
        <button type="button" class="text-gray-400 hover:text-gray-600" id="closeProfileBtn">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="p-6">
        <div class="text-center mb-6">
          <div class="w-20 h-20 rounded-full bg-[#28644c] text-white mx-auto flex items-center justify-center mb-4">
            <i class="fas fa-user-circle text-4xl"></i>
          </div>
          <h4 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h4>
          <p class="text-sm text-gray-500">{{ ucfirst($user->role) }}</p>
        </div>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <p class="text-sm text-gray-900">{{ $user->email }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <p class="text-sm text-gray-900">{{ ucfirst($user->role) }}</p>
          </div>
        </div>
      </div>
      <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" id="closeProfileBtn2">Close</button>
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






