import { z } from 'zod'

export const productItemSchema = z.object({
  project_id: z.number().refine(val => val !== undefined && val !== null, { message: 'Project is required' }),
  name: z.string().min(1, 'Name is required'),
  version: z.string().optional(),
  status: z.enum(['pending', 'in_progress', 'completed', 'blocked'])
})