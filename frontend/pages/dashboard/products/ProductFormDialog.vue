<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Textarea } from '@/components/ui/textarea'
import { useProductForm } from '~/composables/useProductForm'
import { useRuntimeConfig, useAuthStore } from '#imports'
import { toast } from 'vue-sonner'
import { Plus } from 'lucide-vue-next'

const props = defineProps<{
  open: boolean
  product: any
}>()

const emit = defineEmits(['update:open', 'submitted'])

const show = ref(props.open)
watch(() => props.open, val => (show.value = val))
watch(show, val => emit('update:open', val))

const { form, errors, validate, reset, fill } = useProductForm()
const config = useRuntimeConfig()
const auth = useAuthStore()


// Fill form when editing
watch(
  () => props.product,
  (product) => {
    if (product) {
      fill(product)
    } else {
      reset()
    }
  },
  { immediate: true }
)

const isEditing = computed(() => !!props.product)

const submit = async () => {
  if (!validate()) return

  const apiUrl = isEditing.value
    ? `${config.public.apiBase}/products/${props.product.id}`
    : `${config.public.apiBase}/products`

  const method = isEditing.value ? 'PATCH' : 'POST'

  try {
    await $fetch(apiUrl, {
      method,
      body: form.value,
      headers: {
        Authorization: `Bearer ${auth.token}`
      }
    })

    show.value = false
    emit('submitted')
    reset()
    toast.success('Product submitted successfully')
  } catch (err) {
    console.error('Failed to submit form. API error:', err)
  }
}
</script>


<template>
 <Dialog v-model:open="show">
  <DialogTrigger>
     <Button> Add Product</Button>
  </DialogTrigger>

  <DialogContent class="p-6 max-w-md w-full rounded-md shadow space-y-4 bg-card text-foreground">
    <h2 class="text-lg font-semibold"> {{ isEditing ? 'Edit Product' : 'Add Product' }} </h2>
{{ JSON.stringify(props.product, null, 2) }}
    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <Input v-model="form.name" />
        <p v-if="errors.name" class="text-sm text-red-500">{{ errors.name }}</p>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Version</label>
        <Input v-model="form.version" />
        <p v-if="errors.version" class="text-sm text-red-500">{{ errors.version }}</p>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Details</label>
        <Textarea v-model="form.description" rows="3" />
        <p v-if="errors.details" class="text-sm text-red-500">{{ errors.details }}</p>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Price</label>
        <Input type="number" v-model="form.price" />
        <p v-if="errors.price" class="text-sm text-red-500">{{ errors.price }}</p>
      </div>

      <Button type="submit" class="w-full">
        {{ isEditing ? 'Update' : 'Save' }} Product
      </Button>
    </form>
  </DialogContent>
</Dialog>

</template>
