<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mobile QR Scanner - Visitor Check-in</title>
  
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
  
  <style>
    body {
      background: linear-gradient(135deg, #059669 0%, #047857 100%);
      min-height: 100vh;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    
    .scanner-container {
      max-width: 400px;
      margin: 0 auto;
      padding: 20px;
    }
    
    #qr-reader {
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .visitor-card {
      background: white;
      border-radius: 20px;
      padding: 24px;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      animation: slideUp 0.5s ease-out;
    }
    
    @keyframes slideUp {
      from { transform: translateY(30px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
    
    .visitor-photo {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #059669;
    }
    
    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }
    
    .status-badge.active {
      background: #D1FAE5;
      color: #059669;
    }
    
    .status-badge.completed {
      background: #F3F4F6;
      color: #6B7280;
    }
    
    .pulse-dot {
      width: 8px;
      height: 8px;
      background: #059669;
      border-radius: 50%;
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0%, 100% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.5; transform: scale(1.1); }
    }
    
    .action-buttons {
      display: flex;
      gap: 12px;
      margin-top: 20px;
    }
    
    .btn-mobile {
      flex: 1;
      padding: 14px 20px;
      border-radius: 12px;
      font-weight: 600;
      text-align: center;
      transition: all 0.3s;
      border: none;
      cursor: pointer;
    }
    
    .btn-primary {
      background: #059669;
      color: white;
    }
    
    .btn-primary:hover {
      background: #047857;
      transform: translateY(-2px);
    }
    
    .btn-secondary {
      background: #F3F4F6;
      color: #374151;
    }
    
    .btn-secondary:hover {
      background: #E5E7EB;
    }
  </style>
</head>

<body>
  <div class="scanner-container">
    <!-- Header -->
    <div class="text-center mb-6">
      <h1 class="text-2xl font-bold text-white mb-2">Visitor Check-in</h1>
      <p class="text-white/80 text-sm">Scan QR code to view visitor information</p>
    </div>

    <!-- QR Scanner -->
    <div id="qr-reader" class="mb-6"></div>

    <!-- Manual Input -->
    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 mb-6">
      <label class="text-white text-sm font-medium mb-2 block">Or enter visitor code manually:</label>
      <div class="flex gap-2">
        <input type="text" id="manual-code" placeholder="Enter visitor code..." 
               class="flex-1 px-4 py-3 rounded-lg border border-white/20 bg-white/10 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/30">
        <button onclick="lookupManualCode()" class="bg-white/20 hover:bg-white/30 text-white px-4 py-3 rounded-lg font-medium transition-colors">
          Search
        </button>
      </div>
    </div>

    <!-- Visitor Result Card (Hidden initially) -->
    <div id="visitor-result" class="visitor-card hidden">
      <!-- Content will be dynamically inserted here -->
    </div>
  </div>

  <script>
    let scanner = null;
    let currentVisitor = null;

    // Initialize QR Scanner
    function initScanner() {
      scanner = new Html5QrcodeScanner(
        "qr-reader",
        { 
          fps: 10,
          qrbox: { width: 250, height: 250 },
          aspectRatio: 1.0
        }
      );

      scanner.render(onScanSuccess, onScanError);
    }

    // QR Code Scanned Successfully
    function onScanSuccess(decodedText, decodedResult) {
      // Stop scanning
      scanner.clear();
      
      // Vibrate and play sound if available
      if (navigator.vibrate) {
        navigator.vibrate([200, 100, 200]);
      }
      
      // Show loading
      Swal.fire({
        title: 'Scanning...',
        text: 'Looking up visitor information...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      // Lookup visitor
      lookupVisitor(decodedText);
    }

    // QR Scan Error
    function onScanError(errorMessage) {
      console.warn('QR Scan error:', errorMessage);
    }

    // Manual Code Lookup
    function lookupManualCode() {
      const code = document.getElementById('manual-code').value.trim();
      if (!code) {
        Swal.fire({
          icon: 'warning',
          title: 'Enter Code',
          text: 'Please enter a visitor code to search.',
          confirmButtonColor: '#059669'
        });
        return;
      }
      lookupVisitor(code);
    }

    // Lookup Visitor Information
    async function lookupVisitor(code) {
      try {
        const response = await fetch(`api/visitors.php?action=lookup_visitor&code=${encodeURIComponent(code)}`);
        const data = await response.json();

        Swal.close();

        if (!data.found) {
          Swal.fire({
            icon: 'error',
            title: 'Visitor Not Found',
            text: `No visitor found with code: ${code}`,
            confirmButtonColor: '#059669'
          });
          return;
        }

        currentVisitor = data.visitor;
        displayVisitorInfo(data);

      } catch (error) {
        Swal.close();
        Swal.fire({
          icon: 'error',
          title: 'Connection Error',
          text: 'Failed to lookup visitor. Please try again.',
          confirmButtonColor: '#059669'
        });
      }
    }

    // Display Visitor Information
    function displayVisitorInfo(data) {
      const visitor = data.visitor;
      const activeLog = data.active_log;
      const resultDiv = document.getElementById('visitor-result');

      const photoUrl = visitor.photo_url ? `../uploads/visitors/${visitor.photo_url}` : null;
      const photoHtml = photoUrl 
        ? `<img src="${photoUrl}" alt="${visitor.first_name}" class="visitor-photo">`
        : `<div class="visitor-photo bg-gray-200 flex items-center justify-center">
             <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
               <circle cx="9" cy="7" r="4"/>
             </svg>
           </div>`;

      const statusBadge = activeLog 
        ? `<span class="status-badge active">
             <span class="pulse-dot"></span>
             Currently Checked In
           </span>`
        : `<span class="status-badge completed">
             <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l2-2z"/>
             </svg>
             Not Checked In
           </span>`;

      const checkInTime = activeLog ? new Date(activeLog.check_in_time).toLocaleTimeString() : 'Not checked in';
      const duration = activeLog ? calculateDuration(activeLog.check_in_time) : 'N/A';

      resultDiv.innerHTML = `
        <div class="text-center mb-4">
          <h2 class="text-xl font-bold text-gray-800 mb-2">Visitor Information</h2>
          <div class="text-sm text-gray-500">Scanned at ${new Date().toLocaleTimeString()}</div>
        </div>

        <div class="flex items-center gap-4 mb-6">
          ${photoHtml}
          <div class="flex-1">
            <h3 class="text-lg font-bold text-gray-800">${visitor.first_name} ${visitor.last_name}</h3>
            <p class="text-gray-600 text-sm mb-1">${visitor.company || 'No Company'}</p>
            <p class="text-gray-500 text-xs">Visitor Code: ${visitor.visitor_code}</p>
            <p class="text-gray-500 text-xs">${visitor.visit_count || 0} previous visits</p>
          </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 mb-4">
          <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-medium text-gray-700">Status</span>
            ${statusBadge}
          </div>
          ${activeLog ? `
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <span class="text-gray-500">Check-in Time:</span>
                <span class="font-medium text-gray-800 block">${checkInTime}</span>
              </div>
              <div>
                <span class="text-gray-500">Duration:</span>
                <span class="font-medium text-gray-800 block">${duration}</span>
              </div>
            </div>
          ` : ''}
        </div>

        <div class="action-buttons">
          ${activeLog ? 
            `<button onclick="checkOutVisitor()" class="btn-mobile btn-secondary">
               Check Out
             </button>
             <button onclick="viewDetails()" class="btn-mobile btn-primary">
               View Details
             </button>` :
            `<button onclick="checkInVisitor()" class="btn-mobile btn-primary">
               Check In
             </button>
             <button onclick="viewDetails()" class="btn-mobile btn-secondary">
               View Details
             </button>`
          }
        </div>
      `;

      resultDiv.classList.remove('hidden');
      
      // Scroll to result
      resultDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Calculate Visit Duration
    function calculateDuration(checkInTime) {
      const now = new Date();
      const checkIn = new Date(checkInTime);
      const diff = now - checkIn;
      
      const hours = Math.floor(diff / (1000 * 60 * 60));
      const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      
      if (hours > 0) {
        return `${hours}h ${minutes}m`;
      } else {
        return `${minutes}m`;
      }
    }

    // Check In Visitor
    function checkInVisitor() {
      Swal.fire({
        title: 'Check In Visitor',
        text: `Check in ${currentVisitor.first_name} ${currentVisitor.last_name}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        confirmButtonText: 'Yes, Check In'
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            const response = await fetch('api/visitors.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({
                action: 'check_in',
                visitor_id: currentVisitor.visitor_id
              })
            });
            const data = await response.json();
            
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Checked In Successfully',
                text: 'Visitor has been checked in.',
                timer: 2000,
                showConfirmButton: false
              });
              displayVisitorInfo({ ...data, visitor: currentVisitor, active_log: data.log });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Check In Failed',
                text: data.message || 'Failed to check in visitor.',
                confirmButtonColor: '#059669'
              });
            }
          } catch (error) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Failed to check in visitor.',
              confirmButtonColor: '#059669'
            });
          }
        }
      });
    }

    // Check Out Visitor
    function checkOutVisitor() {
      Swal.fire({
        title: 'Check Out Visitor',
        text: `Check out ${currentVisitor.first_name} ${currentVisitor.last_name}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        confirmButtonText: 'Yes, Check Out'
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            const response = await fetch('api/visitors.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({
                action: 'check_out',
                log_id: currentVisitor.active_log.log_id
              })
            });
            const data = await response.json();
            
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Checked Out Successfully',
                text: 'Visitor has been checked out.',
                timer: 2000,
                showConfirmButton: false
              });
              displayVisitorInfo({ visitor: currentVisitor, active_log: null });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Check Out Failed',
                text: data.message || 'Failed to check out visitor.',
                confirmButtonColor: '#059669'
              });
            }
          } catch (error) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Failed to check out visitor.',
              confirmButtonColor: '#059669'
            });
          }
        }
      });
    }

    // View Visitor Details
    function viewDetails() {
      // This would open a detailed view or navigate to the main system
      Swal.fire({
        title: 'Full Details',
        text: 'This would open the detailed visitor management interface.',
        icon: 'info',
        confirmButtonColor: '#059669'
      });
    }

    // Initialize scanner when page loads
    document.addEventListener('DOMContentLoaded', () => {
      initScanner();
    });

    // Handle Enter key in manual input
    document.getElementById('manual-code').addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        lookupManualCode();
      }
    });
  </script>
</body>
</html>
