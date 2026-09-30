export default defineNuxtConfig({
  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt'
  ],
  runtimeConfig: {
    public: {
      apiBase: 'http://localhost:8000/api/v1'
    }
  },
  devtools: { enabled: true }
})
