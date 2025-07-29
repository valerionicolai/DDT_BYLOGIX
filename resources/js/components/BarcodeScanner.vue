<template>
  <div class="barcode-scanner">
    <!-- Scanner Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-900">Scanner Codice a Barre</h2>
        <div class="flex items-center space-x-2">
          <span class="text-sm text-gray-500">Stato:</span>
          <span 
            :class="[
              'px-2 py-1 rounded-full text-xs font-medium',
              isScanning ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
            ]"
          >
            {{ isScanning ? 'Attivo' : 'Inattivo' }}
          </span>
        </div>
      </div>

      <!-- Controls -->
      <div class="flex flex-wrap gap-3 mb-4">
        <button
          @click="startScanning"
          :disabled="isScanning || !hasCamera"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
        >
          <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
          </svg>
          Avvia Scanner
        </button>

        <button
          @click="stopScanning"
          :disabled="!isScanning"
          class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
        >
          <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
          </svg>
          Ferma Scanner
        </button>

        <button
          @click="switchCamera"
          :disabled="!isScanning || availableCameras.length <= 1"
          class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
        >
          <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Cambia Camera
        </button>
      </div>

      <!-- Camera Selection -->
      <div v-if="availableCameras.length > 1" class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Seleziona Camera:</label>
        <select 
          v-model="selectedCameraId"
          @change="onCameraChange"
          class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
        >
          <option v-for="camera in availableCameras" :key="camera.deviceId" :value="camera.deviceId">
            {{ camera.label || `Camera ${camera.deviceId.slice(0, 8)}...` }}
          </option>
        </select>
      </div>
    </div>

    <!-- Scanner Video Container -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
      <div class="relative">
        <!-- Video Element -->
        <video
          ref="videoElement"
          class="w-full max-w-md mx-auto rounded-lg bg-black"
          :class="{ 'hidden': !isScanning }"
          autoplay
          muted
          playsinline
        ></video>

        <!-- Placeholder when not scanning -->
        <div 
          v-if="!isScanning" 
          class="w-full max-w-md mx-auto h-64 bg-gray-100 rounded-lg flex items-center justify-center"
        >
          <div class="text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
            </svg>
            <p class="text-gray-500">Clicca "Avvia Scanner" per iniziare</p>
          </div>
        </div>

        <!-- Scanning Overlay -->
        <div v-if="isScanning" class="absolute inset-0 pointer-events-none">
          <div class="absolute inset-0 border-2 border-blue-500 rounded-lg animate-pulse"></div>
          <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            <div class="w-48 h-48 border-2 border-red-500 bg-transparent"></div>
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex">
          <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
          </svg>
          <p class="text-red-700">{{ error }}</p>
        </div>
      </div>
    </div>

    <!-- Scan Results -->
    <div v-if="lastScanResult" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Ultimo Risultato</h3>
      <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-start">
          <svg class="w-5 h-5 text-green-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
          </svg>
          <div class="flex-1">
            <p class="text-sm font-medium text-green-800">Codice Scansionato:</p>
            <p class="text-lg font-mono text-green-900 mt-1">{{ lastScanResult.text }}</p>
            <p class="text-xs text-green-600 mt-1">Formato: {{ lastScanResult.format }}</p>
            <p class="text-xs text-green-600">{{ formatTimestamp(lastScanResult.timestamp) }}</p>
          </div>
          <button
            @click="copyToClipboard(lastScanResult.text)"
            class="ml-2 px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors"
          >
            Copia
          </button>
        </div>
      </div>
    </div>

    <!-- Scan History -->
    <div v-if="scanHistory.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Cronologia Scansioni</h3>
        <button
          @click="clearHistory"
          class="px-3 py-1 text-sm text-red-600 hover:text-red-800 transition-colors"
        >
          Cancella Cronologia
        </button>
      </div>
      <div class="space-y-3 max-h-64 overflow-y-auto">
        <div 
          v-for="(scan, index) in scanHistory" 
          :key="index"
          class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
        >
          <div class="flex-1">
            <p class="font-mono text-sm text-gray-900">{{ scan.text }}</p>
            <p class="text-xs text-gray-500">{{ scan.format }} • {{ formatTimestamp(scan.timestamp) }}</p>
          </div>
          <button
            @click="copyToClipboard(scan.text)"
            class="ml-2 px-2 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700 transition-colors"
          >
            Copia
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'
import { BrowserMultiFormatReader, NotFoundException } from '@zxing/library'

export default {
  name: 'BarcodeScanner',
  emits: ['scan-result', 'scan-error'],
  setup(props, { emit }) {
    const videoElement = ref(null)
    const isScanning = ref(false)
    const hasCamera = ref(false)
    const error = ref('')
    const lastScanResult = ref(null)
    const scanHistory = ref([])
    const availableCameras = ref([])
    const selectedCameraId = ref('')
    
    let codeReader = null
    let scanningControls = null

    const initializeScanner = async () => {
      try {
        codeReader = new BrowserMultiFormatReader()
        
        // Check for camera availability
        const devices = await navigator.mediaDevices.enumerateDevices()
        const videoDevices = devices.filter(device => device.kind === 'videoinput')
        
        if (videoDevices.length === 0) {
          error.value = 'Nessuna camera disponibile'
          hasCamera.value = false
          return
        }

        hasCamera.value = true
        availableCameras.value = videoDevices
        selectedCameraId.value = videoDevices[0].deviceId
        
      } catch (err) {
        console.error('Error initializing scanner:', err)
        error.value = 'Errore nell\'inizializzazione dello scanner'
        hasCamera.value = false
      }
    }

    const startScanning = async () => {
      if (!hasCamera.value || !codeReader) return

      try {
        error.value = ''
        isScanning.value = true

        scanningControls = await codeReader.decodeFromVideoDevice(
          selectedCameraId.value,
          videoElement.value,
          (result, err) => {
            if (result) {
              const scanResult = {
                text: result.getText(),
                format: result.getBarcodeFormat(),
                timestamp: new Date()
              }
              
              lastScanResult.value = scanResult
              scanHistory.value.unshift(scanResult)
              
              // Keep only last 10 scans
              if (scanHistory.value.length > 10) {
                scanHistory.value = scanHistory.value.slice(0, 10)
              }
              
              emit('scan-result', scanResult)
              
              // Optional: stop scanning after successful scan
              // stopScanning()
            }
            
            if (err && !(err instanceof NotFoundException)) {
              console.error('Scan error:', err)
              error.value = 'Errore durante la scansione'
              emit('scan-error', err)
            }
          }
        )
      } catch (err) {
        console.error('Error starting scanner:', err)
        error.value = 'Impossibile avviare lo scanner'
        isScanning.value = false
        emit('scan-error', err)
      }
    }

    const stopScanning = () => {
      if (scanningControls) {
        scanningControls.stop()
        scanningControls = null
      }
      isScanning.value = false
      error.value = ''
    }

    const switchCamera = async () => {
      if (availableCameras.value.length <= 1) return
      
      const currentIndex = availableCameras.value.findIndex(
        camera => camera.deviceId === selectedCameraId.value
      )
      const nextIndex = (currentIndex + 1) % availableCameras.value.length
      selectedCameraId.value = availableCameras.value[nextIndex].deviceId
      
      if (isScanning.value) {
        stopScanning()
        await new Promise(resolve => setTimeout(resolve, 100))
        startScanning()
      }
    }

    const onCameraChange = async () => {
      if (isScanning.value) {
        stopScanning()
        await new Promise(resolve => setTimeout(resolve, 100))
        startScanning()
      }
    }

    const copyToClipboard = async (text) => {
      try {
        await navigator.clipboard.writeText(text)
        // You could add a toast notification here
      } catch (err) {
        console.error('Failed to copy to clipboard:', err)
      }
    }

    const clearHistory = () => {
      scanHistory.value = []
    }

    const formatTimestamp = (timestamp) => {
      return new Intl.DateTimeFormat('it-IT', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      }).format(timestamp)
    }

    onMounted(() => {
      initializeScanner()
    })

    onUnmounted(() => {
      stopScanning()
    })

    return {
      videoElement,
      isScanning,
      hasCamera,
      error,
      lastScanResult,
      scanHistory,
      availableCameras,
      selectedCameraId,
      startScanning,
      stopScanning,
      switchCamera,
      onCameraChange,
      copyToClipboard,
      clearHistory,
      formatTimestamp
    }
  }
}
</script>

<style scoped>
.barcode-scanner {
  max-width: 800px;
  margin: 0 auto;
}

video {
  max-height: 400px;
  object-fit: cover;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>