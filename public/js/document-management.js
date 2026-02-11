/**
 * Document Management System - Core JavaScript (Fixed Persistence)
 * Handles financial data, document approvals, budget tracking, and API syncing
 */

// ============================================
// LOADING SCREEN FUNCTIONS
// ============================================
window.showLoadingScreen = function () {
    const loadingScreen = document.getElementById('loadingScreen');
    if (loadingScreen) {
        loadingScreen.classList.remove('hidden');
        setTimeout(() => loadingScreen.classList.add('opacity-100'), 10);
    }
};

window.hideLoadingScreen = function () {
    const loadingScreen = document.getElementById('loadingScreen');
    const mainContent = document.getElementById('mainContent');
    if (loadingScreen) {
        loadingScreen.classList.add('opacity-0');
        setTimeout(() => {
            loadingScreen.classList.add('hidden');
            if (mainContent) mainContent.style.opacity = '1';
        }, 300);
    }
};

// ============================================
// LOCAL STORAGE PERSISTENCE (CRITICAL FIX)
// ============================================
function storeStatusChange(refNo, newStatus) {
    if (!refNo) {
        console.warn('⚠️ Cannot store status without ref_no');
        return;
    }

    let storedChanges = {};
    try {
        storedChanges = JSON.parse(localStorage.getItem('financialStatusChanges') || '{}');
    } catch (e) {
        console.error('Error reading localStorage:', e);
        storedChanges = {};
    }

    const normalizedRefNo = String(refNo).trim();
    storedChanges[normalizedRefNo] = newStatus;

    localStorage.setItem('financialStatusChanges', JSON.stringify(storedChanges));
    console.log(`✅ Stored status change: ${normalizedRefNo} -> ${newStatus}`);
    console.log('Current storage:', storedChanges);
}

async function persistStatusOverrideToServer(refNo, newStatus) {
    if (!refNo) return false;
    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await fetch(`/api/financial-proposals/${encodeURIComponent(String(refNo).trim())}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...(token ? { 'X-CSRF-TOKEN': token } : {})
            },
            body: JSON.stringify({ status: newStatus })
        });

        if (!response.ok) return false;
        const data = await response.json().catch(() => null);
        return !!(data && data.success);
    } catch (e) {
        console.warn('Failed to persist override to server:', e);
        return false;
    }
}

async function fetchServerStatusOverrides() {
    try {
        const response = await fetch('/api/financial-proposals/status-overrides', {
            headers: { 'Accept': 'application/json' }
        });
        if (!response.ok) return {};
        const data = await response.json().catch(() => ({}));
        if (data && data.success && data.overrides && typeof data.overrides === 'object') {
            return data.overrides;
        }
        return {};
    } catch (e) {
        console.warn('Failed to fetch server overrides:', e);
        return {};
    }
}

function applyStoredStatusChangesToData(data) {
    if (!data || !Array.isArray(data)) return data;

    let storedChanges = {};
    try {
        storedChanges = JSON.parse(localStorage.getItem('financialStatusChanges') || '{}');
        console.log('📦 Applying stored changes:', storedChanges);
    } catch (e) {
        console.error('Error parsing stored changes:', e);
        storedChanges = {};
    }

    data.forEach((item, index) => {
        if (!item.ref_no) {
            console.warn(`⚠️ Item at index ${index} missing ref_no`);
            return;
        }

        const refNo = String(item.ref_no).trim();

        if (storedChanges[refNo]) {
            const newStatus = storedChanges[refNo];
            console.log(`✅ Applying stored status: ${refNo} -> ${newStatus} (was: ${item.status})`);
            item.status = newStatus;
        }
    });

    return data;
}

// ============================================
// BUDGET & STATS CALCULATIONS
// ============================================
window.updateStatsCards = function () {
    const tbody = document.querySelector('#documentsTable tbody');
    if (!tbody) return;

    // Count regular rows
    const totalRows = tbody.querySelectorAll('tr.document-row:not(.no-results-row)').length;
    const totalDocsCount = document.getElementById('totalDocumentsCount');
    if (totalDocsCount) totalDocsCount.innerText = totalRows;

    // Total allocated and available (from financial data)
    let totalAllocated = 0;
    let totalAvailable = 0;

    if (window.financialData && Array.isArray(window.financialData)) {
        window.financialData.forEach(item => {
            const amount = parseFloat(item.amount || 0);
            if (item.status && item.status.toLowerCase() === 'approved') {
                totalAllocated += amount;
            } else if (item.status && item.status.toLowerCase() === 'pending') {
                totalAvailable += amount;
            }
        });
    }

    const allocatedPill = document.getElementById('totalAllocatedAmount');
    if (allocatedPill) allocatedPill.innerText = '₱' + totalAllocated.toLocaleString();

    const availablePill = document.getElementById('totalAvailableAmount');
    if (availablePill) availablePill.innerText = '₱' + totalAvailable.toLocaleString();

    console.log(`📊 Stats updated - Allocated: ₱${totalAllocated.toLocaleString()}, Available: ₱${totalAvailable.toLocaleString()}`);
};

// ============================================
// API INTEGRATION (FETCHING)
// ============================================
window.fetchFinancialData = async function () {
    console.log('=== 🔄 FETCHING FINANCIAL DATA ===');
    try {
        const table = document.querySelector('#documentsTable');
        const tbody = document.querySelector('#documentsTable tbody');

        if (!table || !tbody) {
            console.error('❌ Table element not found');
            return;
        }

        // Use timestamp to prevent caching
        const timestamp = new Date().getTime();
        const url = `https://finance.microfinancial-1.com/api/manage_proposals.php?action=list_view&_=${timestamp}`;

        console.log(`📡 Fetching from: ${url}`);

        const response = await fetch(url, {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });

        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

        const data = await response.json();
        console.log('📥 API Response:', data);

        let financialData = null;

        // Extract from various possible response structures
        if (data && data.success === true && data.data && Array.isArray(data.data)) {
            financialData = data.data;
            console.log('✅ Using data.data structure');
        } else if (data && Array.isArray(data)) {
            financialData = data;
            console.log('✅ Using direct array structure');
        } else if (data && data.proposals && Array.isArray(data.proposals)) {
            financialData = data.proposals;
            console.log('✅ Using data.proposals structure');
        }

        if (financialData && financialData.length > 0) {
            console.log(`📊 Received ${financialData.length} financial items`);

            const serverOverrides = await fetchServerStatusOverrides();

            // Normalize data: ensure every item has ref_no and status
            financialData.forEach((item, index) => {
                if (!item.ref_no) {
                    item.ref_no = `PROP-${Date.now()}-${index}`;
                    console.warn(`Generated ref_no for item ${index}: ${item.ref_no}`);
                }
                if (!item.status) {
                    item.status = 'Pending';
                }

                const refNo = String(item.ref_no).trim();
                if (serverOverrides[refNo]) {
                    item.status = serverOverrides[refNo];
                }
            });

            // Apply persistence BEFORE storing globally
            const modifiedData = applyStoredStatusChangesToData(financialData);
            window.financialData = modifiedData;

            console.log('📦 Final data to display:', modifiedData);

            // Display & Update UI
            displayFinancialData(modifiedData);
            updateStatsCards();
        } else {
            console.warn('⚠️ No financial data found');
        }
    } catch (error) {
        console.error('❌ Integration Error:', error);
    } finally {
        hideLoadingScreen();
    }
};

// ============================================
// API INTEGRATION (SYNCING STATUS)
// ============================================
async function updateFinancialStatusAPI(refNo, newStatus) {
    console.group(`🔄 Syncing to API: ${refNo} -> ${newStatus}`);
    try {
        const action = newStatus.toLowerCase() === 'approved' ? 'approve' : 'reject';

        const patterns = [
            {
                name: 'JSON POST',
                url: `https://finance.microfinancial-1.com/api/manage_proposals.php`,
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ action, reference_id: refNo, status: newStatus })
            },
            {
                name: 'Form POST',
                url: `https://finance.microfinancial-1.com/api/manage_proposals.php`,
                method: 'POST',
                body: new URLSearchParams({ action, reference_id: refNo, status: newStatus })
            }
        ];

        for (const p of patterns) {
            try {
                console.log(`📡 Trying ${p.name}...`);
                const response = await fetch(p.url, {
                    method: p.method,
                    headers: p.headers || { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: p.body
                });

                const text = await response.text();
                console.log(`Response: ${text.substring(0, 200)}...`);

                let result;
                try { result = JSON.parse(text); } catch (e) { if (response.ok) result = { success: true }; }

                if (result && (result.success === true || result.status === 'success' || text.toLowerCase().includes('successfully'))) {
                    console.log('✅ API Sync Successful');
                    console.groupEnd();
                    return true;
                }
            } catch (e) {
                console.warn(`Failed: ${e.message}`);
                continue;
            }
        }
        console.warn('❌ All sync attempts failed');
        console.groupEnd();
        return false;
    } catch (error) {
        console.error('❌ Fatal Sync Error:', error);
        console.groupEnd();
        return false;
    }
}

// ============================================
// UI DISPLAY (CRITICAL FIX)
// ============================================
function displayFinancialData(data) {
    const tbody = document.querySelector('#documentsTable tbody');
    if (!tbody) {
        console.error('❌ Table body not found');
        return;
    }

    console.log(`🎨 Displaying ${data.length} financial items`);

    // Remove old financial rows
    const oldRows = tbody.querySelectorAll('.financial-data-row');
    console.log(`🗑️ Removing ${oldRows.length} old rows`);
    oldRows.forEach(row => row.remove());

    // Create new rows
    data.forEach((item, index) => {
        const row = document.createElement('tr');
        row.className = 'document-row financial-data-row fade-in';
        row.setAttribute('data-doc-id', item.ref_no);
        row.setAttribute('data-index', index);

        const status = item.status || 'Pending';
        const statusLower = status.toLowerCase();

        let sClass = 'bg-yellow-100 text-yellow-800';
        let sIcon = 'bx-time-five';

        if (statusLower === 'approved') {
            sClass = 'bg-emerald-100 text-emerald-800';
            sIcon = 'bx-check-circle';
        } else if (statusLower === 'rejected') {
            sClass = 'bg-red-100 text-red-800';
            sIcon = 'bx-x-circle';
        }

        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <i class='bx bxs-file text-emerald-500 text-xl mr-3'></i>
                    <div>
                        <div class="text-sm font-medium text-gray-900">${item.project || 'Proposal'}</div>
                        <div class="text-xs text-gray-500">Ref: ${item.ref_no} • ${item.department || 'N/A'}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">Financial Proposal</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                    <i class="bx bx-dollar-circle mr-1"></i>Financial
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.date_posted || 'N/A'}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${sClass}">
                    <i class="bx ${sIcon} mr-1"></i>${status}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button onclick="viewFinancialDetails(${index})" class="text-blue-600 hover:text-blue-900 mr-2" title="View">
                    <i class="bx bx-show"></i>
                </button>
                <button onclick="approveFinancialDocument(${index})" class="text-emerald-600 hover:text-emerald-800 mr-2" title="Approve">
                    <i class="bx bx-check-circle"></i>
                </button>
                <button onclick="rejectFinancialDocument(${index})" class="text-red-600 hover:text-red-800" title="Reject">
                    <i class="bx bx-x-circle"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });

    console.log(`✅ Created ${data.length} new rows`);
}

// ============================================
// DOCUMENT ACTIONS (CRITICAL FIX)
// ============================================
window.viewFinancialDetails = function (index) {
    const item = window.financialData?.[index];
    if (!item) {
        console.error('❌ Item not found at index:', index);
        return;
    }
    Swal.fire({
        title: 'Financial Proposal Details',
        html: `
            <div style="text-align: left;">
                <p><strong>Reference:</strong> ${item.ref_no}</p>
                <p><strong>Project:</strong> ${item.project || 'N/A'}</p>
                <p><strong>Department:</strong> ${item.department || 'N/A'}</p>
                <p><strong>Amount:</strong> ₱${parseFloat(item.amount || 0).toLocaleString()}</p>
                <p><strong>Status:</strong> ${item.status || 'Pending'}</p>
                <p><strong>Date Posted:</strong> ${item.date_posted || 'N/A'}</p>
            </div>
        `,
        icon: 'info',
        confirmButtonColor: '#059669'
    });
};

window.approveFinancialDocument = async function (index) {
    const item = window.financialData?.[index];
    if (!item) {
        console.error('❌ Item not found at index:', index);
        return;
    }

    console.log(`🔍 Approving item:`, item);

    const result = await Swal.fire({
        title: 'Approve Proposal?',
        text: `Approve ${item.ref_no}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve',
        confirmButtonColor: '#10b981',
        cancelButtonText: 'Cancel'
    });

    if (result.isConfirmed) {
        console.log(`✅ User confirmed approval for ${item.ref_no}`);

        // Update in memory
        const oldStatus = item.status;
        item.status = 'Approved';

        // Store in localStorage
        storeStatusChange(item.ref_no, 'Approved');

        // Persist override in Laravel (so refresh won't reset)
        persistStatusOverrideToServer(item.ref_no, 'Approved');

        // Update UI
        displayFinancialData(window.financialData);
        updateStatsCards();

        console.log(`🎨 UI updated: ${oldStatus} -> Approved`);

        // Sync with API (non-blocking)
        const apiSuccess = await updateFinancialStatusAPI(item.ref_no, 'Approved');

        if (apiSuccess) {
            Swal.fire({
                icon: 'success',
                title: 'Approved!',
                text: 'Status synced with server',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Sync Warning',
                text: 'Approved locally, but server sync failed. Changes saved in browser.',
                confirmButtonColor: '#059669'
            });
        }
    }
};

window.rejectFinancialDocument = async function (index) {
    const item = window.financialData?.[index];
    if (!item) {
        console.error('❌ Item not found at index:', index);
        return;
    }

    console.log(`🔍 Rejecting item:`, item);

    const result = await Swal.fire({
        title: 'Reject Proposal?',
        text: `Reject ${item.ref_no}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Reject',
        confirmButtonColor: '#ef4444',
        cancelButtonText: 'Cancel'
    });

    if (result.isConfirmed) {
        console.log(`✅ User confirmed rejection for ${item.ref_no}`);

        // Update in memory
        const oldStatus = item.status;
        item.status = 'Rejected';

        // Store in localStorage
        storeStatusChange(item.ref_no, 'Rejected');

        // Persist override in Laravel (so refresh won't reset)
        persistStatusOverrideToServer(item.ref_no, 'Rejected');

        // Update UI
        displayFinancialData(window.financialData);
        updateStatsCards();

        console.log(`🎨 UI updated: ${oldStatus} -> Rejected`);

        // Sync with API (non-blocking)
        const apiSuccess = await updateFinancialStatusAPI(item.ref_no, 'Rejected');

        if (apiSuccess) {
            Swal.fire({
                icon: 'success',
                title: 'Rejected',
                text: 'Status synced with server',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Sync Warning',
                text: 'Rejected locally, but server sync failed. Changes saved in browser.',
                confirmButtonColor: '#059669'
            });
        }
    }
};

// ============================================
// INITIALIZATION
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    console.log('🚀 Document Management System Initialized');
    showLoadingScreen();
    fetchFinancialData();
});

// Fallback safety
window.addEventListener('load', () => {
    setTimeout(() => {
        console.log('⏰ Safety timeout - ensuring loading screen is hidden');
        hideLoadingScreen();
    }, 3000);
});

console.log('✅ document-management.js loaded successfully');
