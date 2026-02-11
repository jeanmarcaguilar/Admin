@php
$user = auth()->user();
$kpis = [
    'total_balance' => 0,
    'collected_mtd' => 0,
    'active_accounts' => 0,
    'ptp_due_today' => 0,
];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Collections | Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    .modal { display:none; background: rgba(0,0,0,0.5); position:fixed; inset:0; z-index:60; align-items:center; justify-content:center; }
    .modal.active { display:flex; }
  </style>
</head>
<body class="bg-gray-100">
  <nav class="w-full p-3 h-16 bg-[#28644c] text-white shadow-md fixed top-0 left-0 right-0 z-50">
    <div class="flex justify-between items-center h-full max-w-7xl mx-auto">
      <div class="flex items-center space-x-4">
        <h1 class="text-2xl font-bold tracking-tight">Collections</h1>
      </div>
      <div class="flex items-center space-x-2">
        <i class="fa-solid fa-user text-[18px] bg-white text-[#28644c] px-2.5 py-2 rounded-full"></i>
        <span class="text-white font-medium">{{ $user->name }}</span>
      </div>
    </div>
  </nav>
  <main class="max-w-7xl mx-auto p-6 mt-16">
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-xl font-semibold text-[#1a4d38]">Collections Dashboard</h2>
      <button id="unlockBtn" class="px-3 py-2 bg-[#2f855A] text-white rounded-md text-sm font-semibold flex items-center">
        <i class="fas fa-lock mr-2 text-[10px]"></i>Unlock with OTP
      </button>
    </div>
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-4">
        <p class="text-xs font-medium text-gray-500">Total Balance</p>
        <p id="kpi_total_balance" class="text-2xl font-bold text-gray-900 mt-1">******</p>
      </div>
      <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-4">
        <p class="text-xs font-medium text-gray-500">Collected (MTD)</p>
        <p id="kpi_collected_mtd" class="text-2xl font-bold text-gray-900 mt-1">******</p>
      </div>
      <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-4">
        <p class="text-xs font-medium text-gray-500">Active Accounts</p>
        <p id="kpi_active_accounts" class="text-2xl font-bold text-gray-900 mt-1">******</p>
      </div>
      <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-4">
        <p class="text-xs font-medium text-gray-500">PTP Due Today</p>
        <p id="kpi_ptp_due_today" class="text-2xl font-bold text-gray-900 mt-1">******</p>
      </div>
    </section>
  </main>

  <!-- Simple OTP Modal (segmented inputs) -->
  <div id="otpModal" class="modal hidden" role="dialog" aria-modal="true">
    <div class="bg-white rounded-lg shadow-lg w-[380px] max-w-full mx-4">
      <div class="px-5 py-3 border-b flex items-center justify-between">
        <h3 class="font-semibold text-sm text-gray-900">Unlock Sensitive Data</h3>
        <button id="closeOtp" class="text-gray-400 hover:text-gray-600 p-2"><i class="fas fa-times text-xs"></i></button>
      </div>
      <div class="px-6 py-5">
        <p class="text-sm text-gray-600 mb-3">Enter the 6-digit code to view amounts.</p>
        <div id="otpDigits" class="grid grid-cols-6 gap-2 mb-3">
          @for($i=0;$i<6;$i++)
            <input type="text" class="conf-otp-digit border rounded-md h-11 text-center text-lg" maxlength="1" inputmode="numeric" pattern="\d*" />
          @endfor
        </div>
        <p id="otpErr" class="text-xs text-red-600 hidden mb-2"></p>
        <div class="flex items-center justify-between">
          <button id="resendBtn" class="text-xs text-gray-500" disabled>Resend in <span id="timer">00:30</span></button>
          <button id="verifyBtn" class="px-3 py-2 bg-[#2f855A] text-white rounded-md text-sm font-semibold">Verify</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    (function(){
      const btn = document.getElementById('unlockBtn');
      const modal = document.getElementById('otpModal');
      const closeOtp = document.getElementById('closeOtp');
      const verifyBtn = document.getElementById('verifyBtn');
      const digitsWrap = document.getElementById('otpDigits');
      const err = document.getElementById('otpErr');
      const timerEl = document.getElementById('timer');
      const resendBtn = document.getElementById('resendBtn');

      function setMasked(masked){
        const map = {
          kpi_total_balance: '₱' + Number({{ $kpis['total_balance'] }}).toLocaleString(),
          kpi_collected_mtd: '₱' + Number({{ $kpis['collected_mtd'] }}).toLocaleString(),
          kpi_active_accounts: String({{ $kpis['active_accounts'] }}),
          kpi_ptp_due_today: String({{ $kpis['ptp_due_today'] }})
        };
        Object.keys(map).forEach(id => {
          const el = document.getElementById(id);
          if (!el) return;
          el.textContent = masked ? '******' : map[id];
        });
        btn.innerHTML = masked ? '<i class="fas fa-lock mr-2 text-[10px]"></i>Unlock with OTP' : '<i class="fas fa-lock-open mr-2 text-[10px]"></i>Lock';
      }

      function applyInitial(){
        let unlocked = false;
        try { unlocked = sessionStorage.getItem('__collections_unlocked') === '1'; } catch(_) {}
        setMasked(!unlocked);
      }

      function startTimer(){
        let remain = 30;
        resendBtn.disabled = true; resendBtn.classList.add('opacity-60','cursor-not-allowed');
        const t = setInterval(()=>{
          remain -= 1;
          if (remain <= 0){ clearInterval(t); timerEl.textContent = '00:00'; resendBtn.disabled = false; resendBtn.classList.remove('opacity-60','cursor-not-allowed'); }
          else { timerEl.textContent = '00:' + String(remain).padStart(2,'0'); }
        }, 1000);
      }

      function openModal(){ modal.classList.add('active'); modal.classList.remove('hidden'); startTimer(); const d = digitsWrap.querySelectorAll('.conf-otp-digit'); if (d[0]) d[0].focus(); }
      function closeModal(){ modal.classList.remove('active'); modal.classList.add('hidden'); }

      btn.addEventListener('click', (e)=>{
        e.preventDefault();
        const masked = (btn.textContent || '').toLowerCase().includes('unlock');
        if (!masked){ // lock
          setMasked(true);
          try { sessionStorage.removeItem('__collections_unlocked'); } catch(_) {}
          return;
        }
        openModal();
      });
      closeOtp.addEventListener('click', closeModal);
      verifyBtn.addEventListener('click', ()=>{
        // accept any 6 digits
        try{
          const code = Array.from(digitsWrap.querySelectorAll('.conf-otp-digit')).map(d=> (d.value||'').replace(/\D/g,'')).join('');
          if (code.length < 1) { err.textContent = 'Enter any code'; err.classList.remove('hidden'); return; }
        }catch(_){}
        setMasked(false);
        try { sessionStorage.setItem('__collections_unlocked','1'); } catch(_) {}
        closeModal();
      });
      digitsWrap.addEventListener('keydown', (ev)=>{ if(ev.key==='Enter'){ verifyBtn.click(); }});

      applyInitial();
    })();
  </script>

    @auth
        @include('partials.session-timeout-modal')
    @endauth
</body>
</html>

