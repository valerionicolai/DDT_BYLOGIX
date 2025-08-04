<div x-data="authStateManager" x-init="init()" class="hidden">
    <!-- This component manages authentication state globally and is typically hidden -->
    
    <!-- Session Timeout Warning Modal -->
    <div 
        x-show="showTimeoutWarning" 
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Session Timeout Warning
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Your session will expire in <span x-text="timeoutCountdown"></span> seconds due to inactivity. 
                                Would you like to extend your session?
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button 
                        @click="extendSession()"
                        type="button" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Extend Session
                    </button>
                    <button 
                        @click="logout()"
                        type="button" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
                    >
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div 
        x-show="loading" 
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-gray-500 bg-opacity-75 flex items-center justify-center"
        style="display: none;"
    >
        <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
            <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-900">Authenticating...</span>
        </div>
    </div>
</div>

@script
<script>
    Alpine.data('authStateManager', () => ({
        showTimeoutWarning: false,
        timeoutCountdown: 60,
        timeoutWarningTimer: null,
        timeoutTimer: null,
        loading: false,

        init() {
            // Initialize authentication state
            this.initializeAuth();
            
            // Start session monitoring
            this.startSessionMonitoring();
            
            // Listen for user activity
            this.setupActivityListeners();
            
            // Listen for Livewire events
            this.setupLivewireListeners();
        },

        initializeAuth() {
            // Trigger Livewire method to initialize authentication
            $wire.call('initializeAuth');
        },

        startSessionMonitoring() {
            // Check session every minute
            setInterval(() => {
                if ($wire.isAuthenticated) {
                    $wire.call('checkSessionTimeout');
                }
            }, 60000); // 1 minute
        },

        setupActivityListeners() {
            // Track user activity
            const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
            
            events.forEach(event => {
                document.addEventListener(event, () => {
                    if ($wire.isAuthenticated) {
                        $wire.call('updateLastActivity');
                    }
                }, { passive: true });
            });
        },

        setupLivewireListeners() {
            // Listen for authentication events
            $wire.on('auth-success', (event) => {
                console.log('Authentication successful:', event);
                this.loading = false;
                
                // Show success notification
                if (window.showNotification) {
                    window.showNotification('Authentication successful!', 'success');
                }
            });

            $wire.on('auth-error', (event) => {
                console.log('Authentication error:', event);
                this.loading = false;
                
                // Show error notification
                if (window.showNotification) {
                    window.showNotification(event.message || 'Authentication failed', 'error');
                }
            });

            $wire.on('session-timeout-warning', (event) => {
                console.log('Session timeout warning:', event);
                this.showTimeoutWarning = true;
                this.timeoutCountdown = event.countdown || 60;
                this.startTimeoutCountdown();
            });

            $wire.on('session-expired', (event) => {
                console.log('Session expired:', event);
                this.showTimeoutWarning = false;
                this.logout();
                
                // Show session expired notification
                if (window.showNotification) {
                    window.showNotification('Your session has expired. Please log in again.', 'warning');
                }
            });

            $wire.on('token-refreshed', (event) => {
                console.log('Token refreshed:', event);
                
                // Show success notification
                if (window.showNotification) {
                    window.showNotification('Session extended successfully!', 'success');
                }
            });

            $wire.on('logout-success', (event) => {
                console.log('Logout successful:', event);
                this.loading = false;
                
                // Redirect to login page
                window.location.href = '/login';
            });

            $wire.on('loading-start', () => {
                this.loading = true;
            });

            $wire.on('loading-end', () => {
                this.loading = false;
            });
        },

        startTimeoutCountdown() {
            this.timeoutWarningTimer = setInterval(() => {
                this.timeoutCountdown--;
                
                if (this.timeoutCountdown <= 0) {
                    clearInterval(this.timeoutWarningTimer);
                    this.showTimeoutWarning = false;
                    this.logout();
                }
            }, 1000);
        },

        extendSession() {
            clearInterval(this.timeoutWarningTimer);
            this.showTimeoutWarning = false;
            this.loading = true;
            
            // Call Livewire method to refresh token
            $wire.call('refreshToken').then(() => {
                this.loading = false;
            }).catch(() => {
                this.loading = false;
                this.logout();
            });
        },

        logout() {
            clearInterval(this.timeoutWarningTimer);
            this.showTimeoutWarning = false;
            this.loading = true;
            
            // Call Livewire method to logout
            $wire.call('logout');
        }
    }));

    // Global authentication helper functions
    window.authState = {
        isAuthenticated: () => $wire.isAuthenticated,
        getUser: () => $wire.user,
        hasRole: (role) => $wire.call('hasRole', role),
        hasPermission: (permission) => $wire.call('hasPermission', permission),
        logout: () => Alpine.store('authStateManager').logout()
    };

    // Global notification system
    window.showNotification = function(message, type = 'info', duration = 5000) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden transform transition-all duration-300 ease-in-out`;
        
        // Set notification content based on type
        let iconColor, bgColor, textColor;
        switch (type) {
            case 'success':
                iconColor = 'text-green-400';
                bgColor = 'bg-green-50';
                textColor = 'text-green-800';
                break;
            case 'error':
                iconColor = 'text-red-400';
                bgColor = 'bg-red-50';
                textColor = 'text-red-800';
                break;
            case 'warning':
                iconColor = 'text-yellow-400';
                bgColor = 'bg-yellow-50';
                textColor = 'text-yellow-800';
                break;
            default:
                iconColor = 'text-blue-400';
                bgColor = 'bg-blue-50';
                textColor = 'text-blue-800';
        }
        
        notification.innerHTML = `
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
                              type === 'error' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
                              type === 'warning' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>' :
                              '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'}
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium ${textColor}">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // Add to DOM
        document.body.appendChild(notification);
        
        // Auto-remove after duration
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, duration);
    };
</script>
@endscript