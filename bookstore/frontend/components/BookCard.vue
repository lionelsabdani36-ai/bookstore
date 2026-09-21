<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-200">
    <img :src="book.cover_image || '/placeholder.jpg'" :alt="book.title" class="w-full h-48 object-cover" />
    <div class="p-4">
      <h3 class="text-lg font-semibold text-gray-900 truncate">{{ book.title }}</h3>
      <p class="text-sm text-gray-500 mb-2">{{ book.author }}</p>
      <div class="flex justify-between items-center mt-4">
        <span class="text-primary-600 font-bold">${{ book.price }}</span>
        <button @click="$emit('add-to-cart', book)" class="px-3 py-1 bg-primary-100 text-primary-700 rounded-lg hover:bg-primary-200 text-sm font-medium">
          Add to Cart
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  book: {
    type: Object,
    required: true
  }
});
defineEmits(['add-to-cart']);
</script>
