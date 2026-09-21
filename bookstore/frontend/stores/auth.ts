import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(process.client ? localStorage.getItem('auth_token') : null)
  const error = ref(null)
  const loading = ref(false)

  const isAuthenticated = computed(() => !!token.value)

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
      if (process.client) localStorage.setItem('auth_token', response.data.token)
    } catch (err: any) {
      const message = err?.data?.message || err?.data?.errors?.email?.[0] || 'Login failed'
      error.value = message
      throw new Error(message)
    } finally {
      loading.value = false
    }
  }

  const logout = () => {
    user.value = null
    token.value = null
    error.value = null
    if (process.client) localStorage.removeItem('auth_token')
    navigateTo('/login')
  }

  return { user, token, error, loading, isAuthenticated, login, logout }
})
