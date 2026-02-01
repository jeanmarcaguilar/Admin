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

        .category-card {
            transition: all 0.2s ease-in-out;
            border: 1px solid #e5e7eb;
        }

        .category-card:hover {
            border-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .category-card.active {
            border-color: #059669;
            background-color: #f0fdf4;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.15);
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

        input:checked + .permission-toggle-slider {
            background-color: #059669;
        }

        input:checked + .permission-toggle-slider:before {
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
            <a href="/dashboard"
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
          <a href="{{ route('document.archival.retention.policy') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
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
                    Microfinance HR © 2026<br/>
                    Human Resource III System
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
                <h1 class="text-lg font-bold text-gray-800 hidden md:block">Access Control</h1>
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
                                Admin
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
                        <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">Profile</a>
                        <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">Settings</a>
                        <div class="h-px bg-gray-100"></div>
                        <a href="#" class="block px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition">Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Archive Policy Notes -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-base font-semibold text-gray-900">
                            <i class="bx bx-info-circle text-purple-600 mr-2"></i>
                            Archive Policy Notes
                        </h2>
                        <span class="text-xs text-gray-500">Category-specific retention guidelines</span>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                        <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-bold">₱</span>
                                <h4 class="font-semibold text-green-800 text-sm">Financial</h4>
                            </div>
                            <p class="text-xs text-green-700"><strong>Archive:</strong> 6 months</p>
                            <p class="text-xs text-green-700"><strong>Retention:</strong> 7 years</p>
                        </div>
                        
                        <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="bx bx-id-card text-blue-600 text-sm"></i>
                                <h4 class="font-semibold text-blue-800 text-sm">HR</h4>
                            </div>
                            <p class="text-xs text-blue-700"><strong>Archive:</strong> 1 year</p>
                            <p class="text-xs text-blue-700"><strong>Retention:</strong> 5 years</p>
                        </div>
                        
                        <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-200">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="bx bx-gavel text-yellow-600 text-sm"></i>
                                <h4 class="font-semibold text-yellow-800 text-sm">Legal</h4>
                            </div>
                            <p class="text-xs text-yellow-700"><strong>Archive:</strong> 3 months</p>
                            <p class="text-xs text-yellow-700"><strong>Retention:</strong> 10 years</p>
                        </div>
                        
                        <div class="bg-purple-50 rounded-lg p-3 border border-purple-200">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="bx bx-cog text-purple-600 text-sm"></i>
                                <h4 class="font-semibold text-purple-800 text-sm">Operations</h4>
                            </div>
                            <p class="text-xs text-purple-700"><strong>Archive:</strong> 3 months</p>
                            <p class="text-xs text-purple-700"><strong>Retention:</strong> 3 years</p>
                        </div>
                        
                        <div class="bg-red-50 rounded-lg p-3 border border-red-200">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="bx bx-file text-red-600 text-sm"></i>
                                <h4 class="font-semibold text-red-800 text-sm">Contracts</h4>
                            </div>
                            <p class="text-xs text-red-700"><strong>Archive:</strong> 6 months</p>
                            <p class="text-xs text-red-700"><strong>Retention:</strong> 7 years</p>
                        </div>
                        
                        <div class="bg-orange-50 rounded-lg p-3 border border-orange-200">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="bx bx-bolt text-orange-600 text-sm"></i>
                                <h4 class="font-semibold text-orange-800 text-sm">Utilities</h4>
                            </div>
                            <p class="text-xs text-orange-700"><strong>Archive:</strong> 1 year</p>
                            <p class="text-xs text-orange-700"><strong>Retention:</strong> 3 years</p>
                        </div>
                        
                        <div class="bg-indigo-50 rounded-lg p-3 border border-indigo-200">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="bx bx-folder text-indigo-600 text-sm"></i>
                                <h4 class="font-semibold text-indigo-800 text-sm">Projects</h4>
                            </div>
                            <p class="text-xs text-indigo-700"><strong>Archive:</strong> Completion</p>
                            <p class="text-xs text-indigo-700"><strong>Retention:</strong> 5 years</p>
                        </div>
                        
                        <div class="bg-lime-50 rounded-lg p-3 border border-lime-200">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="bx bx-shopping-bag text-lime-600 text-sm"></i>
                                <h4 class="font-semibold text-lime-800 text-sm">Procurement</h4>
                            </div>
                            <p class="text-xs text-lime-700"><strong>Archive:</strong> 6 months</p>
                            <p class="text-xs text-lime-700"><strong>Retention:</strong> 5 years</p>
                        </div>
                        
                        <div class="bg-cyan-50 rounded-lg p-3 border border-cyan-200">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="bx bx-laptop text-cyan-600 text-sm"></i>
                                <h4 class="font-semibold text-cyan-800 text-sm">IT</h4>
                            </div>
                            <p class="text-xs text-cyan-700"><strong>Archive:</strong> 1 year</p>
                            <p class="text-xs text-cyan-700"><strong>Retention:</strong> 3 years</p>
                        </div>
                        
                        <div class="bg-pink-50 rounded-lg p-3 border border-pink-200">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-bold">₱</span>
                                <h4 class="font-semibold text-pink-800 text-sm">Payroll</h4>
                            </div>
                            <p class="text-xs text-pink-700"><strong>Archive:</strong> 2 years</p>
                            <p class="text-xs text-pink-700"><strong>Retention:</strong> 7 years</p>
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
                                <span class="text-xl font-bold">₱</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Financial</div>
                                <div class="text-xs text-gray-500">Budgets, invoices, reports</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="hr">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                                <i class="bx bx-id-card text-xl"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">HR</div>
                                <div class="text-xs text-gray-500">Employee files, policies</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="legal">
                            <div class="w-10 h-10 rounded-lg bg-yellow-100 text-yellow-700 flex items-center justify-center">
                                <i class="bx bx-gavel text-xl"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Legal</div>
                                <div class="text-xs text-gray-500">Contracts, case files</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="operations">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center">
                                <i class="bx bx-cog text-xl"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Operations</div>
                                <div class="text-xs text-gray-500">Processes, procedures</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="contracts">
                            <div class="w-10 h-10 rounded-lg bg-red-100 text-red-700 flex items-center justify-center">
                                <i class="bx bx-file text-xl"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Contracts</div>
                                <div class="text-xs text-gray-500">Agreements, NDAs</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="utilities">
                            <div class="w-10 h-10 rounded-lg bg-orange-100 text-orange-700 flex items-center justify-center">
                                <i class="bx bx-bolt text-xl"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Utilities</div>
                                <div class="text-xs text-gray-500">Electricity, water, gas</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="projects">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center">
                                <i class="bx bx-folder text-xl"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Projects</div>
                                <div class="text-xs text-gray-500">Project plans, reports</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="procurement">
                            <div class="w-10 h-10 rounded-lg bg-lime-100 text-lime-700 flex items-center justify-center">
                                <i class="bx bx-shopping-bag text-xl"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Procurement</div>
                                <div class="text-xs text-gray-500">Vendors, purchases</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="it">
                            <div class="w-10 h-10 rounded-lg bg-cyan-100 text-cyan-700 flex items-center justify-center">
                                <i class="bx bx-laptop text-xl"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">IT</div>
                                <div class="text-xs text-gray-500">Software, hardware</div>
                            </div>
                        </button>
                        <button type="button" class="category-card group bg-white rounded-xl p-5 text-left flex items-start gap-3" data-category="payroll">
                            <div class="w-10 h-10 rounded-lg bg-pink-100 text-pink-700 flex items-center justify-center">
                                <i class="bx bx-money text-xl"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Payroll</div>
                                <div class="text-xs text-gray-500">Employee compensation</div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Documents Section -->
                <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Archived Documents</h2>
                            <p class="text-sm text-gray-500 mt-1">Manage and review archived documents</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="text-sm text-brand-primary hover:text-brand-primary-hover font-medium">
                                <i class="fas fa-sync-alt mr-1"></i> Refresh
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Retention</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($documents ?? [] as $doc)
                                    @php
                                        $dtype = strtoupper($doc['type'] ?? '');
                                        $icon = in_array($dtype, ['PDF']) ? 'bxs-file-pdf text-red-500' : (in_array($dtype, ['WORD','DOC','DOCX']) ? 'bxs-file-doc text-blue-500' : (in_array($dtype, ['EXCEL','XLS','XLSX']) ? 'bxs-file-txt text-green-500' : 'bxs-file text-gray-500'));
                                        $category = $doc['category'] ?? 'General';
                                        $retention = match($category) {
                                            'Financial' => 'Archive after 6 months - Retain 7 Years',
                                            'HR' => 'Archive after 1 year - Retain 5 Years', 
                                            'Legal' => 'Archive after 3 months - Retain 10 Years',
                                            'Operations' => 'Archive after 3 months - Retain 3 Years',
                                            'Contracts' => 'Archive after 6 months - Retain 7 Years',
                                            'Utilities' => 'Archive after 1 year - Retain 3 Years',
                                            'Projects' => 'Archive after project completion - Retain 5 Years',
                                            'Procurement' => 'Archive after 6 months - Retain 5 Years',
                                            'IT' => 'Archive after 1 year - Retain 3 Years',
                                            'Payroll' => 'Archive after 2 years - Retain 7 Years',
                                            default => 'Archive after 6 months - Retain 3 Years'
                                        };
                                    @endphp
                                    <tr class="hover:bg-gray-50" data-doc-id="{{ $doc['id'] ?? '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <i class="bx {{ $icon }} text-xl mr-3"></i>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $doc['name'] ?? 'Unknown Document' }}</div>
                                                    <div class="text-xs text-gray-500">{{ $doc['size'] ?? 'Unknown size' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">{{ $dtype }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 bg-{{ $category === 'Financial' ? 'blue' : ($category === 'HR' ? 'purple' : ($category === 'Legal' ? 'red' : 'gray')) }}-100 text-{{ $category === 'Financial' ? 'blue' : ($category === 'HR' ? 'purple' : ($category === 'Legal' ? 'red' : 'gray')) }}-700 text-xs font-medium rounded-full">{{ $category }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $doc['uploaded'] ?? 'Unknown date' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $retention }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button onclick="showViewModal({{ json_encode($doc) }})" class="px-3 py-1.5 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-lg text-xs font-semibold hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 shadow hover:shadow-md transform hover:-translate-y-0.5 mr-2 flex items-center">
                                                <i class="fas fa-eye mr-1"></i>
                                                View
                                            </button>
                                            <button onclick="showDeleteModal('{{ $doc['id'] ?? '' }}')" class="px-3 py-1.5 bg-gradient-to-r from-red-500 to-rose-500 text-white rounded-lg text-xs font-semibold hover:from-red-600 hover:to-rose-600 transition-all duration-200 shadow hover:shadow-md transform hover:-translate-y-0.5 flex items-center">
                                                <i class="fas fa-trash mr-1"></i>
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">No archived documents available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                Showing {{ count($documents ?? []) }} archived documents
                            </div>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50">
                                    Previous
                                </button>
                                <button class="px-3 py-1 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50">
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

            // Permission toggle functionality
            document.querySelectorAll('.permission-toggle input').forEach(toggle => {
                toggle.addEventListener('change', function() {
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

            // Category filtering
            const categoryCards = document.querySelectorAll('.category-card');
            categoryCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove active class from all cards
                    categoryCards.forEach(c => c.classList.remove('active'));
                    // Add active class to clicked card
                    this.classList.add('active');
                    
                    const selectedCategory = this.dataset.category;
                    const documentRows = document.querySelectorAll('tbody tr');
                    
                    documentRows.forEach(row => {
                        if (selectedCategory === 'all') {
                            row.style.display = '';
                        } else {
                            const categoryCell = row.querySelector('td:nth-child(3) span');
                            if (categoryCell) {
                                const rowCategory = categoryCell.textContent.toLowerCase();
                                if (rowCategory === selectedCategory.toLowerCase()) {
                                    row.style.display = '';
                                } else {
                                    row.style.display = 'none';
                                }
                            } else {
                                row.style.display = 'none';
                            }
                        }
                    });
                    
                    // Update visible count
                    const visibleRows = Array.from(documentRows).filter(row => row.style.display !== 'none');
                    const visibleCount = document.querySelector('.text-sm.text-gray-500');
                    if (visibleCount) {
                        visibleCount.textContent = `Showing ${visibleRows.length} archived documents`;
                    }
                });
            });
        });
    </script>

    <!-- View Document Modal -->
    <div id="viewDocumentModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl shadow-2xl max-w-2xl w-full transform transition-all duration-500 scale-95 opacity-0" id="viewModalContent">
            <!-- Modal Header with Gradient -->
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 px-6 py-6 rounded-t-3xl relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.4"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-size: 60px 60px;"></div>
                </div>
                
                <div class="relative flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <i class="fas fa-file-alt text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white">Document Details</h3>
                    </div>
                    <button onclick="closeViewModal()" class="text-white/80 hover:text-white hover:bg-white/20 rounded-xl p-2 transition-all duration-200 backdrop-blur-sm">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="p-8 bg-gradient-to-br from-blue-50 to-white space-y-6">
                <!-- Document Icon and Basic Info -->
                <div class="flex items-center gap-5 pb-6 border-b border-gray-200">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-xl ring-4 ring-white/50">
                        <i id="modalDocIcon" class="fas fa-file text-white text-3xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 id="modalDocName" class="text-xl font-bold text-gray-900 mb-1"></h4>
                        <div class="flex items-center gap-2">
                            <span id="modalDocType" class="text-sm text-blue-600 font-semibold bg-blue-100 px-3 py-1 rounded-full"></span>
                            <span id="modalDocCategory" class="text-sm text-purple-600 font-semibold bg-purple-100 px-3 py-1 rounded-full"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Document Information Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl p-4 border border-emerald-100 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-database text-white text-sm"></i>
                            </div>
                            <label class="text-xs font-bold text-emerald-600 uppercase tracking-wider">File Size</label>
                        </div>
                        <p id="modalDocSize" class="text-gray-900 font-semibold text-sm mt-1"></p>
                    </div>
                    
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl p-4 border border-amber-100 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar text-white text-sm"></i>
                            </div>
                            <label class="text-xs font-bold text-amber-600 uppercase tracking-wider">Uploaded Date</label>
                        </div>
                        <p id="modalDocUploaded" class="text-gray-900 font-semibold text-sm mt-1"></p>
                    </div>
                    
                    <div class="bg-gradient-to-r from-rose-50 to-pink-50 rounded-2xl p-4 border border-rose-100 hover:shadow-md transition-all duration-200 md:col-span-2">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 bg-rose-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-white text-sm"></i>
                            </div>
                            <label class="text-xs font-bold text-rose-600 uppercase tracking-wider">Retention Policy</label>
                        </div>
                        <p id="modalDocRetention" class="text-gray-900 font-semibold text-sm mt-1"></p>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-5 rounded-b-3xl border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <i class="fas fa-shield-alt text-green-500"></i>
                        <span>Archived Document Information</span>
                    </div>
                    <button onclick="closeViewModal()" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-sm font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center gap-2">
                        <i class="fas fa-check"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Document Modal -->
    <div id="deleteDocumentModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full transform transition-all duration-500 scale-95 opacity-0" id="deleteModalContent">
            <!-- Modal Header with Gradient -->
            <div class="bg-gradient-to-r from-red-600 via-rose-600 to-pink-600 px-6 py-6 rounded-t-3xl relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.4"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-size: 60px 60px;"></div>
                </div>
                
                <div class="relative flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <i class="fas fa-trash text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white">Delete Document</h3>
                    </div>
                    <button onclick="closeDeleteModal()" class="text-white/80 hover:text-white hover:bg-white/20 rounded-xl p-2 transition-all duration-200 backdrop-blur-sm">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="p-8 bg-gradient-to-br from-red-50 to-white text-center">
                <!-- Warning Icon -->
                <div class="mx-auto w-20 h-20 bg-gradient-to-br from-red-100 to-rose-100 rounded-full flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-exclamation-triangle text-red-600 text-3xl animate-pulse"></i>
                </div>
                
                <!-- Warning Message -->
                <h3 class="text-xl font-bold text-gray-900 mb-3">Are you absolutely sure?</h3>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    This action <span class="font-semibold text-red-600">cannot be undone</span>. 
                    This will permanently delete the archived document and remove it from your records.
                </p>
                
                <!-- Action Buttons -->
                <div class="flex justify-center space-x-4">
                    <button onclick="closeDeleteModal()" class="px-6 py-3 border-2 border-gray-300 rounded-xl text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm">
                        <i class="fas fa-shield-alt mr-2"></i>
                        No, Keep It
                    </button>
                    <button onclick="confirmDelete()" class="px-6 py-3 bg-gradient-to-r from-red-600 to-rose-600 text-white rounded-xl text-sm font-semibold hover:from-red-700 hover:to-rose-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                        <i class="fas fa-trash mr-2"></i>
                        Yes, Delete Document
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentDocumentId = null;

        // View Document Modal Functions
        window.showViewModal = function(doc) {
            currentDocumentId = doc.id;
            
            // Populate modal with document data
            document.getElementById('modalDocName').textContent = doc.name || 'Unknown Document';
            document.getElementById('modalDocType').textContent = doc.type || 'Unknown';
            document.getElementById('modalDocCategory').textContent = doc.category || 'General';
            document.getElementById('modalDocSize').textContent = doc.size || 'Unknown size';
            document.getElementById('modalDocUploaded').textContent = doc.uploaded || 'Unknown date';
            document.getElementById('modalDocRetention').textContent = doc.retention || 'Unknown retention policy';
            
            // Update icon based on document type
            const iconElement = document.getElementById('modalDocIcon');
            const dtype = (doc.type || '').toUpperCase();
            if (dtype.includes('PDF')) {
                iconElement.className = 'fas fa-file-pdf text-white text-3xl';
            } else if (dtype.includes('WORD') || dtype.includes('DOC') || dtype.includes('DOCX')) {
                iconElement.className = 'fas fa-file-word text-white text-3xl';
            } else if (dtype.includes('EXCEL') || dtype.includes('XLS') || dtype.includes('XLSX')) {
                iconElement.className = 'fas fa-file-excel text-white text-3xl';
            } else {
                iconElement.className = 'fas fa-file text-white text-3xl';
            }
            
            // Show modal with animation
            const modal = document.getElementById('viewDocumentModal');
            const modalContent = document.getElementById('viewModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        };

        window.closeViewModal = function() {
            const modalContent = document.getElementById('viewModalContent');
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                document.getElementById('viewDocumentModal').classList.add('hidden');
            }, 300);
        };

        // Delete Document Modal Functions
        window.showDeleteModal = function(docId) {
            currentDocumentId = docId;
            
            const modal = document.getElementById('deleteDocumentModal');
            const modalContent = document.getElementById('deleteModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        };

        window.closeDeleteModal = function() {
            const modalContent = document.getElementById('deleteModalContent');
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                document.getElementById('deleteDocumentModal').classList.add('hidden');
            }, 300);
        };

        window.confirmDelete = function() {
            // Here you would typically make an AJAX call to delete the document
            // For now, we'll just show a success message and close the modal
            closeDeleteModal();
            
            // Show success message
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 fade-in';
            successDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="bx bx-check-circle text-xl mr-2"></i>
                    <span>Document deleted successfully</span>
                </div>
            `;
            document.body.appendChild(successDiv);
            
            setTimeout(() => {
                successDiv.style.opacity = '0';
                setTimeout(() => successDiv.remove(), 300);
            }, 3000);
            
            // Remove the row from table
            if (currentDocumentId) {
                const row = document.querySelector(`tr[data-doc-id="${currentDocumentId}"]`);
                if (row) {
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(-100%)';
                    setTimeout(() => row.remove(), 300);
                }
            }
        };

        // Close modals when clicking outside
        document.getElementById('viewDocumentModal').addEventListener('click', (e) => {
            if (e.target.id === 'viewDocumentModal') {
                closeViewModal();
            }
        });

        document.getElementById('deleteDocumentModal').addEventListener('click', (e) => {
            if (e.target.id === 'deleteDocumentModal') {
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>