<script setup lang="ts">
import { computed } from 'vue'
import { Package, FolderOpen, CheckSquare, CheckCircle, BarChart3 } from 'lucide-vue-next'
import { useAuthStore } from '~/stores/auth'

definePageMeta({
  layout: 'dashboard',
  middleware: 'auth'
})

const auth = useAuthStore()
const user = computed(() => auth.user)
const canManageProducts = computed(() => auth.canManageProducts)
const canManageProjects = computed(() => auth.canManageProjects)
const canManageItems = computed(() => auth.canManageItems)
const canViewSummaries = computed(() => auth.canViewSummaries)

interface StatsResponse {
  [key: string]: number
}

const { data: statsData, pending, error } = await useFetch<StatsResponse>('/dashboard/stats', {
  baseURL: useRuntimeConfig().public.apiBase,
  headers: {
    Authorization: `Bearer ${auth.token}`
  }
})

const statsAll = [
  { label: 'Total Products', key: 'total_products', icon: Package, color: 'text-blue-400' },
  { label: 'Active Projects', key: 'active_projects', icon: FolderOpen, color: 'text-green-400' },

  // Item statuses
  { label: 'Pending Items', key: 'pending_items', icon: CheckSquare, color: 'text-yellow-400' },
  { label: 'In Progress Items', key: 'in_progress_items', icon: BarChart3, color: 'text-blue-500' },
  { label: 'Completed Items', key: 'completed_items', icon: CheckCircle, color: 'text-purple-400' },
  { label: 'Blocked Items', key: 'blocked_items', icon: CheckSquare, color: 'text-red-400' },
]

// Filtered stats based on role
const keysByRole: Record<string, string[]> = {
  product_manager: [
    'total_products',
    'active_projects',
    'pending_items',
    'in_progress_items',
    'completed_items',
    'blocked_items',
  ],
  project_manager: [
    'active_projects',
    'pending_items',
    'in_progress_items',
    'completed_items',
    'blocked_items',
  ],
  engineer: [
    'pending_items',
    'in_progress_items',
    'completed_items',
    'blocked_items',
  ],
}

const visibleStats = computed(() => {
  const role = user.value?.role
  const allowedKeys = typeof role === 'string' ? keysByRole[role] || [] : []

  return statsAll
    .filter(stat => allowedKeys.includes(stat.key))
    .map(stat => ({
      ...stat,
      value: statsData.value?.[stat.key] ?? 0,
    }))
})
</script>

<template>
  <main class="space-y-8 max-w-7xl mx-auto p-6">
    <!-- Welcome Section -->
    <section aria-label="Welcome message" class="bg-white shadow rounded-lg p-6">
      <h1 class="text-3xl font-semibold text-gray-900">Welcome back, {{ user?.name }}!</h1>
      <p class="mt-1 text-gray-600 capitalize">Role: {{ user?.role?.replace('_', ' ') }}</p>
    </section>

    <!-- Quick Stats -->
<section aria-label="Quick statistics" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
  <article
    v-for="(stat, idx) in visibleStats"
    :key="idx"
    class="bg-white shadow rounded-lg p-5 flex items-center space-x-4"
    role="region"
    :aria-label="stat.label"
  >
    <component :is="stat.icon" :class="`h-6 w-6 ${stat.color}`" aria-hidden="true" />
    <dl class="flex-1">
      <dt class="text-sm font-medium text-gray-500 truncate">{{ stat.label }}</dt>
      <dd class="text-lg font-semibold text-gray-900">{{ stat.value }}</dd>
    </dl>
  </article>
</section>


    <!-- Quick Actions -->
    <section aria-label="Quick actions" class="bg-white shadow rounded-lg p-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-6">Quick Actions</h2>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <NuxtLink
          v-if="canManageProducts"
          to="/dashboard/products"
          class="action-card bg-blue-50 border-blue-200 hover:border-blue-400 focus:ring-blue-500 p-4 rounded-lg text-center"
          aria-label="Manage Products"
        >
          <Package class="mx-auto h-8 w-8 text-blue-400" />
          <span class="mt-2 block text-sm font-medium text-blue-600">Manage Products</span>
        </NuxtLink>

        <NuxtLink
          v-if="canManageProjects"
          to="/dashboard/projects"
          class="action-card bg-green-50 border-green-200 hover:border-green-400 focus:ring-green-500 p-4 rounded-lg text-center"
          aria-label="Manage Projects"
        >
          <FolderOpen class="mx-auto h-8 w-8 text-green-400" />
          <span class="mt-2 block text-sm font-medium text-green-600">Manage Projects</span>
        </NuxtLink>

        <NuxtLink
          v-if="canManageItems"
          to="/dashboard/items"
          class="action-card bg-yellow-50 border-yellow-200 hover:border-yellow-400 focus:ring-yellow-500 p-4 rounded-lg text-center"
          aria-label="Manage Items"
        >
          <CheckSquare class="mx-auto h-8 w-8 text-yellow-400" />
          <span class="mt-2 block text-sm font-medium text-yellow-600">Manage Items</span>
        </NuxtLink>

        <NuxtLink
          v-if="canViewSummaries"
          to="/dashboard/reports"
          class="action-card bg-purple-50 border-purple-200 hover:border-purple-400 focus:ring-purple-500 p-4 rounded-lg text-center"
          aria-label="View Reports"
        >
          <BarChart3 class="mx-auto h-8 w-8 text-purple-400" />
          <span class="mt-2 block text-sm font-medium text-purple-600">View Summaries</span>
        </NuxtLink>
      </div>
    </section>
  </main>
</template>
