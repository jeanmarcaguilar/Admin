@php
    // Get the authenticated user
    $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>QR Scanner - QR AI</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

        #reader {
            border-radius: 12px;
            overflow: hidden;
        }

        #reader video {
            border-radius: 12px;
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
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1">
                        <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <h1 class="text-xl font-bold text-gray-800">QR Scanner</h1>
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

        <!-- MAIN CONTENT -->
        <main class="p-6">
            <div class="max-w-4xl mx-auto">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">QR Code Scanner</h1>
                    <p class="text-gray-600">Scan visitor QR codes for verification</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Scanner -->
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                        <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                            <i data-feather="camera" class="w-5 h-5 mr-2 text-indigo-600"></i>
                            Scan QR Code
                        </h2>

                        <div id="reader"
                            class="w-full rounded-xl overflow-hidden bg-gray-50 mb-4 border border-gray-100"></div>

                        <div class="flex gap-3">
                            <button id="startBtn" onclick="startScanner()"
                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center">
                                <i data-feather="play" class="w-5 h-5 mr-2"></i>
                                Start Scanner
                            </button>
                            <button id="stopBtn" onclick="stopScanner()"
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center hidden">
                                <i data-feather="square" class="w-5 h-5 mr-2"></i>
                                Stop
                            </button>
                        </div>

                        <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Or enter Visitor ID
                                manually:</label>
                            <div class="flex gap-2">
                                <input type="text" id="manualId" placeholder="VIS-XXXXXXX"
                                    class="flex-1 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <button onclick="manualVerify()"
                                    class="bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                    Verify
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Verification Result -->
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                        <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                            <i data-feather="shield" class="w-5 h-5 mr-2 text-indigo-600"></i>
                            Verification Status
                        </h2>

                        <div id="scanResult" class="hidden">
                            <div id="statusIcon"
                                class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 font-bold">
                                <!-- Icon injected by JS -->
                            </div>

                            <div id="statusText" class="text-center text-2xl font-bold mb-6"></div>

                            <div class="space-y-3 bg-gray-50 rounded-xl p-4 border border-gray-100">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Visitor Name:</span>
                                    <span id="scanName" class="font-semibold text-gray-800"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Visitor ID:</span>
                                    <span id="scanId" class="font-mono text-gray-800"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Access Level:</span>
                                    <span id="scanAccess" class="font-semibold"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Valid Date:</span>
                                    <span id="scanDate" class="text-gray-800"></span>
                                </div>
                            </div>

                            <div class="mt-6 flex gap-3">
                                <button onclick="grantAccess()"
                                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center">
                                    <i data-feather="check-circle" class="w-5 h-5 mr-2"></i>
                                    Grant Access
                                </button>
                                <button onclick="denyAccess()"
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center">
                                    <i data-feather="x-circle" class="w-5 h-5 mr-2"></i>
                                    Deny
                                </button>
                            </div>
                        </div>

                        <div id="waitingState" class="text-center py-12">
                            <div
                                class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-feather="maximize" class="w-12 h-12 text-gray-400"></i>
                            </div>
                            <p class="text-gray-500">Scan a QR code or enter ID to verify</p>
                        </div>
                    </div>
                </div>

                <!-- Access Log -->
                <div class="mt-8 bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i data-feather="list" class="w-5 h-5 mr-2 text-indigo-600"></i>
                        Today's Access Log
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-700 rounded-tl-lg">Time</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-700">Visitor</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-700">ID</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-700">Access</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-700 rounded-tr-lg">Status</th>
                                </tr>
                            </thead>
                            <tbody id="accessLog" class="divide-y divide-gray-100">
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        No entries today
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Feather icons
            feather.replace();

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

            // Sidebar logic
            const visitorBtn = document.getElementById('visitor-management-btn');
            const visitorSubmenu = document.getElementById('visitor-submenu');
            const visitorArrow = document.getElementById('visitor-arrow');

            visitorBtn.addEventListener('click', () => {
                visitorSubmenu.classList.toggle('show');
                visitorArrow.classList.toggle('rotate-180');
            });

            // Scanner logic
            let html5QrcodeScanner = null;
            let currentScan = null;

            window.startScanner = function () {
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                html5QrcodeScanner = new Html5Qrcode("reader");

                html5QrcodeScanner.start(
                    { facingMode: "environment" },
                    config,
                    onScanSuccess,
                    onScanFailure
                ).then(() => {
                    document.getElementById('startBtn').classList.add('hidden');
                    document.getElementById('stopBtn').classList.remove('hidden');
                }).catch(err => {
                    console.error("Scanner error:", err);
                    alert("Error starting scanner: " + err);
                });
            };

            window.stopScanner = function () {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.stop().then(() => {
                        document.getElementById('startBtn').classList.remove('hidden');
                        document.getElementById('stopBtn').classList.add('hidden');
                    });
                }
            };

            function onScanSuccess(decodedText, decodedResult) {
                try {
                    const data = JSON.parse(decodedText);
                    verifyVisitor(data);
                } catch (e) {
                    verifyVisitor({ id: decodedText });
                }
            }

            function onScanFailure(error) { }

            window.manualVerify = function () {
                const id = document.getElementById('manualId').value.trim();
                if (id) {
                    verifyVisitor({ id: id });
                }
            };

            function verifyVisitor(data) {
                const id = data.id;

                fetch('{{ route('api.qr.verify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ id: id })
                })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('waitingState').classList.add('hidden');
                        document.getElementById('scanResult').classList.remove('hidden');

                        const statusIcon = document.getElementById('statusIcon');
                        const statusText = document.getElementById('statusText');

                        if (data.success) {
                            const visitor = data.visitor;
                            currentScan = visitor;

                            statusIcon.className = 'w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 bg-emerald-100 text-emerald-600';
                            statusIcon.innerHTML = '<i data-feather="check" class="w-10 h-10"></i>';
                            statusText.textContent = data.message;
                            statusText.className = 'text-center text-2xl font-bold mb-6 text-emerald-600';

                            document.getElementById('scanName').textContent = visitor.name;
                            document.getElementById('scanId').textContent = visitor.code;

                            const accessColors = {
                                standard: 'text-blue-600',
                                vip: 'text-amber-600',
                                restricted: 'text-red-600'
                            };
                            const accessEl = document.getElementById('scanAccess');
                            accessEl.textContent = visitor.visitor_type.toUpperCase();
                            accessEl.className = `font-semibold ${accessColors[visitor.visitor_type] || 'text-gray-600'}`;

                            document.getElementById('scanDate').textContent = new Date(visitor.check_in_date).toLocaleDateString();
                            updateAccessLog();
                        } else {
                            currentScan = null;
                            statusIcon.className = 'w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 bg-red-100 text-red-600';
                            statusIcon.innerHTML = '<i data-feather="x" class="w-10 h-10"></i>';
                            statusText.textContent = data.message;
                            statusText.className = 'text-center text-2xl font-bold mb-6 text-red-600';

                            document.getElementById('scanName').textContent = '—';
                            document.getElementById('scanId').textContent = id || 'N/A';
                            document.getElementById('scanAccess').textContent = '—';
                            document.getElementById('scanDate').textContent = '—';
                        }
                        feather.replace();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred during verification.');
                    });
            }

            window.grantAccess = function () {
                if (currentScan) {
                    logAccess(currentScan, 'GRANTED');
                    alert(`Access Granted: ${currentScan.firstName} ${currentScan.lastName} has been granted access.`);
                    resetScanner();
                }
            };

            window.denyAccess = function () {
                if (currentScan) {
                    logAccess(currentScan, 'DENIED');
                    alert(`Access Denied: ${currentScan.firstName} ${currentScan.lastName} has been denied access.`);
                    resetScanner();
                }
            };

            function logAccess(visitor, status) {
                let logs = JSON.parse(localStorage.getItem('accessLogs') || '[]');
                logs.unshift({
                    visitor: visitor,
                    status: status,
                    timestamp: new Date().toISOString()
                });
                localStorage.setItem('accessLogs', JSON.stringify(logs));
                updateAccessLog();
            }

            function updateAccessLog() {
                fetch('{{ route('api.qr.recent') }}')
                    .then(response => response.json())
                    .then(visitors => {
                        const tbody = document.getElementById('accessLog');
                        const today = new Date().toDateString();
                        const todayLogs = visitors.filter(v => v.status === 'checked_in' && new Date(v.updated_at).toDateString() === today);

                        if (todayLogs.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No entries today</td></tr>';
                            return;
                        }

                        tbody.innerHTML = todayLogs.slice(0, 10).map(v => `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-800">${new Date(v.updated_at).toLocaleTimeString()}</td>
                        <td class="px-4 py-3 text-sm text-gray-800 font-medium">${v.name}</td>
                        <td class="px-4 py-3 text-sm font-mono text-gray-600">${v.code}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full ${v.visitor_type === 'vip' ? 'bg-amber-100 text-amber-700' :
                                v.visitor_type === 'restricted' ? 'bg-red-100 text-red-700' :
                                    'bg-blue-100 text-blue-700'
                            }">${v.visitor_type.toUpperCase()}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">GRANTED</span>
                        </td>
                    </tr>
                `).join('');
                    });
            }

            function resetScanner() {
                document.getElementById('scanResult').classList.add('hidden');
                document.getElementById('waitingState').classList.remove('hidden');
                currentScan = null;
            }

            updateAccessLog();
        });
    </script>

    @auth
        @include('partials.session-timeout-modal')
    @endauth
</body>

</html>
