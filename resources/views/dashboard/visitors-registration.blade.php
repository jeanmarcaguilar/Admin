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
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
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

        .table-row {
            transition: all 0.2s ease-in-out;
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
    </style>
</head>

<body class="bg-brand-background-main min-h-screen">

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
                <svg id="visitor-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300 rotate-180"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="visitor-submenu" class="submenu mt-1">
                <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                    <a href="{{ route('visitors.registration') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 bg-green-50 text-brand-primary font-medium transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 57 7-7 7">
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

            <div class="mt-8 px-2">
                <div class="flex items-center gap-2 text-xs font-bold text-emerald-600">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    SYSTEM ONLINE
                </div>
                <div class="text-[11px] text-gray-400 mt-2 leading-snug">
                    Microfinance Admin © {{ date('Y') }}<br />
                    Administrative
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
                <span id="real-time-clock" data-server-timestamp="{{ now()->timestamp * 1000 }}"
                    class="text-xs font-bold text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                    {{ now()->format('g:i:s A') }}
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

                    <div id="user-menu-dropdown" class="dropdown-panel hidden opacity-0 translate-y-2 scale-95 pointer-events-none
                            absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-lg border border-gray-100
                            transition-all duration-200 z-50">
                        <button id="openProfileBtn"
                            class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">Profile</button>
                        <button id="openAccountSettingsBtn"
                            class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">Settings</button>
                        <div class="h-px bg-gray-100"></div>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition">Logout</button>
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
                            <h1 class="text-2xl font-bold text-gray-900">Visitor Management</h1>
                            <p class="text-gray-600 mt-1">Register, track, and manage visitor activity</p>
                        </div>
                        <div class="mt-4 md:mt-0 flex gap-3">
                            <button id="scanQRBtn" type="button" onclick="openQRScannerDirect()"
                                class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg px-4 py-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <i class="fas fa-qrcode mr-2"></i> Scan QR Code
                            </button>
                            <script>
                                // Direct function for Scan QR button
                                function openQRScannerDirect() {
                                    console.log('Scan QR button clicked directly!');
                                    const modal = document.getElementById('qrScannerModal');
                                    if (modal) {
                                        // Use the proper modal opening method for centering
                                        modal.classList.add('active');
                                        modal.style.display = 'flex';
                                        document.body.style.overflow = 'hidden';
                                        console.log('QR Scanner modal opened successfully');

                                        // Initialize scanner interface
                                        const instructions = document.getElementById("scannerInstructions");
                                        if (instructions) {
                                            instructions.innerHTML = 'Click "Start Scan" to begin scanning QR codes';
                                        }
                                    } else {
                                        console.error('QR Scanner modal not found');
                                        alert('QR Scanner is not available. Please refresh the page.');
                                    }
                                }
                            </script>
                            <button id="addVisitorBtn"
                                class="inline-flex items-center bg-brand-primary hover:bg-brand-primary-hover text-white font-medium rounded-lg px-4 py-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-primary">
                                <i class="fas fa-qrcode mr-2"></i> Register Visitor with QR
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search -->
                <div class="mb-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="relative flex-1 max-w-3xl">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="searchVisitors"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-primary focus:border-brand-primary block w-full pl-10 p-2.5"
                                placeholder="Search visitors, companies, hosts...">
                        </div>
                    </div>
                </div>

                <!-- Enhanced Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Today Card -->
                    <div
                        class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div
                            class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                        </div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Total Today</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['total_today'] ?? 0 }}</p>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-calendar-day mr-1"></i>
                                        Daily
                                    </span>
                                    <span class="text-xs text-gray-500">Visitors</span>
                                </div>
                            </div>
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-calendar-day text-white text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Checked In Card -->
                    <div
                        class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div
                            class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                        </div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Checked In</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['checked_in'] ?? 0 }}</p>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <i class="fas fa-user-check mr-1"></i>
                                        Active
                                    </span>
                                    <span class="text-xs text-gray-500">Currently</span>
                                </div>
                            </div>
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-user-check text-white text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Scheduled Today Card -->
                    <div
                        class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div
                            class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-amber-50 to-amber-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                        </div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Scheduled Today</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['scheduled_today'] ?? 0 }}
                                </p>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pending
                                    </span>
                                    <span class="text-xs text-gray-500">Expected</span>
                                </div>
                            </div>
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-clock text-white text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Checked Out Card -->
                    <div
                        class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div
                            class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-gray-50 to-gray-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                        </div>
                        <div class="relative flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-gray-600 font-semibold text-sm mb-2">Checked Out</p>
                                <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['checked_out'] ?? 0 }}</p>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-door-open mr-1"></i>
                                        Completed
                                    </span>
                                    <span class="text-xs text-gray-500">Departed</span>
                                </div>
                            </div>
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-door-open text-white text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Table with Pagination -->
                <section class="grid grid-cols-1 gap-6">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-100">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <button
                                        class="text-sm font-medium text-brand-primary px-4 py-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 transition-colors border border-emerald-200">Today</button>
                                    <button
                                        class="text-sm text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors border border-gray-200">Scheduled</button>
                                    <button
                                        class="text-sm text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors border border-gray-200">All</button>
                                </div>
                                <div id="resultsCount" class="text-xs text-gray-500 bg-gray-50 px-3 py-1 rounded-full">0
                                    results</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <select
                                    class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <option>10 per page</option>
                                    <option>25 per page</option>
                                    <option>50 per page</option>
                                    <option>100 per page</option>
                                </select>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Visitor</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Type</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Company</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Host</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Time</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            QR Code</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="visitorsTbody" class="bg-white divide-y divide-gray-200">
                                    @forelse(($visitors ?? []) as $v)
                                        <tr class="table-row">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $v['name'] ?? 'Visitor' }}
                                                </div>
                                                <div class="text-xs text-gray-500">ID: {{ $v['id'] ?? '' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $visitorType = $v['visitor_type'] ?? '';
                                                    $typeClasses = [
                                                        'personal' => 'bg-purple-100 text-purple-800',
                                                        'professional' => 'bg-blue-100 text-blue-800',
                                                        'business_partner' => 'bg-green-100 text-green-800',
                                                        'vip' => 'bg-violet-100 text-violet-800',
                                                        'regular' => 'bg-gray-100 text-gray-800'
                                                    ];
                                                    $typeLabel = ucfirst(str_replace('_', ' ', $visitorType));
                                                @endphp
                                                <span
                                                    class="px-2 py-1 text-xs font-medium rounded-full inline-flex items-center {{ $typeClasses[$visitorType] ?? 'bg-gray-100 text-gray-800' }}">
                                                    @if($visitorType === 'vip')
                                                        <i class="fas fa-crown text-yellow-500 mr-1 text-xs"></i>
                                                    @endif
                                                    {{ $typeLabel }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $v['company'] ?? '—' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $v['host'] ?? '—' }}</div>
                                                <div class="text-xs text-gray-500">{{ $v['host_department'] ?? '' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $__rawTime = $v['check_in_time'] ?? '';
                                                    $__fmtTime = $__rawTime;
                                                    if ($__rawTime) {
                                                        try {
                                                            $__fmtTime = \Carbon\Carbon::createFromFormat('H:i', $__rawTime)->format('g:i A');
                                                        } catch (\Exception $e) {
                                                            try {
                                                                $__fmtTime = \Carbon\Carbon::createFromFormat('H:i:s', $__rawTime)->format('g:i A');
                                                            } catch (\Exception $e2) { /* leave as-is */
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <div class="text-sm text-gray-900">{{ $__fmtTime }}</div>
                                                <div class="text-xs text-gray-500">{{ $v['check_in_date'] ?? '' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="qr-code-cell" data-visitor-id="{{ $v['id'] ?? '' }}">
                                                    @if(isset($v['qr_code']) && !empty($v['qr_code']))
                                                        <div class="inline-block">
                                                            <img src="data:image/png;base64,{{ $v['qr_code'] }}" alt="QR Code"
                                                                class="w-12 h-12 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow cursor-pointer"
                                                                onclick="showQRModal('{{ $v['id'] }}', '{{ $v['name'] ?? 'Visitor' }}', '{{ $v['qr_code'] }}')">
                                                        </div>
                                                    @else
                                                        <div
                                                            class="inline-block w-12 h-12 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center qr-placeholder">
                                                            <i class="fas fa-qrcode text-gray-400 text-lg"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php $st = strtolower($v['status'] ?? 'scheduled'); @endphp
                                                <span
                                                    class="px-2 py-1 text-xs font-medium rounded-full inline-flex items-center {{ $st === 'checked_in' ? 'bg-green-100 text-green-800' : ($st === 'checked_out' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                    {{ ucfirst($st) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="#"
                                                    class="visitorViewBtn text-brand-primary hover:text-brand-primary-hover mr-3"
                                                    data-id="{{ $v['id'] ?? '' }}" data-tooltip="View"><i
                                                        class="fas fa-eye"></i></a>
                                                <a href="#" class="visitorEditBtn text-blue-600 hover:text-blue-900 mr-3"
                                                    data-id="{{ $v['id'] ?? '' }}" data-tooltip="Edit"><i
                                                        class="fas fa-edit"></i></a>
                                                @if($st !== 'checked_out' && $st !== 'checked_in')
                                                    <a href="#"
                                                        class="visitorCheckInBtn text-green-600 hover:text-green-800 mr-3"
                                                        data-id="{{ $v['id'] ?? '' }}" data-tooltip="Check In"><i
                                                            class="fas fa-sign-in-alt"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500">
                                                No visitors registered yet.
                                                <div class="mt-3">
                                                    <button type="button"
                                                        onclick="document.getElementById('addVisitorBtn').click()"
                                                        class="inline-flex items-center bg-brand-primary hover:bg-brand-primary-hover text-white text-xs font-medium rounded-lg px-3 py-2 shadow-sm">
                                                        <i class="fas fa-user-plus mr-2"></i> Register your first visitor
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div
                            class="flex flex-col sm:flex-row items-center justify-between gap-4 px-6 py-4 border-t border-gray-100 bg-gray-50">
                            <div class="text-sm text-gray-700">
                                Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of
                                <span class="font-medium">{{ count($visitors ?? []) }}</span> results
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    class="px-3 py-1 text-sm text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button
                                    class="px-3 py-1 text-sm text-white bg-emerald-600 border border-emerald-600 rounded-lg hover:bg-emerald-700">1</button>
                                <button
                                    class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">2</button>
                                <button
                                    class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">3</button>
                                <span class="px-2 text-sm text-gray-500">...</span>
                                <button
                                    class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">10</button>
                                <button
                                    class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Modals -->

    <!-- View Visitor Modal -->
    <div id="viewVisitorModal" class="modal hidden" aria-modal="true" role="dialog">
        <div class="bg-white rounded-lg shadow-lg w-[420px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
                <h3 class="font-semibold text-sm text-gray-900">View Visitor</h3>
                <button id="closeViewVisitor" type="button"
                    class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200"
                    aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 pt-5 pb-6">
                <div class="space-y-3 text-sm text-gray-800">
                    <div class="grid grid-cols-2 gap-2">
                        <span class="font-medium text-gray-700">ID:</span>
                        <span id="vvId" class="text-gray-900"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <span class="font-medium text-gray-700">Name:</span>
                        <span id="vvName" class="text-gray-900"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <span class="font-medium text-gray-700">Company:</span>
                        <span id="vvCompany" class="text-gray-900"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <span class="font-medium text-gray-700">Type:</span>
                        <span id="vvType" class="text-gray-900"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <span class="font-medium text-gray-700">Host:</span>
                        <span id="vvHost" class="text-gray-900"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <span class="font-medium text-gray-700">Department:</span>
                        <span id="vvDept" class="text-gray-900"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <span class="font-medium text-gray-700">Date:</span>
                        <span id="vvDate" class="text-gray-900"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <span class="font-medium text-gray-700">Time:</span>
                        <span id="vvTime" class="text-gray-900"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <span class="font-medium text-gray-700">Status:</span>
                        <span id="vvStatus" class="text-gray-900"></span>
                    </div>
                </div>
                <div class="flex justify-end pt-4">
                    <button type="button" id="closeViewVisitor2"
                        class="px-4 py-2 text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover rounded-lg transition-colors">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Visitor Modal -->
    <div id="editVisitorModal" class="modal hidden" aria-modal="true" role="dialog">
        <div class="bg-white rounded-lg shadow-lg w-[460px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
                <h3 class="font-semibold text-sm text-gray-900">Edit Visitor</h3>
                <button id="closeEditVisitor" type="button"
                    class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200"
                    aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 pt-5 pb-6">
                <form id="editVisitorForm" class="space-y-4 text-sm text-gray-700">
                    <input type="hidden" id="evId" />
                    <div>
                        <label class="block mb-1 font-medium text-xs">Company</label>
                        <input type="text" id="evCompany"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block mb-1 font-medium text-xs">Type</label>
                            <select id="evType"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                                <option value="">—</option>
                                <option value="client">Client</option>
                                <option value="vendor">Vendor</option>
                                <option value="contractor">Contractor</option>
                                <option value="guest">Guest</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-xs">Purpose</label>
                            <select id="evPurpose"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                                <option value="">—</option>
                                <option value="meeting">Meeting</option>
                                <option value="delivery">Delivery</option>
                                <option value="interview">Interview</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block mb-1 font-medium text-xs">Date</label>
                            <input type="date" id="evDate"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-xs">Time</label>
                            <input type="time" id="evTime"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-xs">Status</label>
                        <select id="evStatus"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent text-sm">
                            <option value="scheduled">Scheduled</option>
                            <option value="checked_in">Checked In</option>
                            <option value="checked_out">Checked Out</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3 pt-2">
                        <button type="button" id="cancelEditVisitor"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover rounded-lg transition-colors">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Check In Modal -->
    <div id="checkInModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="check-in-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-[420px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
                <h3 id="check-in-modal-title" class="font-semibold text-sm text-gray-900">Check In Visitor</h3>
                <button id="closeCheckIn" type="button"
                    class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200"
                    aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 pt-5 pb-6">
                <p class="text-sm text-gray-700 mb-3">Are you sure you want to check in visitor <span id="ciText"
                        class="font-semibold text-gray-900"></span> now?</p>
                <input type="hidden" id="ciId" />
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelCheckIn"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button type="button" id="confirmCheckIn"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">Check
                        In</button>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Scanner Modal -->
    <div id="qrScannerModal" class="modal" aria-modal="true" role="dialog">
        <div class="bg-white rounded-lg shadow-lg w-[480px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
                <h3 class="font-semibold text-sm text-gray-900">Scan QR Code</h3>
                <button id="closeQRScanner" type="button"
                    class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 py-5">
                <div id="reader" class="rounded-xl overflow-hidden bg-gray-100 mb-4"
                    style="width: 100%; min-height: 250px;"></div>
                <div id="scannerInstructions" class="text-center text-sm text-gray-500 mb-4">
                    Click "Start Scan" to begin scanning QR codes
                </div>
                <div id="scannerStatus" class="text-center text-xs text-emerald-600 font-medium mb-4 hidden">
                    <i class="fas fa-circle text-emerald-500 animate-pulse mr-1"></i>
                    Scanner Active - Position QR code in frame
                </div>
                <div class="flex justify-center gap-3">
                    <button type="button" id="startScan" onclick="startScannerDirect()"
                        class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
                        <i class="fas fa-camera mr-2"></i>Start Scan
                    </button>
                    <button type="button" id="testQR" onclick="testQRDirect()"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                        <i class="fas fa-qrcode mr-2"></i>Test QR
                    </button>
                    <button type="button" id="manualScan" onclick="manualScanDirect()"
                        class="px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition-colors">
                        <i class="fas fa-hand-pointer mr-2"></i>Manual Scan
                    </button>
                    <button type="button" id="stopScanner" onclick="stopScannerDirect()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Stop
                        Scanner</button>
                </div>

                <!-- Hidden file input for QR upload -->
                <input type="file" id="qrFileInput" accept="image/*" style="display: none;">
            </div>
        </div>
    </div>

    <!-- Direct Modal Button Functions -->
    <script>
        // Direct functions for modal buttons
        function startScannerDirect() {
            console.log('Start Scan button clicked directly!');
            document.getElementById("scannerInstructions").innerHTML = '📷 Starting camera...';
            document.getElementById("scannerStatus").classList.remove("hidden");

            // Check camera permissions first
            navigator.mediaDevices.getUserMedia({
                video: { facingMode: "environment" }
            })
                .then(stream => {
                    // If we get here, camera permission is granted
                    stream.getTracks().forEach(track => track.stop());

                    document.getElementById("scannerInstructions").innerHTML =
                        '📷 Camera access granted! Starting scanner...';

                    // Start the scanner with enhanced configuration
                    startEnhancedScanner();
                })
                .catch(error => {
                    console.log("Camera permission check failed:", error);
                    if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                        showCameraPermissionError();
                    } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
                        showCameraNotFoundError();
                    } else if (error.name === 'NotReadableError' || error.name === 'TrackStartError') {
                        showCameraInUseError();
                    } else {
                        showCameraPermissionError();
                    }
                });
        }

        function uploadQRDirect() {
            console.log('Upload QR button clicked directly!');
            document.getElementById("qrFileInput").click();
        }

        // File upload handler will be initialized in main DOMContentLoaded

        // Process QR upload result
        function processQRUploadResult(result) {
            console.log("QR Code successfully decoded from image:", result);

            // Close modal
            const modal = document.getElementById('qrScannerModal');
            if (modal) {
                modal.classList.remove('active');
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }

            // Try to parse as JSON first
            try {
                const qrData = JSON.parse(result);
                console.log("Parsed QR data:", qrData);
                if (typeof verifyScannedVisitor === 'function') {
                    verifyScannedVisitor(qrData.id || result);
                }
            } catch (e) {
                console.log("Failed to parse as JSON, using raw text:", result);
                if (typeof verifyScannedVisitor === 'function') {
                    verifyScannedVisitor(result);
                }
            }
        }

        // Enhanced scanner function
        function startEnhancedScanner() {
            if (typeof Html5Qrcode !== 'undefined') {
                let html5QrcodeScanner = new Html5Qrcode("reader");
                let scannerStarted = false;
                let lastError = null;

                // Enhanced scanner configuration
                const config = {
                    fps: 60,
                    qrbox: { width: 400, height: 400 },
                    aspectRatio: 1.0,
                    disableFlip: false,
                    formatsToSupport: [
                        Html5QrcodeSupportedFormats.QR_CODE,
                        Html5QrcodeSupportedFormats.AZTEC,
                        Html5QrcodeSupportedFormats.DATA_MATRIX,
                        Html5QrcodeSupportedFormats.PDF_417
                    ],
                    debug: true,
                    verbose: true,
                    experimentalFeatures: {
                        useBarCodeDetectorIfSupported: true
                    }
                };

                // Try different camera configurations
                const cameraConfigs = [
                    { facingMode: "environment" },
                    { facingMode: "user" },
                    { facingMode: { exact: "environment" } },
                    { facingMode: { exact: "user" } },
                    undefined
                ];

                const tryStartScanner = async (configIndex = 0) => {
                    if (configIndex >= cameraConfigs.length) {
                        throw new Error(lastError || "All camera configurations failed");
                    }

                    try {
                        await html5QrcodeScanner.start(
                            cameraConfigs[configIndex],
                            config,
                            (decodedText) => {
                                console.log("🎯 QR Code detected!", decodedText);

                                // Visual feedback
                                const readerElement = document.getElementById("reader");
                                if (readerElement) {
                                    readerElement.style.border = "3px solid #10b981";
                                    setTimeout(() => {
                                        readerElement.style.border = "";
                                    }, 200);
                                }

                                // Update status
                                document.getElementById("scannerInstructions").innerHTML =
                                    '✅ QR Code detected! Processing...';

                                // Stop scanner and process
                                setTimeout(() => {
                                    html5QrcodeScanner.stop();
                                    const modal = document.getElementById('qrScannerModal');
                                    if (modal) {
                                        modal.classList.remove('active');
                                        modal.style.display = 'none';
                                        document.body.style.overflow = 'auto';
                                    }

                                    // Process the QR code
                                    try {
                                        const qrData = JSON.parse(decodedText);
                                        console.log("Parsed QR data:", qrData);
                                        if (typeof verifyScannedVisitor === 'function') {
                                            verifyScannedVisitor(qrData.id || decodedText);
                                        }
                                    } catch (e) {
                                        console.log("Failed to parse as JSON, using raw text:", decodedText);
                                        if (typeof verifyScannedVisitor === 'function') {
                                            verifyScannedVisitor(decodedText);
                                        }
                                    }
                                }, 500);
                            },
                            (errorMessage) => {
                                // Handle scan errors silently
                            }
                        );
                        scannerStarted = true;
                        console.log(`Scanner started successfully with camera configuration ${configIndex + 1}`);
                        document.getElementById("scannerInstructions").innerHTML =
                            '📷 Scanner Active - Position QR code in frame';
                    } catch (cameraError) {
                        console.log(`Camera configuration ${configIndex + 1} failed:`, cameraError);
                        lastError = cameraError;

                        try {
                            await html5QrcodeScanner.stop();
                            html5QrcodeScanner.clear();
                        } catch (e) {
                            // Ignore cleanup errors
                        }

                        html5QrcodeScanner = new Html5Qrcode("reader");
                        return tryStartScanner(configIndex + 1);
                    }
                };

                tryStartScanner();
            } else {
                console.error('Html5Qrcode library not available');
                document.getElementById("scannerInstructions").innerHTML =
                    '❌ Scanner library not available. Try Upload QR or Manual Scan.';
            }
        }

        // Camera error functions
        function showCameraPermissionError() {
            document.getElementById("reader").innerHTML = `
                <div class="text-center p-6">
                    <div class="mb-4">
                        <i class="fas fa-camera-slash text-4xl text-red-500"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Camera Permission Required</h3>
                    <p class="text-sm text-gray-600 mb-4">Please allow camera access to scan QR codes</p>
                    <div class="text-left bg-gray-50 p-3 rounded-lg text-xs text-gray-700">
                        <p class="font-semibold mb-2">To fix this:</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Click the camera icon in your browser's address bar</li>
                            <li>Select "Allow" for camera access</li>
                            <li>Refresh the page and try again</li>
                        </ol>
                    </div>
                    <div class="mt-4">
                        <button onclick="testQRDirect()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-qrcode mr-2"></i>Try Test QR
                        </button>
                    </div>
                </div>
            `;
            document.getElementById("scannerStatus").classList.add("hidden");
        }

        function showCameraNotFoundError() {
            document.getElementById("reader").innerHTML = `
                <div class="text-center p-6">
                    <div class="mb-4">
                        <i class="fas fa-video-slash text-4xl text-orange-500"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Camera Found</h3>
                    <p class="text-sm text-gray-600 mb-4">Please connect a camera to use QR scanning</p>
                    <div class="text-left bg-gray-50 p-3 rounded-lg text-xs text-gray-700">
                        <p class="font-semibold mb-2">To fix this:</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Connect a webcam to your device</li>
                            <li>Ensure the camera is properly installed</li>
                            <li>Try using a different browser</li>
                        </ol>
                    </div>
                    <div class="mt-4">
                        <button onclick="uploadQRDirect()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            <i class="fas fa-upload mr-2"></i>Upload QR Image
                        </button>
                    </div>
                </div>
            `;
            document.getElementById("scannerStatus").classList.add("hidden");
        }

        function showCameraInUseError() {
            document.getElementById("reader").innerHTML = `
                <div class="text-center p-6">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle text-4xl text-yellow-500"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Camera In Use</h3>
                    <p class="text-sm text-gray-600 mb-4">Camera is being used by another application</p>
                    <div class="text-left bg-gray-50 p-3 rounded-lg text-xs text-gray-700">
                        <p class="font-semibold mb-2">To fix this:</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Close other apps using the camera</li>
                            <li>Refresh the page and try again</li>
                            <li>Restart your browser if needed</li>
                        </ol>
                    </div>
                    <div class="mt-4">
                        <button onclick="manualScanDirect()" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                            <i class="fas fa-hand-pointer mr-2"></i>Manual Scan
                        </button>
                    </div>
                </div>
            `;
            document.getElementById("scannerStatus").classList.add("hidden");
        }

        function testQRDirect() {
            console.log('Test QR button clicked directly!');

            try {
                const readerElement = document.getElementById("reader");
                if (readerElement) {
                    readerElement.innerHTML = '';

                    // Create enhanced test QR data
                    const testQRData = JSON.stringify({
                        id: 'TEST-' + Date.now(),
                        name: 'Test Visitor',
                        access: 'STANDARD',
                        date: new Date().toISOString().split('T')[0],
                        time: new Date().toISOString(),
                        type: "VISITOR_QR",
                        version: "2.0",
                        hash: btoa("TEST-" + Date.now() + new Date().toISOString()).substring(0, 16)
                    });

                    console.log('Generating test QR with data:', testQRData);

                    new QRCode(readerElement, {
                        text: testQRData,
                        width: 300,
                        height: 300,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H,
                        margin: 2
                    });

                    // Update instructions with success message
                    document.getElementById("scannerInstructions").innerHTML =
                        '🧪 <strong>Test QR Code Generated</strong><br><small>Point your camera at this QR code to test the scanner</small>';
                    document.getElementById("scannerStatus").classList.add("hidden");

                    console.log('Test QR code generated successfully');
                }
            } catch (error) {
                console.error('Error generating test QR:', error);
                document.getElementById("scannerInstructions").innerHTML =
                    '❌ Error generating test QR code. Please try again.';
            }
        }

        function manualScanDirect() {
            console.log('Manual Scan button clicked directly!');

            // Prompt user to enter QR code manually
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Manual QR Code Entry',
                    input: 'text',
                    inputLabel: 'Enter Visitor ID or QR Code Data',
                    inputPlaceholder: 'e.g., VIS-12345 or QR data',
                    showCancelButton: true,
                    confirmButtonText: 'Verify',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#059669'
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        console.log("Manual QR entry:", result.value);

                        // Close modal and process
                        const modal = document.getElementById('qrScannerModal');
                        if (modal) {
                            modal.classList.remove('active');
                            modal.style.display = 'none';
                            document.body.style.overflow = 'auto';
                        }

                        // Try to parse as JSON first
                        try {
                            const qrData = JSON.parse(result.value);
                            console.log("Parsed QR data:", qrData);
                            if (typeof verifyScannedVisitor === 'function') {
                                verifyScannedVisitor(qrData.id || result.value);
                            }
                        } catch (e) {
                            console.log("Failed to parse as JSON, using raw text:", result.value);
                            if (typeof verifyScannedVisitor === 'function') {
                                verifyScannedVisitor(result.value);
                            }
                        }
                    }
                });
            } else {
                // Fallback if Swal not available
                const visitorId = prompt('Enter Visitor ID or QR Code Data:');
                if (visitorId) {
                    console.log("Manual QR entry:", visitorId);

                    // Close modal
                    const modal = document.getElementById('qrScannerModal');
                    if (modal) {
                        modal.classList.remove('active');
                        modal.style.display = 'none';
                        document.body.style.overflow = 'auto';
                    }

                    if (typeof verifyScannedVisitor === 'function') {
                        verifyScannedVisitor(visitorId);
                    }
                }
            }
        }

        function stopScannerDirect() {
            console.log('Stop Scanner button clicked directly!');
            document.getElementById("scannerStatus").classList.add("hidden");

            // Try to stop the scanner
            if (typeof stopScanner === 'function') {
                stopScanner();
            }

            // Close modal
            const modal = document.getElementById('qrScannerModal');
            if (modal) {
                modal.classList.remove('active');
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }
    </script>

    <!-- QR Result/Generation Modal -->
    <div id="qrResultModal" class="modal" aria-modal="true" role="dialog">
        <div class="bg-white rounded-lg shadow-lg w-[400px] max-w-full mx-4 fade-in" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
                <h3 id="qrModalTitle" class="font-semibold text-sm text-gray-900">Visitor QR Code</h3>
                <button id="closeQRResult" type="button"
                    class="text-gray-400 hover:text-gray-600 rounded-lg p-2 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 py-8 text-center">
                <div id="qrStatusIcon" class="mb-4 flex justify-center"></div>
                <h4 id="qrResultText" class="text-xl font-bold mb-6"></h4>

                <div id="qrcode"
                    class="flex justify-center mb-6 bg-white p-4 rounded-xl shadow-inner inline-block mx-auto border border-gray-100">
                </div>

                <div id="qrVisitorInfo" class="space-y-2 text-sm text-gray-600 mb-8 pb-6 border-b border-gray-100">
                    <p><strong>Name:</strong> <span id="qrName"></span></p>
                    <p><strong>ID:</strong> <span id="qrId"></span></p>
                    <p><strong>Status:</strong> <span id="qrStatus"></span></p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" id="printQR"
                        class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button type="button" id="downloadQR"
                        class="px-4 py-2.5 text-sm font-medium text-white bg-brand-primary hover:bg-brand-primary-hover rounded-lg transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-download"></i> Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            console.log('Page loaded - initializing all functionality...');
            
            // Sidebar functionality
            const sidebar = document.getElementById("sidebar");
            const mobileMenuBtn = document.getElementById("mobile-menu-btn");
            const sidebarOverlay = document.getElementById("sidebar-overlay");

            // Mobile sidebar toggle
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener("click", () => {
                    sidebar.classList.remove("-translate-x-full");
                    sidebarOverlay.classList.remove("hidden", "opacity-0");
                    sidebarOverlay.classList.add("opacity-100");
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener("click", () => {
                    sidebar.classList.add("-translate-x-full");
                    sidebarOverlay.classList.remove("opacity-100");
                    sidebarOverlay.classList.add("opacity-0");
                    setTimeout(() => sidebarOverlay.classList.add("hidden"), 300);
                });
            }

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
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Dropdown clicked:', btnId);
                        
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
                            console.log('Dropdown opened:', submenuId);
                        } else {
                            submenu.classList.add("hidden");
                            if (arrow) arrow.classList.remove("rotate-180");
                            console.log('Dropdown closed:', submenuId);
                        }
                    });
                } else {
                    console.warn('Dropdown elements not found:', btnId, submenuId);
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

            // Real-time clock with high-precision server synchronization
            const clockElement = document.getElementById('real-time-clock');
            let serverTimeOffset = 0; // Offset in milliseconds between server and client
            let isInitialized = false;
            let clockInterval = null;

            // Initialize with precise server timestamp from data attribute
            if (clockElement) {
                const serverTimestamp = parseInt(clockElement.getAttribute('data-server-timestamp'));
                if (serverTimestamp && !isNaN(serverTimestamp)) {
                    // Calculate initial offset
                    const clientTimestamp = Date.now();
                    serverTimeOffset = serverTimestamp - clientTimestamp;
                    isInitialized = true;

                    console.log('Initial clock sync - Server offset:', serverTimeOffset, 'ms');
                }
            }

            function updateClock() {
                if (!clockElement) return;

                try {
                    // Get current time with server offset applied
                    const now = new Date(Date.now() + serverTimeOffset);

                    // Format time in 12-hour format with AM/PM
                    let hours = now.getHours();
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    const ampm = hours >= 12 ? 'PM' : 'AM';

                    // Convert to 12-hour format
                    hours = hours % 12;
                    hours = hours ? hours : 12; // the hour '0' should be '12'
                    const displayHours = String(hours).padStart(2, '0');

                    const timeString = `${displayHours}:${minutes}:${seconds} ${ampm}`;

                    clockElement.textContent = timeString;
                } catch (error) {
                    console.error('Error updating clock:', error);
                }
            }

            // Sync with server time with network latency compensation
            async function syncServerTime() {
                try {
                    const requestStart = Date.now();
                    const response = await fetch("{{ route('api.server-time') }}", {
                        method: 'GET',
                        headers: {
                            'Cache-Control': 'no-cache'
                        }
                    });

                    if (response.ok) {
                        const requestEnd = Date.now();
                        const data = await response.json();

                        // Estimate network latency (round-trip time / 2)
                        const networkLatency = (requestEnd - requestStart) / 2;

                        // Server timestamp adjusted for network latency
                        const serverTimestamp = data.timestamp + networkLatency;
                        const clientTimestamp = Date.now();

                        // Update offset with latency compensation
                        serverTimeOffset = serverTimestamp - clientTimestamp;

                        console.log('Clock synced with server. Offset:', serverTimeOffset.toFixed(0), 'ms | Latency:', networkLatency.toFixed(0), 'ms');
                    }
                } catch (error) {
                    console.warn('Failed to sync with server time:', error);
                }
            }

            // Clear any existing clock interval
            if (clockInterval) {
                clearInterval(clockInterval);
            }
            
            // Update clock immediately
            updateClock();

            // Use setInterval for consistent 1-second updates
            clockInterval = setInterval(updateClock, 1000);

            // Perform initial sync after page load to refine accuracy
            if (isInitialized) {
                // Wait a moment for page to fully load, then sync
                setTimeout(() => {
                    syncServerTime();
                }, 1000);
            }

            // Sync with server every 5 minutes to prevent drift
            setInterval(syncServerTime, 5 * 60 * 1000);

            // Open "Visitor Management" dropdown by default since we're on Visitors Registration page
            const visitorBtn = document.getElementById('visitor-management-btn');
            const visitorSubmenu = document.getElementById('visitor-submenu');
            const visitorArrow = document.getElementById('visitor-arrow');

            if (visitorSubmenu && visitorBtn) {
                visitorSubmenu.classList.remove('hidden');
                if (visitorArrow) visitorArrow.classList.add('rotate-180');
                console.log('Visitor Management dropdown opened by default');
            }

            // Initialize visitors data and search
            if (typeof initializeVisitorsData === 'function') {
                initializeVisitorsData();
            }
            if (typeof applySearchFilter === 'function') {
                applySearchFilter('');
            }

            // Load QR codes from localStorage after delay
            setTimeout(() => {
                if (typeof loadStoredQRCodes === 'function') {
                    loadStoredQRCodes();
                }
            }, 1000);

            // Setup addVisitorBtn if it exists
            const addVisitorBtn = document.getElementById('addVisitorBtn');
            if (addVisitorBtn) {
                // Remove any existing listener to prevent duplicates
                addVisitorBtn.removeEventListener('click', window.openAddVisitorModal);
                // Add the correct listener
                addVisitorBtn.addEventListener('click', () => {
                    if (typeof openQRScannerDirect === 'function') {
                        openQRScannerDirect();
                    }
                });
            }

            // QR Form submission
            const qrForm = document.getElementById('qrVisitorForm');
            if (qrForm) {
                qrForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    if (typeof generateQRVisitorCode === 'function') {
                        generateQRVisitorCode();
                    }
                });
            }

            // Close modal when clicking outside
            const modal = document.getElementById('qrRegistrationModal');
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === modal) {
                        if (typeof closeQRRegistrationModal === 'function') {
                            closeQRRegistrationModal();
                        }
                    }
                });
            }

            // QR file upload handler
            const qrFileInput = document.getElementById("qrFileInput");
            if (qrFileInput) {
                qrFileInput.addEventListener('change', async function (event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    console.log('Processing uploaded QR file:', file.name);

                    // Show processing message
                    const instructions = document.getElementById("scannerInstructions");
                    const status = document.getElementById("scannerStatus");
                    
                    if (instructions) {
                        instructions.innerHTML = '⚡ <strong>Processing...</strong><br><small>QR detection in progress</small>';
                    }
                    if (status) {
                        status.classList.add("hidden");
                    }

                    // Process the file
                    try {
                        const reader = new FileReader();
                        reader.onload = async (e) => {
                            const img = new Image();
                            img.onload = async () => {
                                // Try to detect QR code
                                try {
                                    const html5QrCode = new Html5Qrcode("reader");
                                    const result = await html5QrCode.scanImage(img, true);
                                    console.log('✅ QR Code found:', result);
                                    if (typeof processQRUploadResult === 'function') {
                                        processQRUploadResult(result);
                                    }
                                } catch (e) {
                                    console.log('QR detection failed:', e);
                                    if (instructions) {
                                        instructions.innerHTML = '❌ <strong>No QR Code Detected</strong><br><small>Try a clearer image</small>';
                                    }
                                }
                            };
                            img.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    } catch (error) {
                        console.error('Error processing QR image:', error);
                        if (instructions) {
                            instructions.innerHTML = '❌ <strong>Processing Error</strong><br><small>Please try again</small>';
                        }
                    }

                    // Reset file input
                    event.target.value = '';
                });
            }

            // Enhanced Search functionality with pagination support
            const searchVisitors = document.getElementById('searchVisitors');
            const resultsCount = document.getElementById('resultsCount');
            const itemsPerPageSelect = document.querySelector('select');
            let currentPage = 1;
            let itemsPerPage = 10;
            let allVisitors = [];
            let filteredVisitors = [];

            // Initialize visitors data
            function initializeVisitorsData() {
                const tbody = document.getElementById('visitorsTbody');
                if (!tbody) return;

                const rows = Array.from(tbody.querySelectorAll('tr'));
                allVisitors = rows.map((row, index) => {
                    const emptyCell = row.querySelector('td[colspan]');
                    if (emptyCell) return null;

                    return {
                        element: row,
                        index: index,
                        name: row.querySelector('td:nth-child(1) .text-sm')?.textContent || '',
                        id: row.querySelector('td:nth-child(1) .text-xs')?.textContent || '',
                        company: row.querySelector('td:nth-child(3) .text-sm')?.textContent || '',
                        type: row.querySelector('td:nth-child(2) .text-xs')?.textContent || '',
                        host: row.querySelector('td:nth-child(4) .text-sm')?.textContent || '',
                        dept: row.querySelector('td:nth-child(4) .text-xs')?.textContent || ''
                    };
                }).filter(v => v !== null);

                filteredVisitors = [...allVisitors];
            }

            function updateResultsCount() {
                if (!resultsCount) return;
                const visible = filteredVisitors.length;
                resultsCount.textContent = visible + (visible === 1 ? ' result' : ' results');
            }

            function applySearchFilter(query) {
                const q = (query || '').toString().toLowerCase().trim();

                if (q === '') {
                    filteredVisitors = [...allVisitors];
                } else {
                    filteredVisitors = allVisitors.filter(visitor => {
                        const hay = (visitor.name + ' ' + visitor.id + ' ' + visitor.company + ' ' + visitor.type + ' ' + visitor.host + ' ' + visitor.dept).toLowerCase();
                        return hay.includes(q);
                    });
                }

                currentPage = 1;
                updatePagination();
                renderVisitors();
                updateResultsCount();
            }

            function renderVisitors() {
                const tbody = document.getElementById('visitorsTbody');
                if (!tbody) return;

                // Hide all visitor rows initially
                allVisitors.forEach(visitor => {
                    visitor.element.classList.add('hidden');
                });

                // Show only the current page's filtered visitors
                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const pageVisitors = filteredVisitors.slice(startIndex, endIndex);

                pageVisitors.forEach(visitor => {
                    visitor.element.classList.remove('hidden');
                });

                // Handle empty state
                const emptyRow = tbody.querySelector('td[colspan]');
                if (emptyRow) {
                    const emptyRowParent = emptyRow.parentElement;
                    emptyRowParent.classList.toggle('hidden', filteredVisitors.length > 0);
                }
            }

            function updatePagination() {
                const totalPages = Math.ceil(filteredVisitors.length / itemsPerPage);
                const paginationContainer = document.querySelector('.flex.items-center.gap-2');
                if (!paginationContainer) return;

                // Clear existing pagination buttons
                paginationContainer.innerHTML = '';

                // Previous button
                const prevBtn = document.createElement('button');
                prevBtn.className = 'px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed';
                prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
                prevBtn.disabled = currentPage === 1;
                prevBtn.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        renderVisitors();
                        updatePagination();
                    }
                });
                paginationContainer.appendChild(prevBtn);

                // Page numbers
                const maxVisiblePages = 5;
                let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
                let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

                if (endPage - startPage < maxVisiblePages - 1) {
                    startPage = Math.max(1, endPage - maxVisiblePages + 1);
                }

                for (let i = startPage; i <= endPage; i++) {
                    const pageBtn = document.createElement('button');
                    pageBtn.className = i === currentPage
                        ? 'px-3 py-1 text-sm text-white bg-emerald-600 border border-emerald-600 rounded-lg hover:bg-emerald-700'
                        : 'px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50';
                    pageBtn.textContent = i;
                    pageBtn.addEventListener('click', () => {
                        currentPage = i;
                        renderVisitors();
                        updatePagination();
                    });
                    paginationContainer.appendChild(pageBtn);
                }

                // Ellipsis if needed
                if (endPage < totalPages) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'px-2 text-sm text-gray-500';
                    ellipsis.textContent = '...';
                    paginationContainer.appendChild(ellipsis);

                    const lastPageBtn = document.createElement('button');
                    lastPageBtn.className = 'px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50';
                    lastPageBtn.textContent = totalPages;
                    lastPageBtn.addEventListener('click', () => {
                        currentPage = totalPages;
                        renderVisitors();
                        updatePagination();
                    });
                    paginationContainer.appendChild(lastPageBtn);
                }

                // Next button
                const nextBtn = document.createElement('button');
                nextBtn.className = 'px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed';
                nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
                nextBtn.disabled = currentPage === totalPages || totalPages === 0;
                nextBtn.addEventListener('click', () => {
                    if (currentPage < totalPages) {
                        currentPage++;
                        renderVisitors();
                        updatePagination();
                    }
                });
                paginationContainer.appendChild(nextBtn);

                // Update results summary
                const summaryElement = document.querySelector('.text-sm.text-gray-700');
                if (summaryElement && filteredVisitors.length > 0) {
                    const start = (currentPage - 1) * itemsPerPage + 1;
                    const end = Math.min(currentPage * itemsPerPage, filteredVisitors.length);
                    summaryElement.innerHTML = `Showing <span class="font-medium">${start}</span> to <span class="font-medium">${end}</span> of <span class="font-medium">${filteredVisitors.length}</span> results`;
                }
            }

            // Items per page change handler
            if (itemsPerPageSelect) {
                itemsPerPageSelect.addEventListener('change', (e) => {
                    itemsPerPage = parseInt(e.target.value);
                    currentPage = 1;
                    renderVisitors();
                    updatePagination();
                });
            }

            // Search input handler
            if (searchVisitors) {
                searchVisitors.addEventListener('input', (e) => applySearchFilter(e.target.value));
            }

            // Modal Management
            const viewVisitorModal = document.getElementById("viewVisitorModal");
            const editVisitorModal = document.getElementById("editVisitorModal");
            const deleteVisitorModal = null;
            const checkInModal = document.getElementById("checkInModal");

            const qrScannerModal = document.getElementById("qrScannerModal");
            const qrResultModal = document.getElementById("qrResultModal");

            function openModal(modal) {
                if (!modal) return;
                // Clear any pending close timers
                if (modal._closeTimer) {
                    clearTimeout(modal._closeTimer);
                    modal._closeTimer = null;
                }
                modal.classList.add("active");
                modal.style.display = "flex";
            }

            function closeModal(modal) {
                if (!modal) return;
                modal.classList.remove("active");
                // Store timer to prevent race conditions
                if (modal._closeTimer) clearTimeout(modal._closeTimer);
                modal._closeTimer = setTimeout(() => {
                    modal.style.display = "none";
                    modal._closeTimer = null;
                }, 300);
            }

            function closeAllModals(excludeModal = null) {
                const allModals = [viewVisitorModal, editVisitorModal, checkInModal, qrScannerModal, qrResultModal];
                allModals.forEach(modal => {
                    if (modal && modal !== excludeModal) {
                        closeModal(modal);
                    }
                });
            }

            // Add Visitor Modal functionality removed - using QR Registration Modal instead

            // QR Functionalify
            const scanQRBtn = document.getElementById("scanQRBtn");
            let html5QrcodeScanner = null;
            let currentQRCode = null;

            let isTransitioning = false;

            if (scanQRBtn) {
                scanQRBtn.addEventListener("click", async () => {
                    if (isTransitioning) return;

                    closeAllModals(qrScannerModal);
                    openModal(qrScannerModal);

                    // Don't auto-start, let user click "Start Scan" button
                    document.getElementById("scannerInstructions").innerHTML =
                        'Click "Start Scan" to begin scanning QR codes';
                });
            }

            // Manual scan controls
            document.getElementById("startScan")?.addEventListener("click", async () => {
                document.getElementById("scannerInstructions").innerHTML =
                    'Position the QR code within the frame to scan';
                document.getElementById("scannerStatus").classList.remove("hidden");
                await startScanner();
            });

            document.getElementById("closeQRScanner")?.addEventListener("click", () => {
                document.getElementById("scannerStatus").classList.add("hidden");
                stopScanner();
                closeModal(qrScannerModal);
            });

            document.getElementById("stopScanner")?.addEventListener("click", () => {
                document.getElementById("scannerStatus").classList.add("hidden");
                stopScanner();
                closeModal(qrScannerModal);
            });

            // Test QR functionality
            document.getElementById("testQR")?.addEventListener("click", () => {
                console.log('Test QR button clicked!');

                try {
                    // Create a test QR code in the reader area
                    const readerElement = document.getElementById("reader");
                    if (readerElement) {
                        readerElement.innerHTML = '';

                        // Create enhanced test QR data
                        const testQRData = JSON.stringify({
                            id: 'TEST-' + Date.now(),
                            name: 'Test Visitor',
                            access: 'STANDARD',
                            date: new Date().toISOString().split('T')[0],
                            time: new Date().toISOString(),
                            type: "VISITOR_QR",
                            version: "2.0",
                            hash: btoa("TEST-" + Date.now() + new Date().toISOString()).substring(0, 16)
                        });

                        console.log('Generating test QR with data:', testQRData);

                        new QRCode(readerElement, {
                            text: testQRData,
                            width: 300,
                            height: 300,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H,
                            margin: 2
                        });

                        // Update instructions with success message
                        document.getElementById("scannerInstructions").innerHTML =
                            '🧪 <strong>Test QR Code Generated</strong><br><small>Point your camera at this QR code to test the scanner</small>';
                        document.getElementById("scannerStatus").classList.add("hidden");

                        console.log('Test QR code generated successfully');
                    }
                } catch (error) {
                    console.error('Error generating test QR:', error);
                    document.getElementById("scannerInstructions").innerHTML =
                        '❌ Error generating test QR code. Please try again.';
                }
            });

            // QR upload functionality with enhanced detection
            document.getElementById("uploadQR")?.addEventListener("click", () => {
                document.getElementById("qrFileInput").click();
            });

            // Enhanced QR file upload with 4 detection methods
            document.getElementById("qrFileInput")?.addEventListener("change", async (event) => {
                const file = event.target.files[0];
                if (!file) return;

                console.log('Processing uploaded QR file:', file.name);

                // Show processing message
                document.getElementById("scannerInstructions").innerHTML =
                    '📤 <strong>Processing QR Image...</strong><br><small>Applying 4 detection methods to your image</small>';
                document.getElementById("scannerStatus").classList.add("hidden");

                try {
                    const reader = new FileReader();
                    reader.onload = async (e) => {
                        const img = new Image();
                        img.onload = async () => {
                            console.log('Image loaded, applying 4 detection methods...');

                            // Method 1: Direct scan
                            try {
                                const html5QrCode = new Html5Qrcode("reader");
                                const result = await html5QrCode.scanImage(img, true);
                                console.log('✅ Method 1 (Direct): QR Code found:', result);
                                processQRResult(result);
                                return;
                            } catch (e) {
                                console.log('Method 1 failed, trying Method 2...');
                            }

                            // Method 2: Grayscale processing
                            try {
                                const canvas = document.createElement('canvas');
                                const ctx = canvas.getContext('2d');
                                canvas.width = img.width;
                                canvas.height = img.height;
                                ctx.drawImage(img, 0, 0);

                                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                                const data = imageData.data;

                                // Convert to grayscale
                                for (let i = 0; i < data.length; i += 4) {
                                    const gray = data[i] * 0.299 + data[i + 1] * 0.587 + data[i + 2] * 0.114;
                                    data[i] = gray;
                                    data[i + 1] = gray;
                                    data[i + 2] = gray;
                                }

                                ctx.putImageData(imageData, 0, 0);
                                const grayImg = new Image();
                                grayImg.src = canvas.toDataURL();

                                const html5QrCode2 = new Html5Qrcode("reader");
                                const result2 = await html5QrCode2.scanImage(grayImg, true);
                                console.log('✅ Method 2 (Grayscale): QR Code found:', result2);
                                processQRResult(result2);
                                return;
                            } catch (e) {
                                console.log('Method 2 failed, trying Method 3...');
                            }

                            // Method 3: Inverted colors
                            try {
                                const canvas2 = document.createElement('canvas');
                                const ctx2 = canvas2.getContext('2d');
                                canvas2.width = img.width;
                                canvas2.height = img.height;
                                ctx2.drawImage(img, 0, 0);

                                const imageData2 = ctx2.getImageData(0, 0, canvas2.width, canvas2.height);
                                const data2 = imageData2.data;

                                // Invert colors
                                for (let i = 0; i < data2.length; i += 4) {
                                    data2[i] = 255 - data2[i];
                                    data2[i + 1] = 255 - data2[i + 1];
                                    data2[i + 2] = 255 - data2[i + 2];
                                }

                                ctx2.putImageData(imageData2, 0, 0);
                                const invertedImg = new Image();
                                invertedImg.src = canvas2.toDataURL();

                                const html5QrCode3 = new Html5Qrcode("reader");
                                const result3 = await html5QrCode3.scanImage(invertedImg, true);
                                console.log('✅ Method 3 (Inverted): QR Code found:', result3);
                                processQRResult(result3);
                                return;
                            } catch (e) {
                                console.log('Method 3 failed, trying Method 4...');
                            }

                            // Method 4: Enhanced contrast
                            try {
                                const canvas3 = document.createElement('canvas');
                                const ctx3 = canvas3.getContext('2d');
                                canvas3.width = img.width;
                                canvas3.height = img.height;
                                ctx3.drawImage(img, 0, 0);

                                const imageData3 = ctx3.getImageData(0, 0, canvas3.width, canvas3.height);
                                const data3 = imageData3.data;

                                // Enhance contrast
                                const factor = 2.0;
                                for (let i = 0; i < data3.length; i += 4) {
                                    data3[i] = Math.min(255, Math.max(0, (data3[i] - 128) * factor + 128));
                                    data3[i + 1] = Math.min(255, Math.max(0, (data3[i + 1] - 128) * factor + 128));
                                    data3[i + 2] = Math.min(255, Math.max(0, (data3[i + 2] - 128) * factor + 128));
                                }

                                ctx3.putImageData(imageData3, 0, 0);
                                const contrastImg = new Image();
                                contrastImg.src = canvas3.toDataURL();

                                const html5QrCode4 = new Html5Qrcode("reader");
                                const result4 = await html5QrCode4.scanImage(contrastImg, true);
                                console.log('✅ Method 4 (Contrast): QR Code found:', result4);
                                processQRResult(result4);
                                return;
                            } catch (e) {
                                console.log('Method 4 failed, trying Method 5...');
                            }

                            // Method 5: Threshold processing
                            try {
                                const canvas4 = document.createElement('canvas');
                                const ctx4 = canvas4.getContext('2d');
                                canvas4.width = img.width;
                                canvas4.height = img.height;
                                ctx4.drawImage(img, 0, 0);

                                const imageData4 = ctx4.getImageData(0, 0, canvas4.width, canvas4.height);
                                const data4 = imageData4.data;

                                // Apply threshold (black and white only)
                                const threshold = 128;
                                for (let i = 0; i < data4.length; i += 4) {
                                    const gray = data4[i] * 0.299 + data4[i + 1] * 0.587 + data4[i + 2] * 0.114;
                                    const value = gray > threshold ? 255 : 0;
                                    data4[i] = value;
                                    data4[i + 1] = value;
                                    data4[i + 2] = value;
                                }

                                ctx4.putImageData(imageData4, 0, 0);
                                const thresholdImg = new Image();
                                thresholdImg.src = canvas4.toDataURL();

                                const html5QrCode5 = new Html5Qrcode("reader");
                                const result5 = await html5QrCode5.scanImage(thresholdImg, true);
                                console.log('✅ Method 5 (Threshold): QR Code found:', result5);
                                processQRResult(result5);
                                return;
                            } catch (e) {
                                console.log('Method 5 failed, trying Method 6...');
                            }

                            // Method 6: Resized processing
                            try {
                                const canvas5 = document.createElement('canvas');
                                const ctx5 = canvas5.getContext('2d');

                                // Resize to larger dimensions for better detection
                                const scale = 2;
                                canvas5.width = img.width * scale;
                                canvas5.height = img.height * scale;

                                ctx5.imageSmoothingEnabled = false;
                                ctx5.drawImage(img, 0, 0, canvas5.width, canvas5.height);

                                const resizedImg = new Image();
                                resizedImg.src = canvas5.toDataURL();

                                const html5QrCode6 = new Html5Qrcode("reader");
                                const result6 = await html5QrCode6.scanImage(resizedImg, true);
                                console.log('✅ Method 6 (Resized): QR Code found:', result6);
                                processQRResult(result6);
                                return;
                            } catch (e) {
                                console.log('Method 6 failed, trying Method 7...');
                            }

                            // Method 7: Sharpening filter
                            try {
                                const canvas6 = document.createElement('canvas');
                                const ctx6 = canvas6.getContext('2d');
                                canvas6.width = img.width;
                                canvas6.height = img.height;
                                ctx6.drawImage(img, 0, 0);

                                const imageData6 = ctx6.getImageData(0, 0, canvas6.width, canvas6.height);
                                const data6 = imageData6.data;
                                const width = canvas6.width;
                                const height = canvas6.height;

                                // Apply sharpening kernel
                                const kernel = [
                                    0, -1, 0,
                                    -1, 5, -1,
                                    0, -1, 0
                                ];

                                const output = new Uint8ClampedArray(data6);

                                for (let y = 1; y < height - 1; y++) {
                                    for (let x = 1; x < width - 1; x++) {
                                        for (let c = 0; c < 3; c++) {
                                            let sum = 0;
                                            for (let ky = -1; ky <= 1; ky++) {
                                                for (let kx = -1; kx <= 1; kx++) {
                                                    const idx = ((y + ky) * width + (x + kx)) * 4 + c;
                                                    sum += data6[idx] * kernel[(ky + 1) * 3 + (kx + 1)];
                                                }
                                            }
                                            output[(y * width + x) * 4 + c] = Math.min(255, Math.max(0, sum));
                                        }
                                    }
                                }

                                const outputData = new ImageData(output, width, height);
                                ctx6.putImageData(outputData, 0, 0);
                                const sharpenedImg = new Image();
                                sharpenedImg.src = canvas6.toDataURL();

                                const html5QrCode7 = new Html5Qrcode("reader");
                                const result7 = await html5QrCode7.scanImage(sharpenedImg, true);
                                console.log('✅ Method 7 (Sharpened): QR Code found:', result7);
                                processQRResult(result7);
                                return;
                            } catch (e) {
                                console.log('Method 7 failed, trying Method 8...');
                            }

                            // Method 8: Multiple rotations
                            const rotations = [90, 180, 270];
                            for (const angle of rotations) {
                                try {
                                    const canvas7 = document.createElement('canvas');
                                    const ctx7 = canvas7.getContext('2d');

                                    if (angle === 90 || angle === 270) {
                                        canvas7.width = img.height;
                                        canvas7.height = img.width;
                                    } else {
                                        canvas7.width = img.width;
                                        canvas7.height = img.height;
                                    }

                                    ctx7.save();
                                    ctx7.translate(canvas7.width / 2, canvas7.height / 2);
                                    ctx7.rotate(angle * Math.PI / 180);
                                    ctx7.drawImage(img, -img.width / 2, -img.height / 2);
                                    ctx7.restore();

                                    const rotatedImg = new Image();
                                    rotatedImg.src = canvas7.toDataURL();

                                    const html5QrCode8 = new Html5Qrcode("reader");
                                    const result8 = await html5QrCode8.scanImage(rotatedImg, true);
                                    console.log(`✅ Method 8 (Rotated ${angle}°): QR Code found:`, result8);
                                    processQRResult(result8);
                                    return;
                                } catch (e) {
                                    console.log(`Rotation ${angle}° failed, trying next...`);
                                }
                            }

                            console.log('All 8 methods failed');
                            throw new Error('No QR Code Found after applying 8 detection methods');
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } catch (error) {
                    console.error('Error processing QR image:', error);
                    document.getElementById("scannerInstructions").innerHTML =
                        '🔍 <strong>QR Detection Failed</strong><br><small>Your image is clear, but detection needs enhancement<br>Try the Test QR button to verify scanner works</small>';
                }
                '❌ Error generating test QR code.';
            }
            }

            document.getElementById("qrFileInput")?.addEventListener("change", (event) => {
                const file = event.target.files[0];
                if (!file) return;

                console.log("QR file selected:", file.name);

                // Show loading state
                document.getElementById("scannerInstructions").innerHTML =
                    '📤 Processing QR code image...';
                document.getElementById("scannerStatus").classList.add("hidden");

                // Create file reader
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = new Image();
                    img.onload = function () {
                        // Create canvas for image processing with enhanced quality
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');

                        // Scale up for better QR detection
                        const scale = Math.max(1, Math.min(800 / img.width, 800 / img.height));
                        canvas.width = img.width * scale;
                        canvas.height = img.height * scale;

                        // Enable image smoothing for better quality
                        ctx.imageSmoothingEnabled = true;
                        ctx.imageSmoothingQuality = 'high';
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                        // Enhanced QR code detection with multiple attempts
                        try {
                            let code = null;

                            // Method 1: Direct detection
                            const imageData1 = ctx.getImageData(0, 0, canvas.width, canvas.height);
                            code = jsQR(imageData1.data, imageData1.width, imageData1.height);

                            // Method 2: Grayscale conversion if direct fails
                            if (!code) {
                                const grayscaleCanvas = document.createElement('canvas');
                                const grayscaleCtx = grayscaleCanvas.getContext('2d');
                                grayscaleCanvas.width = canvas.width;
                                grayscaleCanvas.height = canvas.height;

                                // Convert to grayscale
                                grayscaleCtx.drawImage(canvas, 0, 0);
                                const imageData2 = grayscaleCtx.getImageData(0, 0, canvas.width, canvas.height);
                                const data = imageData2.data;

                                for (let i = 0; i < data.length; i += 4) {
                                    const gray = data[i] * 0.299 + data[i + 1] * 0.587 + data[i + 2] * 0.114;
                                    data[i] = gray;
                                    data[i + 1] = gray;
                                    data[i + 2] = gray;
                                }

                                code = jsQR(imageData2.data, imageData2.width, imageData2.height);
                            }

                            // Method 3: Inverted colors if grayscale fails
                            if (!code) {
                                const invertedCanvas = document.createElement('canvas');
                                const invertedCtx = invertedCanvas.getContext('2d');
                                invertedCanvas.width = canvas.width;
                                invertedCanvas.height = canvas.height;

                                invertedCtx.filter = 'invert(1)';
                                invertedCtx.drawImage(canvas, 0, 0);
                                const imageData3 = invertedCtx.getImageData(0, 0, canvas.width, canvas.height);
                                code = jsQR(imageData3.data, imageData3.width, imageData3.height);
                            }

                            // Method 4: Increased contrast if all else fails
                            if (!code) {
                                const contrastCanvas = document.createElement('canvas');
                                const contrastCtx = contrastCanvas.getContext('2d');
                                contrastCanvas.width = canvas.width;
                                contrastCanvas.height = canvas.height;

                                contrastCtx.filter = 'contrast(200%) brightness(150%)';
                                contrastCtx.drawImage(canvas, 0, 0);
                                const imageData4 = contrastCtx.getImageData(0, 0, canvas.width, canvas.height);
                                code = jsQR(imageData4.data, imageData4.width, imageData4.height);
                            }

                            if (code) {
                                console.log("✅ QR Code successfully decoded from image:", code.data);
                                stopScanner();
                                closeModal(qrScannerModal);

                                // Try to parse as JSON first
                                try {
                                    const qrData = JSON.parse(code.data);
                                    console.log("Parsed QR data:", qrData);
                                    verifyScannedVisitor(qrData.id || code.data);
                                } catch (e) {
                                    console.log("Failed to parse as JSON, using raw text:", code.data);
                                    verifyScannedVisitor(code.data);
                                }
                            } else {
                                console.log("QR Detection failed - tried all 4 methods");
                                document.getElementById("scannerInstructions").innerHTML =
                                    '🔍 QR Detection Failed - Trying all methods...<br><small>Your image is clear, but detection needs enhancement</small>';
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'QR Detection Enhanced',
                                    html: 'Applied 4 detection methods to your image.<br><small>Try the Test QR button to verify scanner works</small>',
                                    confirmButtonColor: "#f59e0b"
                                });
                            }
                        } catch (error) {
                            console.error("Error decoding QR code:", error);
                            document.getElementById("scannerInstructions").innerHTML =
                                '❌ Failed to decode QR code. Please try another image.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Decode Failed',
                                text: 'Failed to decode QR code from image. Please try a clearer image.',
                                confirmButtonColor: "#dc2626"
                            });
                        }
                    };
                    img.onerror = function () {
                        document.getElementById("scannerInstructions").innerHTML =
                            '❌ Failed to load image. Please try another file.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Image Error',
                            text: 'Failed to load the image file.',
                            confirmButtonColor: "#dc2626"
                        });
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);

                // Reset file input
                event.target.value = '';
            });

        // Manual scan functionality
        document.getElementById("manualScan")?.addEventListener("click", () => {
            // Prompt user to enter QR code manually
            Swal.fire({
                title: 'Manual QR Code Entry',
                input: 'text',
                inputLabel: 'Enter Visitor ID or QR Code Data',
                inputPlaceholder: 'e.g., VISITOR-001 or JSON data',
                showCancelButton: true,
                confirmButtonText: 'Verify',
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    console.log("Manual QR entry:", result.value);
                    stopScanner();
                    closeModal(qrScannerModal);

                    // Try to parse as JSON first
                    try {
                        const qrData = JSON.parse(result.value);
                        console.log("Parsed manual QR data:", qrData);
                        verifyScannedVisitor(qrData.id || result.value);
                    } catch (e) {
                        console.log("Failed to parse as JSON, using raw text:", result.value);
                        verifyScannedVisitor(result.value);
                    }
                }
            });
        });

        document.getElementById("closeQRResult")?.addEventListener("click", () => closeModal(qrResultModal));

        // QR Modal display function
        function showQRModal(visitorId, visitorName, qrCode) {
            // Create modal content
            const modalHtml = `
                    <div id="qrDisplayModal" class="modal active" aria-modal="true" role="dialog">
                        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 fade-in">
                            <div class="bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-600 px-6 py-5 rounded-t-2xl">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mr-3">
                                            <i class="fas fa-qrcode text-emerald-600"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-white">Visitor QR Code</h3>
                                            <p class="text-emerald-100 text-sm">${visitorName}</p>
                                        </div>
                                    </div>
                                    <button onclick="closeQRDisplayModal()" class="text-white/90 hover:text-white transition-all duration-300 transform hover:scale-110 p-2 rounded-xl hover:bg-white/20">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="p-6 text-center">
                                <div class="mb-4">
                                    <img src="data:image/png;base64,${qrCode}" 
                                         alt="QR Code for ${visitorName}" 
                                         class="w-48 h-48 mx-auto rounded-xl border-2 border-gray-200 shadow-lg">
                                </div>
                                <div class="text-sm text-gray-600 mb-4">
                                    <strong>Visitor ID:</strong> ${visitorId}
                                </div>
                                <div class="flex gap-3 justify-center">
                                    <button onclick="downloadVisitorQR('${visitorName}', '${qrCode}')" 
                                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors">
                                        <i class="fas fa-download mr-2"></i>Download
                                    </button>
                                    <button onclick="printVisitorQR('${visitorName}', '${qrCode}')" 
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                        <i class="fas fa-printer mr-2"></i>Print
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

            // Remove existing modal if any
            const existingModal = document.getElementById('qrDisplayModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Add new modal to body
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            document.body.style.overflow = 'hidden';
        }

        function closeQRDisplayModal() {
            const modal = document.getElementById('qrDisplayModal');
            if (modal) {
                modal.remove();
                document.body.style.overflow = 'auto';
            }
        }

        function downloadVisitorQR(visitorName, qrCode) {
            // Create download link
            const link = document.createElement('a');
            link.download = `QR-${visitorName.replace(/\s+/g, '-')}-${Date.now()}.png`;
            link.href = `data:image/png;base64,${qrCode}`;
            link.click();
        }

        function printVisitorQR(visitorName, qrCode) {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                    <html>
                    <head><title>QR Code - ${visitorName}</title></head>
                    <body style="display:flex;flex-direction:column;align-items:center;padding:40px;text-align:center;font-family:sans-serif;">
                        <h1>Visitor QR Code</h1>
                        <h2 style="color:#333;margin:10px 0;">${visitorName}</h2>
                        <img src="data:image/png;base64,${qrCode}" style="width:200px;height:200px;margin:20px 0;border:2px solid #ddd;padding:10px;">
                        <p style="color:#666;margin-top:20px;">Scan this QR code for visitor verification</p>
                    </body>
                    </html>
                `);
            printWindow.document.close();
            printWindow.print();
        }

        async function startScanner() {
            if (isTransitioning) return;
            isTransitioning = true;

            const readerElement = document.getElementById("reader");
            if (readerElement) readerElement.innerHTML = '';

            try {
                // Clean up any existing scanner
                if (html5QrcodeScanner) {
                    try {
                        await html5QrcodeScanner.stop();
                    } catch (e) {
                        console.log("Scanner already stopped");
                    }
                    html5QrcodeScanner.clear();
                    html5QrcodeScanner = null;
                }

                // Create new scanner instance
                html5QrcodeScanner = new Html5Qrcode("reader");

                // Try different camera configurations
                const cameraConfigs = [
                    { facingMode: "user" },           // Front camera
                    { facingMode: "environment" },    // Rear camera
                    { facingMode: { exact: "user" } }, // Exact front camera
                    { facingMode: { exact: "environment" } }, // Exact rear camera
                    undefined // Let browser choose
                ];

                let scannerStarted = false;
                let lastError = null;

                for (let i = 0; i < cameraConfigs.length; i++) {
                    try {
                        console.log(`Trying camera configuration ${i + 1}:`, cameraConfigs[i]);

                        const config = {
                            fps: 60, // Maximum FPS for real-time scanning
                            qrbox: { width: 400, height: 400 }, // Full scan area
                            aspectRatio: 1.0,
                            disableFlip: false,
                            formatsToSupport: [
                                Html5QrcodeSupportedFormats.QR_CODE,
                                Html5QrcodeSupportedFormats.AZTEC,
                                Html5QrcodeSupportedFormats.DATA_MATRIX,
                                Html5QrcodeSupportedFormats.PDF_417
                            ],
                            // Enhanced scanning options
                            debug: true,
                            verbose: true,
                            experimentalFeatures: {
                                useBarCodeDetectorIfSupported: true
                            }
                        };

                        await html5QrcodeScanner.start(
                            cameraConfigs[i],
                            config,
                            (decodedText) => {
                                // Visual feedback for successful scan
                                console.log("🎯 QR Code detected!", decodedText);
                                console.log("Raw QR data type:", typeof decodedText);

                                // Flash effect to indicate scan
                                const readerElement = document.getElementById("reader");
                                if (readerElement) {
                                    readerElement.style.border = "3px solid #10b981";
                                    setTimeout(() => {
                                        readerElement.style.border = "";
                                    }, 200);
                                }

                                // Update status immediately
                                document.getElementById("scannerInstructions").innerHTML =
                                    '✅ QR Code detected! Processing...';
                                document.getElementById("scannerStatus").innerHTML =
                                    '<i class="fas fa-check-circle text-green-500 mr-1"></i>QR Code Scanned Successfully!';

                                // Stop scanner and process
                                setTimeout(() => {
                                    stopScanner();
                                    closeModal(qrScannerModal);

                                    // Try to parse as JSON first
                                    try {
                                        const qrData = JSON.parse(decodedText);
                                        console.log("Parsed QR data:", qrData);
                                        verifyScannedVisitor(qrData.id || decodedText);
                                    } catch (e) {
                                        console.log("Failed to parse as JSON, using raw text:", decodedText);
                                        verifyScannedVisitor(decodedText);
                                    }
                                }, 500);
                            },
                            (errorMessage) => {
                                // Only log important errors to avoid spam
                                if (errorMessage.includes("No QR code found") === false) {
                                    console.log("Scanner warning:", errorMessage);
                                }
                            }
                        );

                        scannerStarted = true;
                        console.log(`Scanner started successfully with camera configuration ${i + 1}`);
                        break;

                    } catch (cameraError) {
                        console.log(`Camera configuration ${i + 1} failed:`, cameraError);
                        lastError = cameraError;

                        // Clean up before trying next configuration
                        try {
                            await html5QrcodeScanner.stop();
                            html5QrcodeScanner.clear();
                        } catch (e) {
                            // Ignore cleanup errors
                        }

                        // Create new instance for next attempt
                        html5QrcodeScanner = new Html5Qrcode("reader");
                    }
                }

                if (!scannerStarted) {
                    throw new Error(lastError || "All camera configurations failed");
                }

            } catch (err) {
                console.error("Unable to start scanner", err);
                document.getElementById("scannerStatus").classList.add("hidden");
                document.getElementById("scannerInstructions").innerHTML =
                    '❌ Camera access failed. Try "Manual Scan" or check camera permissions.';

                // Show user-friendly error with alternatives
                Swal.fire({
                    icon: 'warning',
                    title: 'Camera Not Available',
                    html: `
                            <div class="text-left">
                                <p class="mb-3">Unable to access camera. Please try:</p>
                                <ul class="list-disc list-inside text-sm space-y-1">
                                    <li>Check camera permissions in browser settings</li>
                                    <li>Try using the "Manual Scan" button</li>
                                    <li>Refresh the page and try again</li>
                                    <li>Use a different browser (Chrome/Firefox recommended)</li>
                                </ul>
                            </div>
                        `,
                    confirmButtonColor: "#dc2626",
                    showCancelButton: true,
                    cancelButtonText: "Try Manual Scan",
                    cancelButtonColor: "#059669"
                }).then((result) => {
                    if (result.isDismissed) {
                        // User clicked "Try Manual Scan"
                        document.getElementById("manualScan")?.click();
                    }
                });
            } finally {
                isTransitioning = false;
            }
        }

        async function stopScanner() {
            if (!html5QrcodeScanner) return;
            if (isTransitioning) return;

            isTransitioning = true;

            try {
                await html5QrcodeScanner.stop();
                html5QrcodeScanner.clear();
                console.log("Scanner stopped successfully");
            } catch (e) {
                console.error("Error stopping scanner", e);
            } finally {
                isTransitioning = false;
            }
        }

        async function verifyScannedVisitor(id) {
            try {
                const res = await fetch('{{ route('api.qr.verify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ id: id })
                });
                const data = await res.json();

                showQRResult(data.success, data.message, data.visitor || { code: id });
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'An error occurred during verification.', 'error');
            }
        }

        function showQRResult(success, message, visitor) {
            const icon = document.getElementById('qrStatusIcon');
            const text = document.getElementById('qrResultText');
            const modalTitle = document.getElementById('qrModalTitle');

            modalTitle.textContent = success ? 'Access Granted' : 'Access Denied';
            text.textContent = message;
            text.className = `text-xl font-bold mb-6 ${success ? 'text-emerald-600' : 'text-red-600'}`;

            icon.innerHTML = success
                ? '<div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center"><i class="fas fa-check text-3xl"></i></div>'
                : '<div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center"><i class="fas fa-times text-3xl"></i></div>';

            document.getElementById('qrName').textContent = visitor.name || 'Unknown';
            document.getElementById('qrId').textContent = visitor.code || 'N/A';
            document.getElementById('qrStatus').textContent = (visitor.status || 'N/A').replace('_', ' ').toUpperCase();

            // Update QR Code display
            const qrcodeContainer = document.getElementById('qrcode');
            qrcodeContainer.innerHTML = '';

            if (success || visitor.code) {
                const qrData = JSON.stringify({
                    id: visitor.code,
                    name: visitor.name,
                    date: visitor.check_in_date
                });

                new QRCode(qrcodeContainer, {
                    text: qrData,
                    width: 180,
                    height: 180,
                    colorDark: "#1f2937",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            } else {
                qrcodeContainer.innerHTML = '<div class="w-44 h-44 bg-gray-50 flex items-center justify-center text-gray-300"><i class="fas fa-qrcode text-5xl"></i></div>';
            }

            openModal(qrResultModal);

            if (success) {
                // Refresh data if status changed
                initializeVisitorsData();
            }
        }

        // QR Actions
        document.getElementById('printQR')?.addEventListener('click', () => {
            const qrContent = document.getElementById('qrcode').innerHTML;
            const visitorName = document.getElementById('qrName').textContent;
            const visitorId = document.getElementById('qrId').textContent;

            const printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Visitor Badge</title>');
            printWindow.document.write('<style>body{font-family:sans-serif;display:flex;flex-direction:column;align-items:center;padding:40px;text-align:center;} .qr{margin:20px 0;} h2{margin:5px 0;} p{color:#666;}</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<h1>VISITOR ACCESS</h1>');
            printWindow.document.write('<div class="qr">' + qrContent + '</div>');
            printWindow.document.write('<h2>' + visitorName + '</h2>');
            printWindow.document.write('<p>ID: ' + visitorId + '</p>');
            printWindow.document.write('<p>Date: ' + new Date().toLocaleDateString() + '</p>');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        });

        document.getElementById('downloadQR')?.addEventListener('click', () => {
            const img = document.querySelector('#qrcode img');
            if (img) {
                const link = document.createElement('a');
                link.download = `QR_${document.getElementById('qrId').textContent}.png`;
                link.href = img.src;
                link.click();
            } else {
                const canvas = document.querySelector('#qrcode canvas');
                if (canvas) {
                    const link = document.createElement('a');
                    link.download = `QR_${document.getElementById('qrId').textContent}.png`;
                    link.href = canvas.toDataURL();
                    link.click();
                }
            }
        });

        // Edit Visitor Modal
        document.getElementById("closeEditVisitor")?.addEventListener("click", () => closeModal(editVisitorModal));
        document.getElementById("cancelEditVisitor")?.addEventListener("click", () => closeModal(editVisitorModal));


        // Check In Modal
        document.getElementById("closeCheckIn")?.addEventListener("click", () => closeModal(checkInModal));
        document.getElementById("cancelCheckIn")?.addEventListener("click", () => closeModal(checkInModal));

        // Close modals when clicking outside (excluding add visitor modal)
        const modals = [viewVisitorModal, editVisitorModal, checkInModal]; // Removed addVisitorModal
        modals.forEach(modal => {
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === this) {
                        closeModal(this);
                    }
                });
            }
        });

        // Fetch visitor data
        async function fetchVisitor(id) {
            const url = `{{ route('visitor.get') }}?id=${encodeURIComponent(id)}`;
            const res = await fetch(url, { headers: { Accept: "application/json" } });
            if (!res.ok) return null;
            const data = await res.json();
            return data.visitor || null;
        }

        // Delegated clicks for visitor actions
        document.addEventListener("click", async (e) => {
            const vView = e.target.closest(".visitorViewBtn");
            const vEdit = e.target.closest(".visitorEditBtn");
            const vDel = null;
            const vIn = e.target.closest(".visitorCheckInBtn");

            if (!vView && !vEdit && !vIn) return;
            e.preventDefault();
            const id = (vView || vEdit || vDel || vIn)?.dataset.id;

            if (vView) {
                const v = await fetchVisitor(id);
                if (!v) return;
                document.getElementById("vvId").textContent = v.id || "";
                document.getElementById("vvName").textContent = v.name || "—";
                document.getElementById("vvCompany").textContent = v.company || "—";
                document.getElementById("vvType").textContent = v.visitor_type || "—";
                document.getElementById("vvHost").textContent = v.host || "—";
                document.getElementById("vvDept").textContent = v.host_department || "—";
                document.getElementById("vvDate").textContent = v.check_in_date || "—";
                document.getElementById("vvTime").textContent = v.check_in_time || "—";
                document.getElementById("vvStatus").textContent = (v.status || "scheduled").replace("_", " ");
                openModal(viewVisitorModal);
                return;
            }

            if (vEdit) {
                const v = await fetchVisitor(id);
                if (!v) return;
                document.getElementById("evId").value = v.id || "";
                document.getElementById("evCompany").value = v.company || "";
                document.getElementById("evType").value = v.visitor_type || "";
                document.getElementById("evPurpose").value = v.purpose || "";
                document.getElementById("evDate").value = v.check_in_date || "";
                document.getElementById("evTime").value = v.check_in_time || "";
                document.getElementById("evStatus").value = v.status || "scheduled";
                openModal(editVisitorModal);
                return;
            }


            if (vIn) {
                e.stopPropagation();
                document.getElementById("ciId").value = id;
                document.getElementById("ciText").textContent = id;
                openModal(checkInModal);
                return;
            }
        });

        // Form submissions
        const csrf = '{{ csrf_token() }}';

        // Add visitor form functionality removed - using QR Registration Modal instead

        // Edit visitor form
        const editVisitorForm = document.getElementById("editVisitorForm");
        if (editVisitorForm) {
            editVisitorForm.addEventListener("submit", async (e) => {
                e.preventDefault();
                const payload = {
                    id: document.getElementById("evId").value,
                    company: document.getElementById("evCompany").value || null,
                    visitor_type: document.getElementById("evType").value || null,
                    purpose: document.getElementById("evPurpose").value || null,
                    check_in_date: document.getElementById("evDate").value || null,
                    check_in_time: document.getElementById("evTime").value || null,
                    status: document.getElementById("evStatus").value || null,
                };

                try {
                    const res = await fetch(`{{ route('visitor.update') }}`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrf,
                            "Accept": "application/json",
                        },
                        body: JSON.stringify(payload),
                    });

                    if (res.ok) {
                        closeModal(editVisitorModal);
                        location.reload();
                    } else {
                        throw new Error("Failed to update visitor");
                    }
                } catch (err) {
                    Swal.fire({
                        icon: "error",
                        title: "Update failed",
                        text: err.message || "Please try again.",
                        confirmButtonColor: "#059669",
                    });
                }
            });
        }

        // Confirm check in
        document.getElementById("confirmCheckIn")?.addEventListener("click", async function () {
            const btn = this;
            const originalContent = btn.innerHTML;
            const id = document.getElementById("ciId").value;
            const now = new Date();
            const pad = (n) => n.toString().padStart(2, "0");
            const date = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;
            const time = `${pad(now.getHours())}:${pad(now.getMinutes())}`;

            // Disable button and show loading state
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Checking In...';

            try {
                const res = await fetch(`{{ route('visitor.update') }}`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrf,
                        "Accept": "application/json",
                    },
                    body: JSON.stringify({
                        id,
                        status: "checked_in",
                        check_in_date: date,
                        check_in_time: time
                    }),
                });

                if (!res.ok) throw new Error("Failed to check in visitor");

                closeModal(checkInModal);

                // Show success confirmation before reload
                await Swal.fire({
                    icon: "success",
                    title: "Check-in Successful",
                    text: "The visitor has been checked in.",
                    confirmButtonColor: "#059669",
                    timer: 1500,
                    showConfirmButton: false
                });

                location.reload();
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Check-in failed",
                    text: err.message || "Please try again.",
                    confirmButtonColor: "#059669",
                });

                // Reset button
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }
        });
        }); // End of main DOMContentLoaded listener
    </script>

    <!-- QR Registration Modal -->
    <div id="qrRegistrationModal" class="modal">
        <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full mx-4 max-h-[75vh] overflow-y-auto">
            <!-- Modal Header -->
            <div
                class="bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-600 px-6 py-5 rounded-t-2xl shadow-2xl relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div
                        class="absolute top-0 left-0 w-32 h-32 bg-emerald-400 rounded-full blur-3xl -translate-x-16 -translate-y-16">
                    </div>
                    <div class="absolute top-4 right-4 w-24 h-24 bg-cyan-400 rounded-full blur-2xl"></div>
                    <div class="absolute bottom-0 right-0 w-16 h-16 bg-teal-400 rounded-full blur-xl"></div>
                </div>

                <div class="relative flex items-center justify-between">
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-qrcode text-emerald-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-white flex items-center tracking-wide">QR Visitor
                                Registration</h3>
                            <p class="text-emerald-100 text-sm font-medium mt-1">Generate instant access passes</p>
                        </div>
                    </div>
                    <button onclick="closeQRRegistrationModal()"
                        class="text-white/90 hover:text-white transition-all duration-300 transform hover:scale-110 p-2 rounded-xl hover:bg-white/20">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-3">
                <form id="qrVisitorForm" class="space-y-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <div class="relative">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-user text-emerald-500 mr-2"></i>
                                    First Name
                                </label>
                                <input type="text" id="qrFirstName" required
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-white text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:ring-offset-2 transition-all duration-300 hover:border-emerald-400">
                            </div>
                        </div>
                        <div class="relative">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-user text-emerald-500 mr-2"></i>
                                Last Name
                            </label>
                            <input type="text" id="qrLastName" required
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-white text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:ring-offset-2 transition-all duration-300 hover:border-emerald-400">
                        </div>
                    </div>

                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-envelope text-emerald-500 mr-2"></i>
                            Email Address
                        </label>
                        <input type="email" id="qrEmail" required
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-white text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:ring-offset-2 transition-all duration-300 hover:border-emerald-400">
                    </div>

                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-phone text-emerald-500 mr-2"></i>
                            Phone Number
                        </label>
                        <input type="tel" id="qrPhone"
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-white text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:ring-offset-2 transition-all duration-300 hover:border-emerald-400"
                            placeholder="+63 917 1234 5678">
                    </div>

                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-building text-emerald-500 mr-2"></i>
                            Company/Organization
                        </label>
                        <input type="text" id="qrCompany"
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-white text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:ring-offset-2 transition-all duration-300 hover:border-emerald-400">
                    </div>

                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-comment-alt text-emerald-500 mr-2"></i>
                            Purpose of Visit
                        </label>
                        <textarea id="qrPurpose" rows="3"
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-white text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:ring-offset-2 transition-all duration-300 hover:border-emerald-400 resize-none"></textarea>
                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Access Level</label>
                            <select id="qrAccessLevel"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-white text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:ring-offset-2 transition-all duration-300 hover:border-emerald-400">
                                <option value="standard">Standard</option>
                                <option value="vip">VIP</option>
                                <option value="restricted">Restricted Areas</option>
                            </select>
                        </div>
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-calendar text-emerald-500 mr-2"></i>
                                Visit Date
                            </label>
                            <input type="date" id="qrVisitDate" required
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-white text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:ring-offset-2 transition-all duration-300 hover:border-emerald-400"
                                placeholder="Select date">
                        </div>
                    </div>

                    <!-- QR Code Display -->
                    <div id="qrDisplaySection" class="hidden">
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-bold text-gray-800 mb-4 text-center flex items-center">
                                <i class="fas fa-qrcode text-emerald-500 mr-2"></i>
                                Generated QR Code
                            </h4>
                            <div class="flex justify-center mb-4">
                                <div id="qrCodeDisplay"
                                    class="bg-white p-6 rounded-2xl border-2 border-gray-800 shadow-lg relative overflow-hidden">
                                    <!-- High contrast background for better scanning -->
                                    <div class="absolute inset-0 bg-white"></div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-emerald-50 to-teal-100 rounded-xl p-4 text-center">
                                <div class="text-sm text-gray-600 mb-2">
                                    <strong>Visitor ID:</strong> <span id="qrVisitorId"
                                        class="font-mono font-semibold text-emerald-600"></span>
                                </div>
                                <div class="text-sm text-gray-600 mb-2">
                                    <strong>Name:</strong> <span id="qrVisitorName"
                                        class="font-semibold text-gray-800"></span>
                                </div>
                                <div class="text-sm text-gray-600 mb-2">
                                    <strong>Access Level:</strong> <span id="qrAccessLevel"
                                        class="font-semibold px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs"></span>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <strong>Valid Until:</strong> <span id="qrValidDate"
                                        class="font-semibold text-gray-800"></span>
                                </div>
                            </div>
                            <div class="flex gap-3 justify-center mt-4">
                                <button type="button" onclick="downloadQRCode()"
                                    class="flex-1 bg-gradient-to-r from-gray-50 to-gray-100 hover:from-gray-100 hover:to-gray-200 text-gray-800 font-medium py-3 px-4 rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg border border-gray-200">
                                    <i class="fas fa-download mr-2"></i>
                                    Download
                                </button>
                                <button type="button" onclick="printQRCode()"
                                    class="flex-1 bg-gradient-to-r from-gray-50 to-gray-100 hover:from-gray-100 hover:to-gray-200 text-gray-800 font-medium py-3 px-4 rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg border border-gray-200">
                                    <i class="fas fa-printer mr-2"></i>
                                    Print
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeQRRegistrationModal()"
                            class="flex-1 py-3 text-center text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-300 transform hover:scale-105">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 text-sm font-bold text-white bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-600 rounded-xl shadow-lg hover:shadow-xl hover:from-emerald-600 hover:via-teal-500 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-300 transform hover:-translate-y-0.5">
                            <i class="fas fa-qrcode mr-2"></i>
                            Generate QR Code
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // QR Registration Modal Functions
        function openQRRegistrationModal() {
            const modal = document.getElementById('qrRegistrationModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';

            // Set today's date as default
            document.getElementById('qrVisitDate').valueAsDate = new Date();
        }

        function closeQRRegistrationModal() {
            const modal = document.getElementById('qrRegistrationModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
            resetQRForm();
        }

        function resetQRForm() {
            document.getElementById('qrVisitorForm').reset();
            document.getElementById('qrVisitDate').valueAsDate = new Date();
            document.getElementById('qrDisplaySection').classList.add('hidden');
            document.getElementById('qrCodeDisplay').innerHTML = '';
        }

        function generateQRVisitorCode() {
            const firstName = document.getElementById('qrFirstName').value;
            const lastName = document.getElementById('qrLastName').value;
            const email = document.getElementById('qrEmail').value;
            const phone = document.getElementById('qrPhone').value;
            const company = document.getElementById('qrCompany').value;
            const accessLevel = document.getElementById('qrAccessLevel').value;
            const visitDate = document.getElementById('qrVisitDate').value;
            const purpose = document.getElementById('qrPurpose').value;

            const formData = {
                first_name: firstName,
                last_name: lastName,
                email: email,
                phone: phone,
                company: company,
                access_level: accessLevel,
                visit_date: visitDate,
                purpose: purpose
            };

            fetch('{{ route('api.qr.register') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Save QR code to database
                        saveQRCodeToDatabase(data.visitor.id, data.visitor);
                        displayQRCode(data.visitor);
                        Swal.fire({
                            icon: 'success',
                            title: 'QR Code Generated',
                            text: 'Visitor QR code has been generated and saved!',
                            confirmButtonColor: '#059669'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Registration Failed',
                            text: data.message || 'Failed to generate QR code',
                            confirmButtonColor: '#dc2626'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred during registration.',
                        confirmButtonColor: '#dc2626'
                    });
                });
        }

        function saveQRCodeToDatabase(visitorId, visitor) {
            // Generate QR code data
            const qrData = JSON.stringify({
                id: visitor.code,
                name: visitor.name,
                access: visitor.visitor_type,
                date: visitor.check_in_date,
                hash: btoa(visitor.code + visitor.check_in_date).substring(0, 16)
            });

            // Generate QR code as base64
            const tempDiv = document.createElement('div');
            tempDiv.style.position = 'absolute';
            tempDiv.style.left = '-9999px';
            tempDiv.style.top = '-9999px';
            document.body.appendChild(tempDiv);

            new QRCode(tempDiv, {
                text: qrData,
                width: 300,
                height: 300,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H,
                margin: 2
            });

            // Wait for QR code to generate
            setTimeout(() => {
                const canvas = tempDiv.querySelector('canvas');
                if (canvas) {
                    const qrCodeBase64 = canvas.toDataURL('image/png').replace('data:image/png;base64,', '');

                    // Save QR code to database using existing route
                    fetch('{{ route('api.qr.register') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            visitor_id: visitorId,
                            qr_code: qrCodeBase64,
                            qr_data: qrData,
                            save_qr_only: true // Flag to indicate this is QR code save only
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log('QR Code saved to database successfully');
                                // Refresh the page to show updated QR codes from database
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                console.log('Database save failed, using localStorage fallback');
                                // Fallback to localStorage if database save fails
                                saveQRLocalStorage(visitorId, qrCodeBase64, qrData);
                                updateTableRowWithQR(visitorId, qrCodeBase64);
                            }
                        })
                        .catch(error => {
                            console.log('Database save error, using localStorage fallback:', error);
                            // Fallback to localStorage if database save fails
                            saveQRLocalStorage(visitorId, qrCodeBase64, qrData);
                            updateTableRowWithQR(visitorId, qrCodeBase64);
                        });
                }

                // Clean up
                document.body.removeChild(tempDiv);
            }, 500);
        }

        function saveQRLocalStorage(visitorId, qrCodeBase64, qrData) {
            // Store QR code in localStorage for display purposes
            const storedQRs = JSON.parse(localStorage.getItem('visitorQRCodes') || '{}');
            storedQRs[visitorId] = {
                qr_code: qrCodeBase64,
                qr_data: qrData,
                qr_generated_at: new Date().toISOString()
            };
            localStorage.setItem('visitorQRCodes', JSON.stringify(storedQRs));
            console.log('QR Code saved to localStorage fallback');
        }

        function updateTableRowWithQR(visitorId, qrCodeBase64) {
            // Find the QR code cell for this visitor and update display
            const qrCell = document.querySelector(`.qr-code-cell[data-visitor-id="${visitorId}"]`);
            if (qrCell) {
                const row = qrCell.closest('tr');
                const visitorName = row.querySelector('.text-sm.font-medium').textContent;

                qrCell.innerHTML = `
                    <div class="inline-block">
                        <img src="data:image/png;base64,${qrCodeBase64}" 
                             alt="QR Code" 
                             class="w-12 h-12 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow cursor-pointer"
                             onclick="showQRModal('${visitorId}', '${visitorName}', '${qrCodeBase64}')">
                    </div>
                `;

                console.log('QR Code updated for visitor:', visitorId);
            } else {
                console.log('QR Code cell not found for visitor:', visitorId);
            }
        }

        function displayQRCode(visitor) {
            // Enhanced QR data structure for universal compatibility
            const qrData = JSON.stringify({
                id: visitor.code,
                name: visitor.name,
                access: visitor.visitor_type,
                date: visitor.check_in_date,
                time: new Date().toISOString(),
                type: "VISITOR_QR",
                version: "2.0",
                hash: btoa(visitor.code + visitor.check_in_date + new Date().toISOString()).substring(0, 16)
            });

            document.getElementById('qrDisplaySection').classList.remove('hidden');
            document.getElementById('qrCodeDisplay').innerHTML = '';

            new QRCode(document.getElementById('qrCodeDisplay'), {
                text: qrData,
                width: 400,
                height: 400,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H,
                margin: 4,
                quietZone: 4
            });

            document.getElementById('qrVisitorId').textContent = visitor.code;
            document.getElementById('qrVisitorName').textContent = visitor.name;
            document.getElementById('qrAccessLevel').textContent = visitor.visitor_type.toUpperCase();
            document.getElementById('qrValidDate').textContent = new Date(visitor.check_in_date).toLocaleDateString();
        }

        // Load QR codes from localStorage on page load
        function loadStoredQRCodes() {
            const storedQRs = JSON.parse(localStorage.getItem('visitorQRCodes') || '{}');
            const qrCells = document.querySelectorAll('.qr-code-cell');

            qrCells.forEach(cell => {
                const visitorId = cell.getAttribute('data-visitor-id');
                if (visitorId && storedQRs[visitorId]) {
                    const row = cell.closest('tr');
                    const visitorName = row.querySelector('.text-sm.font-medium').textContent;

                    cell.innerHTML = `
                        <div class="inline-block">
                            <img src="data:image/png;base64,${storedQRs[visitorId].qr_code}" 
                                 alt="QR Code" 
                                 class="w-12 h-12 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow cursor-pointer"
                                 onclick="showQRModal('${visitorId}', '${visitorName}', '${storedQRs[visitorId].qr_code}')">
                        </div>
                    `;
                }
            });

            console.log('Loaded QR codes from localStorage:', Object.keys(storedQRs).length, 'codes found');
        }

        // Additional functions will be initialized in main DOMContentLoaded

        function downloadQRCode() {
            const canvas = document.querySelector('#qrCodeDisplay canvas');
            if (canvas) {
                // Create a new high-resolution canvas for better quality
                const highResCanvas = document.createElement('canvas');
                const ctx = highResCanvas.getContext('2d');

                // Set higher resolution (4x the original)
                const scale = 4;
                highResCanvas.width = canvas.width * scale;
                highResCanvas.height = canvas.height * scale;

                // Enable crisp image rendering
                ctx.imageSmoothingEnabled = false;
                ctx.scale(scale, scale);

                // Draw the original canvas content
                ctx.drawImage(canvas, 0, 0);

                // Add white background for better contrast
                const backgroundCanvas = document.createElement('canvas');
                const bgCtx = backgroundCanvas.getContext('2d');
                backgroundCanvas.width = highResCanvas.width;
                backgroundCanvas.height = highResCanvas.height;

                // Fill white background
                bgCtx.fillStyle = '#FFFFFF';
                bgCtx.fillRect(0, 0, backgroundCanvas.width, backgroundCanvas.height);

                // Draw QR code on top of white background
                bgCtx.drawImage(highResCanvas, 0, 0);

                // Create download link with high quality
                const link = document.createElement('a');
                const visitorName = document.getElementById('qrVisitorName').textContent || 'Visitor';
                link.download = `QR-${visitorName.replace(/\s+/g, '-')}-${Date.now()}.png`;
                link.href = backgroundCanvas.toDataURL('image/png', 1.0); // Maximum quality
                link.click();
            }
        }

        function printQRCode() {
            const canvas = document.querySelector('#qrCodeDisplay canvas');
            const qrImage = canvas ? canvas.toDataURL() : '';
            const visitorName = document.getElementById('qrVisitorName').textContent;
            const visitorId = document.getElementById('qrVisitorId').textContent;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head><title>Visitor Pass - ${visitorName}</title></head>
                <body style="font-family: Arial, sans-serif; text-align: center; padding: 40px;">
                    <h1>QR AI VISITOR PASS</h1>
                    <img src="${qrImage}" style="margin: 20px auto; display: block;">
                    <h2>${visitorName}</h2>
                    <p><strong>ID:</strong> ${visitorId}</p>
                    <p><strong>Date:</strong> ${new Date().toLocaleDateString()}</p>
                    <hr style="margin: 30px 0; border: 1px dashed #ccc;">
                    <p style="font-size: 12px; color: #666;">This pass must be presented at security. Valid only for date specified.</p>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }

        // End of JavaScript - All functionality consolidated into main DOMContentLoaded listener

        // Loading Screen Functions
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
            loadingScreen.classList.add('opacity-0');
            setTimeout(() => {
                loadingScreen.classList.add('hidden');
            }, 300);
        }

        // Auto-show loading screen on page load (for demonstration)
        window.addEventListener('load', () => {
            showLoadingScreen();
            // Hide after 3 seconds to demonstrate
            setTimeout(() => {
                hideLoadingScreen();
            }, 3000);
        });
    </script>

    <!-- Loading Screen -->
    <div id="loadingScreen" class="fixed inset-0 z-[9999] hidden">
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
                <h2 class="text-2xl font-bold mb-2">Loading Visitor Management</h2>
                <p class="text-white/80 text-sm mb-4">Preparing visitor registration system and loading data...</p>

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

    <!-- Global Loading Scripts -->
    @include('components.loading-scripts')
    @auth
        @include('partials.session-timeout-modal')
    @endauth
</body>

</html>