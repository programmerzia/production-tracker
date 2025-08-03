<script setup lang="ts">
import { useAuthStore } from '~/stores/auth'
import { createError } from 'h3'

// ✅ Use page meta for auth middleware and layout
definePageMeta({
  middleware: 'auth',
  layout: 'dashboard',
})

// ✅ Auth check (done in setup to support SSR)
const auth = useAuthStore()

if (!auth.canViewSummaries) {
  throw createError({
    statusCode: 403,
    statusMessage: 'Access denied. Project Manager role required.',
  })
}

// Fetch project summaries from API
interface ProjectSummary {
  project_id: number
  project_name: string
  total_items: number
  pending: number
  in_progress: number
  completed: number
  blocked: number
}

const { data: summaries, pending, error } = await useFetch<ProjectSummary[]>('/summary', {
  baseURL: useRuntimeConfig().public.apiBase,
  headers: {
    Authorization: `Bearer ${auth.token}`
  }
})
// Export to CSV
function exportToCSV() {
  const data = summaries.value
  if (!data || !data.length) return

  const headers = ['Project', 'Total', 'Pending', 'In Progress', 'Completed', 'Blocked']
  const rows = data.map(p => [
    p.project_name,
    p.total_items,
    p.pending,
    p.in_progress,
    p.completed,
    p.blocked
  ])

  const csvContent = [headers.join(','), ...rows.map(r => r.join(','))].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = 'project_summary.csv'
  link.click()
}
</script>


<template>
  <div class="max-w-5xl mx-auto my-8 p-4 space-y-6">
    <header class="sm:flex sm:items-center sm:justify-between">
      <div class="sm:flex-auto">
        <h1 class="text-2xl font-semibold text-gray-900">Reports & Analytics</h1>
        <p class="mt-2 text-sm text-gray-700">View project summaries and export to CSV.</p>
      </div>

      <button
        @click="exportToCSV"
        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700"
      >
        Export CSV
      </button>
    </header>

    <div v-if="pending" class="text-center text-gray-500">Loading...</div>
    <div v-else-if="error" class="text-red-600">Failed to load summaries.</div>
    <table v-else class="min-w-full border text-sm text-gray-700 bg-white rounded shadow">
      <thead class="bg-gray-100 text-left">
        <tr>
          <th class="p-3 border-b">Project</th>
          <th class="p-3 border-b text-right">Total</th>
          <th class="p-3 border-b text-right">Pending</th>
          <th class="p-3 border-b text-right">In Progress</th>
          <th class="p-3 border-b text-right">Completed</th>
          <th class="p-3 border-b text-right">Blocked</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="project in summaries" :key="project.project_id" class="hover:bg-gray-50">
          <td class="p-3 border-b">{{ project.project_name }}</td>
          <td class="p-3 border-b text-right">{{ project.total_items }}</td>
          <td class="p-3 border-b text-right">{{ project.pending }}</td>
          <td class="p-3 border-b text-right">{{ project.in_progress }}</td>
          <td class="p-3 border-b text-right">{{ project.completed }}</td>
          <td class="p-3 border-b text-right">{{ project.blocked }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

