<template>
  <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">My Profile</h2>
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
          <h3 class="text-lg leading-6 font-medium text-gray-900">User Information</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">Personal details and settings.</p>
        </div>
        <div>
          <img 
            v-if="authStore.user?.photo" 
            :src="`http://localhost:8000/storage/${authStore.user.photo}`" 
            class="h-16 w-16 rounded-full object-cover border border-gray-200"
            alt="Profile Photo"
          />
          <div 
            v-else 
            class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xl"
          >
            {{ (authStore.user?.name || 'A').charAt(0).toUpperCase() }}
          </div>
        </div>
      </div>
      <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
        <form @submit.prevent="saveProfile" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Email Address</label>
            <div class="mt-1 flex items-center gap-2">
              <input v-model="form.email" type="email" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required />
              <span v-if="authStore.user?.email_verified_at" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Verified</span>
              <template v-else>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Unverified</span>
                <button v-if="!showEmailInput" type="button" @click="sendOtp('email')" class="ml-2 text-sm text-primary-600 hover:text-primary-500">Send OTP</button>
                <div v-if="showEmailInput" class="ml-2 flex items-center gap-2">
                  <input v-model="emailCode" type="text" placeholder="OTP" class="w-20 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                  <button type="button" @click="verifyOtp('email', emailCode)" class="text-sm text-primary-600 hover:text-primary-500">Verify</button>
                </div>
              </template>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">WhatsApp Number</label>
            <div class="mt-1 flex items-center gap-2">
              <input v-model="form.whatsapp_number" type="text" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
              <template v-if="!authStore.user?.whatsapp_number">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Unregistered</span>
              </template>
              <template v-else-if="authStore.user?.whatsapp_verified_at">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Verified</span>
              </template>
              <template v-else>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Unverified</span>
                <button v-if="!showWaInput" type="button" @click="sendOtp('whatsapp')" class="ml-2 text-sm text-primary-600 hover:text-primary-500">Send OTP</button>
                <div v-if="showWaInput" class="ml-2 flex items-center gap-2">
                  <input v-model="waCode" type="text" placeholder="OTP" class="w-20 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                  <button type="button" @click="verifyOtp('whatsapp', waCode)" class="text-sm text-primary-600 hover:text-primary-500">Verify</button>
                </div>
              </template>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Profile Photo</label>
            <input type="file" @change="onFileChange" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
          </div>
          <div class="pt-4">
            <button type="submit" :disabled="loading" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
              {{ loading ? 'Saving...' : 'Save Profile' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '~/stores/auth'
import { useRuntimeConfig } from '#app'

definePageMeta({
  layout: 'admin',
  middleware: ['auth', 'admin']
})

const authStore = useAuthStore()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase

const loading = ref(false)
const form = ref({
  name: '',
  email: '',
  whatsapp_number: ''
})
const photoFile = ref(null)

onMounted(() => {
  if (authStore.user) {
    form.value.name = authStore.user.name
    form.value.email = authStore.user.email
    form.value.whatsapp_number = authStore.user.whatsapp_number || ''
  }
})

const onFileChange = (e) => {
  if (e.target.files.length > 0) {
    photoFile.value = e.target.files[0]
  }
}

const saveProfile = async () => {
  loading.value = true
  try {
    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('email', form.value.email)
    if (form.value.whatsapp_number) {
      formData.append('whatsapp_number', form.value.whatsapp_number)
    }
    if (photoFile.value) {
      formData.append('photo', photoFile.value)
    }
    
    await $fetch(`${apiBase}/profile`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${authStore.token}` },
      body: formData
    })
    
    await authStore.fetchUser()
    alert('Profile updated successfully!')
  } catch (err) {
    console.error(err)
    alert('Failed to update profile')
  } finally {
    loading.value = false
  }
}

const showEmailInput = ref(false)
const showWaInput = ref(false)
const emailCode = ref('')
const waCode = ref('')

const sendOtp = async (type) => {
  try {
    await $fetch(`${apiBase}/send-otp`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${authStore.token}` },
      body: { type }
    })
    if (type === 'email') showEmailInput.value = true
    if (type === 'whatsapp') showWaInput.value = true
    alert(`OTP sent to your ${type}!`)
  } catch (err) {
    console.error(err)
    alert(`Failed to send OTP to ${type}`)
  }
}

const verifyOtp = async (type, code) => {
  if (!code) return alert('Please enter the OTP code')
  try {
    await $fetch(`${apiBase}/verify`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${authStore.token}` },
      body: { type, code }
    })
    await authStore.fetchUser()
    if (type === 'email') showEmailInput.value = false
    if (type === 'whatsapp') showWaInput.value = false
    alert(`${type} verified successfully!`)
  } catch (err) {
    console.error(err)
    alert(`Failed to verify ${type}`)
  }
}
</script>
