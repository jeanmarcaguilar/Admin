@php
// Get the authenticated user
$user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitors Registration | Administrative Dashboard</title>
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

        .status-expected {
            background-color: #dbeafe;
            color: #1e40af;
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
                <h1 class="text-2xl font-bold tracking-tight">Visitors Registration</h1>
            </div>

    <!-- Check In Modal (outside main content) -->
    <div id="checkInModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="check-in-modal-title">
      <div class="bg-white rounded-lg shadow-lg w-[420px] max-w-full mx-4">
        <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
          <h3 id="check-in-modal-title" class="font-semibold text-sm text-gray-900">Check In Visitor</h3>
          <button type="button" id="closeCheckIn" class="text-gray-400 hover:text-gray-600 p-2"><i class="fas fa-times text-xs"></i></button>
        </div>
        <div class="px-6 py-4 text-sm text-gray-800">
          Are you sure you want to check in visitor <span id="ciText" class="font-semibold"></span> now?
          <input type="hidden" id="ciId" />
        </div>
        <div class="px-6 pb-4 text-right">
          <button type="button" id="cancelCheckIn" class="mr-2 text-sm px-4 py-2 rounded border">Cancel</button>
          <button type="button" id="confirmCheckIn" class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded px-4 py-2">Check In</button>
        </div>
      </div>
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
            <span class="text-xs text-gray-400"></span>
        </div>
        <ul class="max-h-72 overflow-y-auto">
            <li class="px-4 py-3 text-sm text-gray-500 text-center">No notifications yet</li>
        </ul>
        <div class="text-center py-2 border-t border-gray-200">
            <a class="text-[#28644c] text-xs font-semibold hover:underline" href="#">View all notifications</a>
        </div>
    </div>
    <!-- View Visitor Modal (moved outside main content) -->
    <div id="viewVisitorModal" class="modal hidden" aria-modal="true" role="dialog">
      <div class="bg-white rounded-lg shadow-lg w-[420px] max-w-full mx-4">
        <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
          <h3 class="font-semibold text-sm text-gray-900">View Visitor</h3>
          <button type="button" id="closeViewVisitor" class="text-gray-400 hover:text-gray-600 p-2"><i class="fas fa-times text-xs"></i></button>
        </div>
        <div class="px-6 py-4 text-sm text-gray-800 space-y-2">
          <div><span class="font-semibold">ID:</span> <span id="vvId"></span></div>
          <div><span class="font-semibold">Name:</span> <span id="vvName"></span></div>
          <div><span class="font-semibold">Company:</span> <span id="vvCompany"></span></div>
          <div><span class="font-semibold">Type:</span> <span id="vvType"></span></div>
          <div><span class="font-semibold">Host:</span> <span id="vvHost"></span></div>
          <div><span class="font-semibold">Department:</span> <span id="vvDept"></span></div>
          <div><span class="font-semibold">Date:</span> <span id="vvDate"></span></div>
          <div><span class="font-semibold">Time:</span> <span id="vvTime"></span></div>
          <div><span class="font-semibold">Status:</span> <span id="vvStatus"></span></div>
        </div>
        <div class="px-6 pb-4 text-right">
          <button type="button" class="bg-[#28644c] hover:bg-[#2f855A] text-white text-sm font-semibold rounded-lg px-4 py-2" id="closeViewVisitor2">Close</button>
        </div>
      </div>
    </div>

    <!-- Edit Visitor Modal (moved outside main content) -->
    <div id="editVisitorModal" class="modal hidden" aria-modal="true" role="dialog">
      <div class="bg-white rounded-lg shadow-lg w-[460px] max-w-full mx-4">
        <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
          <h3 class="font-semibold text-sm text-gray-900">Edit Visitor</h3>
          <button type="button" id="closeEditVisitor" class="text-gray-400 hover:text-gray-600 p-2"><i class="fas fa-times text-xs"></i></button>
        </div>
        <form id="editVisitorForm" class="px-6 py-4 space-y-3">
          <input type="hidden" id="evId" />
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Company</label>
            <input type="text" id="evCompany" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Type</label>
              <select id="evType" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="">—</option>
                <option value="client">Client</option>
                <option value="vendor">Vendor</option>
                <option value="contractor">Contractor</option>
                <option value="guest">Guest</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Purpose</label>
              <select id="evPurpose" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="">—</option>
                <option value="meeting">Meeting</option>
                <option value="delivery">Delivery</option>
                <option value="interview">Interview</option>
                <option value="maintenance">Maintenance</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Date</label>
              <input type="date" id="evDate" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Time</label>
              <input type="time" id="evTime" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
            </div>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
            <select id="evStatus" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
              <option value="scheduled">Scheduled</option>
              <option value="checked_in">Checked In</option>
              <option value="checked_out">Checked Out</option>
            </select>
          </div>
          <div class="pt-2 text-right">
            <button type="button" id="cancelEditVisitor" class="mr-2 text-sm px-4 py-2 rounded border">Cancel</button>
            <button type="submit" class="bg-[#28644c] hover:bg-[#2f855A] text-white text-sm font-semibold rounded px-4 py-2">Save</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Visitor Modal (moved outside main content) -->
    <div id="deleteVisitorModal" class="modal hidden" aria-modal="true" role="dialog">
      <div class="bg-white rounded-lg shadow-lg w-[420px] max-w-full mx-4">
        <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
          <h3 class="font-semibold text-sm text-gray-900">Delete Visitor</h3>
          <button type="button" id="closeDeleteVisitor" class="text-gray-400 hover:text-gray-600 p-2"><i class="fas fa-times text-xs"></i></button>
        </div>
        <div class="px-6 py-4 text-sm text-gray-800">
          Are you sure you want to delete visitor <span id="dvText" class="font-semibold"></span>?
          <input type="hidden" id="dvId" />
        </div>
        <div class="px-6 pb-4 text-right">
          <button type="button" id="cancelDeleteVisitor" class="mr-2 text-sm px-4 py-2 rounded border">Cancel</button>
          <button type="button" id="confirmDeleteVisitor" class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded px-4 py-2">Delete</button>
        </div>
      </div>
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
                            <div class="text-[9px] text-gray-500">Manila, Philippines â€¢ Chrome</div>
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
                        <ul class="dropdown-menu bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
                            <li><a href="{{ route('visitors.registration') }}" class="block px-3 py-2 text-sm bg-white/30 rounded-lg"><i class="bx bx-id-card mr-2"></i>Visitors Registration</a></li>
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
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-[#1a4d38]">Visitor Management</h1>
                            <p class="text-gray-600 text-sm">Register, track, and manage visitor activity</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button id="addVisitorBtn" class="inline-flex items-center bg-[#28644c] hover:bg-[#2f855A] text-white text-sm font-semibold rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]">
                                <i class="fas fa-user-plus mr-2"></i> Register Visitor
                            </button>
                        </div>
                    </div>


                    <!-- Search -->
                    <section class="bg-gradient-to-br from-white to-gray-50 rounded-lg p-6 shadow-sm border border-gray-100">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="relative flex-1 max-w-3xl">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="searchVisitors" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] text-sm" placeholder="Search visitors, companies, hosts...">
                            </div>
                        </div>
                    </section>

                    <!-- Stats Cards (Session-backed) -->
                    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="dashboard-card bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Today</p>
                                    <h3 class="text-2xl font-extrabold text-gray-900">{{ $stats['total_today'] ?? 0 }}</h3>
                                </div>
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                    <i class="fas fa-calendar-day text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-card bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Checked In</p>
                                    <h3 class="text-2xl font-extrabold text-gray-900">{{ $stats['checked_in'] ?? 0 }}</h3>
                                </div>
                                <div class="p-3 rounded-full bg-green-100 text-green-600">
                                    <i class="fas fa-user-check text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-card bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Scheduled Today</p>
                                    <h3 class="text-2xl font-extrabold text-gray-900">{{ $stats['scheduled_today'] ?? 0 }}</h3>
                                </div>
                                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                    <i class="fas fa-clock text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-card bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Checked Out</p>
                                    <h3 class="text-2xl font-extrabold text-gray-900">{{ $stats['checked_out'] ?? 0 }}</h3>
                                </div>
                                <div class="p-3 rounded-full bg-gray-100 text-gray-700">
                                    <i class="fas fa-door-open text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Content Grid -->
                    <section class="grid grid-cols-1 gap-6">
                        <!-- Table -->
                        <div class="bg-gradient-to-br from-white to-gray-50 rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                                <div class="flex items-center gap-4">
                                    <button class="text-sm font-semibold text-[#28644c] px-3 py-1.5 rounded hover:bg-green-50">Today</button>
                                    <button class="text-sm text-gray-600 px-3 py-1.5 rounded hover:bg-gray-50">Scheduled</button>
                                    <button class="text-sm text-gray-600 px-3 py-1.5 rounded hover:bg-gray-50">All</button>
                                </div>
                                <div id="resultsCount" class="text-xs text-gray-500">0 results</div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visitor</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Host</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="visitorsTbody" class="bg-white divide-y divide-gray-200">
                                        @forelse(($visitors ?? []) as $v)
                                            <tr class="table-row">
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
                                                    <span class="status-badge {{ $st === 'checked_in' ? 'status-checked-in' : ($st==='checked_out' ? 'status-checked-out' : 'status-expected') }}">{{ ucfirst($st) }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="#" class="visitorViewBtn text-[#2f855A] hover:text-[#1a4d38] mr-3" data-id="{{ $v['id'] ?? '' }}" data-tooltip="View"><i class="fas fa-eye"></i></a>
                                                    <a href="#" class="visitorEditBtn text-blue-600 hover:text-blue-900 mr-3" data-id="{{ $v['id'] ?? '' }}" data-tooltip="Edit"><i class="fas fa-edit"></i></a>
                                                    @if($st !== 'checked_out' && $st !== 'checked_in')
                                                        <a href="#" class="visitorCheckInBtn text-green-600 hover:text-green-800 mr-3" data-id="{{ $v['id'] ?? '' }}" data-tooltip="Check In"><i class="fas fa-sign-in-alt"></i></a>
                                                    @endif
                                                    <a href="#" class="visitorDeleteBtn text-red-600 hover:text-red-900" data-id="{{ $v['id'] ?? '' }}" data-tooltip="Delete"><i class="fas fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                                                    No visitors registered yet.
                                                    <div class="mt-3">
                                                        <button type="button" onclick="document.getElementById('addVisitorBtn').click()" class="inline-flex items-center bg-[#28644c] hover:bg-[#2f855A] text-white text-xs font-semibold rounded-lg px-3 py-2 shadow-sm">
                                                            <i class="fas fa-user-plus mr-2"></i> Register your first visitor
                                                        </button>
                                                    </div>
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

    <!-- Add Visitor Modal (moved outside main content) -->
    <div id="addVisitorModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="add-visitor-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-[480px] max-w-full mx-4 max-h-[90vh] overflow-y-auto" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                <h3 id="add-visitor-modal-title" class="font-semibold text-sm text-gray-900 select-none">Register New Visitor</h3>
                <button id="closeAddVisitorModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div class="px-8 pt-6 pb-8">
                <form id="addVisitorForm" class="space-y-4 text-xs text-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <h4 class="text-sm font-semibold text-gray-900 mb-2">Visitor Information</h4>
                        </div>
                        <div>
                            <label for="firstName" class="block mb-1 font-semibold">First Name *</label>
                            <input type="text" id="firstName" name="firstName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                        </div>
                        <div>
                            <label for="lastName" class="block mb-1 font-semibold">Last Name *</label>
                            <input type="text" id="lastName" name="lastName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                        </div>
                        <div class="col-span-2">
                            <label for="email" class="block mb-1 font-semibold">Email Address</label>
                            <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]">
                        </div>
                        <div>
                            <label for="phone" class="block mb-1 font-semibold">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                        </div>
                        <div>
                            <label for="company" class="block mb-1 font-semibold">Company *</label>
                            <input type="text" id="company" name="company" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                        </div>
                        <div class="col-span-2">
                            <label for="visitorType" class="block mb-1 font-semibold">Visitor Type *</label>
                            <select id="visitorType" name="visitorType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                <option value="">Select visitor type</option>
                                <option value="client">Client</option>
                                <option value="vendor">Vendor</option>
                                <option value="contractor">Contractor</option>
                                <option value="guest">Guest</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-span-2 border-t border-gray-200 pt-4">
                            <h4 class="text-sm font-semibold text-gray-900 mb-2">Visit Details</h4>
                        </div>
                        <div>
                            <label for="hostName" class="block mb-1 font-semibold">Host Name *</label>
                            <input type="text" id="hostName" name="hostName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" placeholder="e.g., Sarah Johnson" required>
                        </div>
                        <div>
                            <label for="hostDepartment" class="block mb-1 font-semibold">Host Department</label>
                            <input type="text" id="hostDepartment" name="hostDepartment" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" placeholder="e.g., Procurement">
                        </div>
                        <div>
                            <label for="purpose" class="block mb-1 font-semibold">Purpose of Visit *</label>
                            <select id="purpose" name="purpose" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                <option value="">Select purpose</option>
                                <option value="meeting">Meeting</option>
                                <option value="delivery">Delivery</option>
                                <option value="interview">Interview</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label for="checkInDate" class="block mb-1 font-semibold">Check-In Date *</label>
                            <input type="date" id="checkInDate" name="checkInDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                        </div>
                        <div>
                            <label for="checkInTime" class="block mb-1 font-semibold">Check-In Time *</label>
                            <input type="time" id="checkInTime" name="checkInTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                        </div>
                        <div class="col-span-2">
                            <label for="notes" class="block mb-1 font-semibold">Additional Notes</label>
                            <textarea id="notes" name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]"></textarea>
                        </div>
                        <div class="col-span-2">
                            <label class="flex items-center space-x-2">
                                <input id="sendEmailNotification" name="sendEmailNotification" type="checkbox" checked class="w-3.5 h-3.5 text-[#2f855A] focus:ring-[#2f855A] border-gray-300 rounded" />
                                <span>Send email notification to host</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 pt-2">
                        <button type="button" id="cancelAddVisitor" class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
                        <button type="submit" class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200">Register Visitor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
            <!-- View Visitor Modal -->
            <div id="viewVisitorModal" class="modal hidden" aria-modal="true" role="dialog">
              <div class="bg-white rounded-lg shadow-lg w-[420px] max-w-full mx-4">
                <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                  <h3 class="font-semibold text-sm text-gray-900">View Visitor</h3>
                  <button type="button" id="closeViewVisitor" class="text-gray-400 hover:text-gray-600 p-2"><i class="fas fa-times text-xs"></i></button>
                </div>
                <div class="px-6 py-4 text-sm text-gray-800 space-y-2">
                  <div><span class="font-semibold">ID:</span> <span id="vvId"></span></div>
                  <div><span class="font-semibold">Name:</span> <span id="vvName"></span></div>
                  <div><span class="font-semibold">Company:</span> <span id="vvCompany"></span></div>
                  <div><span class="font-semibold">Type:</span> <span id="vvType"></span></div>
                  <div><span class="font-semibold">Host:</span> <span id="vvHost"></span></div>
                  <div><span class="font-semibold">Department:</span> <span id="vvDept"></span></div>
                  <div><span class="font-semibold">Date:</span> <span id="vvDate"></span></div>
                  <div><span class="font-semibold">Time:</span> <span id="vvTime"></span></div>
                  <div><span class="font-semibold">Status:</span> <span id="vvStatus"></span></div>
                </div>
                <div class="px-6 pb-4 text-right">
                  <button type="button" class="bg-[#28644c] hover:bg-[#2f855A] text-white text-sm font-semibold rounded-lg px-4 py-2" id="closeViewVisitor2">Close</button>
                </div>
              </div>
            </div>

            <!-- Edit Visitor Modal -->
            <div id="editVisitorModal" class="modal hidden" aria-modal="true" role="dialog">
              <div class="bg-white rounded-lg shadow-lg w-[460px] max-w-full mx-4">
                <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                  <h3 class="font-semibold text-sm text-gray-900">Edit Visitor</h3>
                  <button type="button" id="closeEditVisitor" class="text-gray-400 hover:text-gray-600 p-2"><i class="fas fa-times text-xs"></i></button>
                </div>
                <form id="editVisitorForm" class="px-6 py-4 space-y-3">
                  <input type="hidden" id="evId" />
                  <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Company</label>
                    <input type="text" id="evCompany" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                  </div>
                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-semibold text-gray-700 mb-1">Type</label>
                      <select id="evType" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                        <option value="">—</option>
                        <option value="client">Client</option>
                        <option value="vendor">Vendor</option>
                        <option value="contractor">Contractor</option>
                        <option value="guest">Guest</option>
                        <option value="other">Other</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-xs font-semibold text-gray-700 mb-1">Purpose</label>
                      <select id="evPurpose" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                        <option value="">—</option>
                        <option value="meeting">Meeting</option>
                        <option value="delivery">Delivery</option>
                        <option value="interview">Interview</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="other">Other</option>
                      </select>
                    </div>
                  </div>
                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-semibold text-gray-700 mb-1">Date</label>
                      <input type="date" id="evDate" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                    </div>
                    <div>
                      <label class="block text-xs font-semibold text-gray-700 mb-1">Time</label>
                      <input type="time" id="evTime" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                    </div>
                  </div>
                  <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                    <select id="evStatus" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                      <option value="scheduled">Scheduled</option>
                      <option value="checked_in">Checked In</option>
                      <option value="checked_out">Checked Out</option>
                    </select>
                  </div>
                  <div class="pt-2 text-right">
                    <button type="button" id="cancelEditVisitor" class="mr-2 text-sm px-4 py-2 rounded border">Cancel</button>
                    <button type="submit" class="bg-[#28644c] hover:bg-[#2f855A] text-white text-sm font-semibold rounded px-4 py-2">Save</button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Delete Visitor Modal -->
            <div id="deleteVisitorModal" class="modal hidden" aria-modal="true" role="dialog">
              <div class="bg-white rounded-lg shadow-lg w-[420px] max-w-full mx-4">
                <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                  <h3 class="font-semibold text-sm text-gray-900">Delete Visitor</h3>
                  <button type="button" id="closeDeleteVisitor" class="text-gray-400 hover:text-gray-600 p-2"><i class="fas fa-times text-xs"></i></button>
                </div>
                <div class="px-6 py-4 text-sm text-gray-800">
                  Are you sure you want to delete visitor <span id="dvText" class="font-semibold"></span>?
                  <input type="hidden" id="dvId" />
                </div>
                <div class="px-6 pb-4 text-right">
                  <button type="button" id="cancelDeleteVisitor" class="mr-2 text-sm px-4 py-2 rounded border">Cancel</button>
                  <button type="button" id="confirmDeleteVisitor" class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded px-4 py-2">Delete</button>
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
                    <li><a href="{{ route('logout') }}" class="w-full text-left flex items-center px-6 py-2 text-red-600 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1" onclick="event.preventDefault(); document.getElementById('signOutModal').classList.add('active');"><i class="fas fa-sign-out-alt mr-2"></i> Sign Out</a></li>
                </ul>
            </div>
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
                                    <div class="text-[9px] text-gray-500">Manila, Philippines â€¢ Chrome</div>
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

            <!-- Add Visitor Modal -->
            <div id="addVisitorModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="add-visitor-modal-title">
                <div class="bg-white rounded-lg shadow-lg w-[480px] max-w-full mx-4 max-h-[90vh] overflow-y-auto" role="document">
                    <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                        <h3 id="add-visitor-modal-title" class="font-semibold text-sm text-gray-900 select-none">Register New Visitor</h3>
                        <button id="closeAddVisitorModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                    <div class="px-8 pt-6 pb-8">
                        <form id="addVisitorForm" class="space-y-4 text-xs text-gray-700">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Visitor Information</h4>
                                </div>
                                <div>
                                    <label for="firstName" class="block mb-1 font-semibold">First Name *</label>
                                    <input type="text" id="firstName" name="firstName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                </div>
                                <div>
                                    <label for="lastName" class="block mb-1 font-semibold">Last Name *</label>
                                    <input type="text" id="lastName" name="lastName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                </div>
                                <div class="col-span-2">
                                    <label for="email" class="block mb-1 font-semibold">Email Address</label>
                                    <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]">
                                </div>
                                <div>
                                    <label for="phone" class="block mb-1 font-semibold">Phone Number *</label>
                                    <input type="tel" id="phone" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                </div>
                                <div>
                                    <label for="company" class="block mb-1 font-semibold">Company *</label>
                                    <input type="text" id="company" name="company" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                </div>
                                <div class="col-span-2">
                                    <label for="visitorType" class="block mb-1 font-semibold">Visitor Type *</label>
                                    <select id="visitorType" name="visitorType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                        <option value="">Select visitor type</option>
                                        <option value="client">Client</option>
                                        <option value="vendor">Vendor</option>
                                        <option value="contractor">Contractor</option>
                                        <option value="guest">Guest</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-span-2 border-t border-gray-200 pt-4">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Visit Details</h4>
                                </div>
                                <div>
                                    <label for="hostId" class="block mb-1 font-semibold">Host *</label>
                                    <select id="hostId" name="hostId" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                        <option value="">Select a host</option>
                                        <option value="1">Sarah Johnson (Procurement)</option>
                                        <option value="2">Michael Brown (Sales)</option>
                                        <option value="3">Jennifer Lee (Business Development)</option>
                                        <option value="4">Robert Chen (IT)</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="purpose" class="block mb-1 font-semibold">Purpose of Visit *</label>
                                    <select id="purpose" name="purpose" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                        <option value="">Select purpose</option>
                                        <option value="meeting">Meeting</option>
                                        <option value="delivery">Delivery</option>
                                        <option value="interview">Interview</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="checkInDate" class="block mb-1 font-semibold">Check-In Date *</label>
                                    <input type="date" id="checkInDate" name="checkInDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                </div>
                                <div>
                                    <label for="checkInTime" class="block mb-1 font-semibold">Check-In Time *</label>
                                    <input type="time" id="checkInTime" name="checkInTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                </div>
                                <div class="col-span-2">
                                    <label for="notes" class="block mb-1 font-semibold">Additional Notes</label>
                                    <textarea id="notes" name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]"></textarea>
                                </div>
                                <div class="col-span-2">
                                    <label class="flex items-center space-x-2">
                                        <input id="sendEmailNotification" name="sendEmailNotification" type="checkbox" checked class="w-3.5 h-3.5 text-[#2f855A] focus:ring-[#2f855A] border-gray-300 rounded" />
                                        <span>Send email notification to host</span>
                                    </label>
                                </div>
                            </div>
                            <div class="flex justify-end space-x-3 pt-2">
                                <button type="button" id="cancelAddVisitor" class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
                                <button type="submit" class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200">Register Visitor</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
document.addEventListener("DOMContentLoaded", () => {
    // Sidebar and overlay elements
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("main-content");
    const toggleBtn = document.getElementById("toggle-btn");
    const overlay = document.getElementById("overlay");

    // Dropdown elements
    const dropdownToggles = document.querySelectorAll(".has-dropdown > div");

    // Notification and user menu elements
    const notificationBtn = document.getElementById("notificationBtn");
    const notificationDropdown = document.getElementById("notificationDropdown");
    const userMenuBtn = document.getElementById("userMenuBtn");
    const userMenuDropdown = document.getElementById("userMenuDropdown");

    // Modal elements
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
    const openSignOutBtn = document.getElementById("openSignOutBtn");
    const cancelSignOutBtn = document.getElementById("cancelSignOutBtn");
    const cancelSignOutBtn2 = document.getElementById("cancelSignOutBtn2");
    const addVisitorBtn = document.getElementById("addVisitorBtn");
    const addVisitorModal = document.getElementById("addVisitorModal");
    const closeAddVisitorModal = document.getElementById("closeAddVisitorModal");
    const cancelAddVisitor = document.getElementById("cancelAddVisitor");
    const checkInModal = document.getElementById("checkInModal");
    const closeCheckIn = document.getElementById("closeCheckIn");
    const cancelCheckIn = document.getElementById("cancelCheckIn");
    const confirmCheckIn = document.getElementById("confirmCheckIn");
    const viewVisitorModal = document.getElementById("viewVisitorModal");
    const editVisitorModal = document.getElementById("editVisitorModal");
    const deleteVisitorModal = document.getElementById("deleteVisitorModal");
    const addVisitorForm = document.getElementById("addVisitorForm");
    const csrf = '{{ csrf_token() }}';
    const searchVisitors = document.getElementById('searchVisitors');
    const resultsCount = document.getElementById('resultsCount');

    // Initialize sidebar state based on screen size
    if (window.innerWidth >= 768) {
        sidebar.classList.remove("-ml-72");
        mainContent.classList.add("md:ml-72", "sidebar-open");
        mainContent.classList.remove("sidebar-closed");
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

    // Close all dropdowns
    function closeAllDropdowns() {
        dropdownToggles.forEach((toggle) => {
            const dropdown = toggle.nextElementSibling;
            const chevron = toggle.querySelector(".bx-chevron-down");
            dropdown.classList.add("hidden");
            chevron.classList.remove("rotate-180");
        });
    }

    // Toggle dropdown
    dropdownToggles.forEach((toggle) => {
        toggle.addEventListener("click", (e) => {
            e.stopPropagation();
            const dropdown = toggle.nextElementSibling;
            const chevron = toggle.querySelector(".bx-chevron-down");
            const isOpen = !dropdown.classList.contains("hidden");

            // Close all other dropdowns
            closeAllDropdowns();

            // Toggle the clicked dropdown
            if (!isOpen) {
                dropdown.classList.remove("hidden");
                chevron.classList.add("rotate-180");
            }
        });
    });

    // Client-side search filtering for visitors table
    function updateResultsCount() {
        const tbody = document.getElementById('visitorsTbody');
        if (!tbody || !resultsCount) return;
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const visitorRows = rows.filter(r => !r.querySelector('td[colspan]'));
        const visible = visitorRows.filter(r => !r.classList.contains('hidden')).length;
        resultsCount.textContent = visible + (visible === 1 ? ' result' : ' results');
    }

    function applySearchFilter(query) {
        const q = (query || '').toString().toLowerCase().trim();
        const tbody = document.getElementById('visitorsTbody');
        if (!tbody) return;
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.forEach(tr => {
            const emptyCell = tr.querySelector('td[colspan]');
            if (emptyCell) { tr.classList.toggle('hidden', q.length > 0); return; }
            const name = tr.querySelector('td:nth-child(1) .text-sm')?.textContent || '';
            const id = tr.querySelector('td:nth-child(1) .text-xs')?.textContent || '';
            const company = tr.querySelector('td:nth-child(2) .text-sm')?.textContent || '';
            const type = tr.querySelector('td:nth-child(2) .text-xs')?.textContent || '';
            const host = tr.querySelector('td:nth-child(3) .text-sm')?.textContent || '';
            const dept = tr.querySelector('td:nth-child(3) .text-xs')?.textContent || '';
            const hay = (name + ' ' + id + ' ' + company + ' ' + type + ' ' + host + ' ' + dept).toLowerCase();
            const match = q === '' ? true : hay.includes(q);
            tr.classList.toggle('hidden', !match);
        });
        updateResultsCount();
    }

    if (searchVisitors) {
        searchVisitors.addEventListener('input', (e) => applySearchFilter(e.target.value));
        // Initialize count on load
        applySearchFilter('');
    }

    // Close dropdowns when clicking outside
    document.addEventListener("click", (e) => {
        if (!e.target.closest(".has-dropdown")) {
            closeAllDropdowns();
        }
    });

    // Sidebar toggle
    toggleBtn.addEventListener("click", toggleSidebar);

    // Close sidebar on overlay click
    overlay.addEventListener("click", () => {
        sidebar.classList.add("-ml-72");
        overlay.classList.add("hidden");
        document.body.style.overflow = "";
        mainContent.classList.remove("sidebar-open");
        mainContent.classList.add("sidebar-closed");
        closeAllDropdowns();
    });

    // Notification dropdown
    notificationBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        notificationDropdown.classList.toggle("hidden");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
        closeAllModals();
    });

    // User menu dropdown
    userMenuBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        userMenuDropdown.classList.toggle("hidden");
        const expanded = userMenuBtn.getAttribute("aria-expanded") === "true";
        userMenuBtn.setAttribute("aria-expanded", !expanded);
        notificationDropdown.classList.add("hidden");
        closeAllModals();
    });

    // Modal handling
    function clearAddVisitorValidation(form) {
        if (!form) return;
        const fields = [form.firstName, form.lastName, form.phone, form.company, form.visitorType, form.purpose, form.checkInDate, form.checkInTime, (form.hostId || form.hostName), form.email];
        fields.forEach(el => {
            if (!el) return;
            el.classList.remove('border-red-500');
            el.classList.add('border-gray-300');
        });
    }
    function closeAllModals() {
        profileModal.classList.remove("active");
        accountSettingsModal.classList.remove("active");
        privacySecurityModal.classList.remove("active");
        signOutModal.classList.remove("active");
        addVisitorModal.classList.remove("active");
        checkInModal.classList.remove("active");
        viewVisitorModal.classList.remove("active");
        editVisitorModal.classList.remove("active");
        deleteVisitorModal.classList.remove("active");
        document.body.classList.remove("overflow-hidden");
    }

    // Profile modal
    openProfileBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        closeAllModals();
        profileModal.classList.add("active");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
    });

    closeProfileBtn.addEventListener("click", closeAllModals);
    closeProfileBtn2.addEventListener("click", closeAllModals);

    // Account settings modal
    openAccountSettingsBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        closeAllModals();
        accountSettingsModal.classList.add("active");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
    });

    closeAccountSettingsBtn.addEventListener("click", closeAllModals);
    cancelAccountSettingsBtn.addEventListener("click", closeAllModals);

    // Privacy & security modal
    openPrivacySecurityBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        closeAllModals();
        privacySecurityModal.classList.add("active");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
    });

    closePrivacySecurityBtn.addEventListener("click", closeAllModals);
    cancelPrivacySecurityBtn.addEventListener("click", closeAllModals);

    // Sign out modal
    openSignOutBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        closeAllModals();
        signOutModal.classList.add("active");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
    });

    cancelSignOutBtn.addEventListener("click", closeAllModals);
    cancelSignOutBtn2.addEventListener("click", closeAllModals);

    // Add visitor modal
    addVisitorBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        closeAllModals();
        addVisitorModal.classList.add("active");
        document.body.classList.add("overflow-hidden");
        const form = addVisitorModal.querySelector('#addVisitorForm');
        if (form) { form.reset(); clearAddVisitorValidation(form); }
    });

    closeAddVisitorModal.addEventListener("click", () => { const f = addVisitorModal.querySelector('#addVisitorForm'); if (f) { f.reset(); clearAddVisitorValidation(f); } closeAllModals(); });
    cancelAddVisitor.addEventListener("click", () => { const f = addVisitorModal.querySelector('#addVisitorForm'); if (f) { f.reset(); clearAddVisitorValidation(f); } closeAllModals(); });

    // Close modals and dropdowns on outside click
    window.addEventListener("click", (e) => {
        if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
            notificationDropdown.classList.add("hidden");
        }
        if (!userMenuBtn.contains(e.target) && !userMenuDropdown.contains(e.target)) {
            userMenuDropdown.classList.add("hidden");
            userMenuBtn.setAttribute("aria-expanded", "false");
        }
        if (
            !profileModal.contains(e.target) &&
            !openProfileBtn.contains(e.target) &&
            !accountSettingsModal.contains(e.target) &&
            !openAccountSettingsBtn.contains(e.target) &&
            !privacySecurityModal.contains(e.target) &&
            !openPrivacySecurityBtn.contains(e.target) &&
            !signOutModal.contains(e.target) &&
            !openSignOutBtn.contains(e.target) &&
            !addVisitorModal.contains(e.target) &&
            !addVisitorBtn.contains(e.target) &&
            !checkInModal.contains(e.target) &&
            !e.target.closest(".visitorCheckInBtn") &&
            !viewVisitorModal.contains(e.target) &&
            !e.target.closest(".visitorViewBtn") &&
            !editVisitorModal.contains(e.target) &&
            !e.target.closest(".visitorEditBtn") &&
            !deleteVisitorModal.contains(e.target) &&
            !e.target.closest(".visitorDeleteBtn")
        ) {
            closeAllModals();
            closeAllDropdowns();
        }
    });

    // Stop propagation inside modals
    [profileModal, accountSettingsModal, privacySecurityModal, signOutModal, addVisitorModal, checkInModal, viewVisitorModal, editVisitorModal, deleteVisitorModal].forEach((modal) => {
        modal.querySelector("div")?.addEventListener("click", (e) => e.stopPropagation());
    });

    // Visitor modals logic
    function openModal(modal) {
        closeAllModals();
        modal.classList.add("active");
        modal.classList.remove("hidden");
        document.body.classList.add("overflow-hidden");
    }

    function closeModal(modal) {
        modal.classList.remove("active");
        modal.classList.add("hidden");
        document.body.classList.remove("overflow-hidden");
    }

    // Check In modal actions
    closeCheckIn?.addEventListener("click", () => closeModal(checkInModal));
    cancelCheckIn?.addEventListener("click", () => closeModal(checkInModal));
    confirmCheckIn?.addEventListener("click", async () => {
        const id = document.getElementById("ciId").value;
        const now = new Date();
        const pad = (n) => n.toString().padStart(2, "0");
        const date = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;
        const time = `${pad(now.getHours())}:${pad(now.getMinutes())}`;
        try {
            const res = await fetch(`{{ route('visitor.update') }}`, {
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": csrf,
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify({ id, status: "checked_in", check_in_date: date, check_in_time: time }),
            });
            if (!res.ok) throw new Error("Failed to check in visitor");
            closeModal(checkInModal);
            location.reload();
        } catch (err) {
            Swal.fire({
                icon: "error",
                title: "Check-in failed",
                text: err.message || "Please try again.",
                confirmButtonColor: "#2f855a",
            });
        }
    });

    // Close buttons for visitor modals
    document.getElementById("closeViewVisitor")?.addEventListener("click", () => closeModal(viewVisitorModal));
    document.getElementById("closeViewVisitor2")?.addEventListener("click", () => closeModal(viewVisitorModal));
    document.getElementById("closeEditVisitor")?.addEventListener("click", () => closeModal(editVisitorModal));
    document.getElementById("cancelEditVisitor")?.addEventListener("click", () => closeModal(editVisitorModal));
    document.getElementById("closeDeleteVisitor")?.addEventListener("click", () => closeModal(deleteVisitorModal));
    document.getElementById("cancelDeleteVisitor")?.addEventListener("click", () => closeModal(deleteVisitorModal));

    // Fetch visitor data
    async function fetchVisitor(id) {
        const url = `{{ route('visitor.get') }}?id=${encodeURIComponent(id)}`;
        const res = await fetch(url, { headers: { Accept: "application/json" } });
        if (!res.ok) return null;
        const data = await res.json();
        return data.visitor || null;
    }

    // Delegated clicks for visitor actions
    document.addEventListener("click", async (e) => {
        const vView = e.target.closest(".visitorViewBtn");
        const vEdit = e.target.closest(".visitorEditBtn");
        const vDel = e.target.closest(".visitorDeleteBtn");
        const vIn = e.target.closest(".visitorCheckInBtn");

        if (!vView && !vEdit && !vDel && !vIn) return;
        e.preventDefault();
        const id = (vView || vEdit || vDel || vIn)?.dataset.id;

        if (vView) {
            const v = await fetchVisitor(id);
            if (!v) return;
            document.getElementById("vvId").textContent = v.id || "";
            document.getElementById("vvName").textContent = v.name || "—";
            document.getElementById("vvCompany").textContent = v.company || "—";
            document.getElementById("vvType").textContent = v.visitor_type || "—";
            document.getElementById("vvHost").textContent = v.host || "—";
            document.getElementById("vvDept").textContent = v.host_department || "—";
            document.getElementById("vvDate").textContent = v.check_in_date || "—";
            document.getElementById("vvTime").textContent = v.check_in_time || "—";
            document.getElementById("vvStatus").textContent = (v.status || "scheduled").replace("_", " ");
            openModal(viewVisitorModal);
            return;
        }

        if (vEdit) {
            const v = await fetchVisitor(id);
            if (!v) return;
            document.getElementById("evId").value = v.id || "";
            document.getElementById("evCompany").value = v.company || "";
            document.getElementById("evType").value = v.visitor_type || "";
            document.getElementById("evPurpose").value = v.purpose || "";
            document.getElementById("evDate").value = v.check_in_date || "";
            document.getElementById("evTime").value = v.check_in_time || "";
            document.getElementById("evStatus").value = v.status || "scheduled";
            openModal(editVisitorModal);
            return;
        }

        if (vDel) {
            document.getElementById("dvId").value = id;
            document.getElementById("dvText").textContent = id;
            openModal(deleteVisitorModal);
            return;
        }

        if (vIn) {
            e.stopPropagation();
            document.getElementById("ciId").value = id;
            document.getElementById("ciText").textContent = id;
            openModal(checkInModal);
            return;
        }
    });

    // Submit edit visitor
    document.getElementById("editVisitorForm")?.addEventListener("submit", async (e) => {
        e.preventDefault();
        const payload = {
            id: document.getElementById("evId").value,
            company: document.getElementById("evCompany").value || null,
            visitor_type: document.getElementById("evType").value || null,
            purpose: document.getElementById("evPurpose").value || null,
            check_in_date: document.getElementById("evDate").value || null,
            check_in_time: document.getElementById("evTime").value || null,
            status: document.getElementById("evStatus").value || null,
        };
        try {
            const res = await fetch(`{{ route('visitor.update') }}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf,
                    "Accept": "application/json",
                },
                body: JSON.stringify(payload),
            });
            if (res.ok) {
                closeModal(editVisitorModal);
                location.reload();
            } else {
                throw new Error("Failed to update visitor");
            }
        } catch (err) {
            Swal.fire({
                icon: "error",
                title: "Update failed",
                text: err.message || "Please try again.",
                confirmButtonColor: "#2f855a",
            });
        }
    });

    // Confirm delete
    document.getElementById("confirmDeleteVisitor")?.addEventListener("click", async () => {
        const id = document.getElementById("dvId").value;
        const fd = new FormData();
        fd.append("id", id);
        try {
            const res = await fetch(`{{ route('visitor.delete') }}`, {
                method: "POST",
                headers: { "X-CSRF-TOKEN": csrf, "Accept": "application/json" },
                body: fd,
            });
            if (res.ok) {
                closeModal(deleteVisitorModal);
                location.reload();
            } else {
                throw new Error("Failed to delete visitor");
            }
        } catch (err) {
            Swal.fire({
                icon: "error",
                title: "Deletion failed",
                text: err.message || "Please try again.",
                confirmButtonColor: "#2f855a",
            });
        }
    });

    // Add visitor form submission (delegate to support duplicate forms/modals)
    document.addEventListener("submit", async (e) => {
        const formEl = e.target;
        if (!(formEl && formEl.id === "addVisitorForm")) return;
        e.preventDefault();
        // Basic client-side validation
        const trim = (v) => (v ?? "").toString().trim();
        const errors = [];
        const firstName = trim(formEl.firstName?.value);
        const lastName = trim(formEl.lastName?.value);
        const phone = trim(formEl.phone?.value);
        const company = trim(formEl.company?.value);
        const visitorType = trim(formEl.visitorType?.value);
        const hostId = trim(formEl.hostId?.value);
        const hostName = trim(formEl.hostName?.value); // optional alt
        const hostDepartment = trim(formEl.hostDepartment?.value);
        const purpose = trim(formEl.purpose?.value);
        const checkInDate = trim(formEl.checkInDate?.value);
        const checkInTime = trim(formEl.checkInTime?.value);
        const email = trim(formEl.email?.value);

        // Requireds
        if (!firstName) errors.push("First name is required.");
        if (!lastName) errors.push("Last name is required.");
        if (!phone) errors.push("Phone is required.");
        if (!company) errors.push("Company is required.");
        if (!visitorType) errors.push("Visitor type is required.");
        if (!purpose) errors.push("Purpose is required.");
        if (!checkInDate) errors.push("Check-in date is required.");
        if (!checkInTime) errors.push("Check-in time is required.");
        // Host requirement: allow either hostId or hostName
        if (!hostId && !hostName) errors.push("Host is required.");

        // Simple format checks
        if (email && !/^\S+@\S+\.\S+$/.test(email)) errors.push("Email format is invalid.");
        if (phone && phone.replace(/[^0-9+\-()\s]/g, "").length < 7) errors.push("Phone seems invalid.");

        // Visual hinting
        const mark = (el, bad) => { if (!el) return; el.classList.toggle("border-red-500", !!bad); el.classList.toggle("border-gray-300", !bad); };
        mark(formEl.firstName, !firstName);
        mark(formEl.lastName, !lastName);
        mark(formEl.phone, !phone);
        mark(formEl.company, !company);
        mark(formEl.visitorType, !visitorType);
        mark(formEl.purpose, !purpose);
        mark(formEl.checkInDate, !checkInDate);
        mark(formEl.checkInTime, !checkInTime);
        mark(formEl.hostId || formEl.hostName, !hostId && !hostName);
        mark(formEl.email, email && !/^\S+@\S+\.\S+$/.test(email));

        if (errors.length > 0) {
            Swal.fire({
                icon: "error",
                title: "Please fix the following",
                html: `<div style="text-align:left">${errors.map(e => `• ${e}`).join('<br>')}</div>`,
                confirmButtonColor: "#2f855a",
            });
            const firstInvalid = [formEl.firstName, formEl.lastName, formEl.phone, formEl.company, formEl.visitorType, formEl.purpose, formEl.checkInDate, formEl.checkInTime, (formEl.hostId || formEl.hostName), formEl.email].find(el => el && el.classList.contains('border-red-500'));
            if (firstInvalid) firstInvalid.focus();
            return;
        }

        const payload = {
            firstName,
            lastName,
            email: email || null,
            phone,
            company,
            visitorType,
            hostId: hostId || null,
            hostName: hostName || "",
            hostDepartment: hostDepartment || null,
            purpose,
            checkInDate,
            checkInTime,
            notes: trim(formEl.notes?.value) || null,
            sendEmailNotification: !!formEl.sendEmailNotification?.checked,
        };
        try {
            const res = await fetch(`{{ route('visitor.create') }}`, {
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": csrf,
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify(payload),
            });
            if (!res.ok) {
                let msg = "Failed to register visitor";
                try {
                    const err = await res.json();
                    msg = err.message || (err.errors ? Object.values(err.errors).flat().join("\n") : msg);
                } catch (_) {
                    const txt = await res.text().catch(() => "");
                    if (res.status === 419) msg = "Session expired or CSRF mismatch. Please refresh and try again.";
                    else if (txt) msg = txt.substring(0, 300);
                }
                throw new Error(msg);
            }
            // Success: clear form & validation and close modal
            clearAddVisitorValidation(formEl);
            formEl.reset();
            closeAllModals();
            // Optional: keep reload for now; if you want instant table update, I can add it.
            location.reload();
        } catch (err) {
            Swal.fire({
                icon: "error",
                title: "Registration failed",
                text: err.message || "Please check the form and try again.",
                confirmButtonColor: "#2f855a",
            });
        }
    });

    // Window resize handling
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

    // Tooltip handling
    const tooltipTriggers = document.querySelectorAll("[data-tooltip]");
    tooltipTriggers.forEach((trigger) => {
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
