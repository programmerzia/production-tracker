// composables/useFetchProjects.ts
import { useAuthStore } from '~/stores/auth'
import type { Project } from '~/types/project'

export const useFetchProjects = () => {
  const auth = useAuthStore()
  const config = useRuntimeConfig()

  const { data: projects, pending: loading, error, refresh: fetchProjects } = useFetch<Project[]>('/projects', {
    baseURL: config.public.apiBase,
    headers: {
      Authorization: `Bearer ${auth.token}`
    }
  })

  return {
    projects,
    loading,
    error,
    fetchProjects
  }
}
