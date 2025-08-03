<!-- components/ProductItemFormDialog.vue -->
<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { Dialog, DialogContent } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Select, SelectItem, SelectTrigger, SelectValue, SelectContent } from '@/components/ui/select'
import { useProductItemForm } from '~/composables/useProductItemForm'
import { toast } from 'vue-sonner'
import { useRuntimeConfig, useAuthStore } from '#imports'

const props = defineProps<{
  open: boolean
  productItem?: any
}>()

const emit = defineEmits(['update:open', 'submitted'])

const auth = useAuthStore()
const config = useRuntimeConfig()

const { form, errors, validate, reset, fill } = useProductItemForm()
const isEditing = computed(() => !!props.productItem)

const show = ref(props.open)
watch(() => props.open, val => (show.value = val))
watch(show, val => emit('update:open', val))

// Load project list
const { data: projects, error: projectError } = await useFetch('/project-list', {
  baseURL: config.public.apiBase,
  headers: {
    Authorization: `Bearer ${auth.token}`
  }
})

// Populate form when editing
watch(
  () => props.productItem,
  (item) => {
    if (item) {
      fill(item)
    } else {
      reset()
    }
  },
  { immediate: true }
)

const submit = async () => {
  if (!validate()) return

  const method = isEditing.value ? 'PUT' : 'POST'
  const url = isEditing.value
    ? `${config.public.apiBase}/items/${props.productItem.id}`
    : `${config.public.apiBase}/items`

  try {
    await $fetch(url, {
      method,
      body: form.value,
      headers: {
        Authorization: `Bearer ${auth.token}`
      }
    })

    emit('submitted')
    show.value = false
    reset()
    toast.success('Product item submitted successfully')
  } catch (err) {
    console.error('API error:', err)
    toast.error('Failed to submit form. API error.')
  }
}
</script>

<template>
  <Dialog v-model:open="show">
    <DialogContent class="max-w-md w-full p-6 space-y-4 bg-white dark:bg-card text-foreground">
      <h2 class="text-lg font-semibold">{{ isEditing ? 'Edit Product Item' : 'Add Product Item' }}</h2>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="text-sm">Project</label>
          <Select v-model="form.project_id">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Select Project" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem
                v-for="project in projects"
                :key="project.id"
                :value="project.id"
              >
                {{ project.name }}
              </SelectItem>
            </SelectContent>
          </Select>
          <p v-if="errors.project_id" class="text-sm text-red-500">{{ errors.project_id }}</p>
        </div>

        <div>
          <label class="text-sm">Name</label>
          <Input v-model="form.name" />
          <p v-if="errors.name" class="text-sm text-red-500">{{ errors.name }}</p>
        </div>

        <div>
          <label class="text-sm">Version</label>
          <Input v-model="form.version" />
        </div>

        <!-- Future enhancement -->
        <!-- <div>
          <label class="text-sm">Status</label>
          <Select v-model="form.status">
            <SelectItem value="pending">Pending</SelectItem>
            <SelectItem value="in_progress">In Progress</SelectItem>
            <SelectItem value="completed">Completed</SelectItem>
            <SelectItem value="blocked">Blocked</SelectItem>
          </Select>
        </div> -->

        <Button type="submit" class="w-full">
          {{ isEditing ? 'Update Item' : 'Create Item' }}
        </Button>
      </form>
    </DialogContent>
  </Dialog>
</template>
