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

  <!-- Favicon -->
  <link rel="icon" type="image/png"
    href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
  <link rel="shortcut icon" type="image/png"
    href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
  <link rel="apple-touch-icon" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">

  <!-- Tailwind CSS -->
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

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Box Icons -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Chart.js -->
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
      z-index: 100;
      align-items: center;
      justify-content: center;
    }

    .modal.active {
      display: flex;
    }

    .chart-container {
      animation: fadeIn 0.5s ease-in-out;
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

    .dashboard-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-lg);
      z-index: 2;
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

    .submenu {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease-in-out;
    }

    .submenu.show {
      max-height: 500px;
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
        <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.11/dist/dotlottie-wc.js" type="module"></script>
        <dotlottie-wc src="https://lottie.host/5378ba62-7703-4273-a14a-3a999385cf7f/s5Vm9nkLqj.lottie"
          style="width: 300px;height: 300px" autoplay loop></dotlottie-wc>
      </div>

      <!-- Loading Text -->
      <div class="text-center text-white">
        <h2 class="text-2xl font-bold mb-2">Loading Dashboard</h2>
        <p class="text-white/80 text-sm mb-4">Preparing your admin dashboard and gathering real-time data...</p>

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
        <a href="{{ route('admin.dashboard') }}" class="mt-3 flex items-center justify-between px-4 py-3 rounded-xl bg-brand-primary text-white shadow
               transition-all duration-200 active:scale-[0.99]">
          <span class="flex items-center gap-3 font-semibold">
            <span class="inline-flex w-9 h-9 rounded-lg bg-white/15 items-center justify-center">📊</span>
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
          <svg id="visitor-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>

        <div id="visitor-submenu" class="submenu mt-1">
          <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
            <a href="{{ route('visitors.registration') }}"
              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
              <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
              </svg>
              Visitors Registration
            </a>
            <a href="{{ route('checkinout.tracking') }}"
              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
              <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
              </svg>
              Check In/Out Tracking
            </a>
            <a href="{{ route('visitor.history.records') }}"
              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
              <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
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
          <svg id="document-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>

        <div id="document-submenu" class="submenu mt-1">
          <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
            <a href="{{ route('document.upload.indexing') }}"
              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
              <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
              </svg>
              Document Upload & Indexing
            </a>

            <a href="{{ route('document.access.control.permissions') }}"
              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
              <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
              </svg>
              Access Control & Permissions
            </a>
            <a href="{{ route('document.archival.retention.policy') }}"
              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
              <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
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
          <svg id="facilities-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>

        <div id="facilities-submenu" class="submenu mt-1">
          <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">

            <a href="{{ route('scheduling.calendar') }}"
              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
              <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
              </svg>
              Scheduling & Calendar Integrations
            </a>
            <a href="{{ route('approval.workflow') }}"
              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
              <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
              </svg>
              Approval Workflow
            </a>
            <a href="{{ route('reservation.history') }}"
              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
              <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
              </svg>
              Reservation History
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
              </svg>
              Case Management
            </a>
            <a href="{{ route('contract.management') }}"
              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
              <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
              </svg>
              Contract Management
            </a>
            <a href="{{ route('compliance.tracking') }}"
              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
              <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
              </svg>
              Compliance Tracking
            </a>
            <a href="{{ route('deadline.hearing.alerts') }}"
              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
              <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
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
      <header class="h-16 bg-white flex items-center justify-between px-4 sm:px-6 relative
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
                <div class="w-full h-full flex items-center justify-center font-bold text-brand-primary bg-emerald-50">
                  {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
              </div>
              <div class="hidden md:flex flex-col items-start text-left">
                <span class="text-sm font-bold text-gray-700 group-hover:text-brand-primary transition-colors">
                  {{ $user->name }}
                </span>
                <span
                  class="text-[10px] text-gray-500 font-medium uppercase group-hover:text-brand-primary transition-colors">
                  {{ ucfirst($user->role) }}
                </span>
              </div>
              <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-primary transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
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
                <li><button id="openAccountSettingsBtn"
                    class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none"><i
                      class="fas fa-cog mr-2"></i> Account Settings</button></li>
                <li><button id="openPrivacySecurityBtn"
                    class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none"><i
                      class="fas fa-shield-alt mr-2"></i> Privacy & Security</button></li>
                <li><button id="openSignOutBtn"
                    class="w-full text-left flex items-center px-6 py-2 text-red-600 hover:bg-gray-100 focus:outline-none"><i
                      class="fas fa-sign-out-alt mr-2"></i> Sign Out</button></li>
              </ul>
            </div>
          </div>
        </div>
      </header>


      <!-- MAIN CONTENT -->
      <main class="p-6">
        <div class="dashboard-container max-w-7xl mx-auto">
          <div
            class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-md border border-gray-100 p-8 space-y-6">
            <h2 class="text-[#1a4d38] font-bold text-xl mb-1"><i class="bx bx-grid-alt mr-2 align-middle"></i>Admin
              Dashboard</h2>
            @php
              // Use real statistics from database (passed from route)
              $checkedInCount = $stats['checked_in_visitors'] ?? 0;
              $totalVisitors = $stats['total_visitors'] ?? 0;
              $documentsCount = $stats['uploaded_documents'] ?? 0;
              $activeCases = $stats['active_cases'] ?? 0;
            @endphp
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
              <!-- Visitors Checked In Card -->
              <div
                class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                <div
                  class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                </div>
                <div class="relative flex justify-between items-start">
                  <div class="flex-1">
                    <p class="text-gray-600 font-semibold text-sm mb-2">Visitors Checked In</p>
                    <p class="font-bold text-3xl text-gray-900 mb-1">{{ $checkedInCount }}</p>
                    <div class="flex items-center gap-2">
                      <span
                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        <i class="bx bx-trending-up mr-1"></i>
                        {{ $totalVisitors > 0 ? round(($checkedInCount / $totalVisitors) * 100, 1) : 0 }}%
                      </span>
                      <span class="text-xs text-gray-500">of {{ $totalVisitors }} total</span>
                    </div>
                  </div>
                  <div
                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <i class="bx bx-log-in text-white text-xl"></i>
                  </div>
                </div>
              </div>

              <!-- Total Visitors Card -->
              <div
                class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                <div
                  class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                </div>
                <div class="relative flex justify-between items-start">
                  <div class="flex-1">
                    <p class="text-gray-600 font-semibold text-sm mb-2">Total Visitors</p>
                    <p class="font-bold text-3xl text-gray-900 mb-1">{{ $totalVisitors }}</p>
                    <div class="flex items-center gap-2">
                      <span
                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="bx bx-user-plus mr-1"></i>
                        All Time
                      </span>
                      <span class="text-xs text-gray-500">Session data</span>
                    </div>
                  </div>
                  <div
                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <i class="bx bx-group text-white text-xl"></i>
                  </div>
                </div>
              </div>

              <!-- Uploaded Documents Card -->
              <div
                class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                <div
                  class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-amber-50 to-amber-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                </div>
                <div class="relative flex justify-between items-start">
                  <div class="flex-1">
                    <p class="text-gray-600 font-semibold text-sm mb-2">Uploaded Documents</p>
                    <p class="font-bold text-3xl text-gray-900 mb-1">{{ $documentsCount }}</p>
                    <div class="flex items-center gap-2">
                      <span
                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                        <i class="bx bx-file mr-1"></i>
                        Files
                      </span>
                      <span class="text-xs text-gray-500">Document Management</span>
                    </div>
                  </div>
                  <div
                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <i class="bx bx-file text-white text-xl"></i>
                  </div>
                </div>
              </div>

              <!-- Active Cases Card -->
              <a href="{{ route('case.management') }}" class="block focus:outline-none"
                aria-label="View Case Management" onclick="return openCaseWithConfGate(this.href)">
                <div
                  class="group relative bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden cursor-pointer">
                  <div
                    class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-violet-50 to-violet-100 rounded-full -mr-10 -mt-10 opacity-50 group-hover:opacity-75 transition-opacity">
                  </div>
                  <div class="relative flex justify-between items-start">
                    <div class="flex-1">
                      <p class="text-gray-600 font-semibold text-sm mb-2">Active Cases</p>
                      <p class="font-bold text-3xl text-gray-900 mb-1">{{ $activeCases }}</p>
                      <div class="flex items-center gap-2">
                        <span
                          class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800">
                          <i class="bx bx-briefcase mr-1"></i>
                          Legal
                        </span>
                        <span class="text-xs text-gray-500">Legal Management</span>
                      </div>
                    </div>
                    <div
                      class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                      <i class="bx bx-briefcase text-white text-xl"></i>
                    </div>
                  </div>
                </div>
              </a>
            </section>
            <section class="grid grid-cols-1 gap-6 chart-container">
              <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100 flex flex-col w-full">
                <div class="flex items-center justify-between mb-6">
                  <div>
                    <h3 class="font-bold text-xl text-gray-900 mb-1">Module Overview</h3>
                    <p class="text-sm text-gray-600">Comprehensive statistics across all system modules</p>
                  </div>
                  <div class="flex items-center gap-2">
                    <span
                      class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                      <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                      Live Data
                    </span>
                  </div>
                </div>
                <div class="relative" style="height: 400px;">
                  <canvas id="dashboardChart"></canvas>
                </div>
                <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
                  <div class="text-center p-3 bg-emerald-50 rounded-lg">
                    <div class="w-3 h-3 bg-emerald-500 rounded-full mx-auto mb-2"></div>
                    <p class="text-xs font-medium text-gray-600">Visitors</p>
                    <p class="text-sm font-bold text-gray-900">{{ $checkedInCount }}</p>
                  </div>
                  <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <div class="w-3 h-3 bg-blue-500 rounded-full mx-auto mb-2"></div>
                    <p class="text-xs font-medium text-gray-600">Total</p>
                    <p class="text-sm font-bold text-gray-900">{{ $totalVisitors }}</p>
                  </div>
                  <div class="text-center p-3 bg-amber-50 rounded-lg">
                    <div class="w-3 h-3 bg-amber-500 rounded-full mx-auto mb-2"></div>
                    <p class="text-xs font-medium text-gray-600">Documents</p>
                    <p class="text-sm font-bold text-gray-900">{{ $documentsCount }}</p>
                  </div>
                  <div class="text-center p-3 bg-violet-50 rounded-lg">
                    <div class="w-3 h-3 bg-violet-500 rounded-full mx-auto mb-2"></div>
                    <p class="text-xs font-medium text-gray-600">Cases</p>
                    <p class="text-sm font-bold text-gray-900">{{ $activeCases }}</p>
                  </div>
                </div>
              </div>
            </section>
          </div>
        </div>
      </main>
    </div>

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

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() {
          sidebar.classList.toggle('-translate-x-full');
          sidebarOverlay.classList.toggle('hidden');
          sidebarOverlay.classList.toggle('opacity-0');
          document.body.style.overflow = document.body.style.overflow === 'hidden' ? '' : 'hidden';
        }

        if (mobileMenuBtn) {
          mobileMenuBtn.addEventListener('click', toggleSidebar);
        }

        if (sidebarOverlay) {
          sidebarOverlay.addEventListener('click', toggleSidebar);
        }

        // User dropdown menu
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenuDropdown = document.getElementById('userMenuDropdown');
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');

        // Toggle user dropdown
        if (userMenuBtn && userMenuDropdown) {
          userMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userMenuDropdown.classList.toggle('hidden');
            notificationDropdown.classList.add('hidden');
          });
        }

        // Toggle notification dropdown
        if (notificationBtn && notificationDropdown) {
          notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
            userMenuDropdown.classList.add('hidden');
          });
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
          if (userMenuDropdown && !userMenuDropdown.contains(e.target) && userMenuBtn && !userMenuBtn.contains(e.target)) {
            userMenuDropdown.classList.add('hidden');
          }
          if (notificationDropdown && !notificationDropdown.contains(e.target) && notificationBtn && !notificationBtn.contains(e.target)) {
            notificationDropdown.classList.add('hidden');
          }
        });

        // Real-time clock
        function updateClock() {
          const now = new Date();
          const timeString = now.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
          });
          const clockElement = document.getElementById('real-time-clock');
          if (clockElement) {
            clockElement.textContent = timeString;
          }
        }

        // Update clock immediately and then every second
        updateClock();
        setInterval(updateClock, 1000);

        // Sidebar dropdown toggles
        const dropdownButtons = [
          { btn: 'visitor-management-btn', submenu: 'visitor-submenu', arrow: 'visitor-arrow' },
          { btn: 'document-management-btn', submenu: 'document-submenu', arrow: 'document-arrow' },
          { btn: 'facilities-management-btn', submenu: 'facilities-submenu', arrow: 'facilities-arrow' },
          { btn: 'legal-management-btn', submenu: 'legal-submenu', arrow: 'legal-arrow' }
        ];

        dropdownButtons.forEach(({ btn, submenu, arrow }) => {
          const button = document.getElementById(btn);
          const menu = document.getElementById(submenu);
          const arrowIcon = document.getElementById(arrow);

          if (button && menu && arrowIcon) {
            let isOpen = false;

            button.addEventListener('click', () => {
              isOpen = !isOpen;

              if (isOpen) {
                menu.classList.add('show');
                arrowIcon.classList.add('rotate-180');
              } else {
                menu.classList.remove('show');
                arrowIcon.classList.remove('rotate-180');
              }
            });
          }
        });

        // Modal functionality (keep your existing modal logic)
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

        // Password change functionality
        const changePasswordForm = document.getElementById('changePasswordForm');
        const verifySection = document.getElementById('verifySection');
        const pwChangeMsg = document.getElementById('pwChangeMsg');
        const verifyPasswordBtn = document.getElementById('verifyPasswordBtn');
        const submitPasswordBtn = document.getElementById('submitPasswordBtn');

        if (changePasswordForm) {
          const csrfToken = changePasswordForm.querySelector('input[name="_token"]')?.value;

          function setPwMsg(text, ok = true) {
            pwChangeMsg.textContent = text;
            pwChangeMsg.className = `text-xs mt-1 ${ok ? 'text-green-700' : 'text-red-600'}`;
          }

          changePasswordForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            setPwMsg('Sending verification code...', true);
            submitPasswordBtn.disabled = true;
            const fd = new FormData(changePasswordForm);

            try {
              const res = await fetch(changePasswordForm.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: fd,
              });

              const data = await res.json();
              if (res.ok && data.ok) {
                setPwMsg('Code sent. Check your email and enter the 6-digit code below.', true);
                verifySection.classList.remove('hidden');
                changePasswordForm.querySelectorAll('input[type="password"]').forEach(i => i.disabled = true);
                document.getElementById('pw-verify-code').focus();
              } else {
                setPwMsg((data && (data.message || data.error)) || 'Failed to send code.', false);
                submitPasswordBtn.disabled = false;
              }
            } catch (err) {
              setPwMsg('Network error while sending code.', false);
              submitPasswordBtn.disabled = false;
            }
          });

          if (verifyPasswordBtn) {
            verifyPasswordBtn.addEventListener('click', async () => {
              const code = (document.getElementById('pw-verify-code').value || '').trim();
              if (code.length !== 6) {
                setPwMsg('Please enter the 6-digit verification code.', false);
                return;
              }

              setPwMsg('Verifying code...', true);
              verifyPasswordBtn.disabled = true;

              try {
                const res = await fetch(verifyPasswordBtn.dataset.verifyAction, {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                  body: JSON.stringify({ code })
                });

                const data = await res.json();
                if (res.ok && data.ok) {
                  setPwMsg('Password changed successfully.', true);
                  setTimeout(() => { privacySecurityModal.classList.remove('active'); }, 800);
                } else {
                  setPwMsg((data && data.message) || 'Invalid or expired code.', false);
                  verifyPasswordBtn.disabled = false;
                }
              } catch (err) {
                setPwMsg('Network error while verifying code.', false);
                verifyPasswordBtn.disabled = false;
              }
            });
          }
        }

        // Initialize Chart.js
        const canvas = document.getElementById('dashboardChart');
        if (canvas) {
          const ctx = canvas.getContext('2d');
          const values = [
          {{ $checkedInCount }},
          {{ $totalVisitors }},
          {{ $documentsCount }},
            {{ $activeCases }}
          ];
          const labels = ['Visitors (Checked In)', 'Total Visitors', 'Documents', 'Active Cases'];
          const total = values.reduce((a, b) => a + b, 0);

          // Enhanced gradients with more vibrant colors
          const gradGreen = ctx.createRadialGradient(canvas.width / 2, canvas.height / 2, 0, canvas.width / 2, canvas.height / 2, canvas.width / 2);
          gradGreen.addColorStop(0, 'rgba(16,185,129,0.9)');
          gradGreen.addColorStop(0.5, 'rgba(5,150,105,0.85)');
          gradGreen.addColorStop(1, 'rgba(4,120,87,0.8)');

          const gradBlue = ctx.createRadialGradient(canvas.width / 2, canvas.height / 2, 0, canvas.width / 2, canvas.height / 2, canvas.width / 2);
          gradBlue.addColorStop(0, 'rgba(59,130,246,0.9)');
          gradBlue.addColorStop(0.5, 'rgba(37,99,235,0.85)');
          gradBlue.addColorStop(1, 'rgba(29,78,216,0.8)');

          const gradAmber = ctx.createRadialGradient(canvas.width / 2, canvas.height / 2, 0, canvas.width / 2, canvas.height / 2, canvas.width / 2);
          gradAmber.addColorStop(0, 'rgba(245,158,11,0.9)');
          gradAmber.addColorStop(0.5, 'rgba(217,119,6,0.85)');
          gradAmber.addColorStop(1, 'rgba(180,83,9,0.8)');

          const gradViolet = ctx.createRadialGradient(canvas.width / 2, canvas.height / 2, 0, canvas.width / 2, canvas.height / 2, canvas.width / 2);
          gradViolet.addColorStop(0, 'rgba(139,92,246,0.9)');
          gradViolet.addColorStop(0.5, 'rgba(124,58,237,0.85)');
          gradViolet.addColorStop(1, 'rgba(109,40,217,0.8)');

          // Enhanced center text plugin with better styling
          const centerTextPlugin = {
            id: 'centerText',
            afterDraw(chart, args, pluginOptions) {
              const { ctx, chartArea: { width, height } } = chart;
              ctx.save();

              // Draw background circle
              const cx = chart.getDatasetMeta(0).data[0]?.x || width / 2;
              const cy = chart.getDatasetMeta(0).data[0]?.y || height / 2;

              ctx.beginPath();
              ctx.arc(cx, cy, 45, 0, 2 * Math.PI);
              ctx.fillStyle = 'rgba(255, 255, 255, 0.95)';
              ctx.fill();
              ctx.strokeStyle = 'rgba(16, 185, 129, 0.2)';
              ctx.lineWidth = 2;
              ctx.stroke();

              // Draw "Total" text
              ctx.font = '500 14px "Inter", sans-serif';
              ctx.fillStyle = '#6b7280';
              ctx.textAlign = 'center';
              ctx.textBaseline = 'middle';
              ctx.fillText('TOTAL', cx, cy - 10);

              // Draw total number
              ctx.font = '700 24px "Inter", sans-serif';
              ctx.fillStyle = '#059669';
              ctx.fillText(String(total), cx, cy + 12);

              ctx.restore();
            }
          };

          new Chart(ctx, {
            type: 'doughnut',
            data: {
              labels,
              datasets: [{
                data: values,
                backgroundColor: [gradGreen, gradBlue, gradAmber, gradViolet],
                borderColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6'],
                borderWidth: 3,
                hoverOffset: 15,
                spacing: 6,
                cutout: '70%',
                borderRadius: 8,
                hoverBorderWidth: 4
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  display: false // Using custom legend cards instead
                },
                tooltip: {
                  backgroundColor: 'rgba(17,24,39,0.95)',
                  titleColor: '#fff',
                  bodyColor: '#e5e7eb',
                  cornerRadius: 12,
                  padding: 12,
                  titleFont: { family: '"Inter", sans-serif', size: 14, weight: 'bold' },
                  bodyFont: { family: '"Inter", sans-serif', size: 13 },
                  borderColor: 'rgba(16,185,129,0.3)',
                  borderWidth: 1,
                  displayColors: true,
                  callbacks: {
                    label: (ctx) => {
                      const val = ctx.parsed;
                      const pct = total ? ((val / total) * 100).toFixed(1) : 0;
                      return `${ctx.label}: ${val} (${pct}%)`;
                    }
                  }
                }
              },
              animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1200,
                easing: 'easeOutQuart'
              },
              interaction: {
                intersect: false,
                mode: 'index'
              }
            },
            plugins: [centerTextPlugin]
          });
        }

        // openCaseWithConfGate function
        if (typeof window.openCaseWithConfGate !== 'function') {
          window.openCaseWithConfGate = function (href) {
            try { if (window.sessionStorage) sessionStorage.setItem('confOtpPending', '1'); } catch (_) { }
            if (href) { window.location.href = href; }
            return false;
          };
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
  @auth
    @include('partials.session-timeout-modal')
  @endauth
</body>

</html>