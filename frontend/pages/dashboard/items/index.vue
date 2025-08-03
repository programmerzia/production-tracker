<script setup lang="ts">
import { ref, computed } from 'vue'
import { Plus, Pencil } from 'lucide-vue-next'
import { useAuthStore } from '~/stores/auth'
import { createError } from 'h3'
import ProductItemFormDialog from './ProductItemFormDialog.vue'
import ProductItemStatusDialog from './ProductItemStatusDialog.vue'

definePageMeta({
  middleware: 'auth',
  layout: 'dashboard'
})

const auth = useAuthStore()

if (!auth.canManageItems) {
  throw createError({
    statusCode: 403,
    statusMessage: 'Access denied. Product Engineer role required.'
  })
}

const { productItems, fetchProductItems, error } = useFetchProductItems()

const editingItem = ref(null)
const dialogOpen = ref(false)

const handleEdit = (item: any) => {
  editingItem.value = item
  dialogOpen.value = true
}

const handleAdd = () => {
  editingItem.value = null
  dialogOpen.value = true
}

const showStatusDialog = ref(false)
const selectedItem = ref(null)

const openStatusDialog = (item: any) => {
  selectedItem.value = item
  showStatusDialog.value = true
}

const statusMap: Record<string, { label: string; color: string }> = {
  pending: { label: 'Pending', color: 'bg-yellow-100 text-yellow-800' },
  in_progress: { label: 'In Progress', color: 'bg-blue-100 text-blue-800' },
  completed: { label: 'Completed', color: 'bg-green-100 text-green-800' },
  blocked: { label: 'Blocked', color: 'bg-red-100 text-red-800' },
}

// --- SEARCH & PAGINATION ---
const searchTerm = ref('')
const currentPage = ref(1)
const itemsPerPage = 10

const filteredItems = computed(() => {
  if (!searchTerm.value) return productItems.value
  const term = searchTerm.value.toLowerCase()
  return (productItems.value ?? []).filter(item =>
    item.name.toLowerCase().includes(term) ||
    (item.version?.toLowerCase().includes(term)) ||
    (item.status?.toLowerCase().includes(term)) ||
    (item?.project?.name?.toLowerCase().includes(term))
  )
})

const totalPages = computed(() =>
  Math.ceil((filteredItems.value ?? []).length / itemsPerPage)
)

const paginatedItems = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return (filteredItems.value ?? []).slice(start, start + itemsPerPage)
})

function goToPage(page: number) {
  if (page < 1 || page > totalPages.value) return
  currentPage.value = page
}
</script>

<template>
  <div class="max-w-5xl mx-auto my-8 p-4">
    <header class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-foreground">Product Items</h1>
        <p class="text-muted-foreground mt-1">Manage product items linked to projects.</p>
      </div>
      <button
        @click="handleAdd"
        class="bg-primary text-primary-foreground px-4 py-2 rounded hover:bg-primary/90 flex items-center font-medium"
      >
        <Plus class="h-4 w-4 mr-2" />
        Add Item
      </button>
    </header>

    <!-- Search input -->
    <input
      type="text"
      v-model="searchTerm"
      placeholder="Search items..."
      class="border rounded px-3 py-2 w-full max-w-xs mb-4"
    />

    <section class="rounded p-4 bg-card border border-border">
      <div v-if="error" class="text-red-500">Failed to load items. Please try again.</div>

      <div v-else-if="!productItems?.length" class="text-muted-foreground text-center py-6">
        No product items found.
      </div>

      <table v-else class="w-full text-left">
        <thead>
          <tr class="border-b text-sm text-muted-foreground">
            <th class="py-2">Name</th>
            <th class="py-2">Version</th>
            <th class="py-2">Status</th>
            <th class="py-2">Project</th>
            <th class="py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="item in paginatedItems"
            :key="item.id"
            class="border-b hover:bg-muted/20"
          >
            <td class="py-2 text-foreground">{{ item.name }}</td>
            <td class="py-2 text-foreground">{{ item.version || '—' }}</td>
            <td class="py-2">
              <span
                class="px-2 py-1 text-sm font-medium rounded-full"
                :class="statusMap[item.status]?.color"
              >
                {{ statusMap[item.status]?.label || item.status.replace('_', ' ') }}
              </span>
            </td>

            <td class="py-2 text-foreground">{{ item.project?.name || 'N/A' }}</td>
            <td class="py-2 gap-2">
              <button
                @click="handleEdit(item)"
                class="text-primary hover:underline flex items-center gap-1"
              >
                <Pencil class="w-4 h-4" /> Edit
              </button>
              <button
                @click="openStatusDialog(item)"
                class="text-blue-500 hover:underline ml-3 text-sm"
              >
                Change Status
              </button>
            </td>
          </tr>
          <tr v-if="paginatedItems.length === 0">
            <td colspan="5" class="py-4 text-center text-gray-500">
              No product items found.
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

    <!-- Add/Edit Dialog -->
    <ProductItemFormDialog
      v-model:open="dialogOpen"
      :product-item="editingItem"
      @submitted="fetchProductItems"
    />
    <ProductItemStatusDialog
      v-model:open="showStatusDialog"
      :item="selectedItem"
      @updated="fetchProductItems"
    />
  </div>
</template>
