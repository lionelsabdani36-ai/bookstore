export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore()

  // If we have a token but no user data, try to fetch the user
  if (authStore.token && !authStore.user) {
    await authStore.fetchUser()
  }

  // If already authenticated, redirect away from guest pages
  if (authStore.isAuthenticated) {
    if (authStore.isAdmin) {
      return navigateTo('/admin')
    }
    return navigateTo('/user')
  }
})
