import { defineStore } from 'pinia'
import { ref } from 'vue'
import { useAuthStore } from './auth'
import { useRuntimeConfig } from '#app'

export const useOrdersStore = defineStore('orders', () => {
  const orders = ref<any[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const authStore = useAuthStore()
  const config = useRuntimeConfig()
  const apiBase = process.server ? 'http://127.0.0.1:8000/api/v1' : config.public.apiBase

  const fetchOrders = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await $fetch<any>(`${apiBase}/admin/orders`, {
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          Accept: 'application/json'
        }
      })
      orders.value = response.data
    } catch (err: any) {
      error.value = err?.data?.message || 'Failed to fetch orders'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  const updateOrder = async (id: number, data: any) => {
    loading.value = true
    error.value = null
    try {
      const response = await $fetch<any>(`${apiBase}/admin/orders/${id}`, {
        method: 'PUT',
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          Accept: 'application/json'
        },
        body: data
      })
      
      const index = orders.value.findIndex(o => o.id === id)
      if (index !== -1) {
        // Because the backend update response might not reload user/details,
        // we merge the updated fields into the existing object locally to preserve relations.
        orders.value[index] = { ...orders.value[index], ...response.data }
      }
      return response.data
    } catch (err: any) {
      error.value = err?.data?.message || 'Failed to update order'
      console.error(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  return { orders, loading, error, fetchOrders, updateOrder }
})
