@php
// Get the authenticated user
$user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compliance Tracking | Administrative Dashboard</title>
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
            max-width: 1152px;
            margin: 0 auto;
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
        .dashboard-card:nth-child(2)::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .dashboard-card:nth-child(3)::before { background: linear-gradient(90deg, #10b981, #34d399); }
        .dashboard-card:nth-child(4)::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }

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
    </style>
</head>
<body>
    <nav class="w-full p-3 h-16 bg-[#28644c] text-white shadow-md fixed top-0 left-0 right-0 z-50">
        <div class="flex justify-between items-center h-full max-w-7xl mx-auto">
            <div class="flex items-center space-x-4">
                <button id="toggle-btn" class="pl-2 focus:outline-none" aria-label="Toggle Sidebar">
                    <i class="fa-solid fa-bars text-2xl cursor-pointer"></i>
                </button>
                <h1 class="text-2xl font-bold tracking-tight">Compliance Tracking</h1>
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
        </ul>
        <div class="text-center py-2 border-t border-gray-200">
            <a class="text-[#28644c] text-xs font-semibold hover:underline" href="#">View all notifications</a>
        </div>
    </div>

    <div class="flex w-full min-h-screen pt-16">
        <div id="overlay" class="hidden fixed inset-0 bg-black opacity-50 z-40"></div>

<<<<<<< HEAD
         <aside id="sidebar" class="bg-[#2f855A] text-white flex flex-col z-40 fixed top-16 bottom-0 w-72 -ml-72 md:sticky md:ml-0 transition-all duration-300 ease-in-out overflow-y-auto">
=======
        <aside id="sidebar" class="bg-[#2f855A] text-white flex flex-col z-40 fixed top-16 bottom-0 w-72 -ml-72 md:sticky md:ml-0 transition-all duration-300 ease-in-out overflow-y-auto">
>>>>>>> 3467a8cdf3aef1c3632815755eba1f09b252a719
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
                    <li class="has-dropdown active">
                        <div class="flex items-center font-medium justify-between text-lg bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-file"></i>
                                <span>Legal Management</span>
                            </div>
                            <i class="bx bx-chevron-down text-2xl transition-transform duration-300 rotate-180"></i>
                        </div>
                        <ul class="dropdown-menu bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
                            <li><a href="{{ route('case.management') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-briefcase mr-2"></i>Case Management</a></li>
                            <li><a href="{{ route('contract.management') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-file-blank mr-2"></i>Contract Management</a></li>
                            <li><a href="{{ route('document.compliance.tracking') }}" class="block px-3 py-2 text-sm bg-white/30 rounded-lg"><i class="bx bx-check-double mr-2"></i>Compliance Tracking</a></li>
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
            <div class="dashboard-container">
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-md border border-gray-100 p-8 space-y-6">
                    <!-- Page Header -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                        <div>
                            <h1 class="text-2xl font-bold text-[#1a4d38]">Compliance Tracking</h1>
                            <p class="text-gray-600 text-sm">Monitor and manage all compliance requirements and deadlines</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <button id="addComplianceBtn" class="px-4 py-2 bg-[#2f855A] text-white rounded-lg hover:bg-[#28644c] transition-colors duration-200 flex items-center text-sm font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]">
                                <i class="fas fa-plus mr-2"></i> Add New Compliance
                            </button>
                        </div>
                    </div>

<<<<<<< HEAD

=======
>>>>>>> 3467a8cdf3aef1c3632815755eba1f09b252a719
                    <!-- Stats Cards -->
                    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="dashboard-card p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Active Compliances</p>
<<<<<<< HEAD
                                    <p class="font-extrabold text-2xl mt-1 text-gray-900">{{ $stats['active'] ?? 0 }}</p>
=======
                                    <p class="font-extrabold text-2xl mt-1 text-gray-900">18</p>
>>>>>>> 3467a8cdf3aef1c3632815755eba1f09b252a719
                                </div>
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                    <i class="fas fa-clipboard-check text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-card p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Due This Month</p>
<<<<<<< HEAD
                                    <p class="font-extrabold text-2xl mt-1 text-gray-900">{{ $stats['due_this_month'] ?? 0 }}</p>
=======
                                    <p class="font-extrabold text-2xl mt-1 text-gray-900">5</p>
>>>>>>> 3467a8cdf3aef1c3632815755eba1f09b252a719
                                </div>
                                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                    <i class="fas fa-calendar-day text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-card p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Pending Review</p>
<<<<<<< HEAD
                                    <p class="font-extrabold text-2xl mt-1 text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
=======
                                    <p class="font-extrabold text-2xl mt-1 text-gray-900">3</p>
>>>>>>> 3467a8cdf3aef1c3632815755eba1f09b252a719
                                </div>
                                <div class="p-3 rounded-full bg-green-100 text-green-600">
                                    <i class="fas fa-search text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-card p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">At Risk</p>
<<<<<<< HEAD
                                    <p class="font-extrabold text-2xl mt-1 text-gray-900">{{ $stats['at_risk'] ?? 0 }}</p>
=======
                                    <p class="font-extrabold text-2xl mt-1 text-gray-900">2</p>
>>>>>>> 3467a8cdf3aef1c3632815755eba1f09b252a719
                                </div>
                                <div class="p-3 rounded-full bg-red-100 text-red-600">
                                    <i class="fas fa-exclamation-triangle text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="bg-gradient-to-br from-white to-gray-50 rounded-lg p-6 shadow-sm border border-gray-100">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="relative flex-1 max-w-md">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="searchInput" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] text-sm" placeholder="Search compliances...">
                            </div>
                            <div class="flex space-x-3">
                                <select id="filterStatus" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-[#2f855A] focus:border-[#2f855A]">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                                <select id="filterType" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-[#2f855A] focus:border-[#2f855A]">
                                    <option value="">All Types</option>
                                    <option value="legal">Legal</option>
                                    <option value="financial">Financial</option>
                                    <option value="hr">HR</option>
                                    <option value="safety">Safety</option>
                                </select>
                                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#2f855A]">
                                    <i class="fas fa-filter text-gray-600 mr-2"></i>
                                    <span>Filter</span>
                                </button>
                            </div>
                        </div>
                    </section>
                    <section class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compliance ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
<<<<<<< HEAD
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
                                                <div class="text-xs text-gray-500">Created: {{ $item->created_at->format('Y-m-d') }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $item->title }}</div>
                                                <div class="text-xs text-gray-500">{{ $item->description ? Str::limit($item->description, 50) : 'No description' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $typeBadge }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $item->due_date->format('Y-m-d') }}</div>
                                                <div class="text-xs text-gray-500">{{ $daysText }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">{{ $statusBadge }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="#" class="viewComplianceBtn text-[#2f855A] hover:text-[#1a4d38] mr-3" data-tooltip="View" data-id="{{ $item->id }}" data-code="{{ $item->code }}" data-title="{{ $item->title }}" data-type="{{ $typeBadge }}" data-status="{{ $statusBadge }}" data-due-date="{{ $item->due_date->format('Y-m-d') }}" data-description="{{ $item->description }}" data-responsible="{{ $item->responsible_person }}" data-priority="{{ $item->priority }}"><i class="fas fa-eye"></i></a>
                                                <a href="#" class="editComplianceBtn text-blue-600 hover:text-blue-900 mr-3" data-tooltip="Edit" data-id="{{ $item->id }}" data-title="{{ $item->title }}" data-type="{{ $item->type }}" data-status="{{ $item->status }}" data-due-date="{{ $item->due_date->format('Y-m-d') }}" data-description="{{ $item->description }}" data-responsible="{{ $item->responsible_person }}" data-priority="{{ $item->priority }}"><i class="fas fa-edit"></i></a>
                                                <a href="#" class="deleteComplianceBtn text-red-600 hover:text-red-900" data-tooltip="Delete" data-id="{{ $item->id }}" data-title="{{ $item->title }}"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">No compliance items found.</td>
                                        </tr>
                                    @endforelse
=======
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
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Financial</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">2023-12-31</div>
                                            <div class="text-xs text-gray-500">in 89 days</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">On Track</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="#" class="text-[#2f855A] hover:text-[#1a4d38] mr-3" data-tooltip="View"><i class="fas fa-eye"></i></a>
                                            <a href="#" class="text-blue-600 hover:text-blue-900 mr-3" data-tooltip="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="#" class="text-red-600 hover:text-red-900" data-tooltip="Delete"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
>>>>>>> 3467a8cdf3aef1c3632815755eba1f09b252a719
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
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
                                    <p class="text-sm text-gray-700">
                                        Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">20</span> results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Previous</span>
                                            <i class="fas fa-chevron-left h-5 w-5"></i>
                                        </a>
                                        <a href="#" aria-current="page" class="z-10 bg-[#2f855A] border-[#2f855A] text-white relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            1
                                        </a>
                                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            2
                                        </a>
                                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            3
                                        </a>
                                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
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

            <!-- Add Compliance Modal -->
<<<<<<< HEAD
            
            
            
            
=======
            <div id="addComplianceModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="add-compliance-modal-title">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                        <h3 id="add-compliance-modal-title" class="text-lg font-medium text-gray-900">Add New Compliance</h3>
                        <button id="closeAddComplianceModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                    <div class="p-6">
                        <form id="addComplianceForm" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label for="complianceTitle" class="block text-sm font-medium text-gray-700 mb-1">Compliance Title *</label>
                                    <input type="text" id="complianceTitle" name="complianceTitle" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                </div>
                                <div>
                                    <label for="complianceType" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                    <select id="complianceType" name="complianceType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                        <option value="">Select a type</option>
                                        <option value="legal">Legal</option>
                                        <option value="financial">Financial</option>
                                        <option value="hr">HR</option>
                                        <option value="safety">Safety</option>
                                        <option value="environmental">Environmental</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="dueDate" class="block text-sm font-medium text-gray-700 mb-1">Due Date *</label>
                                    <input type="date" id="dueDate" name="dueDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                </div>
                                <div class="col-span-2">
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]"></textarea>
                                </div>
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button type="button" id="cancelAddCompliance" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#2f855A] hover:bg-[#28644c] focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">
                                    Save Compliance
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
>>>>>>> 3467a8cdf3aef1c3632815755eba1f09b252a719

            <!-- User Menu Dropdown -->
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

<<<<<<< HEAD
            <!-- Profile, Account, Privacy, and Sign Out modals moved below to be outside main content for full-page overlay -->
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
                <form class="space-y-4 text-xs text-gray-700" action="{{ route('profile.update') }}" method="POST">
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

    <!-- Add Compliance Modal (moved outside main content) -->
    <div id="addComplianceModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="add-compliance-modal-title">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="add-compliance-modal-title" class="text-lg font-medium text-gray-900">Add New Compliance</h3>
                <button id="closeAddComplianceModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="p-6">
                <form id="addComplianceForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label for="complianceTitle" class="block text-sm font-medium text-gray-700 mb-1">Compliance Title *</label>
                            <input type="text" id="complianceTitle" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                        </div>
                        <div>
                            <label for="complianceType" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                            <select id="complianceType" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                <option value="">Select a type</option>
                                <option value="legal">Legal</option>
                                <option value="financial">Financial</option>
                                <option value="hr">HR</option>
                                <option value="safety">Safety</option>
                                <option value="environmental">Environmental</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label for="dueDate" class="block text-sm font-medium text-gray-700 mb-1">Due Date *</label>
                            <input type="date" id="dueDate" name="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                        </div>
                        <div>
                            <label for="responsiblePerson" class="block text-sm font-medium text-gray-700 mb-1">Responsible Person</label>
                            <input type="text" id="responsiblePerson" name="responsible_person" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]">
                        </div>
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <select id="priority" name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancelAddCompliance" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#2f855A] hover:bg-[#28644c] focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">
                            Save Compliance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Compliance Modal (moved outside main content) -->
    <div id="viewComplianceModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="view-compliance-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-[380px] max-w-full mx-4" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                <h3 id="view-compliance-modal-title" class="font-semibold text-sm text-gray-900 select-none">Compliance Details</h3>
                <button id="closeViewComplianceModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div class="px-8 pt-6 pb-8 text-xs text-gray-700 space-y-2">
                <div><span class="font-semibold">Code:</span> <span id="viewComplianceCode"></span></div>
                <div><span class="font-semibold">Title:</span> <span id="viewComplianceTitle"></span></div>
                <div><span class="font-semibold">Type:</span> <span id="viewComplianceType"></span></div>
                <div><span class="font-semibold">Status:</span> <span id="viewComplianceStatus"></span></div>
                <div><span class="font-semibold">Due Date:</span> <span id="viewComplianceDueDate"></span></div>
                <div><span class="font-semibold">Responsible:</span> <span id="viewComplianceResponsible"></span></div>
                <div><span class="font-semibold">Priority:</span> <span id="viewCompliancePriority"></span></div>
                <div><span class="font-semibold">Description:</span> <span id="viewComplianceDescription"></span></div>
                <div class="pt-4 text-right">
                    <button id="closeViewComplianceModal2" type="button" class="bg-[#28644c] hover:bg-[#2f855A] text-white text-sm font-semibold rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Compliance Modal (moved outside main content) -->
    <div id="editComplianceModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="edit-compliance-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-[380px] max-w-full mx-4" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                <h3 id="edit-compliance-modal-title" class="font-semibold text-sm text-gray-900 select-none">Edit Compliance</h3>
                <button id="closeEditComplianceModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div class="px-8 pt-6 pb-8">
                <form id="editComplianceForm" class="space-y-3 text-xs text-gray-700">
                    <input type="hidden" id="editComplianceId">
                    <div>
                        <label for="editComplianceTitle" class="block mb-1 font-semibold">Title</label>
                        <input type="text" id="editComplianceTitle" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                    </div>
                    <div>
                        <label for="editComplianceType" class="block mb-1 font-semibold">Type</label>
                        <select id="editComplianceType" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                            <option value="legal">Legal</option>
                            <option value="financial">Financial</option>
                            <option value="hr">HR</option>
                            <option value="safety">Safety</option>
                            <option value="environmental">Environmental</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="editComplianceStatus" class="block mb-1 font-semibold">Status</label>
                        <select id="editComplianceStatus" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="overdue">Overdue</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div>
                        <label for="editComplianceDueDate" class="block mb-1 font-semibold">Due Date</label>
                        <input type="date" id="editComplianceDueDate" name="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                    </div>
                    <div>
                        <label for="editComplianceResponsible" class="block mb-1 font-semibold">Responsible Person</label>
                        <input type="text" id="editComplianceResponsible" name="responsible_person" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]">
                    </div>
                    <div>
                        <label for="editCompliancePriority" class="block mb-1 font-semibold">Priority</label>
                        <select id="editCompliancePriority" name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div>
                        <label for="editComplianceDescription" class="block mb-1 font-semibold">Description</label>
                        <textarea id="editComplianceDescription" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3 pt-2">
                        <button type="button" id="cancelEditCompliance" class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
                        <button type="submit" class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Compliance Modal (moved outside main content) -->
    <div id="deleteComplianceModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="delete-compliance-modal-title">
        <div class="bg-white rounded-md shadow-lg w-[360px] max-w-full mx-4" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                <h3 id="delete-compliance-modal-title" class="font-semibold text-sm text-gray-900 select-none">Delete Compliance</h3>
                <button id="closeDeleteComplianceModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div class="px-8 pt-6 pb-8 text-center">
                <p class="text-xs text-gray-700 mb-4">Are you sure you want to delete <span class="font-semibold" id="deleteComplianceTitle"></span>?</p>
                <div class="flex justify-center space-x-3">
                    <button type="button" id="cancelDeleteCompliance" class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
                    <button type="button" id="confirmDeleteCompliance" class="bg-red-600 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-sm transition-all duration-200">Delete</button>
                </div>
            </div>
        </div>
    </div>

=======
            <!-- Profile Modal -->
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

            <!-- Account Settings Modal -->
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

            <!-- Privacy & Security Modal -->
            <div id="privacySecurityModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="privacy-security-modal-title">
                <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
                    <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                        <h3 id="privacy-security-modal-title" class="font-semibold text-sm text-gray-900 select-none">Privacy & Security</h3>
                        <button id="closePrivacySecurityBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                    <div class="px-8 pt-6 pb-8">
                        <form class="space-y-4 text-xs text-gray-700" action="{{ route('profile.update') }}" method="POST">
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

            <!-- Sign Out Modal -->
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
        </main>
    </div>

>>>>>>> 3467a8cdf3aef1c3632815755eba1f09b252a719
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
            const addComplianceBtn = document.getElementById("addComplianceBtn");
            const addComplianceModal = document.getElementById("addComplianceModal");
            const closeAddComplianceModal = document.getElementById("closeAddComplianceModal");
            const cancelAddCompliance = document.getElementById("cancelAddCompliance");
            const addComplianceForm = document.getElementById("addComplianceForm");
            const tooltipTriggers = document.querySelectorAll('[data-tooltip]');

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
                addComplianceModal.classList.remove("active");
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
                addComplianceModal.classList.remove("active");
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
                addComplianceModal.classList.remove("active");
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
                addComplianceModal.classList.remove("active");
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
                addComplianceModal.classList.remove("active");
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
                addComplianceModal.classList.remove("active");
            });

            closePrivacySecurityBtn.addEventListener("click", () => {
                privacySecurityModal.classList.remove("active");
            });
            cancelPrivacySecurityBtn.addEventListener("click", () => {
                privacySecurityModal.classList.remove("active");
            });

            cancelSignOutBtn.addEventListener("click", () => {
                signOutModal.classList.remove("active");
            });
            cancelSignOutBtn2.addEventListener("click", () => {
                signOutModal.classList.remove("active");
            });

            addComplianceBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                addComplianceModal.classList.add("active");
                userMenuDropdown.classList.add("hidden");
                userMenuBtn.setAttribute("aria-expanded", "false");
                profileModal.classList.remove("active");
                accountSettingsModal.classList.remove("active");
                privacySecurityModal.classList.remove("active");
                notificationDropdown.classList.add("hidden");
                signOutModal.classList.remove("active");
            });

            closeAddComplianceModal.addEventListener("click", () => {
                addComplianceModal.classList.remove("active");
            });
            cancelAddCompliance.addEventListener("click", () => {
                addComplianceModal.classList.remove("active");
            });

<<<<<<< HEAD
            // Handle View/Edit/Delete buttons
            const viewBtns = document.querySelectorAll('.viewComplianceBtn');
            const editBtns = document.querySelectorAll('.editComplianceBtn');
            const deleteBtns = document.querySelectorAll('.deleteComplianceBtn');
            const viewComplianceModal = document.getElementById('viewComplianceModal');
            const editComplianceModal = document.getElementById('editComplianceModal');
            const deleteComplianceModal = document.getElementById('deleteComplianceModal');
            const closeViewComplianceModal = document.getElementById('closeViewComplianceModal');
            const closeViewComplianceModal2 = document.getElementById('closeViewComplianceModal2');
            const closeEditComplianceModal = document.getElementById('closeEditComplianceModal');
            const cancelEditCompliance = document.getElementById('cancelEditCompliance');
            const closeDeleteComplianceModal = document.getElementById('closeDeleteComplianceModal');
            const cancelDeleteCompliance = document.getElementById('cancelDeleteCompliance');
            const confirmDeleteCompliance = document.getElementById('confirmDeleteCompliance');

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
                document.getElementById('editComplianceType').value = data.type;
                document.getElementById('editComplianceStatus').value = data.status;
                document.getElementById('editComplianceDueDate').value = data.dueDate;
                document.getElementById('editComplianceResponsible').value = data.responsible || '';
                document.getElementById('editCompliancePriority').value = data.priority || 'medium';
                document.getElementById('editComplianceDescription').value = data.description || '';
                editComplianceModal.classList.remove('hidden');
                editComplianceModal.classList.add('active');
            }

            function openDeleteModal(data) {
                document.getElementById('deleteComplianceTitle').textContent = data.title;
                deleteComplianceModal.classList.remove('hidden');
                deleteComplianceModal.classList.add('active');
            }

            viewBtns.forEach(btn => btn.addEventListener('click', (e) => {
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

            editBtns.forEach(btn => btn.addEventListener('click', (e) => {
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

            deleteBtns.forEach(btn => btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const d = e.currentTarget.dataset;
                openDeleteModal({ id: d.id, title: d.title });
            }));

            // Close buttons for modals
            closeViewComplianceModal.addEventListener('click', () => { viewComplianceModal.classList.remove('active'); viewComplianceModal.classList.add('hidden'); });
            closeViewComplianceModal2.addEventListener('click', () => { viewComplianceModal.classList.remove('active'); viewComplianceModal.classList.add('hidden'); });
            viewComplianceModal.querySelector('div').addEventListener('click', (e) => e.stopPropagation());

            closeEditComplianceModal.addEventListener('click', () => { editComplianceModal.classList.remove('active'); editComplianceModal.classList.add('hidden'); });
            cancelEditCompliance.addEventListener('click', () => { editComplianceModal.classList.remove('active'); editComplianceModal.classList.add('hidden'); });
            editComplianceModal.querySelector('div').addEventListener('click', (e) => e.stopPropagation());

            closeDeleteComplianceModal.addEventListener('click', () => { deleteComplianceModal.classList.remove('active'); deleteComplianceModal.classList.add('hidden'); });
            cancelDeleteCompliance.addEventListener('click', () => { deleteComplianceModal.classList.remove('active'); deleteComplianceModal.classList.add('hidden'); });
            deleteComplianceModal.querySelector('div').addEventListener('click', (e) => e.stopPropagation());

            // Submit handlers
            document.getElementById('editComplianceForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData.entries());
                data.id = document.getElementById('editComplianceId').value;

                const resp = await fetch('{{ route('compliance.update') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams(data)
                });
                const result = await resp.json();
                if (result.success) {
                    // Close modal first
                    editComplianceModal.classList.remove('active');
                    editComplianceModal.classList.add('hidden');
                    
                    // Reload data from server
                    await loadComplianceData();
                    
                    Swal.fire({ 
                        icon: 'success', 
                        title: 'Updated', 
                        text: 'Compliance updated successfully.', 
                        confirmButtonColor: '#2f855a' 
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Failed', text: 'Update failed.', confirmButtonColor: '#2f855a' });
                }
            });

            confirmDeleteCompliance.addEventListener('click', async () => {
                const id = document.getElementById('deleteComplianceTitle').closest('.modal').querySelector('[data-id]')?.dataset?.id;
                if (!id) return;

                const resp = await fetch('{{ route('compliance.delete') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ id })
                });
                const result = await resp.json();
                if (result.success) {
                    // Close modal first
                    deleteComplianceModal.classList.remove('active');
                    deleteComplianceModal.classList.add('hidden');
                    
                    // Reload data from server
                    await loadComplianceData();
                    
                    Swal.fire({ 
                        icon: 'success', 
                        title: 'Deleted', 
                        text: 'Compliance deleted successfully.', 
                        confirmButtonColor: '#2f855a' 
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Failed', text: 'Delete failed.', confirmButtonColor: '#2f855a' });
                }
            });

            addComplianceForm.addEventListener("submit", async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData.entries());

                console.log('Submitting compliance data:', data);

                const resp = await fetch('{{ route('compliance.create') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams(data)
                });
                
                console.log('Response status:', resp.status);
                const result = await resp.json();
                console.log('Response data:', result);
                
                if (result.success) {
                    // Store in localStorage as backup
                    storeComplianceInBackup(result.compliance);
                    
                    // Close modal and reset form first
                    addComplianceModal.classList.remove("active");
                    addComplianceModal.classList.add("hidden");
                    addComplianceForm.reset();
                    
                    // Reload data from server to get fresh data
                    await loadComplianceData();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Compliance Added',
                        text: 'The compliance has been added successfully.',
                        confirmButtonColor: '#2f855a'
                    });
                } else {
                    console.error('Failed to create compliance:', result);
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'Failed', 
                        text: result.message || 'Failed to add compliance.', 
                        confirmButtonColor: '#2f855a' 
                    });
                }
            });

            // Function to store data in localStorage as backup (for offline scenarios)
            function storeComplianceInBackup(compliance) {
                const existingData = JSON.parse(localStorage.getItem('compliance_backup') || '[]');
                existingData.push(compliance);
                localStorage.setItem('compliance_backup', JSON.stringify(existingData));
            }


            // Function to attach event listeners to all table buttons
            function attachEventListenersToTable() {
                // Remove existing listeners and add new ones
                const viewBtns = document.querySelectorAll('.viewComplianceBtn');
                const editBtns = document.querySelectorAll('.editComplianceBtn');
                const deleteBtns = document.querySelectorAll('.deleteComplianceBtn');

                viewBtns.forEach(btn => {
                    // Remove any existing listeners
                    btn.replaceWith(btn.cloneNode(true));
                });

                editBtns.forEach(btn => {
                    btn.replaceWith(btn.cloneNode(true));
                });

                deleteBtns.forEach(btn => {
                    btn.replaceWith(btn.cloneNode(true));
                });

                // Re-query and attach listeners
                const newViewBtns = document.querySelectorAll('.viewComplianceBtn');
                const newEditBtns = document.querySelectorAll('.editComplianceBtn');
                const newDeleteBtns = document.querySelectorAll('.deleteComplianceBtn');

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

                newDeleteBtns.forEach(btn => btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const d = e.currentTarget.dataset;
                    openDeleteModal({ id: d.id, title: d.title });
                }));
            }

            // Function to load data from server
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
                            
                            // Re-attach event listeners to all buttons
                            attachEventListenersToTable();
                            
                            // Update stats
                            updateStatsFromServer(doc);
                        }
                    }
                } catch (error) {
                    console.error('Error loading compliance data:', error);
                    // Fallback to localStorage if server fails
                    loadBackupData();
                }
            }

            // Function to update stats from server response
            function updateStatsFromServer(doc) {
                const statsCards = doc.querySelectorAll('.dashboard-card');
                const currentStatsCards = document.querySelectorAll('.dashboard-card');
                
                if (statsCards.length === currentStatsCards.length) {
                    statsCards.forEach((card, index) => {
                        const statValue = card.querySelector('.font-extrabold');
                        if (statValue && currentStatsCards[index]) {
                            const currentStatValue = currentStatsCards[index].querySelector('.font-extrabold');
                            if (currentStatValue) {
                                currentStatValue.textContent = statValue.textContent;
                            }
                        }
                    });
                }
            }

            // Function to load backup data from localStorage (fallback only)
            function loadBackupData() {
                const backupData = JSON.parse(localStorage.getItem('compliance_backup') || '[]');
                console.log('Loading backup data:', backupData);
                
                if (backupData.length > 0) {
                    const tbody = document.querySelector('tbody');
                    const emptyRow = tbody.querySelector('tr td[colspan="6"]');
                    if (emptyRow) {
                        emptyRow.closest('tr').remove();
                    }
                    
                    backupData.forEach(compliance => {
                        addComplianceToTable(compliance);
                    });
                }
            }

            // Initialize data loading when page loads
            document.addEventListener('DOMContentLoaded', function() {
                // First attach event listeners to existing elements
                attachEventListenersToTable();
                
                // Then load fresh data from server
                loadComplianceData();
=======
            addComplianceForm.addEventListener("submit", (e) => {
                e.preventDefault();
                addComplianceModal.classList.remove("active");
                Swal.fire({
                    icon: 'success',
                    title: 'Compliance Added',
                    text: 'The compliance has been added successfully.',
                    confirmButtonColor: '#2f855a'
                });
>>>>>>> 3467a8cdf3aef1c3632815755eba1f09b252a719
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
                if (!addComplianceModal.contains(e.target) && !addComplianceBtn.contains(e.target)) {
                    addComplianceModal.classList.remove("active");
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
            addComplianceModal.querySelector("div").addEventListener("click", (e) => {
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
        });
    </script>
</body>
</html>