import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useOrdersStore = defineStore('orders', () => {
  const orders = ref([
    { id: 101, customer_name: 'John Doe', customer_email: 'john@example.com', total: 19.98, status: 'Pending', created_at: new Date().toISOString(), items: [{ id:1, name: '1984', quantity: 2, price: 9.99 }] }
  ])

  const fetchOrders = async () => {
    // Mock fetch
  }

  return { orders, fetchOrders }
})
