@php
// Get the authenticated user
$user = auth()->user();
// Use variables passed from route if available; otherwise fallback to session
$documents = isset($documents) ? $documents : session('uploaded_documents', []);
$archivedDocuments = isset($archivedDocuments) ? $archivedDocuments : session('archived_documents', []);
$settings = isset($settings) ? $settings : [
    'default_retention' => '5',
    'auto_archive' => true,
    'notification_emails' => '',
];
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Archival & Retention Policy | Admin Dashboard</title>
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
                <h1 class="text-2xl font-bold tracking-tight">Archival & Retention Policy</h1>
            </div>

    <!-- Extend Retention Modal -->
    <div id="extendRetentionModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="extend-retention-title">
        <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                <h3 id="extend-retention-title" class="font-semibold text-sm text-gray-900 select-none">Extend Retention</h3>
                <button id="closeExtendRetentionBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div class="px-6 pt-5 pb-6 text-sm text-gray-700">
                <p class="mb-3">Document: <span id="extendDocName" class="font-semibold text-gray-900"></span></p>
                <label for="extendNewPeriod" class="block text-xs font-medium text-gray-700 mb-1">New Retention Period</label>
                <select id="extendNewPeriod" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-[#2f855A]">
                    <option value="+6 months">Extend by 6 months</option>
                    <option value="+1 year">Extend by 1 year</option>
                    <option value="+3 years">Extend by 3 years</option>
                    <option value="custom">Custom (manual review)</option>
                </select>
                <div class="flex justify-end space-x-3 mt-5">
                    <button id="cancelExtendRetentionBtn" type="button" class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
                    <button id="confirmExtendRetentionBtn" type="button" class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Archive Now Modal -->
    <div id="archiveNowModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="archive-now-title">
        <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                <h3 id="archive-now-title" class="font-semibold text-sm text-gray-900 select-none">Archive Document</h3>
                <button id="closeArchiveNowBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div class="px-6 pt-5 pb-6 text-sm text-gray-700">
                <p class="mb-3">Document: <span id="archiveDocName" class="font-semibold text-gray-900"></span></p>
                <label for="archiveReason" class="block text-xs font-medium text-gray-700 mb-1">Reason (optional)</label>
                <textarea id="archiveReason" rows="3" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-[#2f855A]" placeholder="Add a note for archiving"></textarea>
                <div class="flex justify-end space-x-3 mt-5">
                    <button id="cancelArchiveNowBtn" type="button" class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
                    <button id="confirmArchiveNowBtn" type="button" class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200">Archive</button>
                </div>
            </div>
        </div>
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
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">New document uploaded</p>
                    <p class="text-sm text-gray-500">Quarterly_Report_Q3_2023.pdf was added</p>
                    <p class="text-xs text-gray-400 mt-1">10 minutes ago</p>
                </div>
            </li>
            <li class="flex items-start px-4 py-3 space-x-3">
                <div class="flex-shrink-0 mt-1">
                    <div class="bg-blue-200 text-blue-700 rounded-full p-2">
                        <i class="fas fa-comment-alt"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">New comment</p>
                    <p class="text-sm text-gray-500">John Doe commented on your document</p>
                    <p class="text-xs text-gray-400 mt-1">1 hour ago</p>
                </div>
            </li>
            <li class="flex items-start px-4 py-3 space-x-3">
                <div class="flex-shrink-0 mt-1">
                    <div class="bg-yellow-200 text-yellow-700 rounded-full p-2">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">Action required</p>
                    <p class="text-sm text-gray-500">Your approval is needed for a document</p>
                    <p class="text-xs text-gray-400 mt-1">3 hours ago</p>
                </div>
            </li>
        </ul>
        <div class="bg-gray-50 px-4 py-2 text-center">
            <a href="#" class="text-sm font-medium text-[#2f855A] hover:text-[#1a4d38]">View all notifications</a>
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
                        <ul class="dropdown-menu bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
                            <li><a href="{{ route('document.upload.indexing') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-upload mr-2"></i>Document Upload & Indexing</a></li>
                            <li><a href="{{ route('document.version.control') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-git-branch mr-2"></i>Version Control</a></li>
                            <li><a href="{{ route('document.access.control.permissions') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-lock mr-2"></i>Access Control & Permissions</a></li>
                            <li><a href="{{ route('document.archival.retention.policy') }}" class="block px-3 py-2 text-sm bg-white/30 rounded-lg"><i class="bx bx-archive mr-2"></i>Archival & Retention Policy</a></li>
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
                </ul>
            </div>
            <div class="px-5 pb-6">
                <div class="bg-white rounded-md p-4 text-center text-[#2f855A] text-sm font-semibold select-none">
                    Need Help?<br />
                    Contact support team at<br />
                    <a href="mailto:support@administrative.com" class="text-blue-600 hover:underline">support@administrative.com</a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main id="main-content" class="flex-1 p-6 w-full mt-16">
            <div class="dashboard-container max-w-7xl mx-auto">
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-md border border-gray-100 p-8 space-y-6">
                    <!-- Page Header -->
                    <div class="flex justify-between items-center">
                        <h2 class="text-[#1a4d38] font-bold text-xl mb-1">Archival & Retention Policy</h2>
                        <div class="flex space-x-3">
                            <button id="exportBtn" class="bg-[#2f855A] hover:bg-[#28644c] text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                                <i class="fas fa-download mr-2"></i> Export
                            </button>
                            <button id="printBtn" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                                <i class="fas fa-print mr-2"></i> Print
                            </button>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0 md:space-x-4">
                        <div class="relative flex-1 max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="searchInput" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] block w-full pl-10 p-2.5" placeholder="Search documents...">
                        </div>
                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                            <select id="filterStatus" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] block w-full p-2.5">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="archived">Archived</option>
                                <option value="pending">Pending Review</option>
                            </select>
                            <select id="filterCategory" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] block w-full p-2.5">
                                <option value="">All Categories</option>
                                <option value="financial">Financial</option>
                                <option value="hr">HR</option>
                                <option value="legal">Legal</option>
                                <option value="operations">Operations</option>
                            </select>
                            <select id="filterDocType" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] block w-full p-2.5">
                                <option value="">All Types</option>
                                <option value="pdf">PDF</option>
                                <option value="word">Word</option>
                                <option value="excel">Excel</option>
                                <option value="powerpoint">PowerPoint</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Retention Policies -->
                    @php
                        // Aggregate counts per policy category based on upcoming documents
                        $policyCategories = [
                            'financial' => ['label' => 'Financial', 'retention' => '7 years'],
                            'hr'        => ['label' => 'Employee Records', 'retention' => '7 years'],
                            'legal'     => ['label' => 'Legal Contracts', 'retention' => '10+ years'],
                        ];
                        $policyCounts = ['financial' => 0, 'hr' => 0, 'legal' => 0];
                        foreach ($documents as $docItem) {
                            $docCatKey = strtolower($docItem['category'] ?? '');
                            if (isset($policyCounts[$docCatKey])) {
                                $policyCounts[$docCatKey]++;
                            }
                        }
                    @endphp
                    <section class="mt-8">
                        <h3 class="font-semibold text-lg text-[#1a4d38] mb-4">
                            <i class='bx bx-time-five mr-2'></i>Document Retention Policies
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Financial Documents -->
                            <div class="dashboard-card p-6 policy-card cursor-pointer" data-policy-category="financial">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-medium text-gray-900">Financial Documents</h4>
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">7 years</span>
                                </div>
                                <p class="text-sm text-gray-600 mb-4">Tax returns, financial statements, audit reports, and related documents.</p>
                                <div class="flex items-center text-xs text-gray-500">
                                    <i class='bx bx-file mr-1'></i>
                                    <span>{{ $policyCounts['financial'] }} documents</span>
                                    <span class="mx-2">•</span>
                                    <span class="text-green-600">Compliant</span>
                                </div>
                            </div>

                            <!-- HR Records -->
                            <div class="dashboard-card p-6 policy-card cursor-pointer" data-policy-category="hr">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-medium text-gray-900">Employee Records</h4>
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">7 years</span>
                                </div>
                                <p class="text-sm text-gray-600 mb-4">Employment applications, performance reviews, and termination records.</p>
                                <div class="flex items-center text-xs text-gray-500">
                                    <i class='bx bx-file mr-1'></i>
                                    <span>{{ $policyCounts['hr'] }} documents</span>
                                    <span class="mx-2">•</span>
                                    <span class="text-green-600">Compliant</span>
                                </div>
                            </div>

                            <!-- Legal Documents -->
                            <div class="dashboard-card p-6 policy-card cursor-pointer" data-policy-category="legal">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-medium text-gray-900">Legal Contracts</h4>
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">10+ years</span>
                                </div>
                                <p class="text-sm text-gray-600 mb-4">Contracts, agreements, and other legal documents with varying retention periods.</p>
                                <div class="flex items-center text-xs text-gray-500">
                                    <i class='bx bx-file mr-1'></i>
                                    <span>{{ $policyCounts['legal'] }} documents</span>
                                    <span class="mx-2">•</span>
                                    <span class="text-amber-500">Review needed</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Upcoming Archivals -->
                    <section class="mt-10">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-lg text-[#1a4d38]">
                                <i class='bx bx-time mr-2'></i>Upcoming for Archival
                            </h3>
                            <button class="text-sm text-[#2f855A] hover:underline">View All</button>
                        </div>
                        
                        <div class="dashboard-card overflow-hidden">
                            <div class="overflow-x-auto">
                                <table id="upcomingTable" class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Retention Period</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled For</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($documents as $doc)
                                            @php
                                                $dtype = strtoupper($doc['type'] ?? '');
                                                $icon = in_array($dtype, ['PDF']) ? 'bxs-file-pdf text-red-500' : (in_array($dtype, ['WORD','DOC','DOCX']) ? 'bxs-file-doc text-blue-500' : (in_array($dtype, ['EXCEL','XLS','XLSX']) ? 'bxs-file-txt text-green-500' : 'bxs-file text-gray-500'));
                                                $rawCategory = $doc['category'] ?? ($doc['type'] ?? 'Other');
                                                $categoryKey = strtolower($rawCategory);
                                                $displayCategory = $categoryKey === 'hr' ? 'HR' : ucfirst($categoryKey);
                                                $retention = match($categoryKey) {
                                                    'financial' => '7 years',
                                                    'hr' => '7 years',
                                                    'legal' => '10+ years',
                                                    default => '5 years',
                                                };
                                                // Backward compat: if raw type is not a known category, keep original for display
                                                if (!in_array($categoryKey, ['financial','hr','legal','operations'])) {
                                                    $displayCategory = $doc['type'] ?? 'Other';
                                                }
                                            @endphp
                                            <tr class="hover:bg-gray-50" data-category="{{ $categoryKey }}" data-type="{{ strtolower($doc['type'] ?? 'other') }}">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <i class='bx {{ $icon }} text-xl mr-3'></i>
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-900">{{ $doc['name'] }}</div>
                                                            <div class="text-xs text-gray-500">{{ ($doc['type'] ?? 'File') }} • {{ ($doc['size'] ?? '') }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $displayCategory }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $retention }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::now()->addMonths(6)->format('M d, Y') }}</div>
                                                    <div class="text-xs text-gray-500">in 6 months</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <button class="text-[#2f855A] hover:text-[#1a4d38] mr-3 extend-btn"
                                                            data-name="{{ $doc['name'] }}"
                                                            data-category="{{ $categoryKey }}"
                                                            data-retention="{{ $retention }}">
                                                        Extend
                                                    </button>
                                                    <button class="text-gray-600 hover:text-gray-900 archive-btn"
                                                            data-name="{{ $doc['name'] }}"
                                                            data-category="{{ $categoryKey }}">
                                                        Archive Now
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">No documents available. Upload documents in "Document Upload & Indexing" to manage archival.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>

                    <!-- Archived Documents -->
                    @if(!empty($archivedDocuments) && count($archivedDocuments) > 0)
                    <section class="mt-10">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-lg text-[#1a4d38]">
                                <i class='bx bx-archive mr-2'></i>Recently Archived
                            </h3>
                            <button class="text-sm text-[#2f855A] hover:underline">View All</button>
                        </div>
                        
                        <div class="dashboard-card overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archived On</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Deletion</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($archivedDocuments as $adoc)
                                            @php
                                                $acat = ucfirst(strtolower($adoc['category'] ?? ($adoc['type'] ?? 'Other')));
                                            @endphp
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <i class='bx bxs-file text-gray-400 text-xl mr-3 opacity-50'></i>
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-500">{{ $adoc['name'] ?? 'Document' }}</div>
                                                            <div class="text-xs text-gray-400">{{ ($adoc['type'] ?? 'File') }} • {{ ($adoc['size'] ?? '—') }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $acat }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $adoc['archived_on'] ?? '—' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-500">{{ $adoc['scheduled_deletion'] ?? '—' }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <button class="text-[#2f855A] hover:text-[#1a4d38] mr-3" disabled>Restore</button>
                                                    <button class="text-red-600 hover:text-red-800" disabled>Delete Now</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">No archived documents yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                    @endif

                    <!-- Retention Policy Settings -->
                    <section class="mt-10">
                        <h3 class="font-semibold text-lg text-[#1a4d38] mb-4">
                            <i class='bx bx-cog mr-2'></i>Retention Policy Settings
                        </h3>
                        <div class="dashboard-card p-6">
                            <form action="{{ route('archival.settings.save') }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="defaultRetention" class="block text-sm font-medium text-gray-700 mb-1">Default Retention Period</label>
                                        <select id="defaultRetention" name="defaultRetention" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] block w-full p-2.5">
                                            @php $dr = $settings['default_retention'] ?? '5'; @endphp
                                            <option value="1" {{ $dr=='1' ? 'selected' : '' }}>1 year</option>
                                            <option value="3" {{ $dr=='3' ? 'selected' : '' }}>3 years</option>
                                            <option value="5" {{ $dr=='5' ? 'selected' : '' }}>5 years</option>
                                            <option value="7" {{ $dr=='7' ? 'selected' : '' }}>7 years</option>
                                            <option value="10" {{ $dr=='10' ? 'selected' : '' }}>10 years</option>
                                            <option value="permanent" {{ $dr=='permanent' ? 'selected' : '' }}>Permanent</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="autoArchive" class="block text-sm font-medium text-gray-700 mb-1">Auto-Archive Documents</label>
                                        <div class="flex items-center">
                                            <div class="relative inline-block w-10 mr-2 align-middle select-none">
                                                <input type="checkbox" id="autoArchive" name="autoArchive" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" {{ !empty($settings['auto_archive']) ? 'checked' : '' }} />
                                                <label for="autoArchive" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                                            </div>
                                            <span class="text-sm text-gray-600">Enabled</span>
                                        </div>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="notificationEmails" class="block text-sm font-medium text-gray-700 mb-1">Notification Emails</label>
                                        <input type="text" id="notificationEmails" name="notificationEmails" value="{{ $settings['notification_emails'] ?? '' }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] block w-full p-2.5" placeholder="Enter email addresses separated by commas">
                                        <p class="mt-1 text-xs text-gray-500">Email addresses to receive archival and deletion notifications</p>
                                    </div>
                                </div>
                                <div class="mt-6 flex justify-end space-x-3">
                                    <button type="button" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855A]">
                                        Cancel
                                    </button>
                                    <button type="submit" class="bg-[#2f855A] hover:bg-[#28644c] text-white px-4 py-2 rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855A]">
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                            <div class="mt-4 flex justify-end">
                                <form action="{{ route('archival.run') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-gray-800 hover:bg-black text-white px-4 py-2 rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800">
                                        Run Auto-Archive Now
                                    </button>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
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
                <form action="{{ route('profile.update') }}" method="POST">
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
            const searchInput = document.getElementById("searchInput");
            const filterStatus = document.getElementById("filterStatus");
            const filterCategory = document.getElementById("filterCategory");
            const filterDocType = document.getElementById("filterDocType");
            // Export/Print elements
            const exportBtn = document.getElementById('exportBtn');
            const printBtn = document.getElementById('printBtn');
            const upcomingTable = document.getElementById('upcomingTable');
            // Extend/Archive modals
            const extendRetentionModal = document.getElementById("extendRetentionModal");
            const closeExtendRetentionBtn = document.getElementById("closeExtendRetentionBtn");
            const cancelExtendRetentionBtn = document.getElementById("cancelExtendRetentionBtn");
            const confirmExtendRetentionBtn = document.getElementById("confirmExtendRetentionBtn");
            const extendDocName = document.getElementById("extendDocName");
            const extendNewPeriod = document.getElementById("extendNewPeriod");

            const archiveNowModal = document.getElementById("archiveNowModal");
            const closeArchiveNowBtn = document.getElementById("closeArchiveNowBtn");
            const cancelArchiveNowBtn = document.getElementById("cancelArchiveNowBtn");
            const confirmArchiveNowBtn = document.getElementById("confirmArchiveNowBtn");
            const archiveDocName = document.getElementById("archiveDocName");
            const archiveReason = document.getElementById("archiveReason");

            // Initialize sidebar state
            if (window.innerWidth >= 768) {
                sidebar.classList.remove("-ml-72");
                mainContent.classList.add("md:ml-72", "sidebar-open");
            } else {
                sidebar.classList.add("-ml-72");
                mainContent.classList.remove("md:ml-72", "sidebar-open");
                mainContent.classList.add("sidebar-closed");
            }

            // Modal helpers
            function openModal(el) { el.classList.add("active"); }
            function closeModal(el) { el.classList.remove("active"); }

            // Event delegation for Extend / Archive buttons
            document.addEventListener("click", (e) => {
                const extendBtn = e.target.closest('.extend-btn');
                if (extendBtn) {
                    const name = extendBtn.getAttribute('data-name') || 'Document';
                    extendDocName.textContent = name;
                    extendNewPeriod.value = '+6 months';
                    openModal(extendRetentionModal);
                    return;
                }
                const archiveBtn = e.target.closest('.archive-btn');
                if (archiveBtn) {
                    const name = archiveBtn.getAttribute('data-name') || 'Document';
                    archiveDocName.textContent = name;
                    archiveReason.value = '';
                    openModal(archiveNowModal);
                    return;
                }
            });

            // Close handlers for Extend modal
            closeExtendRetentionBtn.addEventListener('click', () => closeModal(extendRetentionModal));
            cancelExtendRetentionBtn.addEventListener('click', () => closeModal(extendRetentionModal));
            confirmExtendRetentionBtn.addEventListener('click', () => {
                const period = extendNewPeriod.value;
                closeModal(extendRetentionModal);
                Swal.fire({
                    icon: 'success',
                    title: 'Retention Extended',
                    text: `${extendDocName.textContent} retention updated (${period}).`,
                    timer: 1600,
                    showConfirmButton: false
                });
            });

            // Close handlers for Archive modal
            closeArchiveNowBtn.addEventListener('click', () => closeModal(archiveNowModal));
            cancelArchiveNowBtn.addEventListener('click', () => closeModal(archiveNowModal));
            confirmArchiveNowBtn.addEventListener('click', () => {
                const reason = archiveReason.value.trim();
                closeModal(archiveNowModal);
                Swal.fire({
                    icon: 'success',
                    title: 'Document Archived',
                    text: `${archiveDocName.textContent} has been archived.${reason ? ' Note: ' + reason : ''}`,
                    timer: 1600,
                    showConfirmButton: false
                });
            });

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

            // Filter documents
            function filterDocuments() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusFilter = filterStatus.value; // not used in current dataset but kept for UI parity
                const categoryFilter = filterCategory.value;
                const typeFilter = (filterDocType && filterDocType.value) ? filterDocType.value : '';
                const rows = document.querySelectorAll("tbody tr");

                rows.forEach(row => {
                    const name = row.querySelector("td:first-child") ? row.querySelector("td:first-child").textContent.toLowerCase() : '';
                    const category = (row.dataset.category || '').toLowerCase();
                    const dtype = (row.dataset.type || '').toLowerCase();

                    const matchesSearch = !searchTerm || name.includes(searchTerm);
                    const matchesStatus = true; // no explicit status column to filter
                    const matchesCategory = !categoryFilter || category === categoryFilter.toLowerCase();
                    const matchesType = !typeFilter || dtype === typeFilter.toLowerCase();

                    if (matchesSearch && matchesStatus && matchesCategory && matchesType) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                });
            }

            // Add event listeners for filtering
            searchInput.addEventListener("input", filterDocuments);
            filterStatus.addEventListener("change", filterDocuments);
            filterCategory.addEventListener("change", filterDocuments);
            if (filterDocType) filterDocType.addEventListener("change", filterDocuments);

            // Policy cards -> filter by category on click
            const policyCards = document.querySelectorAll('.policy-card');
            const upcomingSection = document.querySelector("section.mt-10");
            policyCards.forEach(card => {
                card.addEventListener('click', () => {
                    const cat = card.getAttribute('data-policy-category') || '';
                    if (filterCategory) {
                        filterCategory.value = cat;
                        filterDocuments();
                    }
                    // Scroll to upcoming table for context
                    if (upcomingSection && upcomingSection.scrollIntoView) {
                        upcomingSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });

            // Helper: convert table to CSV (skips hidden rows and last Actions column)
            function tableToCSV(tableEl) {
                if (!tableEl) return '';
                const rows = Array.from(tableEl.querySelectorAll('thead tr, tbody tr'));
                const csv = [];
                rows.forEach((row, idx) => {
                    // Skip hidden body rows
                    if (row.parentElement.tagName.toLowerCase() === 'tbody' && row.style.display === 'none') return;
                    const cells = Array.from(row.querySelectorAll(idx === 0 ? 'th' : 'td'));
                    // Only take first 4 columns (exclude Actions)
                    const take = cells.slice(0, 4).map(cell => {
                        let text = (cell.textContent || '').trim().replace(/\s+/g, ' ');
                        // Escape double quotes and wrap
                        text = '"' + text.replace(/"/g, '""') + '"';
                        return text;
                    });
                    csv.push(take.join(','));
                });
                return csv.join('\n');
            }

            // Export button -> download CSV of Upcoming Archivals
            if (exportBtn) {
                exportBtn.addEventListener('click', () => {
                    const csv = tableToCSV(upcomingTable);
                    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = 'upcoming_archivals.csv';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                });
            }

            // Print button -> print page with print CSS
            if (printBtn) {
                printBtn.addEventListener('click', () => {
                    window.print();
                });
            }
        });
    </script>
</body>
</html>