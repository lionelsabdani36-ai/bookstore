import { defineStore } from 'pinia'
import { ref } from 'vue'
import { useAuthStore } from './auth'
import { useRuntimeConfig } from '#app'

export const useCategoryStore = defineStore('category', () => {
  const categories = ref<any[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const authStore = useAuthStore()
  const config = useRuntimeConfig()
  const apiBase = process.server ? 'http://127.0.0.1:8000/api/v1' : config.public.apiBase

  const fetchCategories = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await $fetch<any>(`${apiBase}/admin/categories`, {
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          Accept: 'application/json'
        }
      })
      categories.value = response.data
    } catch (err: any) {
      error.value = err?.data?.message || 'Failed to fetch categories'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  const createCategory = async (name: string) => {
    loading.value = true
    error.value = null
    try {
      const response = await $fetch<any>(`${apiBase}/admin/categories`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          Accept: 'application/json'
        },
        body: { name }
      })
      categories.value.push(response.data)
      return response.data
    } catch (err: any) {
      error.value = err?.data?.message || 'Failed to create category'
      console.error(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateCategory = async (id: number, name: string) => {
    loading.value = true
    error.value = null
    try {
      const response = await $fetch<any>(`${apiBase}/admin/categories/${id}`, {
        method: 'PUT',
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          Accept: 'application/json'
        },
        body: { name }
      })
      const index = categories.value.findIndex(c => c.id === id)
      if (index !== -1) {
        categories.value[index] = response.data
      }
      return response.data
    } catch (err: any) {
      error.value = err?.data?.message || 'Failed to update category'
      console.error(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteCategory = async (id: number) => {
    loading.value = true
    error.value = null
    try {
      await $fetch(`${apiBase}/admin/categories/${id}`, {
        method: 'DELETE',
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          Accept: 'application/json'
        }
      })
      categories.value = categories.value.filter(c => c.id !== id)
    } catch (err: any) {
      error.value = err?.data?.message || 'Failed to delete category'
      console.error(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  return { categories, loading, error, fetchCategories, createCategory, updateCategory, deleteCategory }
})
