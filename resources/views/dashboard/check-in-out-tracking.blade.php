<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check In/Out Tracking | Administrative Dashboard</title>
    <link rel="icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
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
        .dashboard-card:nth-child(4)::before { background: linear-gradient(90deg, #ef4444, #f87171); }

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

        .status-in-progress {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-overdue {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-completed {
            background-color: #e5e7eb;
            color: #4b5563;
        }
    </style>
</head>
<body>
    <nav class="w-full p-3 h-16 bg-[#28644c] text-white shadow-md fixed top-0 left-0 right-0 z-50">
        <div class="flex justify-between items-center h-full max-w-7xl mx-auto">
            <div class="flex items-center space-x-4">
                <button id="toggle-btn" class="pl-2 focus:outline-none" aria-label="Toggle Sidebar">
                    <i class="fa-solid fa-bars text-2xl cursor-pointer"></i>
                </button>
                <h1 class="text-2xl font-bold tracking-tight">Check In/Out Tracking</h1>
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
            <!-- View Visitor Modal -->
            <div id="viewVisitorModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="view-visitor-modal-title">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                        <h3 id="view-visitor-modal-title" class="text-lg font-medium text-gray-900">Visitor Details</h3>
                        <button id="closeViewVisitorModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Visitor ID</label>
                                <div id="view_id" class="text-sm font-semibold text-gray-900">—</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Name</label>
                                <div id="view_name" class="text-sm text-gray-900">—</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Company</label>
                                <div id="view_company" class="text-sm text-gray-900">—</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Type</label>
                                <div id="view_type" class="text-sm text-gray-900">—</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Host</label>
                                <div id="view_host" class="text-sm text-gray-900">—</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Department</label>
                                <div id="view_department" class="text-sm text-gray-900">—</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Check-In Date</label>
                                <div id="view_checkin_date" class="text-sm text-gray-900">—</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Check-In Time</label>
                                <div id="view_checkin_time" class="text-sm text-gray-900">—</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Purpose</label>
                                <div id="view_purpose" class="text-sm text-gray-900">—</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Status</label>
                                <div id="view_status" class="text-sm text-gray-900">—</div>
                            </div>
                        </div>
                        <div class="pt-2 text-right">
                            <button id="closeViewVisitorModalFooter" type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Close</button>
                        </div>
                    </div>
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
                    <p class="font-semibold text-gray-900 leading-tight">New Check-In</p>
                    <p class="text-gray-600 leading-tight text-xs">Sarah Johnson checked in</p>
                    <p class="text-gray-400 text-xs mt-0.5">15 min ago</p>
                </div>
            </li>
        </ul>
        <div class="text-center py-2 border-t border-gray-200">
            <a class="text-[#28644c] text-xs font-semibold hover:underline" href="#">View all notifications</a>
        </div>
    </div>

    <!-- User Menu Dropdown (match visitors-registration design) -->
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
            <li><a id="openSignOutLink" href="{{ route('logout') }}" class="w-full text-left flex items-center px-6 py-2 text-red-600 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1" onclick="event.preventDefault(); event.stopPropagation(); if(window.openSignOutModal) window.openSignOutModal();"><i class="fas fa-sign-out-alt mr-2"></i> Sign Out</a></li>
        </ul>
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
                            <li><a href="{{ route('checkinout.tracking') }}" class="block px-3 py-2 text-sm bg-white/30 rounded-lg"><i class="bx bx-transfer mr-2"></i>Check In/Out Tracking</a></li>
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
                            <h1 class="text-2xl font-bold text-[#1a4d38]">Check In/Out Tracking</h1>
                            <p class="text-gray-600 text-sm">Track and manage visitor check-ins and check-outs in real-time</p>
                        </div>
                        
                    </div>

                    <!-- Stats Cards -->
                    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                        <div class="dashboard-card p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Currently Checked In</p>
                                    <p class="font-extrabold text-2xl mt-1 text-gray-900">{{ $stats['currently_checked_in'] ?? 0 }}</p>
                                </div>
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                    <i class="fas fa-user-check text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-card p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Today's Check-Ins</p>
                                    <p class="font-extrabold text-2xl mt-1 text-gray-900">{{ $stats['todays_checkins'] ?? 0 }}</p>
                                </div>
                                <div class="p-3 rounded-full bg-green-100 text-green-600">
                                    <i class="fas fa-sign-in-alt text-xl"></i>
                                </div>
                            </div>
                        </div>
                        
                    </section>

                    <!-- Recent Check-Ins -->
                    <section class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Recent Check-Ins</h3>
                            <p class="mt-1 text-sm text-gray-500">List of visitors currently in the premises</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visitor</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Host</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-In Time</th>
                                        
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php $__rows = $currentCheckIns ?? []; @endphp
                                    @forelse($__rows as $v)
                                        <tr class="table-row">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="ml-0">
                                                        <div class="text-sm font-medium text-gray-900">{{ $v['name'] ?? 'Visitor' }}</div>
                                                        <div class="text-xs text-gray-500">{{ $v['company'] ?? '—' }} @if(!empty($v['visitor_type'])) • {{ ucfirst($v['visitor_type']) }} @endif</div>
                                                    </div>
                                                </div>
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
                                                        try { $__fmtTime = \Carbon\Carbon::createFromFormat('H:i', $__rawTime)->format('g:i A'); }
                                                        catch (\Exception $e) {
                                                            try { $__fmtTime = \Carbon\Carbon::createFromFormat('H:i:s', $__rawTime)->format('g:i A'); }
                                                            catch (\Exception $e2) { /* leave as-is */ }
                                                        }
                                                    }
                                                @endphp
                                                <div class="text-sm text-gray-900">{{ $__fmtTime }}</div>
                                                <div class="text-xs text-gray-500">{{ $v['check_in_date'] ?? '' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="status-badge status-in-progress">In Progress</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button class="view-btn text-indigo-600 hover:text-indigo-900 mr-3" data-id="{{ $v['id'] ?? '' }}" data-tooltip="View"><i class="fas fa-eye"></i></button>
                                                <button class="text-red-600 hover:text-red-900 check-out-btn" data-id="{{ $v['id'] ?? '' }}" data-tooltip="Check Out"><i class="fas fa-sign-out-alt"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        @forelse(($allVisitors ?? []) as $v)
                                            <tr class="table-row">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="ml-0">
                                                            <div class="text-sm font-medium text-gray-900">{{ $v['name'] ?? 'Visitor' }}</div>
                                                            <div class="text-xs text-gray-500">{{ $v['company'] ?? '—' }} @if(!empty($v['visitor_type'])) • {{ ucfirst($v['visitor_type']) }} @endif</div>
                                                        </div>
                                                    </div>
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
                                                            try { $__fmtTime = \Carbon\Carbon::createFromFormat('H:i', $__rawTime)->format('g:i A'); }
                                                            catch (\Exception $e) {
                                                                try { $__fmtTime = \Carbon\Carbon::createFromFormat('H:i:s', $__rawTime)->format('g:i A'); }
                                                                catch (\Exception $e2) { /* leave as-is */ }
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="text-sm text-gray-900">{{ $__fmtTime }}</div>
                                                    <div class="text-xs text-gray-500">{{ $v['check_in_date'] ?? '' }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @php $st = strtolower($v['status'] ?? 'scheduled'); @endphp
                                                    <span class="status-badge {{ $st === 'checked_in' ? 'status-in-progress' : ($st==='checked_out' ? 'status-completed' : 'status-overdue') }}">{{ ucfirst($st) }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <button class="view-btn text-indigo-600 hover:text-indigo-900 mr-3" data-id="{{ $v['id'] ?? '' }}" data-tooltip="View"><i class="fas fa-eye"></i></button>
                                                    <button class="text-red-600 hover:text-red-900 check-out-btn" data-id="{{ $v['id'] ?? '' }}" data-tooltip="Check Out"><i class="fas fa-sign-out-alt"></i></button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">No check-ins yet.</td>
                                            </tr>
                                        @endforelse
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </a>
                                <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </a>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">No results to display</p>
                                </div>
                                <div></div>
                            </div>
                        </div>
                    </section>

                    <!-- Check-Out History -->
                    <section class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Recent Check-Outs</h3>
                            <p class="mt-1 text-sm text-gray-500">History of recent visitor check-outs</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visitor</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Host</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visit Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse(($recentCheckOuts ?? []) as $v)
                                        <tr class="table-row">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $v['name'] ?? 'Visitor' }}</div>
                                                <div class="text-xs text-gray-500">{{ $v['company'] ?? '—' }} @if(!empty($v['visitor_type'])) • {{ ucfirst($v['visitor_type']) }} @endif</div>
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
                                                <div class="text-sm text-gray-900">{{ $v['duration'] ?? '—' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="status-badge status-completed">Completed</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">No check-outs yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
            <!-- Check-In Modal -->
            <div id="checkInModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="check-in-modal-title">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                        <h3 id="check-in-modal-title" class="text-lg font-medium text-gray-900">New Check-In</h3>
                        <button id="closeCheckInModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                    <div class="p-6">
                        <form id="checkInForm" class="space-y-6">
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="visitorName" class="block text-sm font-medium text-gray-700 mb-1">Visitor Name *</label>
                                    <input type="text" id="visitorName" name="visitorName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                </div>
                                <div>
                                    <label for="hostName" class="block text-sm font-medium text-gray-700 mb-1">Host *</label>
                                    <select id="hostName" name="hostName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                        <option value="">Select a host</option>
                                        <option value="John Smith">John Smith (Marketing)</option>
                                        <option value="Lisa Wang">Lisa Wang (IT Department)</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="checkInTime" class="block text-sm font-medium text-gray-700 mb-1">Check-In Time *</label>
                                    <input type="time" id="checkInTime" name="checkInTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                </div>
                            </div>
                            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                                <button type="button" id="cancelCheckIn" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#2f855A] hover:bg-[#28644c] focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Check-Out Modal -->
            <div id="checkOutModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="check-out-modal-title">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                        <h3 id="check-out-modal-title" class="text-lg font-medium text-gray-900">Check Out Visitor</h3>
                        <button id="closeCheckOutModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-500 mb-4">Are you sure you want to check out
                            <span id="check_out_visitor_name" class="font-semibold text-gray-800">this visitor</span>?
                        </p>
                        <div class="flex justify-end space-x-3">
                            <button type="button" id="cancelCheckOut" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">
                                Cancel
                            </button>
                            <button type="button" id="confirmCheckOut" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all duration-200">
                                Check Out
                            </button>
                        </div>
                    </div>
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
        </main>
    </div>

    <!-- View Visitor Modal (moved outside main content) -->
    <div id="viewVisitorModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="view-visitor-modal-title">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="view-visitor-modal-title" class="text-lg font-medium text-gray-900">Visitor Details</h3>
                <button id="closeViewVisitorModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Visitor ID</label>
                        <div id="view_id" class="text-sm font-semibold text-gray-900">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Name</label>
                        <div id="view_name" class="text-sm text-gray-900">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Company</label>
                        <div id="view_company" class="text-sm text-gray-900">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Type</label>
                        <div id="view_type" class="text-sm text-gray-900">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Host</label>
                        <div id="view_host" class="text-sm text-gray-900">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Department</label>
                        <div id="view_department" class="text-sm text-gray-900">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Check-In Date</label>
                        <div id="view_checkin_date" class="text-sm text-gray-900">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Check-In Time</label>
                        <div id="view_checkin_time" class="text-sm text-gray-900">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Purpose</label>
                        <div id="view_purpose" class="text-sm text-gray-900">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Status</label>
                        <div id="view_status" class="text-sm text-gray-900">—</div>
                    </div>
                </div>
                <div class="pt-2 text-right">
                    <button id="closeViewVisitorModalFooter" type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Check-In Modal (moved outside main content) -->
    <div id="checkInModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="check-in-modal-title">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="check-in-modal-title" class="text-lg font-medium text-gray-900">New Check-In</h3>
                <button id="closeCheckInModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="p-6">
                <form id="checkInForm" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="visitorName" class="block text-sm font-medium text-gray-700 mb-1">Visitor Name *</label>
                            <input type="text" id="visitorName" name="visitorName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                        </div>
                        <div>
                            <label for="hostName" class="block text-sm font-medium text-gray-700 mb-1">Host *</label>
                            <select id="hostName" name="hostName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                <option value="">Select a host</option>
                                <option value="John Smith">John Smith (Marketing)</option>
                                <option value="Lisa Wang">Lisa Wang (IT Department)</option>
                            </select>
                        </div>
                        <div>
                            <label for="checkInTime" class="block text-sm font-medium text-gray-700 mb-1">Check-In Time *</label>
                            <input type="time" id="checkInTime" name="checkInTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button type="button" id="cancelCheckIn" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#2f855A] hover:bg-[#28644c] focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Check-Out Modal (moved outside main content) -->
    <div id="checkOutModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="check-out-modal-title">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="check-out-modal-title" class="text-lg font-medium text-gray-900">Check Out Visitor</h3>
                <button id="closeCheckOutModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-500 mb-4">Are you sure you want to check out
                    <span id="check_out_visitor_name" class="font-semibold text-gray-800">this visitor</span>?
                </p>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelCheckOut" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">
                        Cancel
                    </button>
                    <button type="button" id="confirmCheckOut" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all duration-200">
                        Check Out
                    </button>
                </div>
            </div>
        </div>
    </div>

  <!-- Profile Modal -->
  <div id="profileModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="profile-modal-title">
    <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
      <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
        <h3 id="profile-modal-title" class="font-semibold text-sm text-gray-900 select-none">My Profile</h3>
        <button id="closeProfileBtn" onclick="closeProfileModal()" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
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
            <button id="closeProfileBtn2" onclick="closeProfileModal()" type="button" class="bg-[#28644c] hover:bg-[#2f855A] text-white text-sm font-semibold rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Account Settings Modal -->
  <div id="accountSettingsModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="account-settings-modal-title">
    <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
      <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
        <h3 id="account-settings-modal-title" class="font-semibold text-sm text-gray-900 select-none">Account Settings</h3>
        <button id="closeAccountSettingsBtn" onclick="closeAccountSettingsModal()" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
          <i class="fas fa-times text-xs"></i>
        </button>
      </div>
      <div class="px-8 pt-6 pb-8">
        <form class="space-y-4 text-xs text-gray-700" action="{{ route('account.update') }}" method="POST">
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
            <button type="button" id="cancelAccountSettingsBtn" onclick="closeAccountSettingsModal()" class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
            <button type="submit" class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Privacy & Security Modal -->
  <div id="privacySecurityModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="privacy-security-modal-title">
    <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
      <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
        <h3 id="privacy-security-modal-title" class="font-semibold text-sm text-gray-900 select-none">Privacy & Security</h3>
        <button id="closePrivacySecurityBtn" onclick="closePrivacySecurityModal()" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
          <i class="fas fa-times text-xs"></i>
        </button>
      </div>
      <div class="px-8 pt-6 pb-8">
        <form class="space-y-4 text-xs text-gray-900" action="{{ route('privacy.update') }}" method="POST">
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
            <button class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200" id="cancelPrivacySecurityBtn" onclick="closePrivacySecurityModal()" type="button">Cancel</button>
            <button class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200" type="submit">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Sign Out Modal -->
  <div id="signOutModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="sign-out-modal-title">
    <div class="bg-white rounded-md shadow-lg w-[360px] max-w-full mx-4 text-center" role="document">
      <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
        <h3 id="sign-out-modal-title" class="font-semibold text-sm text-gray-900 select-none">Sign Out</h3>
        <button id="cancelSignOutBtn" onclick="closeSignOutModal()" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
          <i class="fas fa-times text-xs"></i>
        </button>
      </div>
      <div class="px-8 pt-6 pb-8">
        <div class="mx-auto mb-4 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
          <i class="fas fa-sign-out-alt text-red-600 text-xl"></i>
        </div>
        <p class="text-xs text-gray-600 mb-6">Are you sure you want to sign out of your account?</p>
        <div class="flex justify-center space-x-4">
          <button id="cancelSignOutBtn2" onclick="closeSignOutModal()" class="bg-gray-200 text-gray-800 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-red-600 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-sm transition-all duration-200">Sign Out</button>
          </form>
        </div>
      </div>
    </div>
  </div>
    <script>
        // Global helpers to open/close modals used by inline onclicks
        window.__suppressOutsideClose = false;
        window.openSignOutModal = function(){
            window.__suppressOutsideClose = true;
            var m = document.getElementById('signOutModal');
            if(!m) return; m.classList.remove('hidden'); m.classList.add('active'); document.body.classList.add('overflow-hidden');
            var ud=document.getElementById('userMenuDropdown'); if(ud) ud.classList.add('hidden');
            setTimeout(function(){ window.__suppressOutsideClose = false; }, 0);
        };
        window.closeSignOutModal = function(){
            var m = document.getElementById('signOutModal');
            if(!m) return; m.classList.add('hidden'); m.classList.remove('active'); document.body.classList.remove('overflow-hidden');
        };
        window.closeProfileModal = function(){ var m=document.getElementById('profileModal'); if(!m) return; m.classList.add('hidden'); m.classList.remove('active'); document.body.classList.remove('overflow-hidden'); };
        window.closeAccountSettingsModal = function(){ var m=document.getElementById('accountSettingsModal'); if(!m) return; m.classList.add('hidden'); m.classList.remove('active'); document.body.classList.remove('overflow-hidden'); };
        window.closePrivacySecurityModal = function(){ var m=document.getElementById('privacySecurityModal'); if(!m) return; m.classList.add('hidden'); m.classList.remove('active'); document.body.classList.remove('overflow-hidden'); };
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
                // Profile/Settings/Privacy/SignOut modals
                profileModal: document.getElementById('profileModal'),
                closeProfileBtn: document.getElementById('closeProfileBtn'),
                closeProfileBtn2: document.getElementById('closeProfileBtn2'),
                openAccountSettingsBtn: document.getElementById('openAccountSettingsBtn'),
                accountSettingsModal: document.getElementById('accountSettingsModal'),
                closeAccountSettingsBtn: document.getElementById('closeAccountSettingsBtn'),
                cancelAccountSettingsBtn: document.getElementById('cancelAccountSettingsBtn'),
                openPrivacySecurityBtn: document.getElementById('openPrivacySecurityBtn'),
                privacySecurityModal: document.getElementById('privacySecurityModal'),
                closePrivacySecurityBtn: document.getElementById('closePrivacySecurityBtn'),
                cancelPrivacySecurityBtn: document.getElementById('cancelPrivacySecurityBtn'),
                signOutModal: document.getElementById('signOutModal'),
                cancelSignOutBtn: document.getElementById('cancelSignOutBtn'),
                cancelSignOutBtn2: document.getElementById('cancelSignOutBtn2'),
                checkInBtn: document.getElementById('checkInBtn'),
                checkInModal: document.getElementById('checkInModal'),
                closeCheckInModal: document.getElementById('closeCheckInModal'),
                cancelCheckIn: document.getElementById('cancelCheckIn'),
                checkInForm: document.getElementById('checkInForm'),
                checkOutModal: document.getElementById('checkOutModal'),
                closeCheckOutModal: document.getElementById('closeCheckOutModal'),
                cancelCheckOut: document.getElementById('cancelCheckOut'),
                confirmCheckOut: document.getElementById('confirmCheckOut'),
                checkOutButtons: document.querySelectorAll('.check-out-btn'),
                // View modal related
                viewButtons: document.querySelectorAll('.view-btn'),
                viewVisitorModal: document.getElementById('viewVisitorModal'),
                closeViewVisitorModal: document.getElementById('closeViewVisitorModal'),
                closeViewVisitorModalFooter: document.getElementById('closeViewVisitorModalFooter'),
                signOutBtn: document.getElementById('signOutBtn'),
                tooltipTriggers: document.querySelectorAll('[data-tooltip]')
            };

            // Define routes and csrf for AJAX calls
            const csrf = '{{ csrf_token() }}';
            const routes = {
                get: '{{ route('visitor.get') }}',
                update: '{{ route('visitor.update') }}'
            };
            let selectedVisitorId = null;

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

            // Toggle check-in modal
            const toggleCheckInModal = () => {
                elements.checkInModal.classList.toggle('hidden');
                elements.checkInModal.classList.toggle('active');
                document.body.classList.toggle('overflow-hidden');
                elements.notificationDropdown.classList.add('hidden');
                elements.userMenuDropdown.classList.add('hidden');
                elements.checkOutModal.classList.add('hidden');
            };

            // Toggle check-out modal
            const toggleCheckOutModal = () => {
                elements.checkOutModal.classList.toggle('hidden');
                elements.checkOutModal.classList.toggle('active');
                document.body.classList.toggle('overflow-hidden');
                elements.notificationDropdown.classList.add('hidden');
                elements.userMenuDropdown.classList.add('hidden');
                elements.checkInModal.classList.add('hidden');
            };

            // Event listeners
            elements.overlay?.addEventListener('click', () => {
                elements.sidebar.classList.add('-ml-72');
                elements.overlay.classList.add('hidden');
                document.body.style.overflow = '';
                elements.mainContent.classList.remove('sidebar-open');
                elements.mainContent.classList.add('sidebar-closed');
            });

            elements.toggleBtn?.addEventListener('click', toggleSidebar);
            // Helpers for modals
            const openModal = (el) => { el?.classList.remove('hidden'); el?.classList.add('active'); document.body.style.overflow='hidden'; };
            const closeModal = (el) => { el?.classList.add('hidden'); el?.classList.remove('active'); document.body.style.overflow=''; };
            elements.notificationBtn?.addEventListener('click', e => {
                e.stopPropagation();
                elements.notificationDropdown.classList.toggle('hidden');
                elements.userMenuDropdown.classList.add('hidden');
                elements.checkInModal.classList.remove('active');
                elements.checkOutModal.classList.remove('active');
            });
            elements.userMenuBtn?.addEventListener('click', e => {
                e.stopPropagation();
                elements.userMenuDropdown.classList.toggle('hidden');
                elements.notificationDropdown.classList.add('hidden');
                elements.checkInModal.classList.remove('active');
                elements.checkOutModal.classList.remove('active');
            });
            // Ensure Sign Out opens modal even if inline handler fails
            document.getElementById('openSignOutLink')?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (window.openSignOutModal) window.openSignOutModal();
            });
            // Open profile
            document.getElementById('openProfileBtn')?.addEventListener('click', (e) => {
                e.stopPropagation();
                elements.userMenuDropdown.classList.add('hidden');
                openModal(elements.profileModal);
            });
            elements.closeProfileBtn?.addEventListener('click', () => closeModal(elements.profileModal));
            elements.closeProfileBtn2?.addEventListener('click', () => closeModal(elements.profileModal));

            // Account settings modal
            elements.openAccountSettingsBtn?.addEventListener('click', (e) => {
                e.stopPropagation();
                elements.userMenuDropdown.classList.add('hidden');
                openModal(elements.accountSettingsModal);
            });
            elements.closeAccountSettingsBtn?.addEventListener('click', () => closeModal(elements.accountSettingsModal));
            elements.cancelAccountSettingsBtn?.addEventListener('click', () => closeModal(elements.accountSettingsModal));

            // Privacy & security modal
            elements.openPrivacySecurityBtn?.addEventListener('click', (e) => {
                e.stopPropagation();
                elements.userMenuDropdown.classList.add('hidden');
                openModal(elements.privacySecurityModal);
            });
            elements.closePrivacySecurityBtn?.addEventListener('click', () => closeModal(elements.privacySecurityModal));
            elements.cancelPrivacySecurityBtn?.addEventListener('click', () => closeModal(elements.privacySecurityModal));

            // Sign out modal (triggered from dropdown via inline handler); add close buttons
            elements.cancelSignOutBtn?.addEventListener('click', () => closeModal(elements.signOutModal));
            elements.cancelSignOutBtn2?.addEventListener('click', () => closeModal(elements.signOutModal));
            elements.checkInBtn?.addEventListener('click', toggleCheckInModal);
            elements.closeCheckInModal?.addEventListener('click', toggleCheckInModal);
            elements.cancelCheckIn?.addEventListener('click', toggleCheckInModal);
            elements.checkInModal?.addEventListener('click', e => {
                if (e.target === elements.checkInModal) toggleCheckInModal();
            });
            elements.closeCheckOutModal?.addEventListener('click', toggleCheckOutModal);
            elements.cancelCheckOut?.addEventListener('click', toggleCheckOutModal);
            elements.checkOutModal?.addEventListener('click', e => {
                if (e.target === elements.checkOutModal) toggleCheckOutModal();
            });

            // Form submission
            elements.checkInForm?.addEventListener('submit', e => {
                e.preventDefault();
                toggleCheckInModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Check-In Registered',
                    text: 'The visitor check-in has been registered successfully.',
                    confirmButtonColor: '#2f855A'
                });
            });

            // Sign out
            elements.signOutBtn?.addEventListener('click', e => {
                e.preventDefault();
                document.getElementById('logout-form').submit();
            });

            // Close dropdowns and modals on outside click
            document.addEventListener('click', e => {
                if (window.__suppressOutsideClose) { return; }
                if (!elements.notificationBtn.contains(e.target) && !elements.notificationDropdown.contains(e.target)) {
                    elements.notificationDropdown.classList.add('hidden');
                }
                if (!elements.userMenuBtn.contains(e.target) && !elements.userMenuDropdown.contains(e.target)) {
                    elements.userMenuDropdown.classList.add('hidden');
                }
                if (!elements.profileModal.contains(e.target)) { elements.profileModal.classList.remove('active'); elements.profileModal.classList.add('hidden'); }
                if (!elements.accountSettingsModal.contains(e.target)) { elements.accountSettingsModal.classList.remove('active'); elements.accountSettingsModal.classList.add('hidden'); }
                if (!elements.privacySecurityModal.contains(e.target)) { elements.privacySecurityModal.classList.remove('active'); elements.privacySecurityModal.classList.add('hidden'); }
                if (!elements.signOutModal.contains(e.target)) { elements.signOutModal.classList.remove('active'); elements.signOutModal.classList.add('hidden'); }
                if (!elements.checkInBtn.contains(e.target) && !elements.checkInModal.contains(e.target)) {
                    elements.checkInModal.classList.remove('active');
                    elements.checkInModal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
                if (!elements.checkOutModal.contains(e.target)) {
                    elements.checkOutModal.classList.remove('active');
                    elements.checkOutModal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
                if (!elements.sidebar.contains(e.target) && !elements.toggleBtn.contains(e.target) && window.innerWidth < 768) {
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

            // Tooltips
            elements.tooltipTriggers.forEach(trigger => {
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

            // View button -> open modal and fetch details
            document.addEventListener('click', async (e) => {
                const btn = e.target.closest('.view-btn');
                if (!btn) return;
                const id = btn.getAttribute('data-id');
                if (!id) return;
                selectedVisitorId = id;
                try {
                    const url = new URL(routes.get, window.location.origin);
                    url.searchParams.set('id', id);
                    const resp = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                    const data = await resp.json();
                    if (!data.success) throw new Error(data.message || 'Failed to fetch visitor');
                    const v = data.visitor || {};
                    // Populate fields
                    const set = (k, val) => { const el = document.getElementById(k); if (el) el.textContent = val || '—'; };
                    set('view_id', v.id);
                    set('view_name', v.name);
                    set('view_company', v.company);
                    set('view_type', v.visitor_type ? v.visitor_type.charAt(0).toUpperCase()+v.visitor_type.slice(1) : '—');
                    set('view_host', v.host);
                    set('view_department', v.host_department);
                    set('view_checkin_date', v.check_in_date);
                    set('view_checkin_time', v.check_in_time);
                    set('view_purpose', v.purpose);
                    set('view_status', v.status ? v.status.charAt(0).toUpperCase()+v.status.slice(1) : '—');
                    openModal(elements.viewVisitorModal);
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Unable to load visitor.' });
                }
            });

            elements.closeViewVisitorModal?.addEventListener('click', () => closeModal(elements.viewVisitorModal));
            elements.closeViewVisitorModalFooter?.addEventListener('click', () => closeModal(elements.viewVisitorModal));

            // Check-out button -> fetch details and open confirm modal
            document.addEventListener('click', async (e) => {
                const btn = e.target.closest('.check-out-btn');
                if (!btn) return;
                const id = btn.getAttribute('data-id');
                if (!id) return;
                selectedVisitorId = id;
                // Try to fetch visitor to show name in modal
                try {
                    const url = new URL(routes.get, window.location.origin);
                    url.searchParams.set('id', id);
                    const resp = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                    const data = await resp.json();
                    if (data?.success && data.visitor) {
                        const nameEl = document.getElementById('check_out_visitor_name');
                        if (nameEl) nameEl.textContent = data.visitor.name || 'this visitor';
                    }
                } catch (_) {
                    // no-op, fallback will keep default label
                }
                openModal(elements.checkOutModal);
            });

            // Cancel/close checkout
            elements.cancelCheckOut?.addEventListener('click', () => closeModal(elements.checkOutModal));
            elements.closeCheckOutModal?.addEventListener('click', () => closeModal(elements.checkOutModal));

            // Confirm checkout -> POST visitor.update
            elements.confirmCheckOut?.addEventListener('click', async () => {
                if (!selectedVisitorId) return;
                try {
                    const resp = await fetch(routes.update, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ id: selectedVisitorId, status: 'checked_out' })
                    });
                    const data = await resp.json();
                    if (!data.success) throw new Error(data.message || 'Failed to update');
                    closeModal(elements.checkOutModal);
                    // Simple refresh to reflect changes
                    window.location.reload();
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Unable to check out visitor.' });
                }
            });
        });
    </script>
</body>
</html>