<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Scheduling & Calendar Integrations</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 min-h-screen">
  <nav class="w-full p-3 h-16 bg-[#28644c] text-white shadow-md fixed top-0 left-0 right-0 z-50">
    <div class="flex justify-between items-center h-full max-w-7xl mx-auto">
      <div class="flex items-center space-x-3">
        <a href="{{ route('admin.dashboard') }}" class="text-white/80 hover:text-white" title="Back to Dashboard">
          <i class="fa-solid fa-chevron-left"></i>
        </a>
        <h1 class="text-xl font-bold tracking-tight">Scheduling & Calendar Integrations</h1>
      </div>
      <div class="flex items-center space-x-2">
        <a href="{{ route('admin.dashboard') }}" class="text-sm bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-md">Dashboard</a>
      </div>
    </div>
  </nav>

  <main class="max-w-7xl mx-auto pt-24 pb-10 px-4">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Calendar Panel -->
      <section class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
          <h2 class="text-[#1a4d38] font-bold">Calendar</h2>
          <div class="flex items-center gap-2">
            <button class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1.5 rounded-md"><i class="fa-solid fa-chevron-left mr-1"></i>Prev</button>
            <span class="text-sm font-semibold select-none">{{ now()->format('F Y') }}</span>
            <button class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1.5 rounded-md">Next<i class="fa-solid fa-chevron-right ml-1"></i></button>
          </div>
        </div>
        <div class="p-6">
          <!-- Placeholder calendar grid -->
          <div class="grid grid-cols-7 gap-2 text-xs">
            <div class="text-gray-500 text-center font-semibold">Sun</div>
            <div class="text-gray-500 text-center font-semibold">Mon</div>
            <div class="text-gray-500 text-center font-semibold">Tue</div>
            <div class="text-gray-500 text-center font-semibold">Wed</div>
            <div class="text-gray-500 text-center font-semibold">Thu</div>
            <div class="text-gray-500 text-center font-semibold">Fri</div>
            <div class="text-gray-500 text-center font-semibold">Sat</div>

            @for ($i = 1; $i <= 35; $i++)
              <div class="h-24 border border-gray-100 rounded-md p-1 relative bg-white hover:bg-gray-50">
                <span class="absolute top-1 right-1 text-[10px] text-gray-400">{{ $i <= 31 ? $i : '' }}</span>
                @if (in_array($i, [3, 9, 14, 22]))
                  <div class="mt-5 space-y-1">
                    <div class="text-[10px] px-2 py-1 rounded bg-green-100 text-green-700 truncate">Team Sync</div>
                    @if ($i === 14)
                      <div class="text-[10px] px-2 py-1 rounded bg-blue-100 text-blue-700 truncate">Client Call</div>
                    @endif
                  </div>
                @endif
              </div>
            @endfor
          </div>
        </div>
      </section>

      <!-- Right Panel: Actions & Integrations -->
      <aside class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
          <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-[#1a4d38] font-bold text-sm">Create Reservation</h3>
          </div>
          <form class="p-6 space-y-4" method="POST" action="#">
            @csrf
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Title</label>
              <input type="text" class="w-full border border-gray-200 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]" placeholder="Meeting title" />
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Date</label>
                <input type="date" class="w-full border border-gray-200 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Time</label>
                <input type="time" class="w-full border border-gray-200 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]" />
              </div>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Room/Equipment</label>
              <select class="w-full border border-gray-200 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]">
                <option>Conference Room A</option>
                <option>Conference Room B</option>
                <option>Projector</option>
                <option>Company Van</option>
              </select>
            </div>
            <div class="flex justify-end">
              <button type="submit" class="bg-[#28644c] text-white text-sm font-semibold px-4 py-2 rounded-md hover:bg-[#2f855A]">Save</button>
            </div>
          </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
          <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-[#1a4d38] font-bold text-sm">Upcoming Events</h3>
          </div>
          <ul class="p-4 space-y-3 text-sm">
            <li class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold">03</div>
              <div>
                <p class="font-semibold text-gray-800">Team Sync</p>
                <p class="text-gray-500 text-xs">03 {{ now()->format('M') }} · 2:00 PM</p>
              </div>
            </li>
            <li class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">14</div>
              <div>
                <p class="font-semibold text-gray-800">Client Call</p>
                <p class="text-gray-500 text-xs">14 {{ now()->format('M') }} · 10:00 AM</p>
              </div>
            </li>
            <li class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold">22</div>
              <div>
                <p class="font-semibold text-gray-800">Budget Review</p>
                <p class="text-gray-500 text-xs">22 {{ now()->format('M') }} · 4:00 PM</p>
              </div>
            </li>
          </ul>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
          <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-[#1a4d38] font-bold text-sm">Integrations</h3>
          </div>
          <div class="p-6 space-y-4 text-sm">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <i class="fa-brands fa-google text-red-500"></i>
                <span>Google Calendar</span>
              </div>
              <button class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1.5 rounded-md">Connect</button>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <i class="fa-brands fa-microsoft text-blue-600"></i>
                <span>Microsoft Outlook</span>
              </div>
              <button class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1.5 rounded-md">Connect</button>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-link text-gray-600"></i>
                <span>WebCal/iCal</span>
              </div>
              <button class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1.5 rounded-md">Configure</button>
            </div>
          </div>
        </div>
      </aside>
    </div>
  </main>

  <script>
    // Example notification when clicking Save
    document.addEventListener('submit', function (e) {
      if (e.target.matches('form')) {
        e.preventDefault();
        Swal.fire({
          icon: 'success',
          title: 'Reservation saved',
          text: 'Your event has been added to the calendar.',
          confirmButtonColor: '#28644c'
        });
      }
    });
  </script>
</body>
</html>
