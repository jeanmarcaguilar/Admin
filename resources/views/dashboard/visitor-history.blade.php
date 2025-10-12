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
    </style>
</head>
<body class="bg-gray-100">
    <nav class="w-full p-3 h-16 bg-[#28644c] text-white shadow-md fixed top-0 left-0 right-0 z-50">
        <div class="flex justify-between items-center h-full max-w-full mx-auto px-4">
            <div class="flex items-center space-x-4">
                <button id="toggle-btn" class="pl-2 focus:outline-none" aria-label="Toggle Sidebar">
                    <i class="fa-solid fa-bars text-2xl cursor-pointer"></i>
                </button>
                <h1 class="text-2xl font-bold tracking-tight">Visitor History</h1>
            </div>
            <div class="flex items-center space-x-1">
                <button class="relative p-2 transition duration-200 focus:outline-none" id="notificationBtn" aria-label="Notifications">
                    <i class="fa-solid fa-bell text-xl"></i>
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
                        <div class="mt-4 md:mt-0 flex space-x-3">
                            <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-gray-50 transition-colors text-sm font-medium">
                                <i class="fas fa-download"></i>
                                <span>Export</span>
                            </button>
                            <button class="bg-[#28644c] hover:bg-[#1e4e3a] text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors text-sm font-medium">
                                <i class="fas fa-filter"></i>
                                <span>Filter</span>
                            </button>
                        </div>
    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div class="dashboard-card bg-white p-6 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Visitors</p>
                                    <h3 class="text-2xl font-bold text-gray-900">0</h3>
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
                                    <h3 class="text-2xl font-bold text-gray-900">0</h3>
                                </div>
                                <div class="p-3 rounded-full bg-green-100 text-green-600">
                                    <i class="fas fa-user-check text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="h-2 bg-gray-200 rounded-full">
                                    <div class="h-2 bg-green-500 rounded-full" style="width: 60%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">+5 from yesterday</p>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="relative flex-1 max-w-md">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" placeholder="Search visitors...">
                            </div>
                            <div class="flex items-center space-x-3">
                                <select class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-[#2f855A] focus:border-[#2f855A] text-sm">
                                    <option>All Status</option>
                                    <option>Checked In</option>
                                    <option>Checked Out</option>
                                    <option>Scheduled</option>
                                    <option>Overdue</option>
                                </select>
                                <select class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-[#2f855A] focus:border-[#2f855A] text-sm">
                                    <option>All Types</option>
                                    <option>Client</option>
                                    <option>Vendor</option>
                                    <option>Contractor</option>
                                    <option>Guest</option>
                                </select>
                                <input type="date" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-[#2f855A] focus:border-[#2f855A] text-sm">
                                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#2f855A]">
                                    <i class="fas fa-filter text-gray-600 mr-2"></i>
                                    <span>Filter</span>
                                </button>
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
                signOutBtn: document.getElementById('signOutBtn')
            };

            // Initialize sidebar state
            if (window.innerWidth >= 768) {
                elements.sidebar.classList.remove('-ml-72');
                elements.mainContent.classList.add('md:ml-72', 'sidebar-open');
            } else {
                elements.sidebar.classList.add('-ml-72');
                elements.mainContent.classList.remove('md:ml-72', 'sidebar-open');
                elements.mainContent.classList.add('sidebar-closed');
            }

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
            const closeAllDropdowns = () => {
                elements.dropdownToggles.forEach(toggle => {
                    const dropdown = toggle.nextElementSibling;
                    const chevron = toggle.querySelector('.bx-chevron-down');
                    if (!dropdown.classList.contains('hidden')) {
                        dropdown.classList.add('hidden');
                        dropdown.classList.remove('active');
                        chevron.classList.remove('rotate-180');
                        dropdown.style.maxHeight = '0';
                    }
                });
            };

            // Toggle dropdowns
            elements.dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', () => {
                    const dropdown = toggle.nextElementSibling;
                    const chevron = toggle.querySelector('.bx-chevron-down');
                    const isHidden = dropdown.classList.contains('hidden');
                    closeAllDropdowns();
                    if (isHidden) {
                        dropdown.classList.remove('hidden');
                        dropdown.classList.add('active');
                        chevron.classList.add('rotate-180');
                        dropdown.style.maxHeight = `${dropdown.scrollHeight}px`;
                    }
                });
            });

            // Initialize active dropdowns
            document.querySelectorAll('.has-dropdown.active .dropdown-menu').forEach(menu => {
                menu.classList.remove('hidden');
                menu.classList.add('active');
                menu.style.maxHeight = `${menu.scrollHeight}px`;
            });

            // Event listeners
            elements.overlay?.addEventListener('click', () => {
                elements.sidebar.classList.add('-ml-72');
                elements.overlay.classList.add('hidden');
                document.body.style.overflow = '';
                elements.mainContent.classList.remove('sidebar-open');
                elements.mainContent.classList.add('sidebar-closed');
            });

            elements.toggleBtn?.addEventListener('click', toggleSidebar);
            
            elements.notificationBtn?.addEventListener('click', e => {
                e.stopPropagation();
                elements.notificationDropdown.classList.toggle('hidden');
                elements.userMenuDropdown.classList.add('hidden');
            });
            
            elements.userMenuBtn?.addEventListener('click', e => {
                e.stopPropagation();
                elements.userMenuDropdown.classList.toggle('hidden');
                elements.notificationDropdown.classList.add('hidden');
            });

            // Close dropdowns on outside click
            document.addEventListener('click', e => {
                if (elements.notificationBtn && !elements.notificationBtn.contains(e.target) && elements.notificationDropdown && !elements.notificationDropdown.contains(e.target)) {
                    elements.notificationDropdown.classList.add('hidden');
                }
                if (elements.userMenuBtn && !elements.userMenuBtn.contains(e.target) && elements.userMenuDropdown && !elements.userMenuDropdown.contains(e.target)) {
                    elements.userMenuDropdown.classList.add('hidden');
                }
                if (elements.sidebar && !elements.sidebar.contains(e.target) && elements.toggleBtn && !elements.toggleBtn.contains(e.target) && window.innerWidth < 768) {
                    toggleSidebar();
                }
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

            // Sign out
            elements.signOutBtn?.addEventListener('click', e => {
                e.preventDefault();
                document.getElementById('logout-form').submit();
            });
        });
    </script>
</body>
</html>