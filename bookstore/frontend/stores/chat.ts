import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useRuntimeConfig } from '#app'
import { useAuthStore } from './auth'

export const useChatStore = defineStore('chat', () => {
  const config = useRuntimeConfig()
  const apiBase = process.server ? 'http://127.0.0.1:8000/api/v1' : config.public.apiBase

  const messages = ref<any[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const selectedUserId = ref<number | null>(null)

  const isAdmin = computed(() => {
    const authStore = useAuthStore()
    return authStore.user?.role === 'admin'
  })

  async function fetchMessages() {
    const authStore = useAuthStore()
    loading.value = true
    error.value = null
    try {
      const endpoint = isAdmin.value ? '/admin/chat' : '/chat'
      const data = await $fetch<any>(`${apiBase}${endpoint}`, {
        headers: {
          Authorization: `Bearer ${authStore.token}`,
        },
      })
      messages.value = Array.isArray(data) ? data : (data.data ?? [])
    } catch (e: any) {
      error.value = e?.data?.message || e?.message || 'Failed to fetch messages'
    } finally {
      loading.value = false
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
      await fetchMessages()
    } catch (e: any) {
      error.value = e?.data?.message || e?.message || 'Failed to send message'
    }
  }

  return {
    messages,
    loading,
    error,
    selectedUserId,
    fetchMessages,
    sendMessage,
  }
})
