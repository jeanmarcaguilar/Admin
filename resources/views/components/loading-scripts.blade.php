<!-- Global Loading Screen JavaScript -->
<script>
// Global Loading Screen Controller
window.GlobalLoading = {
    // Show main loading screen
    show: function(message = 'Processing...', progress = false) {
        const screen = document.getElementById('globalLoadingScreen');
        const messageEl = document.getElementById('loadingMessage');
        const progressBar = document.getElementById('loadingProgress');
        
        if (screen) {
            messageEl.textContent = message;
            
            if (progress) {
                progressBar.style.width = '0%';
                progressBar.classList.remove('hidden');
                this.animateProgress();
            } else {
                progressBar.classList.add('hidden');
            }
            
            screen.classList.remove('hidden');
            setTimeout(() => {
                screen.classList.add('show');
            }, 10);
            
            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        }
    },
    
    // Hide main loading screen
    hide: function() {
        const screen = document.getElementById('globalLoadingScreen');
        
        if (screen) {
            screen.classList.remove('show');
            setTimeout(() => {
                screen.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }
    },
    
    // Update loading message
    updateMessage: function(message) {
        const messageEl = document.getElementById('loadingMessage');
        if (messageEl) {
            messageEl.textContent = message;
        }
    },
    
    // Update progress bar
    updateProgress: function(percent) {
        const progressBar = document.getElementById('loadingProgress');
        if (progressBar) {
            progressBar.style.width = percent + '%';
        }
    },
    
    // Animate progress bar
    animateProgress: function() {
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress >= 90) {
                progress = 90;
                clearInterval(interval);
            }
            this.updateProgress(progress);
        }, 200);
        
        // Store interval ID to clear later
        this._progressInterval = interval;
    },
    
    // Complete progress
    completeProgress: function() {
        if (this._progressInterval) {
            clearInterval(this._progressInterval);
        }
        this.updateProgress(100);
        setTimeout(() => {
            this.hide();
        }, 500);
    },
    
    // Show mini loading bar (top of page)
    showMiniBar: function() {
        const bar = document.getElementById('miniLoadingBar');
        if (bar) {
            bar.classList.remove('hidden');
            setTimeout(() => {
                bar.classList.add('show');
            }, 10);
            this.animateMiniBar();
        }
    },
    
    // Hide mini loading bar
    hideMiniBar: function() {
        const bar = document.getElementById('miniLoadingBar');
        const progressBar = document.getElementById('miniProgressBar');
        
        if (bar) {
            if (this._miniBarInterval) {
                clearInterval(this._miniBarInterval);
            }
            progressBar.style.width = '100%';
            setTimeout(() => {
                bar.classList.remove('show');
                setTimeout(() => {
                    bar.classList.add('hidden');
                    progressBar.style.width = '0%';
                }, 300);
            }, 200);
        }
    },
    
    // Animate mini bar
    animateMiniBar: function() {
        let progress = 0;
        const progressBar = document.getElementById('miniProgressBar');
        
        this._miniBarInterval = setInterval(() => {
            progress += Math.random() * 10;
            if (progress >= 85) {
                progress = 85;
                clearInterval(this._miniBarInterval);
            }
            if (progressBar) {
                progressBar.style.width = progress + '%';
            }
        }, 100);
    },
    
    // Show spinner (for buttons/cards)
    showSpinner: function(message = 'Loading...') {
        const spinner = document.getElementById('loadingSpinner');
        const textEl = document.getElementById('spinnerText');
        
        if (spinner) {
            textEl.textContent = message;
            spinner.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    },
    
    // Hide spinner
    hideSpinner: function() {
        const spinner = document.getElementById('loadingSpinner');
        
        if (spinner) {
            spinner.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }
};

// AJAX Helper with Loading
window.AjaxWithLoading = {
    // Perform AJAX request with loading screen
    request: function(options) {
        const defaults = {
            method: 'GET',
            loadingType: 'main', // 'main', 'mini', 'spinner', 'none'
            loadingMessage: 'Loading...',
            showProgress: false,
            timeout: 30000
        };
        
        const config = Object.assign(defaults, options);
        
        // Show loading
        switch (config.loadingType) {
            case 'main':
                GlobalLoading.show(config.loadingMessage, config.showProgress);
                break;
            case 'mini':
                GlobalLoading.showMiniBar();
                break;
            case 'spinner':
                GlobalLoading.showSpinner(config.loadingMessage);
                break;
        }
        
        // Create abort controller for timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), config.timeout);
        
        // Perform request
        return fetch(config.url, {
            method: config.method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                ...(config.headers || {})
            },
            body: config.data ? JSON.stringify(config.data) : undefined,
            signal: controller.signal
        })
        .then(response => {
            clearTimeout(timeoutId);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            // Update progress if enabled
            if (config.showProgress && config.loadingType === 'main') {
                GlobalLoading.updateProgress(75);
            }
            
            return response.json();
        })
        .then(data => {
            // Complete loading
            switch (config.loadingType) {
                case 'main':
                    if (config.showProgress) {
                        GlobalLoading.completeProgress();
                    } else {
                        GlobalLoading.hide();
                    }
                    break;
                case 'mini':
                    GlobalLoading.hideMiniBar();
                    break;
                case 'spinner':
                    GlobalLoading.hideSpinner();
                    break;
            }
            
            // Call success callback
            if (config.success) {
                config.success(data);
            }
            
            return data;
        })
        .catch(error => {
            clearTimeout(timeoutId);
            
            // Hide loading
            switch (config.loadingType) {
                case 'main':
                    GlobalLoading.hide();
                    break;
                case 'mini':
                    GlobalLoading.hideMiniBar();
                    break;
                case 'spinner':
                    GlobalLoading.hideSpinner();
                    break;
            }
            
            // Show error
            console.error('AJAX Error:', error);
            
            if (config.error) {
                config.error(error);
            } else {
                // Default error handling
                if (error.name === 'AbortError') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Request Timeout',
                        text: 'The request took too long to complete. Please try again.',
                        confirmButtonColor: '#059669'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'An error occurred while processing your request.',
                        confirmButtonColor: '#059669'
                    });
                }
            }
            
            throw error;
        });
    },
    
    // GET request
    get: function(url, options = {}) {
        return this.request(Object.assign({ url, method: 'GET' }, options));
    },
    
    // POST request
    post: function(url, data, options = {}) {
        return this.request(Object.assign({ url, method: 'POST', data }, options));
    },
    
    // PUT request
    put: function(url, data, options = {}) {
        return this.request(Object.assign({ url, method: 'PUT', data }, options));
    },
    
    // DELETE request
    delete: function(url, options = {}) {
        return this.request(Object.assign({ url, method: 'DELETE' }, options));
    }
};

// Form submission with loading
window.submitFormWithLoading = function(form, options = {}) {
    const defaults = {
        loadingType: 'main',
        loadingMessage: 'Submitting...',
        showProgress: true,
        resetForm: true,
        showSuccessMessage: true
    };
    
    const config = Object.assign(defaults, options);
    
    // Prevent default submission
    event.preventDefault();
    
    // Get form data
    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });
    
    // Determine request method
    const method = form.method || 'POST';
    
    // Submit with loading
    return AjaxWithLoading.request({
        url: form.action,
        method: method,
        data: data,
        loadingType: config.loadingType,
        loadingMessage: config.loadingMessage,
        showProgress: config.showProgress,
        success: function(response) {
            if (config.resetForm) {
                form.reset();
            }
            
            if (config.showSuccessMessage && response.message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                    confirmButtonColor: '#059669',
                    timer: 2000,
                    timerProgressBar: true
                });
            }
            
            if (config.success) {
                config.success(response);
            }
        },
        error: config.error
    });
};

// Initialize loading screen on page load
document.addEventListener('DOMContentLoaded', function() {
    // Hide any loading screens that might be visible
    GlobalLoading.hide();
    GlobalLoading.hideMiniBar();
    GlobalLoading.hideSpinner();
    
    // Add loading to all forms with data-loading attribute
    const forms = document.querySelectorAll('form[data-loading]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const loadingType = form.getAttribute('data-loading') || 'main';
            const loadingMessage = form.getAttribute('data-loading-message') || 'Submitting...';
            
            submitFormWithLoading(form, {
                loadingType: loadingType,
                loadingMessage: loadingMessage
            });
        });
    });
    
    // Add loading to all links with data-loading attribute
    const links = document.querySelectorAll('a[data-loading]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            const loadingType = this.getAttribute('data-loading') || 'mini';
            const loadingMessage = this.getAttribute('data-loading-message') || 'Loading...';
            
            GlobalLoading.show(loadingMessage, false);
            
            // Hide loading after a short delay to allow page navigation
            setTimeout(() => {
                GlobalLoading.hide();
            }, 2000);
        });
    });
});

// Export for global access
window.LoadingScreen = GlobalLoading;
</script>
