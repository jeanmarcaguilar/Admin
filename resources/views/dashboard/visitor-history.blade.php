@php
$user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor History | Administrative Dashboard</title>
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

        .dropdown-menu {
            transition: all 0.3s ease;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
        }

        .dropdown-menu.active {
            max-height: 500px;
            opacity: 1;
        }

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-checked-in {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-checked-out {
            background-color: #fee2e2;
            color: #991b1b;
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

        .status-expected {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-overdue {
            background-color: #fef3c7;
            color: #92400e;
        }
        .modal {
            display: none;
            background: rgba(0, 0, 0, 0.5);
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 60;
            align-items: center;
            justify-content: center;
        }
        .modal.active { display: flex; }
        .modal > div:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            transition: box-shadow .2s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-100">
    <nav class="w-full p-3 h-16 bg-[#28644c] text-white shadow-md fixed top-0 left-0 right-0 z-50">
        <div class="flex justify-between items-center h-full max-w-7xl mx-auto">
            <div class="flex items-center space-x-4">
                <button id="toggle-btn" class="pl-2 focus:outline-none" aria-label="Toggle Sidebar">
                    <i class="fa-solid fa-bars text-2xl cursor-pointer"></i>
                </button>
                <h1 class="text-2xl font-bold tracking-tight">Visitor History</h1>
            </div>
            <div class="flex items-center space-x-1">
                <button class="relative p-2 transition duration-200 focus:outline-none" id="notificationBtn" aria-label="Notifications">
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
            <span class="text-xs text-gray-500">No new</span>
        </div>
        <div class="p-4 text-center text-sm text-gray-500">No notifications yet.</div>
    </div>
    <!-- User Menu Dropdown (moved outside main content) -->
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
    <div class="flex w-full min-h-screen pt-16">
        <div id="overlay" class="hidden fixed inset-0 bg-black opacity-50 z-40"></div>

        <aside id="sidebar" class="bg-[#2f855A] text-white flex flex-col z-40 fixed top-16 bottom-0 w-72 -ml-72 md:sticky md:ml-0 transition-all duration-300 ease-in-out overflow-y-auto">
            <div class="department-header px-2 py-4 mx-2 border-b border-white/50">

                <h1 class="text-xl font-bold text-white">Administrative Department</h1>

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
                            <li><a href="{{ route('document.compliance.tracking') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-check-double mr-2"></i>Compliance Tracking</a></li>
                            <li><a href="{{ route('deadline.hearing.alerts') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-alarm mr-2"></i>Deadline & Hearing Alerts</a></li>
                        </ul>
                    </li>
                    <li class="has-dropdown active">
                        <div class="flex items-center font-medium justify-between text-lg bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-group"></i>
                                <span>Visitor Management</span>
                            </div>
                            <i class="bx bx-chevron-down text-2xl transition-transform duration-300 rotate-180"></i>
                        </div>
                        <ul class="dropdown-menu active bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
                            <li><a href="{{ route('visitors.registration') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-id-card mr-2"></i>Visitors Registration</a></li>
                            <li><a href="{{ route('checkinout.tracking') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-transfer mr-2"></i>Check In/Out Tracking</a></li>
                            <li><a href="{{ route('visitor.history') }}" class="block px-3 py-2 text-sm bg-white/30 rounded-lg"><i class="bx bx-history mr-2"></i>Visitor History Records</a></li>
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
                    <a href="mailto:support@admin.com" class="text-blue-600 hover:underline">support@admin.com</a>
                </div>
            </div>
        </aside>

        <main id="main-content" class="flex-1 p-6 w-full mt-16">
            <div class="dashboard-container">
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-md border border-gray-100 p-8 space-y-6">
                    <!-- Page Header -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                        <div>
                            <h1 class="text-2xl font-bold text-[#1a4d38]">Visitor History Records</h1>
                            <p class="text-gray-600">View and manage visitor history and records</p>
                        </div>
                        <div class="mt-4 md:mt-0 flex">
                            <button id="exportBtn" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-gray-50 transition-colors text-sm font-medium">
                                <i class="fas fa-download"></i>
                                <span>Export</span>
                            </button>
                        </div>
    </div>

                    @php
                        $allVisitors = $visitors ?? [];
                        $totalVisitors = is_array($allVisitors) ? count($allVisitors) : 0;
                        try { $todayStr = \Carbon\Carbon::today()->toDateString(); } catch (\Exception $e) { $todayStr = date('Y-m-d'); }
                        $visitorsToday = 0;
                        if (is_array($allVisitors)) {
                            foreach ($allVisitors as $vv) {
                                $d = isset($vv['check_in_date']) ? (string)$vv['check_in_date'] : '';
                                if ($d === $todayStr) { $visitorsToday++; }
                            }
                        }
                        $todayPct = $totalVisitors > 0 ? round(($visitorsToday / $totalVisitors) * 100) : 0;
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div class="dashboard-card bg-white p-6 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Visitors</p>
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $totalVisitors }}</h3>
                                </div>
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                    <i class="fas fa-users text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="h-2 bg-gray-200 rounded-full">
                                    <div class="h-2 bg-blue-500 rounded-full" style="width: 80%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">+150 from last month</p>
                            </div>
                        </div>

                        <div class="dashboard-card bg-white p-6 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Visitors Today</p>
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $visitorsToday }}</h3>
                                </div>
                                <div class="p-3 rounded-full bg-green-100 text-green-600">
                                    <i class="fas fa-user-check text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="h-2 bg-gray-200 rounded-full">
                                    <div class="h-2 bg-green-500 rounded-full" style="width: {{ $todayPct }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">{{ $visitorsToday > 0 ? '+'.$visitorsToday.' from yesterday' : 'No visitors yet' }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Search and Filter -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
                        <div class="flex flex-col md:flex-row md:items-center gap-4">
                            <div class="relative flex-1 max-w-none w-full">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" class="block w-full pl-10 pr-3 py-3 text-base border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" placeholder="Search visitors...">
                            </div>
                        </div>
                    </div>
                    <!-- Live Visitor Records (from session) -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900">Live Visitor Records</h3>
                            <span class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ isset($visitors) ? count($visitors) : 0 }} total</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visitor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Host</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visit Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse(($visitors ?? []) as $v)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $v['name'] ?? 'Visitor' }}</div>
                                                <div class="text-xs text-gray-500">ID: {{ $v['id'] ?? '' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $v['company'] ?? '—' }}</div>
                                                <div class="text-xs text-gray-500 capitalize">{{ $v['visitor_type'] ?? '' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $v['host'] ?? '—' }}</div>
                                                <div class="text-xs text-gray-500">{{ $v['host_department'] ?? '' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $v['check_in_date'] ?? '' }}</div>
                                                <div class="text-xs text-gray-500">{{ $v['check_in_time'] ?? '' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php $st = strtolower($v['status'] ?? 'scheduled'); @endphp
                                                <span class="status-badge {{ $st === 'checked_in' ? 'status-checked-in' : ($st==='checked_out' ? 'status-checked-out' : ($st==='overdue' ? 'status-overdue' : 'status-expected')) }}">{{ ucfirst($st) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">No visitor records yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                <!-- User Menu Dropdown -->
                <div id="userMenuDropdown" class="hidden absolute right-4 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50" style="top: 4rem;" role="menu" aria-labelledby="userMenuBtn">
                    <div class="py-4 px-6 border-b border-gray-100 text-center">
                        <div class="h-12 w-12 rounded-full bg-[#2f855A] flex items-center justify-center text-white text-xl font-semibold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <p class="text-sm font-medium text-gray-900 mt-2">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                    </div>
                    <div class="py-1">
                        <a href="#" id="openProfileBtn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-circle mr-2"></i> Profile
                        </a>
                        <a href="#" id="openAccountSettingsBtn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog mr-2"></i> Settings
                        </a>
                        <a href="#" id="openPrivacySecurityBtn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-shield-alt mr-2"></i> Privacy & Security
                        </a>
                    </div>
                    <div class="py-1 border-t border-gray-100">
                        <a href="#" id="signOutBtn" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i class="fas fa-sign-out-alt mr-2"></i> Sign out
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Profile Modal (moved outside main content) -->
    <div id="profileModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="profile-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
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
                <form class="space-y-4">
                    <div>
                        <label for="emailProfile" class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                        <input id="emailProfile" type="email" readonly value="{{ $user->email }}" class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
                    </div>
                    <div>
                        <label for="phone" class="block text-xs font-semibold text-gray-700 mb-1">Phone</label>
                        <input id="phone" type="text" readonly value="+1234567890" class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
                    </div>
                    <div>
                        <label for="department" class="block text-xs font-semibold text-gray-700 mb-1">Department</label>
                        <input id="department" type="text" readonly value="Administrative" class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
                    </div>
                    <div>
                        <label for="location" class="block text-xs font-semibold text-gray-700 mb-1">Location</label>
                        <input id="location" type="text" readonly value="Manila, Philippines" class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
                    </div>
                    <div>
                        <label for="joined" class="block text-xs font-semibold text-gray-700 mb-1">Joined</label>
                        <input id="joined" type="text" readonly value="{{ $user->created_at->format('F d, Y') }}" class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
                    </div>
                    <div class="flex justify-end pt-2">
                        <button id="closeProfileBtn2" type="button" class="bg-[#28644c] hover:bg-[#2f855A] text-white text-sm font-semibold rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Account Settings Modal (moved outside main content) -->
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

    <!-- Privacy & Security Modal (moved outside main content) -->
    <div id="privacySecurityModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="privacy-security-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                <h3 id="privacy-security-modal-title" class="font-semibold text-sm text-gray-900 select-none">Privacy & Security</h3>
                <button id="closePrivacySecurityBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div class="px-8 pt-6 pb-8">
                <form class="space-y-4 text-xs text-gray-700" action="{{ route('profile.security') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <fieldset>
                        <legend class="font-semibold mb-2 select-none">Change Password</legend>
                        <label class="block mb-1 font-normal select-none" for="current-password">Current Password</label>
                        <input class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" id="current-password" name="current_password" type="password"/>
                        <label class="block mt-3 mb-1 font-normal select-none" for="new-password">New Password</label>
                        <input class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" id="new-password" name="new_password" type="password"/>
                        <label class="block mt-3 mb-1 font-normal select-none" for="confirm-password">Confirm New Password</label>
                        <input class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" id="confirm-password" name="confirm_password" type="password"/>
                    </fieldset>
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
                    <div class="flex justify-end space-x-3 pt-2">
                        <button class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200" id="cancelPrivacySecurityBtn" type="button">Cancel</button>
                        <button class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200" type="submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sign Out Modal (moved outside main content) -->
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
    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <script>
document.addEventListener('DOMContentLoaded', () => {
    const elements = {
        sidebar: document.getElementById('sidebar'),
        mainContent: document.getElementById('main-content'),
        toggleBtn: document.getElementById('toggle-btn'),
        overlay: document.getElementById('overlay'),
        dropdownToggles: document.querySelectorAll('.has-dropdown > div'),
        notificationBtn: document.getElementById('notificationBtn'),
        notificationDropdown: document.getElementById('notificationDropdown'),
        userMenuBtn: document.getElementById('userMenuBtn'),
        userMenuDropdown: document.getElementById('userMenuDropdown'),
        openProfileBtn: document.getElementById('openProfileBtn'),
        openAccountSettingsBtn: document.getElementById('openAccountSettingsBtn'),
        openPrivacySecurityBtn: document.getElementById('openPrivacySecurityBtn'),
        openSignOutBtn: document.getElementById('openSignOutBtn'),
        profileModal: document.getElementById('profileModal'),
        accountSettingsModal: document.getElementById('accountSettingsModal'),
        privacySecurityModal: document.getElementById('privacySecurityModal'),
        signOutModal: document.getElementById('signOutModal'),
        closeProfileBtn: document.getElementById('closeProfileBtn'),
        closeProfileBtn2: document.getElementById('closeProfileBtn2'),
        closeAccountSettingsBtn: document.getElementById('closeAccountSettingsBtn'),
        cancelAccountSettingsBtn: document.getElementById('cancelAccountSettingsBtn'),
        closePrivacySecurityBtn: document.getElementById('closePrivacySecurityBtn'),
        cancelPrivacySecurityBtn: document.getElementById('cancelPrivacySecurityBtn'),
        cancelSignOutBtn: document.getElementById('cancelSignOutBtn'),
        cancelSignOutBtn2: document.getElementById('cancelSignOutBtn2'),
        signOutBtn: document.getElementById('signOutBtn')
    };

    // Initialize sidebar state
    const initializeSidebar = () => {
        if (window.innerWidth >= 768) {
            elements.sidebar.classList.remove('-ml-72');
            elements.mainContent.classList.add('md:ml-72', 'sidebar-open');
            elements.mainContent.classList.remove('sidebar-closed');
        } else {
            elements.sidebar.classList.add('-ml-72');
            elements.mainContent.classList.remove('md:ml-72', 'sidebar-open');
            elements.mainContent.classList.add('sidebar-closed');
        }
    };

    initializeSidebar();

    // Toggle sidebar
    const toggleSidebar = () => {
        if (window.innerWidth >= 768) {
            elements.sidebar.classList.toggle('md:-ml-72');
            elements.mainContent.classList.toggle('md:ml-72');
            elements.mainContent.classList.toggle('sidebar-open');
            elements.mainContent.classList.toggle('sidebar-closed');
        } else {
            elements.sidebar.classList.toggle('-ml-72');
            elements.overlay.classList.toggle('hidden');
            document.body.style.overflow = elements.sidebar.classList.contains('-ml-72') ? '' : 'hidden';
            elements.mainContent.classList.toggle('sidebar-open', !elements.sidebar.classList.contains('-ml-72'));
            elements.mainContent.classList.toggle('sidebar-closed', elements.sidebar.classList.contains('-ml-72'));
        }
    };

    // Close all dropdowns
    const closeAllDropdowns = (exclude = null) => {
        elements.dropdownToggles.forEach(toggle => {
            if (toggle !== exclude) {
                const dropdown = toggle.nextElementSibling;
                const chevron = toggle.querySelector('.bx-chevron-down');
                const parent = toggle.parentElement;
                dropdown.classList.add('hidden');
                dropdown.classList.remove('active');
                chevron.classList.remove('rotate-180');
                dropdown.style.maxHeight = '0';
                parent.classList.remove('active');
            }
        });
    };

    // Toggle dropdown
    const toggleDropdown = (toggle) => {
        const dropdown = toggle.nextElementSibling;
        const chevron = toggle.querySelector('.bx-chevron-down');
        const parent = toggle.parentElement;
        const isHidden = dropdown.classList.contains('hidden');

        if (isHidden) {
            closeAllDropdowns(toggle);
            dropdown.classList.remove('hidden');
            dropdown.classList.add('active');
            chevron.classList.add('rotate-180');
            dropdown.style.maxHeight = `${dropdown.scrollHeight}px`;
            parent.classList.add('active');
        } else {
            dropdown.classList.add('hidden');
            dropdown.classList.remove('active');
            chevron.classList.remove('rotate-180');
            dropdown.style.maxHeight = '0';
            parent.classList.remove('active');
        }
    };

    // Initialize active dropdowns
    document.querySelectorAll('.has-dropdown.active .dropdown-menu').forEach(menu => {
        menu.classList.remove('hidden');
        menu.classList.add('active');
        menu.style.maxHeight = `${menu.scrollHeight}px`;
        const chevron = menu.previousElementSibling.querySelector('.bx-chevron-down');
        chevron.classList.add('rotate-180');
    });

    // Toggle dropdowns on click
    elements.dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', () => toggleDropdown(toggle));
    });

    // Event listeners
    elements.overlay?.addEventListener('click', () => {
        elements.sidebar.classList.add('-ml-72');
        elements.overlay.classList.add('hidden');
        document.body.style.overflow = '';
        elements.mainContent.classList.remove('sidebar-open');
        elements.mainContent.classList.add('sidebar-closed');
        closeAllDropdowns();
    });

    elements.toggleBtn?.addEventListener('click', toggleSidebar);

    elements.notificationBtn?.addEventListener('click', e => {
        e.stopPropagation();
        elements.notificationDropdown.classList.toggle('hidden');
        elements.userMenuDropdown.classList.add('hidden');
        closeAllDropdowns();
    });

    elements.userMenuBtn?.addEventListener('click', e => {
        e.stopPropagation();
        elements.userMenuDropdown.classList.toggle('hidden');
        elements.notificationDropdown.classList.add('hidden');
        closeAllDropdowns();
    });

    // Modal helpers
    const openModal = (modal) => {
        if (!modal) return;
        modal.classList.add('active');
        modal.classList.remove('hidden');
        closeAllDropdowns();
    };

    const closeModal = (modal) => {
        if (!modal) return;
        modal.classList.remove('active');
        modal.classList.add('hidden');
    };

    // Open modals
    elements.openProfileBtn?.addEventListener('click', () => {
        openModal(elements.profileModal);
        elements.userMenuDropdown.classList.add('hidden');
    });

    elements.openAccountSettingsBtn?.addEventListener('click', () => {
        openModal(elements.accountSettingsModal);
        elements.userMenuDropdown.classList.add('hidden');
    });

    elements.openPrivacySecurityBtn?.addEventListener('click', () => {
        openModal(elements.privacySecurityModal);
        elements.userMenuDropdown.classList.add('hidden');
    });

    elements.openSignOutBtn?.addEventListener('click', () => {
        openModal(elements.signOutModal);
        elements.userMenuDropdown.classList.add('hidden');
    });

    // Close buttons
    elements.closeProfileBtn?.addEventListener('click', () => closeModal(elements.profileModal));
    elements.closeProfileBtn2?.addEventListener('click', () => closeModal(elements.profileModal));
    elements.closeAccountSettingsBtn?.addEventListener('click', () => closeModal(elements.accountSettingsModal));
    elements.cancelAccountSettingsBtn?.addEventListener('click', () => closeModal(elements.accountSettingsModal));
    elements.closePrivacySecurityBtn?.addEventListener('click', () => closeModal(elements.privacySecurityModal));
    elements.cancelPrivacySecurityBtn?.addEventListener('click', () => closeModal(elements.privacySecurityModal));
    elements.cancelSignOutBtn?.addEventListener('click', () => closeModal(elements.signOutModal));
    elements.cancelSignOutBtn2?.addEventListener('click', () => closeModal(elements.signOutModal));

    // Sign out
    elements.signOutBtn?.addEventListener('click', e => {
        e.preventDefault();
        document.getElementById('logout-form').submit();
    });

    // Close dropdowns and modals on outside click
    document.addEventListener('click', (e) => {
        const isClickInsideDropdown = Array.from(elements.dropdownToggles).some(toggle => 
            toggle.contains(e.target) || toggle.nextElementSibling.contains(e.target)
        );

        if (!isClickInsideDropdown) {
            closeAllDropdowns();
        }

        if (elements.notificationBtn && !elements.notificationBtn.contains(e.target) && 
            elements.notificationDropdown && !elements.notificationDropdown.contains(e.target)) {
            elements.notificationDropdown.classList.add('hidden');
        }

        if (elements.userMenuBtn && !elements.userMenuBtn.contains(e.target) && 
            elements.userMenuDropdown && !elements.userMenuDropdown.contains(e.target)) {
            elements.userMenuDropdown.classList.add('hidden');
        }

        if (elements.sidebar && !elements.sidebar.contains(e.target) && 
            elements.toggleBtn && !elements.toggleBtn.contains(e.target) && 
            window.innerWidth < 768) {
            toggleSidebar();
        }
    });

    // Close modals on outside click
    [elements.profileModal, elements.accountSettingsModal, elements.privacySecurityModal, elements.signOutModal].forEach(m => {
        m?.addEventListener('click', (e) => {
            if (e.target === m) closeModal(m);
        });
        m?.querySelector('div')?.addEventListener('click', (e) => e.stopPropagation());
    });

    // Handle window resize
    window.addEventListener('resize', () => {
        initializeSidebar();
        if (window.innerWidth >= 768) {
            elements.overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }
        closeAllDropdowns();
    });
});

// Export current table rows to CSV
document.getElementById('exportBtn')?.addEventListener('click', function(){
  try{
    const table = document.querySelector('table');
    if(!table){
      Swal?.fire && Swal.fire({icon:'info',title:'No data',text:'No table data to export.'});
      return;
    }
    const headers = Array.from(table.querySelectorAll('thead th')).map(th=>th.textContent.trim());
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    const csv = [];
    const esc = (v) => '"' + (v??'').toString().replace(/"/g,'""') + '"';
    csv.push(headers.map(esc).join(','));
    rows.forEach(tr=>{
      const cols = Array.from(tr.querySelectorAll('td')).map(td=>td.innerText.replace(/\s+/g,' ').trim());
      csv.push(cols.map(esc).join(','));
    });
    const blob = new Blob([csv.join('\n')], {type:'text/csv;charset=utf-8;'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    const dateStr = new Date().toISOString().slice(0,10);
    a.download = `visitor-history-${dateStr}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  }catch(e){
    console.error(e);
    Swal?.fire && Swal.fire({icon:'error',title:'Export failed',text:'Unable to export CSV.'});
  }
});
</script>
</body>
</html>