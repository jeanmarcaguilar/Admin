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
    <section class="hidden lg:flex w-1/2 items-center justify-center p-12 text-white">
      <div class="flex flex-col items-center w-full py-12">
        
        <!-- Logo & Title -->
        <div class="text-center mb-8">
          <img src="{{ asset('golden-arc.png') }}" alt="Logo" class="w-28 h-28 mx-auto rounded-full border-4 border-white/30 shadow-2xl">
          <h1 class="text-5xl font-bold mt-6 tracking-tight">Welcome Back</h1>
          <p class="text-white/80 text-lg mt-2">Sign in to continue your journey</p>
        </div>

        <!-- Illustration Area -->
        <div class="relative w-full max-w-lg h-80 my-8 overflow-hidden">
          <div class="illustration-slideshow w-full h-full">
            <img src="{{ asset('assets/images/login/illustration-1.svg') }}" alt="Login Illustration 1" class="illustration-slide w-full h-full object-contain">
            <img src="{{ asset('assets/images/login/illustration-2.svg') }}" alt="Login Illustration 2" class="illustration-slide w-full h-full object-contain">
            <img src="{{ asset('assets/images/login/illustration-3.svg') }}" alt="Login Illustration 3" class="illustration-slide w-full h-full object-contain">
            <img src="{{ asset('assets/images/login/illustration-4.svg') }}" alt="Login Illustration 4" class="illustration-slide w-full h-full object-contain">
            <img src="{{ asset('assets/images/login/illustration-5.svg') }}" alt="Login Illustration 5" class="illustration-slide w-full h-full object-contain">
          </div>
        </div>

        <!-- Quote -->
        <div class="text-center mt-8 max-w-xl">
          <p class="italic text-white/90 text-lg leading-relaxed">
            "Success is not final, failure is not fatal: it is the courage to continue that counts."
          </p>
          <cite class="block text-right mt-3 text-white/60 text-sm">- Winston Churchill</cite>
        </div>
      </div>
    </section>

    <!-- Right Panel: Login Form -->
    <section class="w-full lg:w-1/2 flex items-center justify-center p-8">
      <div class="w-full max-w-md glass-card rounded-3xl shadow-2xl p-10">

        <!-- Mobile Logo (shown only on small screens) -->
        <div class="lg:hidden text-center mb-8">
          <img src="{{ asset('golden-arc.png') }}" alt="Logo" class="w-20 h-20 mx-auto rounded-full border-4 border-brand-primary shadow-lg">
          <h2 class="text-2xl font-bold text-gray-800 mt-4">Welcome Back</h2>
        </div>

        <!-- Form Header -->
        <div class="text-center mb-8">
          <h2 class="text-3xl font-bold text-gray-800 hidden lg:block">Sign In</h2>
          <p class="text-gray-600 mt-2">Enter your credentials to access your account</p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
          <div class="mb-6 p-4 bg-green-50 border-l-4 border-brand-primary rounded-lg">
            <p class="text-sm text-brand-primary font-medium">{{ session('status') }}</p>
          </div>
        @endif

        <form id="loginForm" method="POST" action="{{ route('login') }}" class="space-y-5">
          @csrf

          <!-- Username -->
          <div>
            <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                class="input-field w-full pl-12 pr-4 py-3.5 border-2 {{ $errors->has('username') ? 'border-red-500' : 'border-gray-200' }} rounded-xl shadow-sm text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all duration-200"
              />
            </div>
            @if ($errors->has('username'))
              <p class="text-red-600 text-xs mt-2 ml-1">{{ $errors->first('username') }}</p>
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
                autocomplete="current-password"
                placeholder="Enter your password"
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
            @if ($errors->has('password'))
              <p class="text-red-600 text-xs mt-2 ml-1">{{ $errors->first('password') }}</p>
            @endif
          </div>

          <!-- Remember Me -->
          <div class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember" class="custom-checkbox">
            <label for="remember_me" class="ml-3 text-sm text-gray-700 select-none cursor-pointer">
              Remember me for 30 days
            </label>
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
            <a href="/" class="flex-1 py-3.5 text-center text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200">
              Cancel
            </a>
            <button
              id="loginSubmitBtn"
              type="submit"
              class="btn-ripple flex-1 py-3.5 text-sm font-bold text-white bg-gradient-to-r from-brand-primary to-emerald-600 rounded-xl shadow-lg hover:shadow-xl hover:from-brand-primary-hover hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-brand-primary transition-all duration-200 transform hover:-translate-y-0.5"
            >
              Sign In
            </button>
          </div>
        </form>

        <!-- Forgot Password Link -->
        @if (Route::has('password.request'))
          <div class="text-center mt-6">
            <a class="text-sm text-brand-primary hover:text-brand-primary-hover font-medium hover:underline transition-colors" href="{{ route('password.request') }}">
              
            </a>
          </div>
        @endif

        <!-- Footer -->
        <div class="text-center mt-8 pt-6 border-t border-gray-200">
          <p class="text-xs text-gray-500">
            Need help? 
            <a href="#" class="text-brand-primary font-semibold hover:underline ml-1">Contact Support</a>
          </p>
          <p class="text-xs text-gray-400 mt-2">&copy; 2025 Login Portal. All Rights Reserved.</p>
        </div>
      </div>
    </section>
  </div>

  <script>
    (function() {
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
        
        // First submit: reveal 2FA section
        if (!twoFARevealed) {
          e.preventDefault();
          twoFA.classList.remove('hidden');
          twoFARevealed = true;
          submitBtn.textContent = 'Verify & Sign In';
          codeInput.focus();
          sendTwoFactorCode();
          return;
        }

        // Second submit: validate 6-digit code
        const code = (codeInput.value || '').trim();
        if (!/^\d{6}$/.test(code)) {
          e.preventDefault();
          codeInput.classList.add('border-red-500');
          codeInput.focus();
          return;
        }

        form.setAttribute('action', '{{ route('two-factor.verify') }}');
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
    })();
  </script>
</body>
</html>