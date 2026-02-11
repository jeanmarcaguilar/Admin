<!-- Global Loading Screen Component -->
<div id="globalLoadingScreen" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-sm w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="loadingContent">
        <!-- Loading Animation Container -->
        <div class="flex flex-col items-center space-y-6">
            <!-- Lottie Animation -->
            <div class="relative">
                <dotlottie-wc 
                    src="https://lottie.host/5378ba62-7703-4273-a14a-3a999385cf7f/s5Vm9nkLqj.lottie" 
                    style="width: 200px; height: 200px" 
                    autoplay 
                    loop>
                </dotlottie-wc>
                
                <!-- Pulse Ring Effect -->
                <div class="absolute inset-0 rounded-full border-4 border-emerald-200 animate-ping"></div>
            </div>
            
            <!-- Loading Text -->
            <div class="text-center space-y-2">
                <h3 class="text-lg font-bold text-gray-800 animate-pulse">
                    Processing...
                </h3>
                <p class="text-sm text-gray-600" id="loadingMessage">
                    Please wait while we process your request
                </p>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 h-2 rounded-full animate-pulse" 
                     id="loadingProgress"
                     style="width: 0%; transition: width 0.3s ease;">
                </div>
            </div>
            
            <!-- Loading Dots -->
            <div class="flex space-x-2">
                <div class="w-3 h-3 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0s;"></div>
                <div class="w-3 h-3 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.1s;"></div>
                <div class="w-3 h-3 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Mini Loading Bar (for inline loading) -->
<div id="miniLoadingBar" class="fixed top-0 left-0 w-full h-1 bg-gray-200 z-[9998] hidden">
    <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-600 animate-pulse" 
         id="miniProgressBar"
         style="width: 0%; transition: width 0.3s ease;">
    </div>
</div>

<!-- Loading Spinner (for buttons/cards) -->
<div id="loadingSpinner" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 hidden">
    <div class="bg-white rounded-2xl p-6 shadow-xl">
        <div class="flex items-center space-x-3">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600"></div>
            <span class="text-sm font-medium text-gray-700" id="spinnerText">Loading...</span>
        </div>
    </div>
</div>

<script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.11/dist/dotlottie-wc.js" type="module"></script>

<style>
/* Loading screen animations */
@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes slideDown {
    from {
        transform: translateY(-100%);
    }
    to {
        transform: translateY(0);
    }
}

#globalLoadingScreen.show #loadingContent {
    animation: fadeInScale 0.3s ease-out forwards;
}

#miniLoadingBar.show {
    animation: slideDown 0.2s ease-out;
}

/* Smooth transitions */
#globalLoadingScreen,
#miniLoadingBar,
#loadingSpinner {
    transition: all 0.3s ease;
}

/* Backdrop blur enhancement */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
</style>
