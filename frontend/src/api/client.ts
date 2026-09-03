import axios from 'axios'

export const API_URL = import.meta.env.VITE_API_URL as string

export const api = axios.create({
  baseURL: `${API_URL}/api`,
  withCredentials: true,
  withXSRFToken: true,
  headers: { Accept: 'application/json' },
})

export async function ensureCsrfCookie(): Promise<void> {
  await axios.get(`${API_URL}/sanctum/csrf-cookie`, { withCredentials: true, withXSRFToken: true })
}
