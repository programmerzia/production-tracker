<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Plus, Pencil } from 'lucide-vue-next'
import { useAuthStore } from '~/stores/auth'
import { createError } from 'h3'
import { useFetchProjects } from '~/composables/useFetchProjects'
import { useFetchProducts } from '~/composables/useFetchProducts'
import ProjectFormDialog from './ProjectFormDialog.vue'

definePageMeta({
  middleware: 'auth',
  layout: 'dashboard'
})

const auth = useAuthStore()

onMounted(() => {
  if (!auth.canManageProjects) {
    throw createError({
      statusCode: 403,
      statusMessage: 'Access denied. Product Manager or Project Manager role required.'
    })
  }
})

const { projects, fetchProjects } = useFetchProjects()
const { data: products, pending, error } = await useFetch<{id: number, name: string}[]>('/product-list', {
  baseURL: useRuntimeConfig().public.apiBase,
  headers: {
    Authorization: `Bearer ${auth.token}`
  }
})

const dialogOpen = ref(false)
const editingProject = ref(null)

const openNew = () => {
  editingProject.value = null
  dialogOpen.value = true
}

const openEdit = (project: any) => {
  editingProject.value = project
  dialogOpen.value = true
}

// --- SEARCH & PAGINATION ---
const searchTerm = ref('')
const currentPage = ref(1)
const itemsPerPage = 10

const filteredProjects = computed(() => {
  if (!searchTerm.value) return projects.value
  const term = searchTerm.value.toLowerCase()
  return (projects.value ?? []).filter(p =>
    p.name.toLowerCase().includes(term) ||
    (p.product?.name.toLowerCase().includes(term)) ||
    (p.description?.toLowerCase().includes(term))
  )
})

const totalPages = computed(() =>
  Math.ceil((filteredProjects.value ?? []).length / itemsPerPage)
)

const paginatedProjects = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return (filteredProjects.value ?? []).slice(start, start + itemsPerPage)
})

function goToPage(page: number) {
  if (page < 1 || page > totalPages.value) return
  currentPage.value = page
}
</script>

<template>
  <div class="max-w-4xl mx-auto my-8 p-4">
    <header class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-foreground">Projects</h1>
        <p class="text-muted-foreground text-sm mt-1">Manage production projects and track progress.</p>
      </div>
      <button
        @click="openNew"
        class="bg-primary text-primary-foreground px-4 py-2 rounded hover:bg-primary/90 font-medium flex items-center"
      >
        <Plus class="h-4 w-4 mr-2" />
        Add Project
      </button>
    </header>

    <!-- Search input -->
    <input
      type="text"
      v-model="searchTerm"
      placeholder="Search projects..."
      class="border rounded px-3 py-2 w-full max-w-xs mb-4"
    />

    <section class="rounded p-4 bg-card border border-border">
      <table class="w-full text-left">
        <thead>
          <tr class="border-b text-muted-foreground text-sm">
            <th class="py-2">Project</th>
            <th class="py-2">Product</th>
            <th class="py-2">Description</th>
            <th class="py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="project in paginatedProjects"
            :key="project.id"
            class="border-b"
          >
            <td class="py-2 text-foreground">{{ project.name }}</td>
            <td class="py-2 text-foreground">{{ project.product?.name }}</td>
            <td class="py-2 text-foreground">{{ project.description }}</td>
            <td class="py-2">
              <button
                @click="openEdit(project)"
                class="text-primary hover:underline flex items-center gap-1"
              >
                <Pencil class="w-4 h-4" /> Edit
              </button>
            </td>
          </tr>
          <tr v-if="paginatedProjects.length === 0">
            <td colspan="4" class="py-4 text-center text-gray-500">
              No projects found.
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination Controls -->
      <div class="mt-4 flex justify-center space-x-2">
        <button
          @click="goToPage(currentPage - 1)"
          :disabled="currentPage === 1"
          class="px-3 py-1 border rounded disabled:opacity-50"
        >
          Prev
        </button>

        <button
          v-for="page in totalPages"
          :key="page"
          @click="goToPage(page)"
          :class="[
            'px-3 py-1 border rounded',
            currentPage === page ? 'bg-blue-600 text-white' : ''
          ]"
        >
          {{ page }}
        </button>

        <button
          @click="goToPage(currentPage + 1)"
          :disabled="currentPage === totalPages"
          class="px-3 py-1 border rounded disabled:opacity-50"
        >
          Next
        </button>
      </div>
    </section>

    <ProjectFormDialog
      v-model:open="dialogOpen"
      :project="editingProject"
      :products="products ?? []"
      @submitted="fetchProjects"
    />
  </div>
</template>
