<template>
  <div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
        Welcome to Bookstore
      </h1>
      <p class="mt-4 text-xl text-gray-500 max-w-2xl mx-auto">
        Discover the best books from around the world. Let your imagination run wild.
      </p>
      <div class="mt-8 flex justify-center gap-4">
        <NuxtLink to="/register" class="px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 md:text-lg">
          Get Started
        </NuxtLink>
        <NuxtLink to="/login" class="px-8 py-3 border border-transparent text-base font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 md:text-lg">
          Login
        </NuxtLink>
      </div>
    </div>
  </div>
</template>
