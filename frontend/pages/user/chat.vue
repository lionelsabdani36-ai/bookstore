<template>
  <div class="min-h-[80vh] flex flex-col">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Chat with Support</h1>

    <div v-if="chatStore.error" class="mb-4 p-3 bg-red-50 text-red-600 rounded-xl text-sm">
      {{ chatStore.error }}
    </div>

    <div class="flex-1 flex flex-col bg-white rounded-xl shadow overflow-hidden" style="min-height: 500px;">
      <!-- Messages -->
      <div ref="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-3 bg-gray-50">
        <div v-if="chatStore.loading" class="text-center text-gray-400 text-sm py-10">
          Loading messages...
        </div>
        <div v-else-if="chatStore.messages.length === 0" class="text-center text-gray-400 text-sm py-10">
          <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
          </svg>
          <p>No messages yet. Send a message to start a conversation!</p>
        </div>
        <template v-else>
          <div
            v-for="msg in chatStore.messages"
            :key="msg.id"
            class="flex"
            :class="msg.sender_id === myId ? 'justify-end' : 'justify-start'"
          >
            <div
              class="max-w-xs lg:max-w-md px-4 py-2.5 rounded-2xl text-sm"
              :class="msg.sender_id === myId
                ? 'bg-blue-600 text-white rounded-br-md'
                : 'bg-white text-gray-800 shadow-sm border border-gray-100 rounded-bl-md'"
            >
              <p>{{ msg.message }}</p>
              <p
                class="text-[10px] mt-1"
                :class="msg.sender_id === myId ? 'text-blue-200' : 'text-gray-400'"
              >
                {{ formatTime(msg.created_at) }}
              </p>
            </div>
          </div>
        </template>
      </div>

      <!-- Input Bar -->
      <div class="px-4 py-3 border-t border-gray-200 bg-white">
        <form @submit.prevent="handleSend" class="flex gap-2">
          <input
            v-model="newMessage"
            type="text"
            placeholder="Type a message..."
            class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
          <button
            type="submit"
            :disabled="!newMessage.trim()"
            class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Send
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', middleware: ['auth'] })

import { ref, computed, onMounted, nextTick, watch } from 'vue'
import { useChatStore } from '~/stores/chat'
import { useAuthStore } from '~/stores/auth'

const chatStore = useChatStore()
const authStore = useAuthStore()

const newMessage = ref('')
const messagesContainer = ref<HTMLElement | null>(null)

const myId = computed(() => authStore.user?.id)

async function handleSend() {
  const text = newMessage.value.trim()
  if (!text) return
  newMessage.value = ''
  await chatStore.sendMessage(text)
  await nextTick()
  scrollToBottom()
}

function scrollToBottom() {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

function formatTime(dateStr: string) {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

watch(
  () => chatStore.messages,
  async () => {
    await nextTick()
    scrollToBottom()
  }
)

onMounted(async () => {
  await chatStore.fetchMessages()
  await nextTick()
  scrollToBottom()
  chatStore.startPolling()
})

import { onUnmounted } from 'vue'
onUnmounted(() => {
  chatStore.stopPolling()
})
</script>
