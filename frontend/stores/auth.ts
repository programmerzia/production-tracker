import { defineStore } from 'pinia'
import { ref, computed, readonly } from 'vue'
// Use typeof window !== "undefined" to check for client-side
import type { User, AuthResponse, LoginCredentials } from '~/types'
import { useRuntimeConfig, navigateTo } from '#imports'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(null)

  // Restore from localStorage immediately on client
  if (typeof window !== "undefined") {
    const storedToken = localStorage.getItem('token')
    const storedUser = localStorage.getItem('user')
    if (storedToken && storedUser) {
      token.value = storedToken
      user.value = JSON.parse(storedUser)
    }
  }

  const isAuthenticated = computed(() => !!user.value && !!token.value)
  const config = useRuntimeConfig()

  const login = async (credentials: LoginCredentials) => {
    const response = await $fetch<AuthResponse>(`${config.public.apiBase}/login`, {
      method: 'POST',
      body: credentials
    })

    token.value = response.access_token
    user.value = response.user

    if (typeof window !== "undefined") {
      localStorage.setItem('token', response.access_token)
      localStorage.setItem('user', JSON.stringify(response.user))
    }

    return response
  }

  const logout = () => {
    token.value = null
    user.value = null

    if (typeof window !== "undefined") {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    }

    navigateTo('/login')
  }

  // Role helper
  const hasRole = (role: string) => user.value?.role === role

  const canManageProducts = computed(() => hasRole('product_manager'))
  const canManageProjects = computed(() => hasRole('product_manager') || hasRole('project_manager'))
  const canManageItems = computed(() => hasRole('engineer'))
  const canViewSummaries = computed(() => hasRole('project_manager'))

  return {
    user: readonly(user),
    token: readonly(token),
    isAuthenticated,
    login,
    logout,
    canManageProducts,
    canManageProjects,
    canManageItems,
    canViewSummaries
  }
})
