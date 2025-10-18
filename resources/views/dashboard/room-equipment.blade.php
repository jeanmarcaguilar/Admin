@php
// Get the authenticated user
$user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Room & Equipment Booking | Admin Dashboard</title>
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
        <h1 class="text-2xl font-bold tracking-tight">Room & Equipment Booking</h1>
      </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.getElementById('combinedBookingForm');
      const modeRadios = Array.from(document.querySelectorAll('input[name="booking_mode"]'));
      const roomSection = document.getElementById('roomSection');
      const equipmentSection = document.getElementById('equipmentSection');
      const roomSelect = document.getElementById('roomSelect');
      const equipmentContainer = document.getElementById('equipmentContainer');
      const dateInput = document.getElementById('bookingDate');
      const startInput = document.getElementById('startTime');
      const endInput = document.getElementById('endTime');

      function hasEquipmentSelected() {
        const selects = Array.from(equipmentContainer.querySelectorAll('select[name="equipment[]"]'));
        return selects.some(s => (s.value || '').trim() !== '');
      }

      function setVisibility(mode) {
        // Always keep date/time/purpose visible and required; backend expects them
        dateInput.required = true;
        startInput.required = true;
        endInput.required = true;

        if (mode === 'room') {
          roomSection.classList.remove('hidden');
          equipmentSection.classList.add('hidden');
          roomSelect.required = true;
        } else if (mode === 'equipment') {
          roomSection.classList.add('hidden');
          equipmentSection.classList.remove('hidden');
          roomSelect.required = false;
        } else { // both
          roomSection.classList.remove('hidden');
          equipmentSection.classList.remove('hidden');
          roomSelect.required = false; // allow either to be provided; we'll validate before submit
        }
      }

      function currentMode() {
        const checked = modeRadios.find(r => r.checked);
        return checked ? checked.value : 'both';
      }

      modeRadios.forEach(r => r.addEventListener('change', () => setVisibility(currentMode())));
      setVisibility(currentMode());

      form.addEventListener('submit', (e) => {
        const mode = currentMode();
        // Front-end validation aligned with backend combined route
        if (mode === 'room') {
          if (!roomSelect.value) {
            e.preventDefault();
            alert('Please select a room.');
            roomSelect.focus();
            return;
          }
        } else if (mode === 'equipment') {
          if (!hasEquipmentSelected()) {
            e.preventDefault();
            alert('Please select at least one equipment item.');
            const firstSelect = equipmentContainer.querySelector('select[name="equipment[]"]');
            firstSelect && firstSelect.focus();
            return;
          }
        } else { // both
          if (!roomSelect.value && !hasEquipmentSelected()) {
            e.preventDefault();
            alert('Please select a room or at least one equipment item.');
            return;
          }
        }
      });
    });
  </script>
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
            <div class="flex items-center font-medium justify-between text-lg bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
              <div class="flex items-center space-x-2">
                <i class="bx bx-calendar-check"></i>
                <span>Facilities Reservations</span>
              </div>
              <i class="bx bx-chevron-down text-2xl transition-transform duration-300"></i>
            </div>
            <ul class="dropdown-menu hidden bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
              <li><a href="{{ route('room-equipment') }}" class="block px-3 py-2 text-sm bg-white/30 rounded-lg"><i class="bx bx-door-open mr-2"></i>Room & Equipment Booking</a></li>
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

    <!-- Main Content -->
    <main id="main-content" class="flex-1 p-6 w-full mt-16">
      <div class="dashboard-container max-w-7xl mx-auto">
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-md border border-gray-100 p-8 space-y-6">
          <h2 class="text-[#1a4d38] font-bold text-xl mb-1">Room & Equipment Booking</h2>
          
          <!-- Booking Form -->
          <section class="grid grid-cols-1 gap-4">
            <div class="dashboard-card bg-gradient-to-br from-white to-gray-50 rounded-lg p-6 shadow-sm border border-gray-200">
              <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-lg text-[#1a4d38]">
                  <i class='bx bx-calendar-plus mr-2'></i>New Booking
                </h3>
                <a href="{{ route('scheduling.calendar') }}" class="text-[#2f855A] hover:text-[#1a4d38] text-sm font-medium flex items-center">
                  <i class='bx bx-calendar mr-1'></i> View Calendar
                </a>
              </div>
              <form id="combinedBookingForm" class="space-y-4" action="{{ route('booking.combined') }}" method="POST" novalidate>
                @csrf
                <!-- Booking Mode -->
                <fieldset class="border border-gray-200 rounded-md p-3">
                  <legend class="text-sm font-semibold text-gray-700">Booking Type</legend>
                  <div class="mt-2 flex flex-wrap gap-4 text-sm">
                    <label class="inline-flex items-center gap-2">
                      <input type="radio" name="booking_mode" value="both" class="text-[#2f855A]" checked>
                      <span>Room + Equipment</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                      <input type="radio" name="booking_mode" value="room" class="text-[#2f855A]">
                      <span>Room only</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                      <input type="radio" name="booking_mode" value="equipment" class="text-[#2f855A]">
                      <span>Equipment only</span>
                    </label>
                  </div>
                  <p class="mt-2 text-xs text-gray-500">Choose what you need to book. The form will adjust automatically.</p>
                </fieldset>
                
                <!-- Booking Details Section -->
                <div class="space-y-4">
                  <h4 class="text-md font-semibold text-gray-700 border-b pb-2">Booking Details</h4>
                  
                  <!-- Room Selection -->
                  <div id="roomSection" class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Room <span class="text-red-500">*</span></label>
                    <select name="room" id="roomSelect" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#2f855A] focus:border-transparent">
                      <option value="">-- Select a room --</option>
                      <option value="conference">Conference Room (Max: 20pax)</option>
                      <option value="meeting">Meeting Room (Max: 10pax)</option>
                      <option value="training">Training Room (Max: 30pax)</option>
                    </select>
                  </div>
                  
                  <!-- Equipment Selection -->
                  <div id="equipmentSection" class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Add Equipment (Optional)</label>
                    <div id="equipmentContainer">
                      <div class="flex items-end space-x-2 mb-2">
                        <div class="flex-1">
                          <select name="equipment[]" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#2f855A] focus:border-transparent">
                            <option value="">-- Select equipment --</option>
                            <option value="projector">Projector</option>
                            <option value="laptop">Laptop</option>
                            <option value="camera">Camera</option>
                            <option value="audio">Audio System</option>
                            <option value="whiteboard">Whiteboard</option>
                          </select>
                        </div>
                        <div class="w-24">
                          <input type="number" name="quantity[]" min="1" max="10" value="1" placeholder="Qty" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#2f855A] focus:border-transparent">
                        </div>
                        <button type="button" onclick="removeEquipment(this)" class="text-red-500 hover:text-red-700">
                          <i class="bx bx-trash"></i>
                        </button>
                      </div>
                    </div>
                    <button type="button" onclick="addEquipment()" class="mt-2 text-sm text-[#2f855A] hover:text-[#1a4d38] flex items-center">
                      <i class="bx bx-plus mr-1"></i> Add Another Equipment
                    </button>
                  </div>
                  
                  <!-- Date and Time Selection -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                      <label class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                      <input type="date" name="date" id="bookingDate" min="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#2f855A] focus:border-transparent" required>
                    </div>
                    <div class="form-group">
                      <label class="block text-sm font-medium text-gray-700 mb-1">Start Time <span class="text-red-500">*</span></label>
                      <input type="time" name="start_time" id="startTime" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#2f855A] focus:border-transparent" required>
                    </div>
                    <div class="form-group">
                      <label class="block text-sm font-medium text-gray-700 mb-1">End Time <span class="text-red-500">*</span></label>
                      <input type="time" name="end_time" id="endTime" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#2f855A] focus:border-transparent" required>
                    </div>
                    <div class="form-group">
                      <label class="block text-sm font-medium text-gray-700 mb-1">Return Equipment Date</label>
                      <input type="date" name="return_date" id="returnDate" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#2f855A] focus:border-transparent">
                    </div>
                  </div>
                  
                  <!-- Purpose -->
                  <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Purpose <span class="text-red-500">*</span></label>
                    <textarea name="purpose" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#2f855A] focus:border-transparent" placeholder="Briefly describe the purpose of the booking" required></textarea>
                  </div>
                </div>

                <button id="submitBookingBtn" type="submit" class="w-full bg-[#2f855A] text-white py-2 px-4 rounded-md hover:bg-[#276749] transition-colors focus:outline-none focus:ring-2 focus:ring-[#2f855A] focus:ring-offset-2">
                  Submit Booking
                </button>
              </form>
            </div>
          </section>
        </div>
      </div>
    </main>
  </div>

  <!-- View Booking Details Modal -->
  <div id="viewBookingModal" class="modal hidden" aria-modal="true" role="dialog">
    <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-center border-b px-6 py-4">
        <h3 class="text-xl font-semibold text-gray-900">Booking Details</h3>
        <button onclick="closeModal('viewBookingModal')" class="text-gray-400 hover:text-gray-500">
          <i class="fas fa-times text-2xl"></i>
        </button>
      </div>
      <div class="p-6" id="bookingDetailsContent">
        <!-- Content will be loaded by JavaScript -->
      </div>
    </div>
  </div>

  <!-- Cancel Booking Confirmation Modal -->
  <div id="cancelBookingModal" class="modal hidden" aria-modal="true" role="dialog">
    <div class="bg-white rounded-lg w-full max-w-md">
      <div class="p-6 text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
          <i class="fas fa-exclamation text-red-600 text-xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Cancel Booking</h3>
        <p class="text-sm text-gray-500 mb-6">Are you sure you want to cancel this booking? This action cannot be undone.</p>
        <div class="flex justify-center space-x-4">
          <button type="button" onclick="closeModal('cancelBookingModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            No, Keep It
          </button>
          <button type="button" id="confirmCancelBtn" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            Yes, Cancel Booking
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

  <!-- Modals -->
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
  const openPrivacySecurityBtn = document.getElementById("openPrivacySecurityBtn");
  const privacySecurityModal = document.getElementById("privacySecurityModal");
  const closePrivacySecurityBtn = document.getElementById("closePrivacySecurityBtn");
  const cancelPrivacySecurityBtn = document.getElementById("cancelPrivacySecurityBtn");
  const cancelSignOutBtn = document.getElementById("cancelSignOutBtn");
  const cancelSignOutBtn2 = document.getElementById("cancelSignOutBtn2");
  const signOutModal = document.getElementById("signOutModal");
  const openSignOutBtn = document.getElementById("openSignOutBtn");
  const viewBookingModal = document.getElementById("viewBookingModal");
  const cancelBookingModal = document.getElementById("cancelBookingModal");

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
    viewBookingModal.classList.add("hidden");
    cancelBookingModal.classList.add("hidden");
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
    viewBookingModal.classList.add("hidden");
    cancelBookingModal.classList.add("hidden");
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
    viewBookingModal.classList.add("hidden");
    cancelBookingModal.classList.add("hidden");
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
    viewBookingModal.classList.add("hidden");
    cancelBookingModal.classList.add("hidden");
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
    viewBookingModal.classList.add("hidden");
    cancelBookingModal.classList.add("hidden");
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
    viewBookingModal.classList.add("hidden");
    cancelBookingModal.classList.add("hidden");
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
    if (!viewBookingModal.contains(e.target)) {
      viewBookingModal.classList.add("hidden");
    }
    if (!cancelBookingModal.contains(e.target)) {
      cancelBookingModal.classList.add("hidden");
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
  viewBookingModal.querySelector("div").addEventListener("click", (e) => {
    e.stopPropagation();
  });
  cancelBookingModal.querySelector("div").addEventListener("click", (e) => {
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

  // Set minimum return date to be same as booking date
  document.getElementById('bookingDate')?.addEventListener('change', function() {
    const returnDate = document.getElementById('returnDate');
    if (returnDate) {
      returnDate.min = this.value;
      if (returnDate.value && returnDate.value < this.value) {
        returnDate.value = this.value;
      }
    }
  });

  // Function to add equipment field
  window.addEquipment = function() {
    const container = document.getElementById('equipmentContainer');
    const equipmentDiv = document.createElement('div');
    equipmentDiv.className = 'flex items-end space-x-2 mb-2';
    equipmentDiv.innerHTML = `
      <div class="flex-1">
        <select name="equipment[]" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#2f855A] focus:border-transparent">
          <option value="">-- Select equipment --</option>
          <option value="projector">Projector</option>
          <option value="laptop">Laptop</option>
          <option value="camera">Camera</option>
          <option value="audio">Audio System</option>
          <option value="whiteboard">Whiteboard</option>
        </select>
      </div>
      <div class="w-24">
        <input type="number" name="quantity[]" min="1" max="10" value="1" placeholder="Qty" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#2f855A] focus:border-transparent">
      </div>
      <button type="button" onclick="removeEquipment(this)" class="text-red-500 hover:text-red-700">
        <i class="bx bx-trash"></i>
      </button>
    `;
    container.appendChild(equipmentDiv);
  };

  // Function to remove equipment field
  window.removeEquipment = function(button) {
    button.parentElement.remove();
  };

  // Function to show error message
  function showErrorMessage(message) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: message,
      timer: 3000,
      showConfirmButton: false
    });
  }

  // Function to show success message
  function showSuccessMessage(message) {
    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: message,
      timer: 3000,
      showConfirmButton: false
    });
  }

  // Function to close modal
  window.closeModal = function(modalId) {
    document.getElementById(modalId).classList.add('hidden');
  };

  // Function to show booking details
  window.showBookingDetails = function(booking) {
    const modal = document.getElementById('viewBookingModal');
    const content = document.getElementById('bookingDetailsContent');
    let details = `
      <div class="space-y-4">
        <div>
          <h4 class="font-semibold text-gray-700">Booking ID</h4>
          <p class="text-sm text-gray-600">#${booking.id}</p>
        </div>
        <div>
          <h4 class="font-semibold text-gray-700">Type</h4>
          <p class="text-sm text-gray-600">${booking?.type ? booking.type.charAt(0).toUpperCase() + booking.type.slice(1) : 'N/A'}</p>
        </div>
        <div>
          <h4 class="font-semibold text-gray-700">${booking.type === 'room' ? 'Room' : 'Equipment'}</h4>
          <p class="text-sm text-gray-600">${booking.name || (booking.type === 'room' ? booking.room : booking.equipment)}</p>
        </div>
    `;

    if (booking.quantity && booking.type === 'equipment') {
      details += `
        <div>
          <h4 class="font-semibold text-gray-700">Quantity</h4>
          <p class="text-sm text-gray-600">${booking.quantity}</p>
        </div>
      `;
    }

    details += `
        <div>
          <h4 class="font-semibold text-gray-700">Date</h4>
          <p class="text-sm text-gray-600">${new Date(booking.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
        </div>
    `;

    if (booking.return_date && booking.return_date !== booking.date) {
      details += `
        <div>
          <h4 class="font-semibold text-gray-700">Return Date</h4>
          <p class="text-sm text-gray-600">${new Date(booking.return_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
        </div>
      `;
    }

    if (booking.start_time && booking.end_time) {
      details += `
        <div>
          <h4 class="font-semibold text-gray-700">Time</h4>
          <p class="text-sm text-gray-600">${new Date('2000-01-01T' + booking.start_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })} - ${new Date('2000-01-01T' + booking.end_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</p>
        </div>
      `;
    }

    details += `
        <div>
          <h4 class="font-semibold text-gray-700">Status</h4>
          <p class="text-sm text-gray-600">${booking?.status ? booking.status.charAt(0).toUpperCase() + booking.status.slice(1) : 'N/A'}</p>
        </div>
        <div>
          <h4 class="font-semibold text-gray-700">Purpose</h4>
          <p class="text-sm text-gray-600">${booking.purpose || 'Not specified'}</p>
        </div>
      </div>
    `;

    content.innerHTML = details;
    modal.classList.remove('hidden');
  };

  // Function to show cancel confirmation
  window.showCancelConfirmation = function(bookingId) {
    const modal = document.getElementById('cancelBookingModal');
    const confirmBtn = document.getElementById('confirmCancelBtn');

    confirmBtn.onclick = async () => {
      try {
        const response = await fetch(`/booking/cancel/${bookingId}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value,
            'Content-Type': 'application/json'
          }
        });

        if (response.ok) {
          const row = document.querySelector(`tr[data-booking-id="${bookingId}"]`);
          if (row) {
            const statusCell = row.querySelector('.status-cell span');
            statusCell.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800';
            statusCell.textContent = 'Cancelled';
            row.querySelector('td:last-child').innerHTML = `
              <button onclick="showBookingDetails(${JSON.stringify(row.dataset.booking)})" class="text-blue-600 hover:text-blue-900 mr-3 bg-transparent border-none p-0 cursor-pointer">View</button>
            `;
          }
          closeModal('cancelBookingModal');
          showSuccessMessage('Booking cancelled successfully');
        } else {
          showErrorMessage('Failed to cancel booking');
        }
      } catch (error) {
        showErrorMessage('Error cancelling booking');
      }
    };

    modal.classList.remove('hidden');
  };

  // Function to add a new booking to the table
  window.addBookingToTable = function(booking) {
    const bookingsTable = document.querySelector('table.min-w-full tbody');
    if (!bookingsTable) {
      console.error('Bookings table not found');
      return;
    }

    // Debug: Log the incoming booking object
    console.log('Raw booking data:', booking);

    // Ensure booking data is properly structured with fallback values
    const bookingData = {
      id: booking.id || 'temp_' + Date.now(),
      type: booking.room ? 'room' : 'equipment',
      status: (booking.status || 'pending').toLowerCase(),
      date: booking.date || booking.booking_date || new Date().toISOString().split('T')[0],
      return_date: booking.return_date || null,
      start_time: booking.start_time || null,
      end_time: booking.end_time || null,
      room: booking.room || null,
      equipment: booking.equipment || (booking.equipment_data && booking.equipment_data[0]?.name) || null,
      quantity: booking.quantity || (booking.equipment_data && booking.equipment_data[0]?.quantity) || 1,
      equipment_data: Array.isArray(booking.equipment_data) ? booking.equipment_data : 
                     (booking.equipment ? [{ name: booking.equipment, quantity: booking.quantity || 1 }] : []),
      purpose: booking.purpose || 'Not specified'
    };
    
    // Debug: Log the processed booking data
    console.log('Processed booking data:', JSON.parse(JSON.stringify(bookingData)));

    const row = document.createElement('tr');
    row.className = 'activity-item hover:bg-gray-50 transition-colors';
    row.setAttribute('data-booking-id', bookingData.id);

    // Status classes
    const statusClasses = {
      'confirmed': 'bg-green-100 text-green-800',
      'pending': 'bg-yellow-100 text-yellow-800',
      'cancelled': 'bg-red-100 text-red-800',
      'completed': 'bg-blue-100 text-blue-800',
      'approved': 'bg-green-100 text-green-800',
      'rejected': 'bg-red-100 text-red-800',
      'in-progress': 'bg-blue-100 text-blue-800'
    };
    const statusClass = statusClasses[bookingData.status.toLowerCase()] || 'bg-gray-100 text-gray-800';

    // Format date and time
    const formatDate = (dateString) => {
      try {
        if (!dateString) return 'N/A';
        const options = { month: 'short', day: 'numeric', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('en-US', options);
      } catch (e) {
        console.error('Error formatting date:', e);
        return 'Invalid date';
      }
    };

    const formatTime = (timeString) => {
      if (!timeString) return 'All day';
      try {
        // Handle both 'HH:mm' and 'HH:mm:ss' formats
        const timeParts = timeString.split(':');
        const timeObj = new Date();
        timeObj.setHours(parseInt(timeParts[0], 10), parseInt(timeParts[1], 10), 0);
        return timeObj.toLocaleTimeString('en-US', { 
          hour: '2-digit', 
          minute: '2-digit',
          hour12: true
        });
      } catch (e) {
        console.error('Error formatting time:', e);
        return timeString; // Return original if can't parse
      }
    };

    // Format booking items
    let bookingItems = [];
    
    // Handle room bookings
    if (bookingData.room) {
      bookingItems.push({ 
        name: bookingData.room, 
        quantity: 1,
        type: 'room'
      });
    }
    
    // Handle equipment bookings
    if (bookingData.equipment_data && Array.isArray(bookingData.equipment_data)) {
      bookingData.equipment_data.forEach(item => {
        if (item && (item.name || item.equipment_name)) {
          bookingItems.push({
            name: item.name || item.equipment_name,
            quantity: parseInt(item.quantity || 1, 10),
            type: 'equipment'
          });
        }
      });
    } else if (bookingData.equipment) {
      // Fallback for direct equipment field
      bookingItems.push({ 
        name: bookingData.equipment, 
        quantity: parseInt(bookingData.quantity || 1, 10),
        type: 'equipment'
      });
    }

    const formattedItems = bookingItems.length > 0 
      ? bookingItems.map(item => {
          const name = item.name || 'Unnamed Item';
          return item.quantity > 1 ? `${name} (x${item.quantity})` : name;
        }).join(', ')
      : 'No items specified';

    // Date info
    let dateInfo = '';
    if (bookingData.date) {
      dateInfo = `<div class="text-sm text-gray-900">${formatDate(bookingData.date)}</div>`;
      if (bookingData.return_date && bookingData.return_date !== bookingData.date) {
        dateInfo += `<div class="text-xs text-gray-500">to ${formatDate(bookingData.return_date)}</div>`;
      }
    } else {
      dateInfo = '<div class="text-sm text-gray-500">No date specified</div>';
    }

    // Time info
    let timeInfo = '';
    if (bookingData.start_time && bookingData.end_time) {
      const startTime = formatTime(bookingData.start_time);
      const endTime = formatTime(bookingData.end_time);
      timeInfo = `
        <div class="text-sm text-gray-900">
          ${startTime} - ${endTime}
        </div>
      `;
    } else if (bookingData.start_time) {
      timeInfo = `<div class="text-sm text-gray-900">From ${formatTime(bookingData.start_time)}</div>`;
    } else if (bookingData.end_time) {
      timeInfo = `<div class="text-sm text-gray-900">Until ${formatTime(bookingData.end_time)}</div>`;
    } else {
      timeInfo = '<div class="text-sm text-gray-500">All day</div>';
    }

    // Escape HTML in booking data for JSON
    const escapeHtml = (unsafe) => {
      return unsafe
        .toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    };

    // Action buttons
    let actions = `
      <div class="flex space-x-2 justify-end">
        <button onclick="showBookingDetails(${JSON.stringify(bookingData).replace(/"/g, '&quot;')})" 
                class="text-blue-600 hover:text-blue-800 p-1 rounded-full hover:bg-blue-50"
                title="View Details">
          <i class="bx bx-show text-lg"></i>
        </button>
    `;
    
    if (bookingData.status && !['cancelled', 'completed', 'rejected'].includes(bookingData.status.toLowerCase())) {
      actions += `
        <button onclick="event.stopPropagation(); showCancelConfirmation('${bookingData.id}')" 
                class="text-red-600 hover:text-red-800 p-1 rounded-full hover:bg-red-50"
                title="Cancel Booking">
          <i class="bx bx-x-circle text-lg"></i>
        </button>
      `;
    }
    actions += '</div>';

    // Determine icon and background based on booking type
    const isRoomBooking = bookingData.type === 'room' || bookingData.room;
    const iconClass = isRoomBooking ? 'bx-building text-blue-600' : 'bx-laptop text-purple-600';
    const bgClass = isRoomBooking ? 'bg-blue-100' : 'bg-purple-100';

    // Build the row HTML
    row.innerHTML = `
      <td class="px-6 py-4">
        <div class="flex items-center">
          <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center ${bgClass}">
            <i class="bx ${iconClass}"></i>
          </div>
          <div class="ml-4">
            <div class="text-sm font-medium text-gray-900">${formattedItems || 'New Booking'}</div>
            <div class="text-sm text-gray-500">#${bookingData.id}</div>
          </div>
        </div>
      </td>
      <td class="px-6 py-4 whitespace-nowrap">
        ${dateInfo}
      </td>
      <td class="px-6 py-4 whitespace-nowrap">
        ${timeInfo}
      </td>
      <td class="px-6 py-4 whitespace-nowrap status-cell">
        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
          ${bookingData.status ? bookingData.status.charAt(0).toUpperCase() + bookingData.status.slice(1) : 'N/A'}
        </span>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
        ${actions}
      </td>
    `;
    bookingsTable.insertBefore(row, bookingsTable.firstChild);
  };

  // Form submission handler
  const bookingForm = document.getElementById('combinedBookingForm');
  if (bookingForm) {
    bookingForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      
      // Disable submit button to prevent double submission
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
      
      try {
        // Get form data
        const formData = new FormData(this);
        const formObject = {};
        const equipmentData = [];
        
        // Process equipment data
        const equipmentItems = formData.getAll('equipment[]');
        const quantities = formData.getAll('quantity[]');
        
        equipmentItems.forEach((equipment, index) => {
          if (equipment && equipment !== '-- Select equipment --') {
            equipmentData.push({
              name: equipment,
              quantity: parseInt(quantities[index] || 1, 10)
            });
          }
        });
        
        // Build the form object with proper structure
        formObject.room = formData.get('room') || null;
        formObject.purpose = formData.get('purpose') || 'Not specified';
        formObject.date = formData.get('date') || new Date().toISOString().split('T')[0];
        formObject.start_time = formData.get('start_time') || null;
        formObject.end_time = formData.get('end_time') || null;
        formObject.return_date = formData.get('return_date') || null;
        formObject.equipment_data = equipmentData;
        formObject.status = 'pending'; // Default status
        
        // Generate a temporary ID for the new booking
        formObject.id = 'temp_' + Date.now();
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || 
                         document.querySelector('input[name="_token"]')?.value;
        
        if (!csrfToken) {
          throw new Error('CSRF token not found');
        }
        
        // Send request to server
        const response = await fetch(this.action, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(formObject)
        });
        
        const responseData = await response.json();
        
        if (response.ok) {
          showSuccessMessage('Booking created successfully');
          
          // Reset form
          this.reset();
          
          // Reset equipment container
          const equipmentContainer = document.getElementById('equipmentContainer');
          if (equipmentContainer) {
            const firstChild = equipmentContainer.firstElementChild;
            while (equipmentContainer.children.length > 1) {
              equipmentContainer.removeChild(equipmentContainer.lastChild);
            }
          }
          
          // Redirect to calendar page to see the new booking
          setTimeout(() => {
            window.location.href = '{{ route("scheduling.calendar") }}';
          }, 1500);
        } else {
          // Handle validation errors
          if (responseData.errors) {
            const errorMessages = Object.values(responseData.errors)
              .flat()
              .map(error => `<li class="text-sm text-red-600">${error}</li>`)
              .join('');
            showErrorMessage(`<ul class="list-disc pl-4">${errorMessages}</ul>`);
          } else {
            showErrorMessage(responseData.message || 'Failed to create booking');
          }
        }
      } catch (error) {
        console.error('Booking error:', error);
        showErrorMessage('Error: ' + (error.message || 'An unexpected error occurred'));
      } finally {
        // Re-enable submit button
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnText;
        }
      }
    });
  }
});
  </script>
</body>
</html>