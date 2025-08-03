export interface User {
  id: number
  email: string
  name: string
  role: UserRole
  created_at?: string
  updated_at?: string
}

export type UserRole = 'product_manager' | 'project_manager' | 'production_engineer'

export interface Product {
  id: number
  name: string
  version: string
  description: string
  pricing?: number
  created_at: string
  updated_at: string
  projects?: Project[]
}

export interface Project {
  id: number
  name: string
  description: string
  product_id: number
  product?: Product
  status: ProjectStatus
  created_at: string
  updated_at: string
  items?: ProductItem[]
}

export type ProjectStatus = 'planning' | 'active' | 'completed' | 'on_hold'

export interface ProductItem {
  id: number
  name: string
  description: string
  project_id: number
  project?: Project
  status: ProductItemStatus
  created_at: string
  updated_at: string
}

export type ProductItemStatus = 'pending' | 'in_progress' | 'completed' | 'blocked'

export interface AuthResponse {
  access_token: string
  user: User
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterCredentials {
  name: string
  email: string
  password: string
  password_confirmation: string
  role: UserRole
}

export interface Product {
  id: number
  name: string
  version: string
  description: string
  pricing?: number
}