@extends('layouts.app')

@section('content')
<div class="flex w-full min-h-screen pt-16">
    <!-- Overlay for mobile -->
    <div id="overlay" class="hidden fixed inset-0 bg-black opacity-50 z-30" onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden'); document.body.style.overflow = 'auto';"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="bg-[#2f855A] text-white flex flex-col z-40 fixed top-16 bottom-0 w-72 -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
        <div class="department-header px-2 py-4 mx-2 border-b border-white/50">
            <h1 class="text-xl font-bold">Administrative Department</h1>
        </div>
        <div class="px-3 py-10 flex-1">
            <ul class="space-y-6">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center font-medium space-x-2 text-lg hover:bg-white/30 px-3 py-2.5 rounded-lg whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : '' }}">
                        <i class="bx bx-graph"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <!-- Add other menu items here -->
            </ul>
        </div>
        <div class="px-5 pb-6">
            <div class="bg-white rounded-md p-4 text-center text-[#2f855A] text-sm font-semibold select-none">
                Need Help?<br />
                Contact support team at<br />
                <a href="mailto:support@example.com" class="text-blue-500 hover:underline">support@example.com</a>
            </div>
        </div>
    </aside>
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

        .fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }

        /* Custom dropdown styles */
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            margin-top: 0.5rem;
            min-width: 12rem;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            z-index: 50;
            overflow: hidden;
        }

        .dropdown-menu.show {
            display: block;
            animation: fadeIn 0.2s ease-out;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            color: #374151;
            font-size: 0.875rem;
            line-height: 1.25rem;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background-color: #f3f4f6;
        }

        .dropdown-divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 0.25rem 0;
        }

        /* Notification badge */
        .notification-badge {
            position: absolute;
            top: -0.25rem;
            right: -0.25rem;
            width: 1.25rem;
            height: 1.25rem;
            background-color: #ef4444;
            color: white;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 600;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .modal.show {
            display: flex;
            opacity: 1;
        }

        /* Custom checkbox */
        .custom-checkbox {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .custom-checkbox input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #d1d5db;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #3f8a56;
        }

        input:checked + .slider:before {
            transform: translateX(20px);
        }

        /* Custom radio button */
        .custom-radio {
            position: relative;
            padding-left: 28px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            height: 20px;
        }

        .custom-radio input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 20px;
            width: 20px;
            background-color: #e5e7eb;
            border-radius: 50%;
            transition: all 0.2s;
        }

        .custom-radio:hover input ~ .checkmark {
            background-color: #d1d5db;
        }

        .custom-radio input:checked ~ .checkmark {
            background-color: #3f8a56;
            border: 5px solid #e5e7eb;
        }

        /* Custom select */
        .custom-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1em;
            padding-right: 2.5rem;
        }

        /* Custom file input */
        .custom-file-input {
            position: relative;
            z-index: 2;
            width: 100%;
            height: 100%;
            margin: 0;
            opacity: 0;
        }

        .custom-file-label {
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            z-index: 1;
            display: flex;
            height: 2.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background-color: #f9fafb;
            align-items: center;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            color: #6b7280;
        }

        /* Custom scrollbar for dropdowns */
        .dropdown-menu {
            max-height: 300px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #c1c1c1 #f1f1f1;
        }

        .dropdown-menu::-webkit-scrollbar {
            width: 6px;
        }

        .dropdown-menu::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .dropdown-menu::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        /* Animation for sidebar */
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }
            
            .dashboard-card {
                padding: 1rem;
            }
            
            .chart-container {
                height: 300px;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="w-full p-3 h-16 bg-[#28644c] text-white shadow-md fixed top-0 left-0 right-0 z-50">
        <div class="flex justify-between items-center h-full max-w-7xl mx-auto">
            <div class="flex items-center space-x-4">
                <button id="toggle-btn" class="pl-2 focus:outline-none">
                    <i class="bx bx-menu text-2xl"></i>
                </button>
                <h1 class="text-xl font-bold">Admin Portal</h1>
            </div>
            
            <div class="flex items-center space-x-4">
                <!-- Notification Bell -->
                <div class="relative">
                    <button id="notificationBtn" class="p-2 rounded-full hover:bg-white/20 focus:outline-none relative">
                        <i class="bx bx-bell text-xl"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg border border-gray-200 z-50">
                        <div class="p-3 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="font-semibold text-gray-800">Notifications</h3>
                                <button class="text-xs text-blue-600 hover:underline">Mark all as read</button>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                            <!-- Notification Items -->
                            <a href="#" class="flex items-start p-3 hover:bg-gray-50">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                    <i class="bx bx-file"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900">New document uploaded</p>
                                    <p class="text-xs text-gray-500 mt-1">A new document has been uploaded to the system.</p>
                                    <p class="text-xs text-gray-400 mt-1">2 minutes ago</p>
                                </div>
                            </a>
                            <!-- More notification items -->
                        </div>
                        <div class="p-2 bg-gray-50 text-center">
                            <a href="#" class="text-sm font-medium text-blue-600 hover:underline">View all notifications</a>
                        </div>
                    </div>
                </div>
                
                <!-- User Menu -->
                <div class="relative">
                    <button id="userMenuBtn" class="flex items-center space-x-2 focus:outline-none">
                        <div class="h-8 w-8 rounded-full bg-white flex items-center justify-center text-[#28644c] font-semibold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden md:inline text-sm font-medium">{{ Auth::user()->name }}</span>
                        <i class="bx bx-chevron-down text-sm"></i>
                    </button>
                    
                    <!-- User Dropdown Menu -->
                    <div id="userMenuDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50">
                        <div class="py-1">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="bx bx-user mr-2"></i> Profile
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="bx bx-cog mr-2"></i> Settings
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="bx bx-log-out mr-2"></i> Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex w-full min-h-screen pt-16">
        <!-- Overlay for mobile -->
        <div id="overlay" class="hidden fixed inset-0 bg-black opacity-50 z-30" onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden'); document.body.style.overflow = 'auto';"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="bg-[#2f855A] text-white flex flex-col z-40 fixed top-16 bottom-0 w-72 -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="department-header px-2 py-4 mx-2 border-b border-white/50">
                <h1 class="text-xl font-bold">Administrative Department</h1>
            </div>
            <div class="px-3 py-10 flex-1">
                <ul class="space-y-6">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center font-medium space-x-2 text-lg hover:bg-white/30 px-3 py-2.5 rounded-lg whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : '' }}">
                            <i class="bx bx-graph"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="has-dropdown">
                        <div class="flex items-center justify-between font-medium text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-calendar"></i>
                                <span>Reservations</span>
                            </div>
                            <i class="bx bx-chevron-down text-sm transition-transform duration-200"></i>
                        </div>
                        <ul class="dropdown-menu mt-1 ml-8 space-y-1 hidden">
                            <li><a href="#" class="block px-4 py-2 text-sm hover:bg-white/10 rounded">All Reservations</a></li>
                            <li><a href="#" class="block px-4 py-2 text-sm hover:bg-white/10 rounded">New Reservation</a></li>
                            <li><a href="#" class="block px-4 py-2 text-sm hover:bg-white/10 rounded">Calendar View</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-dropdown">
                        <div class="flex items-center justify-between font-medium text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-file"></i>
                                <span>Documents</span>
                            </div>
                            <i class="bx bx-chevron-down text-sm transition-transform duration-200"></i>
                        </div>
                        <ul class="dropdown-menu mt-1 ml-8 space-y-1 hidden">
                            <li><a href="#" class="block px-4 py-2 text-sm hover:bg-white/10 rounded">All Documents</a></li>
                            <li><a href="#" class="block px-4 py-2 text-sm hover:bg-white/10 rounded">Upload New</a></li>
                            <li><a href="#" class="block px-4 py-2 text-sm hover:bg-white/10 rounded">Categories</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-dropdown">
                        <div class="flex items-center justify-between font-medium text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-user"></i>
                                <span>Users</span>
                            </div>
                            <i class="bx bx-chevron-down text-sm transition-transform duration-200"></i>
                        </div>
                        <ul class="dropdown-menu mt-1 ml-8 space-y-1 hidden">
                            <li><a href="#" class="block px-4 py-2 text-sm hover:bg-white/10 rounded">All Users</a></li>
                            <li><a href="#" class="block px-4 py-2 text-sm hover:bg-white/10 rounded">Add New User</a></li>
                            <li><a href="#" class="block px-4 py-2 text-sm hover:bg-white/10 rounded">Roles & Permissions</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-dropdown">
                        <div class="flex items-center justify-between font-medium text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-cog"></i>
                                <span>Settings</span>
                            </div>
                            <i class="bx bx-chevron-down text-sm transition-transform duration-200"></i>
                        </div>
                        <ul class="dropdown-menu mt-1 ml-8 space-y-1 hidden">
                            <li><a href="#" class="block px-4 py-2 text-sm hover:bg-white/10 rounded">General Settings</a></li>
                            <li><a href="#" class="block px-4 py-2 text-sm hover:bg-white/10 rounded">Email Templates</a></li>
                            <li><a href="#" class="block px-4 py-2 text-sm hover:bg-white/10 rounded">System Logs</a></li>
                        </ul>
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
        <main id="main-content" class="flex-1 p-6 w-full">
            <div class="dashboard-container max-w-7xl mx-auto">
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-md border border-gray-100 p-8 space-y-6">
                    <h2 class="text-[#1a4d38] font-bold text-xl mb-1">Welcome, {{ Auth::user()->name }}!</h2>
            
            <!-- Dashboard Stats -->
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Stats Cards -->
                <div class="dashboard-card bg-gradient-to-br from-white to-gray-50 rounded-lg p-4 shadow-sm border-l-4 border-[#2f855A]">
                    <div class="flex justify-between items-start">
                        <p class="text-gray-800 font-semibold text-sm">Facilities Reservations</p>
                        <i class="bx bx-building text-[#2f855A] text-2xl"></i>
                    </div>
                    <p class="font-extrabold text-2xl mt-1 text-gray-900">12</p>
                    <p class="text-sm text-gray-600 mt-1 flex items-center space-x-1">
                        <i class="fas fa-arrow-up text-green-500 text-sm"></i>
                        <span class="text-green-600 font-semibold">3.8%</span>
                        <span>vs last month</span>
                    </p>
                </div>
                
                <!-- Documents Card -->
                <div class="dashboard-card bg-gradient-to-br from-white to-gray-50 rounded-lg p-4 shadow-sm border-l-4 border-[#2f855A]">
                    <div class="flex justify-between items-start">
                        <p class="text-gray-800 font-semibold text-sm">Documents</p>
                        <i class="bx bx-file text-[#2f855A] text-2xl"></i>
                    </div>
                    <p class="font-extrabold text-2xl mt-1 text-gray-900">47</p>
                    <p class="text-sm text-gray-600 mt-1 flex items-center space-x-1">
                        <i class="fas fa-arrow-up text-green-500 text-sm"></i>
                        <span class="text-green-600 font-semibold">12.5%</span>
                        <span>vs last month</span>
                    </p>
                </div>
                
                <!-- Users Card -->
                <div class="dashboard-card bg-gradient-to-br from-white to-gray-50 rounded-lg p-4 shadow-sm border-l-4 border-[#2f855A]">
                    <div class="flex justify-between items-start">
                        <p class="text-gray-800 font-semibold text-sm">Users</p>
                        <i class="bx bx-user text-[#2f855A] text-2xl"></i>
                    </div>
                    <p class="font-extrabold text-2xl mt-1 text-gray-900">{{ \App\Models\User::count() }}</p>
                    <p class="text-sm text-gray-600 mt-1 flex items-center space-x-1">
                        <i class="fas fa-arrow-up text-green-500 text-sm"></i>
                        <span class="text-green-600 font-semibold">{{ round(\App\Models\User::count() / 10 * 100) }}%</span>
                        <span>active</span>
                    </p>
                </div>
                
                <!-- Tasks Card -->
                <div class="dashboard-card bg-gradient-to-br from-white to-gray-50 rounded-lg p-4 shadow-sm border-l-4 border-[#2f855A]">
                    <div class="flex justify-between items-start">
                        <p class="text-gray-800 font-semibold text-sm">Tasks Completed</p>
                        <i class="bx bx-check-circle text-[#2f855A] text-2xl"></i>
                    </div>
                    <p class="font-extrabold text-2xl mt-1 text-gray-900">89%</p>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-[#2f855A] h-2 rounded-full" style="width: 89%"></div>
                    </div>
                </div>
            </section>

            <!-- Recent Activities and Quick Stats -->
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
                <!-- Recent Activities -->
                <div class="lg:col-span-2 bg-gradient-to-br from-white to-gray-50 rounded-lg p-6 shadow-sm border border-gray-100 min-h-[280px] flex flex-col w-full">
                    <h3 class="font-bold text-sm text-[#1a4d38] mb-4">Recent Activities</h3>
                    <ul class="divide-y divide-gray-200 flex-grow">
                        <li class="activity-item flex space-x-3 py-3">
                            <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-700">
                                <i class="bx bx-building text-base"></i>
                            </div>
                            <div class="flex-grow">
                                <p class="text-sm font-semibold text-gray-800">New facility reservation request</p>
                                <p class="text-xs text-gray-600 mt-0.5">Meeting room booked for tomorrow</p>
                            </div>
                            <div class="flex-shrink-0 text-xs text-gray-500 self-start pt-0.5">2 hours ago</div>
                        </li>
                        
                        <li class="activity-item flex space-x-3 py-3">
                            <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700">
                                <i class="bx bx-file text-base"></i>
                            </div>
                            <div class="flex-grow">
                                <p class="text-sm font-semibold text-gray-800">Document uploaded</p>
                                <p class="text-xs text-gray-600 mt-0.5">Q3 financial report</p>
                            </div>
                            <div class="flex-shrink-0 text-xs text-gray-500 self-start pt-0.5">5 hours ago</div>
                        </li>
                        
                        <li class="activity-item flex space-x-3 py-3">
                            <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 text-purple-700">
                                <i class="bx bx-user-plus text-base"></i>
                            </div>
                            <div class="flex-grow">
                                <p class="text-sm font-semibold text-gray-800">New user registered</p>
                                <p class="text-xs text-gray-600 mt-0.5">jane.doe@example.com</p>
                            </div>
                            <div class="flex-shrink-0 text-xs text-gray-500 self-start pt-0.5">Yesterday</div>
                        </li>
                    </ul>
                    <a href="#" class="text-sm text-[#2f855A] font-semibold mt-2 hover:underline">View all activities</a>
                </div>
                
                <!-- Quick Stats -->
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-lg p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-sm text-[#1a4d38] mb-4">Quick Stats</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full bg-[#2f855A]"></div>
                                <span class="text-sm text-gray-600">Active Tasks</span>
                            </div>
                            <span class="text-sm font-semibold">12</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                                <span class="text-sm text-gray-600">In Progress</span>
                            </div>
                            <span class="text-sm font-semibold">8</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                <span class="text-sm text-gray-600">Completed</span>
                            </div>
                            <span class="text-sm font-semibold">24</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                <span class="text-sm text-gray-600">Pending</span>
                            </div>
                            <span class="text-sm font-semibold">5</span>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Storage Usage</h4>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-[#2f855A] h-2 rounded-full" style="width: 65%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>65% used</span>
                            <span>13.2 GB of 20 GB</span>
                        </div>
                    </div>
                    
                    <button class="mt-6 w-full bg-[#2f855A] text-white text-sm font-semibold py-2 px-4 rounded-lg hover:bg-[#28644c] transition-colors duration-200">
                        Upgrade Storage
                    </button>
                </div>
            </section>

            <!-- Charts Section -->
            <section class="mt-8">
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-lg p-6 shadow-sm border border-gray-100">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                        <h3 class="font-bold text-sm text-[#1a4d38]">Monthly Overview</h3>
                        <div class="flex space-x-2 mt-2 md:mt-0">
                            <button class="text-xs px-3 py-1 rounded-lg bg-[#2f855A] text-white">This Year</button>
                            <button class="text-xs px-3 py-1 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">Last Year</button>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="overviewChart"></canvas>
                    </div>
                </div>
            </section>

            <!-- Recent Transactions -->
            <section class="mt-8">
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-sm text-[#1a4d38]">Recent Transactions</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#TRX-001</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Office Supplies</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$245.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Oct 15, 2023</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#TRX-002</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Software License</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$599.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Oct 14, 2023</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#TRX-003</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Web Hosting</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$129.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Oct 12, 2023</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-3 bg-gray-50 text-right">
                        <a href="#" class="text-sm font-medium text-[#2f855A] hover:text-[#1a4d38]">View all transactions</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

<!-- Include your JavaScript for charts and interactivity -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar on mobile
    const toggleBtn = document.getElementById('toggle-btn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            document.body.style.overflow = document.body.style.overflow === 'hidden' ? 'auto' : 'hidden';
        });
    }
    
    // Close sidebar when clicking on overlay
    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = 'auto';
        });
    }
    
    // Dropdown menus
    const dropdownToggles = document.querySelectorAll('.has-dropdown > div');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const dropdown = toggle.nextElementSibling;
            const icon = toggle.querySelector('i');
            
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(d => {
                if (d !== dropdown) {
                    d.classList.add('hidden');
                }
            });
            
            // Toggle current dropdown
            if (dropdown) {
                dropdown.classList.toggle('hidden');
                if (icon) {
                    icon.classList.toggle('rotate-180');
                }
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-menu').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
        document.querySelectorAll('.has-dropdown i').forEach(icon => {
            icon.classList.remove('rotate-180');
        });
    });
    
    // User menu dropdown
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userMenuDropdown = document.getElementById('userMenuDropdown');
    
    if (userMenuBtn && userMenuDropdown) {
        userMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userMenuDropdown.classList.toggle('hidden');
        });
    }
    
    // Notification dropdown
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    if (notificationBtn && notificationDropdown) {
        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
        });
    }
    
    // Close dropdowns when clicking outside
    window.addEventListener('click', (e) => {
        if (userMenuDropdown && !userMenuBtn.contains(e.target) && !userMenuDropdown.contains(e.target)) {
            userMenuDropdown.classList.add('hidden');
        }
        
        if (notificationDropdown && notificationBtn && !notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
            notificationDropdown.classList.add('hidden');
        }
    });
    
    // Initialize charts
    const overviewCtx = document.getElementById('overviewChart');
    if (overviewCtx) {
        new Chart(overviewCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Income',
                        data: [5000, 7000, 8500, 7200, 9200, 11000, 10500, 12000, 9500, 11000, 13000, 15000],
                        borderColor: '#2f855A',
                        backgroundColor: 'rgba(47, 133, 90, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Expenses',
                        data: [3000, 4500, 5000, 4800, 6000, 7000, 6500, 7200, 6800, 7500, 8000, 9000],
                        borderColor: '#E53E3E',
                        backgroundColor: 'rgba(229, 62, 62, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
</script>

<!-- Add any additional scripts here -->
@stack('scripts')

</body>
                    </p>
                </div>
                
                <!-- Add other stat cards here -->
                <div class="dashboard-card bg-gradient-to-br from-white to-gray-50 rounded-lg p-4 shadow-sm border-l-4 border-[#2f855A]">
                    <div class="flex justify-between items-start">
                        <p class="text-gray-800 font-semibold text-sm">Documents</p>
                        <i class="bx bx-file text-[#2f855A] text-2xl"></i>
                    </div>
                    <p class="font-extrabold text-2xl mt-1 text-gray-900">47</p>
                    <p class="text-sm text-gray-600 mt-1 flex items-center space-x-1">
                        <i class="fas fa-arrow-up text-green-500 text-sm"></i>
                        <span class="text-green-600 font-semibold">12.5%</span>
                        <span>vs last month</span>
                    </p>
                </div>
                
                <div class="dashboard-card bg-gradient-to-br from-white to-gray-50 rounded-lg p-4 shadow-sm border-l-4 border-[#2f855A]">
                    <div class="flex justify-between items-start">
                        <p class="text-gray-800 font-semibold text-sm">Users</p>
                        <i class="bx bx-user text-[#2f855A] text-2xl"></i>
                    </div>
                    <p class="font-extrabold text-2xl mt-1 text-gray-900">{{ \App\Models\User::count() }}</p>
                    <p class="text-sm text-gray-600 mt-1 flex items-center space-x-1">
                        <i class="fas fa-arrow-up text-green-500 text-sm"></i>
                        <span class="text-green-600 font-semibold">{{ round(\App\Models\User::count() / 10 * 100) }}%</span>
                        <span>active</span>
                    </p>
                </div>
                
                <div class="dashboard-card bg-gradient-to-br from-white to-gray-50 rounded-lg p-4 shadow-sm border-l-4 border-[#2f855A]">
                    <div class="flex justify-between items-start">
                        <p class="text-gray-800 font-semibold text-sm">Tasks Completed</p>
                        <i class="bx bx-check-circle text-[#2f855A] text-2xl"></i>
                    </div>
                    <p class="font-extrabold text-2xl mt-1 text-gray-900">89%</p>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-[#2f855A] h-2 rounded-full" style="width: 89%"></div>
                    </div>
                </div>
            </section>

                            <div class="flex-grow">
                                <p class="text-sm font-semibold text-gray-800">New facility reservation request</p>
                                <p class="text-xs text-gray-600 mt-0.5">Meeting room booked for tomorrow</p>
                            </div>
                            <div class="flex-shrink-0 text-xs text-gray-500 self-start pt-0.5">2 hours ago</div>
                        </li>
                        
                        <li class="activity-item flex space-x-3 py-3">
                            <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700">
                                <i class="bx bx-file text-base"></i>
                            </div>
                            <div class="flex-grow">
                                <p class="text-sm font-semibold text-gray-800">Document uploaded</p>
                                <p class="text-xs text-gray-600 mt-0.5">Q3 financial report</p>
                            </div>
                            <div class="flex-shrink-0 text-xs text-gray-500 self-start pt-0.5">5 hours ago</div>
                        </li>
                        
                        <li class="activity-item flex space-x-3 py-3">
                            <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 text-purple-700">
                                <i class="bx bx-user-plus text-base"></i>
                            </div>
                            <div class="flex-grow">
                                <p class="text-sm font-semibold text-gray-800">New user registered</p>
                                <p class="text-xs text-gray-600 mt-0.5">jane.doe@example.com</p>
                            </div>
                            <div class="flex-shrink-0 text-xs text-gray-500 self-start pt-0.5">Yesterday</div>
                        </li>
                    </ul>
                    <a href="#" class="text-sm text-[#2f855A] font-semibold mt-2 hover:underline">View all activities</a>
                </div>
                
                <!-- Quick Stats -->
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-lg p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-sm text-[#1a4d38] mb-4">Quick Stats</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full bg-[#2f855A]"></div>
                                <span class="text-sm text-gray-600">Active Tasks</span>
                            </div>
                            <span class="text-sm font-semibold">12</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                                <span class="text-sm text-gray-600">In Progress</span>
                            </div>
                            <span class="text-sm font-semibold">8</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                <span class="text-sm text-gray-600">Completed</span>
                            </div>
                            <span class="text-sm font-semibold">24</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                <span class="text-sm text-gray-600">Pending</span>
                            </div>
                            <span class="text-sm font-semibold">5</span>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Storage Usage</h4>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-[#2f855A] h-2 rounded-full" style="width: 65%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>65% used</span>
                            <span>13.2 GB of 20 GB</span>
                        </div>
                    </div>
                    
                    <button class="mt-6 w-full bg-[#2f855A] text-white text-sm font-semibold py-2 px-4 rounded-lg hover:bg-[#28644c] transition-colors duration-200">
                        Upgrade Storage
                    </button>
                </div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-sm text-[#1a4d38]">Dashboard Overview</h3>
                        <div class="flex space-x-2">
                            <button class="text-xs px-3 py-1 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">Week</button>
                            <button class="text-xs px-3 py-1 rounded-lg bg-[#2f855A] text-white">Month</button>
                            <button class="text-xs px-3 py-1 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">Year</button>
                        </div>
                    </div>
                    <div class="relative" style="height: 300px;">
                        <canvas id="dashboardChart"></canvas>
                    </div>
                </div>
            </section>
            
            <!-- Recent Notifications -->
            <section class="bg-gradient-to-br from-white to-gray-50 rounded-lg p-6 shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-sm text-[#1a4d38]">Recent Notifications</h3>
                    <button class="text-xs text-[#2f855A] hover:underline">Mark all as read</button>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg transition-colors duration-150">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                            <i class="bx bx-bell text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">New update available</p>
                            <p class="text-xs text-gray-600">Version 2.0.5 is now available with new features and improvements.</p>
                            <span class="text-xs text-gray-400 mt-1 block">2 hours ago</span>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    </div>
                    
                    <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg transition-colors duration-150">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                            <i class="bx bx-check-circle text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Task completed</p>
                            <p class="text-xs text-gray-600">Your task "Update user documentation" has been completed.</p>
                            <span class="text-xs text-gray-400 mt-1 block">5 hours ago</span>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg transition-colors duration-150">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                            <i class="bx bx-error text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Warning: Storage limit</p>
                            <p class="text-xs text-gray-600">You've used 85% of your storage. Consider upgrading your plan.</p>
                            <span class="text-xs text-gray-400 mt-1 block">1 day ago</span>
                        </div>
                    </div>
                </div>
                <a href="#" class="block text-center text-sm text-[#2f855A] font-medium mt-4 hover:underline">View all notifications</a>
            </section>
        </div>
    </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar on mobile
    const toggleBtn = document.getElementById('toggle-sidebar');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    
    if (toggleBtn && sidebar && overlay) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            document.body.style.overflow = sidebar.classList.contains('-translate-x-full') ? 'auto' : 'hidden';
        });
        
        // Close sidebar when clicking on overlay
        overlay.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            this.classList.add('hidden');
            document.body.style.overflow = 'auto';
        });
    }
    
    // Initialize charts
    const ctx = document.getElementById('dashboardChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Visitors',
                        data: [65, 59, 80, 81, 56, 55, 40, 45, 60, 70, 75, 80],
                        borderColor: '#2f855A',
                        backgroundColor: 'rgba(47, 133, 90, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Page Views',
                        data: [28, 48, 40, 19, 86, 27, 90, 65, 70, 80, 85, 90],
                        borderColor: '#3f8a56',
                        backgroundColor: 'rgba(63, 138, 86, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#1a4d38',
                        bodyColor: '#4b5563',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 0
                    }
                }
            }
        });
    }
    
    // Initialize dropdowns
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        const button = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        if (button && menu) {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                menu.classList.toggle('hidden');
            });
            
            // Close when clicking outside
            document.addEventListener('click', () => {
                menu.classList.add('hidden');
            });
        }
    });
    
    // Close modals when clicking the close button or outside
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    });
    
    // Close modals with escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            modals.forEach(modal => {
                modal.classList.add('hidden');
            });
        }
    });
});
</script>
@endpush

<!-- Add any additional styles -->
@push('styles')
<style>
    /* Custom scrollbar for dropdowns */
    .dropdown-menu {
        max-height: 300px;
        overflow-y: auto;
    }
    
    /* Animation for dropdowns */
    .dropdown-menu {
        animation: fadeIn 0.2s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Custom scrollbar for the sidebar */
    #sidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    #sidebar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
    }
    
    #sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 3px;
    }
    
    #sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.4);
    }
    
    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
</style>
@endpush
@endsection
