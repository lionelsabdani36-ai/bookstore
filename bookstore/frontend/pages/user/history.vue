<template>
  <div class="space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">Order History</h2>
    <div class="grid grid-cols-1 gap-6">
      <div v-for="order in ordersStore.orders" :key="order.id" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between md:items-center">
        <div>
          <p class="text-sm text-gray-500">Order #{{ order.id }} - {{ new Date(order.created_at).toLocaleDateString() }}</p>
          <p class="font-semibold text-gray-800 mt-1">Status: <span class="text-primary-600">{{ order.status }}</span></p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center gap-4">
          <span class="text-xl font-bold text-gray-800">${{ order.total }}</span>
          <button @click="viewInvoice(order)" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium">View Invoice</button>
        </div>
      </div>
    </div>

    <!-- Invoice Modal mock -->
    <div v-if="selectedOrder" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto p-4 relative">
        <button @click="selectedOrder = null" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800">
          ✕
        </button>
        <button onclick="window.print()" class="absolute top-4 right-12 text-primary-600 hover:text-primary-800 font-medium">
          Print
        </button>
        <div class="pt-8">
          <InvoiceView :order="selectedOrder" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useOrdersStore } from '~/stores/orders'
import InvoiceView from '~/components/InvoiceView.vue'

definePageMeta({ layout: 'user', middleware: ['auth'] })

const ordersStore = useOrdersStore()
const selectedOrder = ref(null)

const viewInvoice = (order) => {
  selectedOrder.value = order
}
</script>
