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
                <svg id="facilities-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="facilities-submenu" class="submenu mt-1">
                <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                    <a href="{{ route('room-equipment') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
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
                <h1 class="text-lg font-bold text-gray-800 hidden md:block">Room & Equipment Booking</h1>
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
                            <h1 class="text-2xl font-bold text-gray-900">Room & Equipment Booking</h1>
                            <p class="text-gray-600 mt-1">Book rooms and equipment for meetings and events</p>
                        </div>
                        <div class="mt-4 md:mt-0 flex space-x-3">
                            <a href="{{ route('scheduling.calendar') }}" class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors font-medium flex items-center">
                                <i class='bx bx-calendar mr-2'></i> View Calendar
                            </a>
                            <button id="historyBtn" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                <i class="fas fa-history mr-2"></i> Booking History
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Main Booking Form -->
                <div class="mb-8">
                    <!-- Booking Form -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="font-semibold text-lg text-gray-900">
                                    <i class='bx bx-calendar-plus mr-2'></i> New Booking
                                </h3>
                            </div>
                            <form id="combinedBookingForm" class="space-y-4" action="{{ route('booking.combined') }}" method="POST" novalidate>
                                @csrf
                                
                                <!-- Booking Type and Name -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Booking Type <span class="text-red-500">*</span></label>
                                        <select name="booking_type" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" required>
                                            <option value="">-- Select booking type --</option>
                                            <option value="meeting">Meeting</option>
                                            <option value="presentation">Presentation</option>
                                            <option value="training">Training</option>
                                            <option value="workshop">Workshop</option>
                                            <option value="conference">Conference</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Your Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" placeholder="Enter your full name" required>
                                    </div>
                                </div>
                                
                                <!-- What to Book -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Room <span class="text-red-500">*</span></label>
                                        <select name="room" id="roomSelect" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" required>
                                            <option value="">-- Select a room --</option>
                                            <option value="conference">Conference Room</option>
                                            <option value="meeting">Meeting Room</option>
                                            <option value="training">Training Room</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Equipment (Optional)</label>
                                        <select name="equipment[]" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                                            <option value="">No equipment needed</option>
                                            <option value="projector">Projector</option>
                                            <option value="laptop">Laptop</option>
                                            <option value="audio">Audio System</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Lead Time and Date/Time -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Lead Time (days before event)</label>
                                        <input type="number" name="lead_time" min="0" max="30" value="0" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" placeholder="How many days needed to prepare?">
                                    </div>
                                    <div class="space-y-4">
                                        <div class="form-group">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                                            <input type="date" name="date" id="bookingDate" min="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" required>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="form-group">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Time <span class="text-red-500">*</span></label>
                                                <input type="time" name="start_time" id="startTime" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">End Time <span class="text-red-500">*</span></label>
                                                <input type="time" name="end_time" id="endTime" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Purpose -->
                                <div class="form-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Purpose <span class="text-red-500">*</span></label>
                                    <textarea name="purpose" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent" placeholder="What's this booking for?" required></textarea>
                                </div>

                                <button id="submitBookingBtn" type="submit" class="w-full bg-brand-primary text-white py-3 px-4 rounded-lg hover:bg-brand-primary-hover transition-colors focus:outline-none focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 font-medium">
                                    Book Now
                                </button>
                            </form>
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
                <p class="text-sm text-gray-500 mb-6">Are you sure you want to cancel this booking? This action cannot be undone.</p>
                <div class="flex justify-center space-x-4">
                    <button type="button" onclick="closeModal('cancelBookingModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        No, Keep It
                    </button>
                    <button type="button" id="confirmCancelBtn" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Yes, Cancel Booking
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message Toast -->
    @if(session('success'))
    <div id="successToast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center justify-between z-50" style="min-width: 300px;">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="document.getElementById('successToast').remove()" class="ml-4 text-white hover:text-gray-100">
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
                historyBtn.addEventListener('click', function() {
                    // Redirect to reservation history
                    window.location.href = '{{ route("reservation.history") }}';
                });
            }

            // Form submission handler
            const bookingForm = document.getElementById('combinedBookingForm');
            if (bookingForm) {
                bookingForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
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
                        formObject.room = formData.get('room') || null;
                        formObject.purpose = formData.get('purpose') || 'Not specified';
                        formObject.date = formData.get('date') || new Date().toISOString().split('T')[0];
                        formObject.start_time = formData.get('start_time') || null;
                        formObject.end_time = formData.get('end_time') || null;
                        formObject.lead_time = formData.get('lead_time') || 0; // Add lead time field
                        formObject.equipment_data = equipmentData;
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

            function normalizeRoomKey(v){
                return (v || '').toString().toLowerCase();
            }

            function bookingMatchesRoom(b, key){
                const type = (b.type || '').toString().toLowerCase();
                const name = normalizeRoomKey(b.name || b.title || '');
                return (type === 'room') && (name.includes(key) || key.includes(name));
            }

            function overlaps(aStart, aEnd, bStart, bEnd){
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
                        const occupying = ['approved','pending','occupied'].includes(bStatus) || bStatus === '';
                        if (!occupying) return false;
                        if (bDate !== dateVal) return false;
                        if (!bookingMatchesRoom(b, roomKey)) return false;
                        const bs = toMinutes((b.start_time || '').slice(0,5));
                        const be = toMinutes((b.end_time || '').slice(0,5));
                        if (bs === null || be === null) return false;
                        return overlaps(startMins, endMins, bs, be);
                    } catch(_) { return false; }
                });

                if (conflicts.length > 0) {
                    e.preventDefault();
                    const first = conflicts[0];
                    const range = `${(first.start_time || '').slice(0,5)} - ${(first.end_time || '').slice(0,5)}`;
                    Swal.fire({
                        icon: 'error',
                        title: 'Room is occupied',
                        html: `<div class="text-left">The selected room is already booked on <b>${dateVal}</b> between <b>${range}</b>.<br/><div class="mt-2">Please choose a different time slot or room.</div></div>`,
                    });
                }
            });
        });

        // Modal functions from original code
        window.closeModal = function(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        };

        window.showBookingDetails = function(booking) {
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
</body>
</html>