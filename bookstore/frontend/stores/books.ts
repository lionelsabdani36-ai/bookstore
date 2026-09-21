import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useBooksStore = defineStore('books', () => {
  const books = ref([
    { id: 1, title: 'The Great Gatsby', author: 'F. Scott Fitzgerald', price: 10.99, cover_image: '' },
    { id: 2, title: '1984', author: 'George Orwell', price: 8.99, cover_image: '' }
  ])

  const fetchBooks = async () => {
    // Mock fetch
  }

  return { books, fetchBooks }
})
