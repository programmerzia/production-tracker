<script setup lang="ts">
import { Package, FolderOpen, BarChart3 } from 'lucide-vue-next'

definePageMeta({
  middleware: 'guest'
})
</script>
<template>
  <NuxtLayout name="default">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
      <!-- Hero Section -->
      <div class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto">
          <div class="relative z-10 pb-8 bg-gradient-to-br from-blue-50 to-indigo-100 sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
            <svg
              class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-blue-50 transform translate-x-1/2"
              fill="currentColor"
              viewBox="0 0 100 100"
              preserveAspectRatio="none"
              aria-hidden="true"
            >
              <polygon points="50,0 100,0 50,100 0,100" />
            </svg>

            <div class="relative pt-6 px-4 sm:px-6 lg:px-8">
              <nav class="relative flex items-center justify-between sm:h-10 lg:justify-start" aria-label="Global">
                <div class="flex items-center flex-grow flex-shrink-0 lg:flex-grow-0">
                  <div class="flex items-center justify-between w-full md:w-auto">
                    <h1 class="text-2xl font-bold text-gray-900">
                      Production Tracker
                    </h1>
                  </div>
                </div>
                <div class="hidden md:block md:ml-10 md:pr-4 md:space-x-8">
                  <NuxtLink
                    to="/login"
                    class="font-medium text-gray-500 hover:text-gray-900 transition duration-150 ease-in-out"
                  >
                    Log in
                  </NuxtLink>
                 
                </div>
              </nav>
            </div>

            <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
              <div class="sm:text-center lg:text-left">
                <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                  <span class="block">Streamline your</span>
                  <span class="block text-blue-600">production process</span>
                </h1>
                <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                  Manage products, projects, and production items with ease. Track progress, collaborate with your team, and deliver exceptional results.
                </p>
                <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                  <div class="rounded-md shadow">
                    <NuxtLink
                      to="/register"
                      class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition duration-150 ease-in-out md:py-4 md:text-lg md:px-10"
                    >
                      Get started
                    </NuxtLink>
                  </div>
                  <div class="mt-3 sm:mt-0 sm:ml-3">
                    <NuxtLink
                      to="/login"
                      class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition duration-150 ease-in-out md:py-4 md:text-lg md:px-10"
                    >
                      Sign in
                    </NuxtLink>
                  </div>
                </div>
              </div>
            </main>
          </div>
        </div>
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
          <img
            class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full"
            src="https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2"
            alt="Team working on production"
          />
        </div>
      </div>

      <!-- Features Section -->
      <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="lg:text-center">
            <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase">Features</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
              Everything you need to manage production
            </p>
          </div>

          <div class="mt-10">
            <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-3 md:gap-x-8 md:gap-y-10">
              <div class="text-center">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white mx-auto">
                  <Package class="h-6 w-6" />
                </div>
                <div class="mt-5">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">Product Management</h3>
                  <p class="mt-2 text-base text-gray-500">
                    Create and manage products with versions, descriptions, and pricing information.
                  </p>
                </div>
              </div>

              <div class="text-center">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white mx-auto">
                  <FolderOpen class="h-6 w-6" />
                </div>
                <div class="mt-5">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">Project Tracking</h3>
                  <p class="mt-2 text-base text-gray-500">
                    Organize production initiatives under projects with clear status tracking.
                  </p>
                </div>
              </div>

              <div class="text-center">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white mx-auto">
                  <BarChart3 class="h-6 w-6" />
                </div>
                <div class="mt-5">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">Analytics & Reports</h3>
                  <p class="mt-2 text-base text-gray-500">
                    View summaries and analytics across projects and products for better insights.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </NuxtLayout>
</template>

