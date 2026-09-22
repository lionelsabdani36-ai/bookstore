<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-lg border border-gray-100">

      <!-- Header -->
      <div>
        <h2 class="mt-2 text-center text-3xl font-extrabold text-gray-900">Reset your password</h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          <template v-if="step === 1">Choose how you want to receive your OTP code</template>
          <template v-else-if="step === 2">Enter the 6-digit OTP code we sent you</template>
          <template v-else>Create a new password for your account</template>
        </p>
      </div>

      <!-- Success Message -->
      <div v-if="successMessage" class="rounded-md bg-green-50 p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-green-700">{{ successMessage }}</p>
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="rounded-md bg-red-50 p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-red-700">{{ errorMessage }}</p>
          </div>
        </div>
      </div>

      <!-- Step Indicator -->
      <div class="flex items-center justify-center space-x-2">
        <div v-for="s in 3" :key="s" class="flex items-center">
          <div
            :class="[
              'w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium transition-colors duration-200',
              s <= step ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-500'
            ]"
          >
            {{ s }}
          </div>
          <div v-if="s < 3" :class="['w-8 h-0.5 transition-colors duration-200', s < step ? 'bg-primary-600' : 'bg-gray-200']" />
        </div>
      </div>

      <!-- STEP 1: Select Channel & Enter Identifier -->
      <form v-if="step === 1" class="mt-6 space-y-6" @submit.prevent="handleSendOtp">
        <!-- Channel Selection -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-3">Send OTP via</label>
          <div class="grid grid-cols-2 gap-3">
            <button
              type="button"
              :class="[
                'relative flex flex-col items-center p-4 rounded-lg border-2 transition-all duration-200 cursor-pointer',
                channel === 'email'
                  ? 'border-primary-500 bg-primary-50 ring-2 ring-primary-200'
                  : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
              ]"
              @click="channel = 'email'"
            >
              <svg class="w-8 h-8 mb-2" :class="channel === 'email' ? 'text-primary-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <span class="text-sm font-medium" :class="channel === 'email' ? 'text-primary-700' : 'text-gray-600'">Email</span>
            </button>
            <button
              type="button"
              :class="[
                'relative flex flex-col items-center p-4 rounded-lg border-2 transition-all duration-200 cursor-pointer',
                channel === 'whatsapp'
                  ? 'border-green-500 bg-green-50 ring-2 ring-green-200'
                  : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
              ]"
              @click="channel = 'whatsapp'"
            >
              <svg class="w-8 h-8 mb-2" :class="channel === 'whatsapp' ? 'text-green-600' : 'text-gray-400'" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
              </svg>
              <span class="text-sm font-medium" :class="channel === 'whatsapp' ? 'text-green-700' : 'text-gray-600'">WhatsApp</span>
            </button>
          </div>
        </div>

        <!-- Input Field -->
        <div>
          <label :for="channel === 'email' ? 'email' : 'phone'" class="block text-sm font-medium text-gray-700 mb-1">
            {{ channel === 'email' ? 'Email address' : 'WhatsApp number' }}
          </label>
          <input
            :id="channel === 'email' ? 'email' : 'phone'"
            v-model="identifier"
            :type="channel === 'email' ? 'email' : 'tel'"
            :placeholder="channel === 'email' ? 'your@email.com' : '08xxxxxxxxxx'"
            required
            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
          />
          <p v-if="channel === 'whatsapp'" class="mt-1 text-xs text-gray-500">Enter the phone number registered in your account</p>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
            </svg>
            {{ loading ? 'Sending...' : 'Send OTP Code' }}
          </button>
        </div>
      </form>

      <!-- STEP 2: Enter OTP -->
      <form v-else-if="step === 2" class="mt-6 space-y-6" @submit.prevent="handleVerifyOtp">
        <div>
          <label for="otp" class="block text-sm font-medium text-gray-700 mb-1">OTP Code</label>
          <div class="flex justify-center space-x-2">
            <input
              v-for="(_, index) in 6"
              :key="index"
              :ref="(el) => { if (el) otpInputs[index] = el }"
              v-model="otpDigits[index]"
              type="text"
              maxlength="1"
              inputmode="numeric"
              pattern="[0-9]"
              required
              class="w-12 h-12 text-center text-lg font-bold border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
              @input="handleOtpInput(index)"
              @keydown.backspace="handleOtpBackspace(index, $event)"
              @paste="handleOtpPaste($event)"
            />
          </div>
          <p class="mt-2 text-xs text-center text-gray-500">
            Sent to {{ channel === 'email' ? identifier : maskPhone(identifier) }}
          </p>
        </div>

        <!-- Countdown / Resend -->
        <div class="text-center">
          <p v-if="countdown > 0" class="text-sm text-gray-500">
            Resend OTP in <span class="font-medium text-primary-600">{{ countdown }}s</span>
          </p>
          <button
            v-else
            type="button"
            class="text-sm font-medium text-primary-600 hover:text-primary-500"
            @click="handleSendOtp"
          >
            Resend OTP Code
          </button>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading || otpDigits.some(d => !d)"
            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
            </svg>
            {{ loading ? 'Verifying...' : 'Verify OTP' }}
          </button>
        </div>
      </form>

      <!-- STEP 3: New Password -->
      <form v-else class="mt-6 space-y-6" @submit.prevent="handleResetPassword">
        <div>
          <label for="new-password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
          <input
            id="new-password"
            v-model="newPassword"
            type="password"
            required
            minlength="6"
            placeholder="Min. 6 characters"
            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="confirm-password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
          <input
            id="confirm-password"
            v-model="confirmPassword"
            type="password"
            required
            minlength="6"
            placeholder="Re-enter your new password"
            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
          />
          <p v-if="confirmPassword && newPassword !== confirmPassword" class="mt-1 text-xs text-red-500">Passwords do not match</p>
        </div>
        <div>
          <button
            type="submit"
            :disabled="loading || !newPassword || !confirmPassword || newPassword !== confirmPassword"
            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
            </svg>
            {{ loading ? 'Resetting...' : 'Reset Password' }}
          </button>
        </div>
      </form>

      <!-- Back to Login -->
      <div class="text-center">
        <NuxtLink to="/login" class="text-sm font-medium text-primary-600 hover:text-primary-500">
          ← Back to Sign in
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onUnmounted } from 'vue'

definePageMeta({ middleware: ['guest'] })

const config = useRuntimeConfig()
const apiBase = config.public.apiBase

// State
const step = ref(1)
const channel = ref('email')
const identifier = ref('')
const otpDigits = ref(['', '', '', '', '', ''])
const otpInputs = ref([])
const newPassword = ref('')
const confirmPassword = ref('')
const loading = ref(false)
const errorMessage = ref(null)
const successMessage = ref(null)
const countdown = ref(0)

let countdownInterval = null

const clearMessages = () => {
  errorMessage.value = null
  successMessage.value = null
}

const startCountdown = (seconds = 60) => {
  countdown.value = seconds
  if (countdownInterval) clearInterval(countdownInterval)
  countdownInterval = setInterval(() => {
    countdown.value--
    if (countdown.value <= 0) {
      clearInterval(countdownInterval)
      countdownInterval = null
    }
  }, 1000)
}

onUnmounted(() => {
  if (countdownInterval) clearInterval(countdownInterval)
})

// OTP input helpers
const handleOtpInput = (index) => {
  const value = otpDigits.value[index]
  if (value && !/^\d$/.test(value)) {
    otpDigits.value[index] = ''
    return
  }
  if (value && index < 5) {
    otpInputs.value[index + 1]?.focus()
  }
}

const handleOtpBackspace = (index, event) => {
  if (!otpDigits.value[index] && index > 0) {
    event.preventDefault()
    otpDigits.value[index - 1] = ''
    otpInputs.value[index - 1]?.focus()
  }
}

const handleOtpPaste = (event) => {
  event.preventDefault()
  const pasted = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6)
  for (let i = 0; i < 6; i++) {
    otpDigits.value[i] = pasted[i] || ''
  }
  const focusIndex = Math.min(pasted.length, 5)
  otpInputs.value[focusIndex]?.focus()
}

const getOtp = () => otpDigits.value.join('')

const maskPhone = (phone) => {
  if (!phone || phone.length < 4) return phone
  return phone.slice(0, 4) + '****' + phone.slice(-2)
}

// Step 1: Send OTP
const handleSendOtp = async () => {
  clearMessages()
  loading.value = true

  try {
    const response = await $fetch(`${apiBase}/password/send-otp`, {
      method: 'POST',
      body: {
        identifier: identifier.value,
        channel: channel.value,
      },
    })

    successMessage.value = response.message
    step.value = 2
    startCountdown(60)

    // Reset OTP fields
    otpDigits.value = ['', '', '', '', '', '']
    nextTick(() => otpInputs.value[0]?.focus())
  } catch (err) {
    errorMessage.value = err?.data?.message || err?.data?.errors?.identifier?.[0] || 'Failed to send OTP. Please try again.'
  } finally {
    loading.value = false
  }
}

// Step 2: Verify OTP
const handleVerifyOtp = async () => {
  clearMessages()
  loading.value = true

  try {
    const response = await $fetch(`${apiBase}/password/verify-otp`, {
      method: 'POST',
      body: {
        identifier: identifier.value,
        channel: channel.value,
        otp: getOtp(),
      },
    })

    successMessage.value = response.message
    step.value = 3
  } catch (err) {
    errorMessage.value = err?.data?.message || 'Invalid OTP. Please try again.'
  } finally {
    loading.value = false
  }
}

// Step 3: Reset Password
const handleResetPassword = async () => {
  clearMessages()

  if (newPassword.value !== confirmPassword.value) {
    errorMessage.value = 'Passwords do not match.'
    return
  }

  loading.value = true

  try {
    const response = await $fetch(`${apiBase}/password/reset`, {
      method: 'POST',
      body: {
        identifier: identifier.value,
        channel: channel.value,
        otp: getOtp(),
        password: newPassword.value,
        password_confirmation: confirmPassword.value,
      },
    })

    successMessage.value = response.message + ' Redirecting to login...'

    setTimeout(() => {
      navigateTo('/login')
    }, 2000)
  } catch (err) {
    errorMessage.value = err?.data?.message || 'Failed to reset password. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>
