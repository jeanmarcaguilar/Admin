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

        .category-card {
            transition: all 0.2s ease-in-out;
            border: 1px solid #e5e7eb;
            cursor: pointer;
            user-select: none;
        }

        .category-card:hover {
            border-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .category-card:active {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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

        #loadingScreen.opacity-100 { opacity: 1; }
        #loadingScreen.opacity-0 { opacity: 0; }

        /* Tab navigation */
        .tab-btn {
            transition: all 0.2s ease;
        }
        
        .tab-btn.active {
            border-bottom: 3px solid #059669;
            color: #059669;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }

        /* Rotation for dropdown arrow */
        .rotate-180 {
            transform: rotate(180deg);
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
                <p class="text-white/80 text-sm mb-4">Preparing document system...</p>
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

            <!-- Sidebar content -->
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
                        <a href="{{ route('document.upload.indexing') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                            <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                            Document Upload & Indexing
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
                            Scheduling & Calendar
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
                    <span id="real-time-clock" data-server-timestamp="{{ now()->timestamp * 1000 }}" class="text-xs font-bold text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
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
                    <!-- Page Header with Tab Navigation -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Document Management</h1>
                                <p class="text-gray-600 mt-1">Upload, index, and manage archived documents</p>
                            </div>
                            <div class="mt-4 md:mt-0 flex space-x-3">
                                <button id="exportBtn" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center">
                                    <i class="fas fa-download mr-2"></i> Export
                                </button>
                            </div>
                        </div>
                        
                        <!-- Tab Navigation -->
                        <div class="mt-6 border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8">
                                <button type="button" onclick="switchTab('upload-indexing')" id="tab-upload-indexing" class="tab-btn active py-3 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                    <i class="fas fa-upload mr-2"></i>Document Upload & Indexing
                                </button>
                                <button type="button" onclick="switchTab('archival-retention')" id="tab-archival-retention" class="tab-btn py-3 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                    <i class="fas fa-archive mr-2"></i>Archival & Retention
                                </button>
                            </nav>
                        </div>
                    </div>

                    <!-- TAB CONTENT: Document Upload & Indexing -->
                    <div id="content-upload-indexing" class="tab-content active">
                        <!-- Search and Filters -->
                        <div class="mb-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="searchInput" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-primary focus:border-brand-primary block w-full pl-12 pr-4 py-3" placeholder="Search documents...">
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                                <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                                <div class="relative flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="text-gray-600 font-semibold text-sm mb-2">Total Documents</p>
                                        <p class="font-bold text-3xl text-gray-900 mb-1" id="totalDocumentsCount">{{ count($documents) }}</p>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><i class="bx bx-file mr-1"></i>All Files</span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                        <i class="bx bx-file text-white text-xl"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                                <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                                <div class="relative flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="text-gray-600 font-semibold text-sm mb-2">Recent Uploads</p>
                                        <p class="font-bold text-3xl text-gray-900 mb-1">{{ count(array_filter($documents, function($doc) { $uploadDate = $doc['uploaded'] ?? null; return $uploadDate && strtotime($uploadDate) > strtotime('-7 days'); })) }}</p>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800"><i class="bx bx-time-five mr-1"></i>This Week</span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                        <i class="bx bx-upload text-white text-xl"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                                <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-purple-50 to-purple-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity"></div>
                                <div class="relative flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="text-gray-600 font-semibold text-sm mb-2">Archived</p>
                                        <p class="font-bold text-3xl text-gray-900 mb-1" id="archivedCount">0</p>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800"><i class="bx bx-archive mr-1"></i>In Storage</span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                        <i class="bx bx-archive text-white text-xl"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                                <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-orange
