<!-- components/ProjectFormDialog.vue -->
<script setup lang="ts">
import { Dialog, DialogContent } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Textarea } from '@/components/ui/textarea'
import { useProjectForm } from '~/composables/useProjectForm'
import { useRuntimeConfig, useAuthStore } from '#imports'
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select'
import { toast } from 'vue-sonner'
const props = defineProps<{
  open: boolean
  project: any
  products: any[]
}>()

const emit = defineEmits(['update:open', 'submitted'])

const show = ref(props.open)
watch(() => props.open, val => (show.value = val))
watch(show, val => emit('update:open', val))

const { form, errors, validate, reset, fill } = useProjectForm()
const isEditing = computed(() => !!props.project)
const auth = useAuthStore()
watch(
  () => props.project,
  (project) => {
    if (project) {
      fill(project)
    } else {
      reset()
    }
  },
  { immediate: true }
)

const submit = async () => {
  if (!validate()) return

  const config = useRuntimeConfig()
  const url = isEditing.value
    ? `${config.public.apiBase}/projects/${props.project.id}`
    : `${config.public.apiBase}/projects`

  const method = isEditing.value ? 'PATCH' : 'POST'

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
  } catch (err) {
    toast.error('Failed to submit form. API error:', err)
  }
  
}
</script>

<template>
  <Dialog v-model:open="show">
    <DialogContent class="bg-card text-foreground p-6 w-full max-w-lg rounded space-y-4">
      <h2 class="text-lg font-semibold">{{ isEditing ? 'Edit Project' : 'Add Project' }}</h2>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="text-sm block mb-1">Product</label>
          <Select v-model="form.product_id">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Select Product" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem
                v-for="product in products"
                :key="product.id"
                :value="product.id"
              >
                {{ product.name }}
              </SelectItem>
            </SelectContent>
          </Select>
          <p v-if="errors.product_id" class="text-red-500 text-sm mt-1">{{ errors.product_id }}</p>
        </div>

        <div>
          <label class="text-sm block mb-1">Project Name</label>
          <Input v-model="form.name" />
          <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
        </div>

        <div>
          <label class="text-sm block mb-1">Description</label>
          <Textarea v-model="form.description" rows="3" />
        </div>

        <Button type="submit" class="w-full">
          {{ isEditing ? 'Update Project' : 'Create Project' }}
        </Button>
      </form>
    </DialogContent>
  </Dialog>
</template>
