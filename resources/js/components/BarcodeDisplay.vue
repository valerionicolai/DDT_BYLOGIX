<template>
  <div class="barcode-display">
    <!-- Barcode Container -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium text-gray-900">Codice a Barre</h3>
        <div class="flex space-x-2">
          <button
            @click="downloadBarcode"
            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          >
            <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Download
          </button>
          <button
            @click="printBarcode"
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          >
            <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Stampa
          </button>
        </div>
      </div>

      <!-- Barcode Display Area -->
      <div class="flex flex-col items-center space-y-4">
        <div 
          ref="barcodeContainer"
          class="bg-white p-4 border border-gray-100 rounded-md"
          :class="{ 'opacity-50': loading }"
        >
          <div v-if="loading" class="flex items-center justify-center h-20">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
          <canvas 
            v-else
            ref="barcodeCanvas"
            class="max-w-full h-auto"
          ></canvas>
        </div>

        <!-- Barcode Text -->
        <div class="text-center">
          <p class="text-sm font-mono text-gray-600">{{ barcodeValue }}</p>
          <p v-if="documentTitle" class="text-xs text-gray-500 mt-1">{{ documentTitle }}</p>
        </div>
      </div>

      <!-- Barcode Options -->
      <div class="mt-6 border-t border-gray-200 pt-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Formato</label>
            <select
              v-model="barcodeFormat"
              @change="generateBarcode"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
            >
              <option value="CODE128">CODE128</option>
              <option value="CODE39">CODE39</option>
              <option value="EAN13">EAN13</option>
              <option value="EAN8">EAN8</option>
              <option value="UPC">UPC</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Larghezza</label>
            <input
              v-model.number="barcodeWidth"
              @input="generateBarcode"
              type="range"
              min="1"
              max="4"
              step="0.5"
              class="w-full"
            >
            <div class="text-xs text-gray-500 text-center">{{ barcodeWidth }}x</div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Altezza</label>
            <input
              v-model.number="barcodeHeight"
              @input="generateBarcode"
              type="range"
              min="50"
              max="200"
              step="10"
              class="w-full"
            >
            <div class="text-xs text-gray-500 text-center">{{ barcodeHeight }}px</div>
          </div>
        </div>

        <div class="mt-4 flex items-center">
          <input
            id="show-text"
            v-model="showText"
            @change="generateBarcode"
            type="checkbox"
            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
          >
          <label for="show-text" class="ml-2 block text-sm text-gray-700">
            Mostra testo sotto il codice a barre
          </label>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch, nextTick } from 'vue'

export default {
  name: 'BarcodeDisplay',
  props: {
    value: {
      type: String,
      required: true
    },
    documentTitle: {
      type: String,
      default: ''
    },
    format: {
      type: String,
      default: 'CODE128'
    },
    width: {
      type: Number,
      default: 2
    },
    height: {
      type: Number,
      default: 100
    },
    displayText: {
      type: Boolean,
      default: true
    }
  },
  setup(props) {
    const barcodeContainer = ref(null)
    const barcodeCanvas = ref(null)
    const loading = ref(false)
    
    // Reactive barcode settings
    const barcodeValue = ref(props.value)
    const barcodeFormat = ref(props.format)
    const barcodeWidth = ref(props.width)
    const barcodeHeight = ref(props.height)
    const showText = ref(props.displayText)

    // Watch for prop changes
    watch(() => props.value, (newValue) => {
      barcodeValue.value = newValue
      generateBarcode()
    })

    const generateBarcode = async () => {
      if (!barcodeValue.value || !barcodeCanvas.value) return

      loading.value = true
      
      try {
        await nextTick()
        
        // Simple barcode generation using canvas
        const canvas = barcodeCanvas.value
        const ctx = canvas.getContext('2d')
        
        // Set canvas size
        canvas.width = 300
        canvas.height = barcodeHeight.value + (showText.value ? 30 : 10)
        
        // Clear canvas
        ctx.fillStyle = 'white'
        ctx.fillRect(0, 0, canvas.width, canvas.height)
        
        // Generate simple barcode pattern (simplified implementation)
        const barWidth = barcodeWidth.value
        const startX = 20
        const startY = 10
        const barcodeWidth = canvas.width - 40
        
        ctx.fillStyle = 'black'
        
        // Create a simple pattern based on the value
        const pattern = generateBarcodePattern(barcodeValue.value, barcodeFormat.value)
        const barCount = pattern.length
        const singleBarWidth = barcodeWidth / barCount
        
        for (let i = 0; i < pattern.length; i++) {
          if (pattern[i] === '1') {
            ctx.fillRect(
              startX + (i * singleBarWidth), 
              startY, 
              singleBarWidth * barWidth, 
              barcodeHeight.value
            )
          }
        }
        
        // Add text if enabled
        if (showText.value) {
          ctx.fillStyle = 'black'
          ctx.font = '12px monospace'
          ctx.textAlign = 'center'
          ctx.fillText(
            barcodeValue.value, 
            canvas.width / 2, 
            barcodeHeight.value + 25
          )
        }
        
      } catch (error) {
        console.error('Errore nella generazione del codice a barre:', error)
      } finally {
        loading.value = false
      }
    }

    const generateBarcodePattern = (value, format) => {
      // Simplified barcode pattern generation
      // In a real implementation, you would use a proper barcode library like JsBarcode
      let pattern = '101' // Start pattern
      
      for (let i = 0; i < value.length; i++) {
        const char = value.charCodeAt(i)
        // Simple encoding: alternate between thick and thin bars
        if (char % 2 === 0) {
          pattern += '110011'
        } else {
          pattern += '101101'
        }
      }
      
      pattern += '101' // End pattern
      return pattern
    }

    const downloadBarcode = () => {
      if (!barcodeCanvas.value) return
      
      try {
        const canvas = barcodeCanvas.value
        const link = document.createElement('a')
        link.download = `barcode-${barcodeValue.value}.png`
        link.href = canvas.toDataURL('image/png')
        link.click()
      } catch (error) {
        console.error('Errore nel download del codice a barre:', error)
      }
    }

    const printBarcode = () => {
      if (!barcodeCanvas.value) return
      
      try {
        const canvas = barcodeCanvas.value
        const printWindow = window.open('', '_blank')
        const img = canvas.toDataURL('image/png')
        
        printWindow.document.write(`
          <html>
            <head>
              <title>Stampa Codice a Barre - ${barcodeValue.value}</title>
              <style>
                body {
                  margin: 0;
                  padding: 20px;
                  display: flex;
                  flex-direction: column;
                  align-items: center;
                  font-family: Arial, sans-serif;
                }
                .barcode-container {
                  text-align: center;
                  page-break-inside: avoid;
                }
                .barcode-image {
                  max-width: 100%;
                  height: auto;
                  border: 1px solid #ddd;
                  padding: 10px;
                  background: white;
                }
                .barcode-info {
                  margin-top: 10px;
                  font-size: 12px;
                  color: #666;
                }
                @media print {
                  body { margin: 0; }
                  .no-print { display: none; }
                }
              </style>
            </head>
            <body>
              <div class="barcode-container">
                <img src="${img}" alt="Codice a Barre" class="barcode-image" />
                <div class="barcode-info">
                  <div><strong>Codice:</strong> ${barcodeValue.value}</div>
                  ${props.documentTitle ? `<div><strong>Documento:</strong> ${props.documentTitle}</div>` : ''}
                  <div><strong>Formato:</strong> ${barcodeFormat.value}</div>
                  <div><strong>Data:</strong> ${new Date().toLocaleDateString('it-IT')}</div>
                </div>
              </div>
              <script>
                window.onload = function() {
                  window.print();
                  window.onafterprint = function() {
                    window.close();
                  };
                };
              </script>
            </body>
          </html>
        `)
        printWindow.document.close()
      } catch (error) {
        console.error('Errore nella stampa del codice a barre:', error)
      }
    }

    onMounted(() => {
      generateBarcode()
    })

    return {
      barcodeContainer,
      barcodeCanvas,
      loading,
      barcodeValue,
      barcodeFormat,
      barcodeWidth,
      barcodeHeight,
      showText,
      generateBarcode,
      downloadBarcode,
      printBarcode
    }
  }
}
</script>

<style scoped>
.barcode-display {
  @apply w-full;
}

/* Print styles */
@media print {
  .barcode-display {
    @apply shadow-none border-none;
  }
}
</style>