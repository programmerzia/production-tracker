<script setup lang="ts">
import { ref, computed, onMounted } from 'vue' // <-- added onMounted
import { useAuthStore } from '~/stores/auth'
import { Home, Package, FolderOpen, CheckSquare, BarChart3, Menu, LogOut } from 'lucide-vue-next'

const auth = useAuthStore()
const mobileMenuOpen = ref(false)

// Only show menu items when ready on client
const menuItems = computed(() => {

  return [
    { label: 'Dashboard', icon: Home, to: '/dashboard', visible: true },
    { label: 'Products', icon: Package, to: '/dashboard/products', visible: auth.canManageProducts },
    { label: 'Projects', icon: FolderOpen, to: '/dashboard/projects', visible: auth.canManageProjects },
    { label: 'Items', icon: CheckSquare, to: '/dashboard/items', visible: auth.canManageItems },
    { label: 'Summary', icon: BarChart3, to: '/dashboard/reports', visible: auth.canViewSummaries }
  ].filter(i => i.visible)
})
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <div class="flex items-center space-x-8">
            <h1 class="text-xl font-semibold text-gray-900">Production Tracker</h1>

            <div class="hidden md:flex space-x-6">
              <NuxtLink
                v-for="item in menuItems"
                :key="item.label"
                :to="item.to"
                class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium flex items-center"
                :class="{ 'text-blue-600 bg-blue-50': $route.path.startsWith(item.to) }"
              >
                <component :is="item.icon" class="w-4 h-4 mr-1" />
                {{ item.label }}
              </NuxtLink>
            </div>
          </div>

          <!-- Mobile menu button -->
          <button
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none"
            aria-label="Toggle menu"
          >
            <Menu class="h-6 w-6" />
          </button>

          <!-- User menu -->
          <div class="hidden md:flex items-center space-x-4" v-if="auth.user">
            <div class="text-sm">
              <p class="font-medium text-gray-700">{{ auth.user.name }}</p>
              <p class="text-xs text-gray-500 capitalize">{{ auth.user.role.replace('_', ' ') }}</p>
            </div>
            <button @click="auth.logout" class="text-gray-500 hover:text-gray-700 focus:outline-none" aria-label="Logout">
              <LogOut class="h-5 w-5" />
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile menu -->
      <div v-show="mobileMenuOpen" class="md:hidden bg-white border-t border-gray-200">
        <div class="px-3 pt-2 pb-3 space-y-1">
          <NuxtLink
            v-for="item in menuItems"
            :key="item.label + '-mobile'"
            :to="item.to"
            @click="mobileMenuOpen = false"
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-500 hover:text-gray-700 flex items-center"
          >
            <component :is="item.icon" class="w-4 h-4 mr-2" />
            {{ item.label }}
          </NuxtLink>

          <div class="border-t border-gray-200 pt-4" v-if="auth.user">
            <p class="px-3 py-2 text-sm font-medium text-gray-800">{{ auth.user.name }}</p>
            <p class="px-3 text-xs text-gray-500 capitalize">{{ auth.user.role.replace('_', ' ') }}</p>
            <button
              @click="auth.logout"
              class="block w-full text-left px-3 py-2 text-sm text-gray-500 hover:text-gray-700 flex items-center"
            >
              <LogOut class="w-4 h-4 mr-2" />
              Sign out
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <slot />
    </main>
  </div>
</template>
