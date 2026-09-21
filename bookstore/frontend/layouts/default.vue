<template>
  <div class="min-h-screen flex flex-col font-sans text-gray-800">
    <header class="bg-white shadow-sm sticky top-0 z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <NuxtLink to="/" class="text-2xl font-bold text-primary-600">Bookstore</NuxtLink>
        <nav class="flex gap-4">
          <NuxtLink to="/our-story" class="hover:text-primary-600">Our Story</NuxtLink>
          <NuxtLink to="/blog" class="hover:text-primary-600">Blog</NuxtLink>
          <NuxtLink to="/contact" class="hover:text-primary-600">Contact</NuxtLink>
          <NuxtLink to="/login" class="text-primary-600 font-medium">Login</NuxtLink>
        </nav>
      </div>
    </header>
    <main class="flex-grow">
      <slot />
    </main>
    <footer class="bg-gray-50 border-t py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
        &copy; 2026 Bookstore. All rights reserved.
      </div>
    </footer>
  </div>
</template>
