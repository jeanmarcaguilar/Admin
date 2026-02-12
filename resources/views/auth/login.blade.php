<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Portal</title>
  <link rel="icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <script>
    window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            "brand-primary": "#10b981",
            "brand-primary-hover": "#059669",
            "brand-background": "#ecfdf5",
          }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }

    /* Floating shapes animation */
    .shape {
      position: absolute;
      border-radius: 50%;
      animation: float 20s infinite ease-in-out;
    }
    
    .shape-1 { animation-duration: 25s; animation-delay: 0s; }
    .shape-2 { animation-duration: 30s; animation-delay: 5s; }
    .shape-3 { animation-duration: 28s; animation-delay: 10s; }
    .shape-4 { animation-duration: 22s; animation-delay: 3s; }
    .shape-5 { animation-duration: 26s; animation-delay: 8s; }
    
    @keyframes float {
      0%, 100% {
        transform: translate(0, 0) rotate(0deg);
      }
      33% {
        transform: translate(30px, -30px) rotate(120deg);
      }
      66% {
        transform: translate(-20px, 20px) rotate(240deg);
      }
    }

    /* Illustration fade animation */
    .illustration {
      opacity: 0;
      transition: opacity 1s ease-in-out;
    }
    
    .illustration.active {
      opacity: 1;
    }

    /* Input focus effects */
    .input-field:focus {
      transform: translateY(-2px);
    }

    /* Button ripple effect */
    .btn-ripple {
      position: relative;
      overflow: hidden;
    }
    
    .btn-ripple::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.3);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }
    
    .btn-ripple:hover::before {
      width: 300px;
      height: 300px;
    }

    /* Illustration slideshow animation */
    .illustration-slideshow {
      position: relative;
      width: 100%;
      height: 100%;
    }
    
    .illustration-slide {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
      transition: opacity 1.5s ease-in-out;
    }
    
    .illustration-slide:first-child {
      opacity: 1;
    }
    
    @keyframes slideshow {
      0%, 16.66% { opacity: 1; }
      20%, 83.33% { opacity: 0; }
      100% { opacity: 0; }
    }
    
    .illustration-slide:nth-child(1) { animation: slideshow 15s infinite 0s; }
    .illustration-slide:nth-child(2) { animation: slideshow 15s infinite 3s; }
    .illustration-slide:nth-child(3) { animation: slideshow 15s infinite 6s; }
    .illustration-slide:nth-child(4) { animation: slideshow 15s infinite 9s; }
    .illustration-slide:nth-child(5) { animation: slideshow 15s infinite 12s; }

    /* Glassmorphism effect */
    .glass-card {
      background: rgba(255, 255, 255, 0.92);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.4);
    }

    /* Custom checkbox */
    .custom-checkbox {
      appearance: none;
      width: 18px;
      height: 18px;
      border: 2px solid #10b981;
      border-radius: 4px;
      cursor: pointer;
      position: relative;
      transition: all 0.3s ease;
    }
    
    .custom-checkbox:checked {
      background: #10b981;
    }
    
    .custom-checkbox:checked::after {
      content: '✓';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      color: white;
      font-size: 12px;
      font-weight: bold;
    }

    /* 2FA Section animation */
    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .slide-down {
      animation: slideDown 0.4s ease-out;
    }

    /* Loading screen progress animation */
    @keyframes progress {
      0% {
        width: 0%;
        opacity: 0.5;
      }
      50% {
        width: 80%;
        opacity: 1;
      }
      100% {
        width: 100%;
        opacity: 0.5;
      }
    }

    /* Loading screen transitions */
    #loadingScreen {
      transition: opacity 0.3s ease-in-out;
    }
    
    #loadingScreen.opacity-100 {
      opacity: 1;
    }
    
    #loadingScreen.opacity-0 {
      opacity: 0;
    }

    /* Page load animations */
    .page-load-animation {
      opacity: 0;
      transform: translateY(30px);
      animation: pageLoadFadeIn 0.8s ease-out forwards;
    }
    
    .page-load-animation-left {
      opacity: 0;
      transform: translateX(-50px);
      animation: pageLoadSlideInLeft 0.8s ease-out forwards;
    }
    
    .page-load-animation-right {
      opacity: 0;
      transform: translateX(50px);
      animation: pageLoadSlideInRight 0.8s ease-out forwards;
    }
    
    .page-load-animation-scale {
      opacity: 0;
      transform: scale(0.9);
      animation: pageLoadScaleIn 0.6s ease-out forwards;
    }
    
    @keyframes pageLoadFadeIn {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    @keyframes pageLoadSlideInLeft {
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }
    
    @keyframes pageLoadSlideInRight {
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }
    
    @keyframes pageLoadScaleIn {
      to {
        opacity: 1;
        transform: scale(1);
      }
    }
    
    /* Stagger animation delays */
    .stagger-1 { animation-delay: 0.1s; }
    .stagger-2 { animation-delay: 0.2s; }
    .stagger-3 { animation-delay: 0.3s; }
    .stagger-4 { animation-delay: 0.4s; }
    .stagger-5 { animation-delay: 0.5s; }
    .stagger-6 { animation-delay: 0.6s; }
  </style>
</head>

<body class="min-h-screen bg-brand-primary relative overflow-hidden">

  <!-- Floating Shapes Background -->
  <div class="absolute inset-0 z-0">
    <div class="shape shape-1 w-72 h-72 top-[5%] left-[-5%] bg-white/5"></div>
    <div class="shape shape-2 w-96 h-96 bottom-[-20%] left-[15%] bg-white/5"></div>
    <div class="shape shape-3 w-80 h-80 top-[-15%] right-[-10%] bg-white/5"></div>
    <div class="shape shape-4 w-56 h-56 bottom-[5%] right-[10%] bg-white/5"></div>
    <div class="shape shape-5 w-48 h-48 top-[50%] left-[50%] -translate-x-1/2 -translate-y-1/2 bg-white/5"></div>
  </div>

  <div class="min-h-screen flex relative z-10">

    <!-- Left Panel: Branding & Illustration -->
    <section class="hidden lg:flex w-1/2 items-center justify-center p-8 text-white page-load-animation-left stagger-1">
      <div class="flex flex-col items-center justify-center w-full h-full max-w-lg">
        
        <!-- Logo & Title -->
        <div class="text-center mb-6 page-load-animation stagger-2">
          <img src="{{ asset('golden-arc.png') }}" alt="Logo" class="w-24 h-24 mx-auto rounded-full border-4 border-white/30 shadow-2xl">
          <h1 class="text-4xl font-bold mt-4 tracking-tight">Welcome Back</h1>
          <p class="text-white/80 text-base mt-1">Sign in to continue your journey</p>
        </div>

        <!-- Illustration Area -->
        <div class="relative w-full h-64 my-6 overflow-hidden page-load-animation stagger-3">
          <div class="illustration-slideshow w-full h-full">
            <img src="{{ asset('assets/images/login/illustration-1.svg') }}" alt="Login Illustration 1" class="illustration-slide w-full h-full object-contain">
            <img src="{{ asset('assets/images/login/illustration-2.svg') }}" alt="Login Illustration 2" class="illustration-slide w-full h-full object-contain">
            <img src="{{ asset('assets/images/login/illustration-3.svg') }}" alt="Login Illustration 3" class="illustration-slide w-full h-full object-contain">
            <img src="{{ asset('assets/images/login/illustration-4.svg') }}" alt="Login Illustration 4" class="illustration-slide w-full h-full object-contain">
            <img src="{{ asset('assets/images/login/illustration-5.svg') }}" alt="Login Illustration 5" class="illustration-slide w-full h-full object-contain">
          </div>
        </div>

        <!-- Quote -->
        <div class="text-center mt-6 max-w-md page-load-animation stagger-4">
          <p class="italic text-white/90 text-base leading-relaxed">
            "Success is not final, failure is not fatal: it is the courage to continue that counts."
          </p>
          <cite class="block text-right mt-2 text-white/60 text-xs">- Winston Churchill</cite>
        </div>
      </div>
    </section>

    <!-- Right Panel: Login Form -->
    <section class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-8 page-load-animation-right stagger-1">
      <div class="w-full max-w-sm glass-card rounded-2xl shadow-xl p-8 page-load-animation-scale stagger-2">

        <!-- Mobile Logo (shown only on small screens) -->
        <div class="lg:hidden text-center mb-6 page-load-animation stagger-3">
          <img src="{{ asset('golden-arc.png') }}" alt="Logo" class="w-16 h-16 mx-auto rounded-full border-4 border-brand-primary shadow-lg">
          <h2 class="text-xl font-bold text-gray-800 mt-3">Welcome Back</h2>
        </div>

        <!-- Form Header -->
        <div class="text-center mb-6 page-load-animation stagger-4">
          <h2 class="text-2xl font-bold text-gray-800 hidden lg:block">Sign In</h2>
          <p class="text-gray-600 text-sm mt-1">Enter your credentials to access your account</p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
          <div class="mb-6 p-4 bg-green-50 border-l-4 border-brand-primary rounded-lg">
            <p class="text-sm text-brand-primary font-medium">{{ session('status') }}</p>
          </div>
        @endif

        <form id="loginForm" method="POST" action="{{ route('login') }}" class="space-y-4 page-load-animation stagger-5">
          @csrf

          <!-- Username -->
          <div>
            <label for="username" class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <input
                type="text"
                id="username"
                name="username"
                value="{{ old('username') }}"
                required
                autofocus
                placeholder="Enter your username"
                class="input-field w-full pl-10 pr-4 py-3 border-2 {{ $errors->has('username') ? 'border-red-500' : 'border-gray-200' }} rounded-lg shadow-sm text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all duration-200"
              />
            </div>
            @if ($errors->has('username'))
              <p class="text-red-600 text-xs mt-1 ml-1">{{ $errors->first('username') }}</p>
            @endif
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
              </div>
              <input
                type="password"
                id="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Enter your password"
                class="input-field w-full pl-10 pr-10 py-3 border-2 {{ $errors->has('password') ? 'border-red-500' : 'border-gray-200' }} rounded-lg shadow-sm text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all duration-200"
              />
              <div id="passwordToggle" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                <svg id="eyeOpen" class="h-4 w-4 text-gray-400 hover:text-brand-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <svg id="eyeClosed" class="h-4 w-4 text-gray-400 hover:text-brand-primary transition-colors hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.943-9.543-7a9.966 9.966 0 012.257-3.592m3.086-2.16A9.956 9.956 0 0112 5c4.478 0 8.269 2.943 9.543 7a9.97 9.97 0 01-4.043 5.197M15 12a3 3 0 00-4.5-2.598M9 12a3 3 0 004.5 2.598M3 3l18 18"></path>
                </svg>
              </div>
            </div>
            @if ($errors->has('password'))
              <p class="text-red-600 text-xs mt-1 ml-1">{{ $errors->first('password') }}</p>
            @endif
          </div>

          
          <!-- Two-Factor Authentication Section -->
          <div id="twoFactorSection" class="slide-down p-5 bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl {{ $errors->has('two_factor_code') || old('two_factor_code') ? '' : 'hidden' }}">
            <label for="two_factor_code" class="block text-sm font-semibold text-gray-700 mb-2">
              Two-Factor Authentication Code
            </label>
            <input
              type="text"
              inputmode="numeric"
              pattern="[0-9]*"
              maxlength="6"
              id="two_factor_code"
              name="two_factor_code"
              placeholder="Enter 6-digit code"
              value="{{ old('two_factor_code') }}"
              class="w-full px-4 py-3 border-2 border-green-300 rounded-lg shadow-sm text-sm text-center font-mono text-lg tracking-widest placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all"
            />
            @if ($errors->has('two_factor_code'))
              <p class="text-red-600 text-xs mt-2">{{ $errors->first('two_factor_code') }}</p>
            @endif
            <div class="mt-3 flex justify-between items-center">
              <span class="text-xs text-gray-600">Check your authenticator app or email</span>
              <button type="button" id="resend2fa" class="text-xs text-brand-primary font-semibold hover:underline">
                Resend Code
              </button>
            </div>
            <p id="twoFactorStatus" class="mt-2 text-xs text-gray-600 hidden"></p>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3 pt-2">
            <a href="/" class="flex-1 py-2.5 text-center text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200">
              Cancel
            </a>
            <button
              id="loginSubmitBtn"
              type="submit"
              class="btn-ripple flex-1 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-brand-primary to-emerald-600 rounded-lg shadow-lg hover:shadow-xl hover:from-brand-primary-hover hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-brand-primary transition-all duration-200 transform hover:-translate-y-0.5"
            >
              Sign In
            </button>
          </div>
        </form>

        <!-- Forgot Password Link -->
        @if (Route::has('password.request'))
          <div class="text-center mt-6 page-load-animation stagger-6">
            <a class="text-sm text-brand-primary hover:text-brand-primary-hover font-medium hover:underline transition-colors" href="{{ route('password.request') }}">
              
            </a>
          </div>
        @endif

        <!-- Footer -->
        <div class="text-center mt-6 pt-4 border-t border-gray-200">
          <p class="text-xs text-gray-400">&copy; 2025 Login Portal. All Rights Reserved.</p>
        </div>
      </div>
    </section>
  </div>

  <!-- Loading Screen -->
  <div id="loadingScreen" class="fixed inset-0 z-[9999] hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gradient-to-br from-brand-primary via-emerald-600 to-teal-600"></div>
    
    <!-- Loading Content -->
    <div class="fixed inset-0 flex flex-col items-center justify-center p-4">
      <!-- Logo -->
      <div class="mb-8">
        <img src="{{ asset('golden-arc.png') }}" alt="Logo" class="w-24 h-24 mx-auto rounded-full border-4 border-white/30 shadow-2xl animate-pulse">
      </div>
      
      <!-- Lottie Animation -->
      <div class="mb-8">
        <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.11/dist/dotlottie-wc.js" type="module"></script>
        <dotlottie-wc src="https://lottie.host/5378ba62-7703-4273-a14a-3a999385cf7f/s5Vm9nkLqj.lottie" style="width: 300px;height: 300px" autoplay loop></dotlottie-wc>
      </div>
      
      <!-- Loading Text -->
      <div class="text-center text-white">
        <h2 class="text-2xl font-bold mb-2">Verifying Your Identity</h2>
        <p class="text-white/80 text-sm mb-4">Securing your session and preparing your dashboard...</p>
        
        <!-- Loading Dots -->
        <div class="flex justify-center space-x-2">
          <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 0ms"></div>
          <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 150ms"></div>
          <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 300ms"></div>
        </div>
      </div>
      
      <!-- Progress Bar -->
      <div class="w-64 h-1 bg-white/20 rounded-full mt-8 overflow-hidden">
        <div class="h-full bg-white rounded-full animate-pulse" style="width: 60%; animation: progress 2s ease-in-out infinite;"></div>
      </div>
    </div>
  </div>

  <!-- OTP Verification Modal -->
  <div id="otpModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity duration-300"></div>
    
    <!-- Modal Container -->
    <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
      <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full transform transition-all duration-500 scale-95 opacity-0 pointer-events-auto" id="otpModalContent">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-brand-primary to-emerald-600 px-8 py-6 rounded-t-3xl relative">
          <!-- Background Pattern -->
          <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="%23000000" fill-opacity="0.1"%3E%3Ccircle cx="20" cy="20" r="2"/%3E%3C/g%3E%3C/svg%3E');"></div>
          </div>
          
          <!-- Header Content -->
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
              </div>
              <button onclick="closeOtpModal()" class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition-colors">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
            
            <h3 class="text-2xl font-bold text-white mb-2">Verify Your Identity</h3>
            <p class="text-white/90 text-sm">Enter the 6-digit code sent to your email</p>
          </div>
        </div>

        <!-- Modal Body -->
        <div class="px-8 py-6">
          <!-- OTP Input -->
          <form id="otpForm" class="space-y-4">
            @csrf
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-3">Verification Code</label>
              <div class="flex justify-center space-x-2 mb-4">
                <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-lg font-bold border-2 border-gray-300 rounded-lg focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 transition-all" data-index="0">
                <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-lg font-bold border-2 border-gray-300 rounded-lg focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 transition-all" data-index="1">
                <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-lg font-bold border-2 border-gray-300 rounded-lg focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 transition-all" data-index="2">
                <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-lg font-bold border-2 border-gray-300 rounded-lg focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 transition-all" data-index="3">
                <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-lg font-bold border-2 border-gray-300 rounded-lg focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 transition-all" data-index="4">
                <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-lg font-bold border-2 border-gray-300 rounded-lg focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 transition-all" data-index="5">
              </div>
              <input type="hidden" id="otpCode" name="two_factor_code" value="">
              <input type="hidden" id="otpUsernameHidden" name="username" value="">
              
              <!-- Error Message -->
              <div id="otpError" class="hidden p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-600 flex items-center">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span id="otpErrorText">Invalid code. Please try again.</span>
                </p>
              </div>
            </div>

            <!-- Resend Code -->
            <div class="text-center">
              <button type="button" id="otpResend" class="text-sm text-brand-primary font-semibold hover:text-brand-primary-hover transition-colors">
                Didn't receive the code? <span class="underline">Resend</span>
              </button>
              <p id="otpResendStatus" class="text-xs text-gray-500 mt-1 hidden"></p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4">
              <button type="button" onclick="closeOtpModal()" class="flex-1 py-3 text-center text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200">
                Cancel
              </button>
              <button
                type="submit"
                id="otpVerifyBtn"
                class="flex-1 py-3 text-sm font-bold text-white bg-gradient-to-r from-brand-primary to-emerald-600 rounded-lg shadow-lg hover:shadow-xl hover:from-brand-primary-hover hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-brand-primary transition-all duration-200 transform hover:-translate-y-0.5"
              >
                Verify & Continue
              </button>
            </div>
          </form>
        </div>

        <!-- Modal Footer -->
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 rounded-b-3xl">
          <div class="flex items-center justify-center text-xs text-gray-500">
            <svg class="w-4 h-4 mr-1 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            Secure authentication powered by two-factor protection
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Page load animation initialization
    function initializePageLoadAnimations() {
      // Add animation classes to elements that should animate on page load
      const animatedElements = document.querySelectorAll('.page-load-animation, .page-load-animation-left, .page-load-animation-right, .page-load-animation-scale');
      
      // Trigger animations by adding a small delay to ensure CSS is loaded
      setTimeout(() => {
        animatedElements.forEach(element => {
          element.style.animationPlayState = 'running';
        });
      }, 100);
    }

    // Loading Screen Functions
    function showLoadingScreen() {
      const loadingScreen = document.getElementById('loadingScreen');
      loadingScreen.classList.remove('hidden');
      // Add fade-in animation
      setTimeout(() => {
        loadingScreen.classList.add('opacity-100');
      }, 10);
    }

    function hideLoadingScreen() {
      const loadingScreen = document.getElementById('loadingScreen');
      loadingScreen.classList.add('opacity-0');
      setTimeout(() => {
        loadingScreen.classList.add('hidden');
      }, 300);
    }

    // OTP Modal Functions
    function showOtpModal(username) {
      const modal = document.getElementById('otpModal');
      const modalContent = document.getElementById('otpModalContent');
      const usernameHidden = document.getElementById('otpUsernameHidden');
      
      // Set username
      usernameHidden.value = username;
      
      // Show modal with animation
      modal.classList.remove('hidden');
      setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
      }, 10);
      
      // Focus first input
      setTimeout(() => {
        document.querySelector('.otp-input').focus();
      }, 300);
      
      // Send OTP code
      sendOtpCode();
    }

    function closeOtpModal() {
      const modal = document.getElementById('otpModal');
      const modalContent = document.getElementById('otpModalContent');
      
      modalContent.classList.remove('scale-100', 'opacity-100');
      modalContent.classList.add('scale-95', 'opacity-0');
      
      setTimeout(() => {
        modal.classList.add('hidden');
        // Reset form
        document.getElementById('otpForm').reset();
        document.getElementById('otpCode').value = '';
        document.getElementById('otpError').classList.add('hidden');
        // Clear OTP inputs
        document.querySelectorAll('.otp-input').forEach(input => input.value = '');
      }, 500);
    }

    // OTP Input Handling
    function setupOtpInputs() {
      const inputs = document.querySelectorAll('.otp-input');
      
      inputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
          const value = e.target.value;
          
          // Only allow numbers
          e.target.value = value.replace(/[^0-9]/g, '');
          
          // Move to next input
          if (e.target.value && index < inputs.length - 1) {
            inputs[index + 1].focus();
          }
          
          // Update hidden field
          updateOtpCode();
        });
        
        input.addEventListener('keydown', function(e) {
          // Handle backspace
          if (e.key === 'Backspace' && !e.target.value && index > 0) {
            inputs[index - 1].focus();
          }
        });
        
        input.addEventListener('paste', function(e) {
          e.preventDefault();
          const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
          
          // Fill inputs with pasted data
          pastedData.split('').forEach((char, i) => {
            if (i < inputs.length) {
              inputs[i].value = char;
            }
          });
          
          // Focus last filled input or next empty one
          const lastFilledIndex = Math.min(pastedData.length - 1, inputs.length - 1);
          inputs[lastFilledIndex].focus();
          
          // Update hidden field
          updateOtpCode();
        });
      });
    }

    function updateOtpCode() {
      const inputs = document.querySelectorAll('.otp-input');
      const code = Array.from(inputs).map(input => input.value).join('');
      document.getElementById('otpCode').value = code;
    }

    async function sendOtpCode() {
      const username = document.getElementById('otpUsernameHidden').value;
      const statusEl = document.getElementById('otpResendStatus');
      
      if (statusEl) {
        statusEl.classList.remove('hidden');
        statusEl.textContent = 'Sending verification code...';
        statusEl.className = 'text-xs text-gray-500 mt-1';
      }
      
      try {
        const response = await fetch('/two-factor/email', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json',
          },
          body: JSON.stringify({ username })
        });
        
        if (!response.ok) throw new Error('Failed to send code');
        
        if (statusEl) {
          statusEl.textContent = '✓ Verification code sent to your email';
          statusEl.className = 'text-xs text-green-600 mt-1';
        }
        return true;
      } catch (error) {
        if (statusEl) {
          statusEl.textContent = '✗ Could not send code. Please try again.';
          statusEl.className = 'text-xs text-red-600 mt-1';
        }
        return false;
      }
    }

    (function() {
      // Initialize page load animations
      initializePageLoadAnimations();
      
      const form = document.getElementById('loginForm');
      const csrfMeta = document.querySelector('meta[name="csrf-token"]');
      const twoFA = document.getElementById('twoFactorSection');
      const codeInput = document.getElementById('two_factor_code');
      const submitBtn = document.getElementById('loginSubmitBtn');
      const resendBtn = document.getElementById('resend2fa');
      const usernameInput = document.getElementById('username');
      const passwordInput = document.getElementById('password');
      const passwordToggle = document.getElementById('passwordToggle');
      const eyeOpen = document.getElementById('eyeOpen');
      const eyeClosed = document.getElementById('eyeClosed');
      const statusEl = document.getElementById('twoFactorStatus');
      
      let twoFARevealed = !twoFA.classList.contains('hidden');

      // Setup OTP inputs
      setupOtpInputs();

      // Password toggle functionality
      passwordToggle && passwordToggle.addEventListener('click', function() {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        eyeOpen.classList.toggle('hidden');
        eyeClosed.classList.toggle('hidden');
      });

      // Update button text if 2FA is already visible
      if (twoFARevealed) {
        submitBtn.textContent = 'Verify & Sign In';
      }

      function ensureCsrfInput() {
        const token = csrfMeta ? csrfMeta.getAttribute('content') : null;
        if (!token) return;
        let tokenInput = form.querySelector('input[name="_token"]');
        if (!tokenInput) {
          tokenInput = document.createElement('input');
          tokenInput.type = 'hidden';
          tokenInput.name = '_token';
          form.appendChild(tokenInput);
        }
        tokenInput.value = token;
      }

      ensureCsrfInput();

      form.addEventListener('submit', function(e) {
        ensureCsrfInput();
        
        // First submit: show OTP modal
        if (!twoFARevealed) {
          e.preventDefault();
          const username = (usernameInput && usernameInput.value) || '';
          
          showOtpModal(username);
          return;
        }

        // Second submit: validate 6-digit code (fallback for inline 2FA)
        const code = (codeInput.value || '').trim();
        if (!/^\d{6}$/.test(code)) {
          e.preventDefault();
          codeInput.classList.add('border-red-500');
          codeInput.focus();
          return;
        }

        form.setAttribute('action', '{{ route('two-factor.verify') }}');
      });

      // OTP Form submission
      document.getElementById('otpForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const code = document.getElementById('otpCode').value;
        const username = document.getElementById('otpUsernameHidden').value;
        const errorEl = document.getElementById('otpError');
        const errorTextEl = document.getElementById('otpErrorText');
        const verifyBtn = document.getElementById('otpVerifyBtn');
        
        // Validate code
        if (!/^\d{6}$/.test(code)) {
          errorEl.classList.remove('hidden');
          errorTextEl.textContent = 'Please enter all 6 digits of the verification code.';
          return;
        }
        
        // Hide any previous errors
        errorEl.classList.add('hidden');
        
        // Show loading state
        verifyBtn.disabled = true;
        verifyBtn.textContent = 'Verifying...';
        
        try {
          // Show loading screen
          showLoadingScreen();
          
          // Create a hidden form and submit it traditionally
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = '/two-factor/verify';
          
          // Add CSRF token
          const csrfInput = document.createElement('input');
          csrfInput.type = 'hidden';
          csrfInput.name = '_token';
          csrfInput.value = window.csrfToken;
          form.appendChild(csrfInput);
          
          // Add username
          const usernameInput = document.createElement('input');
          usernameInput.type = 'hidden';
          usernameInput.name = 'username';
          usernameInput.value = username;
          form.appendChild(usernameInput);
          
          // Add OTP code
          const codeInput = document.createElement('input');
          codeInput.type = 'hidden';
          codeInput.name = 'two_factor_code';
          codeInput.value = code;
          form.appendChild(codeInput);
          
          // Submit the form
          document.body.appendChild(form);
          form.submit();
          
        } catch (error) {
          console.error('Verification error:', error);
          // Hide loading screen if there's an error
          hideLoadingScreen();
          errorEl.classList.remove('hidden');
          errorTextEl.textContent = 'An error occurred during verification. Please try again.';
        } finally {
          // Re-enable button if submission fails (for safety)
          setTimeout(() => {
            if (!verifyBtn.disabled) {
              verifyBtn.disabled = false;
              verifyBtn.textContent = 'Verify & Continue';
            }
          }, 2000);
        }
      });

      // Resend OTP
      document.getElementById('otpResend').addEventListener('click', async function() {
        this.disabled = true;
        this.textContent = 'Sending...';
        
        const success = await sendOtpCode();
        
        setTimeout(() => {
          this.disabled = false;
          this.innerHTML = 'Didn\'t receive the code? <span class="underline">Resend</span>';
        }, success ? 60000 : 3000);
      });

      codeInput && codeInput.addEventListener('input', function() {
        codeInput.classList.remove('border-red-500');
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
      });

      async function sendTwoFactorCode() {
        ensureCsrfInput();
        const token = csrfMeta ? csrfMeta.getAttribute('content') : '';
        const username = (usernameInput && usernameInput.value) || '';
        
        if (statusEl) {
          statusEl.classList.remove('hidden');
          statusEl.textContent = 'Sending verification code...';
        }
        
        try {
          const resp = await fetch('/two-factor/email', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': token,
              'Accept': 'application/json',
            },
            body: JSON.stringify({ username })
          });
          
          if (!resp.ok) throw new Error('Failed to send code');
          
          if (statusEl) {
            statusEl.textContent = '✓ Verification code sent to your email';
            statusEl.classList.add('text-green-600');
          }
          return true;
        } catch (err) {
          if (statusEl) {
            statusEl.textContent = '✗ Could not send code. Please try again.';
            statusEl.classList.add('text-red-600');
          }
          return false;
        }
      }

      resendBtn && resendBtn.addEventListener('click', async function() {
        this.disabled = true;
        this.textContent = 'Sending...';
        const ok = await sendTwoFactorCode();
        this.textContent = ok ? 'Code Sent!' : 'Resend Code';
        setTimeout(() => {
          this.textContent = 'Resend Code';
          this.disabled = false;
        }, 60000);
      });

      // Close modal when clicking outside
      document.getElementById('otpModal').addEventListener('click', function(e) {
        if (e.target === this) {
          closeOtpModal();
        }
      });

      // Escape key to close modal
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          const modal = document.getElementById('otpModal');
          if (!modal.classList.contains('hidden')) {
            closeOtpModal();
          }
        }
      });
    })();
  </script>
</body>
</html>