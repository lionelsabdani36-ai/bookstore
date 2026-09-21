import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCartStore = defineStore('cart', () => {
  const items = ref([])

  const addToCart = (book) => {
    const existing = items.value.find(i => i.id === book.id)
    if (existing) {
      existing.quantity++
    } else {
      items.value.push({ ...book, quantity: 1 })
    }
  }

  const removeFromCart = (bookId) => {
    items.value = items.value.filter(i => i.id !== bookId)
  }

  const clearCart = () => {
    items.value = []
  }

  const total = computed(() => {
    return items.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
  })

  return { items, addToCart, removeFromCart, clearCart, total }
})
