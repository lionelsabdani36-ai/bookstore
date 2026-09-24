<template>
  <div>
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Point of Sale</h2>
      <p class="text-gray-600">Scan items and process transactions</p>
    </div>

    <!-- Error Alert -->
    <div v-if="posStore.error" class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-red-700">
            {{ posStore.error }}
          </p>
        </div>
        <button @click="posStore.error = null" class="ml-auto text-red-500 hover:text-red-700">
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
      <!-- Left Panel: Scanner and Items -->
      <div class="flex-1 lg:w-2/3 flex flex-col gap-6">
        <!-- Barcode Input -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <form @submit.prevent="handleScan" class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </div>
            <input 
              ref="barcodeInput"
              v-model="barcode" 
              type="text" 
              class="block w-full pl-10 pr-3 py-4 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-lg transition duration-150 ease-in-out" 
              placeholder="Scan barcode or type and press Enter..."
              :disabled="posStore.loading"
            />
            <div v-if="posStore.loading" class="absolute inset-y-0 right-0 pr-3 flex items-center">
              <svg class="animate-spin h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
          </form>
        </div>

        <!-- Items Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex-1 overflow-hidden flex flex-col">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book Name</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barcode</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                  <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Qty</th>
                  <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                  <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="posStore.scannedItems.length === 0">
                  <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    No items scanned yet.
                  </td>
                </tr>
                <tr v-for="(item, index) in posStore.scannedItems" :key="index" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ index + 1 }}
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ item.book.name }}</div>
                    <div class="text-xs text-gray-500">{{ item.book.category?.name }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ item.book.barcode || '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatCurrency(item.unit_price) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    <input 
                      type="number" 
                      min="1" 
                      :value="item.qty" 
                      @change="(e) => posStore.updateQty(index, parseInt((e.target as HTMLInputElement).value) || 1)"
                      class="w-16 text-center border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                    />
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                    {{ formatCurrency(item.qty * item.unit_price) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button @click="posStore.removeItem(index)" class="text-red-600 hover:text-red-900 bg-red-50 p-2 rounded-md hover:bg-red-100 transition-colors">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Right Panel: Summary & Payment -->
      <div class="lg:w-1/3">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Order Summary</h3>
          
          <div class="space-y-4 mb-6">
            <div class="flex justify-between text-sm text-gray-600">
              <span>Total Items:</span>
              <span class="font-medium text-gray-900">{{ totalItemsCount }}</span>
            </div>
            
            <div class="flex justify-between items-end border-t pt-4">
              <span class="text-base font-semibold text-gray-900">Total Amount:</span>
              <span class="text-2xl font-bold text-primary-600">{{ formatCurrency(posStore.totalAmount) }}</span>
            </div>
          </div>

          <div class="mb-6 space-y-4">
            <div>
              <label for="cashReceived" class="block text-sm font-medium text-gray-700 mb-1">Cash Received</label>
              <div class="relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="text-gray-500 sm:text-sm">Rp</span>
                </div>
                <input 
                  type="number" 
                  id="cashReceived" 
                  v-model="cashReceived"
                  class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 pr-12 sm:text-lg border-gray-300 rounded-md py-3" 
                  placeholder="0"
                />
              </div>
            </div>

            <div class="flex justify-between items-center p-3 rounded-lg" :class="changeAmount >= 0 ? 'bg-green-50' : 'bg-red-50'">
              <span class="text-sm font-medium" :class="changeAmount >= 0 ? 'text-green-800' : 'text-red-800'">Change:</span>
              <span class="text-xl font-bold" :class="changeAmount >= 0 ? 'text-green-700' : 'text-red-700'">
                {{ formatCurrency(changeAmount) }}
              </span>
            </div>
          </div>

          <div class="space-y-3">
            <button 
              @click="handleCheckout" 
              :disabled="!canCheckout || posStore.loading"
              class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <span v-if="posStore.loading">Processing...</span>
              <span v-else>Complete Sale</span>
            </button>
            
            <button 
              @click="posStore.clearAll(); cashReceived = null" 
              :disabled="posStore.scannedItems.length === 0 || posStore.loading"
              class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              Clear All
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div>
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
              <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-5">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Transaction Successful
              </h3>
              <div class="mt-2 text-sm text-gray-500">
                <p>Order Code: <span class="font-bold text-gray-900">{{ posStore.lastOrder?.order_code || '-' }}</span></p>
                <div class="mt-4 border-t border-b py-3 text-left">
                  <div class="flex justify-between mb-1">
                    <span>Total Amount:</span>
                    <span class="font-medium text-gray-900">{{ formatCurrency(posStore.lastOrder?.total_amount || 0) }}</span>
                  </div>
                  <div class="flex justify-between mb-1">
                    <span>Cash Received:</span>
                    <span class="font-medium text-gray-900">{{ formatCurrency(posStore.lastOrder?.cash_received || 0) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>Change:</span>
                    <span class="font-medium text-gray-900">{{ formatCurrency(posStore.lastOrder?.change_amount || 0) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
            <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm" @click="printReceipt">
              Print Receipt
            </button>
            <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm" @click="closeModal">
              New Transaction
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue'
import { usePosStore } from '~/stores/pos'

definePageMeta({
  layout: 'admin',
  middleware: ['admin']
})

const posStore = usePosStore()
const barcode = ref('')
const barcodeInput = ref<HTMLInputElement | null>(null)
const cashReceived = ref<number | null>(null)
const showSuccessModal = ref(false)

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value || 0)
}

const totalItemsCount = computed(() => {
  return posStore.scannedItems.reduce((total, item) => total + item.qty, 0)
})

const changeAmount = computed(() => {
  if (!cashReceived.value) return 0
  return cashReceived.value - posStore.totalAmount
})

const canCheckout = computed(() => {
  return posStore.scannedItems.length > 0 && 
         cashReceived.value !== null && 
         cashReceived.value >= posStore.totalAmount
})

const handleScan = async () => {
  if (!barcode.value.trim()) return
  
  try {
    await posStore.scanBarcode(barcode.value.trim())
    barcode.value = ''
    // Refocus input after scan
    nextTick(() => {
      barcodeInput.value?.focus()
    })
  } catch (e) {
    // Error is handled in store, select text for easy re-scan
    barcodeInput.value?.select()
  }
}

const handleCheckout = async () => {
  if (!canCheckout.value) return
  
  try {
    await posStore.checkout(cashReceived.value!)
    showSuccessModal.value = true
  } catch (e) {
    // Error handled in store
  }
}

const closeModal = () => {
  showSuccessModal.value = false
  cashReceived.value = null
  posStore.clearAll()
  nextTick(() => {
    barcodeInput.value?.focus()
  })
}

const printReceipt = () => {
  window.print()
}

onMounted(() => {
  // Auto focus barcode input when page loads
  barcodeInput.value?.focus()
})
</script>

<style>
/* Hide print button when actually printing */
@media print {
  body * {
    visibility: hidden;
  }
  #modal-title, #modal-title * {
    visibility: visible;
  }
  .bg-white.rounded-lg.px-4 {
    visibility: visible;
    position: absolute;
    left: 0;
    top: 0;
  }
}
</style>
