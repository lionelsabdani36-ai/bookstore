<template>
  <div class="flex h-screen bg-gray-50 font-sans text-gray-800">
    <Sidebar role="user" />
    <div class="flex-1 flex flex-col overflow-hidden">
      <header class="h-16 bg-white border-b flex items-center justify-between px-6">
        <h1 class="text-xl font-semibold text-gray-800">Customer Portal</h1>
        <div class="flex items-center gap-4">
          <NuxtLink to="/user/cart" class="relative text-gray-500 hover:text-primary-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-4 w-4 flex items-center justify-center">3</span>
          </NuxtLink>
          <img 
            v-if="authStore.user?.photo" 
            :src="`http://localhost:8000/storage/${authStore.user.photo}`" 
            class="w-8 h-8 rounded-full object-cover border border-gray-200"
            alt="Profile Photo"
          />
          <div 
            v-else
            class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold"
          >
            {{ (authStore.user?.name || 'U').charAt(0).toUpperCase() }}
          </div>
        </div>
      </header>
      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'

const authStore = useAuthStore()
</script>

