import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useChatStore = defineStore('chat', () => {
  const messages = ref([
    { id: 1, text: 'Hello, how can we help you?', isMine: false },
  ])

  const sendMessage = (text) => {
    messages.value.push({ id: Date.now(), text, isMine: true })
    setTimeout(() => {
      messages.value.push({ id: Date.now() + 1, text: 'This is an automated reply.', isMine: false })
    }, 1000)
  }

  return { messages, sendMessage }
})
