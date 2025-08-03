import { ref } from 'vue'
import { z } from 'zod'

const statusEnum = z.enum(['pending', 'in_progress', 'completed', 'blocked'])

const schema = z.object({
  project_id: z.coerce.number().min(1, 'Project is required'),
  name: z.string().min(1, 'Item name is required'),
  version: z.string().optional(),
  status: statusEnum.default('pending')
})

export const useProductItemForm = () => {
  const form = ref({
    project_id: 0,
    name: '',
    version: '',
    status: 'pending'
  })

  const errors = ref<Record<string, string>>({})

  const validate = () => {
    const result = schema.safeParse(form.value)
    if (result.success) {
      errors.value = {}
      return true
    }

    errors.value = Object.fromEntries(
      Object.entries(result.error.flatten().fieldErrors).map(([k, v]) => [k, v?.[0] ?? ''])
    )
    return false
  }

  const reset = () => {
    form.value = {
      project_id: 0,
      name: '',
      version: '',
      status: 'pending'
    }
    errors.value = {}
  }

  const fill = (data: any) => {
    form.value = {
      project_id: data.project_id || 0,
      name: data.name || '',
      version: data.version || '',
      status: data.status || 'pending'
    }
  }

  return { form, errors, validate, reset, fill }
}
