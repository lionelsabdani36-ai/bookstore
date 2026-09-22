<template>
  <div class="space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">Admin Dashboard</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
          B
        </div>
        <div>
          <p class="text-sm text-gray-500 font-medium">Total Books</p>
          <p class="text-2xl font-bold text-gray-800">120</p>
        </div>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold text-xl">
          U
        </div>
        <div>
          <p class="text-sm text-gray-500 font-medium">Total Users</p>
          <p class="text-2xl font-bold text-gray-800">45</p>
        </div>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-xl">
          O
        </div>
        <div>
          <p class="text-sm text-gray-500 font-medium">Pending Orders</p>
          <p class="text-2xl font-bold text-gray-800">12</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'admin', middleware: ['admin'] })
</script>
