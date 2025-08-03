<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Plus, Pencil } from 'lucide-vue-next'
import { useAuthStore } from '~/stores/auth'
import { createError } from 'h3'
import ProductFormDialog from './ProductFormDialog.vue'

definePageMeta({
  middleware: 'auth',
  layout: 'dashboard'
})

const auth = useAuthStore()

onMounted(() => {
  if (!auth.canManageProducts) {
    throw createError({
      statusCode: 403,
      statusMessage: 'Access denied. Product Manager role required.'
    })
  }
})

const { products, fetchProducts, error } = useFetchProducts()

const editingProduct = ref(null)
const dialogOpen = ref(false)

const handleEdit = (product: any) => {
  editingProduct.value = product
  dialogOpen.value = true
}

const openNewProductDialog = () => {
  editingProduct.value = null
  dialogOpen.value = true
}

// --- SEARCH & PAGINATION ---
const searchTerm = ref('')
const currentPage = ref(1)
const itemsPerPage = 10

const filteredProducts = computed(() => {
  if (!searchTerm.value) return products.value
  const term = searchTerm.value.toLowerCase()
  return (products.value ?? []).filter(p =>
    p.name.toLowerCase().includes(term) ||
    (p.version?.toLowerCase().includes(term)) ||
    (p.description?.toLowerCase().includes(term))
  )
})

const totalPages = computed(() =>
  Math.ceil((filteredProducts.value ?? []).length / itemsPerPage)
)

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return (filteredProducts.value ?? []).slice(start, start + itemsPerPage)
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
        <h1 class="text-2xl font-semibold text-foreground">Products</h1>
        <p class="text-muted-foreground mt-1">Manage your product catalog and versions.</p>
      </div>
      
      <ProductFormDialog
        v-model:open="dialogOpen"
        :product="editingProduct"
        @submitted="fetchProducts"
      />
    </header>

    <!-- Search input -->
    <input
      type="text"
      v-model="searchTerm"
      placeholder="Search products..."
      class="border rounded px-3 py-2 w-full max-w-xs mb-4"
    />

    <section class="rounded p-4 bg-card border border-border">
      <table class="w-full text-left">
        <thead>
          <tr class="border-b text-sm">
            <th class="py-2">Name</th>
            <th class="py-2">Version</th>
            <th class="py-2">Description</th>
            <th class="py-2">Price</th>
            <th class="py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="product in paginatedProducts"
            :key="product.id"
            class="border-b"
          >
            <td class="py-2 text-foreground">{{ product.name }}</td>
            <td class="py-2 text-foreground">{{ product.version }}</td>
            <td class="py-2 text-foreground">{{ product.description }}</td>
            <td class="py-2 text-foreground">{{ product.price }}</td>
            <td class="py-2">
              <button
                @click="handleEdit(product)"
                class="text-primary hover:underline flex items-center gap-1"
              >
                <Pencil class="w-4 h-4" /> Edit
              </button>
            </td>
          </tr>
          <tr v-if="paginatedProducts.length === 0">
            <td colspan="5" class="py-4 text-center text-gray-500">
              No products found.
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
  </div>
</template>
