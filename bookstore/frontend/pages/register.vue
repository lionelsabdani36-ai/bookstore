<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-lg border border-gray-100">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          {{ step === 1 ? 'Create your account' : 'Verify your account' }}
        </h2>
      </div>

      <div v-if="authStore.error || localError" class="rounded-md bg-red-50 p-4">
        <p class="text-sm text-red-700">{{ authStore.error || localError }}</p>
      </div>
      <div v-if="localSuccess" class="rounded-md bg-green-50 p-4">
        <p class="text-sm text-green-700">{{ localSuccess }}</p>
      </div>

      <form v-if="step === 1" class="mt-8 space-y-6" @submit.prevent="handleRegister">
        <div class="rounded-md shadow-sm -space-y-px">
          <div>
            <label for="name" class="sr-only">Full Name</label>
            <input id="name" v-model="name" name="name" type="text" autocomplete="name" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm" placeholder="Full Name" />
          </div>
          <div>
            <label for="username" class="sr-only">Username</label>
            <input id="username" v-model="username" name="username" type="text" autocomplete="username" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm" placeholder="Username" />
          </div>
          <div>
            <label for="email-address" class="sr-only">Email address</label>
            <input id="email-address" v-model="email" name="email" type="email" autocomplete="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm" placeholder="Email address" />
          </div>
          <div>
            <label for="whatsapp_number" class="sr-only">WhatsApp Number</label>
            <input id="whatsapp_number" v-model="whatsapp_number" name="whatsapp_number" type="text" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm" placeholder="WhatsApp Number (Optional)" />
          </div>
          <div>
            <label for="password" class="sr-only">Password</label>
            <input id="password" v-model="password" name="password" type="password" autocomplete="new-password" required minlength="6" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm" placeholder="Password (min. 6 characters)" />
          </div>
        </div>

        <div class="flex items-center justify-between text-sm">
          <span class="text-gray-600">Already have an account?</span>
          <NuxtLink to="/login" class="font-medium text-primary-600 hover:text-primary-500">Sign in instead</NuxtLink>
        </div>

        <div>
          <button type="submit" :disabled="authStore.loading" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed">
            <span v-if="authStore.loading">Creating account...</span>
            <span v-else>Register</span>
          </button>
        </div>
      </form>

      <div v-else class="mt-8 space-y-6">
        <div class="space-y-4">
          <div class="p-4 border rounded-md">
            <div class="flex justify-between items-center mb-2">
              <span class="font-medium">Email Verification</span>
              <span v-if="authStore.user?.email_verified_at" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Verified</span>
              <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Unverified</span>
            </div>
            <div v-if="!authStore.user?.email_verified_at" class="flex gap-2">
              <button type="button" @click="sendOtp('email')" class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm hover:bg-gray-200">Send OTP</button>
              <input v-model="emailCode" type="text" placeholder="Enter code" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm" />
              <button @click="verify('email', emailCode)" :disabled="isVerifying" class="px-4 py-2 bg-primary-600 text-white rounded-md text-sm hover:bg-primary-700 disabled:opacity-50">Verify</button>
            </div>
          </div>

          <div v-if="whatsapp_number || authStore.user?.whatsapp_number" class="p-4 border rounded-md">
            <div class="flex justify-between items-center mb-2">
              <span class="font-medium">WhatsApp Verification</span>
              <span v-if="authStore.user?.whatsapp_verified_at" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Verified</span>
              <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Unverified</span>
            </div>
            <div v-if="!authStore.user?.whatsapp_verified_at" class="flex gap-2">
              <button type="button" @click="sendOtp('whatsapp')" class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm hover:bg-gray-200">Send OTP</button>
              <input v-model="waCode" type="text" placeholder="Enter code" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm" />
              <button @click="verify('whatsapp', waCode)" :disabled="isVerifying" class="px-4 py-2 bg-primary-600 text-white rounded-md text-sm hover:bg-primary-700 disabled:opacity-50">Verify</button>
            </div>
          </div>
        </div>

        <button @click="navigateTo('/user')" class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
          Continue to Dashboard
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '~/stores/auth'
import { useRuntimeConfig } from '#app'

definePageMeta({ middleware: ['guest'] })

const authStore = useAuthStore()
const config = useRuntimeConfig()

const step = ref(1)
const name = ref('')
const username = ref('')
const email = ref('')
const password = ref('')
const whatsapp_number = ref('')

const emailCode = ref('')
const waCode = ref('')
const isVerifying = ref(false)
const localError = ref('')
const localSuccess = ref('')

const handleRegister = async () => {
  localError.value = ''
  try {
    await authStore.register({
      name: name.value,
      username: username.value,
      email: email.value,
      password: password.value,
      whatsapp_number: whatsapp_number.value
    })
    step.value = 2
  } catch (err) {
    // Error is set in the store
  }
}

const sendOtp = async (type) => {
  try {
    await $fetch(`${config.public.apiBase}/send-otp`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${authStore.token}` },
      body: { type }
    })
    localSuccess.value = `OTP sent to your ${type}!`
  } catch (err) {
    localError.value = `Failed to send OTP to ${type}`
  }
}

const verify = async (type, code) => {
  if (!code) {
    localError.value = 'Please enter a verification code'
    return
  }
  
  isVerifying.value = true
  localError.value = ''
  localSuccess.value = ''
  
  try {
    await $fetch(`${config.public.apiBase}/verify`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${authStore.token}` },
      body: { type, code }
    })
    await authStore.fetchUser()
    localSuccess.value = `${type === 'email' ? 'Email' : 'WhatsApp'} verified successfully!`
    if (type === 'email') emailCode.value = ''
    if (type === 'whatsapp') waCode.value = ''
  } catch (err) {
    localError.value = err?.data?.message || 'Verification failed'
  } finally {
    isVerifying.value = false
  }
}
</script>
