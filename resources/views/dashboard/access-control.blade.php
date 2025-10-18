@php
// Get the authenticated user
$user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Access Control & Permissions | Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
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

        #sidebar.md\:ml-0 ~ #main-content .dashboard-container {
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

        /* Custom checkbox */
        .custom-checkbox {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid #cbd5e0;
            border-radius: 0.25rem;
            outline: none;
            cursor: pointer;
            position: relative;
            vertical-align: middle;
            margin-right: 0.5rem;
        }

        .custom-checkbox:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .custom-checkbox:checked::after {
            content: '✓';
            position: absolute;
            color: white;
            font-size: 0.75rem;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="w-full p-3 h-16 bg-[#28644c] text-white shadow-md fixed top-0 left-0 right-0 z-50">
        <div class="flex justify-between items-center h-full max-w-7xl mx-auto">
            <div class="flex items-center space-x-4">
                <button id="toggle-btn" class="pl-2 focus:outline-none">
                    <i class="fa-solid fa-bars text-2xl cursor-pointer"></i>
                </button>
                <h1 class="text-2xl font-bold tracking-tight">Access Control & Permissions</h1>
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

    <!-- Notification Dropdown -->
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
                    <p class="font-semibold text-gray-900 leading-tight">New Permission Added</p>
                    <p class="text-gray-600 leading-tight text-xs">John Doe was granted Admin access</p>
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
                    <p class="font-semibold text-gray-900 leading-tight">Permission Updated</p>
                    <p class="text-gray-600 leading-tight text-xs">Finance Team permissions modified</p>
                    <p class="text-gray-400 text-xs mt-0.5">2 hours ago</p>
                </div>
            </li>
            <li class="flex items-start px-4 py-3 space-x-3">
                <div class="flex-shrink-0 mt-1">
                    <div class="bg-yellow-200 text-yellow-700 rounded-full p-2">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <div class="flex-grow text-sm">
                    <p class="font-semibold text-gray-900 leading-tight">Action Required</p>
                    <p class="text-gray-600 leading-tight text-xs">Review pending permission requests</p>
                    <p class="text-gray-400 text-xs mt-0.5">Yesterday</p>
                </div>
            </li>
        </ul>
        <div class="text-center py-2 border-t border-gray-200">
            <a class="text-[#28644c] text-xs font-semibold hover:underline" href="#">View all notifications</a>
        </div>
    </div>

    <!-- Sidebar and Main Content -->
    <div class="flex w-full min-h-screen pt-16">
        <div id="overlay" class="hidden fixed inset-0 bg-black opacity-50 z-40"></div>

        <!-- Sidebar -->
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
                        <div class="flex items-center font-medium justify-between text-lg bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-file"></i>
                                <span>Document Management</span>
                            </div>
                            <i class="bx bx-chevron-down text-2xl transition-transform duration-300"></i>
                        </div>
                        <ul class="dropdown-menu hidden bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
                            <li><a href="{{ route('document.upload.indexing') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-upload mr-2"></i>Document Upload & Indexing</a></li>
                            <li><a href="{{ route('document.version.control') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-git-branch mr-2"></i>Version Control</a></li>
                            <li><a href="{{ route('document.access.control.permissions') }}" class="block px-3 py-2 text-sm bg-white/30 rounded-lg"><i class="bx bx-lock mr-2"></i>Access Control & Permissions</a></li>
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

        <!-- Main Content -->
        <main id="main-content" class="flex-1 p-6 w-full mt-16">
            <div class="dashboard-container max-w-7xl mx-auto">
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-md border border-gray-100 p-8 space-y-6">
                    <!-- Page Header -->
                    <div class="flex justify-between items-center">
                        <h2 class="text-[#1a4d38] font-bold text-xl mb-1">Access Control & Permissions</h2>
                        <div class="flex space-x-3">
                            <button id="newPermissionBtn" class="bg-[#2f855A] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#276749] transition-colors focus:outline-none focus:ring-2 focus:ring-[#2f855A] focus:ring-offset-2">
                                <i class="bx bx-plus mr-1"></i> New Permission
                            </button>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm">Manage document access permissions for users and groups.</p>

                    <!-- Search and Filter Section -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="relative flex-1 max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class='bx bx-search text-gray-400'></i>
                            </div>
                            <input type="text" id="searchInput" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent" placeholder="Search permissions...">
                        </div>
                        <div class="flex space-x-3">
                            <select id="filterRole" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent">
                                <option value="">All Roles</option>
                                <option value="admin">Admin</option>
                                <option value="editor">Editor</option>
                                <option value="viewer">Viewer</option>
                            </select>
                            <select id="filterStatus" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Permissions Table -->
                    <section class="mt-8">
                        <h3 class="font-semibold text-lg text-[#1a4d38] mb-4">
                            <i class='bx bx-list-ul mr-2'></i>Access Permissions
                        </h3>
                        <div class="dashboard-card bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permission</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Type</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @php
                                            // Use permissions provided by the controller/route
                                            $permissions = $permissions ?? [];
                                        @endphp

                                        @forelse($permissions as $permission)
                                            <tr class="activity-item" data-permission-id="{{ $permission['id'] }}">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                            <i class='{{ $permission['type'] === 'Group' ? 'bx bx-group text-blue-600' : 'bx bx-user text-blue-600' }}'></i>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">{{ $permission['name'] }}</div>
                                                            <div class="text-xs text-gray-500">{{ $permission['email'] }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $permission['role'] === 'Admin' ? 'bg-green-100 text-green-800' : 
                                                           ($permission['role'] === 'Editor' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                                        {{ $permission['role'] }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $permission['document_type'] }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($permission['permissions'] as $perm)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                                {{ ucfirst($perm) }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $permission['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ ucfirst($permission['status']) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <button onclick="showPermissionDetails({{ json_encode($permission) }})" class="text-blue-600 hover:text-blue-900 mr-3 bg-transparent border-none p-0 cursor-pointer">View</button>
                                                    <button onclick="openEditPermissionModal({{ $permission['id'] }})" class="text-green-600 hover:text-green-900 mr-3 bg-transparent border-none p-0 cursor-pointer">Edit</button>
                                                    <button onclick="confirmDeletePermission({{ $permission['id'] }})" class="text-red-600 hover:text-red-900 bg-transparent border-none p-0 cursor-pointer">Delete</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                                    No permissions found. Click "New Permission" to add one.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Showing <span class="font-medium">1</span> to <span class="font-medium">3</span> of <span class="font-medium">3</span> results
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                <span class="sr-only">Previous</span>
                                                <i class='bx bx-chevron-left'></i>
                                            </a>
                                            <a href="#" aria-current="page" class="z-10 bg-indigo-50 border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                                1
                                            </a>
                                            <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                                2
                                            </a>
                                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                <span class="sr-only">Next</span>
                                                <i class='bx bx-chevron-right'></i>
                                            </a>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>

    <!-- View Permission Details Modal -->
    <div id="viewPermissionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 class="text-xl font-semibold text-gray-900">Permission Details</h3>
                <button onclick="closeModal('viewPermissionModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="p-6" id="permissionDetailsContent">
                <!-- Content will be loaded by JavaScript -->
            </div>
        </div>
    </div>

    <!-- New Permission Modal -->
    <div id="newPermissionModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="new-permission-modal-title">
        <div class="bg-white rounded-lg w-full max-w-2xl">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 id="new-permission-modal-title" class="text-xl font-semibold text-gray-900">Add New Permission</h3>
                <button onclick="closeModal('newPermissionModal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <i class='fas fa-times text-2xl'></i>
                </button>
            </div>
            <form id="newPermissionForm" class="p-6 space-y-6" action="{{ route('permissions.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="permissionType" class="block text-sm font-medium text-gray-700 mb-1">Permission Type</label>
                        <select id="permissionType" name="permission_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="user">User</option>
                            <option value="group">Group</option>
                            <option value="department">Department</option>
                        </select>
                    </div>
                    <div id="userField">
                        <label for="user" class="block text-sm font-medium text-gray-700 mb-1">Select User</label>
                        <select id="user" name="user" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="">Select a user</option>
                            @foreach(($allUsers ?? []) as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="groupField" class="hidden">
                        <label for="group" class="block text-sm font-medium text-gray-700 mb-1">Select Group</label>
                        <select id="group" name="group" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="">Select a group</option>
                            <option value="1">Finance Team</option>
                            <option value="2">HR Department</option>
                            <option value="3">IT Support</option>
                        </select>
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select id="role" name="role" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="admin">Admin</option>
                            <option value="editor">Editor</option>
                            <option value="viewer">Viewer</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div>
                        <label for="documentType" class="block text-sm font-medium text-gray-700 mb-1">Document Type</label>
                        <select id="documentType" name="document_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="all">All Documents</option>
                            <option value="financial">Financial Reports</option>
                            <option value="hr">HR Documents</option>
                            <option value="legal">Legal Contracts</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div id="customPermissions" class="hidden border-t pt-4 mt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Custom Permissions</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="viewPermission" name="permissions[]" value="view" class="custom-checkbox" checked>
                            <label for="viewPermission" class="ml-2 text-sm text-gray-700">View</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="editPermission" name="permissions[]" value="edit" class="custom-checkbox">
                            <label for="editPermission" class="ml-2 text-sm text-gray-700">Edit</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="deletePermission" name="permissions[]" value="delete" class="custom-checkbox">
                            <label for="deletePermission" class="ml-2 text-sm text-gray-700">Delete</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="sharePermission" name="permissions[]" value="share" class="custom-checkbox">
                            <label for="sharePermission" class="ml-2 text-sm text-gray-700">Share</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="downloadPermission" name="permissions[]" value="download" class="custom-checkbox">
                            <label for="downloadPermission" class="ml-2 text-sm text-gray-700">Download</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="printPermission" name="permissions[]" value="print" class="custom-checkbox">
                            <label for="printPermission" class="ml-2 text-sm text-gray-700">Print</label>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-4 mt-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-[#2f855a] focus:border-[#2f855a] sm:text-sm"></textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeModal('newPermissionModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855a]">
                        Cancel
                    </button>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#2f855a] hover:bg-[#276749] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855a]">
                        Save Permission
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Permission Modal -->
    <div id="editPermissionModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="edit-permission-modal-title">
        <div class="bg-white rounded-lg w-full max-w-2xl">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 id="edit-permission-modal-title" class="text-xl font-semibold text-gray-900">Edit Permission</h3>
                <button onclick="closeModal('editPermissionModal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <i class='fas fa-times text-2xl'></i>
                </button>
            </div>
            <form id="editPermissionForm" class="p-6 space-y-6" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" id="editPermissionId" name="id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="editPermissionType" class="block text-sm font-medium text-gray-700 mb-1">Permission Type</label>
                        <select id="editPermissionType" name="permission_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="user">User</option>
                            <option value="group">Group</option>
                            <option value="department">Department</option>
                        </select>
                    </div>
                    <div id="editUserField">
                        <label for="editUser" class="block text-sm font-medium text-gray-700 mb-1">Select User</label>
                        <select id="editUser" name="user" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="">Select a user</option>
                            @foreach(($allUsers ?? []) as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="editGroupField" class="hidden">
                        <label for="editGroup" class="block text-sm font-medium text-gray-700 mb-1">Select Group</label>
                        <select id="editGroup" name="group" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="">Select a group</option>
                            <option value="1">Finance Team</option>
                            <option value="2">HR Department</option>
                            <option value="3">IT Support</option>
                        </select>
                    </div>
                    <div>
                        <label for="editRole" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select id="editRole" name="role" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="admin">Admin</option>
                            <option value="editor">Editor</option>
                            <option value="viewer">Viewer</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div>
                        <label for="editDocumentType" class="block text-sm font-medium text-gray-700 mb-1">Document Type</label>
                        <select id="editDocumentType" name="document_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="all">All Documents</option>
                            <option value="financial">Financial Reports</option>
                            <option value="hr">HR Documents</option>
                            <option value="legal">Legal Contracts</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div id="editCustomPermissions" class="hidden border-t pt-4 mt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Custom Permissions</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="editViewPermission" name="permissions[]" value="view" class="custom-checkbox">
                            <label for="editViewPermission" class="ml-2 text-sm text-gray-700">View</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="editEditPermission" name="permissions[]" value="edit" class="custom-checkbox">
                            <label for="editEditPermission" class="ml-2 text-sm text-gray-700">Edit</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="editDeletePermission" name="permissions[]" value="delete" class="custom-checkbox">
                            <label for="editDeletePermission" class="ml-2 text-sm text-gray-700">Delete</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="editSharePermission" name="permissions[]" value="share" class="custom-checkbox">
                            <label for="editSharePermission" class="ml-2 text-sm text-gray-700">Share</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="editDownloadPermission" name="permissions[]" value="download" class="custom-checkbox">
                            <label for="editDownloadPermission" class="ml-2 text-sm text-gray-700">Download</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="editPrintPermission" name="permissions[]" value="print" class="custom-checkbox">
                            <label for="editPrintPermission" class="ml-2 text-sm text-gray-700">Print</label>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-4 mt-4">
                    <label for="editNotes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea id="editNotes" name="notes" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-[#2f855a] focus:border-[#2f855a] sm:text-sm"></textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeModal('editPermissionModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855a]">
                        Cancel
                    </button>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#2f855a] hover:bg-[#276749] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855a]">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deletePermissionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg w-full max-w-md">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation text-red-600 text-xl"></i>
                </div>
                <h3 id="delete-permission-modal-title" class="text-lg font-medium text-gray-900 mb-2">Delete Permission</h3>
                <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete this permission? This action cannot be undone.</p>
                <div class="flex justify-center space-x-4">
                    <button type="button" onclick="closeModal('deletePermissionModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855a]">
                        Cancel
                    </button>
                    <button type="button" id="confirmDeleteBtn" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete
                    </button>
                </div>
            </div>
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

    <!-- Success Message Toast -->
    @if(session('success'))
    <div id="successToast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center justify-between z-50" style="min-width: 300px;">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="document.getElementById('successToast').remove()" class="ml-4 text-white hover:text-gray-100">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('successToast');
            if (toast) {
                toast.remove();
            }
        }, 5000);
    </script>
    @endif
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
            const openSignOutBtn = document.getElementById("openSignOutBtn");
            const openPrivacySecurityBtn = document.getElementById("openPrivacySecurityBtn");
            const privacySecurityModal = document.getElementById("privacySecurityModal");
            const closePrivacySecurityBtn = document.getElementById("closePrivacySecurityBtn");
            const cancelPrivacySecurityBtn = document.getElementById("cancelPrivacySecurityBtn");
            const signOutModal = document.getElementById("signOutModal");
            const cancelSignOutBtn = document.getElementById("cancelSignOutBtn");
            const cancelSignOutBtn2 = document.getElementById("cancelSignOutBtn2");
            const newPermissionBtn = document.getElementById("newPermissionBtn");
            const newPermissionModal = document.getElementById("newPermissionModal");
            const editPermissionModal = document.getElementById("editPermissionModal");
            const deletePermissionModal = document.getElementById("deletePermissionModal");
            const permissionType = document.getElementById("permissionType");
            const userField = document.getElementById("userField");
            const groupField = document.getElementById("groupField");
            const roleSelect = document.getElementById("role");
            const customPermissions = document.getElementById("customPermissions");
            const editPermissionType = document.getElementById("editPermissionType");
            const editUserField = document.getElementById("editUserField");
            const editGroupField = document.getElementById("editGroupField");
            const editRoleSelect = document.getElementById("editRole");
            const editCustomPermissions = document.getElementById("editCustomPermissions");
            const searchInput = document.getElementById("searchInput");
            const filterRole = document.getElementById("filterRole");
            const filterStatus = document.getElementById("filterStatus");
            const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

            // Initialize sidebar state
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
                    if (dropdown && !dropdown.classList.contains("hidden")) {
                        dropdown.classList.add("hidden");
                        if (chevron) chevron.classList.remove("rotate-180");
                    }
                });
            }

            // Toggle dropdown menus
            dropdownToggles.forEach((toggle) => {
                toggle.addEventListener("click", (e) => {
                    e.stopPropagation();
                    const dropdown = toggle.nextElementSibling;
                    const chevron = toggle.querySelector(".bx-chevron-down");
                    
                    // Close other dropdowns
                    dropdownToggles.forEach((otherToggle) => {
                        if (otherToggle !== toggle) {
                            const otherDropdown = otherToggle.nextElementSibling;
                            const otherChevron = otherToggle.querySelector(".bx-chevron-down");
                            if (otherDropdown) otherDropdown.classList.add("hidden");
                            if (otherChevron) otherChevron.classList.remove("rotate-180");
                        }
                    });
                    
                    // Toggle current dropdown
                    if (dropdown) dropdown.classList.toggle("hidden");
                    if (chevron) chevron.classList.toggle("rotate-180");
                });
            });

            // Delegated handler (more robust) for dropdown toggles
            if (sidebar) {
                sidebar.addEventListener("click", (e) => {
                    const toggle = e.target.closest(".has-dropdown > div");
                    if (!toggle) return;
                    e.stopPropagation();
                    const dropdown = toggle.nextElementSibling;
                    const chevron = toggle.querySelector(".bx-chevron-down");

                    // Close other dropdowns
                    document.querySelectorAll(".has-dropdown > div").forEach((otherToggle) => {
                        if (otherToggle !== toggle) {
                            const otherDropdown = otherToggle.nextElementSibling;
                            const otherChevron = otherToggle.querySelector(".bx-chevron-down");
                            if (otherDropdown) otherDropdown.classList.add("hidden");
                            if (otherChevron) otherChevron.classList.remove("rotate-180");
                        }
                    });

                    // Toggle current dropdown
                    if (dropdown) dropdown.classList.toggle("hidden");
                    if (chevron) chevron.classList.toggle("rotate-180");
                });
            }

            // Close dropdowns when clicking outside
            document.addEventListener("click", (e) => {
                if (!e.target.closest('.has-dropdown')) {
                    closeAllDropdowns();
                }
            });

            // Handle overlay click
            overlay.addEventListener("click", () => {
                sidebar.classList.add("-ml-72");
                overlay.classList.add("hidden");
                document.body.style.overflow = "";
                mainContent.classList.remove("sidebar-open");
                mainContent.classList.add("sidebar-closed");
                closeAllDropdowns();
            });

            // Handle window resize
            function handleResize() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove("-ml-72");
                    mainContent.classList.add("md:ml-72", "sidebar-open");
                    mainContent.classList.remove("sidebar-closed");
                    overlay.classList.add("hidden");
                    document.body.style.overflow = "";
                } else {
                    sidebar.classList.add("-ml-72");
                    mainContent.classList.remove("md:ml-72", "sidebar-open");
                    mainContent.classList.add("sidebar-closed");
                }
            }

            // Set up event listeners
            if (toggleBtn) toggleBtn.addEventListener("click", toggleSidebar);
            window.addEventListener("resize", handleResize);

            // Notification dropdown
            if (notificationBtn) notificationBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                notificationDropdown.classList.toggle("hidden");
                userMenuDropdown.classList.add("hidden");
                userMenuBtn.setAttribute("aria-expanded", "false");
                profileModal.classList.remove("active");
                accountSettingsModal.classList.remove("active");
                privacySecurityModal.classList.remove("active");
                signOutModal.classList.remove("active");
                newPermissionModal.classList.remove("active");
                editPermissionModal.classList.remove("active");
                deletePermissionModal.classList.remove("active");
            });

            // User menu dropdown
            if (userMenuBtn) userMenuBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                userMenuDropdown.classList.toggle("hidden");
                const expanded = userMenuBtn.getAttribute("aria-expanded") === "true";
                userMenuBtn.setAttribute("aria-expanded", !expanded);
                notificationDropdown.classList.add("hidden");
                profileModal.classList.remove("active");
                accountSettingsModal.classList.remove("active");
                privacySecurityModal.classList.remove("active");
                signOutModal.classList.remove("active");
                newPermissionModal.classList.remove("active");
                editPermissionModal.classList.remove("active");
                deletePermissionModal.classList.remove("active");
            });

            if (openSignOutBtn) openSignOutBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                signOutModal.classList.add("active");
                userMenuDropdown.classList.add("hidden");
                userMenuBtn.setAttribute("aria-expanded", "false");
                profileModal.classList.remove("active");
                accountSettingsModal.classList.remove("active");
                privacySecurityModal.classList.remove("active");
                notificationDropdown.classList.add("hidden");
                newPermissionModal.classList.remove("active");
                editPermissionModal.classList.remove("active");
                deletePermissionModal.classList.remove("active");
            });

            // Profile modal
            if (openProfileBtn) openProfileBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                profileModal.classList.add("active");
                userMenuDropdown.classList.add("hidden");
                userMenuBtn.setAttribute("aria-expanded", "false");
                accountSettingsModal.classList.remove("active");
                privacySecurityModal.classList.remove("active");
                notificationDropdown.classList.add("hidden");
                signOutModal.classList.remove("active");
                newPermissionModal.classList.remove("active");
                editPermissionModal.classList.remove("active");
                deletePermissionModal.classList.remove("active");
            });

    if (closeProfileBtn) closeProfileBtn.addEventListener("click", () => {
        profileModal.classList.remove("active");
    });
    if (closeProfileBtn2) closeProfileBtn2.addEventListener("click", () => {
        profileModal.classList.remove("active");
    });

    // Account settings modal
    if (openAccountSettingsBtn) openAccountSettingsBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        accountSettingsModal.classList.add("active");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
        profileModal.classList.remove("active");
        privacySecurityModal.classList.remove("active");
        notificationDropdown.classList.add("hidden");
        signOutModal.classList.remove("active");
        newPermissionModal.classList.remove("active");
        editPermissionModal.classList.remove("active");
        deletePermissionModal.classList.remove("active");
    });

    if (closeAccountSettingsBtn) closeAccountSettingsBtn.addEventListener("click", () => {
        accountSettingsModal.classList.remove("active");
    });
    if (cancelAccountSettingsBtn) cancelAccountSettingsBtn.addEventListener("click", () => {
        accountSettingsModal.classList.remove("active");
    });

    // Privacy & Security modal
    if (openPrivacySecurityBtn) openPrivacySecurityBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        privacySecurityModal.classList.add("active");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
        profileModal.classList.remove("active");
        accountSettingsModal.classList.remove("active");
        notificationDropdown.classList.add("hidden");
        signOutModal.classList.remove("active");
        newPermissionModal.classList.remove("active");
        editPermissionModal.classList.remove("active");
        deletePermissionModal.classList.remove("active");
    });

    if (closePrivacySecurityBtn) closePrivacySecurityBtn.addEventListener("click", () => {
        privacySecurityModal.classList.remove("active");
    });
    if (cancelPrivacySecurityBtn) cancelPrivacySecurityBtn.addEventListener("click", () => {
        privacySecurityModal.classList.remove("active");
    });

    // Sign out modal
    if (cancelSignOutBtn) cancelSignOutBtn.addEventListener("click", () => {
        signOutModal.classList.remove("active");
    });
    if (cancelSignOutBtn2) cancelSignOutBtn2.addEventListener("click", () => {
        signOutModal.classList.remove("active");
    });

    // Close modals and dropdowns on outside click
    document.addEventListener("click", (e) => {
        if (!notificationDropdown.contains(e.target) && !notificationBtn.contains(e.target)) {
            notificationDropdown.classList.add("hidden");
        }
        if (!userMenuDropdown.contains(e.target) && !userMenuBtn.contains(e.target)) {
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
        if (!signOutModal.contains(e.target) && !userMenuDropdown.contains(e.target)) {
            signOutModal.classList.remove("active");
        }
        if (!newPermissionModal.contains(e.target) && !newPermissionBtn.contains(e.target)) {
            newPermissionModal.classList.remove("active");
        }
        if (!editPermissionModal.contains(e.target)) {
            editPermissionModal.classList.remove("active");
        }
        if (!deletePermissionModal.contains(e.target)) {
            deletePermissionModal.classList.remove("active");
        }
    });

    // New Permission Modal
    if (newPermissionBtn) newPermissionBtn.addEventListener("click", () => {
        newPermissionModal.classList.add("active");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
        notificationDropdown.classList.add("hidden");
        profileModal.classList.remove("active");
        accountSettingsModal.classList.remove("active");
        privacySecurityModal.classList.remove("active");
        signOutModal.classList.remove("active");
        editPermissionModal.classList.remove("active");
        deletePermissionModal.classList.remove("active");
        // Reset form
        document.getElementById("newPermissionForm").reset();
        permissionType.value = "user";
        userField.classList.remove("hidden");
        groupField.classList.add("hidden");
        roleSelect.value = "admin";
        customPermissions.classList.add("hidden");
    });

    // Permission Type Toggle for New Permission
    permissionType.addEventListener("change", () => {
        if (permissionType.value === "user") {
            userField.classList.remove("hidden");
            groupField.classList.add("hidden");
        } else if (permissionType.value === "group") {
            userField.classList.add("hidden");
            groupField.classList.remove("hidden");
        } else {
            userField.classList.add("hidden");
            groupField.classList.add("hidden");
        }
    });

    // Role Selection for New Permission
    roleSelect.addEventListener("change", () => {
        if (roleSelect.value === "custom") {
            customPermissions.classList.remove("hidden");
        } else {
            customPermissions.classList.add("hidden");
        }
    });

    // Permission Type Toggle for Edit Permission
    editPermissionType.addEventListener("change", () => {
        if (editPermissionType.value === "user") {
            editUserField.classList.remove("hidden");
            editGroupField.classList.add("hidden");
        } else if (editPermissionType.value === "group") {
            editUserField.classList.add("hidden");
            editGroupField.classList.remove("hidden");
        } else {
            editUserField.classList.add("hidden");
            editGroupField.classList.add("hidden");
        }
    });

    // Role Selection for Edit Permission
    editRoleSelect.addEventListener("change", () => {
        if (editRoleSelect.value === "custom") {
            editCustomPermissions.classList.remove("hidden");
        } else {
            editCustomPermissions.classList.add("hidden");
        }
    });

    // Search and Filter Permissions
    function filterPermissions() {
        const searchText = searchInput.value.toLowerCase();
        const roleFilter = filterRole.value.toLowerCase();
        const statusFilter = filterStatus.value.toLowerCase();
        const rows = document.querySelectorAll("tbody tr");

        rows.forEach((row) => {
            const name = row.querySelector("td:nth-child(1) .text-sm").textContent.toLowerCase();
            const email = row.querySelector("td:nth-child(1) .text-xs").textContent.toLowerCase();
            const role = row.querySelector("td:nth-child(2) span").textContent.toLowerCase();
            const status = row.querySelector("td:nth-child(5) span").textContent.toLowerCase();

            const matchesSearch = name.includes(searchText) || email.includes(searchText);
            const matchesRole = roleFilter === "" || role === roleFilter;
            const matchesStatus = statusFilter === "" || status === statusFilter;

            row.style.display = matchesSearch && matchesRole && matchesStatus ? "" : "none";
        });
    }

    searchInput.addEventListener("input", filterPermissions);
    filterRole.addEventListener("change", filterPermissions);
    filterStatus.addEventListener("change", filterPermissions);

    // View Permission Details
    window.showPermissionDetails = function(permission) {
        const modal = document.getElementById("viewPermissionModal");
        const content = document.getElementById("permissionDetailsContent");
        content.innerHTML = `
            <div class="space-y-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-700">Name</h4>
                    <p class="text-sm text-gray-500">${permission.name}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-700">Email</h4>
                    <p class="text-sm text-gray-500">${permission.email}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-700">Type</h4>
                    <p class="text-sm text-gray-500">${permission.type}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-700">Role</h4>
                    <p class="text-sm text-gray-500">${permission.role}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-700">Document Type</h4>
                    <p class="text-sm text-gray-500">${permission.document_type}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-700">Permissions</h4>
                    <div class="flex flex-wrap gap-2">
                        ${permission.permissions.map(perm => `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">${perm}</span>`).join("")}
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-700">Status</h4>
                    <p class="text-sm text-gray-500">${permission.status}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-700">Last Updated</h4>
                    <p class="text-sm text-gray-500">${permission.last_updated}</p>
                </div>
            </div>
        `;
        modal.classList.remove("hidden");
        modal.classList.add("active");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
        notificationDropdown.classList.add("hidden");
        profileModal.classList.remove("active");
        accountSettingsModal.classList.remove("active");
        privacySecurityModal.classList.remove("active");
        signOutModal.classList.remove("active");
        newPermissionModal.classList.remove("active");
        editPermissionModal.classList.remove("active");
        deletePermissionModal.classList.remove("active");
    };

    // Edit Permission Modal
    window.openEditPermissionModal = function(id) {
        editPermissionModal.classList.add("active");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
        notificationDropdown.classList.add("hidden");
        profileModal.classList.remove("active");
        accountSettingsModal.classList.remove("active");
        privacySecurityModal.classList.remove("active");
        signOutModal.classList.remove("active");
        newPermissionModal.classList.remove("active");
        deletePermissionModal.classList.remove("active");

        // Mock data fetch
        const permission = {
            id: id,
            type: id === 1 ? "user" : id === 2 ? "group" : "user",
            user: id === 1 ? "1" : id === 3 ? "2" : "",
            group: id === 2 ? "1" : "",
            role: id === 1 ? "admin" : id === 2 ? "editor" : "viewer",
            document_type: id === 1 ? "all" : id === 2 ? "financial" : "hr",
            permissions: id === 1 ? ["view", "edit", "delete", "share"] : id === 2 ? ["view", "edit"] : ["view"],
            notes: "Sample notes for permission " + id
        };

        document.getElementById("editPermissionId").value = permission.id;
        editPermissionType.value = permission.type;
        document.getElementById("editUser").value = permission.user;
        document.getElementById("editGroup").value = permission.group;
        editRoleSelect.value = permission.role;
        document.getElementById("editDocumentType").value = permission.document_type;
        document.getElementById("editNotes").value = permission.notes;

        // Toggle user/group fields
        if (permission.type === "user") {
            editUserField.classList.remove("hidden");
            editGroupField.classList.add("hidden");
        } else if (permission.type === "group") {
            editUserField.classList.add("hidden");
            editGroupField.classList.remove("hidden");
        } else {
            editUserField.classList.add("hidden");
            editGroupField.classList.add("hidden");
        }

        // Toggle custom permissions
        if (permission.role === "custom") {
            editCustomPermissions.classList.remove("hidden");
            ["view", "edit", "delete", "share", "download", "print"].forEach(perm => {
                const checkbox = document.getElementById(`edit${perm.charAt(0).toUpperCase() + perm.slice(1)}Permission`);
                checkbox.checked = permission.permissions.includes(perm);
            });
        } else {
            editCustomPermissions.classList.add("hidden");
        }

    // Form Submission Handling
    document.getElementById("newPermissionForm").addEventListener("submit", (e) => {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData.entries()))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: "success",
                    title: "Permission Added",
                    text: data.message || "The new permission has been successfully added.",
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload(); // Reload to show the new permission
                });
            } else {
                throw new Error(data.message || 'Failed to add permission');
            }
        })
        .catch(error => {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: error.message || "An error occurred while adding the permission."
            });
        });
    });

    // Function to open edit modal with permission data
    window.openEditPermissionModal = function(permissionId) {
        // Fetch the permission details
        fetch(`/permissions/${permissionId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const permission = data.permission;
                const form = document.getElementById('editPermissionForm');
                
                // Set the form action with the permission ID
                form.action = `/permissions/${permissionId}`;
                
                // Set form values based on permission data
                document.getElementById('editPermissionId').value = permissionId;
                document.getElementById('editPermissionType').value = permission.type;
                
                // Toggle user/group fields based on permission type
                togglePermissionFields(permission.type, 'edit');
                
                if (permission.type === 'user') {
                    document.getElementById('editUser').value = permission.user_id || '';
                } else {
                    document.getElementById('editGroup').value = permission.group_id || '';
                }
                
                document.getElementById('editRole').value = permission.role;
                document.getElementById('editDocumentType').value = permission.document_type;
                
                // Handle custom permissions checkboxes
                if (permission.role === 'custom' && Array.isArray(permission.permissions)) {
                    permission.permissions.forEach(perm => {
                        const checkbox = document.getElementById(`edit${perm.charAt(0).toUpperCase() + perm.slice(1)}Permission`);
                        if (checkbox) checkbox.checked = true;
                    });
                }
                
                document.getElementById('editNotes').value = permission.notes || '';
                
                // Show the modal
                document.getElementById('editPermissionModal').classList.add('active');
            } else {
                throw new Error(data.message || 'Failed to load permission details');
            }
        })
        .catch(error => {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: error.message || "Failed to load permission details"
            });
        });
    };

    // Handle edit form submission
    document.getElementById("editPermissionForm").addEventListener("submit", (e) => {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const permissionId = document.getElementById('editPermissionId').value;
        
        fetch(form.action, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData.entries()))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: "success",
                    title: "Permission Updated",
                    text: data.message || "The permission has been successfully updated.",
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload(); // Reload to show the updated permission
                });
            } else {
                throw new Error(data.message || 'Failed to update permission');
            }
        })
        .catch(error => {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: error.message || "An error occurred while updating the permission."
            });
        });
    });
    
    // Helper function to toggle permission fields based on type
    function togglePermissionFields(type, prefix = '') {
        const userField = document.getElementById(`${prefix}UserField`);
        const groupField = document.getElementById(`${prefix}GroupField`);
        
        if (type === 'user') {
            userField.classList.remove('hidden');
            groupField.classList.add('hidden');
        } else {
            userField.classList.add('hidden');
            groupField.classList.remove('hidden');
        }
    }
    
    // Initialize permission type change handlers
    document.getElementById('permissionType').addEventListener('change', (e) => {
        togglePermissionFields(e.target.value);
    });
    
    document.getElementById('editPermissionType').addEventListener('change', (e) => {
        togglePermissionFields(e.target.value, 'edit');
    });
});
</script>
</body>
</html>
