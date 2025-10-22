<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Document Upload & Indexing | Admin Dashboard</title>
    <link rel="icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
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
                <h1 class="text-2xl font-bold tracking-tight">Document Upload & Indexing</h1>
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
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
                <div class="flex-grow text-sm">
                    <p class="font-semibold text-gray-900 leading-tight">Document Uploaded</p>
                    <p class="text-gray-600 leading-tight text-xs">New document uploaded: Q3-Report-2023.pdf</p>
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
                    <p class="font-semibold text-gray-900 leading-tight">Document Reviewed</p>
                    <p class="text-gray-600 leading-tight text-xs">Meeting-Minutes.docx reviewed</p>
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
                    <p class="font-semibold text-gray-900 leading-tight">Document Indexed</p>
                    <p class="text-gray-600 leading-tight text-xs">Budget-2023.xlsx indexed</p>
                    <p class="text-gray-400 text-xs mt-0.5">Yesterday</p>
                </div>
            </li>
        </ul>
        <div class="text-center py-2 border-t border-gray-200">
            <a class="text-[#28644c] text-xs font-semibold hover:underline" href="#">View all notifications</a>
        </div>
    </div>

    <!-- Document Details Modal (used by View button) -->
    <div id="documentModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="document-details-modal-title">
        <div class="bg-white rounded-lg w-full max-w-2xl">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 id="document-details-modal-title" class="text-xl font-semibold text-gray-900">Document Details</h3>
                <button onclick="closeModal('documentModal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div id="documentDetailsContent" class="p-6">
                <!-- Populated dynamically by showDocumentDetails(doc) -->
            </div>
        </div>
    </div>

    <!-- Download Document Modal -->
    <div id="downloadDocumentModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="download-document-modal-title">
        <div class="bg-white rounded-lg w-full max-w-md">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 id="download-document-modal-title" class="text-xl font-semibold text-gray-900">Download Document</h3>
                <button onclick="closeModal('downloadDocumentModal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <div class="text-sm text-gray-500">File</div>
                    <div id="downloadDocName" class="text-sm font-medium text-gray-900">—</div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <div class="text-xs text-gray-500">Type</div>
                        <div id="downloadDocType" class="text-sm text-gray-900">—</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Size</div>
                        <div id="downloadDocSize" class="text-sm text-gray-900">—</div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('downloadDocumentModal')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                    <button type="button" id="confirmDownloadBtn" onclick="performDownload()" class="px-4 py-2 rounded-md text-sm font-medium text-white bg-[#2f855A] hover:bg-[#276749]">Download</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Share Document Modal -->
    <div id="shareDocumentModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="share-document-modal-title">
        <div class="bg-white rounded-lg w-full max-w-md">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 id="share-document-modal-title" class="text-xl font-semibold text-gray-900">Share Document</h3>
                <button onclick="closeModal('shareDocumentModal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <div class="text-sm text-gray-500">File</div>
                    <div id="shareDocName" class="text-sm font-medium text-gray-900">—</div>
                </div>
                <div class="mb-4">
                    <label for="shareEmail" class="block text-xs text-gray-500 mb-1">Share with (email)</label>
                    <input id="shareEmail" type="email" placeholder="name@example.com" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]" />
                </div>
                <div class="mb-6">
                    <label class="block text-xs text-gray-500 mb-1">Share link</label>
                    <div class="flex">
                        <input id="shareLink" type="text" readonly class="flex-1 border border-gray-300 rounded-l-md px-3 py-2 text-sm bg-gray-50" />
                        <button type="button" onclick="copyShareLink()" class="px-3 py-2 border border-gray-300 rounded-r-md text-sm bg-white hover:bg-gray-50">Copy</button>
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('shareDocumentModal')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                    <button type="button" onclick="sendShareInvite()" class="px-4 py-2 rounded-md text-sm font-medium text-white bg-[#2f855A] hover:bg-[#276749]">Send Invite</button>
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
                            <li><a href="{{ route('document.upload.indexing') }}" class="block px-3 py-2 text-sm bg-white/30 rounded-lg"><i class="bx bx-upload mr-2"></i>Document Upload & Indexing</a></li>
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
                            <li><a href="{{ route('document.case.management') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-briefcase mr-2"></i>Case Management</a></li>
                            <li><a href="{{ route('contract.management') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-file-blank mr-2"></i>Contract Management</a></li>
                            <li><a href="{{ route('document.compliance.tracking') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-check-double mr-2"></i>Compliance Tracking</a></li>
                            <li><a href="{{ route('deadline.hearing.alerts') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-bell mr-2"></i>Deadline & Hearing Alerts</a></li>
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
                    <div class="flex justify-between items-center">
                        <h2 class="text-[#1a4d38] font-bold text-xl mb-1">Document Upload & Indexing</h2>
                        <div class="flex space-x-3">
                            <button id="uploadDocumentBtn" class="bg-[#2f855A] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#276749] transition-colors focus:outline-none focus:ring-2 focus:ring-[#2f855A] focus:ring-offset-2">
                                <i class="bx bx-plus mr-1"></i> Upload Document
                            </button>
                        </div>
                    </div>

                    <!-- Document Upload Section -->
                    <section id="uploadSection" class="dashboard-card bg-gradient-to-br from-white to-gray-50 rounded-lg p-6 shadow-sm border border-gray-200">
                        <h3 class="font-semibold text-lg text-[#1a4d38] mb-4">
                            <i class='bx bx-cloud-upload mr-2'></i>Upload New Document
                        </h3>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                            <i class="bx bx-cloud-upload text-4xl text-gray-400 mb-3"></i>
                            <p class="text-gray-600 mb-4">Drag and drop files here or click to browse</p>
                            <input type="file" id="document-upload" class="hidden" multiple accept=".pdf,.docx,.xlsx,.pptx">
                            <label for="document-upload" class="inline-flex items-center px-4 py-2 bg-[#2f855A] text-white rounded-md hover:bg-[#276749] cursor-pointer transition-colors focus:outline-none focus:ring-2 focus:ring-[#2f855A] focus:ring-offset-2">
                                <i class="bx bx-upload mr-2"></i>
                                Select Files
                            </label>
                            <p class="text-xs text-gray-500 mt-3">Supports PDF, DOCX, XLSX, PPTX (Max 50MB)</p>
                            <!-- Category and Type selectors for archival mapping -->
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl mx-auto text-left">
                                <div>
                                    <label for="uploadCategory" class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                                    <select id="uploadCategory" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]">
                                        <option value="">Auto-detect</option>
                                        <option value="financial">Financial</option>
                                        <option value="hr">HR</option>
                                        <option value="legal">Legal</option>
                                        <option value="operations">Operations</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="uploadDocType" class="block text-xs font-medium text-gray-700 mb-1">Document Type</label>
                                    <select id="uploadDocType" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]">
                                        <option value="">Infer from file</option>
                                        <option value="PDF">PDF</option>
                                        <option value="Word">Word</option>
                                        <option value="Excel">Excel</option>
                                        <option value="PowerPoint">PowerPoint</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Browse by Category -->
                    <section class="mt-8">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-lg text-[#1a4d38]">
                                <i class='bx bx-category mr-2'></i>Browse by Category
                            </h3>
                            <div class="text-sm text-gray-500" id="documentCount">
                                Showing <span id="visibleCount">0</span> of <span id="totalCount">0</span> documents
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                            <button type="button" class="category-card group bg-white border border-gray-200 rounded-xl p-5 text-left shadow-sm hover:shadow-md transition flex items-start gap-3 active-category" data-category="all">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-700 flex items-center justify-center">
                                    <i class="bx bx-grid-alt text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">All Documents</div>
                                    <div class="text-xs text-gray-500">View all documents</div>
                                </div>
                            </button>
                            <button type="button" class="category-card group bg-white border border-gray-200 rounded-xl p-5 text-left shadow-sm hover:shadow-md transition flex items-start gap-3" data-category="financial">
                                <div class="w-10 h-10 rounded-lg bg-green-100 text-green-700 flex items-center justify-center">
                                    <i class="bx bx-dollar text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">Financial</div>
                                    <div class="text-xs text-gray-500">Budgets, invoices, reports</div>
                                </div>
                            </button>
                            <button type="button" class="category-card group bg-white border border-gray-200 rounded-xl p-5 text-left shadow-sm hover:shadow-md transition flex items-start gap-3" data-category="hr">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                                    <i class="bx bx-id-card text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">HR</div>
                                    <div class="text-xs text-gray-500">Employee files, policies</div>
                                </div>
                            </button>
                            <button type="button" class="category-card group bg-white border border-gray-200 rounded-xl p-5 text-left shadow-sm hover:shadow-md transition flex items-start gap-3" data-category="legal">
                                <div class="w-10 h-10 rounded-lg bg-yellow-100 text-yellow-700 flex items-center justify-center">
                                    <i class="bx bx-gavel text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">Legal</div>
                                    <div class="text-xs text-gray-500">Contracts, case files</div>
                                </div>
                            </button>
                            <button type="button" class="category-card group bg-white border border-gray-200 rounded-xl p-5 text-left shadow-sm hover:shadow-md transition flex items-start gap-3" data-category="operations">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center">
                                    <i class="bx bx-cog text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">Operations</div>
                                    <div class="text-xs text-gray-500">SOPs, checklists, logs</div>
                                </div>
                            </button>
                        </div>
                    </section>

                    <!-- Document List Section -->
                    <section class="mt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-lg text-[#1a4d38]">
                                <i class='bx bx-list-ul mr-2'></i>Documents
                            </h3>
                            <div class="relative">
                                <input type="text" id="documentSearch" placeholder="Search documents..." class="pl-8 pr-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A] focus:border-transparent">
                                <i class="bx bx-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="documentList" class="bg-white divide-y divide-gray-200">
                                        <!-- Documents will be loaded here by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="bg-white px-6 py-3 flex items-center justify-between border-t border-gray-200">
                                <div class="text-sm text-gray-700">
                                    Showing <span id="paginationStart">1</span> to <span id="paginationEnd">10</span> of <span id="paginationTotal">0</span> results
                                </div>
                                <div class="flex space-x-2">
                                    <button id="prevPage" class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855A] disabled:opacity-50" disabled>
                                        Previous
                                    </button>
                                    <button id="nextPage" class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855A] disabled:opacity-50" disabled>
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>

    <!-- Document Details Modal -->
    <div id="documentModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="document-modal-title">
        <div class="bg-white rounded-lg w-full max-w-md">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 id="document-modal-title" class="text-xl font-semibold text-gray-900">Document Details</h3>
                <button onclick="closeModal('documentModal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="p-6" id="documentDetailsContent">
                <!-- Content will be loaded by JavaScript -->
            </div>
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
            const uploadDocumentBtn = document.getElementById("uploadDocumentBtn");
            const fileInput = document.getElementById("document-upload");
            const dropZone = fileInput.parentElement;
            const documentsTable = document.querySelector("table tbody");
            const uploadCategorySelect = document.getElementById("uploadCategory");
            const uploadSection = document.getElementById("uploadSection");
            const categoryCards = document.querySelectorAll('.category-card');
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
                    if (!dropdown.classList.contains("hidden")) {
                        dropdown.classList.add("hidden");
                        chevron.classList.remove("rotate-180");
                        dropdown.style.maxHeight = "0";
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

            overlay.addEventListener("click", () => {
                sidebar.classList.add("-ml-72");
                overlay.classList.add("hidden");
                document.body.style.overflow = "";
                mainContent.classList.remove("sidebar-open");
                mainContent.classList.add("sidebar-closed");
                closeAllDropdowns();
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

            // Initialize dropdowns
            document.querySelectorAll(".has-dropdown.active .dropdown-menu").forEach((menu) => {
                menu.classList.remove("hidden");
                menu.classList.add("active");
                menu.style.maxHeight = `${menu.scrollHeight}px`;
            });

            // Document upload handling
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                dropZone.classList.add('border-blue-500', 'bg-blue-50');
            }

            function unhighlight() {
                dropZone.classList.remove('border-blue-500', 'bg-blue-50');
            }

            dropZone.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles(files);
            }

            fileInput.addEventListener('change', function() {
                handleFiles(this.files);
            });

            uploadDocumentBtn.addEventListener('click', () => {
                fileInput.click();
            });

            function handleFiles(files) {
                if (files.length > 0) {
                    const formData = new FormData();
                    const catEl = document.getElementById('uploadCategory');
                    const typeEl = document.getElementById('uploadDocType');
                    const selCategory = catEl ? (catEl.value || '') : '';
                    const selDocType = typeEl ? (typeEl.value || '') : '';

                    const allowedExt = ['pdf','doc','docx','xls','xlsx','ppt','pptx'];
                    const validFiles = [];
                    const tooLarge = [];
                    const invalidType = [];

                    Array.from(files).forEach(file => {
                        const ext = (file.name.split('.').pop() || '').toLowerCase();
                        if (file.size > 50 * 1024 * 1024) {
                            tooLarge.push(file.name);
                            return;
                        }
                        if (!allowedExt.includes(ext)) {
                            invalidType.push(file.name);
                            return;
                        }
                        validFiles.push(file);
                        formData.append('documents[]', file);
                    });

                    if (tooLarge.length) {
                        alert(`These files exceed the 50MB limit and were skipped:\n- ${tooLarge.join('\n- ')}`);
                    }
                    if (invalidType.length) {
                        alert(`These files are not in allowed formats (PDF, Word, Excel, PowerPoint) and were skipped:\n- ${invalidType.join('\n- ')}`);
                    }

                    if (validFiles.length === 0) {
                        alert('No valid files to upload.');
                        return;
                    }

                    if (selCategory) formData.append('category', selCategory);
                    if (selDocType) formData.append('docType', selDocType);

                    const uploadedCount = validFiles.length;

                    fetch('{{ route('document.upload.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Success validation/confirmation before redirect
                            if (window.Swal && Swal.fire) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Upload complete',
                                    text: `${uploadedCount} file(s) uploaded successfully.`,
                                    confirmButtonColor: '#2f855a',
                                    confirmButtonText: 'View in Version Control'
                                }).then(() => {
                                    window.location.href = '{{ route('document.version.control') }}';
                                });
                            } else {
                                showSuccessMessage(`${uploadedCount} file(s) uploaded successfully.`);
                                window.location.href = '{{ route('document.version.control') }}';
                            }
                        } else {
                            throw new Error(data.message || 'Failed to upload files');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || 'An error occurred while uploading the files');
                    });
                }
            }

            function addDocumentToTable(doc) {
                const row = document.createElement('tr');
                row.className = 'activity-item';
                row.setAttribute('data-doc-id', doc.id);

                const uploadedDate = new Date(doc.uploaded);
                const formattedDate = uploadedDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                ${doc.type === 'PDF' ? '<i class="bx bxs-file-pdf text-red-500 text-xl"></i>' :
                                  doc.type === 'Word' ? '<i class="bx bxs-file-doc text-blue-500 text-xl"></i>' :
                                  doc.type === 'Excel' ? '<i class="bx bxs-file-xls text-green-500 text-xl"></i>' :
                                  '<i class="bx bxs-file text-gray-500 text-xl"></i>'}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${doc.name}</div>
                                <div class="text-xs text-gray-500">${doc.size}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${doc.type}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formattedDate}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${doc.status === 'Indexed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">${doc.status}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick='showDocumentDetails(${JSON.stringify(doc)})' class="text-blue-600 hover:text-blue-900 mr-3 bg-transparent border-none p-0 cursor-pointer">View</button>
                        <button onclick='showDownloadDocumentModal(${JSON.stringify(doc)})' class="text-blue-600 hover:text-blue-900 mr-3 bg-transparent border-none p-0 cursor-pointer"><i class="bx bx-download"></i></button>
                        <button onclick='showShareDocumentModal(${JSON.stringify(doc)})' class="text-gray-600 hover:text-gray-900 mr-3 bg-transparent border-none p-0 cursor-pointer"><i class="bx bx-share-alt"></i></button>
                        <button onclick="showDeleteDocumentConfirmation('${doc.id}')" class="text-red-600 hover:text-red-900 bg-transparent border-none p-0 cursor-pointer"><i class="bx bx-trash"></i></button>
                    </td>
                `;

                if (documentsTable.querySelector('tr:first-child').classList.contains('bg-gray-50')) {
                    documentsTable.insertBefore(row, documentsTable.querySelector('tr:first-child').nextSibling);
                } else {
                    documentsTable.insertBefore(row, documentsTable.firstChild);
                }

                const noDocumentsRow = documentsTable.querySelector('tr:only-child td[colspan="5"]');
                if (noDocumentsRow) {
                    noDocumentsRow.closest('tr').remove();
                }
            }

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

            window.showDocumentDetails = function(doc) {
                const contentDiv = document.getElementById('documentDetailsContent');
                const uploadedDate = new Date(doc.uploaded);
                const formattedDate = uploadedDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });

                contentDiv.innerHTML = `
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">${doc.name}</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium ${doc.status === 'Indexed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                                ${doc.status}
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
                                    <dd class="mt-1 text-sm text-gray-900">${doc.type}</dd>
                                </div>
                                <div class="mb-4">
                                    <dt class="text-sm font-medium text-gray-500">Size</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${doc.size}</dd>
                                </div>
                                <div class="mb-4">
                                    <dt class="text-sm font-medium text-gray-500">Uploaded</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${formattedDate}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('documentModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </button>
                        <button type="button" onclick="showDeleteDocumentConfirmation('${doc.id}')" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Delete Document
                        </button>
                    </div>
                `;

                openModal('documentModal');
            };

            // Download modal handlers
            let currentDownloadDoc = null;
            window.showDownloadDocumentModal = function(doc) {
                currentDownloadDoc = doc;
                document.getElementById('downloadDocName').textContent = doc.name || '—';
                document.getElementById('downloadDocType').textContent = doc.type || '—';
                document.getElementById('downloadDocSize').textContent = doc.size || '—';
                openModal('downloadDocumentModal');
            };
            window.performDownload = function() {
                if (!currentDownloadDoc || !currentDownloadDoc.id) {
                    alert('Document not available for download.');
                    return;
                }
                const baseDocUrl = "{{ url('/document') }}";
                const url = `${baseDocUrl}/${currentDownloadDoc.id}/download`;
                closeModal('downloadDocumentModal');
                window.location.href = url;
            };

            // Share modal handlers
            let currentShareDoc = null;
            window.showShareDocumentModal = function(doc) {
                currentShareDoc = doc;
                document.getElementById('shareDocName').textContent = doc.name || '—';
                const link = `${window.location.origin}/documents/${doc.id}`;
                document.getElementById('shareLink').value = link;
                document.getElementById('shareEmail').value = '';
                openModal('shareDocumentModal');
            };
            window.copyShareLink = function() {
                const input = document.getElementById('shareLink');
                input.select();
                input.setSelectionRange(0, 99999);
                try { document.execCommand('copy'); } catch (e) {}
                showSuccessMessage('Link copied to clipboard');
            };
            window.sendShareInvite = function() {
                const email = (document.getElementById('shareEmail').value || '').trim();
                if (!email) { alert('Please enter an email.'); return; }
                closeModal('shareDocumentModal');
                showSuccessMessage(`Share invite sent to ${email}`);
            };

            let currentDeleteDocumentUrl = '';
            let currentDocumentId = '';

            window.showDeleteDocumentConfirmation = function(docId) {
                currentDocumentId = docId;
                const baseDocUrl = "{{ url('/document') }}";
                currentDeleteDocumentUrl = `${baseDocUrl}/${docId}/delete`;

                const confirmBtn = document.getElementById('confirmDeleteDocumentBtn');
                confirmBtn.onclick = handleDeleteDocument;

                openModal('deleteDocumentModal');
            };

            async function handleDeleteDocument() {
                const confirmBtn = document.getElementById('confirmDeleteDocumentBtn');
                const originalText = confirmBtn.innerHTML;

                try {
                    confirmBtn.disabled = true;
                    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Deleting...';

                    const response = await fetch(currentDeleteDocumentUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });

                    const data = await response.json();

                    if (response.ok) {
                        const documentRow = document.querySelector(`tr[data-doc-id="${currentDocumentId}"]`);
                        if (documentRow) {
                            documentRow.remove();
                        }

                        closeModal('deleteDocumentModal');
                        showSuccessMessage(data.message || 'Document has been deleted successfully');
                    } else {
                        throw new Error(data.message || 'Failed to delete document');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert(error.message || 'An error occurred while deleting the document');
                } finally {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = originalText;
                }
            }
        });

        // Document List and Category Filtering
        document.addEventListener('DOMContentLoaded', function() {
            // Sample document data - in a real app, this would come from your backend
            const documents = [
                { id: 1, name: 'Q1 Financial Report.pdf', type: 'PDF', category: 'financial', uploaded: '2023-10-15', status: 'Indexed' },
                { id: 2, name: 'Employee Handbook 2023.docx', type: 'Word', category: 'hr', uploaded: '2023-10-10', status: 'Indexed' },
                { id: 3, name: 'Non-Disclosure Agreement.pdf', type: 'PDF', category: 'legal', uploaded: '2023-10-05', status: 'Pending' },
                { id: 4, name: 'SOP - Document Management.docx', type: 'Word', category: 'operations', uploaded: '2023-09-28', status: 'Indexed' },
                { id: 5, name: 'Budget Forecast 2024.xlsx', type: 'Excel', category: 'financial', uploaded: '2023-09-20', status: 'Indexed' },
                { id: 6, name: 'Employee Benefits Guide.pdf', type: 'PDF', category: 'hr', uploaded: '2023-09-15', status: 'Indexed' },
                { id: 7, name: 'Vendor Contract Template.docx', type: 'Word', category: 'legal', uploaded: '2023-09-10', status: 'Pending' },
                { id: 8, name: 'IT Security Policy.pdf', type: 'PDF', category: 'operations', uploaded: '2023-09-05', status: 'Indexed' },
                { id: 9, name: 'Annual Report 2023.pdf', type: 'PDF', category: 'financial', uploaded: '2023-08-28', status: 'Indexed' },
                { id: 10, name: 'Onboarding Checklist.xlsx', type: 'Excel', category: 'hr', uploaded: '2023-08-20', status: 'Indexed' }
            ];

            // DOM Elements
            const documentList = document.getElementById('documentList');
            const categoryCards = document.querySelectorAll('.category-card');
            const documentSearch = document.getElementById('documentSearch');
            const uploadCategory = document.getElementById('uploadCategory');
            const prevPageBtn = document.getElementById('prevPage');
            const nextPageBtn = document.getElementById('nextPage');
            const paginationStart = document.getElementById('paginationStart');
            const paginationEnd = document.getElementById('paginationEnd');
            const paginationTotal = document.getElementById('paginationTotal');
            const visibleCount = document.getElementById('visibleCount');
            const totalCount = document.getElementById('totalCount');

            // State
            let currentCategory = 'all';
            let currentSearch = '';
            let currentPage = 1;
            const itemsPerPage = 5;

            // Initialize
            renderDocuments();
            updatePagination();

            // Event Listeners
            categoryCards.forEach(card => {
                card.addEventListener('click', () => {
                    // Update active category
                    document.querySelector('.category-card.active-category')?.classList.remove('active-category');
                    card.classList.add('active-category');
                    
                    // Update current category and reset to first page
                    currentCategory = card.dataset.category;
                    currentPage = 1;
                    
                    // Update upload category select if not 'all'
                    if (currentCategory !== 'all') {
                        uploadCategory.value = currentCategory;
                    }
                    
                    // Re-render documents
                    renderDocuments();
                    updatePagination();
                });
            });

            documentSearch.addEventListener('input', (e) => {
                currentSearch = e.target.value.toLowerCase();
                currentPage = 1; // Reset to first page when searching
                renderDocuments();
                updatePagination();
            });

            prevPageBtn.addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderDocuments();
                    updatePagination();
                    window.scrollTo({ top: document.getElementById('documentList').offsetTop - 20, behavior: 'smooth' });
                }
            });

            nextPageBtn.addEventListener('click', () => {
                const totalPages = Math.ceil(getFilteredDocuments().length / itemsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    renderDocuments();
                    updatePagination();
                    window.scrollTo({ top: document.getElementById('documentList').offsetTop - 20, behavior: 'smooth' });
                }
            });

            // Helper Functions
            function getFilteredDocuments() {
                return documents.filter(doc => {
                    // Filter by category
                    const categoryMatch = currentCategory === 'all' || doc.category === currentCategory;
                    
                    // Filter by search term
                    const searchMatch = !currentSearch || 
                        doc.name.toLowerCase().includes(currentSearch) ||
                        doc.type.toLowerCase().includes(currentSearch) ||
                        doc.category.toLowerCase().includes(currentSearch) ||
                        doc.status.toLowerCase().includes(currentSearch);
                    
                    return categoryMatch && searchMatch;
                });
            }

            function renderDocuments() {
                const filteredDocs = getFilteredDocuments();
                const startIndex = (currentPage - 1) * itemsPerPage;
                const paginatedDocs = filteredDocs.slice(startIndex, startIndex + itemsPerPage);

                // Clear current list
                documentList.innerHTML = '';

                if (paginatedDocs.length === 0) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No documents found matching your criteria.
                        </td>
                    `;
                    documentList.appendChild(row);
                    return;
                }

                // Add documents to the list
                paginatedDocs.forEach(doc => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50';
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-md ${
                                    doc.status === 'Indexed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
                                }">
                                    <i class="bx ${getFileIcon(doc.type)} text-lg"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${doc.name}</div>
                                    <div class="text-xs text-gray-500">${formatFileSize(1024 * (doc.id * 100))}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                ${doc.type}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full ${
                                getCategoryClass(doc.category)
                            }">
                                ${doc.category.charAt(0).toUpperCase() + doc.category.slice(1)}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${formatDate(doc.uploaded)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                doc.status === 'Indexed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                            }">
                                ${doc.status}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="showDocumentDetails(${doc.id})" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="bx bx-show"></i>
                            </button>
                            <a href="#" class="text-gray-600 hover:text-gray-900 mr-3">
                                <i class="bx bx-download"></i>
                            </a>
                            <button onclick="showShareDocumentModal(${doc.id})" class="text-gray-600 hover:text-gray-900">
                                <i class="bx bx-share-alt"></i>
                            </button>
                        </td>
                    `;
                    documentList.appendChild(row);
                });

                // Update counts
                visibleCount.textContent = filteredDocs.length;
                totalCount.textContent = documents.length;
            }

            function updatePagination() {
                const filteredDocs = getFilteredDocuments();
                const totalPages = Math.ceil(filteredDocs.length / itemsPerPage);
                const startItem = filteredDocs.length > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0;
                const endItem = Math.min(startItem + itemsPerPage - 1, filteredDocs.length);

                paginationStart.textContent = startItem;
                paginationEnd.textContent = endItem;
                paginationTotal.textContent = filteredDocs.length;

                // Update button states
                prevPageBtn.disabled = currentPage === 1;
                nextPageBtn.disabled = currentPage >= totalPages;
            }

            function getFileIcon(fileType) {
                switch(fileType.toLowerCase()) {
                    case 'pdf': return 'bxs-file-pdf';
                    case 'word': 
                    case 'docx': 
                    case 'doc': return 'bxs-file-doc';
                    case 'excel': 
                    case 'xlsx': 
                    case 'xls': return 'bxs-file-xls';
                    case 'powerpoint': 
                    case 'pptx': 
                    case 'ppt': return 'bxs-file-ppt';
                    default: return 'bxs-file';
                }
            }

            function getCategoryClass(category) {
                switch(category) {
                    case 'financial': return 'bg-green-100 text-green-800';
                    case 'hr': return 'bg-blue-100 text-blue-800';
                    case 'legal': return 'bg-yellow-100 text-yellow-800';
                    case 'operations': return 'bg-purple-100 text-purple-800';
                    default: return 'bg-gray-100 text-gray-800';
                }
            }

            function formatDate(dateString) {
                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                return new Date(dateString).toLocaleDateString('en-US', options);
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Make functions available globally for the inline event handlers
            window.showDocumentDetails = function(id) {
                const doc = documents.find(d => d.id === id);
                if (doc) {
                    const modal = document.getElementById('documentModal');
                    const title = document.getElementById('document-modal-title');
                    const content = document.getElementById('documentDetailsContent');
                    
                    title.textContent = doc.name;
                    content.innerHTML = `
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12 rounded-lg ${
                                    doc.status === 'Indexed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
                                } flex items-center justify-center mr-4">
                                    <i class="bx ${getFileIcon(doc.type)} text-2xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900">${doc.name}</h4>
                                    <p class="text-sm text-gray-500">${doc.type} • ${formatFileSize(1024 * (doc.id * 100))}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Category</p>
                                    <p class="mt-1 text-sm text-gray-900">${doc.category.charAt(0).toUpperCase() + doc.category.slice(1)}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</p>
                                    <p class="mt-1 text-sm text-gray-900">${formatDate(doc.uploaded)}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</p>
                                    <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                        doc.status === 'Indexed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                                    }">
                                        ${doc.status}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Size</p>
                                    <p class="mt-1 text-sm text-gray-900">${formatFileSize(1024 * (doc.id * 100))}</p>
                                </div>
                            </div>
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="flex space-x-3">
                                    <a href="#" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="bx bx-download mr-2"></i>
                                        Download
                                    </a>
                                    <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="bx bx-edit mr-2"></i>
                                        Edit Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            };

            window.showShareDocumentModal = function(id) {
                const doc = documents.find(d => d.id === id);
                if (doc) {
                    const modal = document.getElementById('shareDocumentModal');
                    const title = document.getElementById('share-document-modal-title');
                    const shareLink = document.getElementById('shareLink');
                    
                    title.textContent = `Share ${doc.name}`;
                    shareLink.value = `${window.location.origin}/documents/${doc.id}`;
                    
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            };
        });
    </script>
</body>
</html>