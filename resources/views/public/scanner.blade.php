<x-auth-wrapper>
    <x-slot name="title">Scanner QR Code</x-slot>
    
    <style>
        #scanner-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        
        #scanner-video {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        #scanner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        
        .scanner-frame {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 250px;
            height: 250px;
            border: 2px solid #10b981;
            border-radius: 8px;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.3);
        }
        
        .scanner-corner {
            position: absolute;
            width: 20px;
            height: 20px;
            border: 3px solid #10b981;
        }
        
        .scanner-corner.top-left {
            top: -3px;
            left: -3px;
            border-right: none;
            border-bottom: none;
        }
        
        .scanner-corner.top-right {
            top: -3px;
            right: -3px;
            border-left: none;
            border-bottom: none;
        }
        
        .scanner-corner.bottom-left {
            bottom: -3px;
            left: -3px;
            border-right: none;
            border-top: none;
        }
        
        .scanner-corner.bottom-right {
            bottom: -3px;
            right: -3px;
            border-left: none;
            border-top: none;
        }
    </style>

@auth

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">QR Code Scanner</h1>
        <p class="text-gray-600">Scan a QR code to view material or document information</p>
    </div>

    <div x-data="qrScanner()" x-init="init()" class="space-y-6">
        <!-- Scanner Section -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="text-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Camera Scanner</h2>
                <p class="text-sm text-gray-600">Position the QR code within the frame</p>
            </div>

            <!-- Scanner Container -->
            <div id="scanner-container" class="mb-4">
                <video id="scanner-video" autoplay muted playsinline x-show="cameraActive"></video>
                <div id="scanner-overlay" x-show="cameraActive">
                    <div class="scanner-frame">
                        <div class="scanner-corner top-left"></div>
                        <div class="scanner-corner top-right"></div>
                        <div class="scanner-corner bottom-left"></div>
                        <div class="scanner-corner bottom-right"></div>
                    </div>
                </div>
                
                <!-- Camera not active placeholder -->
                <div x-show="!cameraActive" class="bg-gray-100 rounded-lg p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-gray-500">Camera not active</p>
                </div>
            </div>

            <!-- Controls -->
            <div class="flex justify-center space-x-4">
                <button @click="startCamera()" 
                        x-show="!cameraActive && !permissionDenied"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Start Camera
                </button>
                
                <button x-show="permissionDenied"
                        @click="openCameraSettings()"
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-yellow-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Open Camera Settings
                </button>
                
                <button @click="stopCamera()" 
                        x-show="cameraActive"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                    </svg>
                    Stop Camera
                </button>
            </div>

            <!-- Status Messages -->
            <div x-show="message" class="mt-4 p-4 rounded-md" :class="messageType === 'error' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-700'">
                <p x-text="message"></p>
            </div>
        </div>

        <!-- Manual Input Section -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Manual Input</h2>
            <p class="text-sm text-gray-600 mb-4">If you can't scan, manually enter the QR code</p>
            
            <form @submit.prevent="processManualCode()" class="space-y-4">
                <div>
                    <label for="manual-code" class="block text-sm font-medium text-gray-700">QR Code</label>
                    <input type="text" 
                           id="manual-code" 
                           x-model="manualCode"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter the QR code or ID">
                </div>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search
                </button>
            </form>
        </div>

        <!-- Recent Scans -->
        <div class="bg-white rounded-lg shadow-lg p-6" x-show="recentScans.length > 0">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Scans</h2>
            <div class="space-y-2">
                <template x-for="scan in recentScans" :key="scan.id">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900" x-text="scan.name"></p>
                            <p class="text-sm text-gray-500" x-text="scan.type + ' - ' + scan.time"></p>
                        </div>
                        <a :href="scan.url" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View
                        </a>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@else
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-lg text-center">
    <h1 class="text-2xl font-bold text-red-600 mb-4">Access Denied</h1>
    <p class="text-gray-700 mb-4">To use the QR Code scanner, you need to log in.</p>
    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
        Log in
    </a></div>
</div>
@endauth

    <script src="https://unpkg.com/jsqr@1.4.0/dist/jsQR.js"></script>
    <script>
    function qrScanner() {
        return {
            cameraActive: false,
            permissionDenied: false,
            message: '',
            messageType: 'info',
            manualCode: '',
            recentScans: JSON.parse(localStorage.getItem('recentScans') || '[]'),
            video: null,
            canvas: null,
            context: null,
            scanning: false,

            init() {
                this.canvas = document.createElement('canvas');
                this.context = this.canvas.getContext('2d');
            },

            async startCamera() {
                try {
                    this.message = 'Starting camera...';
                    this.messageType = 'info';
                    
                    // First check if we have permission
                    const permissionStatus = await navigator.permissions.query({ name: 'camera' });
                    
                    if (permissionStatus.state === 'denied') {
                        this.message = 'Camera access denied. Please enable camera permissions in your browser settings.';
                        this.messageType = 'error';
                        this.permissionDenied = true;
                        return;
                    }
                    
                    this.permissionDenied = false;
                    
                    // Show permission prompt if needed
                    const stream = await navigator.mediaDevices.getUserMedia({
                        video: { 
                            facingMode: 'environment',
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        }
                    }).catch(error => {
                        if (error.name === 'NotAllowedError') {
                            this.message = 'Please allow camera access to use the scanner.';
                            this.messageType = 'error';
                        } else {
                            throw error;
                        }
                    });
                    
                    if (!stream) return;
                    
                    this.video = document.getElementById('scanner-video');
                    this.video.srcObject = stream;
                    this.cameraActive = true;
                    this.message = 'Camera active. Position the QR code in the frame.';
                    
                    this.video.addEventListener('loadedmetadata', () => {
                        this.startScanning();
                    });
                    
                } catch (error) {
                    console.error('Camera access error:', error);
                    this.message = 'Unable to access camera: ' + error.message;
                    this.messageType = 'error';
                    
                    // Show instructions for enabling camera
                    if (error.name === 'NotFoundError' || error.name === 'OverconstrainedError') {
                        this.message += ' Please ensure you have a working camera.';
                    }
                }
            },

            stopCamera() {
                if (this.video && this.video.srcObject) {
                    const tracks = this.video.srcObject.getTracks();
                    tracks.forEach(track => track.stop());
                    this.video.srcObject = null;
                }
                this.cameraActive = false;
                this.scanning = false;
                this.message = 'Camera stopped.';
                this.messageType = 'info';
            },

            startScanning() {
                if (!this.scanning) {
                    this.scanning = true;
                    this.scanFrame();
                }
            },

            scanFrame() {
                if (!this.scanning || !this.video || !this.cameraActive) {
                    return;
                }

                if (this.video.readyState === this.video.HAVE_ENOUGH_DATA) {
                    this.canvas.width = this.video.videoWidth;
                    this.canvas.height = this.video.videoHeight;
                    this.context.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);
                    
                    const imageData = this.context.getImageData(0, 0, this.canvas.width, this.canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height);
                    
                    if (code) {
                        this.processQRCode(code.data);
                        return;
                    }
                }
                
                requestAnimationFrame(() => this.scanFrame());
            },

            async processQRCode(qrData) {
                this.scanning = false;
                this.message = 'QR Code detected! Processing...';
                this.messageType = 'info';
                
                try {
                    // Try to process as a direct URL first
                    if (qrData.includes('/public/material/') || qrData.includes('/public/document/')) {
                        window.location.href = qrData;
                        return;
                    }
                    
                    // Otherwise, use the scan endpoint
                    const response = await fetch(`/public/qr/${encodeURIComponent(qrData)}`);
                    
                    if (response.ok) {
                        // If it's a redirect, follow it
                        window.location.href = response.url;
                    } else {
                        throw new Error('Invalid QR Code');
                    }
                    
                } catch (error) {
                    console.error('QR processing error:', error);
                    this.message = 'Invalid or unrecognized QR Code.';
                    this.messageType = 'error';
                    
                    // Restart scanning after error
                    setTimeout(() => {
                        this.scanning = true;
                        this.scanFrame();
                    }, 2000);
                }
            },

            async processManualCode() {
                if (!this.manualCode.trim()) {
                    this.message = 'Please enter a valid code.';
                    this.messageType = 'error';
                    return;
                }
                
                this.message = 'Processing code...';
                this.messageType = 'info';
                
                try {
                    const response = await fetch(`/public/qr/${encodeURIComponent(this.manualCode)}`);
                    
                    if (response.ok) {
                        window.location.href = response.url;
                    } else {
                        throw new Error('Invalid code');
                    }
                    
                } catch (error) {
                    console.error('Code processing error:', error);
                    this.message = 'Invalid or unrecognized code.';
                    this.messageType = 'error';
                }
            },

            addToRecentScans(item) {
                const scan = {
                    id: Date.now(),
                    name: item.name,
                    type: item.type,
                    url: item.url,
                    time: new Date().toLocaleString('it-IT')
                };
                
                this.recentScans.unshift(scan);
                this.recentScans = this.recentScans.slice(0, 5); // Keep only last 5
                
                localStorage.setItem('recentScans', JSON.stringify(this.recentScans));
            },
            
            openCameraSettings() {
                // Try to detect browser and open appropriate settings page
                const userAgent = navigator.userAgent.toLowerCase();
                
                if (userAgent.includes('chrome')) {
                    window.open('chrome://settings/content/camera', '_blank');
                } else if (userAgent.includes('firefox')) {
                    window.open('about:preferences#privacy', '_blank');
                } else if (userAgent.includes('safari')) {
                    window.open('x-apple.systempreferences:com.apple.preference.security?Privacy_Camera', '_blank');
                } else if (/android|iphone|ipad|ipod/.test(userAgent)) {
                    // Mobile browsers typically handle permissions differently
                    this.message = 'On mobile, please enable camera permissions in your browser or device settings.';
                    this.messageType = 'info';
                } else {
                    // Fallback to generic instructions
                    this.message = 'Please enable camera permissions in your browser settings.';
                    this.messageType = 'info';
                }
            }
        }
    }
    </script>
</x-auth-wrapper>