<template>
  <div class="min-h-[80vh] flex flex-col">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Customer Support Chat</h2>

    <div v-if="chatStore.error" class="mb-4 p-3 bg-red-50 text-red-600 rounded-xl text-sm">
      {{ chatStore.error }}
    </div>

    <div class="flex-1 flex bg-white rounded-xl shadow overflow-hidden" style="min-height: 500px;">
      <!-- Users List (Sidebar) -->
      <div class="w-1/3 border-r border-gray-200 bg-gray-50 flex flex-col">
        <div class="p-4 border-b border-gray-200 bg-white font-semibold text-gray-700">
          Customers
        </div>
        <div class="flex-1 overflow-y-auto">
          <div
            v-for="user in chatUsers"
            :key="user.id"
            @click="selectUser(user.id)"
            class="p-4 border-b border-gray-100 cursor-pointer hover:bg-blue-50 transition-colors"
            :class="{ 'bg-blue-100 border-l-4 border-l-blue-600': chatStore.selectedUserId === user.id }"
          >
            <div class="font-medium text-gray-800">{{ user.name }}</div>
            <div class="text-xs text-gray-500 truncate">{{ user.email }}</div>
          </div>
          <div v-if="chatUsers.length === 0" class="p-4 text-sm text-gray-500 text-center">
            No customers found.
          </div>
        </div>
      </div>

      <!-- Messages Pane -->
      <div class="flex-1 flex flex-col bg-white">
        <template v-if="chatStore.selectedUserId">
          <div class="p-4 border-b border-gray-200 bg-white font-semibold text-gray-700 flex justify-between items-center">
            <span>{{ selectedUserName }}</span>
          </div>
          
          <div ref="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-3 bg-gray-50">
            <div v-if="filteredMessages.length === 0" class="text-center text-gray-400 text-sm py-10">
              No messages yet. Send a message to start!
            </div>
            <template v-else>
              <div
                v-for="msg in filteredMessages"
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
        </template>
        <div v-else class="flex-1 flex items-center justify-center text-gray-400 bg-gray-50">
          Select a customer to start chatting
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: ['admin'] })

import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { useChatStore } from '~/stores/chat'
import { useAuthStore } from '~/stores/auth'

const chatStore = useChatStore()
const authStore = useAuthStore()

const newMessage = ref('')
const messagesContainer = ref<HTMLElement | null>(null)

const myId = computed(() => authStore.user?.id)

// Extract unique users from all messages (where role != admin)
const chatUsers = computed(() => {
  const usersMap = new Map()
  chatStore.messages.forEach((msg: any) => {
    // If sender is not me, it's a customer
    if (msg.sender && msg.sender.id !== myId.value) {
      usersMap.set(msg.sender.id, msg.sender)
    }
    // If receiver is not me, it's a customer
    if (msg.receiver && msg.receiver.id !== myId.value) {
      usersMap.set(msg.receiver.id, msg.receiver)
    }
  })
  return Array.from(usersMap.values())
})

const selectedUserName = computed(() => {
  if (!chatStore.selectedUserId) return ''
  const user = chatUsers.value.find((u: any) => u.id === chatStore.selectedUserId)
  return user ? user.name : 'Unknown User'
})

// Filter messages for the selected user
const filteredMessages = computed(() => {
  if (!chatStore.selectedUserId) return []
  return chatStore.messages.filter((msg: any) => 
    msg.sender_id === chatStore.selectedUserId || msg.receiver_id === chatStore.selectedUserId
  ).sort((a: any, b: any) => new Date(a.created_at).getTime() - new Date(b.created_at).getTime())
})

function selectUser(id: number) {
  chatStore.selectedUserId = id
  nextTick(() => {
    scrollToBottom()
  })
}

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
  },
  { deep: true }
)

onMounted(async () => {
  await chatStore.fetchMessages()
  chatStore.startPolling()
})

onUnmounted(() => {
  chatStore.stopPolling()
})
</script>
