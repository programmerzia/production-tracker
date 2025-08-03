import { ref } from 'vue'
import { z } from 'zod'

const schema = z.object({
  name: z.string().min(1),
  version: z.string().min(1),
  description: z.string().min(1),
  price: z.number().default(0)
})

export const useProductForm = () => {
  const form = ref({
    name: '',
    version: '',
    description: '',
    price: 0
  })

  const errors = ref<Record<string, string>>({})

  const validate = () => {
    const result = schema.safeParse(form.value)
    if (result.success) {
      errors.value = {}
      return true
    }

    errors.value = result.error.flatten().fieldErrors as Record<string, string>
    return false
  }

  const fill = (data: any) => {
    form.value = {
      name: data.name || '',
      version: data.version || '',
      description: data.description || '',
      price: Number(data.price) || 0
    }
  }

  const reset = () => {
    form.value = {
      name: '',
      version: '',
      description: '',
      price: 0
    }
    errors.value = {}
  }

  return { form, errors, validate, reset, fill }
}
