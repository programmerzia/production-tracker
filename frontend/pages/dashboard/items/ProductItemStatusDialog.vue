<!-- components/ProductItemStatusDialog.vue -->
<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { Dialog, DialogContent } from '@/components/ui/dialog'
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select'
import { Button } from '@/components/ui/button'
import { toast } from 'vue-sonner'
import { useAuthStore, useRuntimeConfig } from '#imports'

const props = defineProps<{
  open: boolean
  item: { id: number, status: string } | null
}>()

const emit = defineEmits(['update:open', 'updated'])

const show = ref(props.open)
watch(() => props.open, val => (show.value = val))
watch(show, val => emit('update:open', val))

const statusOptions = ['pending', 'in_progress', 'completed', 'blocked']

const selectedStatus = ref('')
const availableStatuses = computed(() => {
  return statusOptions.filter((s) => s !== props.item?.status)
})

watch(() => props.item, (item) => {
  selectedStatus.value = ''
  if (item) {
    selectedStatus.value = availableStatuses.value[0] || ''
  }
}, { immediate: true })

const config = useRuntimeConfig()
const auth = useAuthStore()

const updateStatus = async () => {
  if (!selectedStatus.value || !props.item) return

  try {
    await $fetch(`/items/${props.item.id}/status`, {
      method: 'PATCH',
      baseURL: config.public.apiBase,
      headers: {
        Authorization: `Bearer ${auth.token}`
      },
      body: {
        status: selectedStatus.value
      }
    })
    toast.success('Status updated')
    emit('updated')
    emit('update:open', false)
  } catch (err) {
    toast.error('Failed to update status')
    console.error(err)
  }
}
</script>

<template>
  <Dialog v-model:open="show">
    <DialogContent class="max-w-sm w-full p-6 space-y-4 bg-white dark:bg-card text-foreground">
      <h2 class="text-lg font-semibold">Update Status</h2>
      <div>
        <label class="text-sm block mb-1">New Status</label>
        <Select v-model="selectedStatus">
          <SelectTrigger class="w-full">
            <SelectValue placeholder="Select Status" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem
              v-for="status in availableStatuses"
              :key="status"
              :value="status"
              class="capitalize"
            >
              {{ status.replace('_', ' ') }}
            </SelectItem>
          </SelectContent>
        </Select>
      </div>

      <Button @click="updateStatus" class="w-full">Update</Button>
    </DialogContent>
  </Dialog>
</template>
