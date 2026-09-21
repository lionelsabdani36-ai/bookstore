<template>
  <div class="space-y-6 max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800">Shopping Cart</h2>
    
    <div v-if="cartStore.items.length === 0" class="bg-white p-8 rounded-xl text-center shadow-sm border border-gray-100">
      <p class="text-gray-500">Your cart is empty.</p>
      <NuxtLink to="/user" class="mt-4 inline-block text-primary-600 font-medium hover:underline">Browse books</NuxtLink>
    </div>

    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <div v-for="item in cartStore.items" :key="item.id" class="flex justify-between items-center py-4 border-b last:border-0">
        <div>
          <h3 class="font-semibold text-gray-800">{{ item.title }}</h3>
          <p class="text-sm text-gray-500">Qty: {{ item.quantity }}</p>
        </div>
        <div class="flex items-center gap-4">
          <span class="font-bold text-gray-800">${{ item.price * item.quantity }}</span>
          <button @click="cartStore.removeFromCart(item.id)" class="text-red-500 hover:text-red-700 text-sm font-medium">Remove</button>
        </div>
      </div>
      
      <div class="mt-8 border-t pt-4 flex justify-between items-center">
        <span class="text-lg font-bold text-gray-800">Total:</span>
        <span class="text-2xl font-bold text-primary-600">${{ cartStore.total.toFixed(2) }}</span>
      </div>
      
      <div class="mt-6 flex justify-end">
        <button class="bg-primary-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-primary-700 shadow-md">
          Checkout
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCartStore } from '~/stores/cart'

definePageMeta({ layout: 'user' })
const cartStore = useCartStore()
</script>
