<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
    <link rel="apple-touch-icon" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script>
      window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #e6f0e6;
    }
    .error-text {
      color: #dc2626;
      font-size: 10px;
      margin-top: 4px;
      display: none;
    }
    .error input {
      border-color: #dc2626;
    }
    .error .error-text {
      display: block;
    }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8 text-gray-900 text-xs">
  <div class="w-full max-w-sm sm:max-w-md bg-white rounded-2xl shadow-2xl border border-gray-200 p-8">
    <div class="flex items-center justify-center mb-8">
      <img src="{{ asset('golden-arc.png') }}" alt="Logo" class="h-12 w-12 mr-3 rounded-full border border-gray-200" />
      <h2 class="text-2xl font-extrabold text-green-800">Login Portal</h2>
    </div>
    
    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form id="loginForm" class="space-y-6" method="POST" action="{{ route('login') }}">
      @csrf
      
      <!-- Username -->
      <div>
        <label for="username" class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
        <input
          type="text"
          id="username"
          name="username"
          value="{{ old('username') }}"
          required
          autofocus
          placeholder="Enter your username"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition {{ $errors->has('username') ? 'border-red-500' : '' }}"
        />
        @if ($errors->has('username'))
          <p class="text-red-600 text-xs mt-1">{{ $errors->first('username') }}</p>
        @endif
      </div>
      
      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
        <input
          type="password"
          id="password"
          name="password"
          required
          autocomplete="current-password"
          placeholder="Enter your password"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition {{ $errors->has('password') ? 'border-red-500' : '' }}"
        />
        @if ($errors->has('password'))
          <p class="text-red-600 text-xs mt-1">{{ $errors->first('password') }}</p>
        @endif
      </div>
      
      <!-- Remember Me -->
      <div class="flex items-center">
        <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
        <label for="remember_me" class="ml-2 block text-xs text-gray-700">
          {{ __('Remember me') }}
        </label>
      </div>

      <!-- Two-Factor Authentication (hidden until first submit) -->
      <div id="twoFactorSection" class="mt-2 p-3 bg-gray-50 border border-gray-200 rounded-lg {{ $errors->has('two_factor_code') || old('two_factor_code') ? '' : 'hidden' }}">
        <label for="two_factor_code" class="block text-sm font-semibold text-gray-700 mb-1">Two-Factor Code</label>
        <input
          type="text"
          inputmode="numeric"
          pattern="[0-9]*"
          maxlength="6"
          id="two_factor_code"
          name="two_factor_code"
          placeholder="Enter 6-digit code"
          value="{{ old('two_factor_code') }}"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
        />
        <p id="twoFactorError" class="error-text">Please enter a valid 6-digit code.</p>
        @if ($errors->has('two_factor_code'))
          <p class="text-red-600 text-xs mt-1">{{ $errors->first('two_factor_code') }}</p>
        @endif
        <div class="mt-2 flex justify-between items-center">
          <span class="text-[10px] text-gray-500">Check your authenticator app or email for the code.</span>
          <button type="button" id="resend2fa" class="text-[11px] text-green-700 font-semibold hover:underline">Resend code</button>
        </div>
      </div>

      <div class="flex space-x-4">
        <a href="/" class="flex-1 py-3 text-center text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
          Cancel
        </a>
        <button
          id="loginSubmitBtn"
          type="submit"
          class="flex-1 py-3 text-sm font-semibold text-white bg-gradient-to-r from-green-600 to-green-500 rounded-lg shadow-lg hover:from-green-700 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-green-600 transition"
        >
          Log In
        </button>
      </div>
    </form>

    @if (Route::has('password.request'))
      <div class="text-center mt-4">
        <a class="text-xs text-green-600 hover:text-green-800" href="{{ route('password.request') }}">
          {{ __('Forgot your password?') }}
        </a>
      </div>
    @endif

    <div class="text-center mt-8">
      <p class="text-[10px] text-gray-600">
        Need Help?
        <a href="#" class="text-green-700 font-semibold hover:underline">Contact Support</a>
      </p>
    </div>
  </div>

  <script>
    (function() {
      const form = document.getElementById('loginForm');
      const csrfMeta = document.querySelector('meta[name="csrf-token"]');
      const twoFA = document.getElementById('twoFactorSection');
      const codeInput = document.getElementById('two_factor_code');
      const errorText = document.getElementById('twoFactorError');
      const submitBtn = document.getElementById('loginSubmitBtn');
      const resendBtn = document.getElementById('resend2fa');
      const usernameInput = document.getElementById('username');
      const statusAreaId = 'twofaStatusMsg';
      let twoFARevealed = !twoFA.classList.contains('hidden');

      // If server-side returned with 2FA visible, adjust button label
      if (twoFARevealed) {
        submitBtn.textContent = 'Verify & Log In';
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

      // Make sure CSRF token is present at load
      ensureCsrfInput();

      form.addEventListener('submit', function(e) {
        // Always ensure CSRF token before attempting submit
        ensureCsrfInput();
        // First submit: reveal 2FA section and stop submit
        if (!twoFARevealed) {
          e.preventDefault();
          twoFA.classList.remove('hidden');
          twoFARevealed = true;
          submitBtn.textContent = 'Verify & Log In';
          codeInput.focus();
          // Attempt to send a verification code to email on first reveal
          sendTwoFactorCode();
          return;
        }

        // Second submit: require a 6-digit code
        const code = (codeInput.value || '').trim();
        if (!/^\d{6}$/.test(code)) {
          e.preventDefault();
          errorText.style.display = 'block';
          codeInput.classList.add('border-red-500');
          codeInput.focus();
          return;
        }

        // Route second submit to 2FA verification endpoint
        // Keep username and remember; password will be ignored by the verifier
        form.setAttribute('action', '{{ route('two-factor.verify') }}');
      });

      codeInput && codeInput.addEventListener('input', function() {
        errorText.style.display = 'none';
        codeInput.classList.remove('border-red-500');
        // Only allow digits
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
      });

      async function sendTwoFactorCode() {
        ensureCsrfInput();
        const token = csrfMeta ? csrfMeta.getAttribute('content') : '';
        const username = (usernameInput && usernameInput.value) || '';
        const statusEl = getOrCreateStatusArea();
        try {
          statusEl.textContent = 'Sending verification code to your email...';
          // NOTE: Update this endpoint in your routes/controller as needed
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
          statusEl.textContent = 'Verification code sent. Please check your email.';
          return true;
        } catch (err) {
          statusEl.textContent = 'Could not send the verification code. Please try again.';
          return false;
        }
      }

      function getOrCreateStatusArea() {
        let el = document.getElementById(statusAreaId);
        if (!el) {
          el = document.createElement('p');
          el.id = statusAreaId;
          el.className = 'mt-2 text-[11px] text-gray-600';
          twoFA.appendChild(el);
        }
        return el;
      }

      resendBtn && resendBtn.addEventListener('click', async function() {
        this.disabled = true;
        const ok = await sendTwoFactorCode();
        this.textContent = ok ? 'Code sent!' : 'Resend code';
        setTimeout(() => {
          this.textContent = 'Resend code';
          this.disabled = false;
        }, 60000);
      });
    })();
  </script>
</body>
</html>
