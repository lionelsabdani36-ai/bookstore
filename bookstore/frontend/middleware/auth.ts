export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore()

  // If we have a token but no user data, try to fetch the user
  if (authStore.token && !authStore.user) {
    await authStore.fetchUser()
  }

  // If still not authenticated, redirect to login
  if (!authStore.isAuthenticated) {
    return navigateTo('/login')
  }
})
