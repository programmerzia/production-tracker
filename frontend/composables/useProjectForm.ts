import { ref } from 'vue'
import { z } from 'zod'

const schema = z.object({
  product_id: z.number(),
  name: z.string().min(1, 'Project name is required'),
  description: z.string().optional()
})

export const useProjectForm = () => {
  const form = ref({
    product_id: 0,
    name: '',
    description: ''
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
      product_id: 0,
      name: '',
      description: ''
    }
    errors.value = {}
  }

  const fill = (data: any) => {
    form.value = {
      product_id: data.product_id || 0,
      name: data.name || '',
      description: data.description || ''
    }
  }

  return { form, errors, validate, reset, fill }
}