@php
    // Get the authenticated user
    $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>QR AI - Admin Dashboard</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png"
        href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
    <link rel="shortcut icon" type="image/png"
        href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
    <link rel="apple-touch-icon" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "brand-primary": "#059669",
                        "brand-primary-hover": "#047857",
                        "brand-background-main": "#F0FDF4",
                        "brand-border": "#D1FAE5",
                        "brand-text-primary": "#1F2937",
                        "brand-text-secondary": "#4B5563",
                    }
                }
            }
        }
    </script>

    <!-- External Libraries -->
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <!-- Font Awesome & Box Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
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

        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }

        .submenu.show {
            max-height: 500px;
        }

        .dropdown-panel {
            display: none;
        }

        .dropdown-panel.active {
            display: block;
        }
    </style>
</head>

<body class="bg-brand-background-main min-h-screen">
    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed top-0 left-0 h-full w-72 bg-white border-r border-gray-100 shadow-sm z-50
           transform -translate-x-full md:translate-x-0 transition-transform duration-300">

        <div class="h-16 flex items-center px-4 border-b border-gray-100">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 w-full rounded-xl px-2 py-2
               hover:bg-gray-100 active:bg-gray-200 transition group">
                <img src="{{ asset('golden-arc.png') }}" alt="Logo" class="w-10 h-10">
                <div class="leading-tight">
                    <div class="font-bold text-gray-800 group-hover:text-brand-primary transition-colors">
                        Microfinance Admin
                    </div>
                    <div
                        class="text-[11px] text-gray-500 font-semibold uppercase group-hover:text-brand-primary transition-colors">
                        Administrative
                    </div>
                </div>
            </a>
        </div>

        <!-- Sidebar content -->
        <div class="px-4 py-4 overflow-y-auto h-[calc(100%-4rem)]">
            <div class="text-xs font-bold text-gray-400 tracking-wider px-2">ADMINISTRATIVE DEPARTMENT</div>

            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" class="mt-3 flex items-center justify-between px-4 py-3 rounded-xl hover:bg-green-50 text-gray-700
               transition-all duration-200 active:scale-[0.99] font-semibold">
                <span class="flex items-center gap-3">
                    <span class="inline-flex w-9 h-9 rounded-lg bg-emerald-50 items-center justify-center">📊</span>
                    Dashboard
                </span>
            </a>

            <!-- Visitor Management Dropdown -->
            <button id="visitor-management-btn" class="mt-3 w-full flex items-center justify-between px-4 py-3 rounded-xl
               text-gray-700 hover:bg-green-50 hover:text-brand-primary
               transition-all duration-200 hover:translate-x-1 active:translate-x-0 active:scale-[0.99] font-semibold">
                <span class="flex items-center gap-3">
                    <span class="inline-flex w-9 h-9 rounded-lg bg-emerald-50 items-center justify-center">👥</span>
                    Visitor Management
                </span>
                <svg id="visitor-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-300 rotate-180"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="visitor-submenu" class="submenu mt-1 show">
                <div class="pl-4 pr-2 py-2 space-y-1 border-l-2 border-gray-100 ml-6">
                    <a href="{{ route('qr.dashboard') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm bg-brand-primary text-white shadow transition-all duration-200">
                        <svg class="w-3 h-3 opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        QR AI
                    </a>
                    <a href="{{ route('visitors.registration') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        Visitors Registration
                    </a>
                    <a href="{{ route('visitor.history.records') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        Visitor History
                    </a>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN WRAPPER -->
    <div class="md:pl-72">
        <!-- TOP HEADER -->
        <header class="h-16 bg-white flex items-center justify-between px-4 sm:px-6 relative shadow-sm">
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-bold text-gray-800">QR AI Dashboard</h1>
            </div>
            <div class="flex items-center gap-3">
                <span id="real-time-clock"
                    class="text-xs font-bold text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                    --:--:--
                </span>
                <div
                    class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center font-bold text-brand-primary border border-gray-100 shadow-sm">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT (Converted from qr/index.html) -->
        <main class="p-6">
            <!-- Dashboard Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div
                    class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total Visitors Today</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-1" id="totalVisitors">{{ $totalToday }}</h3>
                        </div>
                        <div
                            class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i data-feather="users" class="text-indigo-600"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-emerald-500 flex items-center">
                            <i data-feather="trending-up" class="w-4 h-4 mr-1"></i>
                            +12%
                        </span>
                        <span class="text-gray-400 ml-2">vs yesterday</span>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">VIP Guests</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-1" id="vipCount">
                                {{ $recentActivities->where('visitor_type', 'vip')->count() }}</h3>
                        </div>
                        <div
                            class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i data-feather="crown" class="text-amber-600"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-emerald-500 flex items-center">
                            <i data-feather="star" class="w-4 h-4 mr-1"></i>
                            Premium
                        </span>
                        <span class="text-gray-400 ml-2">exclusive access</span>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Pending Approvals</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-1" id="pendingCount">
                                {{ $recentActivities->where('status', 'pending')->count() }}</h3>
                        </div>
                        <div
                            class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i data-feather="clock" class="text-rose-600"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-rose-500 flex items-center">
                            <i data-feather="alert-circle" class="w-4 h-4 mr-1"></i>
                            Requires action
                        </span>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Checked In Today</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-1" id="scanCount">{{ $checkedInCount }}</h3>
                        </div>
                        <div
                            class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i data-feather="check-circle" class="text-emerald-600"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-emerald-500 flex items-center">
                            <i data-feather="activity" class="w-4 h-4 mr-1"></i>
                            Successful entries
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <a href="{{ route('qr.registration') }}"
                    class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 group text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold mb-1">New Registration</h3>
                            <p class="text-indigo-100 text-sm">Register visitor & generate QR</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i data-feather="plus" class="text-white"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('qr.scanner') }}"
                    class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 group text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold mb-1">Scan QR Code</h3>
                            <p class="text-emerald-100 text-sm">Verify visitor access</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i data-feather="camera" class="text-white"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Recent Activity</h2>
                    <a href="#" class="text-indigo-600 text-sm hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-sm font-medium text-gray-700 rounded-tl-lg">Visitor</th>
                                <th class="px-4 py-3 text-sm font-medium text-gray-700">Action</th>
                                <th class="px-4 py-3 text-sm font-medium text-gray-700">Time</th>
                                <th class="px-4 py-3 text-sm font-medium text-gray-700 rounded-tr-lg">Status</th>
                            </tr>
                        </thead>
                        <tbody id="activityLog" class="divide-y divide-gray-100">
                            @forelse($recentActivities as $activity)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div
                                                class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center mr-3">
                                                <span
                                                    class="text-xs font-bold text-indigo-600">{{ strtoupper(substr($activity->name, 0, 1)) }}</span>
                                            </div>
                                            <span class="text-sm font-medium text-gray-800">{{ $activity->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $activity->status === 'checked_in' ? 'Access Granted' : 'Registration' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $activity->updated_at->format('h:i A') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full {{ $activity->status === 'checked_in' ? 'bg-emerald-100 text-emerald-700' : ($activity->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                            {{ strtoupper(str_replace('_', ' ', $activity->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                        No recent activity
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Sidebar toggle and submenu logic
        document.addEventListener('DOMContentLoaded', function () {
            const visitorBtn = document.getElementById('visitor-management-btn');
            const visitorSubmenu = document.getElementById('visitor-submenu');
            const visitorArrow = document.getElementById('visitor-arrow');

            visitorBtn.addEventListener('click', () => {
                visitorSubmenu.classList.toggle('show');
                visitorArrow.classList.toggle('rotate-180');
            });

            // Clock logic
            function updateClock() {
                const now = new Date();
                const hours = now.getHours();
                const ampm = hours >= 12 ? 'PM' : 'AM';
                const displayHours = hours % 12 || 12;
                const minutes = now.getMinutes().toString().padStart(2, '0');
                const seconds = now.getSeconds().toString().padStart(2, '0');
                document.getElementById('real-time-clock').textContent = `${displayHours}:${minutes}:${seconds} ${ampm}`;
            }
            setInterval(updateClock, 1000);
            updateClock();

            // Feather icons
            feather.replace();
        });
    </script>

    @auth
        @include('partials.session-timeout-modal')
    @endauth
</body>

</html>

