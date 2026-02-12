/**
 * Check In/Out Tracking - Purpose Breakdown Functionality
 * Provides comprehensive breakdown and analysis of visitor purposes
 */

// Purpose categories with subcategories
const purposeCategories = {
    'business': {
        name: 'Business Meetings',
        icon: 'briefcase',
        color: '#3B82F6',
        subcategories: [
            'Client Meeting',
            'Partner Discussion',
            'Sales Presentation',
            'Contract Negotiation',
            'Project Review',
            'Strategy Session'
        ]
    },
    'interview': {
        name: 'Interviews',
        icon: 'user-tie',
        color: '#8B5CF6',
        subcategories: [
            'Job Interview',
            'Panel Interview',
            'Technical Assessment',
            'HR Interview',
            'Final Interview',
            'Walk-in Interview'
        ]
    },
    'delivery': {
        name: 'Deliveries & Services',
        icon: 'truck',
        color: '#F59E0B',
        subcategories: [
            'Package Delivery',
            'Equipment Delivery',
            'Document Delivery',
            'Food Delivery',
            'Maintenance Service',
            'Installation Service'
        ]
    },
    'personal': {
        name: 'Personal Visits',
        icon: 'user',
        color: '#10B981',
        subcategories: [
            'Family Visit',
            'Friend Visit',
            'Personal Appointment',
            'Emergency Visit',
            'Pick-up/Drop-off',
            'Other Personal'
        ]
    },
    'official': {
        name: 'Official Business',
        icon: 'building',
        color: '#EF4444',
        subcategories: [
            'Government Visit',
            'Inspection',
            'Audit',
            'Compliance Check',
            'Legal Matter',
            'Regulatory Visit'
        ]
    },
    'maintenance': {
        name: 'Maintenance & Support',
        icon: 'wrench',
        color: '#6B7280',
        subcategories: [
            'IT Support',
            'Facility Maintenance',
            'Equipment Repair',
            'Cleaning Service',
            'Security Check',
            'Utilities Service'
        ]
    }
};

// Initialize purpose breakdown functionality
document.addEventListener('DOMContentLoaded', function() {
    initializePurposeBreakdown();
    setupPurposeFiltering();
    setupTimeAnalysis();
    createPurposeStatistics();
});

function initializePurposeBreakdown() {
    // Create purpose selector if not exists
    createPurposeSelector();
    
    // Setup purpose breakdown modal
    setupPurposeBreakdownModal();
    
    // Initialize purpose statistics
    updatePurposeStatistics();
}

function createPurposeSelector() {
    const purposeSelect = document.getElementById('purpose');
    if (!purposeSelect) return;

    // Clear existing options
    purposeSelect.innerHTML = '<option value="">Select Purpose...</option>';

    // Add categorized options
    Object.entries(purposeCategories).forEach(([key, category]) => {
        // Add category group
        const optgroup = document.createElement('optgroup');
        optgroup.label = category.name;
        
        // Add subcategories
        category.subcategories.forEach(subcategory => {
            const option = document.createElement('option');
            option.value = subcategory;
            option.textContent = subcategory;
            option.dataset.category = key;
            optgroup.appendChild(option);
        });
        
        purposeSelect.appendChild(optgroup);
    });

    // Add change event listener
    purposeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const category = selectedOption.dataset.category;
        updatePurposeCategoryDisplay(category);
    });
}

function updatePurposeCategoryDisplay(category) {
    const categoryDisplay = document.getElementById('purpose-category-display');
    if (!categoryDisplay) return;

    if (category && purposeCategories[category]) {
        const cat = purposeCategories[category];
        categoryDisplay.innerHTML = `
            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" 
                 style="background-color: ${cat.color}20; color: ${cat.color};">
                <i class="fas fa-${cat.icon} mr-2"></i>
                ${cat.name}
            </div>
        `;
    } else {
        categoryDisplay.innerHTML = '';
    }
}

function setupPurposeBreakdownModal() {
    // Create modal if it doesn't exist
    if (!document.getElementById('purposeBreakdownModal')) {
        createPurposeBreakdownModal();
    }

    // Setup modal triggers
    const breakdownBtn = document.getElementById('purposeBreakdownBtn');
    const modal = document.getElementById('purposeBreakdownModal');
    
    if (breakdownBtn && modal) {
        breakdownBtn.addEventListener('click', () => {
            showPurposeBreakdown();
        });
    }
}

function createPurposeBreakdownModal() {
    const modalHTML = `
        <div id="purposeBreakdownModal" class="modal hidden" aria-modal="true" role="dialog">
            <div class="bg-white rounded-2xl shadow-2xl max-w-4xl max-w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-white flex items-center">
                            <i class="fas fa-chart-pie mr-3"></i>
                            Purpose Breakdown Analysis
                        </h2>
                        <button id="closePurposeBreakdownModal" class="text-white hover:bg-white/20 rounded-lg p-2 transition">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <p class="text-white/80 mt-2">Detailed analysis of visitor purposes and time distribution</p>
                </div>
                
                <div class="p-6">
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6" id="purposeSummaryCards">
                        <!-- Cards will be populated dynamically -->
                    </div>
                    
                    <!-- Charts Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Purpose Distribution Chart -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h3 class="font-semibold text-gray-900 mb-4">Purpose Distribution</h3>
                            <div id="purposeChart" class="h-64 flex items-center justify-center">
                                <canvas id="purposePieChart"></canvas>
                            </div>
                        </div>
                        
                        <!-- Time Analysis Chart -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h3 class="font-semibold text-gray-900 mb-4">Average Duration by Purpose</h3>
                            <div id="timeChart" class="h-64 flex items-center justify-center">
                                <canvas id="timeBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detailed Breakdown Table -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 mb-4">Detailed Purpose Breakdown</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Visitors</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Percentage</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Duration</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Time</th>
                                    </tr>
                                </thead>
                                <tbody id="purposeBreakdownTable" class="bg-white divide-y divide-gray-200">
                                    <!-- Table rows will be populated dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Setup modal close event
    document.getElementById('closePurposeBreakdownModal').addEventListener('click', () => {
        closePurposeBreakdownModal();
    });
    
    // Close modal when clicking outside
    document.getElementById('purposeBreakdownModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePurposeBreakdownModal();
        }
    });
}

function showPurposeBreakdown() {
    const modal = document.getElementById('purposeBreakdownModal');
    if (!modal) return;
    
    // Update modal content
    updatePurposeBreakdownContent();
    
    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('active');
    modal.style.display = 'flex';
}

function closePurposeBreakdownModal() {
    const modal = document.getElementById('purposeBreakdownModal');
    if (!modal) return;
    
    modal.classList.remove('active');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

function updatePurposeBreakdownContent() {
    // Get visitor data from tables
    const visitorData = extractVisitorData();
    
    // Calculate purpose statistics
    const purposeStats = calculatePurposeStatistics(visitorData);
    
    // Update summary cards
    updateSummaryCards(purposeStats);
    
    // Update charts
    updatePurposeChart(purposeStats);
    updateTimeChart(purposeStats);
    
    // Update breakdown table
    updateBreakdownTable(purposeStats);
}

function extractVisitorData() {
    const visitors = [];
    
    // Extract from check-ins table
    const checkinRows = document.querySelectorAll('#checkinsTable tbody tr');
    checkinRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 6) {
            const purpose = cells[5]?.textContent?.trim() || 'Unknown';
            const duration = cells[4]?.textContent?.trim() || '0 minutes';
            
            visitors.push({
                purpose: purpose,
                duration: parseDuration(duration),
                type: 'checkin'
            });
        }
    });
    
    // Extract from check-outs table
    const checkoutRows = document.querySelectorAll('#checkoutsTable tbody tr');
    checkoutRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 7) {
            const purpose = cells[6]?.textContent?.trim() || 'Unknown';
            const duration = cells[7]?.textContent?.trim() || '0 minutes';
            
            visitors.push({
                purpose: purpose,
                duration: parseDuration(duration),
                type: 'checkout'
            });
        }
    });
    
    return visitors;
}

function parseDuration(durationText) {
    // Parse duration like "2 hours 30 minutes" or "45 minutes"
    const hourMatch = durationText.match(/(\\d+)\\s*hour/);
    const minuteMatch = durationText.match(/(\\d+)\\s*minute/);
    
    const hours = hourMatch ? parseInt(hourMatch[1]) : 0;
    const minutes = minuteMatch ? parseInt(minuteMatch[1]) : 0;
    
    return hours * 60 + minutes; // Return total minutes
}

function calculatePurposeStatistics(visitorData) {
    const stats = {};
    let totalVisitors = 0;
    let totalDuration = 0;
    
    visitorData.forEach(visitor => {
        const purpose = visitor.purpose;
        const duration = visitor.duration;
        
        if (!stats[purpose]) {
            stats[purpose] = {
                count: 0,
                totalDuration: 0,
                category: getCategoryForPurpose(purpose)
            };
        }
        
        stats[purpose].count++;
        stats[purpose].totalDuration += duration;
        totalVisitors++;
        totalDuration += duration;
    });
    
    // Calculate percentages and averages
    Object.keys(stats).forEach(purpose => {
        const stat = stats[purpose];
        stat.percentage = totalVisitors > 0 ? (stat.count / totalVisitors) * 100 : 0;
        stat.averageDuration = stat.count > 0 ? stat.totalDuration / stat.count : 0;
        stat.totalDurationFormatted = formatDuration(stat.totalDuration);
        stat.averageDurationFormatted = formatDuration(stat.averageDuration);
    });
    
    return {
        purposes: stats,
        totalVisitors: totalVisitors,
        totalDuration: totalDuration,
        totalDurationFormatted: formatDuration(totalDuration)
    };
}

function getCategoryForPurpose(purpose) {
    for (const [categoryKey, category] of Object.entries(purposeCategories)) {
        if (category.subcategories.includes(purpose)) {
            return categoryKey;
        }
    }
    return 'other';
}

function formatDuration(minutes) {
    if (minutes < 60) {
        return `${Math.round(minutes)} minute${minutes !== 1 ? 's' : ''}`;
    } else {
        const hours = Math.floor(minutes / 60);
        const remainingMinutes = Math.round(minutes % 60);
        if (remainingMinutes === 0) {
            return `${hours} hour${hours !== 1 ? 's' : ''}`;
        } else {
            return `${hours} hour${hours !== 1 ? 's' : ''} ${remainingMinutes} minute${remainingMinutes !== 1 ? 's' : ''}`;
        }
    }
}

function updateSummaryCards(stats) {
    const container = document.getElementById('purposeSummaryCards');
    if (!container) return;
    
    // Find top purpose
    let topPurpose = null;
    let maxCount = 0;
    
    Object.entries(stats.purposes).forEach(([purpose, stat]) => {
        if (stat.count > maxCount) {
            maxCount = stat.count;
            topPurpose = purpose;
        }
    });
    
    const categoryKey = topPurpose ? stats.purposes[topPurpose].category : 'other';
    const category = purposeCategories[categoryKey] || { name: 'Other', icon: 'question', color: '#6B7280' };
    
    container.innerHTML = `
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Visitors</p>
                    <p class="text-2xl font-bold text-gray-900">${stats.totalVisitors}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Top Purpose</p>
                    <p class="text-lg font-bold text-gray-900">${topPurpose || 'N/A'}</p>
                    <p class="text-sm text-gray-500">${maxCount} visitors</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: ${category.color}20;">
                    <i class="fas fa-${category.icon}" style="color: ${category.color};"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Time</p>
                    <p class="text-2xl font-bold text-gray-900">${stats.totalDurationFormatted}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-green-600"></i>
                </div>
            </div>
        </div>
    `;
}

function updatePurposeChart(stats) {
    const canvas = document.getElementById('purposePieChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const purposes = Object.entries(stats.purposes).slice(0, 6); // Top 6 purposes
    
    // Simple pie chart representation
    const colors = ['#3B82F6', '#8B5CF6', '#F59E0B', '#10B981', '#EF4444', '#6B7280'];
    
    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // Draw simple chart
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const radius = Math.min(centerX, centerY) - 20;
    
    let currentAngle = -Math.PI / 2;
    
    purposes.forEach(([purpose, stat], index) => {
        const sliceAngle = (stat.percentage / 100) * 2 * Math.PI;
        
        // Draw slice
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + sliceAngle);
        ctx.lineTo(centerX, centerY);
        ctx.fillStyle = colors[index % colors.length];
        ctx.fill();
        
        currentAngle += sliceAngle;
    });
}

function updateTimeChart(stats) {
    const canvas = document.getElementById('timeBarChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const purposes = Object.entries(stats.purposes).slice(0, 5); // Top 5 purposes
    
    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // Draw simple bar chart
    const barWidth = canvas.width / (purposes.length * 2);
    const maxDuration = Math.max(...purposes.map(([_, stat]) => stat.averageDuration));
    
    purposes.forEach(([purpose, stat], index) => {
        const barHeight = (stat.averageDuration / maxDuration) * (canvas.height - 40);
        const x = (index * 2 + 0.5) * barWidth;
        const y = canvas.height - barHeight - 20;
        
        ctx.fillStyle = '#3B82F6';
        ctx.fillRect(x, y, barWidth, barHeight);
        
        // Add label
        ctx.fillStyle = '#374151';
        ctx.font = '10px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(purpose.substring(0, 10) + '...', x + barWidth / 2, canvas.height - 5);
    });
}

function updateBreakdownTable(stats) {
    const tbody = document.getElementById('purposeBreakdownTable');
    if (!tbody) return;
    
    const sortedPurposes = Object.entries(stats.purposes).sort((a, b) => b[1].count - a[1].count);
    
    tbody.innerHTML = sortedPurposes.map(([purpose, stat]) => {
        const category = purposeCategories[stat.category] || { name: 'Other', icon: 'question', color: '#6B7280' };
        
        return `
            <tr>
                <td class="px-4 py-3 text-sm font-medium text-gray-900">${purpose}</td>
                <td class="px-4 py-3 text-sm text-gray-500">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" 
                         style="background-color: ${category.color}20; color: ${category.color};">
                        <i class="fas fa-${category.icon} mr-1"></i>
                        ${category.name}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-900">${stat.count}</td>
                <td class="px-4 py-3 text-sm text-gray-900">${stat.percentage.toFixed(1)}%</td>
                <td class="px-4 py-3 text-sm text-gray-900">${stat.averageDurationFormatted}</td>
                <td class="px-4 py-3 text-sm text-gray-900">${stat.totalDurationFormatted}</td>
            </tr>
        `;
    }).join('');
}

function setupPurposeFiltering() {
    // Add purpose filter buttons to the interface
    const filterContainer = document.querySelector('.mb-6.bg-white.rounded-2xl');
    if (!filterContainer) return;
    
    const purposeFilterHTML = `
        <div class="mt-4 pt-4 border-t border-gray-200">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Filter by Purpose Category</h4>
            <div class="flex flex-wrap gap-2" id="purposeFilterButtons">
                <button class="purpose-filter-btn px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition" data-category="all">
                    All Purposes
                </button>
                ${Object.entries(purposeCategories).map(([key, category]) => `
                    <button class="purpose-filter-btn px-3 py-1 rounded-full text-xs font-medium hover:bg-gray-200 transition" 
                            data-category="${key}" 
                            style="background-color: ${category.color}20; color: ${category.color};">
                        <i class="fas fa-${category.icon} mr-1"></i>
                        ${category.name}
                    </button>
                `).join('')}
            </div>
        </div>
    `;
    
    filterContainer.insertAdjacentHTML('beforeend', purposeFilterHTML);
    
    // Setup filter event listeners
    document.querySelectorAll('.purpose-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.dataset.category;
            filterByPurposeCategory(category);
            
            // Update active state
            document.querySelectorAll('.purpose-filter-btn').forEach(b => {
                b.classList.remove('ring-2', 'ring-offset-2');
            });
            this.classList.add('ring-2', 'ring-offset-2');
            this.style.setProperty('ring-color', purposeCategories[category]?.color || '#6B7280');
        });
    });
}

function filterByPurposeCategory(category) {
    const checkinRows = document.querySelectorAll('#checkinsTable tbody tr');
    const checkoutRows = document.querySelectorAll('#checkoutsTable tbody tr');
    
    // Filter check-ins
    checkinRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 6) {
            const purpose = cells[5]?.textContent?.trim() || '';
            const purposeCategory = getCategoryForPurpose(purpose);
            
            if (category === 'all' || purposeCategory === category) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
    
    // Filter check-outs
    checkoutRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 7) {
            const purpose = cells[6]?.textContent?.trim() || '';
            const purposeCategory = getCategoryForPurpose(purpose);
            
            if (category === 'all' || purposeCategory === category) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}

function setupTimeAnalysis() {
    // Add time analysis features
    addTimeAnalysisToStats();
    setupRealTimeUpdates();
}

function addTimeAnalysisToStats() {
    // Add time breakdown to existing stats cards
    const statsContainer = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-2.gap-6.mb-8');
    if (!statsContainer) return;
    
    const timeAnalysisHTML = `
        <div class="group relative bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-orange-50 to-orange-100 rounded-full -mr-12 -mt-12 opacity-50 group-hover:opacity-75 transition-opacity"></div>
            <div class="relative flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-gray-600 font-semibold text-base mb-3">Purpose Breakdown</p>
                    <p class="font-bold text-4xl text-gray-900 mb-2" id="purposeBreakdownCount">0</p>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                            <i class="fas fa-chart-pie mr-2"></i>
                            Categories
                        </span>
                        <button id="purposeBreakdownBtn" class="text-sm text-brand-primary hover:text-brand-primary-hover font-medium">
                            View Details →
                        </button>
                    </div>
                </div>
                <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-chart-pie text-white text-2xl"></i>
                </div>
            </div>
        </div>
    `;
    
    statsContainer.insertAdjacentHTML('beforeend', timeAnalysisHTML);
    
    // Update purpose breakdown count
    updatePurposeBreakdownCount();
}

function updatePurposeBreakdownCount() {
    const visitorData = extractVisitorData();
    const uniquePurposes = new Set(visitorData.map(v => v.purpose));
    const countElement = document.getElementById('purposeBreakdownCount');
    
    if (countElement) {
        countElement.textContent = uniquePurposes.size;
    }
}

function setupRealTimeUpdates() {
    // Update purpose statistics every 30 seconds
    setInterval(() => {
        updatePurposeStatistics();
        updatePurposeBreakdownCount();
    }, 30000);
}

function updatePurposeStatistics() {
    // This function would typically fetch updated data from the server
    // For now, we'll recalculate from existing data
    const visitorData = extractVisitorData();
    const stats = calculatePurposeStatistics(visitorData);
    
    // Update any displays that show purpose statistics
    console.log('Purpose statistics updated:', stats);
}

function createPurposeStatistics() {
    // Initialize purpose statistics on page load
    updatePurposeStatistics();
    updatePurposeBreakdownCount();
}

// Export functions for global access
window.purposeBreakdown = {
    showBreakdown: showPurposeBreakdown,
    updateStatistics: updatePurposeStatistics,
    filterByCategory: filterByPurposeCategory,
    categories: purposeCategories
};
