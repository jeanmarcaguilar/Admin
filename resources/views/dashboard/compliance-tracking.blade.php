@php
    // Get the authenticated user
    $user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrative</title>
    <link rel="icon" type="image/png"
        href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
    <link rel="shortcut icon" type="image/png"
        href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
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

        :root {
            --primary-color: #28644c;
            --primary-light: #3f8a56;
            --primary-dark: #1a4d38;
            --accent-color: #3f8a56;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --bg-light: #f9fafb;
            --bg-card: #ffffff;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
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

        body {
            font-family: "Inter", sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            color: var(--text-primary);
            line-height: 1.6;
            margin: 0;
            padding: 0;
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

        .modal>div:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            transition: box-shadow 0.2s ease-in-out;
        }

        #main-content {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            min-height: calc(100vh - 4rem);
            margin-left: auto;
            margin-right: auto;
            max-width: 1200px;
            width: 100%;
            padding: 0 1rem;
            transition: width 0.3s ease-in-out;
        }

        @media (min-width: 768px) {
            #main-content {
                width: calc(100% - 18rem);
            }

            #main-content.sidebar-closed {
                width: calc(100% - 4rem);
            }
        }

        .dashboard-container {
            max-width: 1152px;
            margin: 0 auto;
            transition: max-width 0.3s ease-in-out;
        }

        .hidden {
            display: none;
        }

        #sidebar.md\\:ml-0~#main-content .dashboard-container {
            max-width: 1152px;
        }

        .dashboard-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--bg-card);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }

        .dashboard-card:nth-child(1)::before {
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
        }

        .dashboard-card:nth-child(2)::before {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }

        .dashboard-card:nth-child(3)::before {
            background: linear-gradient(90deg, #10b981, #34d399);
        }

        .dashboard-card:nth-child(4)::before {
            background: linear-gradient(90deg, #8b5cf6, #a78bfa);
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            z-index: 2;
        }

        .table-row {
            transition: all 0.2s ease-in-out;
        }

        .table-row:hover {
            background-color: rgba(16, 185, 129, 0.05);
            transform: translateX(4px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }

        .submenu.show {
            max-height: 500px;
        }

        .dropdown-panel {
            opacity: 0;
            transform: translateY(10px);
            pointer-events: none;
            transition: all 0.2s ease-in-out;
        }

        .dropdown-panel:not(.hidden) {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
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

<body>

    <!-- Loading Screen (Login Style) -->
    <div id="loadingScreen" class="fixed inset-0 z-[9999]">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-600"></div>

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
                <h2 class="text-2xl font-bold mb-2">Loading Compliance Tracking</h2>
                <p class="text-white/80 text-sm mb-4">Preparing compliance tracking system and loading regulatory
                    data...</p>

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

        return false;
        };
        }
        })();
        </script>
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
                        <div class="font-bold text-gray-800 group-hover:text-emerald-600 transition-colors">
                            Microfinance Admin
                        </div>
                        <div
                            class="text-[11px] text-gray-500 font-semibold uppercase group-hover:text-emerald-600 transition-colors">
                            Administrative
                        </div>
                    </div>
                </a>
            </div>

            <!-- Sidebar content -->
            <div class="px-4 py-4 overflow-y-auto h-[calc(100%-4rem)] custom-scrollbar">
                <div class="text-xs font-bold text-gray-400 tracking-wider px-2">ADMINISTRATIVE DEPARTMENT</div>

                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="mt-3 flex items-center justify-between px-4 py-3 rounded-xl
               text-gray-700 hover:bg-green-50 hover:text-brand-primary
               transition-all duration-200 hover:translate-x-1 active:translate-x-0 active:scale-[0.99] font-semibold">
                    <span class="flex items-center gap-3">
                        <span class="inline-flex w-9 h-9 rounded-lg bg-emerald-50 items-center justify-center">📊</span>
                        Dashboard
                    </span>
                </a>

                <!-- Visitor Management Dropdown -->
                <button id="visitor-management-btn" class="mt-3 w-full flex items-center justify-between px-4 py-3 rounded-xl
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

                <div id="visitor-submenu" class="submenu mt-1">
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
                <button id="document-management-btn" class="mt-3 w-full flex items-center justify-between px-4 py-3 rounded-xl
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

                <div id="document-submenu" class="submenu mt-1">
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
                <button id="facilities-management-btn" class="mt-3 w-full flex items-center justify-between px-4 py-3 rounded-xl
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

                <div id="facilities-submenu" class="submenu mt-1">
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
                    </div>
                </div>

                <!-- Legal Management Dropdown -->
                <button id="legal-management-btn" class="mt-3 w-full flex items-center justify-between px-4 py-3 rounded-xl
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

                <div id="legal-submenu" class="submenu mt-1">
                    <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                        <a href="{{ route('case.management') }}"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1"
                            onclick="return openCaseWithConfGate(this.href)">
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
                        Adminstrative System
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN WRAPPER -->
        <div class="md:pl-72">
            <!-- TOP HEADER -->
            <header class="h-16 bg-white flex items-center justify-between px-4 sm:px-6 fixed top-0 left-0 right-0 z-40
                 shadow-[0_2px_8px_rgba(0,0,0,0.06)]">
                <!-- Border cover to hide sidebar line -->
                <div class="hidden md:block absolute left-0 top-0 h-16 w-[2px] bg-white"></div>

                <div class="flex items-center gap-3">
                    <button id="mobile-menu-btn"
                        class="md:hidden w-10 h-10 rounded-xl hover:bg-gray-100 active:bg-gray-200 transition flex items-center justify-center">
                        ☰
                    </button>
                </div>

                <div class="flex items-center gap-3 sm:gap-5">
                    <!-- Clock -->
                    <span id="real-time-clock"
                        class="text-xs font-bold text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                        --:--:--
                    </span>

                    <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>

                    <!-- User Profile Dropdown -->
                    <div class="relative">
                        <button id="userMenuBtn" class="flex items-center gap-3 focus:outline-none group rounded-xl px-2 py-2
                   hover:bg-gray-100 active:bg-gray-200 transition">
                            <div
                                class="w-10 h-10 rounded-full bg-white shadow group-hover:shadow-md transition-shadow overflow-hidden flex items-center justify-center border border-gray-100">
                                <div
                                    class="w-full h-full flex items-center justify-center font-bold text-emerald-600 bg-emerald-50">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="hidden md:flex flex-col items-start text-left">
                                <span
                                    class="text-sm font-bold text-gray-700 group-hover:text-emerald-600 transition-colors">
                                    {{ $user->name }}
                                </span>
                                <span
                                    class="text-[10px] text-gray-500 font-medium uppercase group-hover:text-emerald-600 transition-colors">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-emerald-600 transition-colors"
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
            <div class="dashboard-container pt-16">
                <div
                    class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-md border border-gray-100 p-8 space-y-6">
                    <!-- Page Header -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                        <div class="space-y-2">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-clipboard-check text-white text-xl"></i>
                                </div>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Compliance Tracking</h1>
                                    <p class="text-gray-600 text-sm font-medium">Monitor and manage all compliance
                                        requirements and deadlines</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <button id="addComplianceBtn" type="button"
                                onclick="if(window.__openAddCompliance){window.__openAddCompliance(event);}"
                                class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 flex items-center text-sm font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                <i class="fas fa-plus mr-2"></i>
                                Add New Compliance
                            </button>
                        </div>
                    </div>
                    <!-- Enhanced Stats Cards -->
                    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Active Compliances Card -->
                        <div
                            class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                            </div>
                            <div class="relative flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-gray-600 font-semibold text-sm mb-2">Active Compliances</p>
                                    <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['active'] ?? 0 }}</p>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            <i class="fas fa-clipboard-check mr-1"></i>
                                            Live
                                        </span>
                                        <span class="text-xs text-gray-500">Active</span>
                                    </div>
                                </div>
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="fas fa-clipboard-check text-white text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Review Card -->
                        <div
                            class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                            </div>
                            <div class="relative flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-gray-600 font-semibold text-sm mb-2">Pending Review</p>
                                    <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['pending'] ?? 0 }}</p>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-search mr-1"></i>
                                            Waiting
                                        </span>
                                        <span class="text-xs text-gray-500">Review</span>
                                    </div>
                                </div>
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="fas fa-search text-white text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Overdue Card -->
                        <div
                            class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-red-50 to-red-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                            </div>
                            <div class="relative flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-gray-600 font-semibold text-sm mb-2">Overdue</p>
                                    <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['overdue'] ?? 0 }}</p>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Late
                                        </span>
                                        <span class="text-xs text-gray-500">Urgent</span>
                                    </div>
                                </div>
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Completed Card -->
                        <div
                            class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-violet-50 to-violet-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                            </div>
                            <div class="relative flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-gray-600 font-semibold text-sm mb-2">Completed</p>
                                    <p class="font-bold text-3xl text-gray-900 mb-1">{{ $stats['completed'] ?? 0 }}</p>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Done
                                        </span>
                                        <span class="text-xs text-gray-500">Finished</span>
                                    </div>
                                </div>
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="fas fa-check-circle text-white text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div class="relative flex-1 max-w-lg">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400 text-sm"></i>
                                </div>
                                <input type="text" id="searchInput"
                                    class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm placeholder-gray-500 bg-gray-50 focus:bg-white transition-all duration-200"
                                    placeholder="Search compliances...">
                            </div>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <select id="filterStatus"
                                    class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50 focus:bg-white transition-all duration-200 min-w-[140px]">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                                <select id="filterType"
                                    class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50 focus:bg-white transition-all duration-200 min-w-[140px]">
                                    <option value="">All Types</option>
                                    <option value="legal">Legal</option>
                                    <option value="financial">Financial</option>
                                    <option value="hr">HR</option>
                                    <option value="safety">Safety</option>
                                    <option value="government">Government</option>
                                </select>
                                <button
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#2f855A]">
                                    <i class="fas fa-filter text-gray-600 mr-2"></i>
                                    <span>Filter</span>
                                </button>
                            </div>
                        </div>
                    </section>
                    <section class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Compliance Management</h3>

                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Compliance ID</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Title</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Type</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Due Date</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse((isset($complianceItems) ? $complianceItems : []) as $item)
                                        @php
                                            $daysUntilDue = now()->diffInDays($item->due_date, false);
                                            $daysText = $daysUntilDue > 0 ? "in {$daysUntilDue} days" : ($daysUntilDue == 0 ? "today" : abs($daysUntilDue) . " days overdue");
                                            $typeBadge = ucfirst($item->type);
                                            $statusBadge = ucfirst($item->status);
                                            $statusClasses = $item->status_badge_classes;
                                        @endphp
                                        <tr class="table-row" data-id="{{ $item->id }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $item->code }}</div>
                                                <div class="text-xs text-gray-500">Created:
                                                    {{ $item->created_at->format('Y-m-d') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $item->title }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $item->description ? Str::limit($item->description, 50) : 'No description' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $typeBadge }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $item->due_date->format('Y-m-d') }}
                                                </div>
                                                <div class="text-xs text-gray-500">{{ $daysText }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">{{ $statusBadge }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="#"
                                                    class="viewComplianceBtn text-[#2f855A] hover:text-[#1a4d38] mr-3"
                                                    data-tooltip="View" data-id="{{ $item->id }}"
                                                    data-code="{{ $item->code }}" data-title="{{ $item->title }}"
                                                    data-type="{{ $typeBadge }}" data-status="{{ $statusBadge }}"
                                                    data-due-date="{{ $item->due_date->format('Y-m-d') }}"
                                                    data-description="{{ $item->description }}"
                                                    data-responsible="{{ $item->responsible_person }}"
                                                    data-priority="{{ $item->priority }}"><i class="fas fa-eye"></i></a>
                                                <a href="#" class="editComplianceBtn text-blue-600 hover:text-blue-900 mr-3"
                                                    data-tooltip="Edit" data-id="{{ $item->id }}"
                                                    data-title="{{ $item->title }}" data-type="{{ $item->type }}"
                                                    data-status="{{ $item->status }}"
                                                    data-due-date="{{ $item->due_date->format('Y-m-d') }}"
                                                    data-description="{{ $item->description }}"
                                                    data-responsible="{{ $item->responsible_person }}"
                                                    data-priority="{{ $item->priority }}"><i class="fas fa-edit"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">No
                                                compliance
                                                items found.</td>
                                        </tr>
                                    @endforelse
                                    <tr class="table-row">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">CPL-2023-046</div>
                                            <div class="text-xs text-gray-500">Created: 2023-10-01</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">Government Permit Renewal
                                            </div>
                                            <div class="text-xs text-gray-500">Annual business permit compliance</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Government</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">2024-01-15</div>
                                            <div class="text-xs text-gray-500">in 45 days</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="#" class="text-[#2f855A] hover:text-[#1a4d38] mr-3"
                                                data-tooltip="View"><i class="fas fa-eye"></i></a>
                                            <a href="#" class="text-blue-600 hover:text-blue-900 mr-3"
                                                data-tooltip="Edit"><i class="fas fa-edit"></i></a>
                                                                                    </td>
                                    </tr>
                                    <tr class="table-row">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">CPL-2023-045</div>
                                            <div class="text-xs text-gray-500">Created: 2023-09-15</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">Annual Financial Report</div>
                                            <div class="text-xs text-gray-500">SEC Compliance</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Financial</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">2023-12-31</div>
                                            <div class="text-xs text-gray-500">in 89 days</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">On
                                                Track</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="#" class="text-[#2f855A] hover:text-[#1a4d38] mr-3"
                                                data-tooltip="View"><i class="fas fa-eye"></i></a>
                                            <a href="#" class="text-blue-600 hover:text-blue-900 mr-3"
                                                data-tooltip="Edit"><i class="fas fa-edit"></i></a>
                                                                                    </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <div
                            class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <a href="#"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </a>
                                <a href="#"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </a>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing <span class="font-medium">1</span> to <span
                                            class="font-medium">10</span> of
                                        <span class="font-medium">20</span> results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                        aria-label="Pagination">
                                        <a href="#"
                                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Previous</span>
                                            <i class="fas fa-chevron-left h-5 w-5"></i>
                                        </a>
                                        <a href="#" aria-current="page"
                                            class="z-10 bg-[#2f855A] border-[#2f855A] text-white relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            1
                                        </a>
                                        <a href="#"
                                            class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            2
                                        </a>
                                        <a href="#"
                                            class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            3
                                        </a>
                                        <a href="#"
                                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Next</span>
                                            <i class="fas fa-chevron-right h-5 w-5"></i>
                                        </a>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <!-- User Menu Dropdown -->
            <div id="userMenuDropdown"
                class="hidden fixed right-4 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200"
                style="top: 4rem; z-index: 9999;" role="menu" aria-labelledby="userMenuBtn"
                onclick="(function(e){ if(e&&e.stopPropagation) e.stopPropagation(); if(e&&e.stopImmediatePropagation) e.stopImmediatePropagation(); })(event)">
                <div class="py-4 px-6 border-b border-gray-100 text-center">
                    <div
                        class="w-14 h-14 rounded-full bg-[#28644c] text-white mx-auto flex items-center justify-center mb-2">
                        <i class="fas fa-user-circle text-3xl"></i>
                    </div>
                    <p class="font-semibold text-[#28644c]">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500">{{ ucfirst($user->role) }}</p>
                </div>
                <ul class="text-sm text-gray-700">
                    <li><button id="openProfileBtn"
                            class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none"
                            role="menuitem" tabindex="-1"><i class="fas fa-user-circle mr-2"></i> My Profile</button>
                    </li>
                    <li><button id="openAccountSettingsBtn"
                            class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none"
                            role="menuitem" tabindex="-1"><i class="fas fa-cog mr-2"></i> Account Settings</button></li>
                    <li><button id="openPrivacySecurityBtn"
                            class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none"
                            role="menuitem" tabindex="-1"><i class="fas fa-shield-alt mr-2"></i> Privacy &
                            Security</button>
                    </li>
                    <li><button id="openSignOutBtn"
                            class="w-full text-left flex items-center px-6 py-2 text-red-600 hover:bg-gray-100 focus:outline-none"
                            role="menuitem" tabindex="-1"><i class="fas fa-sign-out-alt mr-2"></i> Sign Out</button>
                    </li>
                </ul>
            </div>

            <script>
                // Profile modal controls (aligned with case-management behavior)
                if (typeof window.openProfileModal !== 'function') {
                    window.openProfileModal = function () {
                        try {
                            var m = document.getElementById('profileModal');
                            if (!m) return;
                            m.classList.remove('hidden');
                            m.style.display = 'flex';
                            document.body.style.overflow = 'hidden';
                            // close user menu
                            var d = document.getElementById('userMenuDropdown'); if (d) d.classList.add('hidden');
                            var b = document.getElementById('userMenuBtn'); if (b) b.setAttribute('aria-expanded', 'false');
                        } catch (e) { }
                    };
                }
                if (typeof window.closeProfileModal !== 'function') {
                    window.closeProfileModal = function () {
                        try {
                            var m = document.getElementById('profileModal');
                            if (!m) return;
                            m.classList.add('hidden');
                            m.style.display = 'none';
                            document.body.style.overflow = 'auto';
                        } catch (e) { }
                    };
                }
                // Bind buttons
                document.addEventListener('DOMContentLoaded', function () {
                    var op = document.getElementById('openProfileBtn');
                    if (op) { op.addEventListener('click', function (e) { e.stopPropagation(); if (window.openProfileModal) window.openProfileModal(); }); }
                    var cp = document.getElementById('closeProfileBtn');
                    if (cp) { cp.addEventListener('click', function () { if (window.closeProfileModal) window.closeProfileModal(); }); }
                    var cp2 = document.getElementById('closeProfileBtn2');
                    if (cp2) { cp2.addEventListener('click', function () { if (window.closeProfileModal) window.closeProfileModal(); }); }
                    // Close on Escape
                    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { if (window.closeProfileModal) window.closeProfileModal(); } });
                    // Close when clicking on backdrop
                    document.addEventListener('click', function (e) {
                        var modal = document.getElementById('profileModal');
                        if (modal && e.target === modal) { if (window.closeProfileModal) window.closeProfileModal(); }
                    });
                });
            </script>

            <script>
                // Account Settings modal
                if (typeof window.openAccountSettingsModal !== 'function') {
                    window.openAccountSettingsModal = function () {
                        try {
                            var m = document.getElementById('accountSettingsModal'); if (!m) return;
                            m.classList.remove('hidden'); m.style.display = 'flex'; document.body.style.overflow = 'hidden';
                            var d = document.getElementById('userMenuDropdown'); if (d) d.classList.add('hidden');
                            var b = document.getElementById('userMenuBtn'); if (b) b.setAttribute('aria-expanded', 'false');
                        } catch (e) { }
                    };
                }
                if (typeof window.closeAccountSettingsModal !== 'function') {
                    window.closeAccountSettingsModal = function () {
                        try {
                            var m = document.getElementById('accountSettingsModal'); if (!m) return;
                            m.classList.add('hidden'); m.style.display = 'none'; document.body.style.overflow = 'auto';
                        } catch (e) { }
                    };
                }

                // Privacy & Security modal
                if (typeof window.openPrivacySecurityModal !== 'function') {
                    window.openPrivacySecurityModal = function () {
                        try {
                            var m = document.getElementById('privacySecurityModal'); if (!m) return;
                            m.classList.remove('hidden'); m.style.display = 'flex'; document.body.style.overflow = 'hidden';
                            var d = document.getElementById('userMenuDropdown'); if (d) d.classList.add('hidden');
                            var b = document.getElementById('userMenuBtn'); if (b) b.setAttribute('aria-expanded', 'false');
                        } catch (e) { }
                    };
                }
                if (typeof window.closePrivacySecurityModal !== 'function') {
                    window.closePrivacySecurityModal = function () {
                        try {
                            var m = document.getElementById('privacySecurityModal'); if (!m) return;
                            m.classList.add('hidden'); m.style.display = 'none'; document.body.style.overflow = 'auto';
                        } catch (e) { }
                    };
                }

                // Sign Out modal
                if (typeof window.openSignOutModal !== 'function') {
                    window.openSignOutModal = function () {
                        try {
                            var m = document.getElementById('signOutModal'); if (!m) return;
                            m.classList.remove('hidden'); m.style.display = 'flex'; document.body.style.overflow = 'hidden';
                            var d = document.getElementById('userMenuDropdown'); if (d) d.classList.add('hidden');
                            var b = document.getElementById('userMenuBtn'); if (b) b.setAttribute('aria-expanded', 'false');
                        } catch (e) { }
                    };
                }
                if (typeof window.closeSignOutModal !== 'function') {
                    window.closeSignOutModal = function () {
                        try {
                            var m = document.getElementById('signOutModal'); if (!m) return;
                            m.classList.add('hidden'); m.style.display = 'none'; document.body.style.overflow = 'auto';
                        } catch (e) { }
                    };
                }

                // Bind openers from user menu
                document.addEventListener('DOMContentLoaded', function () {
                    var oas = document.getElementById('openAccountSettingsBtn');
                    if (oas) { oas.addEventListener('click', function (e) { e.stopPropagation(); if (window.openAccountSettingsModal) window.openAccountSettingsModal(); }); }
                    var ops = document.getElementById('openPrivacySecurityBtn');
                    if (ops) { ops.addEventListener('click', function (e) { e.stopPropagation(); if (window.openPrivacySecurityModal) window.openPrivacySecurityModal(); }); }
                    var oso = document.getElementById('openSignOutBtn');
                    if (oso) { oso.addEventListener('click', function (e) { e.stopPropagation(); if (window.openSignOutModal) window.openSignOutModal(); }); }

                    // Account Settings close buttons
                    var cas = document.getElementById('closeAccountSettingsBtn');
                    if (cas) { cas.addEventListener('click', function () { if (window.closeAccountSettingsModal) window.closeAccountSettingsModal(); }); }
                    var xas = document.getElementById('cancelAccountSettingsBtn');
                    if (xas) { xas.addEventListener('click', function () { if (window.closeAccountSettingsModal) window.closeAccountSettingsModal(); }); }

                    // Privacy & Security close buttons
                    var cps = document.getElementById('closePrivacySecurityBtn');
                    if (cps) { cps.addEventListener('click', function () { if (window.closePrivacySecurityModal) window.closePrivacySecurityModal(); }); }
                    var xps = document.getElementById('cancelPrivacySecurityBtn');
                    if (xps) { xps.addEventListener('click', function () { if (window.closePrivacySecurityModal) window.closePrivacySecurityModal(); }); }

                    // Sign Out close buttons
                    var cso = document.getElementById('cancelSignOutBtn');
                    if (cso) { cso.addEventListener('click', function () { if (window.closeSignOutModal) window.closeSignOutModal(); }); }
                    var cso2 = document.getElementById('cancelSignOutBtn2');
                    if (cso2) { cso2.addEventListener('click', function () { if (window.closeSignOutModal) window.closeSignOutModal(); }); }

                    // Escape closes any of the three modals
                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape') {
                            if (window.closeAccountSettingsModal) window.closeAccountSettingsModal();
                            if (window.closePrivacySecurityModal) window.closePrivacySecurityModal();
                            if (window.closeSignOutModal) window.closeSignOutModal();
                        }
                    });

                    // Backdrop click closes modals
                    document.addEventListener('click', function (e) {
                        var as = document.getElementById('accountSettingsModal'); if (as && e.target === as) { if (window.closeAccountSettingsModal) window.closeAccountSettingsModal(); }
                        var ps = document.getElementById('privacySecurityModal'); if (ps && e.target === ps) { if (window.closePrivacySecurityModal) window.closePrivacySecurityModal(); }
                        var so = document.getElementById('signOutModal'); if (so && e.target === so) { if (window.closeSignOutModal) window.closeSignOutModal(); }
                    });
                    var d = document.getElementById('userMenuDropdown');
                    var b = document.getElementById('userMenuBtn');
                    if (d) {
                        d.addEventListener('click', function (e) {
                            var t = e.target.closest('a,button');
                            if (t) {
                                d.classList.add('hidden');
                                if (b) b.setAttribute('aria-expanded', 'false');
                            }
                        });
                    }
                });
            </script>

            <!-- Profile, Account, Privacy, and Sign Out modals moved below to be outside main content for full-page overlay -->
            </main>
        </div>

        <!-- Profile Modal (moved outside main content) -->
        <div id="profileModal" class="modal hidden" aria-modal="true" role="dialog"
            aria-labelledby="profile-modal-title">
            <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
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
                    <form class="space-y-4">
                        <div>
                            <label for="emailProfile"
                                class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                            <input id="emailProfile" type="email" readonly value="{{ $user->email }}"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
                        </div>
                        <div>
                            <label for="phone" class="block text-xs font-semibold text-gray-700 mb-1">Phone</label>
                            <input id="phone" type="text" readonly value="+1234567890"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
                        </div>
                        <div>
                            <label for="department"
                                class="block text-xs font-semibold text-gray-700 mb-1">Department</label>
                            <input id="department" type="text" readonly value="Administrative"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
                        </div>
                        <div>
                            <label for="location"
                                class="block text-xs font-semibold text-gray-700 mb-1">Location</label>
                            <input id="location" type="text" readonly value="Manila, Philippines"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
                        </div>
                        <div>
                            <label for="joined" class="block text-xs font-semibold text-gray-700 mb-1">Joined</label>
                            <input id="joined" type="text" readonly value="{{ $user->created_at->format('F d, Y') }}"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
                        </div>
                        <div class="flex justify-end pt-2">
                            <button id="closeProfileBtn2" type="button"
                                class="bg-[#28644c] hover:bg-[#2f855A] text-white text-sm font-semibold rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Settings Modal (moved outside main content) -->
        <div id="accountSettingsModal" class="modal hidden" aria-modal="true" role="dialog"
            aria-labelledby="account-settings-modal-title">
            <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
                <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                    <h3 id="account-settings-modal-title" class="font-semibold text-sm text-gray-900 select-none">
                        Account
                        Settings</h3>
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

        <!-- Privacy & Security Modal (moved outside main content) -->
        <div id="privacySecurityModal" class="modal hidden" aria-modal="true" role="dialog"
            aria-labelledby="privacy-security-modal-title">
            <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
                <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                    <h3 id="privacy-security-modal-title" class="font-semibold text-sm text-gray-900 select-none">
                        Privacy &
                        Security</h3>
                    <button id="closePrivacySecurityBtn" type="button"
                        class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200"
                        aria-label="Close">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                <div class="px-8 pt-6 pb-8">
                    <form class="space-y-4 text-xs text-gray-700" action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <fieldset>
                            <legend class="font-semibold mb-2 select-none">Change Password</legend>
                            <label class="block mb-1 font-normal select-none" for="current-password">Current
                                Password</label>
                            <input
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]"
                                id="current-password" name="current_password" type="password" />
                            <label class="block mt-3 mb-1 font-normal select-none" for="new-password">New
                                Password</label>
                            <input
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]"
                                id="new-password" name="new_password" type="password" />
                            <label class="block mt-3 mb-1 font-normal select-none" for="confirm-password">Confirm New
                                Password</label>
                            <input
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]"
                                id="confirm-password" name="confirm_password" type="password" />
                        </fieldset>
                        <fieldset>
                            <legend class="font-semibold mb-1 select-none">Two-Factor Authentication</legend>
                            <p class="text-[10px] mb-1 select-none">Enhance your account security</p>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-[#2f855A] font-semibold select-none">Status:
                                    Enabled</span>
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
                        <div class="flex justify-end space-x-3 pt-2">
                            <button
                                class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200"
                                id="cancelPrivacySecurityBtn" type="button">Cancel</button>
                            <button
                                class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200"
                                type="submit">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sign Out Modal (moved outside main content) -->
        <div id="signOutModal" class="modal hidden" aria-modal="true" role="dialog"
            aria-labelledby="sign-out-modal-title">
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

        <!-- Add Compliance Modal (moved outside main content) -->
        <div id="addComplianceModal" class="modal hidden" aria-modal="true" role="dialog"
            aria-labelledby="add-compliance-modal-title">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                    <h3 id="add-compliance-modal-title" class="text-lg font-medium text-gray-900">Add New Compliance
                    </h3>
                    <button id="closeAddComplianceModal" type="button"
                        class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200"
                        aria-label="Close">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
                <div class="p-6">
                    <form id="addComplianceForm" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <label for="complianceTitle"
                                    class="block text-sm font-medium text-gray-700 mb-1">Compliance
                                    Title *</label>
                                <input type="text" id="complianceTitle" name="title"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]"
                                    required>
                            </div>
                            <div>
                                <label for="complianceType" class="block text-sm font-medium text-gray-700 mb-1">Type
                                    *</label>
                                <select id="complianceType" name="type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]"
                                    required>
                                    <option value="">Select a type</option>
                                    <option value="legal">Legal</option>
                                    <option value="financial">Financial</option>
                                    <option value="hr">HR</option>
                                    <option value="safety">Safety</option>
                                    <option value="government">Government</option>
                                    <option value="environmental">Environmental</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label for="dueDate" class="block text-sm font-medium text-gray-700 mb-1">Due Date
                                    *</label>
                                <input type="date" id="dueDate" name="due_date"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]"
                                    required>
                            </div>
                            <div>
                                <label for="responsiblePerson"
                                    class="block text-sm font-medium text-gray-700 mb-1">Responsible Person</label>
                                <input type="text" id="responsiblePerson" name="responsible_person"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]">
                            </div>
                            <div>
                                <label for="priority"
                                    class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                <select id="priority" name="priority"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="description" name="description" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" id="cancelAddCompliance"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#2f855A] hover:bg-[#28644c] focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">
                                Save Compliance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View Compliance Modal (moved outside main content) -->
        <div id="viewComplianceModal" class="modal hidden" aria-modal="true" role="dialog"
            aria-labelledby="view-compliance-modal-title">
            <div class="bg-white rounded-lg shadow-lg w-[380px] max-w-full mx-4" role="document">
                <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                    <h3 id="view-compliance-modal-title" class="font-semibold text-sm text-gray-900 select-none">
                        Compliance
                        Details</h3>
                    <button id="closeViewComplianceModal" type="button"
                        class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200"
                        aria-label="Close">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                <div class="px-8 pt-6 pb-8 text-xs text-gray-700 space-y-2">
                    <div><span class="font-semibold">Code:</span> <span id="viewComplianceCode"></span></div>
                    <div><span class="font-semibold">Title:</span> <span id="viewComplianceTitle"></span></div>
                    <div><span class="font-semibold">Type:</span> <span id="viewComplianceType"></span></div>
                    <div><span class="font-semibold">Status:</span> <span id="viewComplianceStatus"></span></div>
                    <div><span class="font-semibold">Due Date:</span> <span id="viewComplianceDueDate"></span></div>
                    <div><span class="font-semibold">Responsible:</span> <span id="viewComplianceResponsible"></span>
                    </div>
                    <div><span class="font-semibold">Priority:</span> <span id="viewCompliancePriority"></span></div>
                    <div><span class="font-semibold">Description:</span> <span id="viewComplianceDescription"></span>
                    </div>
                    <div class="pt-4 text-right">
                        <button id="closeViewComplianceModal2" type="button"
                            class="bg-[#28644c] hover:bg-[#2f855A] text-white text-sm font-semibold rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Compliance Modal (moved outside main content) -->
        <div id="editComplianceModal" class="modal hidden" aria-modal="true" role="dialog"
            aria-labelledby="edit-compliance-modal-title">
            <div class="bg-white rounded-lg shadow-lg w-[380px] max-w-full mx-4" role="document">
                <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                    <h3 id="edit-compliance-modal-title" class="font-semibold text-sm text-gray-900 select-none">Edit
                        Compliance</h3>
                    <button id="closeEditComplianceModal" type="button"
                        class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200"
                        aria-label="Close">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                <div class="px-8 pt-6 pb-8">
                    <form id="editComplianceForm" class="space-y-3 text-xs text-gray-700">
                        <input type="hidden" id="editComplianceId">
                        <div>
                            <label for="editComplianceTitle" class="block mb-1 font-semibold">Title</label>
                            <input type="text" id="editComplianceTitle" name="title"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]"
                                required>
                        </div>
                        <div>
                            <label for="editComplianceType" class="block mb-1 font-semibold">Type</label>
                            <select id="editComplianceType" name="type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]"
                                required>
                                <option value="legal">Legal</option>
                                <option value="financial">Financial</option>
                                <option value="hr">HR</option>
                                <option value="safety">Safety</option>
                                <option value="government">Government</option>
                                <option value="environmental">Environmental</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label for="editComplianceStatus" class="block mb-1 font-semibold">Status</label>
                            <select id="editComplianceStatus" name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]"
                                required>
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="overdue">Overdue</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div>
                            <label for="editComplianceDueDate" class="block mb-1 font-semibold">Due Date</label>
                            <input type="date" id="editComplianceDueDate" name="due_date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]"
                                required>
                        </div>
                        <div>
                            <label for="editComplianceResponsible" class="block mb-1 font-semibold">Responsible
                                Person</label>
                            <input type="text" id="editComplianceResponsible" name="responsible_person"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]">
                        </div>
                        <div>
                            <label for="editCompliancePriority" class="block mb-1 font-semibold">Priority</label>
                            <select id="editCompliancePriority" name="priority"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div>
                            <label for="editComplianceDescription" class="block mb-1 font-semibold">Description</label>
                            <textarea id="editComplianceDescription" name="description" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]"></textarea>
                        </div>
                        <div class="flex justify-end space-x-3 pt-2">
                            <button type="button" id="cancelEditCompliance"
                                class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
                            <button type="submit"
                                class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200">Save
                                Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Profile Modal -->
        <div id="profileModal" class="modal hidden" aria-modal="true" role="dialog"
            aria-labelledby="profile-modal-title">
            <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
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
                    <form class="space-y-4">
                        <div>
                            <label for="emailProfile"
                                class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                            <input id="emailProfile" type="email" readonly value="{{ $user->email }}"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
                        </div>
                        <div>
                            <label for="phone" class="block text-xs font-semibold text-gray-700 mb-1">Phone</label>
                            <input id="phone" type="text" readonly value="+1234567890"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
                        </div>
                        <div>
                            <label for="department"
                                class="block text-xs font-semibold text-gray-700 mb-1">Department</label>
                            <input id="department" type="text" readonly value="Administrative"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
                        </div>
                        <div>
                            <label for="location"
                                class="block text-xs font-semibold text-gray-700 mb-1">Location</label>
                            <input id="location" type="text" readonly value="Manila, Philippines"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
                        </div>
                        <div>
                            <label for="joined" class="block text-xs font-semibold text-gray-700 mb-1">Joined</label>
                            <input id="joined" type="text" readonly value="{{ $user->created_at->format('F d, Y') }}"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
                        </div>
                        <div class="flex justify-end pt-2">
                            <button id="closeProfileBtn2" type="button"
                                class="bg-[#28644c] hover:bg-[#2f855A] text-white text-sm font-semibold rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Settings Modal -->
        <div id="accountSettingsModal" class="modal hidden" aria-modal="true" role="dialog"
            aria-labelledby="account-settings-modal-title">
            <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
                <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                    <h3 id="account-settings-modal-title" class="font-semibold text-sm text-gray-900 select-none">
                        Account
                        Settings</h3>
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

        <!-- Privacy & Security Modal -->
        <div id="privacySecurityModal" class="modal hidden" aria-modal="true" role="dialog"
            aria-labelledby="privacy-security-modal-title">
            <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
                <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                    <h3 id="privacy-security-modal-title" class="font-semibold text-sm text-gray-900 select-none">
                        Privacy &
                        Security</h3>
                    <button id="closePrivacySecurityBtn" type="button"
                        class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200"
                        aria-label="Close">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                <div class="px-8 pt-6 pb-8">
                    <form class="space-y-4 text-xs text-gray-700" action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <fieldset>
                            <legend class="font-semibold mb-2 select-none">Change Password</legend>
                            <label class="block mb-1 font-normal select-none" for="current-password">Current
                                Password</label>
                            <input
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]"
                                id="current-password" name="current_password" type="password" />
                            <label class="block mt-3 mb-1 font-normal select-none" for="new-password">New
                                Password</label>
                            <input
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]"
                                id="new-password" name="new_password" type="password" />
                            <label class="block mt-3 mb-1 font-normal select-none" for="confirm-password">Confirm New
                                Password</label>
                            <input
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]"
                                id="confirm-password" name="confirm_password" type="password" />
                        </fieldset>
                        <fieldset>
                            <legend class="font-semibold mb-1 select-none">Two-Factor Authentication</legend>
                            <p class="text-[10px] mb-1 select-none">Enhance your account security</p>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-[#2f855A] font-semibold select-none">Status:
                                    Enabled</span>
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
                        <div class="flex justify-end space-x-3 pt-2">
                            <button
                                class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200"
                                id="cancelPrivacySecurityBtn" type="button">Cancel</button>
                            <button
                                class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200"
                                type="submit">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sign Out Modal -->
        <div id="signOutModal" class="modal hidden" aria-modal="true" role="dialog"
            aria-labelledby="sign-out-modal-title">
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
        </main>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Element references
            const sidebar = document.getElementById("sidebar");
            const mainContent = document.getElementById("main-content");
            const toggleBtn = document.getElementById("toggle-btn");
            const overlay = document.getElementById("overlay");
            const notificationBtn = document.getElementById("notificationBtn");
            const notificationDropdown = document.getElementById("notificationDropdown");
            const userMenuBtn = document.getElementById("userMenuBtn");
            const userMenuDropdown = document.getElementById("userMenuDropdown");
            const profileModal = document.getElementById("profileModal");
            const openProfileBtn = document.getElementById("openProfileBtn");
            const closeProfileBtn = document.getElementById("closeProfileBtn");
            const closeProfileBtn2 = document.getElementById("closeProfileBtn2");
            const openAccountSettingsBtn = document.getElementById("openAccountSettingsBtn");
            const accountSettingsModal = document.getElementById("accountSettingsModal");
            const closeAccountSettingsBtn = document.getElementById("closeAccountSettingsBtn");
            const cancelAccountSettingsBtn = document.getElementById("cancelAccountSettingsBtn");
            const openPrivacySecurityBtn = document.getElementById("openPrivacySecurityBtn");
            const privacySecurityModal = document.getElementById("privacySecurityModal");
            const closePrivacySecurityBtn = document.getElementById("closePrivacySecurityBtn");
            const cancelPrivacySecurityBtn = document.getElementById("cancelPrivacySecurityBtn");
            const signOutModal = document.getElementById("signOutModal");
            const cancelSignOutBtn = document.getElementById("cancelSignOutBtn");
            const cancelSignOutBtn2 = document.getElementById("cancelSignOutBtn2");
            const openSignOutBtn = document.getElementById("openSignOutBtn");
            const addComplianceBtn = document.getElementById("addComplianceBtn");
            const addComplianceModal = document.getElementById("addComplianceModal");
            const closeAddComplianceModal = document.getElementById("closeAddComplianceModal");
            const cancelAddCompliance = document.getElementById("cancelAddCompliance");
            const addComplianceForm = document.getElementById("addComplianceForm");
            const tooltipTriggers = document.querySelectorAll('[data-tooltip]');

            // Initialize sidebar state
            if (window.innerWidth >= 768) {
                sidebar.classList.remove("-ml-72");
                mainContent.classList.add("md:ml-72", "sidebar-open");
            } else {
                sidebar.classList.add("-ml-72");
                mainContent.classList.remove("md:ml-72", "sidebar-open");
                mainContent.classList.add("sidebar-closed");
            }

            // Toggle sidebar
            function toggleSidebar() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.toggle("md:-ml-72");
                    mainContent.classList.toggle("md:ml-72");
                    mainContent.classList.toggle("sidebar-open");
                    mainContent.classList.toggle("sidebar-closed");
                } else {
                    sidebar.classList.toggle("-ml-72");
                    overlay.classList.toggle("hidden");
                    document.body.style.overflow = sidebar.classList.contains("-ml-72") ? "" : "hidden";
                    mainContent.classList.toggle("sidebar-open", !sidebar.classList.contains("-ml-72"));
                    mainContent.classList.toggle("sidebar-closed", sidebar.classList.contains("-ml-72"));
                }
            }

            // Dropdown functionality
            function bindDropdownListeners() {
                const dropdownToggles = document.querySelectorAll('.has-dropdown > div');
                dropdownToggles.forEach((toggle) => {
                    // Skip if already bound
                    if (toggle.__dropdownBound) return;
                    toggle.__dropdownBound = true;

                    // Set accessibility attributes
                    const menu = toggle.nextElementSibling;
                    const isOpen = menu && !menu.classList.contains('hidden');
                    toggle.setAttribute('role', 'button');
                    toggle.setAttribute('tabindex', '0');
                    toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

                    // Enhance menu accessibility
                    if (menu) {
                        menu.setAttribute('role', 'menu');
                        menu.querySelectorAll('a').forEach(link => link.setAttribute('role', 'menuitem'));
                    }

                    // Click handler (avoid double-toggle if inline onclick exists)
                    if (!toggle.getAttribute('onclick')) {
                        toggle.addEventListener("click", (e) => {
                            e.stopPropagation();
                            window.toggleSidebarDropdown(toggle);
                        });
                    }

                    // Keyboard handler
                    toggle.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            window.toggleSidebarDropdown(toggle);
                        }
                    });
                });
            }

            // User menu functionality
            function bindUserMenuListeners() {
                // If the early navbar script already bound handlers, avoid double-binding
                if (window.__complianceMenusBound) return;
                if (!userMenuBtn || userMenuBtn.__userMenuBound) return;
                userMenuBtn.__userMenuBound = true;

                // Set accessibility attributes
                userMenuBtn.setAttribute('role', 'button');
                userMenuBtn.setAttribute('tabindex', '0');
                userMenuBtn.setAttribute('aria-expanded', userMenuDropdown && !userMenuDropdown.classList.contains('hidden') ? 'true' : 'false');

                // Enhance dropdown accessibility
                if (userMenuDropdown) {
                    userMenuDropdown.setAttribute('role', 'menu');
                    userMenuDropdown.querySelectorAll('a').forEach(link => link.setAttribute('role', 'menuitem'));
                }

                // Click handler
                userMenuBtn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    if (userMenuDropdown) {
                        userMenuDropdown.classList.toggle("hidden");
                        userMenuBtn.setAttribute('aria-expanded', userMenuDropdown.classList.contains('hidden') ? 'false' : 'true');
                        notificationDropdown.classList.add("hidden");
                        closeAllModals();
                        closeAllSidebarDropdowns(); // Close sidebar dropdowns when opening user menu
                    }
                });

                // Keyboard handler
                userMenuBtn.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        if (userMenuDropdown) {
                            userMenuDropdown.classList.toggle("hidden");
                            userMenuBtn.setAttribute('aria-expanded', userMenuDropdown.classList.contains('hidden') ? 'false' : 'true');
                            notificationDropdown.classList.add("hidden");
                            closeAllModals();
                            closeAllSidebarDropdowns();
                        }
                    }
                });
            }

            function closeAllSidebarDropdowns(except = null) {
                const dropdownToggles = document.querySelectorAll('.has-dropdown > div');
                dropdownToggles.forEach((toggle) => {
                    if (toggle === except) return;
                    const menu = toggle.nextElementSibling;
                    const chev = toggle.querySelector('.bx-chevron-down');
                    if (menu && !menu.classList.contains('hidden')) {
                        menu.classList.add('hidden');
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                    if (chev) chev.classList.remove('rotate-180');
                });
            }

            // Global function for inline onclick handlers
            window.toggleSidebarDropdown = function (el) {
                if (!el) return;
                const menu = el.nextElementSibling;
                const chev = el.querySelector('.bx-chevron-down');
                closeAllSidebarDropdowns(el);
                if (menu) {
                    menu.classList.toggle('hidden');
                    el.setAttribute('aria-expanded', menu.classList.contains('hidden') ? 'false' : 'true');
                }
                if (chev) chev.classList.toggle('rotate-180');
                // Close user menu and notification dropdown when sidebar dropdown is toggled
                if (userMenuDropdown) userMenuDropdown.classList.add('hidden');
                if (userMenuBtn) userMenuBtn.setAttribute('aria-expanded', 'false');
                if (notificationDropdown) notificationDropdown.classList.add('hidden');
            };

            // Close dropdowns and user menu on outside click or Escape key
            document.addEventListener("click", (e) => {
                if (!e.target.closest('.has-dropdown')) closeAllSidebarDropdowns();
                if (!e.target.closest('#userMenuDropdown') && !e.target.closest('#userMenuBtn')) {
                    if (userMenuDropdown && !userMenuDropdown.classList.contains('hidden')) {
                        userMenuDropdown.classList.add('hidden');
                        if (userMenuBtn) userMenuBtn.setAttribute('aria-expanded', 'false');
                    }
                }
                if (!e.target.closest('#notificationDropdown') && !e.target.closest('#notificationBtn')) {
                    if (notificationDropdown && !notificationDropdown.classList.contains('hidden')) {
                        notificationDropdown.classList.add('hidden');
                    }
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeAllSidebarDropdowns();
                    if (userMenuDropdown && !userMenuDropdown.classList.contains('hidden')) {
                        userMenuDropdown.classList.add('hidden');
                        if (userMenuBtn) userMenuBtn.setAttribute('aria-expanded', 'false');
                    }
                    if (notificationDropdown && !notificationDropdown.classList.contains('hidden')) {
                        notificationDropdown.classList.add('hidden');
                    }
                    if (addComplianceModal && !addComplianceModal.classList.contains('hidden')) {
                        addComplianceModal.classList.add('hidden');
                        addComplianceModal.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                }
            });

            // Auto-expand sidebar dropdown based on URL
            function autoExpandDropdowns() {
                try {
                    const currentPath = window.location.pathname.replace(/\/$/, '');
                    const links = document.querySelectorAll('#sidebar .dropdown-menu a');
                    links.forEach((link) => {
                        let linkPath;
                        try {
                            linkPath = new URL(link.href, window.location.origin).pathname;
                        } catch (_) {
                            linkPath = link.getAttribute('href') || '';
                        }
                        if (linkPath) linkPath = linkPath.replace(/\/$/, '');
                        const isMatch = linkPath && (
                            currentPath === linkPath ||
                            currentPath.endsWith(linkPath) ||
                            linkPath.endsWith(currentPath)
                        );
                        if (isMatch) {
                            const menu = link.closest('.dropdown-menu');
                            if (menu) {
                                menu.classList.remove('hidden');
                                const toggle = menu.previousElementSibling;
                                if (toggle) {
                                    toggle.setAttribute('aria-expanded', 'true');
                                    const chev = toggle.querySelector('.bx-chevron-down');
                                    if (chev) chev.classList.add('rotate-180');
                                }
                            }
                            link.classList.add('bg-white/30');
                        }
                    });
                } catch (err) {
                    console.error('Error in auto-expand dropdowns:', err);
                }
            }

            // Sidebar and modal event listeners
            if (overlay) {
                overlay.addEventListener("click", () => {
                    sidebar.classList.add("-ml-72");
                    overlay.classList.add("hidden");
                    document.body.style.overflow = "";
                    mainContent.classList.remove("sidebar-open");
                    mainContent.classList.add("sidebar-closed");
                    closeAllSidebarDropdowns();
                    if (userMenuDropdown) userMenuDropdown.classList.add('hidden');
                    if (userMenuBtn) userMenuBtn.setAttribute('aria-expanded', 'false');
                    if (notificationDropdown) notificationDropdown.classList.add('hidden');
                });
            }

            if (toggleBtn) toggleBtn.addEventListener("click", toggleSidebar);

            if (notificationBtn) {
                notificationBtn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    notificationDropdown.classList.toggle("hidden");
                    if (userMenuDropdown) userMenuDropdown.classList.add("hidden");
                    if (userMenuBtn) userMenuBtn.setAttribute("aria-expanded", "false");
                    closeAllModals();
                    closeAllSidebarDropdowns();
                });
            }

            // Hide user menu when clicking any actionable item inside it
            if (userMenuDropdown) {
                userMenuDropdown.addEventListener('click', (e) => {
                    const actionable = e.target && e.target.closest('button, a');
                    if (actionable) {
                        userMenuDropdown.classList.add('hidden');
                        if (userMenuBtn) userMenuBtn.setAttribute('aria-expanded', 'false');
                    }
                }, true); // capture phase to run before inline stopImmediatePropagation
            }

            // Function to close all modals
            function closeAllModals(except = null) {
                const modals = [profileModal, accountSettingsModal, privacySecurityModal, signOutModal, addComplianceModal];
                modals.forEach((modal) => {
                    if (modal && modal !== except && !modal.classList.contains('hidden')) {
                        modal.classList.add("hidden");
                        modal.classList.remove("active");
                        document.body.style.overflow = '';
                    }
                });
            }

            // Modal event listeners
            if (openProfileBtn) {
                openProfileBtn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    profileModal.classList.remove("hidden");
                    profileModal.classList.add("active");
                    if (userMenuDropdown) userMenuDropdown.classList.add("hidden");
                    if (userMenuBtn) userMenuBtn.setAttribute("aria-expanded", "false");
                    closeAllModals(profileModal);
                    closeAllSidebarDropdowns();
                });
            }

            if (closeProfileBtn) closeProfileBtn.addEventListener("click", () => closeModal(profileModal));
            if (closeProfileBtn2) closeProfileBtn2.addEventListener("click", () => closeModal(profileModal));

            if (openAccountSettingsBtn) {
                openAccountSettingsBtn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    accountSettingsModal.classList.remove("hidden");
                    accountSettingsModal.classList.add("active");
                    if (userMenuDropdown) userMenuDropdown.classList.add("hidden");
                    if (userMenuBtn) userMenuBtn.setAttribute("aria-expanded", "false");
                    closeAllModals(accountSettingsModal);
                    closeAllSidebarDropdowns();
                });
            }

            if (closeAccountSettingsBtn) closeAccountSettingsBtn.addEventListener("click", () => closeModal(accountSettingsModal));
            if (cancelAccountSettingsBtn) cancelAccountSettingsBtn.addEventListener("click", () => closeModal(accountSettingsModal));

            if (openPrivacySecurityBtn) {
                openPrivacySecurityBtn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    privacySecurityModal.classList.remove("hidden");
                    privacySecurityModal.classList.add("active");
                    if (userMenuDropdown) userMenuDropdown.classList.add("hidden");
                    if (userMenuBtn) userMenuBtn.setAttribute("aria-expanded", "false");
                    closeAllModals(privacySecurityModal);
                    closeAllSidebarDropdowns();
                });
            }

            if (closePrivacySecurityBtn) closePrivacySecurityBtn.addEventListener("click", () => closeModal(privacySecurityModal));
            if (cancelPrivacySecurityBtn) cancelPrivacySecurityBtn.addEventListener("click", () => closeModal(privacySecurityModal));

            if (openSignOutBtn) {
                openSignOutBtn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    signOutModal.classList.remove("hidden");
                    signOutModal.classList.add("active");
                    if (userMenuDropdown) userMenuDropdown.classList.add("hidden");
                    if (userMenuBtn) userMenuBtn.setAttribute("aria-expanded", "false");
                    closeAllModals(signOutModal);
                    closeAllSidebarDropdowns();
                });
            }

            if (cancelSignOutBtn) cancelSignOutBtn.addEventListener("click", () => closeModal(signOutModal));
            if (cancelSignOutBtn2) cancelSignOutBtn2.addEventListener("click", () => closeModal(signOutModal));

            // Add New Compliance Modal
            if (addComplianceBtn) {
                addComplianceBtn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    if (addComplianceModal) {
                        addComplianceModal.classList.remove("hidden");
                        addComplianceModal.classList.add("active");
                        document.body.style.overflow = 'hidden';
                        if (userMenuDropdown) userMenuDropdown.classList.add("hidden");
                        if (userMenuBtn) userMenuBtn.setAttribute("aria-expanded", "false");
                        if (notificationDropdown) notificationDropdown.classList.add("hidden");
                        closeAllModals(addComplianceModal);
                        closeAllSidebarDropdowns();
                        const firstInput = addComplianceModal.querySelector('input');
                        if (firstInput) firstInput.focus();
                    }
                });
            }

            if (closeAddComplianceModal) {
                closeAddComplianceModal.addEventListener("click", () => closeModal(addComplianceModal));
            }
            if (cancelAddCompliance) {
                cancelAddCompliance.addEventListener("click", () => closeModal(addComplianceModal));
            }

            // Helper function to close a modal
            function closeModal(modal) {
                if (modal) {
                    modal.classList.add("hidden");
                    modal.classList.remove("active");
                    document.body.style.overflow = '';
                }
            }

            // Add Compliance Form Submission
            if (addComplianceForm) {
                addComplianceForm.addEventListener("submit", async (e) => {
                    e.preventDefault();
                    const submitButton = e.target.querySelector('button[type="submit"]');
                    submitButton.disabled = true;

                    const title = document.getElementById('complianceTitle').value.trim();
                    const type = document.getElementById('complianceType').value;
                    const dueDate = document.getElementById('dueDate').value;

                    if (!title || !type || !dueDate) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Validation Error',
                            text: 'Please fill in all required fields (Title, Type, Due Date).',
                            confirmButtonColor: '#2f855a'
                        });
                        submitButton.disabled = false;
                        return;
                    }

                    const formData = new FormData(e.target);
                    const data = Object.fromEntries(formData.entries());

                    try {
                        const resp = await fetch('{{ route('compliance.create') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams(data)
                        });

                        const result = await resp.json();
                        if (result.success && result.compliance) {
                            storeComplianceInBackup(result.compliance);
                            addComplianceModal.classList.add("hidden");
                            addComplianceModal.classList.remove("active");
                            document.body.style.overflow = '';
                            addComplianceForm.reset();
                            addComplianceToTable(result.compliance);
                            updateStats();

                            Swal.fire({
                                icon: 'success',
                                title: 'Compliance Added',
                                text: 'The compliance has been added successfully.',
                                confirmButtonColor: '#2f855a'
                            });
                        } else {
                            throw new Error(result.message || 'Failed to add compliance.');
                        }
                    } catch (error) {
                        console.error('Error adding compliance:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'An error occurred while adding the compliance.',
                            confirmButtonColor: '#2f855a'
                        });
                    } finally {
                        submitButton.disabled = false;
                    }
                });
            }

            // Function to add compliance to table dynamically
            function addComplianceToTable(compliance) {
                const tbody = document.querySelector('tbody');
                const emptyRow = tbody.querySelector('tr td[colspan="6"]');
                if (emptyRow) emptyRow.closest('tr').remove();

                const today = new Date();
                const dueDate = new Date(compliance.due_date);
                const daysUntilDue = Math.round((dueDate - today) / (1000 * 60 * 60 * 24));
                const daysText = daysUntilDue > 0 ? `in ${daysUntilDue} days` : (daysUntilDue === 0 ? 'today' : `${Math.abs(daysUntilDue)} days overdue`);

                const statusClasses = {
                    active: 'bg-green-100 text-green-800',
                    pending: 'bg-yellow-100 text-yellow-800',
                    overdue: 'bg-red-100 text-red-800',
                    completed: 'bg-blue-100 text-blue-800'
                }[compliance.status] || 'bg-gray-100 text-gray-800';

                const typeClasses = {
                    legal: 'bg-blue-100 text-blue-800',
                    financial: 'bg-blue-100 text-blue-800',
                    hr: 'bg-green-100 text-green-800',
                    safety: 'bg-orange-100 text-orange-800',
                    government: 'bg-purple-100 text-purple-800',
                    environmental: 'bg-teal-100 text-teal-800',
                    other: 'bg-gray-100 text-gray-800'
                }[compliance.type] || 'bg-gray-100 text-gray-800';

                const tr = document.createElement('tr');
                tr.className = 'table-row';
                tr.dataset.id = compliance.id;
                tr.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">${compliance.code || 'CPL-' + compliance.id}</div>
                <div class="text-xs text-gray-500">Created: ${new Date(compliance.created_at).toISOString().split('T')[0]}</div>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900">${compliance.title}</div>
                <div class="text-xs text-gray-500">${compliance.description ? compliance.description.substring(0, 50) + (compliance.description.length > 50 ? '...' : '') : 'No description'}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${typeClasses}">${compliance.type.charAt(0).toUpperCase() + compliance.type.slice(1)}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${compliance.due_date.split('T')[0]}</div>
                <div class="text-xs text-gray-500">${daysText}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClasses}">${compliance.status.charAt(0).toUpperCase() + compliance.status.slice(1)}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <a href="#" class="viewComplianceBtn text-[#2f855A] hover:text-[#1a4d38] mr-3" data-tooltip="View" 
                   data-id="${compliance.id}" data-code="${compliance.code || 'CPL-' + compliance.id}" 
                   data-title="${compliance.title}" data-type="${compliance.type.charAt(0).toUpperCase() + compliance.type.slice(1)}" 
                   data-status="${compliance.status.charAt(0).toUpperCase() + compliance.status.slice(1)}" 
                   data-due-date="${compliance.due_date.split('T')[0]}" 
                   data-description="${compliance.description || ''}" 
                   data-responsible="${compliance.responsible_person || ''}" 
                   data-priority="${compliance.priority || 'Medium'}"><i class="fas fa-eye"></i></a>
                <a href="#" class="text-blue-600 hover:text-blue-900 mr-3" 
                   data-tooltip="Edit" 
                   data-id="${compliance.id}" 
                   data-code="${compliance.code}" 
                   data-title="${compliance.title}" 
                   data-type="${compliance.type}" 
                   data-status="${compliance.status}" 
                   data-due-date="${compliance.due_date || ''}" 
                   data-description="${compliance.description || ''}" 
                   data-responsible="${compliance.responsible_person || ''}" 
                   data-priority="${compliance.priority || 'Medium'}"><i class="fas fa-edit"></i></a>
            </td>
        `;
                tbody.insertBefore(tr, tbody.firstChild);
                attachEventListenersToTable();
            }

            // Function to update stats
            function updateStats() {
                const tbody = document.querySelector('tbody');
                const rows = tbody.querySelectorAll('tr.table-row');
                let activeCount = 0;
                let pendingCount = 0;

                rows.forEach(row => {
                    const status = row.querySelector('td:nth-child(5) span').textContent.toLowerCase();
                    if (status === 'active' || status === 'on track') activeCount++;
                    if (status === 'pending') pendingCount++;
                });

                const activeStat = document.querySelector('.dashboard-card:nth-child(1) .font-extrabold');
                const pendingStat = document.querySelector('.dashboard-card:nth-child(2) .font-extrabold');
                if (activeStat) activeStat.textContent = activeCount;
                if (pendingStat) pendingStat.textContent = pendingCount;
            }

            // Store compliance in localStorage as backup
            function storeComplianceInBackup(compliance) {
                const existingData = JSON.parse(localStorage.getItem('compliance_backup') || '[]');
                existingData.push(compliance);
                localStorage.setItem('compliance_backup', JSON.stringify(existingData));
            }

            // Handle View/Edit buttons
            const viewComplianceModal = document.getElementById('viewComplianceModal');
            const editComplianceModal = document.getElementById('editComplianceModal');
            const closeViewComplianceModal = document.getElementById('closeViewComplianceModal');
            const closeViewComplianceModal2 = document.getElementById('closeViewComplianceModal2');
            const closeEditComplianceModal = document.getElementById('closeEditComplianceModal');
            const cancelEditCompliance = document.getElementById('cancelEditCompliance');

            function openViewModal(data) {
                document.getElementById('viewComplianceCode').textContent = data.code;
                document.getElementById('viewComplianceTitle').textContent = data.title;
                document.getElementById('viewComplianceType').textContent = data.type;
                document.getElementById('viewComplianceStatus').textContent = data.status;
                document.getElementById('viewComplianceDueDate').textContent = data.dueDate;
                document.getElementById('viewComplianceResponsible').textContent = data.responsible || 'Not assigned';
                document.getElementById('viewCompliancePriority').textContent = data.priority || 'Medium';
                document.getElementById('viewComplianceDescription').textContent = data.description || 'No description';
                viewComplianceModal.classList.remove('hidden');
                viewComplianceModal.classList.add('active');
            }

            function openEditModal(data) {
                document.getElementById('editComplianceId').value = data.id;
                document.getElementById('editComplianceTitle').value = data.title;
                document.getElementById('editComplianceType').value = data.type.toLowerCase();
                document.getElementById('editComplianceStatus').value = data.status.toLowerCase();
                document.getElementById('editComplianceDueDate').value = data.dueDate;
                document.getElementById('editComplianceResponsible').value = data.responsible || '';
                document.getElementById('editCompliancePriority').value = data.priority.toLowerCase() || 'medium';
                document.getElementById('editComplianceDescription').value = data.description || '';
                editComplianceModal.classList.remove('hidden');
                editComplianceModal.classList.add('active');
            }


            function attachEventListenersToTable() {
                const viewBtns = document.querySelectorAll('.viewComplianceBtn');
                const editBtns = document.querySelectorAll('.editComplianceBtn');

                viewBtns.forEach(btn => {
                    btn.replaceWith(btn.cloneNode(true));
                });
                editBtns.forEach(btn => {
                    btn.replaceWith(btn.cloneNode(true));
                });

                const newViewBtns = document.querySelectorAll('.viewComplianceBtn');
                const newEditBtns = document.querySelectorAll('.editComplianceBtn');

                newViewBtns.forEach(btn => btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const d = e.currentTarget.dataset;
                    openViewModal({
                        code: d.code,
                        title: d.title,
                        type: d.type,
                        status: d.status,
                        dueDate: d.dueDate,
                        responsible: d.responsible,
                        priority: d.priority,
                        description: d.description
                    });
                }));

                newEditBtns.forEach(btn => btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const d = e.currentTarget.dataset;
                    openEditModal({
                        id: d.id,
                        title: d.title,
                        type: d.type,
                        status: d.status,
                        dueDate: d.dueDate,
                        responsible: d.responsible,
                        priority: d.priority,
                        description: d.description
                    });
                }));
            }

            // Modal close handlers
            if (closeViewComplianceModal) closeViewComplianceModal.addEventListener('click', () => closeModal(viewComplianceModal));
            if (closeViewComplianceModal2) closeViewComplianceModal2.addEventListener('click', () => closeModal(viewComplianceModal));
            if (viewComplianceModal) viewComplianceModal.querySelector('div').addEventListener('click', (e) => e.stopPropagation());

            if (closeEditComplianceModal) closeEditComplianceModal.addEventListener('click', () => closeModal(editComplianceModal));
            if (cancelEditCompliance) cancelEditCompliance.addEventListener('click', () => closeModal(editComplianceModal));
            if (editComplianceModal) editComplianceModal.querySelector('div').addEventListener('click', (e) => e.stopPropagation());


            // Edit form submissions
            if (document.getElementById('editComplianceForm')) {
                document.getElementById('editComplianceForm').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const formData = new FormData(e.target);
                    const data = Object.fromEntries(formData.entries());
                    data.id = document.getElementById('editComplianceId').value;

                    try {
                        const resp = await fetch('{{ route('compliance.update') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams(data)
                        });
                        const result = await resp.json();
                        if (result.success) {
                            closeModal(editComplianceModal);
                            await loadComplianceData();
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated',
                                text: 'Compliance updated successfully.',
                                confirmButtonColor: '#2f855a'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: result.message || 'Update failed.',
                                confirmButtonColor: '#2f855a'
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating the compliance.',
                            confirmButtonColor: '#2f855a'
                        });
                    }
                });
            }

            // Load compliance data
            async function loadComplianceData() {
                try {
                    const resp = await fetch('{{ route('document.compliance.tracking') }}', {
                        method: 'GET',
                        headers: { 'Accept': 'text/html' }
                    });
                    if (resp.ok) {
                        const html = await resp.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newTableBody = doc.querySelector('tbody');
                        if (newTableBody) {
                            const currentTableBody = document.querySelector('tbody');
                            currentTableBody.innerHTML = newTableBody.innerHTML;
                            attachEventListenersToTable();
                            updateStatsFromServer(doc);
                        }
                    }
                } catch (error) {
                    console.error('Error loading compliance data:', error);
                    loadBackupData();
                }
            }

            function updateStatsFromServer(doc) {
                const statsCards = doc.querySelectorAll('.dashboard-card');
                const currentStatsCards = document.querySelectorAll('.dashboard-card');
                if (statsCards.length === currentStatsCards.length) {
                    statsCards.forEach((card, index) => {
                        const statValue = card.querySelector('.font-extrabold');
                        if (statValue && currentStatsCards[index]) {
                            const currentStatValue = currentStatsCards[index].querySelector('.font-extrabold');
                            if (currentStatValue) currentStatValue.textContent = statValue.textContent;
                        }
                    });
                }
            }

            function loadBackupData() {
                const backupData = JSON.parse(localStorage.getItem('compliance_backup') || '[]');
                if (backupData.length > 0) {
                    const tbody = document.querySelector('tbody');
                    const emptyRow = tbody.querySelector('tr td[colspan="6"]');
                    if (emptyRow) emptyRow.closest('tr').remove();
                    backupData.forEach(compliance => addComplianceToTable(compliance));
                }
            }

            // Initialize compliance tracking
            function initComplianceTracking() {
                bindDropdownListeners();
                bindUserMenuListeners();
                autoExpandDropdowns();
                attachEventListenersToTable();
                loadComplianceData();

                // Close modals and dropdowns on outside click
                window.addEventListener("click", (e) => {
                    if (!e.target.closest('#notificationDropdown') && !e.target.closest('#notificationBtn')) {
                        if (notificationDropdown) notificationDropdown.classList.add("hidden");
                    }
                    if (!e.target.closest('#userMenuDropdown') && !e.target.closest('#userMenuBtn')) {
                        if (userMenuDropdown) userMenuDropdown.classList.add("hidden");
                        if (userMenuBtn) userMenuBtn.setAttribute("aria-expanded", "false");
                    }
                    if (!e.target.closest('#profileModal') && !e.target.closest('#openProfileBtn')) {
                        closeModal(profileModal);
                    }
                    if (!e.target.closest('#accountSettingsModal') && !e.target.closest('#openAccountSettingsBtn')) {
                        closeModal(accountSettingsModal);
                    }
                    if (!e.target.closest('#privacySecurityModal') && !e.target.closest('#openPrivacySecurityBtn')) {
                        closeModal(privacySecurityModal);
                    }
                    if (!e.target.closest('#signOutModal') && !e.target.closest('#openSignOutBtn')) {
                        closeModal(signOutModal);
                    }
                    if (!e.target.closest('#addComplianceModal') && !e.target.closest('#addComplianceBtn')) {
                        closeModal(addComplianceModal);
                    }
                });

                // Stop propagation for modal content
                [profileModal, accountSettingsModal, privacySecurityModal, signOutModal, addComplianceModal].forEach(modal => {
                    if (modal) {
                        const content = modal.querySelector("div");
                        if (content) content.addEventListener("click", (e) => e.stopPropagation());
                    }
                });

                // Resize handler
                window.addEventListener("resize", () => {
                    if (window.innerWidth >= 768) {
                        sidebar.classList.remove("-ml-72");
                        overlay.classList.add("hidden");
                        document.body.style.overflow = "";
                        mainContent.classList.add("md:ml-72", "sidebar-open");
                        mainContent.classList.remove("sidebar-closed");
                    } else {
                        sidebar.classList.add("-ml-72");
                        mainContent.classList.remove("md:ml-72", "sidebar-open");
                        mainContent.classList.add("sidebar-closed");
                        overlay.classList.add("hidden");
                        document.body.style.overflow = "";
                    }
                    closeAllSidebarDropdowns();
                    if (userMenuDropdown) userMenuDropdown.classList.add('hidden');
                    if (userMenuBtn) userMenuBtn.setAttribute('aria-expanded', 'false');
                    if (notificationDropdown) notificationDropdown.classList.add('hidden');
                });

                // Tooltip handlers
                tooltipTriggers.forEach(trigger => {
                    trigger.addEventListener('mouseenter', e => {
                        const tooltip = document.createElement('div');
                        tooltip.className = 'absolute z-50 px-2 py-1 text-xs text-white bg-gray-900 rounded shadow-lg';
                        tooltip.textContent = e.target.dataset.tooltip;
                        document.body.appendChild(tooltip);
                        const rect = e.target.getBoundingClientRect();
                        tooltip.style.top = `${rect.bottom + window.scrollY + 5}px`;
                        tooltip.style.left = `${rect.left + window.scrollX}px`;
                        e.target._tooltip = tooltip;
                    });
                    trigger.addEventListener('mouseleave', e => {
                        if (e.target._tooltip) {
                            e.target._tooltip.remove();
                            delete e.target._tooltip;
                        }
                    });
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initComplianceTracking);
            } else {
                initComplianceTracking();
            }
        });



        // Sidebar and Header JavaScript from dashboard.blade.php
        document.addEventListener('DOMContentLoaded', function () {
            // Clock functionality
            function updateClock() {
                const now = new Date();
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

            // Mobile menu toggle
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');

            if (mobileMenuBtn && sidebar && sidebarOverlay) {
                mobileMenuBtn.addEventListener('click', function () {
                    sidebar.classList.toggle('-translate-x-full');
                    sidebarOverlay.classList.toggle('hidden');
                    sidebarOverlay.classList.toggle('opacity-0');
                });

                sidebarOverlay.addEventListener('click', function () {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                    sidebarOverlay.classList.add('opacity-0');
                });
            }

            // Sidebar dropdown functionality
            const dropdownButtons = [
                { btn: 'visitor-management-btn', submenu: 'visitor-submenu', arrow: 'visitor-arrow' },
                { btn: 'document-management-btn', submenu: 'document-submenu', arrow: 'document-arrow' },
                { btn: 'facilities-management-btn', submenu: 'facilities-submenu', arrow: 'facilities-arrow' },
                { btn: 'legal-management-btn', submenu: 'legal-submenu', arrow: 'legal-arrow' }
            ];

            dropdownButtons.forEach(({ btn, submenu, arrow }) => {
                const button = document.getElementById(btn);
                const submenuElement = document.getElementById(submenu);
                const arrowElement = document.getElementById(arrow);

                if (button && submenuElement && arrowElement) {
                    button.addEventListener('click', function () {
                        const isOpen = submenuElement.classList.contains('show');

                        // Close all other submenus
                        dropdownButtons.forEach(({ submenu: otherSubmenu, arrow: otherArrow }) => {
                            const otherSubmenuElement = document.getElementById(otherSubmenu);
                            const otherArrowElement = document.getElementById(otherArrow);
                            if (otherSubmenuElement && otherArrowElement && otherSubmenuElement !== submenuElement) {
                                otherSubmenuElement.classList.remove('show');
                                otherArrowElement.style.transform = 'rotate(0deg)';
                            }
                        });

                        // Toggle current submenu
                        if (isOpen) {
                            submenuElement.classList.remove('show');
                            arrowElement.style.transform = 'rotate(0deg)';
                        } else {
                            submenuElement.classList.add('show');
                            arrowElement.style.transform = 'rotate(180deg)';
                        }
                    });
                }
            });

            // User menu dropdown
            const userMenuBtn = document.getElementById('userMenuBtn');
            const userMenuDropdown = document.getElementById('userMenuDropdown');

            if (userMenuBtn && userMenuDropdown) {
                userMenuBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    userMenuDropdown.classList.toggle('hidden');
                });

                document.addEventListener('click', function () {
                    userMenuDropdown.classList.add('hidden');
                });

                userMenuDropdown.addEventListener('click', function (e) {
                    e.stopPropagation();
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