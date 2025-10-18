<aside id="sidebar" class="bg-[#2f855A] text-white flex flex-col z-40 fixed top-16 bottom-0 w-72 -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
    <div class="department-header px-2 py-4 mx-2 border-b border-white/50">
        <h1 class="text-xl font-bold">Administrative Department</h1>
    </div>
    <div class="px-3 py-10 flex-1">
        <ul class="space-y-6">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center font-medium space-x-2 text-lg hover:bg-white/30 px-3 py-2.5 rounded-lg whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : '' }}">
                    <i class="bx bx-graph"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <!-- Add more menu items as needed -->
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

<!-- Overlay for mobile -->
<div id="overlay" class="fixed inset-0 bg-black opacity-50 z-30 hidden md:hidden" onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden'); document.body.style.overflow = 'auto';"></div>
