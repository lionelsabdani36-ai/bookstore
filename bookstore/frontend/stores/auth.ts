import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useCookie, useRuntimeConfig, navigateTo } from '#app'

export const useAuthStore = defineStore('auth', () => {
  // Read initial state from cookies (works in SSR and client)
  const token = ref<string | null>(useCookie<string | null>('auth_token').value || null)
  const user = ref<any>(useCookie<any>('auth_user').value || null)
  const error = ref<string | null>(null)
  const loading = ref(false)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  const config = useRuntimeConfig()
  const apiBase = process.server ? 'http://127.0.0.1:8000/api/v1' : config.public.apiBase

  const login = async (credentials: { email: string; password: string; remember?: boolean }) => {
    loading.value = true
    error.value = null

    try {
      const response = await $fetch<any>(`${apiBase}/login`, {
        method: 'POST',
        headers: { Accept: 'application/json' },
        body: credentials,
      })

      const newToken = response.data.token
      const newUser = response.data.user

      token.value = newToken
      user.value = newUser

      // Cookie options: 30 days if remember me is checked, otherwise session (no maxAge)
      const maxAge = credentials.remember ? 60 * 60 * 24 * 30 : undefined
      const tCookie = useCookie('auth_token', { maxAge, path: '/' })
      const uCookie = useCookie('auth_user', { maxAge, path: '/' })
      
      tCookie.value = newToken
      uCookie.value = newUser

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
      const response = await $fetch<any>(`${apiBase}/me`, {
        headers: { 
          Authorization: `Bearer ${token.value}`,
          Accept: 'application/json'
        },
      })
      user.value = response.data.user
      
      const uCookie = useCookie('auth_user', { path: '/' })
      uCookie.value = response.data.user
      
      return response.data.user
    } catch (error) {
      console.error('[fetchUser error]:', error)
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
    
    // Clear cookies by setting to null
    const tCookie = useCookie('auth_token', { path: '/' })
    const uCookie = useCookie('auth_user', { path: '/' })
    tCookie.value = null
    uCookie.value = null
    
    // Clean up legacy localStorage if it exists
    if (process.client) {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
    }
  }

  return { user, token, error, loading, isAuthenticated, isAdmin, login, logout, fetchUser, clearAuth }
})
