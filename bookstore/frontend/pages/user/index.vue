<template>
  <div class="space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">Book Catalog</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <BookCard 
        v-for="book in booksStore.books" 
        :key="book.id" 
        :book="book" 
        @add-to-cart="cartStore.addToCart(book)" 
      />
    </div>
  </div>
</template>

<script setup>
import { useBooksStore } from '~/stores/books'
import { useCartStore } from '~/stores/cart'

definePageMeta({ layout: 'user' })

const booksStore = useBooksStore()
const cartStore = useCartStore()
</script>
