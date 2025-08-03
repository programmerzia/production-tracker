import { useRuntimeConfig, useFetch } from '#imports'
import type { Product } from '~/types'
import { useAuthStore } from '~/stores/auth'

export const useFetchProducts = () => {
  const config = useRuntimeConfig()
  const auth = useAuthStore()

  const { data: products, refresh: fetchProducts, error } = useFetch<Product[]>(
    () => `${config.public.apiBase}/products`,
    {
      headers: auth.token
        ? {
            Authorization: `Bearer ${auth.token}`
          }
        : {},
      // Optional: handle on server or only client
      server: false // disable SSR for token-based fetch
    }
  )

  return {
    products,
    fetchProducts,
    error
  }
}
