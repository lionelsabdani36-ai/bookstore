<template>
  <div class="max-w-3xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-12">
      <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">Contact Us</h2>
      <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">We'd love to hear from you. Send us a message!</p>
    </div>
    <div class="bg-white py-8 px-6 shadow rounded-lg sm:px-10">
      <form @submit.prevent="submitForm" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <input v-model="form.name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input v-model="form.email" type="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Message</label>
          <textarea v-model="form.message" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"></textarea>
        </div>
        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
          Send Message
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const form = ref({
  name: '',
  email: '',
  message: ''
})

const submitForm = () => {
  alert(`Thank you, ${form.value.name}! Your message has been sent.`)
  form.value = { name: '', email: '', message: '' }
}
</script>
