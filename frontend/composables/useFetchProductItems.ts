import { useAuthStore } from '~/stores/auth'

interface ProductItem {
    id: number
    name: string
    version: string
    project_id: number
    status: string
}
export const useFetchProductItems = () => {
  const auth = useAuthStore()
  const config = useRuntimeConfig()

  const { data: productItems, pending: loading, error, refresh: fetchProductItems } = useFetch<ProductItem[]>('/items', {
    baseURL: config.public.apiBase,
    headers: {
      Authorization: `Bearer ${auth.token}`
    }
  })

  return { productItems, loading, error, fetchProductItems }
}
