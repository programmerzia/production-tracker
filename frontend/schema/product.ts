import { z } from 'zod'

export const productSchema = z.object({
  name: z.string().min(1, 'Product name is required'),
  price: z.coerce.number().positive('Price must be greater than 0'),
})

export type ProductForm = z.infer<typeof productSchema>
