import { defineStore } from 'pinia'
import { ref } from 'vue'
import { useAuthStore } from './auth'
import { useRuntimeConfig } from '#app'

export const useBooksStore = defineStore('books', () => {
  const books = ref<any[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const authStore = useAuthStore()
  const config = useRuntimeConfig()
  const apiBase = process.server ? 'http://127.0.0.1:8000/api/v1' : config.public.apiBase

  const fetchBooks = async () => {
    loading.value = true
    error.value = null
    try {
      const endpoint = authStore.isAdmin ? '/admin/books' : '/books'
      const response = await $fetch<any>(`${apiBase}${endpoint}`, {
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          Accept: 'application/json'
        }
      })
      books.value = response.data
    } catch (err: any) {
      error.value = err?.data?.message || 'Failed to fetch books'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  const createBook = async (formData: FormData) => {
    loading.value = true
    error.value = null
    try {
      const response = await $fetch<any>(`${apiBase}/admin/books`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          Accept: 'application/json'
        },
        body: formData
      })
      books.value.push(response.data)
      return response.data
    } catch (err: any) {
      error.value = err?.data?.message || 'Failed to create book'
      console.error(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateBook = async (id: number, formData: FormData) => {
    loading.value = true
    error.value = null
    try {
      formData.append('_method', 'PUT')
      const response = await $fetch<any>(`${apiBase}/admin/books/${id}`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          Accept: 'application/json'
        },
        body: formData
      })
      const index = books.value.findIndex(b => b.id === id)
      if (index !== -1) {
        books.value[index] = response.data
      }
      return response.data
    } catch (err: any) {
      error.value = err?.data?.message || 'Failed to update book'
      console.error(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteBook = async (id: number) => {
    loading.value = true
    error.value = null
    try {
      await $fetch(`${apiBase}/admin/books/${id}`, {
        method: 'DELETE',
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          Accept: 'application/json'
        }
      })
      books.value = books.value.filter(b => b.id !== id)
    } catch (err: any) {
      error.value = err?.data?.message || 'Failed to delete book'
      console.error(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  return { books, loading, error, fetchBooks, createBook, updateBook, deleteBook }
})
