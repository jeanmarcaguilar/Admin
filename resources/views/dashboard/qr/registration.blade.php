@php
    // Get the authenticated user
    $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Visitor Registration - QR AI</title>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

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
                <h1 class="text-xl font-bold text-gray-800">Visitor Registration</h1>
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
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">QR AI Registration</h1>
                    <p class="text-gray-600">Register new visitors and generate access QR codes</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Registration Form -->
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                        <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                            <i data-feather="user-plus" class="w-5 h-5 mr-2 text-indigo-600"></i>
                            New Visitor
                        </h2>

                        <form id="visitorForm" class="space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                    <input type="text" id="firstName" required
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                        placeholder="John">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                    <input type="text" id="lastName" required
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                        placeholder="Doe">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input type="email" id="email" required
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                    placeholder="john.doe@company.com">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" id="phone"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                    placeholder="+1 (555) 123-4567">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Company/Organization</label>
                                <input type="text" id="company"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                    placeholder="Acme Corporation">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Access Level</label>
                                    <select id="accessLevel"
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                        <option value="standard">Standard</option>
                                        <option value="vip">VIP</option>
                                        <option value="restricted">Restricted Areas</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Visit Date</label>
                                    <input type="date" id="visitDate" required
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Purpose of Visit</label>
                                <textarea id="purpose" rows="3"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                    placeholder="Meeting with sales team..."></textarea>
                            </div>

                            <button type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center">
                                <i data-feather="qr-code" class="w-5 h-5 mr-2"></i>
                                Generate QR Code
                            </button>
                        </form>
                    </div>

                    <!-- QR Code Display -->
                    <div
                        class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100 flex flex-col items-center justify-center min-h-[500px]">
                        <div id="qrPlaceholder" class="text-center">
                            <div class="w-32 h-32 bg-gray-50 rounded-2xl flex items-center justify-center mb-4 mx-auto">
                                <i data-feather="qr-code" class="w-16 h-16 text-gray-400"></i>
                            </div>
                            <p class="text-gray-500">Fill out the form to generate a QR code</p>
                        </div>

                        <div id="qrContainer" class="hidden text-center w-full">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Access QR Code</h3>
                            <div id="qrcode"
                                class="flex justify-center mb-6 bg-white p-4 rounded-xl inline-block border border-gray-100 shadow-sm">
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4 mb-4 text-left border border-gray-100">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">Visitor ID:</span>
                                    <span id="visitorId" class="text-sm font-mono font-semibold text-indigo-600"></span>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">Name:</span>
                                    <span id="displayName" class="text-sm font-semibold text-gray-800"></span>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">Access Level:</span>
                                    <span id="displayAccess" class="text-sm font-semibold"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Valid Until:</span>
                                    <span id="displayDate" class="text-sm font-semibold text-gray-800"></span>
                                </div>
                            </div>

                            <div class="flex gap-3 w-full">
                                <button onclick="downloadQR()"
                                    class="flex-1 bg-gray-50 hover:bg-gray-100 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center border border-gray-200">
                                    <i data-feather="download" class="w-4 h-4 mr-2"></i>
                                    Download
                                </button>
                                <button onclick="printQR()"
                                    class="flex-1 bg-gray-50 hover:bg-gray-100 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center border border-gray-200">
                                    <i data-feather="printer" class="w-4 h-4 mr-2"></i>
                                    Print
                                </button>
                            </div>

                            <button onclick="resetForm()" class="mt-4 text-gray-500 hover:text-gray-700 text-sm">
                                Register Another Visitor
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Recent Registrations -->
                <div class="mt-8 bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i data-feather="clock" class="w-5 h-5 mr-2 text-indigo-600"></i>
                        Recent Registrations
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-700 rounded-tl-lg">Name</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-700">Company</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-700">Access</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-700">Date</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-700 rounded-tr-lg">Status</th>
                                </tr>
                            </thead>
                            <tbody id="recentVisitors" class="divide-y divide-gray-100">
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        No recent registrations
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

            // Registration logic
            document.getElementById('visitDate').valueAsDate = new Date();

            document.getElementById('visitorForm').addEventListener('submit', function (e) {
                e.preventDefault();
                generateVisitorQR();
            });

            let currentQRCode = null;
            let visitorData = null;

            function generateVisitorQR() {
                const firstName = document.getElementById('firstName').value;
                const lastName = document.getElementById('lastName').value;
                const email = document.getElementById('email').value;
                const phone = document.getElementById('phone').value;
                const company = document.getElementById('company').value;
                const accessLevel = document.getElementById('accessLevel').value;
                const visitDate = document.getElementById('visitDate').value;
                const purpose = document.getElementById('purpose').value;

                const formData = {
                    first_name: firstName,
                    last_name: lastName,
                    email: email,
                    phone: phone,
                    company: company,
                    access_level: accessLevel,
                    visit_date: visitDate,
                    purpose: purpose
                };

                fetch('{{ route('api.qr.register') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(formData)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            visitorData = data.visitor;
                            displayQR(visitorData);
                            updateRecentList();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred during registration.');
                    });
            }

            function displayQR(visitor) {
                const qrData = JSON.stringify({
                    id: visitor.code,
                    name: visitor.name,
                    access: visitor.visitor_type,
                    date: visitor.check_in_date,
                    hash: btoa(visitor.code + visitor.check_in_date).substring(0, 16)
                });

                document.getElementById('qrPlaceholder').classList.add('hidden');
                document.getElementById('qrContainer').classList.remove('hidden');
                document.getElementById('qrcode').innerHTML = '';

                currentQRCode = new QRCode(document.getElementById('qrcode'), {
                    text: qrData, width: 200, height: 200, colorDark: '#1f2937', colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H
                });

                document.getElementById('visitorId').textContent = visitor.code;
                document.getElementById('displayName').textContent = visitor.name;

                const accessColors = {
                    standard: 'text-blue-600',
                    vip: 'text-amber-600',
                    restricted: 'text-red-600'
                };
                const accessDisplay = document.getElementById('displayAccess');
                accessDisplay.textContent = visitor.visitor_type.toUpperCase();
                accessDisplay.className = `text-sm font-semibold ${accessColors[visitor.visitor_type] || 'text-gray-600'}`;

                document.getElementById('displayDate').textContent = new Date(visitor.check_in_date).toLocaleDateString();
            }

            function updateRecentList() {
                fetch('{{ route('api.qr.recent') }}')
                    .then(response => response.json())
                    .then(visitors => {
                        const tbody = document.getElementById('recentVisitors');

                        if (visitors.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No recent registrations</td></tr>';
                            return;
                        }

                        tbody.innerHTML = visitors.slice(0, 5).map(v => `
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-800 font-medium">${v.name}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">${v.company || '-'}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full ${v.visitor_type === 'vip' ? 'bg-amber-100 text-amber-700' :
                                v.visitor_type === 'restricted' ? 'bg-red-100 text-red-700' :
                                    'bg-blue-100 text-blue-700'
                            }">${v.visitor_type.toUpperCase()}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">${new Date(v.check_in_date).toLocaleDateString()}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full ${v.status === 'checked_in' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'}">${v.status.toUpperCase()}</span>
                            </td>
                        </tr>
                    `).join('');
                    });
            }

            window.downloadQR = function () {
                const canvas = document.querySelector('#qrcode canvas');
                if (canvas) {
                    const link = document.createElement('a');
                    link.download = `QR-${visitorData.id}.png`;
                    link.href = canvas.toDataURL();
                    link.click();
                }
            };

            window.printQR = function () {
                const printWindow = window.open('', '_blank');
                const canvas = document.querySelector('#qrcode canvas');
                const qrImage = canvas ? canvas.toDataURL() : '';

                printWindow.document.write(`
                <html>
                <head><title>Visitor Pass - ${visitorData.firstName} ${visitorData.lastName}</title></head>
                <body style="font-family: Arial, sans-serif; text-align: center; padding: 40px;">
                    <h1>QR AI ACCESS PASS</h1>
                    <img src="${qrImage}" style="margin: 20px auto; display: block;">
                    <h2>${visitorData.name}</h2>
                    <p><strong>ID:</strong> ${visitorData.code}</p>
                    <p><strong>Access Level:</strong> ${visitorData.visitor_type.toUpperCase()}</p>
                    <p><strong>Valid Date:</strong> ${new Date(visitorData.check_in_date).toLocaleDateString()}</p>
                    <hr style="margin: 30px 0; border: 1px dashed #ccc;">
                    <p style="font-size: 12px; color: #666;">This pass must be presented at security. Valid only for the date specified.</p>
                
    @auth
        @include('partials.session-timeout-modal')
    @endauth
</body>
                </html>
            `);
                printWindow.document.close();
                printWindow.print();
            };

            window.resetForm = function () {
                document.getElementById('visitorForm').reset();
                document.getElementById('visitDate').valueAsDate = new Date();
                document.getElementById('qrPlaceholder').classList.remove('hidden');
                document.getElementById('qrContainer').classList.add('hidden');
                document.getElementById('qrcode').innerHTML = '';
                currentQRCode = null;
                visitorData = null;
            };

            updateRecentList();
        });
    </script>

    @auth
        @include('partials.session-timeout-modal')
    @endauth
</body>

</html>
