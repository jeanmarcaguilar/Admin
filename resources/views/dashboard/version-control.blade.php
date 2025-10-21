@php
// Get the authenticated user
$user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Document Version Control - Admin Dashboard</title>
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
    </style>
</head>
<body class="bg-gray-100">
    @php
    // Get the authenticated user
    $user = auth()->user();
    @endphp

    <!-- Navigation Bar -->
    <nav class="w-full p-3 h-16 bg-[#28644c] text-white shadow-md fixed top-0 left-0 right-0 z-50">
        <div class="flex justify-between items-center h-full max-w-7xl mx-auto">
            <div class="flex items-center space-x-4">
                <button id="toggle-btn" class="pl-2 focus:outline-none">
                    <i class="fa-solid fa-bars text-2xl cursor-pointer"></i>
                </button>
                <h1 class="text-2xl font-bold tracking-tight">Document Version Control</h1>
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
                        <i class="fas fa-file-upload"></i>
                    </div>
                </div>
                <div class="flex-grow text-sm">
                    <p class="font-semibold text-gray-900 leading-tight">New Version Uploaded</p>
                    <p class="text-gray-600 leading-tight text-xs">Q3_Financial_Report.pdf v3.2 uploaded</p>
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
                    <p class="font-semibold text-gray-900 leading-tight">Document Reviewed</p>
                    <p class="text-gray-600 leading-tight text-xs">Client_Contract_2023.docx approved</p>
                    <p class="text-gray-400 text-xs mt-0.5">1 day ago</p>
                </div>
            </li>
            <li class="flex items-start px-4 py-3 space-x-3">
                <div class="flex-shrink-0 mt-1">
                    <div class="bg-yellow-200 text-yellow-700 rounded-full p-2">
                        <i class="fas fa-exclamation"></i>
                    </div>
                </div>
                <div class="flex-grow text-sm">
                    <p class="font-semibold text-gray-900 leading-tight">Pending Review</p>
                    <p class="text-gray-600 leading-tight text-xs">Project_Proposal.pdf needs review</p>
                    <p class="text-gray-400 text-xs mt-0.5">3 days ago</p>
                </div>
            </li>
        </ul>
        <div class="text-center py-2 border-t border-gray-200">
            <a class="text-[#28644c] text-xs font-semibold hover:underline" href="#">View all notifications</a>
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
                    <li class="has-dropdown active">
                        <div class="flex items-center font-medium justify-between text-lg bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-file"></i>
                                <span>Document Management</span>
                            </div>
                            <i class="bx bx-chevron-down text-2xl transition-transform duration-300"></i>
                        </div>
                        <ul class="dropdown-menu active bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
                            <li><a href="{{ route('document.upload.indexing') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-upload mr-2"></i>Document Upload & Indexing</a></li>
                            <li><a href="{{ route('document.version.control') }}" class="block px-3 py-2 text-sm bg-white/30 rounded-lg"><i class="bx bx-git-branch mr-2"></i>Version Control</a></li>
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
                            <li><a href="{{ route('document.case.management') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-briefcase mr-2"></i>Case Management</a></li>
                            <li><a href="{{ route('contract.management') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-file-blank mr-2"></i>Contract Management</a></li>
                            <li><a href="{{ route('document.compliance.tracking') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-check-double mr-2"></i>Compliance Tracking</a></li>
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
                    <!-- Header Section -->
                    <div class="flex justify-between items-center">
                        <h2 class="text-[#1a4d38] font-bold text-xl mb-1">Document Version Control</h2>
                    </div>

                    <!-- Search -->
                    <section class="dashboard-card bg-gradient-to-br from-white to-gray-50 rounded-lg p-6 shadow-sm border border-gray-200">
                        <h3 class="font-semibold text-lg text-[#1a4d38] mb-4">
                            <i class='bx bx-search-alt-2 mr-2'></i>Search Documents
                        </h3>
                        <div>
                            <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="Search by name, type or category..." class="w-full pl-10 pr-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-[#2f855A] focus:ring focus:ring-[#2f855A]/50 text-sm">
                                <i class='bx bx-search absolute left-3 top-3 text-gray-400'></i>
                            </div>
                        </div>
                    </section>

                    <!-- Document Versions Table -->
                    <section class="mt-8">
                        <h3 class="font-semibold text-lg text-[#1a4d38] mb-4">
                            <i class='bx bx-list-ul mr-2'></i>Document Versions
                        </h3>
                        <div class="dashboard-card bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Modified</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modified By</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @php
                                            $documents = $documents ?? [];
                                            $userName = $user->name ?? 'User';
                                            $initials = collect(explode(' ', $userName))->map(fn($p) => strtoupper(substr($p,0,1)))->implode('');
                                        @endphp
                                        @forelse($documents as $doc)
                                            <tr class="activity-item" data-doc-id="{{ $doc['id'] }}" data-name="{{ strtolower($doc['name'] ?? '') }}" data-type="{{ strtolower($doc['type'] ?? '') }}" data-category="{{ strtolower($doc['category'] ?? 'other') }}" data-status="{{ strtolower($doc['status'] ?? 'indexed') }}">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                            @php
                                                                $dtype = strtoupper($doc['type'] ?? '');
                                                                $icon = in_array($dtype, ['PDF']) ? 'pdf' : (in_array($dtype, ['WORD','DOC','DOCX']) ? 'doc' : (in_array($dtype, ['EXCEL','XLS','XLSX']) ? 'xls' : 'txt'));
                                                            @endphp
                                                            <i class='bx bxs-file-{{ $icon }} text-blue-600'></i>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">{{ $doc['name'] }}</div>
                                                            <div class="text-sm text-gray-500">{{ $doc['size'] }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $doc['type'] }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst(strtolower($doc['category'] ?? 'Other')) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ isset($doc['uploaded']) ? \Carbon\Carbon::parse($doc['uploaded'])->diffForHumans() : \Carbon\Carbon::now()->diffForHumans() }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <div class="flex items-center">
                                                        <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-medium">{{ $initials }}</div>
                                                        <span class="ml-2">{{ $userName }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ ($doc['status'] ?? '') == 'Approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ $doc['status'] ?? 'Indexed' }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <button onclick="showVersionDetails({{ json_encode($doc) }})" class="text-blue-600 hover:text-blue-900 mr-3 bg-transparent border-none p-0 cursor-pointer">View</button>
                                                    <button onclick="showDeleteDocumentConfirmation('{{ $doc['id'] }}')" class="text-red-600 hover:text-red-900 bg-transparent border-none p-0 cursor-pointer">Delete</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                                    No documents found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchInput');
        const tbody = document.querySelector('table.min-w-full tbody');
        if (!tbody) return;

        const rows = Array.from(tbody.querySelectorAll('tr.activity-item'));

        function norm(v){ return (v || '').toString().trim().toLowerCase(); }

        function rowMatches(row){
          const q = norm(searchInput?.value);
          const name = row.dataset.name || '';
          const type = row.dataset.type || '';
          const cat = row.dataset.category || '';
          return !q || name.includes(q) || type.includes(q) || cat.includes(q);
        }

        function applyFilters(){
          rows.forEach(r => {
            const show = rowMatches(r);
            r.classList.toggle('hidden', !show);
          });
        }

        searchInput?.addEventListener('input', applyFilters);
        applyFilters();
      });
    </script>
</body>
</html>
    <!-- Version History Modal -->
    <div id="versionHistoryModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="version-history-modal-title">
        <div class="bg-white rounded-lg w-full max-w-4xl">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 id="version-history-modal-title" class="text-xl font-semibold text-gray-900">Version History</h3>
                <button onclick="closeModal('versionHistoryModal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="p-6" id="versionHistoryContent">
                <!-- Content will be loaded by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Version Details Modal (View) -->
    <div id="versionDetailsModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="version-details-modal-title">
        <div class="bg-white rounded-lg w-full max-w-2xl">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 id="version-details-modal-title" class="text-xl font-semibold text-gray-900">Document Details</h3>
                <button onclick="closeModal('versionDetailsModal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div id="versionDetailsContent" class="p-6">
                <!-- Populated dynamically by showVersionDetails(doc) -->
            </div>
        </div>
    </div>

    <!-- New Version Modal -->
    <div id="newVersionModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="new-version-modal-title">
        <div class="bg-white rounded-lg w-full max-w-lg">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 id="new-version-modal-title" class="text-xl font-semibold text-gray-900">Upload New Version</h3>
                <button onclick="closeModal('newVersionModal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <form id="newVersionForm" class="p-6" action="{{ route('document.version.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="documentSelect" class="block text-sm font-medium text-gray-700">Select Document</label>
                        <select id="documentSelect" name="document_id" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm">
                            <option value="">Select a document...</option>
                            @foreach($documents as $doc)
                                <option value="{{ $doc['id'] }}">{{ $doc['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="versionNumber" class="block text-sm font-medium text-gray-700">Version Number</label>
                        <input type="text" id="versionNumber" name="version_number" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm" placeholder="e.g., 2.0">
                    </div>
                    <div>
                        <label for="versionNotes" class="block text-sm font-medium text-gray-700">Version Notes</label>
                        <textarea id="versionNotes" name="version_notes" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm" placeholder="Describe the changes in this version..."></textarea>
                    </div>
                    <div>
                        <label for="file-upload" class="block text-sm font-medium text-gray-700">Upload File</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <div class="flex text-sm text-gray-600">
                                    <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-[#2f855A] hover:text-[#1e6e3f] focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-[#2f855A]">
                                        <span>Upload a file</span>
                                        <input id="file-upload" name="file" type="file" required class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, DOCX, XLSX up to 50MB</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('newVersionModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855A]">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#2f855A] hover:bg-[#276749] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855A]">
                        Upload New Version
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Document Confirmation Modal -->
    <div id="deleteDocumentModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="delete-document-modal-title">
        <div class="bg-white rounded-lg w-full max-w-md">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation text-red-600 text-xl"></i>
                </div>
                <h3 id="delete-document-modal-title" class="text-lg font-medium text-gray-900 mb-2">Delete Document</h3>
                <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete this document? This action cannot be undone.</p>
                <div class="flex justify-center space-x-4">
                    <button type="button" onclick="closeModal('deleteDocumentModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        No, Keep It
                    </button>
                    <button type="button" id="confirmDeleteDocumentBtn" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Yes, Delete Document
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
    const newVersionBtn = document.getElementById("newVersionBtn");
    const newVersionModal = document.getElementById("newVersionModal");
    const newVersionForm = document.getElementById("newVersionForm");
    const documentsTable = document.querySelector('table tbody');
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

    // Modal helpers
    function openModal(modalId) {
        const el = document.getElementById(modalId);
        if (!el) return;
        el.classList.remove('hidden');
        el.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(modalId) {
        const el = document.getElementById(modalId);
        if (!el) return;
        el.classList.remove('active');
        el.classList.add('hidden');
        document.body.style.overflow = '';
    }
    // Expose helpers globally for inline onclick="closeModal('...')" usage
    window.openModal = openModal;
    window.closeModal = closeModal;

    // View (Version Details)
    window.showVersionDetails = function(doc) {
        const contentDiv = document.getElementById('versionDetailsContent');
        const uploadedDate = doc.uploaded ? new Date(doc.uploaded) : new Date();
        const formattedDate = uploadedDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });

        contentDiv.innerHTML = `
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">${doc.name}</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium ${doc.status === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                        ${doc.status || 'Indexed'}
                    </span>
                </div>
                <div class="border-t border-b border-gray-200 py-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">Document ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">${doc.id}</dd>
                        </div>
                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">${doc.type || '—'}</dd>
                        </div>
                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">Size</dt>
                            <dd class="mt-1 text-sm text-gray-900">${doc.size || '—'}</dd>
                        </div>
                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">Uploaded</dt>
                            <dd class="mt-1 text-sm text-gray-900">${formattedDate}</dd>
                        </div>
                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">Version</dt>
                            <dd class="mt-1 text-sm text-gray-900">${doc.version || '1.0'}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('versionDetailsModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </button>
                <button type="button" onclick="showDeleteDocumentConfirmation('${doc.id}')" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Delete Document
                </button>
            </div>
        `;
        openModal('versionDetailsModal');
    };

    // Delete flow
    let currentDeleteDocumentUrl = '';
    let currentDocumentId = '';
    window.showDeleteDocumentConfirmation = function(docId) {
        currentDocumentId = docId;
        const baseDocUrl = "{{ url('/document') }}";
        currentDeleteDocumentUrl = `${baseDocUrl}/${docId}/delete`;
        const btn = document.getElementById('confirmDeleteDocumentBtn');
        btn.onclick = handleDeleteDocument;
        openModal('deleteDocumentModal');
    };
    async function handleDeleteDocument() {
        const btn = document.getElementById('confirmDeleteDocumentBtn');
        const original = btn.innerHTML;
        try {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Deleting...';
            const response = await fetch(currentDeleteDocumentUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

    // Close modals on outside click
    window.addEventListener('click', (e) => {
        if (newVersionModal && !newVersionModal.classList.contains('hidden')) {
            const panel = newVersionModal.querySelector('div');
            if (panel && !panel.contains(e.target)) {
                closeModal('newVersionModal');
            }
        }
    });

    // Prevent modal inner clicks from closing the New Version modal
    if (newVersionModal) {
        const panel = newVersionModal.querySelector('div');
        panel && panel.addEventListener('click', (e) => e.stopPropagation());
    }
            const data = await response.json();
            if (response.ok) {
                const row = document.querySelector(`tr[data-doc-id="${currentDocumentId}"]`);
                if (row) row.remove();
                closeModal('deleteDocumentModal');
                // optional toast
            } else {
                throw new Error(data.message || 'Failed to delete document');
            }
        } catch (e) {
            alert(e.message || 'Delete failed');
        } finally {
            btn.disabled = false;
            btn.innerHTML = original;
        }
    }
    const signOutModal = document.getElementById("signOutModal");
    const cancelSignOutBtn = document.getElementById("cancelSignOutBtn");
    const cancelSignOutBtn2 = document.getElementById("cancelSignOutBtn2");
    const openSignOutBtn = document.getElementById("openSignOutBtn");

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

    // Close all dropdown menus
    function closeAllDropdowns() {
        dropdownToggles.forEach((toggle) => {
            const dropdown = toggle.nextElementSibling;
            const chevron = toggle.querySelector(".bx-chevron-down");
            if (!dropdown.classList.contains("hidden")) {
                dropdown.classList.add("hidden");
                chevron.classList.remove("rotate-180");
                dropdown.style.maxHeight = "0";
            }
        });
    }

    // Handle dropdown menu toggling
    dropdownToggles.forEach((toggle) => {
        toggle.addEventListener("click", () => {
            const dropdown = toggle.nextElementSibling;
            const chevron = toggle.querySelector(".bx-chevron-down");
            dropdownToggles.forEach((otherToggle) => {
                if (otherToggle !== toggle) {
                    otherToggle.nextElementSibling.classList.add("hidden");
                    otherToggle.querySelector(".bx-chevron-down").classList.remove("rotate-180");
                    otherToggle.nextElementSibling.style.maxHeight = "0";
                }
            });
            const isHidden = dropdown.classList.contains("hidden");
            dropdown.classList.toggle("hidden", !isHidden);
            dropdown.classList.toggle("active", isHidden);
            chevron.classList.toggle("rotate-180", isHidden);
            dropdown.style.maxHeight = isHidden ? `${dropdown.scrollHeight}px` : "0";
        });
    });

    // Close sidebar on overlay click
    overlay.addEventListener("click", () => {
        sidebar.classList.add("-ml-72");
        overlay.classList.add("hidden");
        document.body.style.overflow = "";
        mainContent.classList.remove("sidebar-open");
        mainContent.classList.add("sidebar-closed");
        closeAllDropdowns();
    });

    // Toggle sidebar on button click
    toggleBtn.addEventListener("click", toggleSidebar);

    // Toggle notification dropdown
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

    // Toggle user menu dropdown
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

    // Open profile modal
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

    // Close profile modal
    closeProfileBtn.addEventListener("click", () => {
        profileModal.classList.remove("active");
    });
    closeProfileBtn2.addEventListener("click", () => {
        profileModal.classList.remove("active");
    });

    // Open account settings modal
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

    // Close account settings modal
    closeAccountSettingsBtn.addEventListener("click", () => {
        accountSettingsModal.classList.remove("active");
    });
    cancelAccountSettingsBtn.addEventListener("click", () => {
        accountSettingsModal.classList.remove("active");
    });

    // Open privacy & security modal
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

    // Close privacy & security modal
    closePrivacySecurityBtn.addEventListener("click", () => {
        privacySecurityModal.classList.remove("active");
    });
    cancelPrivacySecurityBtn.addEventListener("click", () => {
        privacySecurityModal.classList.remove("active");
    });

    // Close sign out modal
    cancelSignOutBtn.addEventListener("click", () => {
        signOutModal.classList.remove("active");
    });
    cancelSignOutBtn2.addEventListener("click", () => {
        signOutModal.classList.remove("active");
    });

    // Close dropdowns and modals on outside click
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

    // Prevent modal content click from closing modals
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

    // Open new version modal
    if (newVersionBtn) {
        newVersionBtn.addEventListener("click", () => {
            if (newVersionForm) newVersionForm.reset();
            openModal('newVersionModal');
        });
    }

    // Add document to table
    function addDocumentToTable(doc) {
        const row = document.createElement('tr');
        row.className = 'activity-item';
        row.setAttribute('data-doc-id', doc.id);

        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class='bx bxs-file-${doc.type === "Report" || doc.type === "Proposal" ? "pdf" : "doc"} text-blue-600'></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">${doc.name}</div>
                        <div class="text-sm text-gray-500">${doc.size}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${doc.type}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">v${doc.version}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${doc.modified}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="flex items-center">
                    <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-medium">${doc.modified_by.initials}</div>
                    <span class="ml-2">${doc.modified_by.name}</span>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${doc.status === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">${doc.status}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button onclick='showVersionDetails(${JSON.stringify(doc)})' class="text-blue-600 hover:text-blue-900 mr-3 bg-transparent border-none p-0 cursor-pointer">View</button>
                <button onclick="showDeleteDocumentConfirmation('${doc.id}')" class="text-red-600 hover:text-red-900 bg-transparent border-none p-0 cursor-pointer">Delete</button>
            </td>
        `;

        if (documentsTable.querySelector('tr:first-child').classList.contains('bg-gray-50')) {
            documentsTable.insertBefore(row, documentsTable.querySelector('tr:first-child').nextSibling);
        } else {
            documentsTable.insertBefore(row, documentsTable.firstChild);
        }

        const noDocumentsRow = documentsTable.querySelector('tr:only-child td[colspan="7"]');
        if (noDocumentsRow) {
            noDocumentsRow.closest('tr').remove();
        }
    }

    // Show success message toast
    function showSuccessMessage(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center justify-between z-50';
        toast.style.minWidth = '300px';
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>${message}</span>
            </div>
            <button class="ml-4 text-white hover:text-gray-100">
                <i class="fas fa-times"></i>
            </button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.remove();
        }, 5000);
        toast.querySelector('button').addEventListener('click', () => {
            toast.remove();
        });
    }

    // Handle new version form submission
    if (newVersionForm) {
        newVersionForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    addDocumentToTable(data.document);
                    showSuccessMessage(data.message || 'Document version uploaded successfully!');
                    this.reset();
                    closeModal('newVersionModal');
                } else {
                    throw new Error(data.message || 'Failed to upload document version');
                }
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'An error occurred while uploading the document version');
            }
        });
    }
    
});
    </script>
</body>
</html>