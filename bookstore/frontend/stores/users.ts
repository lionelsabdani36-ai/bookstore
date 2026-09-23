import { defineStore } from 'pinia'
import { ref } from 'vue'
import { useAuthStore } from './auth'
import { useRuntimeConfig } from '#app'

export const useUsersStore = defineStore('users', () => {
  const users = ref<any[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const authStore = useAuthStore()
  const config = useRuntimeConfig()
  const apiBase = process.server ? 'http://127.0.0.1:8000/api/v1' : config.public.apiBase

  const fetchUsers = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await $fetch<any>(`${apiBase}/admin/users`, {
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          Accept: 'application/json'
        }
      })
      users.value = response.data
    } catch (err: any) {
      error.value = err?.data?.message || 'Failed to fetch users'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  const createUser = async (formData: FormData) => {
    loading.value = true
    error.value = null
    try {
      const response = await $fetch<any>(`${apiBase}/admin/users`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          Accept: 'application/json'
        },
        body: formData
      })
      users.value.push(response.data)
      return response.data
    } catch (err: any) {
      error.value = err?.data?.message || 'Failed to create user'
      console.error(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateUser = async (id: number, formData: FormData) => {
    loading.value = true
    error.value = null
    try {
      formData.append('_method', 'PUT')
      const response = await $fetch<any>(`${apiBase}/admin/users/${id}`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          Accept: 'application/json'
        },
        body: formData
      })
      const index = users.value.findIndex(u => u.id === id)
      if (index !== -1) {
        users.value[index] = response.data
      }
      return response.data
    } catch (err: any) {
      error.value = err?.data?.message || 'Failed to update user'
      console.error(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteUser = async (id: number) => {
    loading.value = true
    error.value = null
    try {
      await $fetch(`${apiBase}/admin/users/${id}`, {
        method: 'DELETE',
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          Accept: 'application/json'
        }
      })
      users.value = users.value.filter(u => u.id !== id)
    } catch (err: any) {
      error.value = err?.data?.message || 'Failed to delete user'
      console.error(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  return { users, loading, error, fetchUsers, createUser, updateUser, deleteUser }
})
