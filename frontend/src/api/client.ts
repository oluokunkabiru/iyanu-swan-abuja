import axios from 'axios'

export const API_URL = import.meta.env.VITE_API_URL as string
const ACCESS_TOKEN_KEY = 'swan_access_token'

export const api = axios.create({
  baseURL: `${API_URL}/api`,
  headers: { Accept: 'application/json' },
})

export function setAccessToken(token: string | null): void {
  if (token) {
    sessionStorage.setItem(ACCESS_TOKEN_KEY, token)
    return
  }

  sessionStorage.removeItem(ACCESS_TOKEN_KEY)
}

export function hasAccessToken(): boolean {
  return sessionStorage.getItem(ACCESS_TOKEN_KEY) !== null
}

api.interceptors.request.use((config) => {
  const token = sessionStorage.getItem(ACCESS_TOKEN_KEY)

  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  return config
})
