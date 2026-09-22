<template>
  <div class="flex h-screen bg-gray-50 font-sans text-gray-800">
    <Sidebar role="admin" />
    <div class="flex-1 flex flex-col overflow-hidden">
      <header class="h-16 bg-white border-b flex items-center justify-between px-6">
        <h1 class="text-xl font-semibold text-gray-800">Admin Portal</h1>
        <div class="flex items-center gap-4">
          <span class="text-sm text-gray-600">{{ authStore.user?.name || 'Admin' }}</span>
          <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold">
            {{ (authStore.user?.name || 'A').charAt(0).toUpperCase() }}
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

