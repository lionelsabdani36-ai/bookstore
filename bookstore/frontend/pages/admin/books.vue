<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold text-gray-800">Books Management</h2>
      <button class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">Add Book</button>
    </div>
    <DataTable :columns="columns" :data="booksStore.books" actions>
      <template #actions="{ item }">
        <button class="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
        <button class="text-red-600 hover:text-red-800">Delete</button>
      </template>
    </DataTable>
  </div>
</template>

<script setup>
import { useBooksStore } from '~/stores/books'

definePageMeta({ layout: 'admin' })

const booksStore = useBooksStore()
const columns = [
  { key: 'id', label: 'ID' },
  { key: 'title', label: 'Title' },
  { key: 'author', label: 'Author' },
  { key: 'price', label: 'Price ($)' }
]
</script>
