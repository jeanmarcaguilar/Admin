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
    <title>Document Management</title>
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
            cursor: pointer;
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

        /* Loading screen progress animation */
        @keyframes progress {
            0% { width: 0%; opacity: 0.5; }
            50% { width: 80%; opacity: 1; }
            100% { width: 100%; opacity: 0.5; }
        }

        #loadingScreen {
            transition: opacity 0.3s ease-in-out;
        }

        #loadingScreen.opacity-100 {
            opacity: 1;
        }

        #loadingScreen.opacity-0 {
            opacity: 0;
        }

        .dropdown-panel {
            transform-origin: top right;
        }

        /* Permission toggle styles */
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
    </style>
</head>

<body class="bg-brand-background-main min-h-screen">

    <!-- Loading Screen -->
    <div id="loadingScreen" class="fixed inset-0 z-[9999]">
        <div class="fixed inset-0 bg-gradient-to-br from-brand-primary via-emerald-600 to-teal-600"></div>
        <div class="fixed inset-0 flex flex-col items-center justify-center p-4">
            <div class="mb-8">
                <img src="{{ asset('golden-arc.png') }}" alt="Logo" class="w-24 h-24 mx-auto rounded-full border-4 border-white/30 shadow-2xl animate-pulse">
            </div>
            <div class="mb-8">
                <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.11/dist/dotlottie-wc.js" type="module"></script>
                <dotlottie-wc src="https://lottie.host/5378ba62-7703-4273-a14a-3a999385cf7f/s5Vm9nkLqj.lottie" style="width: 300px;height: 300px" autoplay loop></dotlottie-wc>
            </div>
            <div class="text-center text-white">
                <h2 class="text-2xl font-bold mb-2">Loading Document Management</h2>
                <p class="text-white/80 text-sm mb-4">Preparing document management and archival system...</p>
                <div class="flex justify-center space-x-2">
                    <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                    <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                    <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>
            </div>
            <div class="w-64 h-1 bg-white/20 rounded-full mt-8 overflow-hidden">
                <div class="h-full bg-white rounded-full animate-pulse" style="width: 60%; animation: progress 2s ease-in-out infinite;"></div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div id="mainContent" class="opacity-0 transition-opacity duration-500">

        <!-- Overlay (mobile) -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 hidden opacity-0 transition-opacity duration-300 z-40"></div>

        <!-- SIDEBAR -->
        <aside id="sidebar" class="fixed top-0 left-0 h-full w-72 bg-white border-r border-gray-100 shadow-sm z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300">
            <div class="h-16 flex items-center px-4 border-b border-gray-100">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 w-full rounded-xl px-2 py-2 hover:bg-gray-100 active:bg-gray-200 transition group">
                    <img src="{{ asset('golden-arc.png') }}" alt="Logo" class="w-10 h-10">
                    <div class="leading-tight">
                        <div class="font-bold text-gray-800 group-hover:text-brand-primary transition-colors">Microfinance Admin</div>
                        <div class="text-[11px] text-gray-500 font-semibold uppercase group-hover:text-brand-primary transition-colors">Administrative</div>
                    </div>
                </a>
            </div>

            <div class="px-4 py-4 overflow-y-auto h-[calc(100%-4rem)] custom-scrollbar">
                <div class="text-xs font-bold text-gray-400 tracking-wider px-2">ADMINISTRATIVE DEPARTMENT</div>

                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="mt-3 flex items-center justify-between px-4 py-3 rounded-xl text-gray-700 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1 active:translate-x-0 active:scale-[0.99] font-semibold">
                    <span class="flex items-center gap-3">
                        <span class="inline-flex w-9 h-9 rounded-lg bg-emerald-50 items-center justify-center">📊</span>
                        Dashboard
                    </span>
                </a>

                <!-- Visitor Management Dropdown -->
                <button id="visitor-management-btn" class="mt-3 w-full flex items-center justify-between px-4 py-3 rounded-xl text-gray-700 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1 active:translate-x-0 active:scale-[0.99] font-semibold">
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
                <button id="document-management-btn" class="mt-3 w-full flex items-center justify-between px-4 py-3 rounded-xl text-gray-700 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1 active:translate-x-0 active:scale-[0.99] font-semibold">
                    <span class="flex items-center gap-3">
                        <span class="inline-flex w-9 h-9 rounded-lg bg-emerald-50 items-center justify-center">📄</span>
                        Document Management
                    </span>
                    <svg id="document-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="document-submenu" class="submenu mt-1">
                    <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                        <a href="{{ route('document.management.combined') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                            Document Management
                        </a>
                        <a href="{{ route('document.access.control.permissions') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                            <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                            Access Control & Permissions
                        </a>
                    </div>
                </div>

                <!-- Facilities Management Dropdown -->
                <button id="facilities-management-btn" class="mt-3 w-full flex items-center justify-between px-4 py-3 rounded-xl text-gray-700 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1 active:translate-x-0 active:scale-[0.99] font-semibold">
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
                <button id="legal-management-btn" class="mt-3 w-full flex items-center justify-between px-4 py-3 rounded-xl text-gray-700 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1 active:translate-x-0 active:scale-[0.99] font-semibold">
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
            <header class="h-16 bg-white flex items-center justify-between px-4 sm:px-6 relative shadow-[0_2px_8px_rgba(0,0,0,0.06)]">
                <div class="hidden md:block absolute left-0 top-0 h-16 w-[2px] bg-white"></div>

                <div class="flex items-center gap-3">
                    <button id="mobile-menu-btn" class="md:hidden w-10 h-10 rounded-xl hover:bg-gray-100 active:bg-gray-200 transition flex items-center justify-center">☰</button>
                </div>

                <div class="flex items-center gap-3 sm:gap-5">
                    <span id="real-time-clock" class="text-xs font-bold text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                        {{ now()->format('g:i:s A') }}
                    </span>

                    <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>

                    <!-- User Profile Dropdown -->
                    <div class="relative">
                        <button id="userMenuBtn" class="flex items-center gap-3 focus:outline-none group rounded-xl px-2 py-2 hover:bg-gray-100 active:bg-gray-200 transition">
                            <div class="w-10 h-10 rounded-full bg-white shadow group-hover:shadow-md transition-shadow overflow-hidden flex items-center justify-center border border-gray-100">
                                <div class="w-full h-full flex items-center justify-center font-bold text-brand-primary bg-emerald-50">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="hidden md:flex flex-col items-start text-left">
                                <span class="text-sm font-bold text-gray-700 group-hover:text-brand-primary transition-colors">{{ $user->name }}</span>
                                <span class="text-[10px] text-gray-500 font-medium uppercase group-hover:text-brand-primary transition-colors">{{ ucfirst($user->role) }}</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div id="userMenuDropdown" class="dropdown-panel hidden absolute right-0 mt-3 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50">
                            <div class="py-4 px-6 border-b border-gray-100 text-center">
                                <div class="w-14 h-14 rounded-full bg-[#28644c] text-white mx-auto flex items-center justify-center mb-2">
                                    <i class="fas fa-user-circle text-3xl"></i>
                                </div>
                                <p class="font-semibold text-[#28644c]">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400">{{ ucfirst($user->role) }}</p>
                            </div>
                            <ul class="text-sm text-gray-700">
                                <li><button id="openProfileBtn" class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none"><i class="fas fa-user-circle mr-2"></i> My Profile</button></li>
                                <li><button id="openSignOutBtn" class="w-full text-left flex items-center px-6 py-2 text-red-600 hover:bg-gray-100 focus:outline-none"><i class="fas fa-sign-out-alt mr-2"></i> Sign Out</button></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>

            <!-- MAIN CONTENT -->
            <main class="p-4 sm:p-6">
                <div class="max-w-7xl mx-auto">
                    
                    <!-- Archive Policy Notes Section -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">
                                <i class="bx bx-info-circle text-purple-600 mr-2"></i>
                                Archive Policy Notes
                            </h2>
                            <span class="text-xs text-gray-500">Category-specific retention guidelines</span>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
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

                    <!-- Page Header -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Document Management</h1>
                                <p class="text-gray-600 mt-1">Upload, organize, index, and manage documents with archival retention</p>
                            </div>
                            <div class="mt-4 md:mt-0 flex space-x-3">
                                <button id="uploadDocumentsBtn" class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-hover transition-colors font-medium flex items-center">
                                    <i class="fas fa-cloud-upload-alt mr-2"></i> Upload Documents
                                </button>
                                <button id="exportBtn" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
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
                                class="
                        <div class="flex justify-center gap-3 pt-4 border-t border-gray-100">
                            <button type="button" id="cancelUploadBtn" class="group px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded hover:border-gray-300 hover:bg-gray-50 transition-all duration-200 flex items-center">
