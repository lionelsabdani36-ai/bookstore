<template>
  <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-12">
      <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">Blog</h2>
      <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">Latest news, book reviews, and author interviews.</p>
    </div>
    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
      <div v-for="i in 3" :key="i" class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="h-48 bg-gray-200"></div>
        <div class="p-6">
          <p class="text-sm font-medium text-primary-600 mb-1">Book Reviews</p>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">The Best Sci-Fi Novels of 2026</h3>
          <p class="text-gray-500 mb-4">Discover the most mind-bending and imaginative science fiction released this year...</p>
          <button class="text-primary-600 hover:text-primary-800 font-medium text-sm">Read More &rarr;</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>
