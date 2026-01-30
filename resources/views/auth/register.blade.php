<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - Create Account</title>
  <link rel="icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
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

    /* Glassmorphism effect */
    .glass-card {
      background: rgba(255, 255, 255, 0.92);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.4);
    }

    /* Password strength indicator */
    .strength-bar {
      height: 4px;
      border-radius: 2px;
      transition: all 0.3s ease;
    }
    
    .strength-weak { width: 33%; background: #ef4444; }
    .strength-medium { width: 66%; background: #f59e0b; }
    .strength-strong { width: 100%; background: #10b981; }
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

    <!-- Left Panel: Branding -->
    <section class="hidden lg:flex w-1/2 items-center justify-center p-12 text-white">
      <div class="flex flex-col items-center w-full py-12">
        
        <!-- Logo & Title -->
        <div class="text-center mb-8">
          <img src="{{ asset('golden-arc.png') }}" alt="Logo" class="w-28 h-28 mx-auto rounded-full border-4 border-white/30 shadow-2xl">
          <h1 class="text-5xl font-bold mt-6 tracking-tight">Join Us Today</h1>
          <p class="text-white/80 text-lg mt-2">Create your account and get started</p>
        </div>

        <!-- Illustration -->
        <div class="relative w-full max-w-lg h-80 my-8">
          <svg class="w-full h-full" viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="grad2" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:rgba(255,255,255,0.9);stop-opacity:1" />
                <stop offset="100%" style="stop-color:rgba(255,255,255,0.6);stop-opacity:1" />
              </linearGradient>
            </defs>
            <!-- Illustration: people collaborating -->
            <ellipse cx="200" cy="270" rx="140" ry="15" fill="rgba(255,255,255,0.2)"/>
            <!-- Person 1 -->
            <circle cx="150" cy="140" r="30" fill="url(#grad2)"/>
            <rect x="130" y="170" width="40" height="60" rx="8" fill="url(#grad2)"/>
            <path d="M 130 185 Q 100 210 90 230" stroke="url(#grad2)" stroke-width="6" fill="none" stroke-linecap="round"/>
            <path d="M 170 185 Q 145 210 140 230" stroke="url(#grad2)" stroke-width="6" fill="none" stroke-linecap="round"/>
            <!-- Person 2 -->
            <circle cx="250" cy="140" r="30" fill="url(#grad2)"/>
            <rect x="230" y="170" width="40" height="60" rx="8" fill="url(#grad2)"/>
            <path d="M 230 185 Q 260 210 265 230" stroke="url(#grad2)" stroke-width="6" fill="none" stroke-linecap="round"/>
            <path d="M 270 185 Q 300 210 310 230" stroke="url(#grad2)" stroke-width="6" fill="none" stroke-linecap="round"/>
            <!-- Connection line -->
            <path d="M 180 155 L 220 155" stroke="rgba(255,255,255,0.8)" stroke-width="4" stroke-dasharray="5,5"/>
            <circle cx="200" cy="155" r="8" fill="rgba(16,185,129,0.8)"/>
          </svg>
        </div>

        <!-- Quote -->
        <div class="text-center mt-8 max-w-xl">
          <p class="italic text-white/90 text-lg leading-relaxed">
            "Alone we can do so little; together we can do so much."
          </p>
          <cite class="block text-right mt-3 text-white/60 text-sm">- Helen Keller</cite>
        </div>
      </div>
    </section>

    <!-- Right Panel: Registration Form -->
    <section class="w-full lg:w-1/2 flex items-center justify-center p-8">
      <div class="w-full max-w-md glass-card rounded-3xl shadow-2xl p-10">

        <!-- Mobile Logo -->
        <div class="lg:hidden text-center mb-8">
          <img src="{{ asset('golden-arc.png') }}" alt="Logo" class="w-20 h-20 mx-auto rounded-full border-4 border-brand-primary shadow-lg">
          <h2 class="text-2xl font-bold text-gray-800 mt-4">Create Account</h2>
        </div>

        <!-- Form Header -->
        <div class="text-center mb-8">
          <h2 class="text-3xl font-bold text-gray-800 hidden lg:block">Create Account</h2>
          <p class="text-gray-600 mt-2">Fill in your details to get started</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
          @csrf

          <!-- Name -->
          <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Enter your full name"
                class="input-field w-full pl-12 pr-4 py-3.5 border-2 {{ $errors->has('name') ? 'border-red-500' : 'border-gray-200' }} rounded-xl shadow-sm text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all duration-200"
              />
            </div>
            @if ($errors->has('name'))
              <p class="text-red-600 text-xs mt-2 ml-1">{{ $errors->first('name') }}</p>
            @endif
          </div>

          <!-- Username -->
          <div>
            <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
              </div>
              <input
                type="text"
                id="username"
                name="username"
                value="{{ old('username') }}"
                required
                autocomplete="username"
                placeholder="Choose a username"
                class="input-field w-full pl-12 pr-4 py-3.5 border-2 {{ $errors->has('username') ? 'border-red-500' : 'border-gray-200' }} rounded-xl shadow-sm text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all duration-200"
              />
            </div>
            @if ($errors->has('username'))
              <p class="text-red-600 text-xs mt-2 ml-1">{{ $errors->first('username') }}</p>
            @endif
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
              </div>
              <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="email"
                placeholder="Enter your email"
                class="input-field w-full pl-12 pr-4 py-3.5 border-2 {{ $errors->has('email') ? 'border-red-500' : 'border-gray-200' }} rounded-xl shadow-sm text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all duration-200"
              />
            </div>
            @if ($errors->has('email'))
              <p class="text-red-600 text-xs mt-2 ml-1">{{ $errors->first('email') }}</p>
            @endif
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
              </div>
              <input
                type="password"
                id="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Create a password"
                class="input-field w-full pl-12 pr-12 py-3.5 border-2 {{ $errors->has('password') ? 'border-red-500' : 'border-gray-200' }} rounded-xl shadow-sm text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all duration-200"
              />
              <div id="passwordToggle" class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer">
                <svg id="eyeOpen" class="h-5 w-5 text-gray-400 hover:text-brand-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <svg id="eyeClosed" class="h-5 w-5 text-gray-400 hover:text-brand-primary transition-colors hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.943-9.543-7a9.966 9.966 0 012.257-3.592m3.086-2.16A9.956 9.956 0 0112 5c4.478 0 8.269 2.943 9.543 7a9.97 9.97 0 01-4.043 5.197M15 12a3 3 0 00-4.5-2.598M9 12a3 3 0 004.5 2.598M3 3l18 18"></path>
                </svg>
              </div>
            </div>
            <!-- Password Strength Indicator -->
            <div class="mt-2 bg-gray-200 rounded-full h-1 overflow-hidden">
              <div id="strengthBar" class="strength-bar"></div>
            </div>
            <p id="strengthText" class="text-xs text-gray-500 mt-1"></p>
            @if ($errors->has('password'))
              <p class="text-red-600 text-xs mt-2 ml-1">{{ $errors->first('password') }}</p>
            @endif
          </div>

          <!-- Confirm Password -->
          <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Confirm your password"
                class="input-field w-full pl-12 pr-12 py-3.5 border-2 {{ $errors->has('password_confirmation') ? 'border-red-500' : 'border-gray-200' }} rounded-xl shadow-sm text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all duration-200"
              />
              <div id="confirmToggle" class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer">
                <svg id="confirmEyeOpen" class="h-5 w-5 text-gray-400 hover:text-brand-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <svg id="confirmEyeClosed" class="h-5 w-5 text-gray-400 hover:text-brand-primary transition-colors hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.943-9.543-7a9.966 9.966 0 012.257-3.592m3.086-2.16A9.956 9.956 0 0112 5c4.478 0 8.269 2.943 9.543 7a9.97 9.97 0 01-4.043 5.197M15 12a3 3 0 00-4.5-2.598M9 12a3 3 0 004.5 2.598M3 3l18 18"></path>
                </svg>
              </div>
            </div>
            @if ($errors->has('password_confirmation'))
              <p class="text-red-600 text-xs mt-2 ml-1">{{ $errors->first('password_confirmation') }}</p>
            @endif
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3 pt-4">
            <a href="{{ route('login') }}" class="flex-1 py-3.5 text-center text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200">
              Cancel
            </a>
            <button
              type="submit"
              class="btn-ripple flex-1 py-3.5 text-sm font-bold text-white bg-gradient-to-r from-brand-primary to-emerald-600 rounded-xl shadow-lg hover:shadow-xl hover:from-brand-primary-hover hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-brand-primary transition-all duration-200 transform hover:-translate-y-0.5"
            >
              Create Account
            </button>
          </div>
        </form>

        <!-- Login Link -->
        <div class="text-center mt-6">
          <p class="text-sm text-gray-600">
            Already have an account? 
            <a class="text-brand-primary hover:text-brand-primary-hover font-semibold hover:underline transition-colors ml-1" href="{{ route('login') }}">
              Sign in
            </a>
          </p>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 pt-6 border-t border-gray-200">
          <p class="text-xs text-gray-400">&copy; 2025 Registration Portal. All Rights Reserved.</p>
        </div>
      </div>
    </section>
  </div>

  <script>
    // Password toggle functionality
    const passwordInput = document.getElementById('password');
    const passwordToggle = document.getElementById('passwordToggle');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    passwordToggle && passwordToggle.addEventListener('click', function() {
      const type = passwordInput.type === 'password' ? 'text' : 'password';
      passwordInput.type = type;
      eyeOpen.classList.toggle('hidden');
      eyeClosed.classList.toggle('hidden');
    });

    // Confirm password toggle
    const confirmInput = document.getElementById('password_confirmation');
    const confirmToggle = document.getElementById('confirmToggle');
    const confirmEyeOpen = document.getElementById('confirmEyeOpen');
    const confirmEyeClosed = document.getElementById('confirmEyeClosed');

    confirmToggle && confirmToggle.addEventListener('click', function() {
      const type = confirmInput.type === 'password' ? 'text' : 'password';
      confirmInput.type = type;
      confirmEyeOpen.classList.toggle('hidden');
      confirmEyeClosed.classList.toggle('hidden');
    });

    // Password strength indicator
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');

    passwordInput && passwordInput.addEventListener('input', function() {
      const password = this.value;
      let strength = 0;
      
      if (password.length >= 8) strength++;
      if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
      if (/\d/.test(password)) strength++;
      if (/[^a-zA-Z0-9]/.test(password)) strength++;

      strengthBar.className = 'strength-bar';
      
      if (strength <= 1) {
        strengthBar.classList.add('strength-weak');
        strengthText.textContent = 'Weak password';
        strengthText.className = 'text-xs text-red-600 mt-1';
      } else if (strength <= 2) {
        strengthBar.classList.add('strength-medium');
        strengthText.textContent = 'Medium strength';
        strengthText.className = 'text-xs text-yellow-600 mt-1';
      } else {
        strengthBar.classList.add('strength-strong');
        strengthText.textContent = 'Strong password';
        strengthText.className = 'text-xs text-green-600 mt-1';
      }
      
      if (password.length === 0) {
        strengthBar.className = 'strength-bar';
        strengthText.textContent = '';
      }
    });
  </script>
</body>
</html>