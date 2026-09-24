import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useRuntimeConfig } from '#app'
import { useAuthStore } from './auth'

export const usePosStore = defineStore('pos', () => {
  const scannedItems = ref<Array<{ book: any, qty: number, unit_price: number }>>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const lastOrder = ref<any>(null)
  
  const totalAmount = computed(() => {
    return scannedItems.value.reduce((total, item) => total + (item.qty * item.unit_price), 0)
  })

  const scanBarcode = async (barcode: string) => {
    loading.value = true
    error.value = null
    
    try {
      const config = useRuntimeConfig()
      const authStore = useAuthStore()
      const response = await $fetch<any>(`${config.public.apiBase}/admin/pos/scan`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Accept': 'application/json'
        },
        body: { barcode }
      })

      if (response.status === 'success' && response.data) {
        const book = response.data
        const existingItemIndex = scannedItems.value.findIndex(item => item.book.id === book.id)
        
        if (existingItemIndex !== -1) {
          scannedItems.value[existingItemIndex].qty += 1
        } else {
          scannedItems.value.push({
            book,
            qty: 1,
            unit_price: book.sell_price
          })
        }
      } else {
        throw new Error(response.message || 'Book not found')
      }
    } catch (err: any) {
      error.value = err.data?.message || err.message || 'Failed to scan barcode'
      throw err
    } finally {
      loading.value = false
    }
  }

  const removeItem = (index: number) => {
    scannedItems.value.splice(index, 1)
  }

  const updateQty = (index: number, qty: number) => {
    if (qty > 0) {
      scannedItems.value[index].qty = qty
    }
  }

  const checkout = async (cashReceived: number) => {
    loading.value = true
    error.value = null
    
    try {
      const config = useRuntimeConfig()
      const authStore = useAuthStore()
      
      const payload = {
        items: scannedItems.value.map(item => ({
          book_id: item.book.id,
          qty: item.qty,
          unit_price: item.unit_price
        })),
        cash_received: cashReceived
      }

      const response = await $fetch<any>(`${config.public.apiBase}/admin/pos/checkout`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Accept': 'application/json'
        },
        body: payload
      })

      if (response.status === 'success' && response.data) {
        lastOrder.value = response.data
        scannedItems.value = []
        return response.data
      } else {
        throw new Error(response.message || 'Checkout failed')
      }
    } catch (err: any) {
      error.value = err.data?.message || err.message || 'Failed to complete checkout'
      throw err
    } finally {
      loading.value = false
    }
  }

  const clearAll = () => {
    scannedItems.value = []
    error.value = null
    lastOrder.value = null
  }

  return {
    scannedItems,
    loading,
    error,
    lastOrder,
    totalAmount,
    scanBarcode,
    removeItem,
    updateQty,
    checkout,
    clearAll
  }
})
