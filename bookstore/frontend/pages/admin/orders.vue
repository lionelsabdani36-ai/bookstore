<template>
  <div class="space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">Orders Confirmation</h2>
    <DataTable :columns="columns" :data="ordersStore.orders" actions>
      <template #actions="{ item }">
        <button class="bg-green-100 text-green-700 px-3 py-1 rounded hover:bg-green-200">Confirm</button>
      </template>
    </DataTable>
  </div>
</template>

<script setup>
import { useOrdersStore } from '~/stores/orders'

definePageMeta({ layout: 'admin', middleware: ['admin'] })
const ordersStore = useOrdersStore()
const columns = [
  { key: 'id', label: 'Order ID' },
  { key: 'customer_name', label: 'Customer' },
  { key: 'total', label: 'Total ($)' },
  { key: 'status', label: 'Status' }
]
</script>
