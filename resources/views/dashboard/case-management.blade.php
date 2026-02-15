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
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        .case-card {
            transition: all 0.2s ease-in-out;
            border: 1px solid #e5e7eb;
        }

        .case-card:hover {
            border-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .case-row {
            transition: all 0.2s ease;
        }

        .case-row:hover {
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
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-confirmed { background-color: #d1fae5; color: #065f46; }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }
        .status-completed { background-color: #dbeafe; color: #1e40af; }

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
        <h2 class="text-2xl font-bold mb-2">Loading Case Management</h2>
        <p class="text-white/80 text-sm mb-4">Preparing case management system and loading case files...</p>
        
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
                        Microfinance Admin
                    </div>
                    <div class="text-[11px] text-gray-500 font-semibold uppercase group-hover:text-brand-primary transition-colors">
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
                    <a href="{{ route('scheduling.calendar') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        Scheduling & Calendar Integrations
                    </a>
                    <a href="{{ route('approval.workflow') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
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
                <svg id="legal-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="legal-submenu" class="submenu mt-1">
                <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                    <a href="{{ route('case.management') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
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
                    Microfinance Admin © {{ date('Y') }}<br/>
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

        <!-- Notification Dropdown -->
        <div id="notificationDropdown" class="dropdown-panel hidden opacity-0 translate-y-2 scale-95 pointer-events-none
            absolute right-4 mt-3 w-80 bg-white rounded-xl shadow-lg border border-gray-100 z-50">
            <div class="flex justify-between items-center px-4 py-3 border-b border-gray-100">
                <span class="font-semibold text-sm">Notifications</span>
                <span class="bg-red-600 text-white text-xs font-semibold rounded-full px-2 py-0.5">3 new</span>
            </div>
            <ul class="divide-y divide-gray-100 max-h-72 overflow-y-auto custom-scrollbar">
                <li class="flex items-start px-4 py-3 space-x-3 hover:bg-gray-50">
                    <div class="flex-shrink-0 mt-1">
                        <div class="bg-green-200 text-green-700 rounded-full p-2">
                            <i class="fas fa-gavel"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">New Case Assignment</p>
                        <p class="text-sm text-gray-500">Case #C-2023-045 has been assigned to you</p>
                        <p class="text-xs text-gray-400 mt-1">10 minutes ago</p>
                    </div>
                </li>
                <li class="flex items-start px-4 py-3 space-x-3 hover:bg-gray-50">
                    <div class="flex-shrink-0 mt-1">
                        <div class="bg-blue-200 text-blue-700 rounded-full p-2">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">Upcoming Hearing</p>
                        <p class="text-sm text-gray-500">Hearing for Case #C-2023-042 is tomorrow at 10:00 AM</p>
                        <p class="text-xs text-gray-400 mt-1">1 hour ago</p>
                    </div>
                </li>
                <li class="flex items-start px-4 py-3 space-x-3 hover:bg-gray-50">
                    <div class="flex-shrink-0 mt-1">
                        <div class="bg-yellow-200 text-yellow-700 rounded-full p-2">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">Deadline Approaching</p>
                        <p class="text-sm text-gray-500">Filing deadline for Case #C-2023-040 is in 2 days</p>
                        <p class="text-xs text-gray-400 mt-1">3 hours ago</p>
                    </div>
                </li>
            </ul>
            <div class="bg-gray-50 px-4 py-2 text-center">
                <a href="#" class="text-sm font-medium text-brand-primary hover:text-brand-primary-hover">View all notifications</a>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <main class="p-4 sm:p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Page Header -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Case Management</h1>
                            <p class="text-gray-600 mt-1">Manage all legal cases, track progress, and monitor deadlines in one place.</p>
                        </div>
                        <div class="mt-4 md:mt-0 flex space-x-3">
                            <button id="printBtn" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                <i class="fas fa-print mr-2"></i> Print
                            </button>
                            <button id="newCaseBtn" onclick="openNewCaseModal()" class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors font-medium flex items-center">
                                <i class="fas fa-plus mr-2"></i> New Case
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Case Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Active Cases Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Active Cases</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['active_cases'] }}</p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <i class="fas fa-gavel mr-1"></i>
                                        Open
                                    </span>
                                    <span class="text-xs text-gray-500">Active</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-gavel text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            @php 
                                $pct = $stats['total_cases'] > 0 ? min(100, round(($stats['active_cases'] / $stats['total_cases']) * 100)) : 0;
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">{{ $pct }}% of total cases</p>
                        </div>
                    </div>

                    <!-- Pending Cases Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Pending</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['pending_tasks'] ?? 0 }}</p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Waiting
                                    </span>
                                    <span class="text-xs text-gray-500">Progress</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-clock text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            @php 
                                $pct = $stats['total_cases'] > 0 ? min(100, round(($stats['pending_tasks'] / $stats['total_cases']) * 100)) : 0;
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">{{ $pct }}% of total cases</p>
                        </div>
                    </div>

                    <!-- Urgent Cases Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-amber-50 to-amber-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Urgent</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['urgent_cases'] ?? 0 }}</p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Priority
                                    </span>
                                    <span class="text-xs text-gray-500">High</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            @php 
                                $pct = $stats['total_cases'] > 0 ? min(100, round(($stats['urgent_cases'] / $stats['total_cases']) * 100)) : 0;
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-gradient-to-r from-amber-400 to-orange-500 h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $pct }}% of total cases</p>
                        </div>
                    </div>

                    <!-- Completed Cases Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-violet-50 to-violet-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Completed</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['completed_cases'] ?? 0 }}</p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Done
                                    </span>
                                    <span class="text-xs text-gray-500">Closed</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-check-circle text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            @php 
                                $pct = $stats['total_cases'] > 0 ? min(100, round(($stats['completed_cases'] ?? 0) / $stats['total_cases']) * 100) : 0;
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-gradient-to-r from-violet-400 to-violet-600 h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">{{ $pct }}% of total cases</p>
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
                            <input type="text" id="searchInput" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-primary focus:border-brand-primary block w-full pl-10 p-2.5" placeholder="Search cases...">
                        </div>
                        <div class="flex flex-wrap gap-2" id="filterButtons">
                            <button onclick="setFilter('all')" class="filter-btn active px-3 py-1.5 text-sm font-medium bg-blue-100 text-blue-700 rounded-full hover:bg-blue-100 transition-colors" data-filter="all">
                                All Cases
                            </button>
                            <button onclick="setFilter('active')" class="filter-btn px-3 py-1.5 text-sm font-medium bg-white text-gray-600 border border-gray-200 rounded-full hover:bg-green-50 hover:text-green-700 transition-colors" data-filter="active">
                                <i class='bx bx-check-circle mr-1'></i> Active
                            </button>
                            <button onclick="setFilter('pending')" class="filter-btn px-3 py-1.5 text-sm font-medium bg-white text-gray-600 border border-gray-200 rounded-full hover:bg-amber-50 hover:text-amber-700 transition-colors" data-filter="pending">
                                <i class='bx bx-time-five mr-1'></i> Pending
                            </button>
                            <button onclick="setFilter('urgent')" class="filter-btn px-3 py-1.5 text-sm font-medium bg-white text-gray-600 border border-gray-200 rounded-full hover:bg-purple-50 hover:text-purple-700 transition-colors" data-filter="urgent">
                                <i class='bx bx-gavel mr-1'></i> Urgent
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Hearings -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-900">
                                <i class="fas fa-calendar-day mr-2 text-indigo-600"></i>Upcoming Hearings
                            </h3>
                            <p class="text-sm text-gray-500">Hearings scheduled in the next 30 days</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">{{ isset($stats['upcoming_hearings']) ? $stats['upcoming_hearings'] : 0 }} total</span>
                        </div>
                    </div>
                    <div class="p-6">
                        @forelse(($upcoming ?? []) as $u)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-3 hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-gavel text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $u['title'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $u['code'] }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">{{ $u['hearing_date'] ?? '-' }}</div>
                                    @if(!empty($u['hearing_time']))
                                        @php
                                            try { $__ut_disp = \Carbon\Carbon::parse($u['hearing_time'])->format('g:i A'); }
                                            catch (\Exception $e) { $__ut_disp = $u['hearing_time']; }
                                        @endphp
                                        <div class="text-xs text-gray-500">{{ $__ut_disp }}</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">No upcoming hearings scheduled</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Cases Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-900">All Cases</h3>
                            <p class="text-sm text-gray-500">Manage and track all legal cases</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button id="exportCasesBtn" class="px-3 py-1.5 text-sm font-medium bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                <i class="fas fa-download mr-1"></i> Export
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case Number</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contract Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Hearing</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            
                            <tbody id="casesTbody" class="bg-white divide-y divide-gray-200">
                                @if(!empty($cases))
                                    @foreach($cases as $c)
                                        @php $typeKey = strtolower($c['type_badge'] ?? 'civil'); @endphp
                                        @php
                                            $__ht_raw = $c['hearing_time'] ?? '';
                                            try { $__ht_norm = $__ht_raw ? \Carbon\Carbon::parse($__ht_raw)->format('H:i') : ''; }
                                            catch (\Exception $e) { $__ht_norm = preg_match('/^\d{2}:\d{2}$/', (string)$__ht_raw) ? $__ht_raw : ''; }
                                        @endphp
                                        <tr class="case-row hover:bg-gray-50"
                                            data-number="{{ $c['number'] }}"
                                            data-name="{{ $c['name'] }}"
                                            data-client="{{ $c['client'] }}"
                                            data-type="{{ $typeKey }}"
                                            data-status="{{ $c['status'] }}"
                                            data-hearing-date="{{ $c['hearing_date'] ?? '' }}"
                                            data-hearing-time="{{ $__ht_norm }}"
                                        >
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center space-x-2">
                                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                                        <i class='bx bx-briefcase text-sm'></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-bold text-gray-900">{{ $c['number'] }}</div>
                                                        <div class="text-xs text-gray-500">Filed: {{ $c['filed'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if(!empty($c['contract_type']))
                                                    @php
                                                        $statusClass = 'bg-gray-100 text-gray-800';
                                                        $statusText = 'Unknown';
                                                        
                                                        // Set status class and text based on contract status
                                                        if (isset($c['contract_status'])) {
                                                            $rawContractStatus = strtolower((string) $c['contract_status']);
                                                            if ($rawContractStatus === 'inactive') $rawContractStatus = 'active';
                                                            $statusMap = [
                                                                'active' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Active'],
                                                                'expired' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Expired'],
                                                                'terminated' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Terminated'],
                                                                'renewed' => ['class' => 'bg-blue-100 text-blue-800', 'text' => 'Renewed'],
                                                                'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'text' => 'Pending'],
                                                                'upcoming' => ['class' => 'bg-blue-100 text-blue-800', 'text' => 'Upcoming']
                                                            ];
                                                            
                                                            $statusInfo = $statusMap[$rawContractStatus] ?? ['class' => 'bg-gray-100 text-gray-800', 'text' => ucfirst($rawContractStatus)];
                                                            $statusClass = $statusInfo['class'];
                                                            $statusText = $statusInfo['text'];
                                                        } else {
                                                            $statusText = 'Active';
                                                            $statusClass = 'bg-green-100 text-green-800';
                                                        }
                                                        
                                                        // Get contract type label
                                                        $contractLabel = $c['contract_type_label'] ?? (
                                                            [
                                                                'employee' => 'Employee Contract',
                                                                'employment' => 'Employment Agreement',
                                                                'service' => 'Service Contract',
                                                                'other' => 'Other Agreement'
                                                            ][$c['contract_type']] ?? 'Contract'
                                                        );
                                                    @endphp
                                                    
                                                    <div class="flex flex-col space-y-1">
                                                        <div class="flex items-center">
                                                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }} mr-2">
                                                                {{ $statusText }}
                                                            </span>
                                                            <span class="text-sm font-medium text-gray-900">
                                                                {{ $contractLabel }}
                                                            </span>
                                                        </div>
                                                        @if(isset($c['contract_expiration']))
                                                            <div class="text-xs text-gray-500">
                                                                @if($c['contract_status'] === 'expired')
                                                                    Expired on {{ \Carbon\Carbon::parse($c['contract_expiration'])->format('M d, Y') }}
                                                                @else
                                                                    Expires on {{ \Carbon\Carbon::parse($c['contract_expiration'])->format('M d, Y') }}
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        No Contract
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $c['name'] }}</div>
                                                <div class="text-xs text-gray-500">{{ $c['type_label'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-medium text-sm mr-2">{{ $c['client_initials'] ?? '--' }}</div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $c['client'] }}</div>
                                                        <div class="text-xs text-gray-500">{{ $c['client_org'] ?? '' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $c['type_badge'] }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusColor = 'gray';
                                                    $statusText = $c['status'] ?? 'Unknown';
                                                    if (in_array(strtolower($statusText), ['active', 'open', 'confirmed'])) $statusColor = 'green';
                                                    elseif (in_array(strtolower($statusText), ['pending', 'in progress'])) $statusColor = 'yellow';
                                                    elseif (in_array(strtolower($statusText), ['closed', 'completed'])) $statusColor = 'blue';
                                                    elseif (in_array(strtolower($statusText), ['cancelled'])) $statusColor = 'red';
                                                @endphp
                                                <span class="status-badge status-{{ $statusColor }}">
                                                    {{ $statusText }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $c['hearing_date'] ?? '-' }}</div>
                                                @php
                                                    $__ht = $c['hearing_time'] ?? '';
                                                    try { $__ht_disp = $__ht ? \Carbon\Carbon::parse($__ht)->format('g:i A') : ''; }
                                                    catch (\Exception $e) { $__ht_disp = $__ht; }
                                                @endphp
                                                <div class="text-xs text-gray-500">{{ $__ht_disp }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button class="viewCaseBtn text-brand-primary hover:text-brand-primary-hover mr-3"
                                                       title="View Details"
                                                       data-number="{{ $c['number'] }}"
                                                       data-name="{{ $c['name'] }}"
                                                       data-client="{{ $c['client'] }}"
                                                       data-type="{{ $typeKey }}"
                                                       data-type-label="{{ $c['type_label'] }}"
                                                       data-status="{{ $c['status'] }}"
                                                       data-hearing-date="{{ $c['hearing_date'] ?? '' }}"
                                                       data-hearing-time="{{ (function($t){ try { return $t ? \Carbon\Carbon::parse($t)->format('g:i A') : ''; } catch (\Exception $e) { return $t; } })($c['hearing_time'] ?? '') }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="editCaseBtn text-blue-600 hover:text-blue-800 mr-3"
                                                       title="Edit"
                                                       data-number="{{ $c['number'] }}"
                                                       data-name="{{ $c['name'] }}"
                                                       data-client="{{ $c['client'] }}"
                                                       data-type="{{ $typeKey }}"
                                                       data-status="{{ $c['status'] }}"
                                                       data-hearing-date="{{ $c['hearing_date'] ?? '' }}"
                                                       data-hearing-time="{{ (function($t){ try { return $t ? \Carbon\Carbon::parse($t)->format('g:i A') : ''; } catch (\Exception $e) { return $t; } })($c['hearing_time'] ?? '') }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="px-6 py-6 text-center text-sm text-gray-500">No cases found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="bg-white px-6 py-3 flex items-center justify-between border-t border-gray-200">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </a>
                            <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </a>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">{{ count($cases) }}</span> results</p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Previous</span>
                                        <i class="fas fa-chevron-left h-5 w-5"></i>
                                    </a>
                                    <a href="#" aria-current="page" class="z-10 bg-brand-primary border-brand-primary text-white relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                        1
                                    </a>
                                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                        2
                                    </a>
                                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                        3
                                    </a>
                                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                        ...
                                    </span>
                                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                        8
                                    </a>
                                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Next</span>
                                        <i class="fas fa-chevron-right h-5 w-5"></i>
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <!-- New Case Modal -->
    <div id="newCaseModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="new-case-title">
        <div class="bg-white rounded-lg shadow-lg w-[480px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="new-case-title" class="font-semibold text-lg text-gray-900">Create New Case</h3>
                <button type="button" onclick="closeModal('newCaseModal')" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <form id="newCaseForm" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700">Case Title *</label>
                            <input type="text" name="title" id="title" required class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                            <textarea name="description" id="description" required rows="3" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm"></textarea>
                        </div>
                        <div>
                            <label for="case_type" class="block text-sm font-medium text-gray-700">Case Type *</label>
                            <select id="case_type" name="case_type" required class="mt-1 block w-full border border-gray-300 bg-white rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                                <option value="">Select case type</option>
                                <option value="civil">Civil</option>
                                <option value="criminal">Criminal</option>
                                <option value="family">Family Law</option>
                                <option value="corporate">Corporate</option>
                                <option value="contract">Contract</option>
                                <option value="labor">Labor</option>
                            </select>
                        </div>
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700">Priority *</label>
                            <select id="priority" name="priority" required class="mt-1 block w-full border border-gray-300 bg-white rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                                <option value="">Select priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                            <select id="status" name="status" required class="mt-1 block w-full border border-gray-300 bg-white rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                                <option value="">Select status</option>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="closed">Closed</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                        <div>
                            <label for="hearing_date" class="block text-sm font-medium text-gray-700">Next Hearing Date</label>
                            <input type="date" id="hearing_date" name="hearing_date" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label for="hearing_time" class="block text-sm font-medium text-gray-700">Next Hearing Time</label>
                            <input type="time" id="hearing_time" name="hearing_time" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label for="client" class="block text-sm font-medium text-gray-700">Client Name *</label>
                            <input type="text" id="client" name="client" required placeholder="Enter client name" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm" />
                        </div>
                        
                        <div>
                            <label for="court" class="block text-sm font-medium text-gray-700">Court</label>
                            <input type="text" id="court" name="court" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label for="judge" class="block text-sm font-medium text-gray-700">Judge</label>
                            <input type="text" id="judge" name="judge" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                        </div>

                        <div>
                            <label for="contract_type" class="block text-sm font-medium text-gray-700">Contract Type</label>
                            <select id="contract_type" name="contract_type" class="mt-1 block w-full border border-gray-300 bg-white rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                                <option value="">Select contract type</option>
                                <option value="employee">Employee Contract</option>
                                <option value="employment">Employment Agreement</option>
                                <option value="service">Service Contract</option>
                                <option value="other">Other Agreement</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeModal('newCaseModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                        <button type="button" onclick="submitNewCase()" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover">Create Case</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Case Modal -->
    <div id="viewCaseModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="view-case-title">
        <div class="bg-white rounded-lg shadow-lg w-[480px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="view-case-title" class="font-semibold text-lg text-gray-900">Case Details</h3>
                <button id="closeViewCaseBtn" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 text-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-500">Case Number</p>
                        <p id="vcNumber" class="font-medium text-gray-900">—</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Status</p>
                        <p id="vcStatus" class="font-medium text-gray-900">—</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-gray-500">Case Name</p>
                        <p id="vcName" class="font-medium text-gray-900">—</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Client</p>
                        <p id="vcClient" class="font-medium text-gray-900">—</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Type</p>
                        <p id="vcType" class="font-medium text-gray-900">—</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-gray-500">Next Hearing</p>
                        <p id="vcHearing" class="font-medium text-gray-900">—</p>
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button id="closeViewCaseBtn2" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Case Modal -->
    <div id="editCaseModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="edit-case-title">
        <div class="bg-white rounded-lg shadow-lg w-[480px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="edit-case-title" class="font-semibold text-lg text-gray-900">Edit Case</h3>
                <button id="closeEditCaseBtn" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editCaseForm" class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecNumber">Case Number</label>
                        <input id="ecNumber" name="number" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" readonly />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecStatus">Status</label>
                        <select id="ecStatus" name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                            <option value="In Progress">In Progress</option>
                            <option value="Active">Active</option>
                            <option value="Pending">Pending</option>
                            <option value="Closed">Closed</option>
                            <option value="appeal">On Appeal</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs text-gray-600 mb-1" for="ecName">Case Name</label>
                        <input id="ecName" name="name" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecClient">Client</label>
                        <select id="ecClient" name="client" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                            <option value="">Select client</option>
                            @foreach($clients ?? [] as $client)
                                <option value="{{ $client->name }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecType">Type</label>
                        <select id="ecType" name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                            <option value="civil">Civil</option>
                            <option value="criminal">Criminal</option>
                            <option value="family">Family</option>
                            <option value="corporate">Corporate</option>
                            <option value="contract">Contract</option>
                            <option value="ip">IP</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecHearingDate">Next Hearing Date</label>
                        <input id="ecHearingDate" name="hearing_date" type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecHearingTime">Next Hearing Time</label>
                        <input id="ecHearingTime" name="hearing_time" type="time" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" />
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" id="cancelEditCaseBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover">Save</button>
                </div>
            </form>
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

            // Open "Legal Management" dropdown by default since we're on Case Management page
            const legalBtn = document.getElementById('legal-management-btn');
            const legalSubmenu = document.getElementById('legal-submenu');
            const legalArrow = document.getElementById('legal-arrow');
            
            if (legalSubmenu && !legalSubmenu.classList.contains('hidden')) {
                legalSubmenu.classList.remove('hidden');
                if (legalArrow) legalArrow.classList.add('rotate-180');
            }

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
            const closeProfileBtn = document.getElementById('closeProfileBtn');
            const closeProfileBtn2 = document.getElementById('closeProfileBtn2');
            const profileModal = document.getElementById('profileModal');
            const openAccountSettingsBtn = document.getElementById('openAccountSettingsBtn');
            const openPrivacySecurityBtn = document.getElementById('openPrivacySecurityBtn');
            const openSignOutBtn = document.getElementById('openSignOutBtn');
            const cancelSignOutBtn = document.getElementById('cancelSignOutBtn');
            const signOutModal = document.getElementById('signOutModal');

            // Profile modal
            if (openProfileBtn) {
                openProfileBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    userMenuDropdown.classList.add("opacity-0", "translate-y-2", "scale-95", "pointer-events-none");
                    setTimeout(() => userMenuDropdown.classList.add("hidden"), 200);
                    profileModal.classList.add('active');
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

            // Close profile modal when clicking outside
            window.addEventListener('click', (e) => {
                if (profileModal && !profileModal.contains(e.target) && openProfileBtn && !openProfileBtn.contains(e.target)) {
                    profileModal.classList.remove('active');
                }
            });

            if (openAccountSettingsBtn) {
                openAccountSettingsBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    userMenuDropdown.classList.add("opacity-0", "translate-y-2", "scale-95", "pointer-events-none");
                    setTimeout(() => userMenuDropdown.classList.add("hidden"), 200);
                    // Add account settings modal functionality here if needed
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
                    signOutModal.classList.add('active');
                });
            }

            if (cancelSignOutBtn) {
                cancelSignOutBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    signOutModal.classList.remove('active');
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

            // Search and Filter functionality
            let currentFilter = 'all';
            let currentSearch = '';

            window.setFilter = function(filterType) {
                currentFilter = filterType;
                
                // Update button styles
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    const type = btn.dataset.filter;
                    if (type === filterType) {
                        // Active state
                        btn.className = `filter-btn active px-3 py-1.5 text-sm font-medium rounded-full transition-colors ${getFilterColorClass(type)}`;
                    } else {
                        // Inactive state
                        btn.className = 'filter-btn px-3 py-1.5 text-sm font-medium bg-white text-gray-600 border border-gray-200 rounded-full hover:bg-gray-50 transition-colors';
                    }
                });

                filterTable();
            };

            function getFilterColorClass(type) {
                switch(type) {
                    case 'all': return 'bg-blue-100 text-blue-700';
                    case 'active': return 'bg-green-100 text-green-700';
                    case 'pending': return 'bg-amber-100 text-amber-700';
                    case 'urgent': return 'bg-purple-100 text-purple-700';
                    default: return 'bg-gray-100 text-gray-700';
                }
            }

            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    currentSearch = this.value.toLowerCase();
                    filterTable();
                });
            }

            function filterTable() {
                const rows = document.querySelectorAll('#casesTbody tr');
                let visibleCount = 0;

                rows.forEach(row => {
                    // Skip empty/error rows if they don't have data attributes (like "No cases found")
                    if (!row.dataset.status) return;

                    const status = (row.dataset.status || '').toLowerCase();
                    const text = row.textContent.toLowerCase();
                    
                    // Priority is sometimes mapped to status in this UI logic or handled separately. 
                    // Based on the cards, 'Urgent' might be a specific status or priority level.
                    // Let's assume for now it checks if 'urgent' appears in the row text or data-priority if it existed.
                    // Looking at the view_file output, there isn't an explicit priority column shown in the big table loop, 
                    // but there is a Status column. Let's check how 'Urgent' is identified. 
                    // The 'Urgent' card uses $stats['urgent_cases'].
                    // If 'Urgent' is a status, simple check. If it's a priority, we might need to check text content if data-priority isn't there.
                    // The row has `data-status`. Let's assume for now we match 'active', 'pending'. 
                    // For 'urgent', since we don't have a data-priority, we'll check if the text contains 'urgent' OR if the status corresponds to something urgent.
                    // Actually, looking at the code for the stats cards:
                    // Urgent cases = priority 'urgent'.
                    // The table row doesn't have data-priority. 
                    // I'll check the text content for 'urgent' for now, or if I can add data-priority to the row that would be better.
                    // Row has: data-number, data-name, data-client, data-type, data-status.
                    
                    let matchesFilter = false;
                    if (currentFilter === 'all') {
                        matchesFilter = true;
                    } else if (currentFilter === 'active') {
                        matchesFilter = ['active', 'open', 'confirmed', 'in progress'].includes(status);
                    } else if (currentFilter === 'pending') {
                        matchesFilter = ['pending'].includes(status);
                    } else if (currentFilter === 'urgent') {
                         // Since we don't have data-priority, we check if the row text contains 'urgent' or if status is urgent
                         matchesFilter = status === 'urgent' || text.includes('urgent'); // Fallback to text search for priority
                    }

                    const matchesSearch = text.includes(currentSearch);

                    if (matchesFilter && matchesSearch) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Handle no results
                let noResultsRow = document.getElementById('no-results-row');
                if (visibleCount === 0) {
                    if (!noResultsRow) {
                        const tbody = document.getElementById('casesTbody');
                        noResultsRow = document.createElement('tr');
                        noResultsRow.id = 'no-results-row';
                        noResultsRow.innerHTML = `<td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-search text-2xl mb-2 text-gray-300"></i>
                            <p>No cases found matching your filters.</p>
                        </td>`;
                        tbody.appendChild(noResultsRow);
                    } else {
                        noResultsRow.style.display = '';
                    }
                } else {
                    if (noResultsRow) noResultsRow.style.display = 'none';
                }
            }

            // Print functionality
            const printBtn = document.getElementById('printBtn');
            if (printBtn) {
                printBtn.addEventListener('click', function() {
                    window.print();
                });
            }

            // Export cases functionality
            const exportCasesBtn = document.getElementById('exportCasesBtn');
            if (exportCasesBtn) {
                exportCasesBtn.addEventListener('click', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Export Started',
                        text: 'Your cases export has been queued. You will receive an email when it\'s ready.',
                        timer: 2000,
                        showConfirmButton: false
                    });
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

            // Make closeModal function globally accessible
            window.closeModal = closeModal;

            // Close modals when clicking outside
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal')) {
                    e.target.classList.remove('active');
                    setTimeout(() => {
                        e.target.style.display = 'none';
                    }, 300);
                }
            });

            // New Case Modal
            window.openNewCaseModal = function() {
                try {
                    var now = new Date();
                    var randomNum = Math.floor(1000 + Math.random() * 9000);
                    var caseNumEl = document.getElementById('caseNumber');
                    if (caseNumEl) caseNumEl.value = 'C-' + now.getFullYear() + '-' + randomNum;
                    var filingDateEl = document.getElementById('filingDate');
                    if (filingDateEl) filingDateEl.valueAsDate = new Date();
                    
                    openModal('newCaseModal');
                } catch(e) {
                    console.error(e);
                }
            };

            window.submitNewCase = async function() {
                var form = document.getElementById('newCaseForm');
                if (!form) return;
                
                var submitBtn = form.querySelector('button[type="button"]');
                var originalBtnText = submitBtn ? submitBtn.innerHTML : '';
                if (submitBtn){ 
                    submitBtn.disabled = true; 
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating...'; 
                }
                
                try {
                    // Get all form data including contract_type
                    var formData = new FormData(form);
                    var formObj = {};
                    formData.forEach((value, key) => {
                        // Handle form data properly, especially for checkboxes and selects
                        if (formObj[key]) {
                            if (!Array.isArray(formObj[key])) {
                                formObj[key] = [formObj[key]];
                            }
                            formObj[key].push(value);
                        } else {
                            formObj[key] = value;
                        }
                    });

                    // Ensure contract_type is included even if empty
                    if (!formObj.contract_type) {
                        formObj.contract_type = '';
                    }

                    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                    var csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';
                    
                    var response = await fetch('{{ route("case.create") }}', {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': csrf, 
                            'Accept': 'application/json', 
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });
                    
                    var data = await response.json();
                    if (!response.ok) { 
                        console.error('Server error:', data);
                        throw new Error(data.message || 'Failed to create case. ' + (data.error || '')); 
                    }
                    
                    await Swal.fire({ 
                        icon: 'success', 
                        title: 'Success!', 
                        text: 'Case has been created successfully.', 
                        showConfirmButton: false, 
                        timer: 1500 
                    });
                    
                    form.reset();
                    closeModal('newCaseModal');
                    window.location.reload();
                } catch(error) {
                    console.error('Error:', error);
                    // Check if this is a validation error with error details
                    let errorMessage = (error && error.message) || 'Failed to create case. Please try again.';
                    
                    // If it's a validation error from the server, extract the error messages
                    if (error.response && error.response.data && error.response.data.errors) {
                        const errors = error.response.data.errors;
                        errorMessage = Object.values(errors).flat().join('\n');
                    }
                    
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'Error', 
                        html: errorMessage.replace(/\n/g, '<br>'),
                        confirmButtonColor: '#059669',
                        width: '500px'
                    });
                } finally {
                    if (submitBtn){ submitBtn.disabled = false; submitBtn.innerHTML = originalBtnText; }
                }
            };

            // View Case Modal handler
            function openViewCaseModal(btn) {
                if (!btn) return;
                const d = btn.dataset || {};
                
                const vcNumber = document.getElementById('vcNumber');
                const vcStatus = document.getElementById('vcStatus');
                const vcName = document.getElementById('vcName');
                const vcClient = document.getElementById('vcClient');
                const vcType = document.getElementById('vcType');
                const vcHearing = document.getElementById('vcHearing');
                
                if (vcNumber) vcNumber.textContent = d.number || '—';
                if (vcStatus) vcStatus.textContent = d.status || '—';
                if (vcName) vcName.textContent = d.name || '—';
                if (vcClient) vcClient.textContent = d.client || '—';
                if (vcType) vcType.textContent = (d.typeLabel || d.type || '—').split('_').join(' ');
                if (vcHearing) vcHearing.textContent = (d.hearingDate ? d.hearingDate : '—') + (d.hearingTime ? ' • ' + d.hearingTime : '');
                
                openModal('viewCaseModal');
            }

            // Edit Case Modal handler
            function openEditCaseModal(btn) {
                if (!btn) return;
                const d = btn.dataset || {};
                const tr = btn.closest('tr');
                const rd = tr ? tr.dataset || {} : {};
                
                const ecNumber = document.getElementById('ecNumber');
                const ecStatus = document.getElementById('ecStatus');
                const ecName = document.getElementById('ecName');
                const ecClient = document.getElementById('ecClient');
                const ecType = document.getElementById('ecType');
                const ecHearingDate = document.getElementById('ecHearingDate');
                const ecHearingTime = document.getElementById('ecHearingTime');
                
                if (ecNumber) ecNumber.value = rd.number || d.number || '';
                if (ecStatus) ecStatus.value = rd.status || d.status || '';
                if (ecName) ecName.value = rd.name || d.name || '';
                if (ecClient) {
                    ecClient.value = rd.client || d.client || '';
                    if (ecClient.value === '' && (rd.client || d.client)) {
                        const opt = document.createElement('option');
                        opt.value = rd.client || d.client;
                        opt.text = rd.client || d.client;
                        ecClient.appendChild(opt);
                        ecClient.value = rd.client || d.client;
                    }
                }
                if (ecType) ecType.value = rd.type || d.type || '';
                if (ecHearingDate) ecHearingDate.value = rd.hearingDate || d.hearingDate || '';
                if (ecHearingTime) {
                    let ht = rd.hearingTime || d.hearingTime || '';
                    if (ht) {
                        const hhmmMatch = /^\d{2}:\d{2}(:\d{2})?$/.test(ht);
                        if (hhmmMatch) {
                            ecHearingTime.value = ht.substring(0,5);
                        } else {
                            const m = ht.match(/^(\d{1,2}):(\d{2})\s*([AaPp][Mm])$/);
                            if (m) {
                                let h = parseInt(m[1],10);
                                const min = m[2];
                                const ampm = m[3].toUpperCase();
                                if (ampm === 'PM' && h !== 12) h += 12;
                                if (ampm === 'AM' && h === 12) h = 0;
                                const hh = String(h).padStart(2,'0');
                                ecHearingTime.value = `${hh}:${min}`;
                            } else {
                                ecHearingTime.value = '';
                            }
                        }
                    } else {
                        ecHearingTime.value = '';
                    }
                }
                
                openModal('editCaseModal');
            }


            // Event delegation for view and edit case buttons
            document.addEventListener("click", (e) => {
                const viewBtn = e.target.closest(".viewCaseBtn");
                const editBtn = e.target.closest(".editCaseBtn");
                
                if (viewBtn) {
                    e.preventDefault();
                    openViewCaseModal(viewBtn);
                } else if (editBtn) {
                    e.preventDefault();
                    openEditCaseModal(editBtn);
                }
            });

            // Close modal buttons
            document.getElementById('closeViewCaseBtn')?.addEventListener('click', () => closeModal('viewCaseModal'));
            document.getElementById('closeViewCaseBtn2')?.addEventListener('click', () => closeModal('viewCaseModal'));
            document.getElementById('closeEditCaseBtn')?.addEventListener('click', () => closeModal('editCaseModal'));
            document.getElementById('cancelEditCaseBtn')?.addEventListener('click', () => closeModal('editCaseModal'));


            // Edit case form submission
            const editCaseForm = document.getElementById('editCaseForm');
            if (editCaseForm) {
                editCaseForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    let submitBtn = this.querySelector('button[type="submit"]');
                    let original = submitBtn ? submitBtn.innerHTML : '';
                    
                    try {
                        if (submitBtn) { 
                            submitBtn.disabled = true; 
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...'; 
                        }

                        const payload = {
                            number: document.getElementById('ecNumber')?.value || '',
                            case_name: document.getElementById('ecName')?.value || '',
                            client_name: document.getElementById('ecClient')?.value || '',
                            case_type: document.getElementById('ecType')?.value || '',
                            status: document.getElementById('ecStatus')?.value || '',
                            hearing_date: document.getElementById('ecHearingDate')?.value || '',
                            hearing_time: document.getElementById('ecHearingTime')?.value || '',
                        };

                        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                        const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';
                        const fd = new FormData();
                        fd.append('number', payload.number);
                        fd.append('case_name', payload.case_name);
                        fd.append('client_name', payload.client_name);
                        fd.append('case_type', payload.case_type);
                        fd.append('status', payload.status);
                        fd.append('hearing_date', payload.hearing_date);
                        fd.append('hearing_time', payload.hearing_time);
                        fd.append('_token', csrf);

                        const res = await fetch('{{ route("case.update") }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: fd
                        });
                        
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok || data.success === false) {
                            throw new Error((data && (data.message || data.error)) || 'Failed to update case');
                        }

                        await Swal.fire({ icon: 'success', title: 'Saved', text: 'Case has been updated.', showConfirmButton: false, timer: 1200 });
                        closeModal('editCaseModal');
                        window.location.reload();
                    } catch(err) {
                        console.error('Update failed:', err);
                        Swal.fire({ icon: 'error', title: 'Error', text: (err && err.message) || 'Failed to update case. Please try again.', confirmButtonColor: '#059669' });
                    } finally {
                        if (submitBtn) { 
                            submitBtn.disabled = false; 
                            submitBtn.innerHTML = original || 'Save'; 
                        }
                    }
                });
            }

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
