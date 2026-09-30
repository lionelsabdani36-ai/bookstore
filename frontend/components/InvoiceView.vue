<template>
  <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 max-w-3xl mx-auto printable">
    <div class="flex justify-between items-start mb-8 border-b pb-4">
      <div>
        <h2 class="text-3xl font-bold text-primary-600">INVOICE</h2>
        <p class="text-gray-500 mt-1">#{{ order.order_code || 'INV-' + order.id }}</p>
      </div>
      <div class="text-right">
        <p class="font-semibold text-gray-800">Bookstore Inc.</p>
        <p class="text-sm text-gray-500">123 Bookstore Ave.</p>
        <p class="text-sm text-gray-500">hello@bookstore.com</p>
      </div>
    </div>

    <div class="mb-8 flex justify-between">
      <div>
        <p class="text-sm text-gray-500 font-medium">Billed To:</p>
        <p class="font-semibold text-gray-800">{{ order.user?.name || authStore?.user?.name || 'Customer' }}</p>
        <p class="text-sm text-gray-500">{{ order.user?.email || authStore?.user?.email || '' }}</p>
      </div>
      <div class="text-right">
        <p class="text-sm text-gray-500 font-medium">Date:</p>
        <p class="text-sm text-gray-800">{{ new Date(order.created_at).toLocaleDateString() }}</p>
      </div>
    </div>

    <table class="w-full text-left mb-8">
      <thead>
        <tr class="border-b border-gray-200">
          <th class="py-2 text-sm font-semibold text-gray-700">Item</th>
          <th class="py-2 text-sm font-semibold text-gray-700 text-center">Qty</th>
          <th class="py-2 text-sm font-semibold text-gray-700 text-right">Price</th>
          <th class="py-2 text-sm font-semibold text-gray-700 text-right">Total</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in order.details" :key="item.id" class="border-b border-gray-100">
          <td class="py-3 text-sm text-gray-800">{{ item.book?.name || 'Book' }}</td>
          <td class="py-3 text-sm text-gray-800 text-center">{{ item.qty }}</td>
          <td class="py-3 text-sm text-gray-800 text-right">{{ formatCurrency(item.unit_price) }}</td>
          <td class="py-3 text-sm text-gray-800 text-right">{{ formatCurrency(item.subtotal) }}</td>
        </tr>
      </tbody>
    </table>

    <div class="flex justify-end">
      <div class="w-64">
        <div class="flex justify-between py-2 text-sm text-gray-600">
          <span>Subtotal</span>
          <span>{{ formatCurrency(order.total_amount) }}</span>
        </div>
        <div class="flex justify-between py-2 text-sm text-gray-600">
          <span>Tax (0%)</span>
          <span>{{ formatCurrency(0) }}</span>
        </div>
        <div class="flex justify-between py-3 text-lg font-bold text-gray-800 border-t border-gray-200 mt-2">
          <span>Total</span>
          <span class="text-primary-600">{{ formatCurrency(order.total_amount) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'

defineProps({
  order: {
    type: Object,
    required: true
  }
});

const authStore = useAuthStore();

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value || 0);
};
</script>

<style scoped>
@media print {
  .printable {
    box-shadow: none !important;
    border: none !important;
  }
}
</style>
