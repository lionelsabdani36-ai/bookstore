import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(process.client ? JSON.parse(localStorage.getItem('auth_user') || 'null') : null)
  const token = ref(process.client ? localStorage.getItem('auth_token') : null)
  const error = ref(null)
  const loading = ref(false)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase

  const login = async (credentials: { email: string; password: string }) => {
    loading.value = true
    error.value = null

    try {
      const response = await $fetch(`${apiBase}/login`, {
        method: 'POST',
        body: credentials,
      })

      token.value = response.data.token
      user.value = response.data.user
      if (process.client) {
        localStorage.setItem('auth_token', response.data.token)
        localStorage.setItem('auth_user', JSON.stringify(response.data.user))
      }
    } catch (err: any) {
      const message = err?.data?.message || err?.data?.errors?.email?.[0] || 'Login failed'
      error.value = message
      throw new Error(message)
    } finally {
      loading.value = false
    }
  }

  const fetchUser = async () => {
    if (!token.value) return null
    try {
      const response = await $fetch(`${apiBase}/me`, {
        headers: { Authorization: `Bearer ${token.value}` },
      })
      user.value = response.data.user
      if (process.client) {
        localStorage.setItem('auth_user', JSON.stringify(response.data.user))
      }
      return response.data.user
    } catch {
      // Token is invalid/expired — clear everything
      clearAuth()
      return null
    }
  }

  const logout = async () => {
    try {
      if (token.value) {
        await $fetch(`${apiBase}/logout`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${token.value}` },
        })
      }
    } catch {
      // Ignore errors — we still want to clear local state
    } finally {
      clearAuth()
      navigateTo('/login')
    }
  }

  const clearAuth = () => {
    user.value = null
    token.value = null
    error.value = null
    if (process.client) {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
    }
  }

  return { user, token, error, loading, isAuthenticated, isAdmin, login, logout, fetchUser, clearAuth }
})
