<template>
  <div class="space-y-6 h-full flex flex-col">
    <h2 class="text-2xl font-bold text-gray-800">Customer Support Chat</h2>
    <div class="flex-1 min-h-[400px]">
      <ChatWindow title="Chat with Users" :messages="chatStore.messages" @send="chatStore.sendMessage" />
    </div>
  </div>
</template>

<script setup>
import { useChatStore } from '~/stores/chat'

definePageMeta({ layout: 'admin' })
const chatStore = useChatStore()
</script>
