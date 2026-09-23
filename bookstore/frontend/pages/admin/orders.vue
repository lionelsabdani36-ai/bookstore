<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold text-gray-800">Orders Management</h2>
    </div>

    <!-- Error State -->
    <div v-if="ordersStore.error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-4">
      <p>{{ ordersStore.error }}</p>
    </div>

    <!-- Loading State -->
    <div v-if="ordersStore.loading && ordersStore.orders.length === 0" class="flex justify-center items-center py-10">
      <p class="text-gray-500">Loading orders...</p>
    </div>

    <!-- Orders List -->
    <div v-else class="bg-white rounded-xl shadow overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Code</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="order in ordersStore.orders" :key="order.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ order.order_code }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ order.user?.name || 'Unknown User' }}</div>
              <div class="text-sm text-gray-500">{{ order.user?.email }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ order.total_amount }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="{
                  'bg-yellow-100 text-yellow-800': order.status === 'pending',
                  'bg-blue-100 text-blue-800': order.status === 'confirmed',
                  'bg-green-100 text-green-800': order.status === 'paid',
                  'bg-red-100 text-red-800': order.status === 'cancelled'
                }">
                {{ order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="openManageModal(order)" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded">Manage</button>
            </td>
          </tr>
          <tr v-if="ordersStore.orders.length === 0">
            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No orders found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal for Order Management (Cashier) -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-bold text-gray-900">Manage Order: {{ activeOrder?.order_code }}</h3>
          <span class="px-2 py-1 text-xs font-semibold rounded-full"
            :class="{
              'bg-yellow-100 text-yellow-800': activeOrder?.status === 'pending',
              'bg-blue-100 text-blue-800': activeOrder?.status === 'confirmed',
              'bg-green-100 text-green-800': activeOrder?.status === 'paid',
              'bg-red-100 text-red-800': activeOrder?.status === 'cancelled'
            }">
            {{ activeOrder?.status.toUpperCase() }}
          </span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <!-- Order Details list -->
          <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Items</h4>
            <ul class="space-y-2 mb-4">
              <li v-for="item in activeOrder?.details" :key="item.id" class="flex justify-between text-sm">
                <span>{{ item.qty }}x {{ item.book?.name || 'Unknown Book' }}</span>
                <span class="font-medium">${{ item.subtotal }}</span>
              </li>
            </ul>
            <div class="flex justify-between font-bold text-lg border-t pt-2">
              <span>Total:</span>
              <span>${{ activeOrder?.total_amount }}</span>
            </div>
          </div>

          <!-- Cashier Actions -->
          <div>
            <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Cashier Actions</h4>
            
            <form @submit.prevent="submitUpdate">
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Update Status</label>
                <select v-model="form.status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                  <option value="pending">Pending</option>
                  <option value="confirmed">Confirmed (Preparing)</option>
                  <option value="paid">Paid (Completed)</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>

              <!-- Only show payment fields if we are processing payment -->
              <div v-if="form.status === 'paid'" class="space-y-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Cash Received ($)</label>
                  <input v-model="form.cash_received" type="number" step="0.01" min="0" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="flex justify-between items-center text-sm">
                  <span class="font-medium text-gray-700">Change Amount:</span>
                  <span class="font-bold text-lg" :class="changeAmount >= 0 ? 'text-green-600' : 'text-red-600'">
                    ${{ changeAmount.toFixed(2) }}
                  </span>
                </div>
                <p v-if="changeAmount < 0" class="text-xs text-red-500 mt-1">Insufficient cash received!</p>
              </div>
              
              <div class="mt-4 p-3 bg-gray-50 rounded text-sm text-gray-600" v-if="activeOrder?.cash_received">
                <strong>Previously Paid:</strong> Received ${{ activeOrder.cash_received }} / Change ${{ activeOrder.change_amount }}
              </div>

              <div class="flex justify-end space-x-3 mt-6">
                <button type="button" @click="closeModal" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                  Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50" :disabled="ordersStore.loading || (form.status === 'paid' && changeAmount < 0)">
                  {{ ordersStore.loading ? 'Saving...' : 'Update Order' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import { useOrdersStore } from '~/stores/orders'

definePageMeta({ 
  layout: 'admin', 
  middleware: ['admin'] 
})

const ordersStore = useOrdersStore()

const showModal = ref(false)
const activeOrder = ref(null)

const form = reactive({
  status: 'pending',
  cash_received: 0
})

onMounted(() => {
  ordersStore.fetchOrders()
})

const changeAmount = computed(() => {
  if (!activeOrder.value) return 0
  return Number(form.cash_received) - Number(activeOrder.value.total_amount)
})

const openManageModal = (order) => {
  activeOrder.value = order
  form.status = order.status
  form.cash_received = order.cash_received || order.total_amount
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  activeOrder.value = null
}

const submitUpdate = async () => {
  const payload = {
    status: form.status
  }
  
  if (form.status === 'paid') {
    payload.cash_received = form.cash_received
  }

  try {
    await ordersStore.updateOrder(activeOrder.value.id, payload)
    closeModal()
  } catch (e) {
    // Handled in store
  }
}
</script>
