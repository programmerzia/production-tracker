export interface Product {
  id: number
  name: string
  version: string
  description: string
  price: string
  created_at: string
  updated_at: string
}

export interface Project {
  id: number
  product_id: number
  name: string
  description: string
  created_at: string
  updated_at: string
  product: Product
}