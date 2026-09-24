<template>
  <aside class="w-64 bg-gradient-to-b from-primary-50 to-white border-r h-screen flex flex-col">
    <div class="p-6">
      <h2 class="text-2xl font-bold text-primary-600">Dashboard</h2>
    </div>
    <nav class="flex-1 px-4 space-y-2">
      <template v-if="role === 'admin'">
        <NuxtLink to="/admin" class="block px-4 py-2 rounded hover:bg-primary-100 hover:text-primary-700 flex items-center gap-2">
          Dashboard
        </NuxtLink>
        <NuxtLink to="/admin/pos" class="block px-4 py-2 rounded hover:bg-primary-100 hover:text-primary-700 flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4M4 4h16v16H4z"></path>
          </svg>
          POS Cashier
        </NuxtLink>
        <NuxtLink to="/admin/books" class="block px-4 py-2 rounded hover:bg-primary-100 hover:text-primary-700">Books</NuxtLink>
        <NuxtLink to="/admin/categories" class="block px-4 py-2 rounded hover:bg-primary-100 hover:text-primary-700">Categories</NuxtLink>
        <NuxtLink to="/admin/users" class="block px-4 py-2 rounded hover:bg-primary-100 hover:text-primary-700">Users</NuxtLink>
        <NuxtLink to="/admin/orders" class="block px-4 py-2 rounded hover:bg-primary-100 hover:text-primary-700">Orders</NuxtLink>
        <NuxtLink to="/admin/reports" class="block px-4 py-2 rounded hover:bg-primary-100 hover:text-primary-700">Reports</NuxtLink>
        <NuxtLink to="/admin/chat" class="block px-4 py-2 rounded hover:bg-primary-100 hover:text-primary-700">Chat</NuxtLink>
      </template>
      <template v-else>
        <NuxtLink to="/user" class="block px-4 py-2 rounded hover:bg-primary-100 hover:text-primary-700">Catalog</NuxtLink>
        <NuxtLink to="/user/cart" class="block px-4 py-2 rounded hover:bg-primary-100 hover:text-primary-700">Cart</NuxtLink>
        <NuxtLink to="/user/history" class="block px-4 py-2 rounded hover:bg-primary-100 hover:text-primary-700">History</NuxtLink>
        <NuxtLink to="/user/chat" class="block px-4 py-2 rounded hover:bg-primary-100 hover:text-primary-700">Chat</NuxtLink>
      </template>
    </nav>
    <div class="p-4 border-t">
      <button @click="logout" class="w-full py-2 bg-red-50 text-red-600 rounded hover:bg-red-100">Logout</button>
    </div>
  </aside>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'

const props = defineProps({
  role: { type: String, default: 'user' }
});

const authStore = useAuthStore()

const logout = async () => {
  await authStore.logout()
};
</script>
