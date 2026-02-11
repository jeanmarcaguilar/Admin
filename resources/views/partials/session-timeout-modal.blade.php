<!-- Session Timeout Warning Modal -->
<div id="sessionTimeoutModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[9999]">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95"
        id="sessionTimeoutModalContent">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-6 rounded-t-2xl relative">
            <div class="absolute inset-0 bg-black opacity-10 rounded-t-2xl"></div>
            <div class="relative z-10">
                <div
                    class="flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 backdrop-blur-sm rounded-full mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white text-center">Session Timeout Warning</h3>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <div class="text-center mb-6">
                <p class="text-gray-600 mb-4">
                    Your session will expire in <span id="sessionCountdown"
                        class="font-bold text-amber-600 text-lg">300</span> seconds due to inactivity.
                </p>
                <p class="text-sm text-gray-500">
                    Do you want to extend your session?
                </p>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
                <div id="sessionProgressBar"
                    class="bg-gradient-to-r from-amber-500 to-orange-500 h-2 rounded-full transition-all duration-1000 linear"
                    style="width: 100%"></div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button onclick="extendSession()"
                    class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 text-white px-4 py-3 rounded-xl font-semibold hover:from-amber-600 hover:to-orange-600 transition-all duration-200 transform hover:scale-105 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Extend Session
                </button>
                <button onclick="logoutNow()"
                    class="flex-1 bg-gray-100 text-gray-700 px-4 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-all duration-200 transform hover:scale-105 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Logout
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Session Expired Modal -->
<div id="sessionExpiredModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[9999]">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95"
        id="sessionExpiredModalContent">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-red-500 to-rose-500 p-6 rounded-t-2xl relative">
            <div class="absolute inset-0 bg-black opacity-10 rounded-t-2xl"></div>
            <div class="relative z-10">
                <div
                    class="flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 backdrop-blur-sm rounded-full mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white text-center">Session Expired</h3>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <div class="text-center mb-6">
                <p class="text-gray-600 mb-4">
                    Your session has expired due to inactivity.
                </p>
                <p class="text-sm text-gray-500">
                    Please login again to continue.
                </p>
            </div>

            <!-- Action Button -->
            <button onclick="redirectToLogin()"
                class="w-full bg-gradient-to-r from-red-500 to-rose-500 text-white px-4 py-3 rounded-xl font-semibold hover:from-red-600 hover:to-rose-600 transition-all duration-200 transform hover:scale-105 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                    </path>
                </svg>
                Go to Login
            </button>
        </div>
    </div>
</div>

<script>
    // Session Timeout Configuration
    const SESSION_TIMEOUT = 7200000; // 120 minutes in milliseconds
    const WARNING_TIMEOUT = 6900000; // Show warning at 115 minutes
    const COUNTDOWN_DURATION = 300000; // 5 minute countdown (300 seconds)
    let sessionTimer;
    let warningTimer;
    let countdownTimer;
    let countdownInterval;
    let isWarningShown = false;

    // Initialize session timeout monitoring
    document.addEventListener('DOMContentLoaded', function () {
        initializeSessionTimeout();
        setupActivityListeners();
    });

    function initializeSessionTimeout() {
        console.log('Initializing session timeout...');
        // Clear existing timers
        clearAllTimers();

        // Start session timer
        sessionTimer = setTimeout(() => {
            if (!isWarningShown) {
                console.log('Session timeout reached, showing warning...');
                showSessionWarning();
            }
        }, WARNING_TIMEOUT);

        console.log('Session timer set for', WARNING_TIMEOUT, 'ms');
    }

    function setupActivityListeners() {
        // Track user activity
        const events = [
            'mousedown', 'mousemove', 'keypress', 'scroll',
            'touchstart', 'click', 'keydown', 'keyup'
        ];

        events.forEach(event => {
            document.addEventListener(event, resetSessionTimer, true);
        });

        // Track AJAX requests
        const originalFetch = window.fetch;
        window.fetch = function (...args) {
            resetSessionTimer();
            return originalFetch.apply(this, args);
        };

        // Track jQuery AJAX if available
        if (window.jQuery) {
            window.jQuery(document).ajaxComplete(resetSessionTimer);
        }
    }

    function resetSessionTimer() {
        if (!isWarningShown) {
            console.log('Resetting session timer...');
            clearAllTimers();
            initializeSessionTimeout();

            // Ping server to extend session
            fetch('/session/extend', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Session extend response:', data);
                })
                .catch(error => {
                    console.error('Session extend error:', error);
                });
        }
    }

    function showSessionWarning() {
        console.log('Showing session warning modal...');
        isWarningShown = true;
        const modal = document.getElementById('sessionTimeoutModal');
        const modalContent = document.getElementById('sessionTimeoutModalContent');

        if (!modal) {
            console.error('Session timeout modal not found!');
            return;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Animate modal appearance
        setTimeout(() => {
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);

        startCountdown();

        // Auto logout after countdown
        countdownTimer = setTimeout(() => {
            console.log('Countdown finished, logging out...');
            logoutNow();
        }, COUNTDOWN_DURATION);
    }

    function startCountdown() {
        let timeLeft = COUNTDOWN_DURATION / 1000; // Convert to seconds (5 seconds)
        const countdownElement = document.getElementById('sessionCountdown');
        const progressBar = document.getElementById('sessionProgressBar');

        countdownInterval = setInterval(() => {
            timeLeft--;
            countdownElement.textContent = timeLeft;

            // Update progress bar
            const progress = (timeLeft / (COUNTDOWN_DURATION / 1000)) * 100;
            progressBar.style.width = progress + '%';

            // Change color as time runs out
            if (timeLeft <= 10) {
                progressBar.classList.remove('from-amber-500', 'to-orange-500');
                progressBar.classList.add('from-red-500', 'to-rose-500');
                countdownElement.classList.remove('text-amber-600');
                countdownElement.classList.add('text-red-600');
            }

            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
            }
        }, 1000);
    }

    function extendSession() {
        clearAllTimers();
        hideWarningModal();
        isWarningShown = false;
        initializeSessionTimeout();

        // Show success feedback
        showNotification('Session extended successfully', 'success');
    }

    function hideWarningModal() {
        const modal = document.getElementById('sessionTimeoutModal');
        const modalContent = document.getElementById('sessionTimeoutModalContent');

        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    function logoutNow() {
        clearAllTimers();
        hideWarningModal();

        // Show expired modal
        const expiredModal = document.getElementById('sessionExpiredModal');
        const expiredModalContent = document.getElementById('sessionExpiredModalContent');

        expiredModal.classList.remove('hidden');
        expiredModal.classList.add('flex');

        setTimeout(() => {
            expiredModalContent.classList.remove('scale-95');
            expiredModalContent.classList.add('scale-100');
        }, 10);

        // Perform logout
        fetch('/logout', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Content-Type': 'application/json'
            }
        }).finally(() => {
            // Redirect to login after showing modal
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
        });
    }

    function redirectToLogin() {
        window.location.href = '/login';
    }

    function clearAllTimers() {
        clearTimeout(sessionTimer);
        clearTimeout(warningTimer);
        clearTimeout(countdownTimer);
        clearInterval(countdownInterval);
    }

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-[10000] px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;

        // Set color based on type
        const colors = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            warning: 'bg-amber-500 text-white',
            info: 'bg-blue-500 text-white'
        };

        notification.classList.add(...colors[type].split(' '));
        notification.innerHTML = `
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
                type === 'error' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' :
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'}
            </svg>
            <span>${message}</span>
        </div>
    `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
            notification.classList.add('translate-x-0');
        }, 10);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.remove('translate-x-0');
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
</script>