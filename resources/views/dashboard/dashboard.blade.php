@php
// Get the authenticated user
$user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <link rel="icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
  <link rel="shortcut icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
  <link rel="apple-touch-icon" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
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
    .modal > div:hover {
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
      transition: max-width 0.3s ease-in-out;
    }
    #sidebar.md\\:ml-0 ~ #main-content .dashboard-container {
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

    .dashboard-card:nth-child(1)::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .dashboard-card:nth-child(2)::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .dashboard-card:nth-child(3)::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .dashboard-card:nth-child(4)::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }

    .dashboard-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-lg);
      z-index: 2;
    }

    .activity-item {
      transition: all 0.2s ease-in-out;
      border-radius: 8px;
      margin: 4px 0;
      padding: 12px 16px;
    }

    .activity-item:hover {
      background-color: rgba(16, 185, 129, 0.05);
      transform: translateX(4px);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .chart-container {
      animation: fadeIn 0.5s ease-in-out;
    }
  </style>
</head>
<body class="bg-gray-100">
  <nav class="w-full p-3 h-16 bg-[#28644c] text-white shadow-md fixed top-0 left-0 right-0 z-50">
    <div class="flex justify-between items-center h-full max-w-7xl mx-auto">
      <div class="flex items-center space-x-4">
        <button id="toggle-btn" class="pl-2 focus:outline-none">
          <i class="fa-solid fa-bars text-2xl cursor-pointer"></i>
        </button>
        <h1 class="text-2xl font-bold tracking-tight"><i class="bx bx-grid-alt mr-2 align-middle"></i>Admin Dashboard</h1>
      </div>
      <div class="flex items-center space-x-1">
        <button class="relative p-2 transition duration-200 focus:outline-none" id="notificationBtn">
          <i class="fa-solid fa-bell text-xl"></i>
          <span class="absolute top-1 right-1 bg-red-500 text-xs text-white rounded-full px-1">3</span>
        </button>
        <div class="flex items-center space-x-2 cursor-pointer px-3 py-2 transition duration-200" id="userMenuBtn" aria-label="User menu" aria-haspopup="true" aria-expanded="false">
          <i class="fa-solid fa-user text-[18px] bg-white text-[#28644c] px-2.5 py-2 rounded-full"></i>
          <span class="text-white font-medium">{{ $user->name }}</span>
          <i class="fa-solid fa-chevron-down text-sm"></i>
        </div>
      </div>
    </div>
  </nav>

  <div id="notificationDropdown" class="hidden absolute right-4 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 text-gray-800 z-50" style="top: 4rem;">
    <div class="flex justify-between items-center px-4 py-2 border-b border-gray-200">
      <span class="font-semibold text-sm">Notifications</span>
      <span class="bg-red-600 text-white text-xs font-semibold rounded-full px-2 py-0.5">3 new</span>
    </div>
    <ul class="divide-y divide-gray-200 max-h-72 overflow-y-auto">
      <li class="flex items-start px-4 py-3 space-x-3">
        <div class="flex-shrink-0 mt-1">
          <div class="bg-green-200 text-green-700 rounded-full p-2">
            <i class="fas fa-user-plus"></i>
          </div>
        </div>
        <div class="flex-grow text-sm">
          <p class="font-semibold text-gray-900 leading-tight">Employee Onboarding</p>
          <p class="text-gray-600 leading-tight text-xs">New employee added: {{ $user->name }}</p>
          <p class="text-gray-400 text-xs mt-0.5">30 min ago</p>
        </div>
      </li>
      <li class="flex items-start px-4 py-3 space-x-3">
        <div class="flex-shrink-0 mt-1">
          <div class="bg-blue-200 text-blue-700 rounded-full p-2">
            <i class="fas fa-file-alt"></i>
          </div>
        </div>
        <div class="flex-grow text-sm">
          <p class="font-semibold text-gray-900 leading-tight">Report Generated</p>
          <p class="text-gray-600 leading-tight text-xs">Monthly report generated</p>
          <p class="text-gray-400 text-xs mt-0.5">2 hours ago</p>
        </div>
      </li>
      <li class="flex items-start px-4 py-3 space-x-3">
        <div class="flex-shrink-0 mt-1">
          <div class="bg-blue-200 text-blue-700 rounded-full p-2">
            <i class="fas fa-file-alt"></i>
          </div>
        </div>
        <div class="flex-grow text-sm">
          <p class="font-semibold text-gray-900 leading-tight">Document Uploaded</p>
          <p class="text-gray-600 leading-tight text-xs">New document uploaded</p>
          <p class="text-gray-400 text-xs mt-0.5">Yesterday</p>
        </div>
      </li>
    </ul>
    <div class="text-center py-2 border-t border-gray-200">
      <a class="text-[#28644c] text-xs font-semibold hover:underline" href="#">View all notifications</a>
    </div>
  </div>

  <div class="flex w-full min-h-screen pt-16">
    <div id="overlay" class="hidden fixed inset-0 bg-black opacity-50 z-40"></div>

    <aside id="sidebar" class="bg-[#2f855A] text-white flex flex-col z-40 fixed top-16 bottom-0 w-72 -ml-72 md:sticky md:ml-0 transition-all duration-300 ease-in-out overflow-y-auto">
      <div class="department-header px-2 py-4 mx-2 border-b border-white/50">
        <h1 class="text-xl font-bold">Administrative Department</h1>
      </div>
      <div class="px-3 py-10 flex-1">
        <ul class="space-y-6">
          <li>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center font-medium space-x-2 text-lg hover:bg-white/30 px-3 py-2.5 rounded-lg whitespace-nowrap">
              <i class="bx bx-grid-alt"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li class="has-dropdown">
            <div class="flex items-center font-medium justify-between text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
              <div class="flex items-center space-x-2">
                <i class="bx bx-calendar-check"></i>
                <span>Facilities Reservations</span>
              </div>
              <i class="bx bx-chevron-down text-2xl transition-transform duration-300"></i>
            </div>
            <ul class="dropdown-menu hidden bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
              <li><a href="{{ route('room-equipment') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-door-open mr-2"></i>Room & Equipment Booking</a></li>
              <li><a href="{{ route('scheduling.calendar') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-calendar mr-2"></i>Scheduling & Calendar Integrations</a></li>
              <li><a href="{{ route('approval.workflow') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-check-circle mr-2"></i>Approval Workflow</a></li>
              <li><a href="{{ route('reservation.history') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-history mr-2"></i>Reservation History</a></li>
            </ul>
          </li>
          <li class="has-dropdown">
            <div class="flex items-center font-medium justify-between text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
              <div class="flex items-center space-x-2">
                <i class="bx bx-file"></i>
                <span>Document Management</span>
              </div>
              <i class="bx bx-chevron-down text-2xl transition-transform duration-300"></i>
            </div>
            <ul class="dropdown-menu hidden bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
              <li><a href="{{ route('document.upload.indexing') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-upload mr-2"></i>Document Upload & Indexing</a></li>
              <li><a href="{{ route('document.version.control') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-git-branch mr-2"></i>Version Control</a></li>
              <li><a href="{{ route('document.access.control.permissions') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-lock mr-2"></i>Access Control & Permissions</a></li>
              <li><a href="{{ route('document.archival.retention.policy') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-archive mr-2"></i>Archival & Retention Policy</a></li>
            </ul>
          </li>
          <li class="has-dropdown">
            <div class="flex items-center font-medium justify-between text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
              <div class="flex items-center space-x-2">
                <i class="bx bx-file"></i>
                <span>Legal Management</span>
              </div>
              <i class="bx bx-chevron-down text-2xl transition-transform duration-300"></i>
            </div>
            <ul class="dropdown-menu hidden bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
              <li><a href="{{ route('case.management') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-briefcase mr-2"></i>Case Management</a></li>
              <li><a href="{{ route('contract.management') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-file-blank mr-2"></i>Contract Management</a></li>
              <li><a href="{{ route('compliance.tracking') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-check-double mr-2"></i>Compliance Tracking</a></li>
              <li><a href="{{ route('deadline.hearing.alerts') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-alarm mr-2"></i>Deadline & Hearing Alerts</a></li>
            </ul>
          </li>
          <li class="has-dropdown">
            <div class="flex items-center font-medium justify-between text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
              <div class="flex items-center space-x-2">
                <i class="bx bx-group"></i>
                <span>Visitor Management</span>
              </div>
              <i class="bx bx-chevron-down text-2xl transition-transform duration-300"></i>
            </div>
            <ul class="dropdown-menu hidden bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
              <li><a href="{{ route('visitors.registration') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-id-card mr-2"></i>Visitors Registration</a></li>
              <li><a href="{{ route('checkinout.tracking') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-transfer mr-2"></i>Check In/Out Tracking</a></li>
              <li><a href="{{ route('visitor.history.records') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-history mr-2"></i>Visitor History Records</a></li>
            </ul>
          </li>
          <li>
            <a href="#" class="flex items-center font-medium space-x-2 text-lg hover:bg-white/30 px-3 py-2.5 rounded-lg whitespace-nowrap">
              <i class="bx bx-user-shield"></i>
              <span>Administrator</span>
            </a>
          </li>
        </ul>
      </div>
      <div class="px-5 pb-6">
        <div class="bg-white rounded-md p-4 text-center text-[#2f855A] text-sm font-semibold select-none">
          Need Help?<br />
          Contact support team at<br />
          <button type="button" class="mt-2 bg-[#3f8a56] text-white text-sm font-semibold px-4 py-2 rounded-lg shadow-sm hover:bg-[#28644c] focus:outline-none focus:ring-2 focus:ring-[#3f8a56] transition-all duration-200">
            Contact Support
          </button>
        </div>
      </div>
    </aside>

    <main id="main-content" class="flex-1 p-6 w-full mt-16">
      <div class="dashboard-container max-w-7xl mx-auto">
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-md border border-gray-100 p-8 space-y-6">
          <h2 class="text-[#1a4d38] font-bold text-xl mb-1"><i class="bx bx-grid-alt mr-2 align-middle"></i>Admin Dashboard</h2>
          @php
            // Use real statistics from database (passed from route)
            $checkedInCount = $stats['checked_in_visitors'] ?? 0;
            $totalVisitors = $stats['total_visitors'] ?? 0;
            $documentsCount = $stats['uploaded_documents'] ?? 0;
            $activeCases = $stats['active_cases'] ?? 0;
          @endphp
          <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="dashboard-card bg-gradient-to-br from-white to-gray-50 rounded-lg p-4 shadow-sm border-l-4 border-[#2f855A]">
              <div class="flex justify-between items-start">
                <p class="text-gray-800 font-semibold text-sm">Visitors Checked In</p>
                <i class="bx bx-log-in text-[#2f855A] text-2xl"></i>
              </div>
              <p class="font-extrabold text-2xl mt-1 text-gray-900">{{ $checkedInCount }}</p>
              <p class="text-xs text-gray-500 mt-1">From total {{ $totalVisitors }} visitors</p>
            </div>
            <div class="dashboard-card bg-gradient-to-br from-white to-gray-50 rounded-lg p-4 shadow-sm border-l-4 border-[#2f855A]">
              <div class="flex justify-between items-start">
                <p class="text-gray-800 font-semibold text-sm">Total Visitors</p>
                <i class="bx bx-group text-[#2f855A] text-2xl"></i>
              </div>
              <p class="font-extrabold text-2xl mt-1 text-gray-900">{{ $totalVisitors }}</p>
              <p class="text-xs text-gray-500 mt-1">Session data</p>
            </div>
            <div class="dashboard-card bg-gradient-to-br from-white to-gray-50 rounded-lg p-4 shadow-sm border-l-4 border-[#2f855A]">
              <div class="flex justify-between items-start">
                <p class="text-gray-800 font-semibold text-sm">Uploaded Documents</p>
                <i class="bx bx-file text-[#2f855A] text-2xl"></i>
              </div>
              <p class="font-extrabold text-2xl mt-1 text-gray-900">{{ $documentsCount }}</p>
              <p class="text-xs text-gray-500 mt-1">Document Management</p>
            </div>
            <a href="{{ route('case.management') }}" class="block focus:outline-none" aria-label="View Case Management">
              <div class="dashboard-card bg-gradient-to-br from-white to-gray-50 rounded-lg p-4 shadow-sm border-l-4 border-[#2f855A] cursor-pointer hover:shadow-md">
                <div class="flex justify-between items-start">
                  <p class="text-gray-800 font-semibold text-sm">Active Cases</p>
                  <i class="bx bx-briefcase text-[#2f855A] text-2xl"></i>
                </div>
                <p class="font-extrabold text-2xl mt-1 text-gray-900">{{ $activeCases }}</p>
                <p class="text-xs text-gray-500 mt-1">Legal Management</p>
              </div>
            </a>
          </section>
          <section class="grid grid-cols-1 gap-4 chart-container">
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-lg p-6 shadow-sm border border-gray-100 flex flex-col w-full">
              <h3 class="font-bold text-sm text-[#1a4d38] mb-4">Module Overview</h3>
              <div class="relative" style="height: 380px;">
                <canvas id="dashboardChart"></canvas>
              </div>
            </div>
          </section>
        </div>
      </div>
    </main>
  </div>

  <!-- Modals -->
  <div id="userMenuDropdown" class="hidden absolute right-4 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50" style="top: 4rem;" role="menu" aria-labelledby="userMenuBtn">
    <div class="py-4 px-6 border-b border-gray-100 text-center">
      <div class="w-14 h-14 rounded-full bg-[#28644c] text-white mx-auto flex items-center justify-center mb-2">
        <i class="fas fa-user-circle text-3xl"></i>
      </div>
      <p class="font-semibold text-[#28644c]">{{ $user->name }}</p>
      <p class="text-xs text-gray-400">Administrator</p>
    </div>
    <ul class="text-sm text-gray-700">
      <li><button id="openProfileBtn" class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1"><i class="fas fa-user-circle mr-2"></i> My Profile</button></li>
      <li><button id="openAccountSettingsBtn" class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1"><i class="fas fa-cog mr-2"></i> Account Settings</button></li>
      <li><button id="openPrivacySecurityBtn" class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1"><i class="fas fa-shield-alt mr-2"></i> Privacy & Security</button></li>
      <li><button id="openSignOutBtn" class="w-full text-left flex items-center px-6 py-2 text-red-600 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1"><i class="fas fa-sign-out-alt mr-2"></i> Sign Out</button></li>
    </ul>
  </div>

  <div id="profileModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="profile-modal-title">
    <div class="bg-white rounded-lg shadow-lg w-[480px] max-w-full mx-4" role="document">
      <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
        <h3 id="profile-modal-title" class="font-semibold text-sm text-gray-900 select-none">My Profile</h3>
        <button id="closeProfileBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
          <i class="fas fa-times text-xs"></i>
        </button>
      </div>
      <div class="px-8 pt-6 pb-8">
        <div class="flex flex-col items-center mb-4">
          <div class="bg-[#28644c] rounded-full w-20 h-20 flex items-center justify-center mb-3">
            <i class="fas fa-user text-white text-3xl"></i>
          </div>
          <p class="font-semibold text-gray-900 text-base leading-5 mb-0.5">{{ $user->name }}</p>
          <p class="text-xs text-gray-500 leading-4">Administrator</p>
        </div>
        <form class="space-y-4" action="{{ route('profile.update') }}" method="POST">
          @csrf
          @method('PATCH')
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="nameProfile" class="block text-xs font-semibold text-gray-700 mb-1">Full Name</label>
              <input id="nameProfile" name="name" type="text" value="{{ old('name', $user->name) }}" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" />
            </div>
            <div>
              <label for="usernameProfile" class="block text-xs font-semibold text-gray-700 mb-1">Username</label>
              <input id="usernameProfile" name="username" type="text" value="{{ old('username', $user->username) }}" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" />
            </div>
            <div>
              <label for="emailProfile" class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
              <input id="emailProfile" name="email" type="email" value="{{ old('email', $user->email) }}" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" />
            </div>
            <div>
              <label for="phoneProfile" class="block text-xs font-semibold text-gray-700 mb-1">Phone</label>
              <input id="phoneProfile" name="phone" type="text" value="{{ old('phone', $user->phone) }}" placeholder="+63 9xx xxx xxxx" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" />
            </div>
            <div>
              <label for="department" class="block text-xs font-semibold text-gray-700 mb-1">Department</label>
              <input id="department" type="text" value="Administrative" readonly class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-gray-50" />
            </div>
            <div>
              <label for="location" class="block text-xs font-semibold text-gray-700 mb-1">Location</label>
              <input id="location" type="text" value="Manila, Philippines" readonly class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-gray-50" />
            </div>
            <div>
              <label for="joined" class="block text-xs font-semibold text-gray-700 mb-1">Joined</label>
              <input id="joined" type="text" value="{{ $user->created_at->format('F d, Y') }}" readonly class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-gray-50" />
            </div>
          </div>
          <div class="flex justify-end space-x-3 pt-2">
            <button id="closeProfileBtn2" type="button" class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
            <button type="submit" class="bg-[#28644c] hover:bg-[#2f855A] text-white text-sm font-semibold rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="accountSettingsModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="account-settings-modal-title">
    <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
      <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
        <h3 id="account-settings-modal-title" class="font-semibold text-sm text-gray-900 select-none">Account Settings</h3>
        <button id="closeAccountSettingsBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
          <i class="fas fa-times text-xs"></i>
        </button>
      </div>
      <div class="px-8 pt-6 pb-8">
        <form class="space-y-4 text-xs text-gray-700" action="{{ route('profile.update') }}" method="POST">
          @csrf
          @method('PATCH')
          <div>
            <label for="username" class="block mb-1 font-semibold">Username</label>
            <input id="username" name="username" type="text" value="{{ $user->name }}" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" />
          </div>
          <div>
            <label for="emailAccount" class="block mb-1 font-semibold">Email</label>
            <input id="emailAccount" name="email" type="email" value="{{ $user->email }}" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" />
          </div>
          <div>
            <label for="language" class="block mb-1 font-semibold">Language</label>
            <select id="language" name="language" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]">
              <option selected>English</option>
            </select>
          </div>
          <div>
            <label for="timezone" class="block mb-1 font-semibold">Time Zone</label>
            <select id="timezone" name="timezone" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]">
              <option selected>Philippine Time (GMT+8)</option>
            </select>
          </div>
          <fieldset class="space-y-1">
            <legend class="font-semibold text-xs mb-1">Notifications</legend>
            <div class="flex items-center space-x-2">
              <input id="email-notifications" name="email_notifications" type="checkbox" checked class="w-3.5 h-3.5 text-[#2f855A] focus:ring-[#2f855A] border-gray-300 rounded" />
              <label for="email-notifications" class="text-xs">Email notifications</label>
            </div>
            <div class="flex items-center space-x-2">
              <input id="browser-notifications" name="browser_notifications" type="checkbox" checked class="w-3.5 h-3.5 text-[#2f855A] focus:ring-[#2f855A] border-gray-300 rounded" />
              <label for="browser-notifications" class="text-xs">Browser notifications</label>
            </div>
          </fieldset>
          <div class="flex justify-end space-x-3 pt-2">
            <button type="button" id="cancelAccountSettingsBtn" class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
            <button type="submit" class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="privacySecurityModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="privacy-security-modal-title">
    <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
      <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
        <h3 id="privacy-security-modal-title" class="font-semibold text-sm text-gray-900 select-none">Privacy & Security</h3>
        <button id="closePrivacySecurityBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
          <i class="fas fa-times text-xs"></i>
        </button>
      </div>
      <div class="px-8 pt-6 pb-8">
          <form id="changePasswordForm" action="{{ route('account.password.change.request') }}" method="POST" class="space-y-3">
            @csrf
            <fieldset>
              <legend class="font-semibold mb-2 select-none">Change Password</legend>
              <label class="block mb-1 font-normal select-none" for="current-password">Current Password</label>
              <input class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" id="current-password" name="current_password" type="password"/>
              <label class="block mt-3 mb-1 font-normal select-none" for="new-password">New Password</label>
              <input class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" id="new-password" name="new_password" type="password"/>
              <label class="block mt-3 mb-1 font-normal select-none" for="confirm-password">Confirm New Password</label>
              <input class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" id="confirm-password" name="new_password_confirmation" type="password"/>
            </fieldset>
            <div id="verifySection" class="hidden">
              <label class="block mt-2 mb-1 font-normal select-none" for="pw-verify-code">Enter Verification Code</label>
              <input class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" id="pw-verify-code" name="code" type="text" maxlength="6"/>
              <button id="verifyPasswordBtn" type="button" data-verify-action="{{ route('account.password.change.verify') }}" class="mt-3 w-full bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200">Verify Code & Update</button>
            </div>
            <div class="text-xs" id="pwChangeMsg"></div>
            <div class="flex justify-end space-x-3 pt-2">
              <button class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200" id="cancelPrivacySecurityBtn" type="button">Cancel</button>
              <button id="submitPasswordBtn" class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200" type="submit">Send Code</button>
            </div>
          </form>
          <fieldset>
            <legend class="font-semibold mb-1 select-none">Two-Factor Authentication</legend>
            <p class="text-[10px] mb-1 select-none">Enhance your account security</p>
            <div class="flex items-center justify-between">
              <span class="text-[10px] text-[#2f855A] font-semibold select-none">Status: Enabled</span>
              <button class="text-[10px] bg-gray-200 text-gray-700 rounded-lg px-3 py-1.5 font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200" type="button">Configure</button>
            </div>
          </fieldset>
          <fieldset>
            <legend class="font-semibold mb-1 select-none">Session Management</legend>
            <div class="bg-gray-100 rounded px-3 py-2 text-[10px] text-gray-700 select-none">
              <div class="font-semibold">Current Session</div>
              <div class="text-[9px] text-gray-500">Manila, Philippines • Chrome</div>
              <div class="inline-block mt-1 bg-green-100 text-green-700 text-[9px] font-semibold rounded px-2 py-0.5 select-none">Active</div>
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
        <button id="cancelSignOutBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
          <i class="fas fa-times text-xs"></i>
        </button>
      </div>
      <div class="px-8 pt-6 pb-8">
        <div class="mx-auto mb-4 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
          <i class="fas fa-sign-out-alt text-red-600 text-xl"></i>
        </div>
        <p class="text-xs text-gray-600 mb-6">Are you sure you want to sign out of your account?</p>
        <div class="flex justify-center space-x-4">
          <button id="cancelSignOutBtn2" class="bg-gray-200 text-gray-800 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-red-600 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-sm transition-all duration-200">Sign Out</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const sidebar = document.getElementById("sidebar");
      const mainContent = document.getElementById("main-content");
      const toggleBtn = document.getElementById("toggle-btn");
      const overlay = document.getElementById("overlay");
      const dropdownToggles = document.querySelectorAll(".has-dropdown > div");
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

      // Initialize sidebar state based on screen size
      if (window.innerWidth >= 768) {
        sidebar.classList.remove("-ml-72");
        mainContent.classList.add("md:ml-72", "sidebar-open");
      } else {
        sidebar.classList.add("-ml-72");
        mainContent.classList.remove("md:ml-72", "sidebar-open");
        mainContent.classList.add("sidebar-closed");
      }

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

      function closeAllDropdowns() {
        dropdownToggles.forEach((toggle) => {
          const dropdown = toggle.nextElementSibling;
          const chevron = toggle.querySelector(".bx-chevron-down");
          if (!dropdown.classList.contains("hidden")) {
            dropdown.classList.add("hidden");
            chevron.classList.remove("rotate-180");
          }
        });
      }

      dropdownToggles.forEach((toggle) => {
        toggle.addEventListener("click", () => {
          const dropdown = toggle.nextElementSibling;
          const chevron = toggle.querySelector(".bx-chevron-down");
          dropdownToggles.forEach((otherToggle) => {
            if (otherToggle !== toggle) {
              otherToggle.nextElementSibling.classList.add("hidden");
              otherToggle.querySelector(".bx-chevron-down").classList.remove("rotate-180");
            }
          });
          dropdown.classList.toggle("hidden");
          chevron.classList.toggle("rotate-180");
        });
      });

      overlay.addEventListener("click", () => {
        sidebar.classList.add("-ml-72");
        overlay.classList.add("hidden");
        document.body.style.overflow = "";
        mainContent.classList.remove("sidebar-open");
        mainContent.classList.add("sidebar-closed");
      });

      toggleBtn.addEventListener("click", toggleSidebar);

      notificationBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        notificationDropdown.classList.toggle("hidden");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
        profileModal.classList.remove("active");
        accountSettingsModal.classList.remove("active");
        privacySecurityModal.classList.remove("active");
        signOutModal.classList.remove("active");
      });

      userMenuBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        userMenuDropdown.classList.toggle("hidden");
        const expanded = userMenuBtn.getAttribute("aria-expanded") === "true";
        userMenuBtn.setAttribute("aria-expanded", !expanded);
        notificationDropdown.classList.add("hidden");
        profileModal.classList.remove("active");
        accountSettingsModal.classList.remove("active");
        privacySecurityModal.classList.remove("active");
        signOutModal.classList.remove("active");
      });

      openProfileBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        profileModal.classList.add("active");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
        accountSettingsModal.classList.remove("active");
        privacySecurityModal.classList.remove("active");
        notificationDropdown.classList.add("hidden");
        signOutModal.classList.remove("active");
      });

      closeProfileBtn.addEventListener("click", () => {
        profileModal.classList.remove("active");
      });
      closeProfileBtn2.addEventListener("click", () => {
        profileModal.classList.remove("active");
      });

      openAccountSettingsBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        accountSettingsModal.classList.add("active");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
        profileModal.classList.remove("active");
        privacySecurityModal.classList.remove("active");
        notificationDropdown.classList.add("hidden");
        signOutModal.classList.remove("active");
      });

      closeAccountSettingsBtn.addEventListener("click", () => {
        accountSettingsModal.classList.remove("active");
      });
      cancelAccountSettingsBtn.addEventListener("click", () => {
        accountSettingsModal.classList.remove("active");
      });

      openPrivacySecurityBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        privacySecurityModal.classList.add("active");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
        profileModal.classList.remove("active");
        accountSettingsModal.classList.remove("active");
        notificationDropdown.classList.add("hidden");
        signOutModal.classList.remove("active");
      });

      openSignOutBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        signOutModal.classList.add("active");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
        profileModal.classList.remove("active");
        accountSettingsModal.classList.remove("active");
        privacySecurityModal.classList.remove("active");
        notificationDropdown.classList.add("hidden");
      });

      closePrivacySecurityBtn.addEventListener("click", () => {
        privacySecurityModal.classList.remove("active");
      });
      cancelPrivacySecurityBtn.addEventListener("click", () => {
        privacySecurityModal.classList.remove("active");
      });

      // Change Password two-step flow
      const changePasswordForm = document.getElementById('changePasswordForm');
      const verifySection = document.getElementById('verifySection');
      const pwChangeMsg = document.getElementById('pwChangeMsg');
      const verifyPasswordBtn = document.getElementById('verifyPasswordBtn');
      const submitPasswordBtn = document.getElementById('submitPasswordBtn');
      const csrfToken = changePasswordForm?.querySelector('input[name="_token"]')?.value;

      function setPwMsg(text, ok = true) {
        pwChangeMsg.textContent = text;
        pwChangeMsg.className = `text-xs mt-1 ${ok ? 'text-green-700' : 'text-red-600'}`;
      }

      if (changePasswordForm) {
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

      cancelSignOutBtn.addEventListener("click", () => {
        signOutModal.classList.remove("active");
      });
      cancelSignOutBtn2.addEventListener("click", () => {
        signOutModal.classList.remove("active");
      });

      window.addEventListener("click", (e) => {
        if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
          notificationDropdown.classList.add("hidden");
        }
        if (!userMenuBtn.contains(e.target) && !userMenuDropdown.contains(e.target)) {
          userMenuDropdown.classList.add("hidden");
          userMenuBtn.setAttribute("aria-expanded", "false");
        }
        if (!profileModal.contains(e.target) && !openProfileBtn.contains(e.target)) {
          profileModal.classList.remove("active");
        }
        if (!accountSettingsModal.contains(e.target) && !openAccountSettingsBtn.contains(e.target)) {
          accountSettingsModal.classList.remove("active");
        }
        if (!privacySecurityModal.contains(e.target) && !openPrivacySecurityBtn.contains(e.target)) {
          privacySecurityModal.classList.remove("active");
        }
        if (!signOutModal.contains(e.target)) {
          signOutModal.classList.remove("active");
        }
      });

      profileModal.querySelector("div").addEventListener("click", (e) => {
        e.stopPropagation();
      });
      accountSettingsModal.querySelector("div").addEventListener("click", (e) => {
        e.stopPropagation();
      });
      privacySecurityModal.querySelector("div").addEventListener("click", (e) => {
        e.stopPropagation();
      });
      signOutModal.querySelector("div").addEventListener("click", (e) => {
        e.stopPropagation();
      });

      window.addEventListener("resize", () => {
        if (window.innerWidth >= 768) {
          sidebar.classList.remove("-ml-72");
          overlay.classList.add("hidden");
          document.body.style.overflow = "";
          if (!mainContent.classList.contains("md:ml-72")) {
            mainContent.classList.add("md:ml-72", "sidebar-open");
            mainContent.classList.remove("sidebar-closed");
          }
        } else {
          sidebar.classList.add("-ml-72");
          mainContent.classList.remove("md:ml-72", "sidebar-open");
          mainContent.classList.add("sidebar-closed");
          overlay.classList.add("hidden");
          document.body.style.overflow = "";
        }
        closeAllDropdowns();
      });

      // Initialize Chart.js (Doughnut for module distribution) with aesthetic upgrades
      const canvas = document.getElementById('dashboardChart');
      const ctx = canvas.getContext('2d');

      const values = [
        {{ $checkedInCount }},
        {{ $totalVisitors }},
        {{ $documentsCount }},
        {{ $activeCases }}
      ];
      const labels = ['Visitors (Checked In)', 'Total Visitors', 'Documents', 'Active Cases'];
      const total = values.reduce((a,b)=>a+b,0);

      // Gradients
      const gradGreen = ctx.createLinearGradient(0, 0, 0, canvas.height);
      gradGreen.addColorStop(0, 'rgba(16,185,129,0.95)');
      gradGreen.addColorStop(1, 'rgba(5,150,105,0.85)');
      const gradBlue = ctx.createLinearGradient(0, 0, 0, canvas.height);
      gradBlue.addColorStop(0, 'rgba(59,130,246,0.95)');
      gradBlue.addColorStop(1, 'rgba(37,99,235,0.85)');
      const gradAmber = ctx.createLinearGradient(0, 0, 0, canvas.height);
      gradAmber.addColorStop(0, 'rgba(245,158,11,0.95)');
      gradAmber.addColorStop(1, 'rgba(217,119,6,0.85)');
      const gradViolet = ctx.createLinearGradient(0, 0, 0, canvas.height);
      gradViolet.addColorStop(0, 'rgba(139,92,246,0.95)');
      gradViolet.addColorStop(1, 'rgba(124,58,237,0.85)');

      // Center text plugin
      const centerTextPlugin = {
        id: 'centerText',
        afterDraw(chart, args, pluginOptions) {
          const {ctx, chartArea: {width, height}} = chart;
          ctx.save();
          ctx.font = '600 18px "Inter", sans-serif';
          ctx.fillStyle = '#1f2937';
          ctx.textAlign = 'center';
          ctx.textBaseline = 'middle';
          const cx = chart.getDatasetMeta(0).data[0]?.x || width/2;
          const cy = chart.getDatasetMeta(0).data[0]?.y || height/2;
          ctx.fillText('Total', cx, cy - 12);
          ctx.font = '800 20px "Inter", sans-serif';
          ctx.fillStyle = '#28644c';
          ctx.fillText(String(total), cx, cy + 12);
          ctx.restore();
        }
      };

      // Soft shadow plugin for arcs
      const softShadowPlugin = {
        id: 'softShadow',
        beforeDatasetsDraw(chart, args, pluginOptions) {
          const {ctx} = chart;
          ctx.save();
          ctx.shadowColor = 'rgba(0,0,0,0.15)';
          ctx.shadowBlur = 12;
          ctx.shadowOffsetX = 0;
          ctx.shadowOffsetY = 6;
        },
        afterDatasetsDraw(chart, args, pluginOptions){
          chart.ctx.restore();
        }
      };

      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels,
          datasets: [{
            data: values,
            backgroundColor: [gradGreen, gradBlue, gradAmber, gradViolet],
            borderColor: ['#10b981','#3b82f6','#f59e0b','#8b5cf6'],
            borderWidth: 2,
            hoverOffset: 10,
            spacing: 4,
            cutout: '65%'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                usePointStyle: true,
                pointStyle: 'circle',
                boxWidth: 8,
                padding: 16,
                font: { family: '"Inter", sans-serif', size: 12 },
                color: '#1f2937'
              }
            },
            tooltip: {
              backgroundColor: 'rgba(17,24,39,0.9)',
              titleColor: '#fff',
              bodyColor: '#e5e7eb',
              cornerRadius: 8,
              padding: 10,
              callbacks: {
                label: (ctx) => {
                  const val = ctx.parsed;
                  const pct = total ? ((val/total)*100).toFixed(1) : 0;
                  return `${ctx.label}: ${val} (${pct}%)`;
                }
              }
            },
            title: {
              display: true,
              text: 'Module Distribution',
              font: { family: '"Inter", sans-serif', size: 14, weight: 'bold' },
              color: '#1a4d38'
            }
          },
          animation: { duration: 900, easing: 'easeOutQuart' }
        },
        plugins: [centerTextPlugin, softShadowPlugin]
      });
    });
  </script>
</body>
</html>