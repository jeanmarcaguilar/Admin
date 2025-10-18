@php
// Get the authenticated user
$user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contract Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <button id="toggle-btn" class="pl-2 focus:outline-none">
                    <i class="fa-solid fa-bars text-2xl cursor-pointer"></i>
                </button>
                <h1 class="text-2xl font-bold tracking-tight">Contract Management</h1>
            </div>
            <div class="flex items-center space-x-1">
                <button class="relative p-2 transition duration-200 focus:outline-none" id="notificationBtn">
                    <i class="fa-solid fa-bell text-xl"></i>
                    <span class="absolute top-1 right-1 bg-red-500 text-xs text-white rounded-full px-1">3</span>
                </button>
                <div class="flex items-center space-x-2 cursor-pointer px-3 py-2 transition duration-200" id="userMenuBtn" aria-label="User menu" aria-haspopup="true" aria-expanded="false">
                    <i class="fa-solid fa-user text-[18px] bg-white text-[#28644c] px-2.5 py-2 rounded-full"></i>
                    <span class="text-white font-medium">{{ auth()->user()->name }}</span>
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
                    <p class="text-gray-600 leading-tight text-xs">New employee added: {{ auth()->user()->name }}</p>
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
                    <!-- Page Header -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                        <div>
                            <h1 class="text-2xl font-bold text-[#1a4d38]">Contract Management</h1>
                            <p class="text-gray-600 text-sm">Manage and track all legal contracts in one place</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <button id="addContractBtn" class="px-4 py-2 bg-[#2f855A] text-white rounded-lg hover:bg-[#28644c] transition-colors duration-200 flex items-center text-sm font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]">
                                <i class="fas fa-plus mr-2"></i> Add New Contract
                            </button>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    @php
                        $totalContracts = (int) ($stats['total'] ?? 0);
                        $activeCount = (int) ($stats['active'] ?? 0);
                        $pendingCount = (int) ($stats['pending'] ?? 0);
                        $activePct = $totalContracts > 0 ? round(($activeCount / $totalContracts) * 100) : 0;
                        $pendingPct = $totalContracts > 0 ? round(($pendingCount / $totalContracts) * 100) : 0;
                    @endphp
                    <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="dashboard-card p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Active Contracts</p>
                                    <p id="activeCountEl" class="font-extrabold text-3xl mt-1 text-gray-900">{{ $stats['active'] ?? 0 }}</p>
                                    <p class="text-xs text-gray-500 mt-1"><span id="activeCountText">{{ $activeCount }}</span> of <span id="totalContractsText">{{ $totalContracts }}</span> total</p>
                                </div>
                                <div class="p-4 rounded-full bg-blue-100 text-blue-600">
                                    <i class="fas fa-file-contract text-2xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div id="activeBar" class="bg-blue-500 h-2.5 rounded-full" style="width: {{ $activePct }}%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span id="activePctEl">{{ $activePct }}%</span>
                                    <span>Total: <span id="activeTotalEl">{{ $totalContracts }}</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-card p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Pending Review</p>
                                    <p id="pendingCountEl" class="font-extrabold text-3xl mt-1 text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
                                    <p class="text-xs text-gray-500 mt-1"><span id="pendingCountText">{{ $pendingCount }}</span> of <span>{{ $totalContracts }}</span> total</p>
                                </div>
                                <div class="p-4 rounded-full bg-green-100 text-green-600">
                                    <i class="fas fa-search text-2xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div id="pendingBar" class="bg-green-500 h-2.5 rounded-full" style="width: {{ $pendingPct }}%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span id="pendingPctEl">{{ $pendingPct }}%</span>
                                    <span>Total: <span id="pendingTotalEl">{{ $totalContracts }}</span></span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Search and Filter -->
                    <section class="bg-gradient-to-br from-white to-gray-50 rounded-lg p-6 shadow-sm border border-gray-100">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="relative flex-1 max-w-md">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="searchInput" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] text-sm" placeholder="Search contracts...">
                            </div>
                            <div class="flex space-x-3">
                                <select id="filterStatus" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-[#2f855A] focus:border-[#2f855A]">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="expired">Expired</option>
                                </select>
                                <select id="filterType" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-[#2f855A] focus:border-[#2f855A]">
                                    <option value="">All Types</option>
                                    <option value="nda">NDA</option>
                                    <option value="service">Service</option>
                                    <option value="employment">Employment</option>
                                </select>
                                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#2f855A]">
                                    <i class="fas fa-filter text-gray-600 mr-2"></i>
                                    <span>Filter</span>
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Contracts Table -->
                    <section class="bg-gradient-to-br from-white to-gray-50 rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contract ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse(($contracts ?? []) as $c)
                                        @php
                                            $badge = $c->type === 'nda' ? 'NDA' : ($c->type === 'service' ? 'Service' : 'Employment');
                                            $statusLabel = ucfirst($c->status);
                                            $statusClasses = $c->status === 'active' ? 'bg-green-100 text-green-800' : ($c->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-200 text-gray-700');
                                        @endphp
                                        <tr class="table-row" data-code="{{ $c->code }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $c->code }}</div>
                                                <div class="text-xs text-gray-500">Created: {{ $c->created_on ?? '—' }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $c->title }}</div>
                                                <div class="text-xs text-gray-500">{{ $c->company ?? '—' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $badge }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">{{ $statusLabel }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="#" class="viewContractBtn text-[#2f855A] hover:text-[#1a4d38] mr-3" data-tooltip="View" data-id="{{ $c->code }}" data-title="{{ $c->title }}" data-company="{{ $c->company }}" data-type="{{ $badge }}" data-status="{{ $statusLabel }}" data-created="{{ $c->created_on ?? '' }}">View</a>
                                                <a href="#" class="editContractBtn text-blue-600 hover:text-blue-900 mr-3" data-tooltip="Edit" data-id="{{ $c->code }}" data-title="{{ $c->title }}" data-company="{{ $c->company }}" data-type="{{ $c->type }}" data-status="{{ $c->status }}" data-created="{{ $c->created_on ?? '' }}">Edit</a>
                                                <a href="#" class="deleteContractBtn text-red-600 hover:text-red-900" data-tooltip="Delete" data-id="{{ $c->code }}" data-title="{{ $c->title }}">Delete</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">No contracts found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>

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

            <!-- Add Contract Modal -->
            <div id="addContractModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="add-contract-modal-title">
                <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
                    <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                        <h3 id="add-contract-modal-title" class="font-semibold text-sm text-gray-900 select-none">Add New Contract</h3>
                        <button id="closeAddContractModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                    <div class="px-8 pt-6 pb-8">
                        <form id="addContractForm" class="space-y-4 text-xs text-gray-700">
                            <div>
                                <label for="contractTitle" class="block mb-1 font-semibold">Contract Title *</label>
                                <input type="text" id="contractTitle" name="contractTitle" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                            </div>
                            <div>
                                <label for="contractType" class="block mb-1 font-semibold">Contract Type *</label>
                                <select id="contractType" name="contractType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                    <option value="">Select a type</option>
                                    <option value="nda">NDA</option>
                                    <option value="service">Service Agreement</option>
                                    <option value="employment">Employment Contract</option>
                                </select>
                            </div>
                            <div>
                                <label for="startDate" class="block mb-1 font-semibold">Start Date *</label>
                                <input type="date" id="startDate" name="startDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                            </div>
                            <div>
                                <label for="description" class="block mb-1 font-semibold">Description</label>
                                <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]"></textarea>
                            </div>
                            <div class="flex justify-end space-x-3 pt-2">
                                <button type="button" id="cancelAddContract" class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
                                <button type="submit" class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200">Save Contract</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- View Contract Modal -->
            <div id="viewContractModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="view-contract-modal-title">
                <div class="bg-white rounded-lg shadow-lg w-[380px] max-w-full mx-4" role="document">
                    <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                        <h3 id="view-contract-modal-title" class="font-semibold text-sm text-gray-900 select-none">Contract Details</h3>
                        <button id="closeViewContractModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                    <div class="px-8 pt-6 pb-8 text-xs text-gray-700 space-y-2">
                        <div><span class="font-semibold">Contract ID:</span> <span id="viewContractId"></span></div>
                        <div><span class="font-semibold">Title:</span> <span id="viewContractTitle"></span></div>
                        <div><span class="font-semibold">Company:</span> <span id="viewContractCompany"></span></div>
                        <div><span class="font-semibold">Type:</span> <span id="viewContractType"></span></div>
                        <div><span class="font-semibold">Status:</span> <span id="viewContractStatus"></span></div>
                        <div><span class="font-semibold">Created:</span> <span id="viewContractCreated"></span></div>
                        <div class="pt-4 text-right">
                            <button id="closeViewContractModal2" type="button" class="bg-[#28644c] hover:bg-[#2f855A] text-white text-sm font-semibold rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Contract Modal -->
            <div id="editContractModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="edit-contract-modal-title">
                <div class="bg-white rounded-lg shadow-lg w-[380px] max-w-full mx-4" role="document">
                    <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                        <h3 id="edit-contract-modal-title" class="font-semibold text-sm text-gray-900 select-none">Edit Contract</h3>
                        <button id="closeEditContractModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                    <div class="px-8 pt-6 pb-8">
                        <form id="editContractForm" class="space-y-3 text-xs text-gray-700">
                            <input type="hidden" id="editContractId">
                            <div>
                                <label for="editContractTitle" class="block mb-1 font-semibold">Title</label>
                                <input type="text" id="editContractTitle" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                            </div>
                            <div>
                                <label for="editContractCompany" class="block mb-1 font-semibold">Company</label>
                                <input type="text" id="editContractCompany" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                            </div>
                            <div>
                                <label for="editContractType" class="block mb-1 font-semibold">Type</label>
                                <select id="editContractType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                    <option value="nda">NDA</option>
                                    <option value="service">Service Agreement</option>
                                    <option value="employment">Employment Contract</option>
                                </select>
                            </div>
                            <div>
                                <label for="editContractStatus" class="block mb-1 font-semibold">Status</label>
                                <select id="editContractStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                            <div class="flex justify-end space-x-3 pt-2">
                                <button type="button" id="cancelEditContract" class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
                                <button type="submit" class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Contract Modal -->
            <div id="deleteContractModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="delete-contract-modal-title">
                <div class="bg-white rounded-md shadow-lg w-[360px] max-w-full mx-4" role="document">
                    <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                        <h3 id="delete-contract-modal-title" class="font-semibold text-sm text-gray-900 select-none">Delete Contract</h3>
                        <button id="closeDeleteContractModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                    <div class="px-8 pt-6 pb-8 text-center">
                        <p class="text-xs text-gray-700 mb-4">Are you sure you want to delete <span class="font-semibold" id="deleteContractTitle"></span> (<span id="deleteContractId"></span>)?</p>
                        <div class="flex justify-center space-x-3">
                            <button type="button" id="cancelDeleteContract" class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
                            <button type="button" id="confirmDeleteContract" class="bg-red-600 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-sm transition-all duration-200">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
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
            const addContractBtn = document.getElementById('addContractBtn');
            const addContractModal = document.getElementById('addContractModal');
            const closeAddContractModal = document.getElementById('closeAddContractModal');
            const cancelAddContract = document.getElementById('cancelAddContract');
            const viewContractModal = document.getElementById('viewContractModal');
            const closeViewContractModal = document.getElementById('closeViewContractModal');
            const closeViewContractModal2 = document.getElementById('closeViewContractModal2');
            const editContractModal = document.getElementById('editContractModal');
            const closeEditContractModal = document.getElementById('closeEditContractModal');
            const cancelEditContract = document.getElementById('cancelEditContract');
            const deleteContractModal = document.getElementById('deleteContractModal');
            const closeDeleteContractModal = document.getElementById('closeDeleteContractModal');
            const cancelDeleteContract = document.getElementById('cancelDeleteContract');
            const confirmDeleteContract = document.getElementById('confirmDeleteContract');

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
                viewContractModal.classList.remove("active");
                editContractModal.classList.remove("active");
                deleteContractModal.classList.remove("active");
                addContractModal.classList.remove("active");
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
                addContractModal.classList.remove("active");
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
                addContractModal.classList.remove("active");
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
                addContractModal.classList.remove("active");
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
                addContractModal.classList.remove("active");
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
                addContractModal.classList.remove("active");
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

            function toggleAddContractModal() {
                addContractModal.classList.toggle("hidden");
                addContractModal.classList.toggle("active");
                document.body.classList.toggle("overflow-hidden");
                notificationDropdown.classList.add("hidden");
                userMenuDropdown.classList.add("hidden");
                userMenuBtn.setAttribute("aria-expanded", "false");
                profileModal.classList.remove("active");
                accountSettingsModal.classList.remove("active");
                privacySecurityModal.classList.remove("active");
                signOutModal.classList.remove("active");
                viewContractModal.classList.remove("active");
                editContractModal.classList.remove("active");
                deleteContractModal.classList.remove("active");
            }

            addContractBtn.addEventListener('click', toggleAddContractModal);
            closeAddContractModal.addEventListener('click', toggleAddContractModal);
            cancelAddContract.addEventListener('click', toggleAddContractModal);

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
                if (!viewContractModal.contains(e.target)) {
                    viewContractModal.classList.remove("active");
                }
                if (!editContractModal.contains(e.target)) {
                    editContractModal.classList.remove("active");
                }
                if (!deleteContractModal.contains(e.target)) {
                    deleteContractModal.classList.remove("active");
                }
                if (!addContractModal.contains(e.target) && !addContractBtn.contains(e.target)) {
                    addContractModal.classList.remove("active");
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
            addContractModal.querySelector("div").addEventListener("click", (e) => {
                e.stopPropagation();
            });

            // Handle View/Edit/Delete buttons
            const viewBtns = document.querySelectorAll('.viewContractBtn');
            const editBtns = document.querySelectorAll('.editContractBtn');
            const deleteBtns = document.querySelectorAll('.deleteContractBtn');

            function openViewModal(data) {
                document.getElementById('viewContractId').textContent = data.id;
                document.getElementById('viewContractTitle').textContent = data.title;
                document.getElementById('viewContractCompany').textContent = data.company;
                document.getElementById('viewContractType').textContent = data.type;
                document.getElementById('viewContractStatus').textContent = data.status;
                document.getElementById('viewContractCreated').textContent = data.created;
                viewContractModal.classList.remove('hidden');
                viewContractModal.classList.add('active');
            }

            function openEditModal(data) {
                document.getElementById('editContractId').value = data.id;
                document.getElementById('editContractTitle').value = data.title;
                document.getElementById('editContractCompany').value = data.company;
                document.getElementById('editContractType').value = (data.type || '').toLowerCase();
                document.getElementById('editContractStatus').value = (data.status || '').toLowerCase().includes('pending') ? 'pending' : (data.status || '').toLowerCase();
                editContractModal.classList.remove('hidden');
                editContractModal.classList.add('active');
            }

            function openDeleteModal(data) {
                document.getElementById('deleteContractId').textContent = data.id;
                document.getElementById('deleteContractTitle').textContent = data.title;
                deleteContractModal.classList.remove('hidden');
                deleteContractModal.classList.add('active');
            }

            viewBtns.forEach(btn => btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const d = e.currentTarget.dataset;
                openViewModal({ id: d.id, title: d.title, company: d.company, type: d.type, status: d.status, created: d.created });
            }));

            editBtns.forEach(btn => btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const d = e.currentTarget.dataset;
                openEditModal({ id: d.id, title: d.title, company: d.company, type: d.type, status: d.status, created: d.created });
            }));

            deleteBtns.forEach(btn => btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const d = e.currentTarget.dataset;
                openDeleteModal({ id: d.id, title: d.title });
            }));

            // Close buttons for modals
            closeViewContractModal.addEventListener('click', () => { viewContractModal.classList.remove('active'); viewContractModal.classList.add('hidden'); });
            closeViewContractModal2.addEventListener('click', () => { viewContractModal.classList.remove('active'); viewContractModal.classList.add('hidden'); });
            viewContractModal.querySelector('div').addEventListener('click', (e) => e.stopPropagation());

            closeEditContractModal.addEventListener('click', () => { editContractModal.classList.remove('active'); editContractModal.classList.add('hidden'); });
            cancelEditContract.addEventListener('click', () => { editContractModal.classList.remove('active'); editContractModal.classList.add('hidden'); });
            editContractModal.querySelector('div').addEventListener('click', (e) => e.stopPropagation());

            closeDeleteContractModal.addEventListener('click', () => { deleteContractModal.classList.remove('active'); deleteContractModal.classList.add('hidden'); });
            cancelDeleteContract.addEventListener('click', () => { deleteContractModal.classList.remove('active'); deleteContractModal.classList.add('hidden'); });
            deleteContractModal.querySelector('div').addEventListener('click', (e) => e.stopPropagation());

            // Submit handlers (wired to backend)
            document.getElementById('editContractForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const code = document.getElementById('editContractId').value;
                const title = document.getElementById('editContractTitle').value.trim();
                const company = document.getElementById('editContractCompany').value.trim();
                const type = document.getElementById('editContractType').value;
                const status = document.getElementById('editContractStatus').value;

                const resp = await fetch('{{ route('contracts.update') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ code, title, company, type, status })
                });
                const data = await resp.json();
                if (data.success) {
                    // Update row in-place
                    const row = Array.from(document.querySelectorAll('tbody tr')).find(tr => (tr.querySelector('td:nth-child(1) .text-sm.font-medium')?.textContent || '') === code);
                    if (row) {
                        // Title and company
                        const titleCell = row.querySelector('td:nth-child(2)');
                        if (titleCell) {
                            const titleEl = titleCell.querySelector('.text-sm.font-medium');
                            const compEl = titleCell.querySelector('.text-xs');
                            if (titleEl) titleEl.textContent = title;
                            if (compEl) compEl.textContent = company;
                        }
                        // Type badge
                        const typeBadge = row.querySelector('td:nth-child(3) span');
                        if (typeBadge) {
                            const label = type === 'nda' ? 'NDA' : (type === 'service' ? 'Service' : 'Employment');
                            typeBadge.textContent = label;
                        }
                        // Status badge
                        const statusBadge = row.querySelector('td:nth-child(4) span');
                        if (statusBadge) {
                            statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                        }
                    }
                    editContractModal.classList.remove('active');
                    editContractModal.classList.add('hidden');
                    Swal.fire({ icon: 'success', title: 'Saved', text: 'Contract updated.', confirmButtonColor: '#2f855a' });
                } else {
                    Swal.fire({ icon: 'error', title: 'Failed', text: 'Update failed.', confirmButtonColor: '#2f855a' });
                }
            });

            confirmDeleteContract.addEventListener('click', async () => {
                const code = document.getElementById('deleteContractId').textContent;
                const resp = await fetch('{{ route('contracts.delete') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ code })
                });
                const data = await resp.json();
                if (data.success) {
                    const row = Array.from(document.querySelectorAll('tbody tr')).find(tr => (tr.querySelector('td:nth-child(1) .text-sm.font-medium')?.textContent || '') === code);
                    if (row) row.remove();
                    deleteContractModal.classList.remove('active');
                    deleteContractModal.classList.add('hidden');
                    Swal.fire({ icon: 'success', title: 'Deleted', text: 'Contract deleted.', confirmButtonColor: '#2f855a' });
                } else {
                    Swal.fire({ icon: 'error', title: 'Failed', text: 'Delete failed.', confirmButtonColor: '#2f855a' });
                }
            });

            addContractForm.addEventListener('submit', function(e) {
                e.preventDefault();
                toggleAddContractModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Contract Added',
                    text: 'The contract has been added successfully.',
                    confirmButtonColor: '#2f855a',
                });
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

            const tooltipTriggers = document.querySelectorAll("[data-tooltip]");
            tooltipTriggers.forEach(trigger => {
                trigger.addEventListener("mouseenter", (e) => {
                    const tooltip = document.createElement("div");
                    tooltip.className = "absolute z-50 px-2 py-1 text-xs text-white bg-gray-900 rounded shadow-lg";
                    tooltip.textContent = e.target.dataset.tooltip;
                    document.body.appendChild(tooltip);

                    const rect = e.target.getBoundingClientRect();
                    tooltip.style.top = `${rect.bottom + window.scrollY + 5}px`;
                    tooltip.style.left = `${rect.left + window.scrollX}px`;

                    e.target._tooltip = tooltip;
                });

                trigger.addEventListener("mouseleave", (e) => {
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