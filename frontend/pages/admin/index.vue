<template>
  <div class="space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">Admin Dashboard</h2>

    <div v-if="pending" class="text-gray-500">
      Loading dashboard data...
    </div>
    
    <div v-else-if="error" class="text-red-500">
      Error loading dashboard: {{ error.message }}
    </div>

    <template v-else>
      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
          <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
            B
          </div>
          <div>
            <p class="text-sm text-gray-500 font-medium">Total Books</p>
            <p class="text-2xl font-bold text-gray-800">{{ stats.total_books }}</p>
          </div>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
          <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold text-xl">
            U
          </div>
          <div>
            <p class="text-sm text-gray-500 font-medium">Total Users</p>
            <p class="text-2xl font-bold text-gray-800">{{ stats.total_users }}</p>
          </div>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
          <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-xl">
            O
          </div>
          <div>
            <p class="text-sm text-gray-500 font-medium">Total Orders</p>
            <p class="text-2xl font-bold text-gray-800">{{ stats.total_orders }}</p>
          </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
          <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 font-bold text-xl">
            $
          </div>
          <div>
            <p class="text-sm text-gray-500 font-medium">Total Revenue</p>
            <p class="text-2xl font-bold text-gray-800">{{ formatCurrency(stats.total_revenue) }}</p>
          </div>
        </div>
      </div>

      <!-- Recent Orders Table -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-8">
        <div class="px-6 py-4 border-b border-gray-100">
          <h3 class="text-lg font-bold text-gray-800">Recent Orders</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Code</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="!stats.recent_orders?.length">
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No recent orders.</td>
              </tr>
              <tr v-for="order in stats.recent_orders" :key="order.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ order.order_code || `ORD-${order.id}` }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ order.user ? order.user.name : 'Walk-in Customer' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ new Date(order.created_at).toLocaleDateString() }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                    :class="{
                      'bg-green-100 text-green-800': order.status === 'completed' || order.status === 'paid',
                      'bg-yellow-100 text-yellow-800': order.status === 'pending',
                      'bg-red-100 text-red-800': order.status === 'cancelled',
                      'bg-gray-100 text-gray-800': !['completed', 'paid', 'pending', 'cancelled'].includes(order.status)
                    }">
                    {{ order.status || 'Completed' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                  {{ formatCurrency(order.total_amount) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRuntimeConfig } from '#app'
import { useAuthStore } from '~/stores/auth'

definePageMeta({ layout: 'admin', middleware: ['admin'] })

const config = useRuntimeConfig()
const authStore = useAuthStore()

const stats = ref(null)
const pending = ref(true)
const error = ref(null)

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value || 0)
}

const fetchDashboardData = async () => {
  try {
    pending.value = true
    const response = await $fetch(`${config.public.apiBase}/admin/dashboard`, {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json'
      }
    })
    
    if (response.status === 'success') {
      stats.value = response.data
    } else {
      throw new Error('Failed to load dashboard data')
    }
  } catch (err) {
    error.value = err
  } finally {
    pending.value = false
  }
}

onMounted(() => {
  fetchDashboardData()
})
</script>
