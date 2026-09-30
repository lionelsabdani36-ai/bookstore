<template>
  <div class="flex flex-col h-full bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="p-4 border-b bg-gradient-to-r from-primary-50 to-white rounded-t-xl">
      <h3 class="text-lg font-semibold text-primary-700">{{ title }}</h3>
    </div>
    
    <div class="flex-1 p-4 overflow-y-auto space-y-4 bg-gray-50">
      <div v-for="msg in messages" :key="msg.id" :class="['flex', msg.isMine ? 'justify-end' : 'justify-start']">
        <div :class="['max-w-[70%] rounded-2xl px-4 py-2 text-sm', msg.isMine ? 'bg-primary-600 text-white rounded-br-none' : 'bg-white border text-gray-800 rounded-bl-none']">
          {{ msg.text }}
        </div>
      </div>
    </div>

    <div class="p-4 border-t bg-white rounded-b-xl">
      <form @submit.prevent="sendMessage" class="flex gap-2">
        <input v-model="newMessage" type="text" placeholder="Type a message..." class="flex-1 rounded-full px-4 py-2 border border-gray-300 focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500" />
        <button type="submit" :disabled="!newMessage.trim()" class="bg-primary-600 text-white rounded-full p-2 w-10 h-10 flex items-center justify-center hover:bg-primary-700 disabled:opacity-50">
          <span class="sr-only">Send</span>
          &rarr;
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
  title: {
    type: String,
    default: 'Chat'
  },
  messages: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['send']);
const newMessage = ref('');

const sendMessage = () => {
  if (newMessage.value.trim()) {
    emit('send', newMessage.value);
    newMessage.value = '';
  }
};
</script>
