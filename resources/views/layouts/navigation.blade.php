<nav class="w-full p-3 h-16 bg-[#28644c] text-white shadow-md fixed top-0 left-0 right-0 z-50">
    <div class="flex justify-between items-center h-full max-w-7xl mx-auto">
        <div class="flex items-center space-x-4">
            <button id="toggle-sidebar" class="pl-2 focus:outline-none">
                <i class="fa-solid fa-bars text-2xl cursor-pointer"></i>
            </button>
            <h1 class="text-2xl font-bold tracking-tight">Admin Dashboard</h1>
        </div>
        <div class="flex items-center space-x-1">
            <button class="relative p-2 transition duration-200 focus:outline-none" id="notificationBtn">
                <i class="fa-solid fa-bell text-xl"></i>
                <span class="absolute top-1 right-1 bg-red-500 text-xs text-white rounded-full px-1">3</span>
            </button>
            <div class="dropdown relative">
                <button class="flex items-center space-x-2 cursor-pointer px-3 py-2 transition duration-200" id="userMenuBtn" aria-label="User menu" aria-haspopup="true" aria-expanded="false">
                    <i class="fa-solid fa-user text-[18px] bg-white text-[#28644c] px-2.5 py-2 rounded-full"></i>
                    <span class="text-white font-medium">{{ Auth::user()->name }}</span>
                    <i class="fa-solid fa-chevron-down text-sm"></i>
                </button>
                <div class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50">
                    <button onclick="document.getElementById('profileModal').classList.remove('hidden')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user-circle mr-2"></i> My Profile
                    </button>
                    <button onclick="document.getElementById('accountSettingsModal').classList.remove('hidden')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-cog mr-2"></i> Account Settings
                    </button>
                    <button onclick="document.getElementById('privacySecurityModal').classList.remove('hidden')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-shield-alt mr-2"></i> Privacy & Security
                    </button>
                    <div class="border-t border-gray-100"></div>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i> Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
