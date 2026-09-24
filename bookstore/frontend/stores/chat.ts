import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useRuntimeConfig } from '#app'
import { useAuthStore } from './auth'

export const useChatStore = defineStore('chat', () => {
  const config = useRuntimeConfig()
  const apiBase = process.server ? 'http://127.0.0.1:8000/api/v1' : (config.public.apiBase || 'http://localhost:8000/api/v1')

  const messages = ref<any[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const selectedUserId = ref<number | null>(null)
  let pollInterval: any = null

  const isAdmin = computed(() => {
    const authStore = useAuthStore()
    return authStore.user?.role === 'admin'
  })

  async function fetchMessages(background = false) {
    const authStore = useAuthStore()
    if (!background) loading.value = true
    if (!background) error.value = null
    try {
      const endpoint = isAdmin.value ? '/admin/chat' : '/chat'
      const data = await $fetch<any>(`${apiBase}${endpoint}`, {
        headers: {
          Authorization: `Bearer ${authStore.token}`,
        },
      })
      messages.value = Array.isArray(data) ? data : (data.data ?? [])
    } catch (e: any) {
      if (!background) error.value = e?.data?.message || e?.message || 'Failed to fetch messages'
    } finally {
      if (!background) loading.value = false
    }
  }

  async function sendMessage(text: string, receiverId?: number) {
    const authStore = useAuthStore()
    error.value = null
    try {
      if (isAdmin.value) {
        const rid = receiverId ?? selectedUserId.value
        if (!rid) {
          error.value = 'No receiver selected'
          return
        }
        await $fetch<any>(`${apiBase}/admin/chat`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${authStore.token}`,
          },
          body: {
            receiver_id: rid,
            message: text,
          },
        })
      } else {
        await $fetch<any>(`${apiBase}/chat`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${authStore.token}`,
          },
          body: {
            message: text,
          },
        })
      }
      await fetchMessages(true)
    } catch (e: any) {
      error.value = e?.data?.message || e?.message || 'Failed to send message'
    }
  }
  
  function startPolling() {
    if (pollInterval) return
    pollInterval = setInterval(() => {
      fetchMessages(true)
    }, 3000)
  }
  
  function stopPolling() {
    if (pollInterval) {
      clearInterval(pollInterval)
      pollInterval = null
    }
  }

  return {
    messages,
    loading,
    error,
    selectedUserId,
    fetchMessages,
    sendMessage,
    startPolling,
    stopPolling
  }
})
